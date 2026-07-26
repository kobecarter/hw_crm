<?php

class resourcehumaine
{
    static $table =  __prefixe_db__ . "resourcehumaine";
    static $table2 =  __prefixe_db__ . "workdate";

    private $id;
    private $reference;
    private $reference_pointage;
    private $agency;
    private $cin;
    private $cnss_number;
	private $firstname;
    private $lastname;
	private $email;
    private $password;
	private $phone;
	private $address;
    private $city;
    private $prospecting_source;
    private $rib;
    private $function;
    private $profil;
    private $status;
    private $start_date;
    private $contract_signing_date;
    private $end_date;
	private $photo;
	private $second_phone;
    private $remark;
    private $active;
    private $date_add;
    private $last_edit;
    private $connected = false;
    

    public function __construct()
    {
        $this->id = 0;
    }
function calculerPeriodeAgent($start_date, $end_date = null) {
    $dateDebut = new DateTime($start_date);

    if ($end_date) {
        // Si l'agent a une date de fin, on calcule la période qu'il VA passer avec nous
        $dateFin = new DateTime($end_date);
    } else {
        // Sinon, on calcule la période déjà passée jusqu'à aujourd’hui
        $dateFin = new DateTime(); // aujourd'hui
    }

    $interval = $dateDebut->diff($dateFin);

    // Retourne une chaîne lisible : X années, Y mois, Z jours
    return $interval->format('%y années, %m mois, %d jours');
}

function calculerPeriodeStagiaire($start_date, $end_date = null) {
    $dateDebut = new DateTime($start_date);

    if ($end_date) {
        // Si une date de fin est fournie, on calcule la période prévue
        $dateFin = new DateTime($end_date);
    } else {
        // Sinon, on calcule la période écoulée jusqu'à aujourd'hui
        $dateFin = new DateTime(); // aujourd'hui
    }

    $interval = $dateDebut->diff($dateFin);

    if ($interval->m >= 1 || $interval->y >= 1) {
        // Si la période est d'au moins un mois, afficher en mois et jours
        $moisTotal = $interval->y * 12 + $interval->m;
        return $moisTotal . ' mois, ' . $interval->d . ' jours';
    } else {
        // Sinon, afficher uniquement le nombre total de jours
        return $interval->days . ' jours';
    }
}




