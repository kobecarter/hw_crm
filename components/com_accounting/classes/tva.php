<?php

class tva
{
    static $table =  __prefixe_db__ . "tva";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $agence;
    private $amount;
    private $increasion;
    private $date;
    private $status;
    private $remark;
    private $doc;
    private $date_add;
    private $last_edit;

    public function __construct(){
        $this->id = 0;
    }

    public function getId(){
        return $this->id;
    }

    public function getAgence()
    {
        return $this->agence;
    }

    public function getAmount(){
        return $this->amount;
    }

    public function getIncreasion(){
        return $this->increasion;
    }

    public function getDate(){
        return $this->date;
    }

    public function getStatus(){
        return $this->status;
    }


    public function getRemark(){
        return $this->remark;
    }

    public function getDoc(){
        return $this->doc;
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

    public function setAgence($agence)
    {
        $this->agence = $agence;
    }

    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function setIncreasion($increasion){
        $this->increasion = $increasion;
    }

    public function setDate($date){
        $this->date = $date;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function setRemark($remark){
        $this->remark = $remark;
    }
    
    public function setDoc($doc){
        $this->doc = $doc;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence, amount, increasion, date, status, remark, doc, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->increasion, "double"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->doc, "text"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET amount = %s, increasion = %s, date = %s, status = %s, remark = %s, doc = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->increasion, "double"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->doc, "text"),
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

    public static function find($id,$agence = 1)
    {
        global $db;
        $tva = new tva();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id = %s and A.id_agence = %s",
            GetSQLValueString($id, "int"),
            GetSQLValueString($agence, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $tva = static::build($data);
        }
        return $tva;
    }

    public static function findAll($agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s",
            GetSQLValueString($agence, "int")
        );
		$SQLselect .= " ORDER BY A.id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $tva = static::build($data);
            array_push($items, $tva);
        }
        return $items;
    }

    public static function findOneOrderByDate($agence = 1,$order = "asc")
    {
        global $db;
        $tva = new tva();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s",
            GetSQLValueString($agence, "int")
        );
        if($order == 'desc'){
            $SQLselect .= " order by A.date desc LIMIT 1";
        }else{
            $SQLselect .= " order by A.date asc LIMIT 1";
        }
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $tva = static::build($data);
        }
        return $tva;
    }

    public static function findByYear($agence = 1,$year='2024')
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s and A.date like %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($year."%", "text")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $tva = static::build($data);
            array_push($items, $tva);
        }
        return $items;
    }

    public static function findByDate($agence = 1,$date='2024')
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s and A.date like %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($date."%", "text")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $tva = static::build($data);
            array_push($items, $tva);
        }
        return $items;
    }

    public static function build($data){
        $tva = new tva();
        $tva->setId($data['ID']);
        $tva->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
        $tva->setAmount($data['amount']);
        $tva->setIncreasion($data['increasion']);
        $tva->setDate($data['date']);
        $tva->setStatus($data['status']);
        $tva->setRemark($data['remark']);
        $tva->setDoc($data['doc']);
        $tva->setDateAdd($data['date_add']);
        $tva->setLastEdit($data['last_edit']);
        return $tva;
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

