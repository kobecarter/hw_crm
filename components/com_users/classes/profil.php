<?php
class profil {
	
    static $table =  __prefixe_db__ . "profils";
	static $table2 =  __prefixe_db__ . "droits";

	private $id;
    private $profil;
	
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
	
	public function setId($id){
        $this->id = $id;
    }

    public function setProfil($profil){
        $this->profil = $profil;
    }

    public function hasDroit($action, $module){
        global $db;
        $SQLselect = "SELECT * FROM ".__prefixe_db__."droits WHERE module = '$module' AND action = '$action' AND id_profil = ".$this->id;
        $result = $db->query($SQLselect);
        if($db->num_rows($result) == 1){
            return 1;
        }else{
            return 0;
        }
        
    }
	
	// action base de données
	public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (profil) VALUES (%s)",
            GetSQLValueString($this->profil, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET profil = %s WHERE id = %s",
            GetSQLValueString($this->profil, "text"),
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
        $profil = new profil();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int"));
		
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $profil = static::build($data);
        }
        return $profil;
    }
	
	public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT * FROM " . static::$table;
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $profil = static::build($data);
            array_push($items, $profil);
        }
        return $items;
    }
	
	public static function build($data){
		global $db;
        $profil = new profil();
        $profil->setId($data['id']);
        $profil->setProfil($data['profil']);
        return $profil;
    }
}
?>