    public function getId()
    {
        return $this->id;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getReferencePointage()
    {
        return $this->reference_pointage;
    }

    public function getAgency()
    {
        return $this->agency;
    }

    public function getCin()
    {
        return $this->cin;
    }
    
    public function getCnssNumber()
    {
        return $this->cnss_number;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }
	
	public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPhone()
    {
        return $this->phone;
    }
    
     public function getSecondPhone()
    {
        return $this->second_phone;
    }
	

	public function getAddress()
    {
        return $this->address;
    }
	
	public function getCity()
    {
        return $this->city;
    }
    
    public function getProspectingSource()
    {
        return $this->prospecting_source;
    }
    
    public function getRib()
    {
        return $this->rib;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function getProfil(){
        return $this->profil;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }
    
    public function getContractSigningDate()
    {
        return $this->contract_signing_date;
    }

    public function getEndDate()
    {
        return $this->end_date;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getRemark()
    {
        return $this->remark;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function getFullName()
    {
        return $this->firstname." ".$this->lastname;
    }

    public function isConnected(){
        return $this->connected;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAgency($agency)
    {
        $this->agency = $agency;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setReferencePointage($reference_pointage)
    {
        $this->reference_pointage = $reference_pointage;
    }

    public function setCin($cin)
    {
        $this->cin = $cin;
    }
    
    public function setCnssNumber($cnss_number)
    {
        $this->cnss_number = $cnss_number;
    }

    public function setFirstName($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastName($lastname)
    {
        $this->lastname = $lastname;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
    
    public function setSecondPhone($second_phone)
    {
        $this->second_phone = $second_phone;
    }
	
	public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }
    
    public function setProspectingSource($prospecting_source)
    {
        $this->prospecting_source = $prospecting_source;
    }
    
    public function setRib($rib)
    {
        $this->rib = $rib;
    }

    public function setFunction($function)
    {
        $this->function = $function;
    }

    public function setProfil($profil){
        $this->profil = $profil;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }
    
    public function setContractSigningDate($contract_signing_date)
    {
        $this->contract_signing_date = $contract_signing_date;
    }

    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
    }
	

	public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function setConnected($connected){
        $this->connected = $connected;
    }

    public function disconnect(){
        $this->connected = false;
        $_SESSION['user'] = array();
        session_unset();
    }

    public function isActive(){
        return $this->active == 1;
    }

    public function isSuperUser(){
        return false;
    }

    public function getName(){
        return $this->getFullName();
    }

    public function isResourceHumaine(){
        return true;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (reference,reference_pointage,id_agency, cin, cnss_number, firstname, lastname, email, password, phone, second_phone, address, city, prospecting_source, rib, function, id_profil, status, start_date, contract_signing_date, end_date, photo, remark, active, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->reference, "text"),
            GetSQLValueString($this->reference_pointage, "text"),
            GetSQLValueString($this->agency->getId(), "int"),
            GetSQLValueString($this->cin, "text"),
             GetSQLValueString($this->cnss_number, "text"),
            GetSQLValueString($this->firstname, "text"),
			GetSQLValueString($this->lastname, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->password, "text"),
			GetSQLValueString($this->phone, "text"),
			GetSQLValueString($this->second_phone, "text"),
			GetSQLValueString($this->address, "text"),				 
            GetSQLValueString($this->prospecting_source, "text"),
            GetSQLValueString($this->city, "text"),
            GetSQLValueString($this->rib, "text"),
            GetSQLValueString($this->function, "text"),
            GetSQLValueString($this->profil->getId(), "int"),
            GetSQLValueString($this->status, "text"),
            GetSQLValueString($this->start_date, "date"),
             GetSQLValueString($this->contract_signing_date, "date"),
            GetSQLValueString($this->end_date, "date"),
			GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->remark, "text"),
            GetSQLValueString($this->active, "int"),				 
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET reference = %s, reference_pointage = %s, id_agency = %s, cin = %s, cnss_number = %s, firstname = %s, lastname = %s, email = %s, password = %s, phone = %s, second_phone = %s, address = %s, city = %s, prospecting_source = %s, rib = %s, function = %s, id_profil= %s, status = %s, start_date = %s, contract_signing_date = %s, end_date = %s, photo = %s, remark = %s, active = %s, date_add = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->reference, "text"),
            GetSQLValueString($this->reference_pointage, "text"),
            GetSQLValueString($this->agency->getId(), "int"),
            GetSQLValueString($this->cin, "text"),
            GetSQLValueString($this->cnss_number, "text"),
            GetSQLValueString($this->firstname, "text"),
            GetSQLValueString($this->lastname, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->password, "text"),
            GetSQLValueString($this->phone, "text"),
            GetSQLValueString($this->second_phone, "text"),
            GetSQLValueString($this->address, "text"),				 
            GetSQLValueString($this->city, "text"),
            GetSQLValueString($this->prospecting_source, "text"),
            GetSQLValueString($this->rib, "text"),
            GetSQLValueString($this->function, "text"),
            GetSQLValueString($this->profil->getId(), "int"),
            GetSQLValueString($this->status, "text"),
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->contract_signing_date, "date"),
            GetSQLValueString($this->end_date, "date"),
            GetSQLValueString($this->photo, "text"),	
            GetSQLValueString($this->remark, "text"),		
            GetSQLValueString($this->active, "int"),				 
            GetSQLValueString($this->date_add, "date"),
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

    public static function find($id)
    {
        global $db;
        $resourcehumaine = new resourcehumaine();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $resourcehumaine = static::build($data);
        }
        return $resourcehumaine;
    }

    public static function findByMatricule($matricule)
    {
        global $db;
        $resourcehumaine = new resourcehumaine();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE reference = %s",
            GetSQLValueString($matricule, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $resourcehumaine = static::build($data);
        }
        return $resourcehumaine;
    }

    public static function findByReferencePointage($reference_pointage)
    {
        global $db;
        $resourcehumaine = new resourcehumaine();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE reference_pointage = %s",
            GetSQLValueString($reference_pointage, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $resourcehumaine = static::build($data);
        }
        return $resourcehumaine;
    }

    public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT * FROM " . static::$table." ORDER BY id DESC";
		
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $resourcehumaine = static::build($data);
            array_push($items, $resourcehumaine);
        }
        return $items;
    }

    public static function findAllByStatus($status)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where status = %s ORDER BY id DESC",
            GetSQLValueString($status, "text")
        );
		
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $resourcehumaine = static::build($data);
            array_push($items, $resourcehumaine);
        }
        return $items;
    }

    public static function findAllByStatuses($statuses)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where status in ('".implode("','",$statuses)."') ORDER BY id DESC");
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $resourcehumaine = static::build($data);
            array_push($items, $resourcehumaine);
        }
        return $items;
    }


    public static function build($data){
        $resourcehumaine = new resourcehumaine();
        $resourcehumaine->setId($data['id']);
        $resourcehumaine->setReferencePointage($data['reference_pointage']);
        $resourcehumaine->setReference($data['reference']);
        $resourcehumaine->setAgency(agence::find($data['id_agency'],$_SESSION['langue']));
        $resourcehumaine->setCin($data['cin']);
        $resourcehumaine->setCnssNumber($data['cnss_number']);
        $resourcehumaine->setFirstName($data['firstname']);
		$resourcehumaine->setLastName($data['lastname']);
        $resourcehumaine->setEmail($data['email']);
        $resourcehumaine->setPassword($data['password']);
        $resourcehumaine->setPhone($data['phone']);
        $resourcehumaine->setSecondPhone($data['second_phone']);
		$resourcehumaine->setAddress($data['address']);
		$resourcehumaine->setCity($data['city']);
		$resourcehumaine->setProspectingSource($data['prospecting_source']);
		$resourcehumaine->setRib($data['rib']);
        $resourcehumaine->setFunction($data['function']);
        $resourcehumaine->setProfil(isset($data['id_profil']) ? profil::find($data['id_profil']) : new profil());
        $resourcehumaine->setStatus($data['status']);
		$resourcehumaine->setStartDate($data['start_date']);
		$resourcehumaine->setContractSigningDate($data['contract_signing_date']);
		$resourcehumaine->setEndDate($data['end_date']);
		$resourcehumaine->setPhoto($data['photo']);
        $resourcehumaine->setRemark($data['remark']);
        $resourcehumaine->setActive($data['active']);
        $resourcehumaine->setDateAdd($data['date_add']);
        $resourcehumaine->setLastEdit($data['last_edit']);
        return $resourcehumaine;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    public static function count($year = false){
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
		
		if($year){
			$SQLcount .= " WHERE YEAR(date_add) = $year";
		}
		
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function login($email, $password)
    {
        global $db;
        $resourcehumaine = new resourcehumaine();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s and password = %s",
            GetSQLValueString($email, "text"),
			GetSQLValueString(hash('sha256',$password), "text"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $resourcehumaine = static::build($data);
        }
        return $resourcehumaine;
    }

    public function hasDroit($action, $module){
        if($this->getProfil()->hasDroit($action, $module)){
            return true;
        }else{
            return false;
        }
    }

    public static function isEmailValable($email){
        global $db;
        $result = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s AND active = 1",
            GetSQLValueString($email, "text"),
        );
        if ($db->num_rows($result) == 1){
            return true;
        }else{
            return false;
        }
    }
    
     public function getWorkDates()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table2 . " WHERE id_resourcehumaine = %s ORDER BY id ASC",
            GetSQLValueString($this->id, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item = workdate::find($data['id']);
            array_push($items, $item);
        }
        return $items;
    }
	
}