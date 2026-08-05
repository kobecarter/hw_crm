<?php

// Métadonnées d'un lot d'import (un lot = un fichier de relevé déposé et confirmé) : compte
// bancaire, fichier source (conservé pour servir de justificatif aux charges créées à partir de
// ce lot), et période couverte. N'existe en base QUE pour un lot confirmé - l'aperçu (avant clic
// sur "Valider la lecture") ne crée jamais de ligne ici, conformément au principe "rien n'est
// enregistré tant que ce n'est pas validé".
class releveLot
{
    static $table = __prefixe_db__ . "releve_lot";

    private $id;
    private $agence;
    private $bank;
    private $lot_import;
    private $fichier_source;
    private $date_debut;
    private $date_fin;
    private $periode_libelle;
    private $date_add;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAgence()
    {
        return $this->agence;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function getLotImport()
    {
        return $this->lot_import;
    }

    public function getFichierSource()
    {
        return $this->fichier_source;
    }

    public function getDateDebut()
    {
        return $this->date_debut;
    }

    public function getDateFin()
    {
        return $this->date_fin;
    }

    public function getPeriodeLibelle()
    {
        return $this->periode_libelle;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAgence($agence)
    {
        $this->agence = $agence;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function setLotImport($lot_import)
    {
        $this->lot_import = $lot_import;
    }

    public function setFichierSource($fichier_source)
    {
        $this->fichier_source = $fichier_source;
    }

    public function setDateDebut($date_debut)
    {
        $this->date_debut = $date_debut;
    }

    public function setDateFin($date_fin)
    {
        $this->date_fin = $date_fin;
    }

    public function setPeriodeLibelle($periode_libelle)
    {
        $this->periode_libelle = $periode_libelle;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence, id_bank, lot_import, fichier_source, date_debut, date_fin, periode_libelle, date_add) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence ? $this->agence->getId() : null, "int"),
            GetSQLValueString($this->bank ? $this->bank->getId() : null, "int"),
            GetSQLValueString($this->lot_import, "text"),
            GetSQLValueString($this->fichier_source, "text"),
            GetSQLValueString($this->date_debut, "date"),
            GetSQLValueString($this->date_fin, "date"),
            GetSQLValueString($this->periode_libelle, "text"),
            GetSQLValueString($this->date_add, "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        }
        return 0;
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        }
        return 0;
    }

    public static function find($id)
    {
        global $db;
        $lot = new releveLot();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s", GetSQLValueString($id, "int"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $lot = static::build($db->fetch_assoc($result));
        }
        return $lot;
    }

    public static function findByLotImport($lotImport)
    {
        global $db;
        $lot = new releveLot();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE lot_import = %s", GetSQLValueString($lotImport, "text"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $lot = static::build($db->fetch_assoc($result));
        }
        return $lot;
    }

    // Traçabilité "quel relevé bancaire couvrait la période où le comptable a calculé cette TVA" -
    // un simple recoupement de dates (date_debut/date_fin du lot vs mois de la déclaration),
    // jamais une lecture du contenu des lignes : la TVA est saisie/poussée par le comptable
    // indépendamment du module BANK STATEMENT, on veut seulement voir avec quel(s) relevé(s) elle
    // coïncide dans le temps. $dateDebutPeriode/$dateFinPeriode = premier/dernier jour du mois
    // couvert par la déclaration (voir com_accounting/index.php, case 'tva').
    public static function findAllByTva($idAgence, $dateDebutPeriode, $dateFinPeriode)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_agence = %s AND date_debut <= %s AND date_fin >= %s ORDER BY date_debut DESC",
            GetSQLValueString($idAgence, "int"),
            GetSQLValueString($dateFinPeriode, "date"),
            GetSQLValueString($dateDebutPeriode, "date")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    // Même recoupement que findAllByTva() ci-dessus, mais agrégé pour TOUTES les déclarations TVA
    // d'une agence en une seule requête groupée (JOIN direct sur les dates) - alimente l'icône
    // "relevé(s) lié(s)" affichée sur chaque ligne de la liste TVA sans un aller-retour par ligne.
    public static function compterLotsParTva($idAgence)
    {
        global $db;
        $compteurs = array();
        $SQLselect = sprintf(
            "SELECT t.id as id_tva, COUNT(l.id) as nb FROM " . __prefixe_db__ . "tva t
             INNER JOIN " . static::$table . " l ON l.id_agence = t.id_agence
                AND l.date_debut <= t.date AND l.date_fin >= DATE_FORMAT(t.date, '%%Y-%%m-01')
             WHERE t.id_agence = %s
             GROUP BY t.id",
            GetSQLValueString($idAgence, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $row) {
            $compteurs[(int) $row['id_tva']] = (int) $row['nb'];
        }
        return $compteurs;
    }

    // Regroupe tous les lots (relevés déjà importés) d'un même compte bancaire qui couvrent la
    // même période TVA (mois ou trimestre selon agence::getTvaPeriodicite()) - alimente la
    // question posée juste après confirmation d'un import ("X relevé(s) pour cette période,
    // faire le rapprochement maintenant ?"). periode_libelle est une chaîne dérivée (ex: "Août
    // 2026"/"T3 2026"), pas une vraie clé de période, mais un simple match exact suffit puisque
    // tous les lots d'une même agence la calculent via la même fonction (tva::libellePeriode()).
    public static function findAllByPeriode($idAgence, $idBank, $periodeLibelle)
    {
        global $db;
        $items = array();
        if ($periodeLibelle === null || trim((string) $periodeLibelle) === '') {
            return $items;
        }
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_agence = %s AND id_bank = %s AND periode_libelle = %s ORDER BY id DESC",
            GetSQLValueString($idAgence, "int"),
            GetSQLValueString($idBank, "int"),
            GetSQLValueString($periodeLibelle, "text")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function findAll($agence)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_agence = %s ORDER BY id DESC", GetSQLValueString($agence, "int"));
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function build($data)
    {
        $lot = new releveLot();
        $lot->setId($data['id']);
        if (!empty($data['id_agence'])) {
            $lot->setAgence(agence::find($data['id_agence'], isset($_SESSION['langue']) ? $_SESSION['langue'] : 'fr'));
        }
        if (!empty($data['id_bank'])) {
            $lot->setBank(bank::find($data['id_bank']));
        }
        $lot->setLotImport($data['lot_import']);
        $lot->setFichierSource(isset($data['fichier_source']) ? $data['fichier_source'] : null);
        $lot->setDateDebut(isset($data['date_debut']) ? $data['date_debut'] : null);
        $lot->setDateFin(isset($data['date_fin']) ? $data['date_fin'] : null);
        $lot->setPeriodeLibelle(isset($data['periode_libelle']) ? $data['periode_libelle'] : null);
        $lot->setDateAdd($data['date_add']);
        return $lot;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }
}
