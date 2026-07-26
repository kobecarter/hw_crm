<?php

class condition
{
    static $table =  __prefixe_db__ . "condition";

    private $id;
    private $devis;
    private $montant;
    private $condition;
    private $date;


    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDevis()
    {
        return $this->devis;
    }

    public function getMontant()
    {
        return $this->montant;
    }

    public function getCondition()
    {
        return $this->condition;
    }
    
     public function getDate()
    {
        return $this->date;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDevis($devis)
    {
        $this->devis = $devis;
    }

    public function setMontant($montant)
    {
        $this->montant = $montant;
    }

    public function setCondition($condition)
    {
        $this->condition = $condition;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_devis, montant, condition_pay, date_rappel) VALUES (%s, %s, %s, %s)",
            GetSQLValueString($this->devis->getId(), "int"),
            GetSQLValueString($this->montant, "text"),
            GetSQLValueString($this->condition, "text"),
            GetSQLValueString($this->date, "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_devis = %s, montant = %s, condition_pay = %s, date_rappel = %s WHERE id = %s",
            GetSQLValueString($this->devis->getId(), "int"),
            GetSQLValueString($this->montant, "text"),
            GetSQLValueString($this->condition, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->id, "int")
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
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $condition = new condition();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $condition = static::build($data);
        }
        return $condition;
    }

    public static function findAllByDevis($id_devis)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_devis = %s ORDER BY id DESC",
            GetSQLValueString($id_devis, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item = static::build($data);
            array_push($items, $item);
        }
        return $items;
    }

    public static function build($data)
    {
        $condition = new condition();
        $condition->setId($data['id']);
        $condition->setDevis(isset($data['id_devis']) ? devis::find($data['id_devis'], $_SESSION['agence']) : null);
        $condition->setCondition($data['condition_pay']);
        $condition->setMontant($data['montant']);
        $condition->setDate($data['date_rappel']);
        return $condition;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count()
    {
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

}
