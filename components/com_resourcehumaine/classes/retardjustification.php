<?php

// Justification d'un retard de pointage (case "Retard" du calendrier employé, task=pointage) -
// un enregistrement par (employé, date) suffit à marquer ce retard comme justifié : il sort alors
// du calcul des totaux "Retard" (badge par employé + KPI global, voir
// pointageweb::classifierJour()/calendrierMois()), exactement comme une absence justifiée sort du
// compteur d'absences non justifiées. Distincte de la table crm_absence (jours entiers, impacte le
// solde de congés/la paie) - un retard n'est jamais un jour d'absence.
class retardjustification
{
    static $table = __prefixe_db__ . "retard_justification";

    private $id;
    private $resourcehumaine;
    private $date;
    private $remark;
    private $justification;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId(){ return $this->id; }
    public function getResourcehumaine(){ return $this->resourcehumaine; }
    public function getDate(){ return $this->date; }
    public function getRemark(){ return $this->remark; }
    public function getJustification(){ return $this->justification; }
    public function getDateAdd(){ return $this->date_add; }
    public function getLastEdit(){ return $this->last_edit; }

    public function setId($id){ $this->id = $id; }
    public function setResourcehumaine($resourcehumaine){ $this->resourcehumaine = $resourcehumaine; }
    public function setDate($date){ $this->date = $date; }
    public function setRemark($remark){ $this->remark = $remark; }
    public function setJustification($justification){ $this->justification = $justification; }
    public function setDateAdd($date_add){ $this->date_add = $date_add; }
    public function setLastEdit($last_edit){ $this->last_edit = $last_edit; }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_resourcehumaine, date, remark, justification, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->justification, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)) {
            $this->setId($db->last_id());
            return 1;
        } else {
            return 2;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET remark = %s, justification = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->justification, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 2;
        }
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s", GetSQLValueString($this->getId(), "int"));
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 2;
        }
    }

    public static function find($id)
    {
        global $db;
        $item = new retardjustification();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s", GetSQLValueString($id, "int"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    public static function findByDate($idResourcehumaine, $date)
    {
        global $db;
        $item = new retardjustification();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s AND date = %s",
            GetSQLValueString($idResourcehumaine, "int"),
            GetSQLValueString($date, "date")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    public static function build($data)
    {
        $item = new retardjustification();
        $item->setId($data['id']);
        $item->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
        $item->setDate($data['date']);
        $item->setRemark($data['remark']);
        $item->setJustification($data['justification']);
        $item->setDateAdd($data['date_add']);
        $item->setLastEdit($data['last_edit']);
        return $item;
    }
}
