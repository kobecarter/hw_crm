<?php

class relance
{
    static $table =  __prefixe_db__ . "relance";
    static $tableClient =  __prefixe_db__ . "client";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $client;
    private $facture;
    private $type;
    private $date;
    private $photo;
    private $remarque;
    private $traite;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getFacture()
    {
        return $this->facture;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function getTraite()
    {
        return $this->traite;
    }

    public function isTraite()
    {
        return $this->traite == 1 ? true : false;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setFacture($facture)
    {
        $this->facture = $facture;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setRemarque($seo_titre)
    {
        $this->remarque = $seo_titre;
    }

    public function setTraite($traite)
    {
        $this->traite = $traite;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function getDaysLeft()
    {
        $from = new DateTime();
        $to = new DateTime($this->date);

        $interval = $from->diff($to);
        return $interval->format('%r%a');
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, id_facture, type, date, remarque, photo, traite, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->traite, "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date"),
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_client = %s, id_facture = %s, type = %s, date = %s, remarque = %s, photo = %s, traite = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->traite, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 2;
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
        $relance = new relance();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id WHERE C.id = %s and A.id = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $relance = static::build($data);
        }
        return $relance;
    }

    public static function findAll($agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where C.id = %s",
        GetSQLValueString($agence, "int"),
    );
        $SQLselect .= " ORDER BY A.date";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }
    
    public static function findAllNonTraite($agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id WHERE C.id = %s AND traite = 0",
        GetSQLValueString($agence, "int"),
    );
        $SQLselect .= " ORDER BY A.date";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }

    public static function findByClient($client_id,$agence=1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where C.id = %s and A.id_client = %s",
        GetSQLValueString($agence, "int"),
        GetSQLValueString($client_id, "int")
    );
        $SQLselect .= " ORDER BY date";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }

    public static function findByFacture($facture)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A WHERE id_facture = %s",
            GetSQLValueString($facture, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }

    public static function build($data)
    {
        $relance = new relance();
        $relance->setId($data['id']);
        $relance->setClient(client::find($data['id_client'],$_SESSION['agence']));
        $relance->setFacture(facture::find($data['id_facture'],$_SESSION['agence']));
        $relance->setType($data['type']);
        $relance->setDate($data['date']);
        $relance->setRemarque($data['remarque']);
        $relance->setPhoto($data['photo']);
        $relance->setTraite($data['traite']);
        $relance->setDateAdd($data['date_add']);
        $relance->setLastEdit($data['last_edit']);
        return $relance;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count()
    {
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function countByClient($client_id){
        global $db;
        $SQLcount = sprintf("SELECT count(id) as c FROM " . static::$table . " where id_client = %s",
            GetSQLValueString($client_id, "int")
        );
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }
}
