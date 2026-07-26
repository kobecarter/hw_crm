<?php

class item_facture
{
    static $table =  __prefixe_db__ . "item_facture";

    private $id;
    private $facture;
    private $service;
    private $qte;
    private $discount;
    private $prix;
    private $total;
    private $unite;
    private $titre;
    private $description;
    private $ordre;


    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFacture()
    {
        return $this->facture;
    }

    public function getService()
    {
        return $this->service;
    }

    public function getQte()
    {
        return $this->qte;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getUnite()
    {
        return $this->unite;
    }

    public function getOrdre()
    {
        return $this->ordre;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFacture($facture)
    {
        $this->facture = $facture;
    }

    public function setService($service)
    {
        $this->service = $service;
    }

    public function setQte($qte)
    {
        $this->qte = $qte;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function setUnite($unite)
    {
        $this->unite = $unite;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_facture, id_service, qte, discount,  prix, total, unite, titre, description, ordre) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->service->getId(), "int"),
            GetSQLValueString($this->qte, "int"),
            GetSQLValueString($this->discount, "double"),
            GetSQLValueString($this->prix, "double"),
            GetSQLValueString($this->total, "double"),
            GetSQLValueString($this->unite, "text"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->ordre, "int")
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
            "UPDATE " . static::$table . " SET  id_facture = %s, id_service = %s, qte = %s, discount = %s, prix = %s, total = %s, unite = %s, titre = %s, description = %s, ordre = %s  WHERE id = %s",
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->service->getId(), "int"),
            GetSQLValueString($this->qte, "int"),
            GetSQLValueString($this->discount, "double"),
            GetSQLValueString($this->prix, "double"),
            GetSQLValueString($this->total, "double"),
            GetSQLValueString($this->unite, "text"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->id, "int")
        );
        //echo $SQLupdate;
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
        $item_facture = new item_facture();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $item_facture = static::build($data);
        }
        return $item_facture;
    }

    public static function findAllByFacture($id_facture)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_facture = %s ORDER BY date_add DESC, id DESC",
            GetSQLValueString($id_facture, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item = static::build($data);
            array_push($items, $item);
        }
        return $items;
    }

    public static function build($data)
    {
        $item_facture = new item_facture();
        $item_facture->setId($data['id']);
        $item_facture->setFacture(isset($data['id_facture']) ? facture::find($data['id_facture'],$_SESSION['agence']) : null);
        $item_facture->setService(isset($data['id_service']) ? service::find($data['id_service']) : null);
        $item_facture->setQte($data['qte']);
        $item_facture->setDiscount($data['discount']);
        $item_facture->setPrix($data['prix']);
        $item_facture->setTotal($data['total']);
        $item_facture->setUnite($data['unite']);
        $item_facture->setTitre($data['titre']);
        $item_facture->setDescription($data['description']);
        $item_facture->setOrdre($data['ordre']);
        return $item_facture;
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
    public function toArray()
    {
        mb_internal_encoding("UTF-8");
        try {
            return array(
                "qte" => $this->getQte(),
                "discount" => $this->getDiscount(),
                "prix" => $this->getPrix(),
                "total" => $this->getTotal(),
                "unite" => $this->getUnite(),
                "titre" => $this->getTitre(), // mb_convert_encoding($this->getTitre(), 'Windows-1252', 'UTF-8'),
            );
        } catch (Exception $e) {
            return array();
        }
    }

    // api

    public static function buildApi($data)
    {
        $items_facture = [
            'id' => $data['id'],
            'facture' => facture::findByIdApi($data['id_facture']),
            'service' => service::find($data['id_service']),
            'qte' => $data['qte'],
            'discount' => $data['discount'],
            'prix' => $data['prix'],
            'total' => $data['total'],
            'unite' => $data['unite'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'ordre' => $data['ordre'],
        ];
        return $items_facture;
    }

    public static function findByIdApi($id)
    {
        global $db;
        $item_facture = new item_facture();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $item_facture = static::buildApi($data);
        }
        return $item_facture;
    }

    public static function findAllByFactureApi($id_facture)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_facture = %s ORDER BY date_add DESC, id DESC",
            GetSQLValueString($id_facture, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item = static::buildApi($data);
            array_push($items, $item);
        }
        return $items;
    }
}
