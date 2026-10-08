<?php

// Simulateur TVA temps réel : estime la TVA due par agence/mois à partir des règlements
// clients réels (base encaissements, cf. payment::getReglementbyDate()) et des charges
// payées marquées déductibles (charge::montantTvaDeductible()), sans attendre la fin du
// mois. Ne remplace pas la déclaration officielle — sert d'estimation anticipée.
class tvaSimulateur
{
    static $tableSimulation = __prefixe_db__ . "tva_simulation";
    static $tablePayment = __prefixe_db__ . "payment";
    static $tableFacture = __prefixe_db__ . "facture";
    static $tableClient = __prefixe_db__ . "client";
    static $tableAgence = __prefixe_db__ . "agence";

    // TVA collectée = somme, sur les règlements encaissés dans la période (date_payment),
    // de la part TVA de chaque paiement — dérivée du taux TVA actuel de l'agence, comme le
    // fait déjà payment::pdfPayment(). Les factures proforma (pas de TVA réelle due) et
    // archivées sont exclues.
    public static function calculerTvaCollectee($annee, $mois, $agence = 1, $devise = 'DH')
    {
        global $db;
        $agenceObj = agence::find($agence, $_SESSION['langue']);
        $taux = $agenceObj->getTva() ? $agenceObj->getTva() : 0;

        $SQLselect = "SELECT A.montant, B.tva_retenue_source FROM " . static::$tablePayment . " A
            INNER JOIN " . static::$tableFacture . " B ON A.id_facture = B.id
            INNER JOIN " . static::$tableClient . " C ON B.id_client = C.id
            INNER JOIN " . static::$tableAgence . " D ON C.id_agence = D.id
            WHERE D.id = " . GetSQLValueString($agence, "int") . "
            AND (B.archived IS NULL OR B.archived = 0)
            AND (B.proforma IS NULL OR B.proforma = 0)
            AND B.devise = " . GetSQLValueString($devise, "text") . "
            AND YEAR(A.date_payment) = " . GetSQLValueString($annee, "int") . "
            AND MONTH(A.date_payment) = " . GetSQLValueString($mois, "int");

        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (B.id_user_added = " . intval($_SESSION['user']->getId()) . ")";
        }

