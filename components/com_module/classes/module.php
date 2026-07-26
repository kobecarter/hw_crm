<?php
class module
{
	
    static $table =  __prefixe_db__ . "modules";
	
	private $id;
    private $id_module; // com_*
    private $enabled;
    private $installed;
    private $nom;
    private $classe;
    private $nom_table;
    private $translated;
    private $url;
    private $systeme;
    private $icon;
    private $position;

    /*public function __construct($id_module, $db) {

        $SQLselect = "SELECT * FROM ".__prefixe_db__."modules WHERE id_module = '". $id_module ."'";
        $result = $db->query($SQLselect);

        if ($db->num_rows($result) == 1){
            $data = $db->fetch_assoc($result);

            $this->id = $data['id'];
            $this->id_module = $data['id_module'];
            $this->enabled = $data['enabled'];
            $this->installed = $data['installed'];
            $this->nom = $data['nom'];
            $this->classe = $data['classe'];
            $this->nom_table = $data['nom_table'];
            $this->translated = $data['translated'];
            $this->url = $data['url'];
            $this->system = $data['system'];
            $this->icon = $data['icon'];
            $this->position = $data['positioned'];
        } else {
            $this->id = 0;
            $this->id_module = $id_module;
            $this->enabled = 0;
            $this->installed = 0;
            $this->url = 0;
            $this->system = 0;
        }
    }*/
	
	public function __construct()
    {
        $this->id = 0;
    }

    public function __destruct(){

    }
	
	// Getter
	
    public function getId(){
        return $this->id;
    }

    public function getIdModule(){
        return $this->id_module;
    }

    public function isEnabled(){
        return $this->enabled == 1 ? true : false ;
    }

    public function isInstalled(){
        return $this->installed == 1 ? true : false ;
    }

    public function getNom(){
        return $this->nom;
    }

    public function getClasse(){
        return $this->classe;
    }

    public function getNomTable(){
        return $this->nom_table;
    }

    public function isTranslated(){
        return $this->translated == 1 ? true : false ;
    }

    public function isUrl(){
        return $this->url == 1 ? true : false ;
    }

    public function isSystem(){
        return $this->systeme == 1 ? true : false ;
    }

    public function getIcon(){
        return $this->icon;
    }

    public function getPosition(){
        return $this->position;
    }
	
	// Setter
	
	public function setId($id){
        $this->id = $id;
    }
	
	public function setIdModule($id_module){
        $this->id_module = $id_module;
    }
	
	public function setEnabled($enabled){
        $this->enabled = $enabled;
    }
	
	public function setInstalled($installed){
        $this->installed = $installed;
    }
	
	public function setNom($nom){
        $this->nom = $nom;
    }
	
	public function setClasse($classe){
        $this->classe = $classe;
    }
	
	public function setNomTable($nom_table){
        $this->nom_table = $nom_table;
    }
	
	public function setTranslated($translated){
        $this->translated = $translated;
    }
	
	public function setURL($url){
        $this->url = $url;
    }
	
	public function setSysteme($systeme){
        $this->systeme = $systeme;
    }
	
	public function setIcon($icon){
        $this->icon = $icon;
    }
	
	public function setPosition($position){
        $this->position = $position;
    }
	
	public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_module, enabled, installed, nom, classe, nom_table, translated, url, system, icon, positioned) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->id_module, "text"),
            GetSQLValueString($this->enabled, "int"),
            GetSQLValueString($this->installed, "int"),
			GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->classe, "text"),
            GetSQLValueString($this->nom_table, "text"),
			GetSQLValueString($this->translated, "int"),
            GetSQLValueString($this->url, "int"),
            GetSQLValueString($this->systeme, "int"),
			GetSQLValueString($this->icon, "text"),				 
            GetSQLValueString($this->position, "text")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_module = %s, enabled = %s, installed = %s, nom = %s, classe = %s, nom_table = %s, translated = %s, url = %s, system = %s, icon = %s, positioned = %s WHERE id = %s",
            GetSQLValueString($this->id_module, "text"),
            GetSQLValueString($this->enabled, "int"),
            GetSQLValueString($this->installed, "int"),
			GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->classe, "text"),
            GetSQLValueString($this->nom_table, "text"),
			GetSQLValueString($this->translated, "int"),
            GetSQLValueString($this->url, "int"),
            GetSQLValueString($this->systeme, "int"),
			GetSQLValueString($this->icon, "text"),				 
            GetSQLValueString($this->position, "text"),
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
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }
	
    public static function exists($id_module){
        global $db;
        $SQLselect = "SELECT * FROM ".__prefixe_db__."modules WHERE id_module = '". $id_module ."' AND installed = 1 AND enabled = 1";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1){
            return true;
        }else{
            return false;
        }
    }
	
	public static function find($id)
    {
        global $db;
        $module = new module();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $module = static::build($data);
        }
        return $module;
    }
	
	public static function findByModule($id_module)
    {
        global $db;
        $module = new module();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_module = %s",
            GetSQLValueString($id_module, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $module = static::build($data);
        }
        return $module;
    }
	
    public static function findAll(){
        global $db;
        $modules = array();
        $SQLselect = "SELECT * FROM " . __prefixe_db__ . "modules WHERE enabled = 1 AND installed = 1 ORDER BY ordre ASC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
			$module = static::build($data);
            array_push($modules, $module);
        }
        return $modules;
    }

    public static function findAllUrl(){
        global $db;
        $ids = array();
        $SQLselect = "SELECT id_module FROM " . __prefixe_db__ . "modules WHERE enabled = 1 AND installed = 1 AND url = 1 ORDER BY ordre ASC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($ids, $data['id_module']);
        }
        return $ids;
    }

    public static function findAllPosition($position){ // center, side, param
        global $db;
        $modules = array();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE enabled = 1 AND installed = 1 AND positioned = '".$position."' ORDER BY ordre ASC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $module = static::build($data);
            array_push($modules, $module);
        }
        return $modules;
    }
	
	public static function build($data){
		global $db;
        $module = new module();
        $module->setId($data['id']);
        $module->setIdModule($data['id_module']);
		$module->setEnabled($data['enabled']);
		$module->setInstalled($data['installed']);
		$module->setNom($data['nom']);
		$module->setClasse($data['classe']);
		$module->setNomTable($data['nom_table']);
		$module->setTranslated($data['translated']);
		$module->setURL($data['url']);
		$module->setSysteme($data['system']);
		$module->setIcon($data['icon']);
		$module->setPosition($data['positioned']);

        return $module;
    }
}

?>
