<?php

class workdate
{
    static $table =  __prefixe_db__ . "workdate";

    private $id;
    private $resourcehumaine;
    private $start_date;
    private $contract_signing_date;
    private $end_date;
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

    public function getStartDate(){
        return $this->start_date;
    }

    public function getContractSigningDate(){
        return $this->contract_signing_date;
    }

    public function getEndDate(){
        return $this->end_date;
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

    public function setStartDate($start_date){
        $this->start_date = $start_date;
    }

    public function setContractSigningDate($contract_signing_date){
        $this->contract_signing_date = $contract_signing_date;
    }

    public function setEndDate($end_date){
        $this->end_date = $end_date;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, start_date, contract_signing_date , end_date, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->contract_signing_date, "date"),
            GetSQLValueString($this->end_date, "date"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET start_date = %s, contract_signing_date = %s, end_date = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->contract_signing_date, "date"),
            GetSQLValueString($this->end_date, "date"),
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
        $workdate = new workdate();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $workdate = static::build($data);
        }
        return $workdate;
    }

    public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " ORDER BY id ASC");
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $workdate = static::build($data);
            array_push($items, $workdate);
        }
        return $items;
    }

    public static function findByDate($id_resourcehumaine,$date)
    {
        global $db;
        $workdate = new workdate();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where (%s >= start_date and %s <= end_date) and id_resourcehumaine = %s order by id desc",
            GetSQLValueString($date, "date"),
            GetSQLValueString($date, "date"),
            GetSQLValueString($id_resourcehumaine, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $workdate = static::build($data);
        }
        return $workdate;
    }

    public static function findByDateMonthly($id_resourcehumaine,$dateMonthly)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where start_date like %s and id_resourcehumaine = %s order by id desc",
            GetSQLValueString($dateMonthly."%", "text"),
            GetSQLValueString($id_resourcehumaine, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $workdates = static::build($data);
            array_push($items, $workdates);
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

    public static function build($data){
        $workdate = new workdate();
        $workdate->setId($data['id']);
        $workdate->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $workdate->setStartDate($data['start_date']);
        $workdate->setContractSigningDate($data['contract_signing_date']);
        $workdate->setEndDate($data['end_date']);
        $workdate->setDateAdd($data['date_add']);
        $workdate->setLastEdit($data['last_edit']);
        return $workdate;
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

