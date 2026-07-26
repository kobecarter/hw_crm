<?php

class notification
{
    static $table =  __prefixe_db__ . "notification";

    private $id;
    private $user_id;
    private $type_user;
    private $title;
    private $message;
    private $type_message;
    private $url;
    private $class;
    private $data;
    private $is_read;
    private $date_add;
    private $date_edit;

    public function __construct(){
        $this->id = 0;
    }

    public function getId(){
        return $this->id;
    }

    public function getUser(){
        return $this->user_id;
    }

    public function getTypeUser(){
        return $this->type_user;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getMessage(){
        return $this->message;
    }

    public function getTypeMessage(){
        return $this->type_message;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getClass(){
        return $this->class;
    }

    public function getData(){
        return $this->data;
    }

    public function getIsRead(){
        return $this->is_read;
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

    public function setUser($user_id){
        $this->user_id = $user_id;
    }

    public function setTypeUser($type_user){
        $this->type_user = $type_user;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function setTypeMessage($type_message){
        $this->type_message = $type_message;
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function setClass($class){
        $this->class = $class;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function setIsRead($is_read){
        $this->is_read = $is_read;
    }

    public function setDateAdd($date_add){
        $this->date_add = $date_add;
    }

    public function setDateEdit($date_edit){
        $this->date_edit = $date_edit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (user_id, type_user, title, message, type_message, url, class, data, is_read, date_add, date_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->user_id->getId(), "int"),
            GetSQLValueString($this->type_user, "text"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->type_message, "text"),
            GetSQLValueString($this->url, "text"),
            GetSQLValueString($this->class, "text"),
            GetSQLValueString($this->data, "text"),
            GetSQLValueString($this->is_read, "int"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET user_id = %s, type_user = %s, title = %s, message = %s, type_message = %s,url = %s, class = %s, data = %s, is_read = %s, date_edit = %s WHERE id = %s",
            GetSQLValueString($this->user_id->getId(), "int"),
            GetSQLValueString($this->type_user, "text"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->type_message, "text"),
            GetSQLValueString($this->url, "text"),
            GetSQLValueString($this->class, "text"),
            GetSQLValueString($this->data, "text"),
            GetSQLValueString($this->is_read, "int"),
            GetSQLValueString($this->date_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $notification = new notification();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $notification = static::build($data);
        }
        return $notification;
    }

    public static function findAll($type_user = false,$user_id = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table. " where is_read = 0");
        if($type_user == 'resourcehumaine'){
            $SQLselect .= " AND type_user = 'resourcehumaine' AND user_id = ".$user_id;
        }else if($type_user == 'user'){
            $SQLselect .= " AND type_user = 'user' AND user_id = '0'";
        }
        $SQLselect .= " ORDER BY id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $notifications = static::build($data);
            array_push($items, $notifications);
        }
        return $items;
    }

    public static function build($data){
        $notification = new notification();
        $notification->setId($data['id']);
        $notification->setUser($data['type_user'] == 'resourcehumaine' ? resourcehumaine::find($data["user_id"]) : new user());
        $notification->setTypeUser($data['type_user']);
        $notification->setTitle($data['title']);
        $notification->setMessage($data['message']);
        $notification->setTypeMessage($data['type_message']);
        $notification->setUrl($data['url']);
        $notification->setClass($data['class']);
        $notification->setData($data['data']);
        $notification->setIsRead($data['is_read']);
        $notification->setDateAdd($data['date_add']);
        $notification->setDateEdit($data['date_edit']);
        return $notification;
    }
}

