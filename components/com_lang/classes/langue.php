<?php

class langue{
	
    static $table =  __prefixe_db__ . "langue";

	private $id = 0;
    private $nom;
    private $code;
    private $defaut;
    private $flag;
    private $actif;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function __destruct(){

    }

    public function getId(){
        return $this->id;
    }

    public function getNom(){
        return $this->nom;
    }

    public function isDefault(){
        return ($this->defaut == 1) ? true : false ;
    }

    public function getCode(){
        return $this->code;
    }

    public function getFlag(){
        return $this->flag;
    }

    public function isActif(){
        return ($this->actif == 1) ? true : false ;
    }

    public function getDateAdd(){
        return $this->date_add;
    }
	
	public function setId($id){
        $this->id = $id;
    }
	
	public function setNom($nom){
        $this->nom = $nom;
    }
	
	public function setCode($code){
        $this->code = $code;
    }
	
	public function setDefaut($defaut){
        $this->defaut = $defaut;
    }
	
	public function setActif($actif){
        $this->actif = $actif;
    }
	
	public function setFlag($flag){
        $this->flag = $flag;
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

        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (nom, code, flag, defaut, actif, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->code, "text"),
            GetSQLValueString($this->flag, "text"),
			GetSQLValueString($this->defaut, "int"),
			GetSQLValueString($this->actif, "int"),				 
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)){
			return 1;
        } else {
            return 0;
        }
    }
	
	public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET nom = %s, code = %s, flag = %s, defaut = %s, actif = %s, last_edit = %s WHERE id = %s",
			GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->code, "text"),
            GetSQLValueString($this->flag, "text"),
			GetSQLValueString($this->defaut, "int"),
			GetSQLValueString($this->actif, "int"),				 
            GetSQLValueString($this->last_edit, "date"),
			GetSQLValueString($this->id, "int")				 
        );
        if (!$db->query($SQLupdate)) {
			return 1;
        } else {
            return 0;
        }
    }

    public static function isLangueDefault($langue){
        global $db;
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE code = '$langue'";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1){
            $data = $db->fetch_assoc($result);
            $l = static::build($data);
            return $l->isDefault();
        }
        return false;
    }

    public static function getDefaultLanguage(){
        global $db;
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE defaut = 1";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return static::build($data);
        }
        return "";
    }
	
	public static function findByCode($code){
        global $db;
        $langue = new langue();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE code = '$code'";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $langue = static::build($data);
        }
        return $langue;
    }

    public static function findAll($active = false){
        global $db;
        $langues = array();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE 1 = 1";
		
		if($active){
            $SQLselect .= " AND actif = 1";
        }
		
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
			$langue = static::build($data);
            array_push($langues, $langue);
        }
        return $langues;
    }
	
	public static function find($id){
        global $db;
		$langue = new langue();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE id = $id";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
			$langue = static::build($data);
            return $langue;
        }
        return "";
    }

    public static function findOthers($langue){
        global $db;
        $ids = array();
        $SQLselect = "SELECT id FROM ".__prefixe_db__."langue WHERE actif = 1 AND code != '".$langue."'";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($ids, $data['id']);
        }
        return $ids;
    }

    public static function getIdLangue($langue){
        global $db;
        $ids = array();
        $SQLselect = "SELECT id FROM ".__prefixe_db__."langue WHERE code = '$langue'";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1){
            $data = $db->fetch_assoc($result);
            return $data['id'];
        }
        return 0;
    }

    public function delete()
    {
        global $db;
		$SQLdelete = sprintf("DELETE FROM ".__prefixe_db__."langue WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }
	
	public static function build($data){
        global $db;
        $langue = new langue();
        
		$langue->id = $data['id'];
		$langue->nom = $data['nom'];
		$langue->code = $data['code'];
		$langue->defaut = $data['defaut'];
		$langue->flag = $data['flag'];
		$langue->actif = $data['actif'];
		$langue->date_add = $data['date_add'];
		$langue->last_edit = $data['last_edit'];
        return $langue;
    }
}