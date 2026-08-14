<?php

// Dérogation manuelle à la règle automatique de pointageweb::estJourTravaille() (lundi-vendredi
// toujours, samedi un sur deux) - permet à l'admin de forcer un jour précis comme travaillé ou
// non travaillé (ex: un samedi de rattrapage, un pont exceptionnellement chômé...), gérée depuis
// la page admin task=pointage. Porte aussi, optionnellement, un horaire spécifique à ce jour
// (heure_debut_matin/fin_matin/debut_apresmidi/fin_apresmidi) qui prime sur l'horaire de référence
// (horairereference) - ex: un jeudi où l'équipe termine à 16h. Globale (pas par agence/employé),
// comme la table holiday.
class jourtravailoverride
{
    static $table = __prefixe_db__ . "jour_travail_override";

    private $id;
    private $date;
    private $est_travaille;
    private $heure_debut_matin;
    private $heure_fin_matin;
    private $heure_debut_apresmidi;
    private $heure_fin_apresmidi;
    private $remark;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId(){ return $this->id; }
    public function getDate(){ return $this->date; }
    public function getEstTravaille(){ return $this->est_travaille; }
    public function getHeureDebutMatin(){ return $this->heure_debut_matin; }
    public function getHeureFinMatin(){ return $this->heure_fin_matin; }
    public function getHeureDebutApresmidi(){ return $this->heure_debut_apresmidi; }
    public function getHeureFinApresmidi(){ return $this->heure_fin_apresmidi; }
    public function getRemark(){ return $this->remark; }
    public function getDateAdd(){ return $this->date_add; }
    public function getLastEdit(){ return $this->last_edit; }

    public function setId($id){ $this->id = $id; }
    public function setDate($date){ $this->date = $date; }
    public function setEstTravaille($est_travaille){ $this->est_travaille = $est_travaille; }
    public function setHeureDebutMatin($v){ $this->heure_debut_matin = $v; }
    public function setHeureFinMatin($v){ $this->heure_fin_matin = $v; }
    public function setHeureDebutApresmidi($v){ $this->heure_debut_apresmidi = $v; }
    public function setHeureFinApresmidi($v){ $this->heure_fin_apresmidi = $v; }
    public function setRemark($remark){ $this->remark = $remark; }
    public function setDateAdd($date_add){ $this->date_add = $date_add; }
    public function setLastEdit($last_edit){ $this->last_edit = $last_edit; }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (date, est_travaille, heure_debut_matin, heure_fin_matin, heure_debut_apresmidi, heure_fin_apresmidi, remark, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->est_travaille, "int"),
            GetSQLValueString($this->heure_debut_matin, "text"),
            GetSQLValueString($this->heure_fin_matin, "text"),
            GetSQLValueString($this->heure_debut_apresmidi, "text"),
            GetSQLValueString($this->heure_fin_apresmidi, "text"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)) {
            $this->setId($db->last_id());
            return 1;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET est_travaille = %s, heure_debut_matin = %s, heure_fin_matin = %s, heure_debut_apresmidi = %s, heure_fin_apresmidi = %s, remark = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->est_travaille, "int"),
            GetSQLValueString($this->heure_debut_matin, "text"),
            GetSQLValueString($this->heure_fin_matin, "text"),
            GetSQLValueString($this->heure_debut_apresmidi, "text"),
            GetSQLValueString($this->heure_fin_apresmidi, "text"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s", GetSQLValueString($this->getId(), "int"));
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function findByDate($date)
    {
        global $db;
        $item = null;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE date = %s", GetSQLValueString($date, "date"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    // Toutes les dérogations d'un mois donné ('YYYY-MM') - alimente le calendrier admin, une
    // seule requête plutôt qu'un findByDate() par jour.
    public static function findAllByMonth($mois)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE date LIKE %s ORDER BY date ASC", GetSQLValueString($mois . '%', "text"));
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function build($data)
    {
        $item = new jourtravailoverride();
        $item->setId($data['id']);
        $item->setDate($data['date']);
        $item->setEstTravaille($data['est_travaille']);
        $item->setHeureDebutMatin($data['heure_debut_matin']);
        $item->setHeureFinMatin($data['heure_fin_matin']);
        $item->setHeureDebutApresmidi($data['heure_debut_apresmidi']);
        $item->setHeureFinApresmidi($data['heure_fin_apresmidi']);
        $item->setRemark($data['remark']);
        $item->setDateAdd($data['date_add']);
        $item->setLastEdit($data['last_edit']);
        return $item;
    }
}
