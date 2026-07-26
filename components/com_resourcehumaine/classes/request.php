<?php

class request
{
    static $table =  __prefixe_db__ . "request";

    private $id;
    private $resourcehumaine;
    private $title;
    private $description;
    private $response;
    private $status;
    private $date_add;
    private $date_edit;

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
    
    public function getDescription(){
        return $this->description;
    }

    public function getResponse(){
        return $this->response;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getDateAdd(){
        return $this->date_add;
    }


    public function getDateEdit(){
        return $this->date_edit;
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
    
    public function setDescription($description){
        $this->description = $description;
    }

    public function setResponse($response){
        $this->response = $response;
    }

    public function setStatus($status){
        $this->status = $status;
    }


    public function setDateAdd($date_add){
        $this->date_add = $date_add;
    }

    public function setDateEdit($date_edit){
        $this->date_edit = $date_edit;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
        //155058/139558
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, title, description, response, status, date_add, date_edit) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->response, "text"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->date_edit, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_resourcehumaine = %s, title = %s, description = %s, response = %s,status = %s, date_edit = %s WHERE id = %s",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->response, "text"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->date_edit, "date"),
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
        $request = new request();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $request = static::build($data);
        }
        return $request;
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
        $request = new request();
        $request->setId($data['id']);
        $request->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $request->setTitle($data['title']);
        $request->setDescription($data['description']);
        $request->setResponse($data['response']);
        $request->setStatus($data['status']);
        $request->setDateAdd($data['date_add']);
        $request->setDateEdit($data['date_edit']);
        return $request;
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

