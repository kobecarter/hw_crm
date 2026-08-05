<?php

class presentationFile
{
    static $table = __prefixe_db__ . "presentation_file";

    private $id;
    private $id_client;
    private $fichier;
    private $extracted_json;
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

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getFichier()
    {
        return $this->fichier;
    }

    public function getExtractedJson()
    {
        return $this->extracted_json;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setIdClient($id_client)
    {
        $this->id_client = $id_client;
    }

    public function setFichier($fichier)
    {
        $this->fichier = $fichier;
    }

    public function setExtractedJson($extracted_json)
    {
        $this->extracted_json = $extracted_json;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_client, fichier, extracted_json, date_add, last_edit) VALUES (%s, %s, %s, %s, %s)",
            GetSQLValueString($this->id_client, "int"),
            GetSQLValueString($this->fichier, "text"),
            GetSQLValueString($this->extracted_json, "text"),
            GetSQLValueString(date("Y-m-d H:i:s"), "text"),
            GetSQLValueString(date("Y-m-d H:i:s"), "text")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function findByClient($id_client)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE id_client = " . intval($id_client) . " ORDER BY date_add DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $row) {
            $items[] = self::hydrate($row);
        }
        return $items;
    }

    private static function hydrate($row)
    {
        $item = new presentationFile();
        $item->setId($row['id']);
        $item->setIdClient($row['id_client']);
        $item->setFichier($row['fichier']);
        $item->setExtractedJson($row['extracted_json']);
        return $item;
    }
}
