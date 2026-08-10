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

        $SQLselect = "SELECT A.montant FROM " . static::$tablePayment . " A
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
            B.id AS id_facture, B.numero, B.date_facture, B.devise,
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
            $montantTVA = $taux > 0 ? $montantTTC - ($montantTTC / (1 + $taux / 100)) : 0;
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

    // Liste de TOUTES les factures de la période (proforma/avoirs compris, toutes devises),
    // pour l'export comptable "Toutes les ventes ajoutées" : contrairement à
    // detailVentesTvaCollectee() qui ne montre que ce qui compte réellement dans la TVA,
    // celle-ci sert de vue d'ensemble/traçabilité complète pour le comptable.
    public static function detailVentesAjoutees($from, $to, $agence = 1)
    {
        global $db;
        $SQLselect = "SELECT B.numero, B.date_facture, B.devise, B.total, B.proforma, B.avoir,
            C.raison_social, C.nom, C.prenom,
            (SELECT COALESCE(SUM(P.montant), 0) FROM " . static::$tablePayment . " P WHERE P.id_facture = B.id) AS montant_regle
            FROM " . static::$tableFacture . " B
            INNER JOIN " . static::$tableClient . " C ON B.id_client = C.id
            INNER JOIN " . static::$tableAgence . " D ON C.id_agence = D.id
            WHERE D.id = " . GetSQLValueString($agence, "int") . "
            AND (B.archived IS NULL OR B.archived = 0)
            AND B.date_facture >= " . GetSQLValueString($from, "date") . "
            AND B.date_facture <= " . GetSQLValueString($to, "date");

        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (B.id_user_added = " . intval($_SESSION['user']->getId()) . ")";
        }
        $SQLselect .= " ORDER BY B.date_facture ASC";

        return $db->queryS($SQLselect);
    }
}
