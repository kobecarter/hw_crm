<?php

class service
{
    static $table =  __prefixe_db__ . "service";
    static $table2 =  __prefixe_db__ . "details_service";

    private $id;
    private $categorie;
	private $prix;
    private $ordre;
    private $facturation;
	private $active;
    private $titre;
    private $description;
    private $intervenant;
    private $unite;
    private $date_add;
    private $last_edit;
    private $langue;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }
	
	public function getCategorie()
    {
        return $this->categorie;
    }

    public function getPrix()
    {
        return $this->prix;
    }
	
    public function isActive()
    {
        return $this->active ? 1 : 0;
    }

    public function getActive()
    {
        return $this->active;
    }

	public function getOrdre()
    {
        return $this->ordre;
    }
    
    public function getFacturation()
    {
        return $this->facturation;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getIntervenant()
    {
        return $this->intervenant;
    }

    public function getUnite()
    {
        return $this->unite;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function getLangue()
    {
        return $this->langue;
    }

	
    public function setId($id)
    {
        $this->id = $id;
    }
	
	public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }

    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

	public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }
    
    public function setFacturation($facturation)
    {
        $this->facturation = $facturation;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setIntervenant($intervenant)
    {
        $this->intervenant = $intervenant;
    }

    public function setUnite($unite)
    {
        $this->unite = $unite;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function setLangue($langue)
    {
        $this->langue = $langue;
    }

    public function add()
    {
        global $db;

        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_categorie, prix, ordre, active, facturation, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($this->categorie->getId(), "int"),
            GetSQLValueString($this->prix, "double"),
            GetSQLValueString($this->ordre, "int"),
			GetSQLValueString($this->active, "int"),
			GetSQLValueString($this->facturation, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );

        if (!$db->query($SQLinsert)) 
        {
            $id_service = $db->last_id();
            $SQLinsert2 = sprintf("INSERT INTO " . static::$table2 . " (id_service, titre, description, intervenant, unite, langue) VALUES (%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_service, "int"),
                GetSQLValueString($this->titre, "text"),
                GetSQLValueString($this->description, "text"),
                GetSQLValueString($this->intervenant, "text"),
                GetSQLValueString($this->unite, "text"),
                GetSQLValueString($this->langue, "text")
            );
            if (!$db->query($SQLinsert2)) {
                return 1;
            }
            return 2;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  id_categorie = %s, prix = %s, ordre = %s, facturation = %s, active = %s, last_edit = %s WHERE id = %s",
			GetSQLValueString($this->categorie->getId(), "int"),
            GetSQLValueString($this->prix, "double"),
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->facturation, "text"),
			GetSQLValueString($this->active, "int"),				 
            GetSQLValueString($this->last_edit, "date"), 
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
            $SQLselect = sprintf("SELECT * FROM " . static::$table2 . " WHERE id_service = %s AND langue = %s",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->langue, "text")
            );
            
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 0) {
                $SQLupdate = sprintf("INSERT INTO " . static::$table2 . " (id_service, titre, description, intervenant, unite, langue) VALUES (%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->titre, "text"),
                GetSQLValueString($this->description, "text"),
                GetSQLValueString($this->intervenant, "text"),
                GetSQLValueString($this->unite, "text"),
                GetSQLValueString($this->langue, "text")
                );
            } else {
                $SQLupdate = sprintf("UPDATE " . static::$table2 . " SET titre = %s, description = %s, intervenant = %s, unite = %s WHERE id_service = %s AND langue = %s",
					GetSQLValueString($this->titre, "text"),
					GetSQLValueString($this->description, "text"),
					GetSQLValueString($this->intervenant, "text"),
					GetSQLValueString($this->unite, "text"),
                    GetSQLValueString($this->id, "int"),
                    GetSQLValueString($this->langue, "text")
                );
            }
            if (!$db->query($SQLupdate)) {
                return 1;
            } else {
                return 2;
            }
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
        $SQLdelete2 = sprintf("DELETE FROM " . static::$table2 . " WHERE id_service = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete) && !$db->query($SQLdelete2)){
            return 1;
        } else {
            return 0;
        }
    }

    public function enable()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET active = %s WHERE id = %s",
            GetSQLValueString($this->getActive(), "int"),
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLupdate)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id, $langue = 'fr')
    {
        global $db;
        $service = new service();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_service AND langue = %s WHERE A.id = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $service = static::build($data);
        }
        return $service;
    }

    public static function findAll($langue, $active = false, $categorie = false, $ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A RIGHT JOIN " . static::$table2 . " B ON A.id = B.id_service AND langue = %s WHERE 1 = 1",
            GetSQLValueString($langue, "text")
        );
        if($active){
            $SQLselect .= " AND active = 1";
        }
        if($categorie){
            $SQLselect .= " AND id_categorie = " . intval($categorie);
        }
		if($ordre){
			$SQLselect .= " ORDER BY ordre ASC";
		}
		
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $service = static::build($data);
            array_push($items, $service);
        }
        return $items;
    }

    public static function build($data){
        global $db;
        $service = new service();
        
        $service->setId($data['ID']);
        $service->setCategorie(categorie::find($data['id_categorie'], $data["langue"]));
        $service->setPrix($data['prix']);
		$service->setOrdre($data['ordre']);
		$service->setFacturation($data['facturation']);
        $service->setActive($data['active']);
        $service->setTitre($data['titre']);
        $service->setDescription($data['description']);
        $service->setIntervenant($data['intervenant']);
        $service->setUnite($data['unite']);
        $service->setDateAdd($data['date_add']);
        $service->setLastEdit($data['last_edit']);
        $service->setLangue($data['langue']);
        return $service;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
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

    public static function enableMultiple($data)
    {
        global $db;
        if(isset($data['ids']) && !empty($data['ids']) && isset($data['active']) && $data['active'] != '')
        {
            extract($data);

            $SQLupdate = sprintf("UPDATE " . static::$table . " SET active = $active WHERE id in$ids");
            
            if(!$db->query($SQLupdate))
                return 1;
            else
                return 2;
        }
        else
            return 0;
    }
    
    public static function deleteMultiple($data){
        
        global $db;	
        if(isset($data['ids']) && !empty($data['ids'])){
            extract($data);
            $SQLdelete = "DELETE FROM ". static::$table ." WHERE id in $ids";
            $SQLdelete2 = "DELETE FROM ". static::$table2 ." WHERE id_service in $ids";
            if(!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
                //seo();
                return 1;
            }else
                return 2;
        }
        else
            return 0;
    }

    // api

    public static function buildApi($data){
        $service_data = [
            'id' => $data['ID'],
            'categorie' => categorie::find($data['id_categorie'], $data["langue"]),
            'prix' => $data['prix'],
            'ordre' => $data['ordre'],
            'active' => $data['active'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'intervenant' => $data['intervenant'],
            'unite' => $data['unite'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
            'langue' => $data['langue'],
        ];
        return $service_data;
    }

    public static function findByIdApi($id, $langue = 'fr')
    {
        global $db;
        $service = new service();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_service AND langue = %s WHERE A.id = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $service = static::buildApi($data);
        }
        return $service;
    }

    public static function findAllApi($langue, $active = false, $categorie = false, $ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_service AND langue = %s WHERE 1 = 1",
            GetSQLValueString($langue, "text")
        );
        if($active){
            $SQLselect .= " AND active = 1";
        }
        if($categorie){
            $SQLselect .= " AND id_categorie = " . intval($categorie);
        }
		if($ordre){
			$SQLselect .= " ORDER BY ordre ASC";
		}
		
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $service = static::buildApi($data);
            array_push($items, $service);
        }
        return $items;
    }

}