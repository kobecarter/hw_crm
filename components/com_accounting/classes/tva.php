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
    private $date_payment;
    private $status;
    private $remark;
    private $doc;
    private $id_charge;
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

    public function getDatePayment(){
        return $this->date_payment;
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

    public function getIdCharge(){
        return $this->id_charge;
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

    public function setDatePayment($date_payment){
        $this->date_payment = $date_payment;
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

    public function setIdCharge($id_charge){
        $this->id_charge = $id_charge;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence, amount, increasion, date, date_payment, status, remark, doc, id_charge, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->increasion, "double"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->date_payment, "date"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->doc, "text"),
            GetSQLValueString($this->id_charge, "int"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET amount = %s, increasion = %s, date = %s, date_payment = %s, status = %s, remark = %s, doc = %s, id_charge = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->increasion, "double"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->date_payment, "date"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->doc, "text"),
            GetSQLValueString($this->id_charge, "int"),
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

    // Libellé humain d'une période TVA (un mois, ou un trimestre civil T1-T4) à partir d'une
    // date de référence — utilisé à la fois par le rappel d'échéance (list.php) et par la
    // colonne "Période" de l'export comptable, pour que les deux parlent le même langage.
    public static function libellePeriode($reference, $periodicite = 'mensuel')
    {
        $moisNoms = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
        $ts = ($reference instanceof DateTime) ? $reference->getTimestamp() : strtotime($reference);
        $m = (int) date('n', $ts);
        $y = (int) date('Y', $ts);
        if ($periodicite === 'trimestriel') {
            return 'T' . ((int) ceil($m / 3)) . ' ' . $y;
        }
        return $moisNoms[$m] . ' ' . $y;
    }

    // Calcule les bornes d'une période de déclaration TVA (un mois ou un trimestre civil) et sa
    // date limite de dépôt (le 20 du mois suivant la fin de période, règle marocaine standard),
    // décalée de $decalage périodes par rapport à celle contenant $reference (0 = période en
    // cours, -1 = dernière période pleinement écoulée, etc). Retourne 'debut'/'fin' (DateTime),
    // 'limite' (DateTime) et 'libelle' (string).
    public static function periodeReference($periodicite, DateTime $reference = null, $decalage = 0)
    {
        if ($reference === null) {
            $reference = new DateTime('today');
        }
        if ($periodicite === 'trimestriel') {
            $moisDebutTrimestre = ((int) ceil(((int) $reference->format('n')) / 3) - 1) * 3 + 1;
            $debut = new DateTime($reference->format('Y') . '-' . sprintf('%02d', $moisDebutTrimestre) . '-01');
            $debut->modify((($decalage * 3)) . ' months');
            $fin = (clone $debut)->modify('+2 months')->modify('last day of this month');
        } else {
            $debut = new DateTime($reference->format('Y-m-01'));
            $debut->modify($decalage . ' months');
            $fin = clone $debut;
            $fin->modify('last day of this month');
        }
        $limite = (clone $fin)->modify('first day of next month')->modify('+19 days');
        return array(
            'debut' => $debut,
            'fin' => $fin,
            'limite' => $limite,
            'libelle' => self::libellePeriode($debut, $periodicite)
        );
    }

    public static function build($data){
        $tva = new tva();
        $tva->setId($data['ID']);
        $tva->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
        $tva->setAmount($data['amount']);
        $tva->setIncreasion($data['increasion']);
        $tva->setDate($data['date']);
        $tva->setDatePayment(isset($data['date_payment']) ? $data['date_payment'] : null);
        $tva->setStatus($data['status']);
        $tva->setRemark($data['remark']);
        $tva->setDoc($data['doc']);
        $tva->setIdCharge(isset($data['id_charge']) ? $data['id_charge'] : null);
        $tva->setDateAdd($data['date_add']);
        $tva->setLastEdit($data['last_edit']);
        return $tva;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
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

