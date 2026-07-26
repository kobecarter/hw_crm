<?php

class reclamation
{
    static $table =  __prefixe_db__ . "reclamation";
    static $tableFacture =  __prefixe_db__ . "facture";
    static $tableClient =  __prefixe_db__ . "client";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $client;
    private $department;
    private $sujet;
    private $message;
    private $etat;
    private $date_add;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isProcess()
    {
        return $this->etat ? 1 : 0;
    }

    public function getClient()
    {
        return $this->client;
    }
    
    public function getDepartment()
    {
        return $this->department;
    }

    public function getSujet()
    {
        return $this->sujet;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function setSujet($sujet)
    {
        $this->sujet = $sujet;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, department, sujet, message, etat, date_add) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->department, "text"),
            GetSQLValueString($this->sujet, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->etat, "int"),
            GetSQLValueString($this->date_add, "date")
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_client = %s, department = %s, sujet = %s, message = %s, etat = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->department, "text"),
            GetSQLValueString($this->sujet, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->etat, "int"),
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

        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }


    public static function find($id,$agence = 1)
    {
        global $db;
        $reclamation = new reclamation();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id WHERE C.id = %s and A.id = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $reclamation = static::build($data);
        }
        return $reclamation;
    }

    public static function findAll($etat = false, $client = false,$agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where C.id = %s",
            GetSQLValueString($agence, "int"),
        );

        if ($etat) {
            $SQLselect .= " WHERE etat = 1";
        }

        if ($client) {
            $SQLselect .= " WHERE A.id_client = $client";
        }

        $SQLselect .= " ORDER BY A.date_add DESC, id DESC";

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $reclamation = static::build($data);
            array_push($items, $reclamation);
        }
        return $items;
    }

    public static function build($data)
    {
        $reclamation = new reclamation();
        $reclamation->setId($data['id']);
        $reclamation->setClient(client::find($data['id_client'],$_SESSION['agence']));
        $reclamation->setDepartment($data['department']);
        $reclamation->setSujet($data['sujet']);
        $reclamation->setMessage($data['message']);
        $reclamation->setEtat($data['etat']);
        $reclamation->setDateAdd($data['date_add']);
        return $reclamation;
    }
    

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count($year = false)
    {
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;

        if ($year) {
            $SQLcount .= " WHERE YEAR(date_add) = $year";
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    // API
    public static function buildApi($data)
    {
        $reclamation = array(
            'id' => $data['id'],
            'client' => client::ApiFindById($data['id_client']),
            'department' => $data['department'],
            'sujet' => $data['sujet'],
            'message' => $data['message'],
            'etat' => $data['etat'],
            'date_add' => $data['date_add'],
        );
        return $reclamation;
    }

    public static function findAllByClientApi($clientID = false)
    {
        if(getToken()){
            global $db;
            $items = array();
            $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where A.id_client = %s",
                GetSQLValueString($clientID, "int"),
            );

            $SQLselect .= " ORDER BY A.date_add DESC, id DESC";

            $result = $db->queryS($SQLselect);
            foreach ($result as $data) {
                $reclamation = static::buildApi($data);
                array_push($items, $reclamation);
            }
            return $items;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function createReclamationApi($data)
    {
        if(getToken()){
            global $db;
            $SQLinsert = sprintf(
                "INSERT INTO " . static::$table . " (id_client, department, sujet, message, etat, date_add) VALUES (%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($data['id_client'], "int"),
                GetSQLValueString($data['department'], "text"),
                GetSQLValueString($data['sujet'], "text"),
                GetSQLValueString($data['message'], "text"),
                GetSQLValueString(0, "int"),
                GetSQLValueString(date("Y-m-d"), "date")
            );
            if (!$db->query($SQLinsert)) {
                return json_encode(array("icon"=>"success","message"=>"The request has been successfully sent"));
            } else {
                return json_encode(array("icon"=>"warning","message"=>"The request has not been sent"));
            }
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }
}
