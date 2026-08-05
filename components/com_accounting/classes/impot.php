<?php

// Déclaration d'impôts (IS - Impôt sur les Sociétés ou IR - Impôt sur le Revenu), annuelle -
// même structure que bilan.php (date de dépôt, année, montant, majoration, statut, justificatif),
// avec en plus un type (IS/IR) puisqu'une même agence dépose les deux chaque année.
class impot
{
    static $table =  __prefixe_db__ . "impot";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $agence;
    private $type;
    private $date_of_depot;
    private $year;
    private $amount;
    private $increasion;
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

    public function getType(){
        return $this->type;
    }

    public function getDateOfDepot(){
        return $this->date_of_depot;
    }

    public function getYear(){
        return $this->year;
    }

    public function getAmount(){
        return $this->amount;
    }

    public function getIncreasion(){
        return $this->increasion;
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

    public function setType($type){
        $this->type = $type;
    }

    public function setDateOfDepot($date_of_depot){
        $this->date_of_depot = $date_of_depot;
    }

    public function setYear($year){
        $this->year = $year;
    }

    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function setIncreasion($increasion){
        $this->increasion = $increasion;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence, type, date_of_depot, year, amount, increasion, status, remark, doc, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->date_of_depot, "date"),
            GetSQLValueString($this->year, "text"),
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->increasion, "double"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET type = %s, date_of_depot = %s, year = %s, amount = %s, increasion = %s, status = %s, remark = %s, doc = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->date_of_depot, "date"),
            GetSQLValueString($this->year, "text"),
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->increasion, "double"),
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
        $impot = new impot();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id = %s and A.id_agence = %s",
            GetSQLValueString($id, "int"),
            GetSQLValueString($agence, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $impot = static::build($data);
        }
        return $impot;
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
            $impot = static::build($data);
            array_push($items, $impot);
        }
        return $items;
    }

    public static function findByYear($agence, $year)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s and A.year = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($year, "text")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $impot = static::build($data);
            array_push($items, $impot);
        }
        return $items;
    }

    public static function build($data){
        $impot = new impot();
        $impot->setId($data['ID']);
        $impot->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
        $impot->setType($data['type']);
        $impot->setDateOfDepot($data['date_of_depot']);
        $impot->setYear($data['year']);
        $impot->setAmount($data['amount']);
        $impot->setIncreasion($data['increasion']);
        $impot->setStatus($data['status']);
        $impot->setRemark($data['remark']);
        $impot->setDoc($data['doc']);
        $impot->setDateAdd($data['date_add']);
        $impot->setLastEdit($data['last_edit']);
        return $impot;
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
