<?php

class pointage
{
    static $table =  __prefixe_db__ . "pointage";

    private $id;
    private $resourcehumaine;
    private $date;
    private $hour1;
    private $hour2;
    private $hour3;
    private $hour4;
    private $delay;
    private $overtime;
    private $date_add;
    private $last_edit;

    public function __construct(){
        $this->id = 0;
    }

    public function getId(){
        return $this->id;
    }

    public function getResourcehumaine(){
        return $this->resourcehumaine;
    }

    public function getDate(){
        return $this->date;
    }

    public function getHour1(){
        return $this->hour1;
    }

    public function getHour2(){
        return $this->hour2;
    }

    public function getHour3(){
        return $this->hour3;
    }
    
    public function getHour4(){
        return $this->hour4;
    }

    public function getDelay(){
        return $this->delay;
    }

    public function getOvertime(){
        return $this->overtime;
    }

    public function getDateAdd(){
        return $this->date_add;
    }

    public function getLastEdit(){
        return $this->last_edit;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setResourcehumaine($resourcehumaine){
        $this->resourcehumaine = $resourcehumaine;
    }

    public function setDate($date){
        $this->date = $date;
    }

    public function setHour1($hour1){
        $this->hour1 = $hour1;
    }

    public function setHour2($hour2){
        $this->hour2 = $hour2;
    }

    public function setHour3($hour3){
        $this->hour3 = $hour3;
    }

    public function setHour4($hour4){
        $this->hour4 = $hour4;
    }
    
    public function setDelay($delay){
        $this->delay = $delay;
    }

    public function setOvertime($overtime){
        $this->overtime = $overtime;
    }

    public function setDateAdd($date_add){
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit){
        $this->last_edit = $last_edit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, date, hour1, hour2, hour3, hour4, delay, overtime, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, %s,%s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->hour1, "text"),
            GetSQLValueString($this->hour2, "text"),
            GetSQLValueString($this->hour3, "text"),
            GetSQLValueString($this->hour4, "text"),
            GetSQLValueString($this->delay, "text"),
            GetSQLValueString($this->overtime, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET date = %s, hour1 = %s, hour2 = %s, hour3 = %s, hour4 = %s, delay = %s, overtime = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->hour1, "text"),
            GetSQLValueString($this->hour2, "text"),
            GetSQLValueString($this->hour3, "text"),
            GetSQLValueString($this->hour4, "text"),
            GetSQLValueString($this->delay, "text"),
            GetSQLValueString($this->overtime, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete(){
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $pointage = new pointage();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $pointage = static::build($data);
        }
        return $pointage;
    }

    public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " ORDER BY id ASC");
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $pointage = static::build($data);
            array_push($items, $pointage);
        }
        return $items;
    }

    public static function findAllByResourcehumaine($id_resourcehumaine)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s ORDER BY id ASC",
            GetSQLValueString($id_resourcehumaine, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $filesresourcehumaine = static::build($data);
            array_push($items, $filesresourcehumaine);
        }
        return $items;
    }

    public static function findAllByResourcehumaineAndMonth($id_resourcehumaine,$month)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s and date like %s ORDER BY id ASC",
            GetSQLValueString($id_resourcehumaine, "int"),
            GetSQLValueString($month."%", "text")
        );
    
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $filesresourcehumaine = static::build($data);
            array_push($items, $filesresourcehumaine);
        }
        return $items;
    }

    public static function build($data){
        $pointage = new pointage();
        $pointage->setId($data['id']);
        $pointage->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $pointage->setDate($data['date']);
        $pointage->setHour1($data['hour1']);
        $pointage->setHour2($data['hour2']);
        $pointage->setHour3($data['hour3']);
        $pointage->setHour4($data['hour4']);
        $pointage->setDelay($data['delay']);
        $pointage->setOvertime($data['overtime']);
        $pointage->setDateAdd($data['date_add']);
        $pointage->setLastEdit($data['last_edit']);
        return $pointage;
    }

    public static function count(){
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

