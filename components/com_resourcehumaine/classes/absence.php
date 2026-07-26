<?php

class absence
{
    static $table =  __prefixe_db__ . "absence";

    private $id;
    private $resourcehumaine;
    private $number_of_days;
    private $nature_of_absence;
    private $start_date;
    private $end_date;
    private $back_date;
    private $status;
    private $remark;
    private $justification;
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

    public function getNumberOfDays(){
        return $this->number_of_days;
    }

    public function getNatureOfAbsence(){
        return $this->nature_of_absence;
    }

    public function getStartDate(){
        return $this->start_date;
    }

    public function getEndDate(){
        return $this->end_date;
    }
    
    public function getBackDate(){
        return $this->back_date;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getRemark(){
        return $this->remark;
    }

    public function getJustification(){
        return $this->justification;
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

    public function setNumberOfDays($number_of_days){
        $this->number_of_days = $number_of_days;
    }

    public function setNatureOfAbsence($nature_of_absence){
        $this->nature_of_absence = $nature_of_absence;
    }

    public function setStartDate($start_date){
        $this->start_date = $start_date;
    }

    public function setEndDate($end_date){
        $this->end_date = $end_date;
    }
    
    public function setBackDate($back_date){
        $this->back_date = $back_date;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function setRemark($remark){
        $this->remark = $remark;
    }

    public function setJustification($justification){
        $this->justification = $justification;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, number_of_days, nature_of_absence, start_date, end_date, back_date, status, remark, justification, date_add, last_edit) VALUES (%s, %s, %s, %s, %s,%s, %s, %s,%s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->number_of_days, "double"),
            GetSQLValueString($this->nature_of_absence, "int"),
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->end_date, "date"),
            GetSQLValueString($this->back_date, "date"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->justification, "text"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET number_of_days = %s, nature_of_absence = %s, start_date = %s, end_date = %s, back_date = %s, status = %s, remark = %s, justification = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->number_of_days, "double"),
            GetSQLValueString($this->nature_of_absence, "int"),
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->end_date, "date"),
            GetSQLValueString($this->back_date, "date"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->justification, "text"),
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
        $absence = new absence();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $absence = static::build($data);
        }
        return $absence;
    }

    public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " ORDER BY id ASC");
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $absence = static::build($data);
            array_push($items, $absence);
        }
        return $items;
    }

    public static function findByDate($id_resourcehumaine,$date)
    {
        global $db;
        $absence = new absence();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where (%s >= start_date and %s <= end_date) and id_resourcehumaine = %s order by id desc",
            GetSQLValueString($date, "date"),
            GetSQLValueString($date, "date"),
            GetSQLValueString($id_resourcehumaine, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $absence = static::build($data);
        }
        return $absence;
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
            $absences = static::build($data);
            array_push($items, $absences);
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
        $absence = new absence();
        $absence->setId($data['id']);
        $absence->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $absence->setNumberOfDays($data['number_of_days']);
        $absence->setNatureOfAbsence($data['nature_of_absence']);
        $absence->setStartDate($data['start_date']);
        $absence->setEndDate($data['end_date']);
        $absence->setBackDate($data['back_date']);
        $absence->setStatus($data['status']);
        $absence->setRemark($data['remark']);
        $absence->setJustification($data['justification']);
        $absence->setDateAdd($data['date_add']);
        $absence->setLastEdit($data['last_edit']);
        return $absence;
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

