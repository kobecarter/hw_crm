<?php

// Régime de déclaration TVA (mensuel/trimestriel) par agence et par année. Consommé par la
// page TVA de com_accounting (échéance, export comptable, cumul par année) via
// periodiciteAnnee() ; agence::getTvaPeriodicite() (valeur plate, défaut mensuel) reste par
// ailleurs inchangée pour ses autres consommateurs (com_rapprochement notamment).
class regimeTva
{
    static $table = __prefixe_db__ . "regime_tva";

    public static function findAll($id_agence)
    {
        global $db;
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE id_agence = " . GetSQLValueString($id_agence, "int") . " ORDER BY annee ASC";
        return $db->queryS($SQLselect);
    }

    // Remplacement complet des lignes de l'agence à partir des tableaux soumis par le
    // formulaire (mêmes index parallèles) - plus simple qu'un diff, le nombre de lignes par
    // agence reste faible (une par année). Les années futures sont ignorées ici aussi (le
    // formulaire bloque déjà la saisie côté JS, mais on ne fait pas confiance uniquement au
    // client) - une année future n'a pas encore de périodicité de déclaration à statuer.
    public static function syncForAgence($id_agence, $annees, $periodicites)
    {
        global $db;

        $db->query("DELETE FROM " . static::$table . " WHERE id_agence = " . GetSQLValueString($id_agence, "int"));

        if (!is_array($annees)) {
            return;
        }

        $anneeCourante = (int) date('Y');

        foreach ($annees as $cpt => $annee) {
            if ($annee === '' || $annee === null || (int) $annee > $anneeCourante) {
                continue;
            }
            $periodicite = isset($periodicites[$cpt]) && $periodicites[$cpt] === 'trimestriel' ? 'trimestriel' : 'mensuel';
            $db->query("INSERT INTO " . static::$table . " (id_agence, annee, periodicite, date_add, last_edit) VALUES ("
                . GetSQLValueString($id_agence, "int") . ", "
                . GetSQLValueString($annee, "int") . ", "
                . GetSQLValueString($periodicite, "text") . ", "
                . "NOW(), NOW())");
        }
    }

    // Périodicité de l'année courante parmi les lignes soumises - sert à maintenir à jour
    // agence::tva_periodicite (champ plat encore lu par le module TVA/rapprochement) sans que
    // le formulaire n'expose plus de sélecteur dédié pour ce champ.
    public static function periodiciteAnneeCourante($annees, $periodicites, $defaut = 'mensuel')
    {
        if (!is_array($annees)) {
            return $defaut;
        }
        $anneeCourante = (int) date('Y');
        foreach ($annees as $cpt => $annee) {
            if ((int) $annee === $anneeCourante) {
                return isset($periodicites[$cpt]) && $periodicites[$cpt] === 'trimestriel' ? 'trimestriel' : 'mensuel';
            }
        }
        return $defaut;
    }

    // Périodicité effective d'une agence pour une année donnée (lecture directe en base,
    // indépendante d'une soumission de formulaire) - défaut trimestriel si aucune ligne
    // n'existe pour cette année, contrairement à periodiciteAnneeCourante() (défaut mensuel) :
    // deux besoins distincts (page TVA vs formulaire agence), pas de fusion des défauts.
    public static function periodiciteAnnee($id_agence, $annee, $defaut = 'trimestriel')
    {
        global $db;
        $result = $db->queryS("SELECT periodicite FROM " . static::$table . " WHERE id_agence = " . GetSQLValueString($id_agence, "int") . " AND annee = " . GetSQLValueString($annee, "int") . " LIMIT 1");
        if (!empty($result)) {
            return $result[0]['periodicite'] === 'trimestriel' ? 'trimestriel' : 'mensuel';
        }
        return $defaut;
    }
}
