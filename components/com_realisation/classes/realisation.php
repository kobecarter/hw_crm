<?php

class realisation
{
    static $table =  __prefixe_db__ . "realisation";

    private $id;
    private $photo;
    private $titre;
    private $ordre;
    private $extrait;
    private $texte;
    private $url_project;
    private $date_add;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getExtrait()
    {
        return $this->extrait;
    }

    public function setExtrait($extrait)
    {
        $this->extrait = $extrait;
    }

    public function getTexte()
    {
        return $this->texte;
    }

    public function setTexte($texte)
    {
        $this->texte = $texte;
    }

    public function getUrlProject()
    {
        return $this->url_project;
    }

    public function setUrlProject($url_project)
    {
        $this->url_project = $url_project;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function getOrdre()
    {
        return $this->ordre;
    }

    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }




    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (ordre,titre, extrait, texte, photo, url_project, date_add) VALUES (%d, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->extrait, "text"),
            GetSQLValueString($this->texte, "text"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->url_project, "text"),
            GetSQLValueString($this->date_add, "date"),

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
            "UPDATE " . static::$table . " SET ordre = %d, titre = %s, extrait = %s, texte = %s, photo = %s, url_project = %s, date_add = %s WHERE id = %d",
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->extrait, "text"),
            GetSQLValueString($this->texte, "text"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->url_project, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->id, "int")
        );
        // die(var_dump($SQLupdate));
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


    public static function find($id)
    {
        global $db;
        $realisation = new realisation();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $realisation = static::build($data);
        }
        return $realisation;
    }

    public static function findAll($ordre = 0)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT * FROM " . static::$table;

        if ($ordre != 0) {
            $SQLselect .= " ORDER BY ordre ASC";
        } else {
            $SQLselect .= " ORDER BY date_add DESC, id DESC";
        }


        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $realisation = static::build($data);
            array_push($items, $realisation);
        }
        return $items;
    }

    public static function build($data)
    {
        $realisation = new realisation();
        $realisation->setId($data['id']);
        $realisation->setTitre($data['titre']);
        $realisation->setExtrait($data['extrait']);
        $realisation->setTexte($data['texte']);
        $realisation->setUrlProject($data['url_project']);
        $realisation->setDateAdd($data['date_add']);
        $realisation->setPhoto($data['photo']);
        $realisation->setOrdre($data['ordre']);

        return $realisation;
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


    public function toArray()
    {

        global $siteURL;
        return array(
            "id" => $this->getId(),
            "titre" => $this->getTitre(),
            "extrait" => $this->getExtrait(),
            "texte" => $this->getTexte(),
            "url_project" =>  $this->getUrlProject(),
            "photo" => $this->getPhoto() ? $siteURL . 'images/realisation/' . $this->getPhoto() : "",
            "ordre" => $this->getOrdre(),
            "date_add" => $this->getDateAdd()
        );
    }
}
