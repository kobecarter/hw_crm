<?php

class fournisseur
{
    static $table =  __prefixe_db__ . "fournisseur";
	static $tableFacture =  __prefixe_db__ . "facture";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $agence;
    private $source;
    private $active;
    private $valide;
    private $titre;
	private $prenom;
    private $nom;
    private $raison_social;
	private $ice;
	private $tel;
	private $tel2;
	private $tel3;
    private $email;
	private $password;
    private $cp;
    private $adresse;
	private $adresse2;
    private $ville;
	private $region;
    private $pays;
	private $photo;
	private $doc;
	private $categorie;
	private $lien;
    private $user_add;
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

    public function getAgence()
    {
        return $this->agence;
    }
    
    public function getSource()
    {
        return $this->source;
    }

    public function isActive()
    {
        return $this->active ? 1 : 0;
    }

    public function getActive()
    {
        return $this->active;
    }
    
    public function isValide()
    {
        return $this->valide ? 1 : 0;
    }

    public function getValide()
    {
        return $this->valide;
    }

    public function getTitre()
    {
        return $this->titre;
    }
	
	public function getPrenom()
    {
        return $this->prenom;
    }

    public function getNom()
    {
        return $this->nom;
    }
	
	public function getRaisonSocial()
    {
        return $this->raison_social;
    }
	
	public function getICE()
    {
        return $this->ice;
    }

    public function getTel()
    {
        return $this->tel;
    }
	
	public function getTel2()
    {
        return $this->tel2;
    }
	
	public function getTel3()
    {
        return $this->tel3;
    }

    public function getEmail()
    {
        return $this->email;
    }
	
	public function getPassword()
    {
        return $this->password;
    }

    public function getCp()
    {
        return $this->cp;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }
	
	public function getAdresse2()
    {
        return $this->adresse2;
    }

    public function getVille()
    {
        return $this->ville;
    }
	
	public function getRegion()
    {
        return $this->region;
    }
    
    public function getCategorie()
    {
        return $this->categorie;
    }
    
    public function getLien()
    {
        return $this->lien;
    }

    public function getPays()
    {
        return $this->pays;
    }
	
	public function getPhoto()
    {
        return $this->photo;
    }
    
    public function getDoc()
    {
        return $this->doc;
    }

    public function getUserAdd()
    {
        return $this->user_add;
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

    public function setAgence($agence)
    {
        $this->agence = $agence;
    }
    
    public function setSource($source)
    {
        $this->source = $source;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }
    
    public function setValide($valide)
    {
        $this->valide = $valide;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }
	
	public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }
	
	public function setRaisonSocial($raison_social)
    {
        $this->raison_social = $raison_social;
    }
	
	public function setICE($ice)
    {
        $this->ice = $ice;
    }

    public function setTel($tel)
    {
        $this->tel = $tel;
    }
	
	public function setTel2($tel2)
    {
        $this->tel2 = $tel2;
    }
	
	public function setTel3($tel3)
    {
        $this->tel3 = $tel3;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
	
	public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }
	
