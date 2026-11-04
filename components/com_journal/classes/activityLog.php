<?php

class activityLog
{
    static $table = __prefixe_db__ . "log";

    private $id;
    private $id_agence;
    private $id_user;
    private $entite;
    private $id_entite;
    private $action;
    private $description;
    private $ip;
    private $date_add;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdAgence()
    {
        return $this->id_agence;
    }

    public function getIdUser()
    {
        return $this->id_user;
    }

    public function getEntite()
    {
        return $this->entite;
    }

    public function getIdEntite()
    {
        return $this->id_entite;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    // Journal immuable : un seul point d'écriture, appelé depuis les modèles
    // (edit/delete) et les contrôleurs (add, voir le plan - last_id() ne doit
    // jamais être consommé par ce propre INSERT avant que l'appelant n'ait
    // récupéré l'id de l'entité qu'il vient de créer).
    public static function add($entite, $idEntite, $action, $description)
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_agence, id_user, entite, id_entite, action, description, ip, date_add) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString(isset($_SESSION['agence']) ? $_SESSION['agence'] : null, "int"),
            GetSQLValueString(isset($_SESSION['user']) ? $_SESSION['user']->getId() : null, "int"),
            GetSQLValueString($entite, "text"),
            GetSQLValueString($idEntite, "int"),
            GetSQLValueString($action, "text"),
            GetSQLValueString($description, "text"),
            GetSQLValueString(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null, "text"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function findAll($agence = null)
    {
        global $db;
        $SQLselect = "SELECT * FROM " . static::$table;
        if ($agence !== null) {
            $SQLselect .= " WHERE id_agence = " . intval($agence);
        }
        $SQLselect .= " ORDER BY date_add DESC";
        $rows = $db->queryS($SQLselect);
        $logs = array();
        if ($rows) {
            foreach ($rows as $row) {
                array_push($logs, self::build($row));
            }
        }
        return $logs;
    }

    public static function build($data)
    {
        $log = new activityLog();
        $log->id = $data['id'];
        $log->id_agence = $data['id_agence'];
        $log->id_user = $data['id_user'];
        $log->entite = $data['entite'];
        $log->id_entite = $data['id_entite'];
        $log->action = $data['action'];
        $log->description = $data['description'];
        $log->ip = $data['ip'];
        $log->date_add = $data['date_add'];
        return $log;
    }
}
