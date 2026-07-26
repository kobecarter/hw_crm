<?php

class fileresourcehumaine
{
    static $table =  __prefixe_db__ . "fileresourcehumaine";

    private $id;
    private $resourcehumaine;
    private $title;
    private $file;

    public function __construct(){
        $this->id = 0;
    }

    public function getId(){
        return $this->id;
    }

    public function getResourcehumaine(){
        return $this->resourcehumaine;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getFile(){
        return $this->file;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setResourcehumaine($resourcehumaine){
        $this->resourcehumaine = $resourcehumaine;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setFile($file){
        $this->file = $file;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, title, file) VALUES (%s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->file, "text")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_resourcehumaine = %s, title = %s, file = %s WHERE id = %s",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->file, "text"),
            GetSQLValueString($this->getId(), "int")
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

    public static function deleteGroupe($id_resourcehumaine)
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id_resourcehumaine = %s",
            GetSQLValueString($id_resourcehumaine, "int")
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
        $fileresourcehumaine = new fileresourcehumaine();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $fileresourcehumaine = static::build($data);
        }
        return $fileresourcehumaine;
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
        $fileresourcehumaine = new fileresourcehumaine();
        $fileresourcehumaine->setId($data['id']);
        $fileresourcehumaine->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $fileresourcehumaine->setTitle($data['title']);
        $fileresourcehumaine->setFile($data['file']);
        return $fileresourcehumaine;
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