	public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
    }
	
	public function setRegion($region)
    {
        $this->region = $region;
    }

	public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }
    
    public function setLien($lien)
    {
        $this->lien = $lien;
    }

    public function setPays($pays)
    {
        $this->pays = $pays;
    }
	
	public function setPhoto($photo)
    {
        $this->photo = $photo;
    }
    
    public function setDoc($doc)
    {
        $this->doc = $doc;
    }

    public function setUserAdd($user_add)
    {
        $this->user_add = $user_add;
    }
    
    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence, source, active, valide, titre, prenom, nom, raison_social, ice, tel, tel2, tel3, email, password, cp, adresse, adresse2, ville, region, categorie, lien, pays, photo, doc, user_add, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->source, "text"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->valide, "int"),
            GetSQLValueString($this->titre, "text"),
			GetSQLValueString($this->prenom, "text"),
            GetSQLValueString($this->nom, "text"),
			GetSQLValueString($this->raison_social, "text"),
			GetSQLValueString($this->ice, "text"),				 
            GetSQLValueString($this->tel, "text"),
			GetSQLValueString($this->tel2, "text"),
			GetSQLValueString($this->tel3, "text"),				 
            GetSQLValueString($this->email, "text"),
			GetSQLValueString($this->password, "text"),				 
            GetSQLValueString($this->cp, "text"),
            GetSQLValueString($this->adresse, "text"),
			GetSQLValueString($this->adresse2, "text"),				 
            GetSQLValueString($this->ville, "text"),
			GetSQLValueString($this->region, "text"),
			GetSQLValueString($this->categorie, "int"),
			GetSQLValueString($this->lien, "text"),
            GetSQLValueString($this->pays, "text"),
			GetSQLValueString($this->photo, "text"),
			GetSQLValueString($this->doc, "text"),
            GetSQLValueString($this->user_add, "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_agence = %s, active = %s, valide = %s, source = %s, titre = %s, prenom = %s, nom = %s, raison_social = %s, ice = %s, tel = %s, tel2 = %s, tel3 = %s, email = %s, password = %s, cp = %s, adresse = %s, adresse2 = %s, ville = %s, region = %s, categorie = %s, lien = %s, pays =%s, photo =%s, doc =%s, user_edit = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->valide, "int"),
            GetSQLValueString($this->source, "text"),
            GetSQLValueString($this->titre, "text"),
			GetSQLValueString($this->prenom, "text"),				 
            GetSQLValueString($this->nom, "text"),
			GetSQLValueString($this->raison_social, "text"),
			GetSQLValueString($this->ice, "text"),				 
			GetSQLValueString($this->tel, "text"),
			GetSQLValueString($this->tel2, "text"),
			GetSQLValueString($this->tel3, "text"),				 
            GetSQLValueString($this->email, "text"),
			GetSQLValueString($this->password, "text"),				 
            GetSQLValueString($this->cp, "text"),
            GetSQLValueString($this->adresse, "text"),
			GetSQLValueString($this->adresse2, "text"),	
            GetSQLValueString($this->ville, "text"),
			GetSQLValueString($this->region, "text"),
			GetSQLValueString($this->categorie, "int"),
			GetSQLValueString($this->lien, "text"),
            GetSQLValueString($this->pays, "text"),
			GetSQLValueString($this->photo, "text"),
			GetSQLValueString($this->doc, "text"),
            GetSQLValueString($this->user_edit, "int"),
            GetSQLValueString($this->last_edit, "date"),
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

		$SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id,$agence = 1)
    {
        global $db;
        $fournisseur = new fournisseur();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE A.id = %s",
            GetSQLValueString($id, "int")
        );
        //echo $SQLselect;
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $fournisseur = static::build($data);
        }
        return $fournisseur;
    }
	
	public static function findByEmail($email)
    {
        global $db;
        $fournisseur = null;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s",
            GetSQLValueString($email, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $fournisseur = static::build($data);
        }
        return $fournisseur;
    }

    public static function findAll($active = false, $agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE 1 = 1");
        if($active){
            $SQLselect .= " AND A.active = 1";
        }
        
        if(isset($_SESSION['user']) && !$_SESSION['user']->isSuperUser()){
            $SQLselect .= " AND A.user_add = " . $_SESSION['user']->getId();
        }
		
		$SQLselect .= " ORDER BY date_add DESC, id DESC";
		
		
        $result = $db->queryS($SQLselect);
        //echo $SQLselect; exit;
        foreach ($result as $data) {
            $fournisseur = static::build($data);
            array_push($items, $fournisseur);
        }
        
        return $items;
    }

    // Recherche globale (bandeau de recherche du bandeau haut) : nom, raison sociale ou email -
    // pas de filtre par agence, cohérent avec findAll() ci-dessus qui n'en applique pas non plus.
    public static function search($terme)
    {
        global $db;
        $items = array();
        $like = GetSQLValueString('%' . $terme . '%', 'text');
        $SQLselect = "SELECT id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE (nom LIKE $like OR prenom LIKE $like OR raison_social LIKE $like OR email LIKE $like) ORDER BY id DESC LIMIT 8";
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function findAllByCategory($active = false, $cat = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE A.categorie = %s",
            GetSQLValueString($cat, "int")
        );
        
        if($active){
            $SQLselect .= " AND A.active = 1";
        }
        
        if(isset($_SESSION['user']) && !$_SESSION['user']->isSuperUser()){
            $SQLselect .= " AND A.user_add = " . $_SESSION['user']->getId();
        }
		
		$SQLselect .= " ORDER BY date_add DESC, id DESC";
		//echo $SQLselect; exit;
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $fournisseur = static::build($data);
            array_push($items, $fournisseur);
        }
        
        return $items;
    }
	
	public static function filterFournisseur($year = false,$agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id";
		if($year){
			$SQLselect .= " INNER JOIN ". static::$tableFacture . " C ON A.id = C.id_client";
		}
        $SQLselect .= " WHERE A.id_agence = " . intval($agence);
        if($year){
            $SQLselect .= " AND YEAR(C.date_facture) = " . intval($year);
        }
		
		$SQLselect .= " GROUP BY A.id ORDER BY A.date_add DESC, id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $fournisseur = static::build($data);
            array_push($items, $fournisseur);
        }
        return $items;
    }

    public static function build($data){
        $fournisseur = new fournisseur();
        $fournisseur->setId($data['ID']);
        $fournisseur->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
        $fournisseur->setActive($data['active']);
        $fournisseur->setValide($data['valide']);
        $fournisseur->setSource($data['source']);
        $fournisseur->setTitre($data['titre']);
		$fournisseur->setPrenom($data['prenom']);
        $fournisseur->setNom($data['nom']);
		$fournisseur->setRaisonSocial($data['raison_social']);
		$fournisseur->setICE($data['ice']);
        $fournisseur->setTel($data['tel']);
		$fournisseur->setTel2($data['tel2']);
		$fournisseur->setTel3($data['tel3']);
        $fournisseur->setEmail($data['email']);
		$fournisseur->setPassword($data['password']);
        $fournisseur->setCp($data['cp']);
        $fournisseur->setAdresse($data['adresse']);
		$fournisseur->setAdresse2($data['adresse2']);
        $fournisseur->setVille($data['ville']);
		$fournisseur->setRegion($data['region']);
		$fournisseur->setCategorie($data['categorie']);
		$fournisseur->setLien($data['lien']);
        $fournisseur->setPays($data['pays']);
		$fournisseur->setPhoto($data['photo']);
		$fournisseur->setDoc($data['doc']);
		if($data['user_add']) $fournisseur->setUserAdd(user::find($data['user_add']));
        if($data['user_edit']) $fournisseur->setUserEdit(user::find($data['user_edit']));
        $fournisseur->setDateAdd($data['date_add']);
        $fournisseur->setLastEdit($data['last_edit']);
        return $fournisseur;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    public static function count($year = false){
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
		
		if($year){
			$SQLcount .= " WHERE YEAR(date_add) = " . intval($year);
		}
		
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    // Avatar "initiales colorées" pour un fournisseur sans photo (utilisé par le hero de la
    // page Fournisseurs) - même principe que bank::getLogoInfo(), mais sans liste d'enseignes
    // connues (un fournisseur est une entreprise quelconque, pas une des quelques grandes
    // banques marocaines) : couleur choisie dans une petite palette de façon déterministe à
    // partir du nom, pour varier visuellement les bulles sans dépendre du hasard.
    public static function getLogoInfo($nom)
    {
        $nom = trim((string) $nom);
        $palette = array('#6366f1', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#8b5cf6', '#ef4444', '#14b8a6');
        $indexCouleur = $nom !== '' ? (crc32($nom) % count($palette)) : 0;

        $mots = preg_split('/\s+/', $nom);
        $initiales = '';
        foreach (array_slice($mots, 0, 2) as $mot) {
            if ($mot !== '') {
                $initiales .= mb_strtoupper(mb_substr($mot, 0, 1, 'UTF-8'), 'UTF-8');
            }
        }
        return array('initials' => $initiales !== '' ? $initiales : '?', 'bg' => $palette[$indexCouleur]);
    }

}