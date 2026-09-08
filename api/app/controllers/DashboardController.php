<?php

namespace App\Controllers;

use App\Controllers\AuthController as Auth;
use App\Utils\ResponseMessages;
use App\Utils\ApiException;

class DashboardController
{
    public function __construct()
    {
        Auth::middlware('auth');
    }

    // Historique des paiements réellement encaissés (20 derniers jours avec
    // règlement), pour le graphique d'évolution des prix de l'accueil - un
    // point par date de paiement (les paiements du même jour sont sommés),
    // pas une agrégation par mois. Vraies données crm_payment/crm_facture du
    // client, pas une donnée fictive.
    public function paymentsChart()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            global $db;
            $sql = sprintf(
                "SELECT p.date_payment AS date, SUM(p.montant) AS montant " .
                "FROM crm_payment p JOIN crm_facture f ON f.id = p.id_facture " .
                "WHERE f.id_client = %s AND p.date_payment IS NOT NULL " .
                "GROUP BY p.date_payment ORDER BY p.date_payment DESC LIMIT 20",
                GetSQLValueString($client->getId(), "int")
            );
            $rows = $db->queryS($sql);
            $data = array();
            if (is_array($rows)) {
                foreach (array_reverse($rows) as $row) {
                    $data[] = array(
                        'date' => $row['date'],
                        'montant' => (float) $row['montant'],
                    );
                }
            }

            // Payé/reste agrégés sur toutes les factures actives du client, pour le diagramme
            // "payé vs reste à payer" de l'app mobile - réutilise getMontantAttendu()/getReste(),
            // déjà le calcul de référence affiché sur la fiche facture. Les proforma sont des
            // brouillons (pas encore de montant réellement dû), donc exclues des deux totaux.
            $totalPaye = 0.0;
            $totalReste = 0.0;
            foreach (\facture::ofClient($client->getId()) as $facture) {
                if ($facture->isProforma()) {
                    continue;
                }
                $reste = $facture->getReste();
                $totalReste += max(0, $reste);
                $totalPaye += $facture->getMontantAttendu() - $reste;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'totalPaye' => $totalPaye,
                'totalReste' => $totalReste,
            ]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }
}
