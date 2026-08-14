<?php

// Horaire de référence de l'entreprise (une seule ligne, id=1, motif "singleton" déjà utilisé par
// crm_pointage_rappel) : matin 09:00-13:00 / après-midi 14:00-18:00 par défaut - utilisé comme
// heure de retard de référence (pointageweb::getHeureDebutMatin()) quand aucune dérogation
// jourtravailoverride ne s'applique pour la date. Modifiable depuis la page admin task=pointage.
class horairereference
{
    static $table = __prefixe_db__ . "horaire_reference";

    private $id;
    private $heure_debut_matin;
    private $heure_fin_matin;
    private $heure_debut_apresmidi;
    private $heure_fin_apresmidi;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId(){ return $this->id; }
    public function getHeureDebutMatin(){ return $this->heure_debut_matin; }
    public function getHeureFinMatin(){ return $this->heure_fin_matin; }
    public function getHeureDebutApresmidi(){ return $this->heure_debut_apresmidi; }
    public function getHeureFinApresmidi(){ return $this->heure_fin_apresmidi; }
    public function getLastEdit(){ return $this->last_edit; }

    public function setId($id){ $this->id = $id; }
    public function setHeureDebutMatin($v){ $this->heure_debut_matin = $v; }
    public function setHeureFinMatin($v){ $this->heure_fin_matin = $v; }
    public function setHeureDebutApresmidi($v){ $this->heure_debut_apresmidi = $v; }
    public function setHeureFinApresmidi($v){ $this->heure_fin_apresmidi = $v; }
    public function setLastEdit($v){ $this->last_edit = $v; }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET heure_debut_matin = %s, heure_fin_matin = %s, heure_debut_apresmidi = %s, heure_fin_apresmidi = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->heure_debut_matin, "text"),
            GetSQLValueString($this->heure_fin_matin, "text"),
            GetSQLValueString($this->heure_debut_apresmidi, "text"),
            GetSQLValueString($this->heure_fin_apresmidi, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    // Toujours la ligne id=1 (seedée par la migration) - pas de add()/delete(), ce singleton
    // existe toujours.
    public static function find()
    {
        global $db;
        $item = new horairereference();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE id = 1";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    public static function build($data)
    {
        $item = new horairereference();
        $item->setId($data['id']);
        $item->setHeureDebutMatin($data['heure_debut_matin']);
        $item->setHeureFinMatin($data['heure_fin_matin']);
        $item->setHeureDebutApresmidi($data['heure_debut_apresmidi']);
        $item->setHeureFinApresmidi($data['heure_fin_apresmidi']);
        $item->setLastEdit($data['last_edit']);
        return $item;
    }
}