        $result = $db->queryS($SQLselect);
        $montant = 0;
        foreach ($result as $row) {
            // TVA retenue à la source : le règlement encaissé EST le montant HT (le client a
            // versé la TVA directement au fisc) - aucune part TVA à en extraire.
            if ($row['tva_retenue_source'] == 1) {
                continue;
            }
            $paye = (float) $row['montant'];
            if ($taux > 0) {
                $montant += $paye - ($paye / (1 + $taux / 100));
            }
        }
        return $montant;
    }

    // TVA déductible = délègue à charge::montantTvaDeductible() (charges payées, période
    // sur date_payment, marquées déductibles, taux propre à chaque charge).
    public static function calculerTvaDeductible($annee, $mois, $agence = 1, $devise = 'DH')
    {
        $from = sprintf('%04d-%02d-01', $annee, $mois);
        $to = date('Y-m-t', strtotime($from));
        return charge::montantTvaDeductible($from, $to, $agence, $devise);
    }

    // Calcule l'estimation complète du mois et l'enregistre (historique consultable, upsert
    // par agence/année/mois). $creditReporte reste une saisie manuelle (comme demandé dans
    // le cahier des charges : "Saisie du solde créditeur du mois antérieur").
    public static function simuler($annee, $mois, $agence, $creditReporte = 0)
    {
        global $db;
        $collectee = static::calculerTvaCollectee($annee, $mois, $agence);
        $deductible = static::calculerTvaDeductible($annee, $mois, $agence);
        $creditReporte = (float) $creditReporte;
        $due = $collectee - $deductible - $creditReporte;

        $existe = $db->query("SELECT id FROM " . static::$tableSimulation . " WHERE id_agence = " . GetSQLValueString($agence, "int") . " AND annee = " . GetSQLValueString($annee, "int") . " AND mois = " . GetSQLValueString($mois, "int"));
        if ($db->num_rows($existe) > 0) {
            $row = $db->fetch_assoc($existe);
            $db->query("UPDATE " . static::$tableSimulation . " SET tva_collectee = " . GetSQLValueString($collectee, "double") . ", tva_deductible = " . GetSQLValueString($deductible, "double") . ", credit_reporte = " . GetSQLValueString($creditReporte, "double") . ", tva_due = " . GetSQLValueString($due, "double") . ", date_calcul = NOW() WHERE id = " . GetSQLValueString($row['id'], "int"));
        } else {
            $db->query("INSERT INTO " . static::$tableSimulation . " (id_agence, annee, mois, tva_collectee, tva_deductible, credit_reporte, tva_due, date_calcul) VALUES (" . GetSQLValueString($agence, "int") . ", " . GetSQLValueString($annee, "int") . ", " . GetSQLValueString($mois, "int") . ", " . GetSQLValueString($collectee, "double") . ", " . GetSQLValueString($deductible, "double") . ", " . GetSQLValueString($creditReporte, "double") . ", " . GetSQLValueString($due, "double") . ", NOW())");
        }

        return array(
            'tva_collectee' => $collectee,
            'tva_deductible' => $deductible,
            'credit_reporte' => $creditReporte,
            'tva_due' => $due,
        );
    }

    // Récupère le crédit reporté déjà saisi pour ce mois (si déjà calculé une fois), pour
    // que la page ne réaffiche pas 0 à chaque visite après une première saisie.
    public static function creditReporteExistant($annee, $mois, $agence)
    {
        global $db;
        $result = $db->query("SELECT credit_reporte FROM " . static::$tableSimulation . " WHERE id_agence = " . GetSQLValueString($agence, "int") . " AND annee = " . GetSQLValueString($annee, "int") . " AND mois = " . GetSQLValueString($mois, "int"));
        if ($db->num_rows($result) == 1) {
            $row = $db->fetch_assoc($result);
            return (float) $row['credit_reporte'];
        }
        return 0;
    }

    // Historique des derniers mois calculés pour une agence (le plus récent en premier).
    public static function historique($agence, $limit = 6)
    {
        global $db;
        $items = array();
        $result = $db->queryS("SELECT * FROM " . static::$tableSimulation . " WHERE id_agence = " . GetSQLValueString($agence, "int") . " ORDER BY annee DESC, mois DESC LIMIT " . intval($limit));
        foreach ($result as $row) {
            array_push($items, $row);
        }
        return $items;
    }

    // "Besoin en factures d'achat complémentaires HT" : formule exacte du cahier des
    // charges. Ne renvoie un besoin que si la TVA due dépasse l'objectif ; sinon 0.
    public static function besoinFacturesComplementaires($tvaDue, $objectif, $tauxAgence)
    {
        if ($tauxAgence <= 0 || $tvaDue <= $objectif) {
            return 0;
        }
        return ($tvaDue - $objectif) / ($tauxAgence / 100);
    }

    // Détail ligne par ligne (un règlement = une ligne) des paiements pris en compte dans la
    // TVA collectée sur une période libre (pas forcément un mois calendaire) — mêmes critères
    // exacts que calculerTvaCollectee() (proforma/archivées exclues, DH uniquement), mais
    // renvoie chaque facture/client/paiement au lieu du seul total agrégé. Sert de source
    // unique à l'export comptable Excel, pour rester garanti cohérent avec les KPI affichés.
    public static function detailVentesTvaCollectee($from, $to, $agence = 1, $devise = 'DH')
    {
        global $db;
        $agenceObj = agence::find($agence, $_SESSION['langue']);
        $taux = $agenceObj->getTva() ? $agenceObj->getTva() : 0;

        $SQLselect = "SELECT A.date_payment, A.montant, A.methode_payment,
            B.id AS id_facture, B.numero, B.date_facture, B.devise, B.tva_retenue_source,
            C.raison_social, C.nom, C.prenom
            FROM " . static::$tablePayment . " A
            INNER JOIN " . static::$tableFacture . " B ON A.id_facture = B.id
            INNER JOIN " . static::$tableClient . " C ON B.id_client = C.id
            INNER JOIN " . static::$tableAgence . " D ON C.id_agence = D.id
            WHERE D.id = " . GetSQLValueString($agence, "int") . "
            AND (B.archived IS NULL OR B.archived = 0)
            AND (B.proforma IS NULL OR B.proforma = 0)
            AND B.devise = " . GetSQLValueString($devise, "text") . "
            AND A.date_payment >= " . GetSQLValueString($from, "date") . "
            AND A.date_payment <= " . GetSQLValueString($to, "date");

        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (B.id_user_added = " . intval($_SESSION['user']->getId()) . ")";
        }
        $SQLselect .= " ORDER BY A.date_payment ASC";

        $result = $db->queryS($SQLselect);
        $lignes = array();
        foreach ($result as $row) {
            $montantTTC = (float) $row['montant'];
            $retenueSource = $row['tva_retenue_source'] == 1;
            // TVA retenue à la source : le règlement encaissé EST le montant HT - pas de part
            // TVA à en extraire (déjà versée par le client directement au fisc).
            $montantTVA = ($taux > 0 && !$retenueSource) ? $montantTTC - ($montantTTC / (1 + $taux / 100)) : 0;
            $lignes[] = array(
                'id_facture' => $row['id_facture'],
                'client' => trim($row['raison_social']) !== '' ? $row['raison_social'] : trim($row['prenom'] . ' ' . $row['nom']),
                'numero_facture' => $row['numero'],
                'date_facture' => $row['date_facture'],
                'date_paiement' => $row['date_payment'],
                'methode_paiement' => $row['methode_payment'],
                'taux_tva' => $taux,
                'montant_ttc' => $montantTTC,
                'montant_ht' => $montantTTC - $montantTVA,
                'montant_tva' => $montantTVA,
                'tva_retenue_source' => $retenueSource,
            );
        }
        return $lignes;
    }

    // Détail ligne par ligne des charges prises en compte dans la TVA déductible — délègue à
    // charge::detailTvaDeductible() (mêmes critères que montantTvaDeductible()), pour rester
    // cohérent avec calculerTvaDeductible() qui délègue déjà de la même façon.
    public static function detailAchatsTvaDeductible($from, $to, $agence = 1, $devise = 'DH')
    {
        return charge::detailTvaDeductible($from, $to, $agence, $devise);
    }

    // Liste des ventes de la période pour l'export comptable "Toutes les ventes ajoutées"
    // (proforma/avoirs compris, toutes devises) — contrairement à detailVentesTvaCollectee()
    // qui ne montre que ce qui compte réellement dans la TVA, celle-ci sert de vue
    // d'ensemble/traçabilité complète pour le comptable. Suit la même logique "encaissement"
    // que detailVentesTvaCollectee() (un règlement = une ligne, datée de son paiement) : une
    // facture soldée par un UNIQUE règlement reste une seule ligne "facture globale" (montant =
    // total facture) ; une facture réglée en plusieurs fois (ou dont l'unique règlement connu ne
    // la solde pas encore) éclate en une ligne par règlement, avec le montant et la date de CE
    // règlement. Une facture encore jamais réglée n'a aucune date de paiement à suivre : elle
    // reste affichée sur sa date de facture, sinon elle disparaîtrait purement et simplement de
    // cet onglet censé tout tracer.
    public static function detailVentesAjoutees($from, $to, $agence = 1)
    {
        global $db;

        $SQLreglements = "SELECT B.id, B.numero, B.date_facture, B.devise, B.total, B.proforma, B.avoir, B.tva_retenue_source,
            C.raison_social, C.nom, C.prenom, D.tva AS agence_tva,
            A.date_payment, A.montant AS montant_reglement
            FROM " . static::$tablePayment . " A
            INNER JOIN " . static::$tableFacture . " B ON A.id_facture = B.id
            INNER JOIN " . static::$tableClient . " C ON B.id_client = C.id
            INNER JOIN " . static::$tableAgence . " D ON C.id_agence = D.id
            WHERE D.id = " . GetSQLValueString($agence, "int") . "
            AND (B.archived IS NULL OR B.archived = 0)
            AND A.date_payment >= " . GetSQLValueString($from, "date") . "
            AND A.date_payment <= " . GetSQLValueString($to, "date");
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLreglements .= " AND (B.id_user_added = " . intval($_SESSION['user']->getId()) . ")";
        }
        $SQLreglements .= " ORDER BY A.date_payment ASC, A.id ASC";
        $reglements = $db->queryS($SQLreglements);

        // Nombre total de règlements et montant total encaissé de chaque facture concernée,
        // sur TOUTE sa durée de vie (pas seulement ceux tombant dans [from, to]) - c'est ce qui
        // décide "soldée en un seul paiement" (ligne globale) vs "réglée en plusieurs fois"
        // (une ligne par règlement), même si un seul de ces règlements tombe dans la période
        // exportée ici.
        $idsFactures = array_values(array_unique(array_column($reglements, 'id')));
        $nbReglementsParFacture = array();
        $montantRegleParFacture = array();
        if (!empty($idsFactures)) {
            $SQLcompte = "SELECT id_facture, COUNT(*) AS nb, SUM(montant) AS total_regle
                FROM " . static::$tablePayment . "
                WHERE id_facture IN (" . implode(',', array_map('intval', $idsFactures)) . ")
                GROUP BY id_facture";
            foreach ($db->queryS($SQLcompte) as $c) {
                $nbReglementsParFacture[$c['id_facture']] = (int) $c['nb'];
                $montantRegleParFacture[$c['id_facture']] = (float) $c['total_regle'];
            }
        }

        $rows = array();
        foreach ($reglements as $r) {
            $idF = $r['id'];
            $nb = isset($nbReglementsParFacture[$idF]) ? $nbReglementsParFacture[$idF] : 1;
            $montantRegleCumule = isset($montantRegleParFacture[$idF]) ? $montantRegleParFacture[$idF] : (float) $r['montant_reglement'];
            $montantAttendu = self::montantAttenduVente($r);
            $soldee = $r['proforma'] != 1 && $montantRegleCumule >= $montantAttendu - 0.01;

            $montantLigne = ($nb === 1 && $soldee) ? (float) $r['total'] : (float) $r['montant_reglement'];
            $rows[] = self::ligneVenteAjoutee($r, $r['date_payment'], $montantLigne, $montantAttendu, $montantRegleCumule, $r['date_payment']);
        }

        // Factures encore jamais réglées (aucun règlement, donc rien dans $reglements) mais
        // émises dans la période : gardées sur leur date de facture, comme avant la bascule
        // "encaissement" ci-dessus - sinon elles disparaîtraient de cet onglet qui doit rester
        // une vue de TOUTES les ventes ajoutées, payées ou non.
        $SQLnonReglees = "SELECT B.id, B.numero, B.date_facture, B.devise, B.total, B.proforma, B.avoir, B.tva_retenue_source,
            C.raison_social, C.nom, C.prenom, D.tva AS agence_tva
            FROM " . static::$tableFacture . " B
            INNER JOIN " . static::$tableClient . " C ON B.id_client = C.id
            INNER JOIN " . static::$tableAgence . " D ON C.id_agence = D.id
            WHERE D.id = " . GetSQLValueString($agence, "int") . "
            AND (B.archived IS NULL OR B.archived = 0)
            AND B.date_facture >= " . GetSQLValueString($from, "date") . "
            AND B.date_facture <= " . GetSQLValueString($to, "date") . "
            AND NOT EXISTS (SELECT 1 FROM " . static::$tablePayment . " P WHERE P.id_facture = B.id)";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLnonReglees .= " AND (B.id_user_added = " . intval($_SESSION['user']->getId()) . ")";
        }
        $SQLnonReglees .= " ORDER BY B.date_facture ASC, B.id ASC";
        foreach ($db->queryS($SQLnonReglees) as $f) {
            $montantAttendu = self::montantAttenduVente($f);
            $rows[] = self::ligneVenteAjoutee($f, $f['date_facture'], (float) $f['total'], $montantAttendu, 0, null);
        }

        usort($rows, function ($a, $b) {
            return strcmp($a['date_tri'], $b['date_tri']);
        });

        return $rows;
    }

    // Reste à payer "HT-aware" pour une facture "TVA retenue à la source" : le client ne
    // règlera jamais que le HT, comparer au TTC ferait apparaître un reste jamais soldable.
    private static function montantAttenduVente($f)
    {
        $retenueSource = $f['tva_retenue_source'] == 1;
        return ($retenueSource && $f['proforma'] != 1 && (float) $f['agence_tva'] > 0)
            ? (float) $f['total'] / (1 + (float) $f['agence_tva'] / 100)
            : (float) $f['total'];
    }

    // Construit une ligne de l'onglet "Toutes les ventes" - factorisé car partagé par les deux
    // sources ci-dessus (règlements de la période / factures jamais réglées).
    private static function ligneVenteAjoutee($f, $dateTri, $montantLigne, $montantAttendu, $montantRegleCumule, $datePaiement)
    {
        return array(
            'numero' => $f['numero'],
            'client' => trim($f['raison_social']) !== '' ? $f['raison_social'] : trim($f['prenom'] . ' ' . $f['nom']),
            'date_facture' => $f['date_facture'],
            'date_paiement' => $datePaiement,
            'devise' => $f['devise'],
            'proforma' => $f['proforma'],
            'avoir' => $f['avoir'],
            'tva_retenue_source' => $f['tva_retenue_source'] == 1,
            'montant_total' => (float) $f['total'],
            'montant_ligne' => $montantLigne,
            'montant_regle' => $montantRegleCumule,
            'reste' => $f['proforma'] == 1 ? null : round($montantAttendu - $montantRegleCumule, 2),
            'date_tri' => $dateTri,
        );
    }
}
