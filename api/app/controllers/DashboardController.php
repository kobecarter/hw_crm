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
            return response()->json(['success' => true, 'data' => $data]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }
}
