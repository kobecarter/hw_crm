<?php
class droit {
	
    static $table =  __prefixe_db__ . "droits";

	private $id;
    private $profil;
	private $module;
	private $action;
	
	public function __construct(){
        $this->id = 0;
    }
	
    public function __destruct(){

    }

    public function getId(){
        return $this->id;
    }

    public function getProfil(){
        return $this->profil;
    }
	
	public function getModule(){
        return $this->module;
    }
	
	public function getAction(){
        return $this->action;
    }
	
	public function setId($id){
        $this->id = $id;
    }

    public function setProfil($profil){
        $this->profil = $profil;
    }
	
	public function setModule($module){
        $this->module = $module;
    }
	
	public function setAction($action){
        $this->action = $action;
    }
	
	// action base de données
	public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_profil, module, action) VALUES (%s, %s, %s)",
            GetSQLValueString($this->profil->getId(), "int"),
			GetSQLValueString($this->module, "text"),
			GetSQLValueString($this->action, "text")
        );

		if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 2;
        }
    }
	
	public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_profil = %s, module = %s, action = %s WHERE id = %s",
            GetSQLValueString($this->profil->getId(), "int"),
			GetSQLValueString($this->module, "text"),
			GetSQLValueString($this->action, "text"),
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
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }
	
	public static function find($id){
        global $db;
        $droit = new droit();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int"));
		
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $droit = static::build($data);
        }
        return $droit;
    }
	
	public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT * FROM " . static::$table;
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $droit = static::build($data);
            array_push($items, $droit);
        }
        return $items;
    }
	
	public static function build($data){
		global $db;
        $droit = new droit();
        $droit->setId($data['id']);
        $droit->setProfil(isset($data['V']) ? profil::find($data['id_profil']) : NULL);
		$droit->setModule($data['module']);
		$droit->setAction($data['action']);
        return $profil;
    }
}
?>