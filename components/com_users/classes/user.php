<?php
class user {
	
    static $table =  __prefixe_db__ . "users";
	
    private $id;
    private $login;
    private $prenom;
    private $nom;
    private $email;
    private $tel;
    private $adresse;
	private $fonction;
	private $photo;
    private $profil;
    private $pass;
    private $actif;
    private $su;
    private $taux_commission = 0;
    private $langue;
	private $date_add;
	private $last_edit;
    private $connected = false;
    private $dev;

    /*public function __construct($login, $password, $db) {
        if (isset($login) && isset($password)){
            $login = addslashes($login);
            $password = hash('sha256', $password);
            $result = $db->query("SELECT * FROM ".__prefixe_db__."users WHERE login = '".$login."' AND password = '".$password."' AND actif = 1");
            if ($db->num_rows($result) == 1){

                $data = $db->fetch_assoc($result);
                $this->connected = true;
                $this->id = $data['id'];
                $this->username = $data['login'];
                $this->firstname = $data['prenom'];
                $this->lastname = $data['nom'];
                $this->email = $data['email'];
                $this->tel = $data['tel'];
                $this->adresse = $data['adresse'];
				//$this->fonction = $data['fonction'];
				//$this->photo = $data['photo'];
                $this->id_profil = $data['id_profil'];
                $this->actif = $data['actif'];
                $this->pass = $password;
                $this->su = $data['su'];
                $this->langue = $data['langue'];
                $this->dev = 0;

                $_SESSION['user'] = $this;
            }
        }
    }*/

    public function __construct()
    {
        $this->id = 0;
    }
    public function __destruct(){

    }

    public function getId(){
        return $this->id;
    }

    public function getPassword(){
        return $this->pass;
    }

    public function getAdresse(){
        return $this->adresse;
    }

    public function getName(){
        return ucfirst($this->prenom)." ".strtoupper($this->nom);
    }

    public function getPrenom(){
        return $this->prenom;
    }

    public function getNom(){
        return $this->nom;
    }

    public function getLogin(){
        return $this->login;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getTel(){
        return $this->tel;
    }
	
	public function getFonction(){
        return $this->fonction;
    }
	
	public function getProfil(){
        return $this->profil;
    }
	
	public function getPhoto(){
        return $this->photo;
    }

    public function getLangue(){
        return $this->langue;
    }

    public function getDateAdd(){
        return $this->date_add;
    }
	
	public function getLastEdit(){
        return $this->last_edit;
    }

	public function getTauxCommission(){
        return $this->taux_commission;
    }

    public function isConnected(){
        return $this->connected;
    }

    public function isResourceHumaine(){
        return false;
    }

	
	// Setter
	public function setId($id){
        $this->id = $id;
    }
	
	public function setNom($nom){
        $this->nom = $nom;
    }
	
	public function setPrenom($prenom){
        $this->prenom = $prenom;
    }
	
	public function setTel($tel){
        $this->tel = $tel;
    }
	
	public function setEmail($email){
        $this->email = $email;
    }
	
	public function setAdresse($adresse){
        $this->adresse = $adresse;
    }
	
	public function setLangue($langue){
        $this->langue = $langue;
    }
	
	public function setPhoto($photo){
        $this->photo = $photo;
    }
	
	public function setProfil($profil){
        $this->profil = $profil;
    }
	
	public function setLogin($login){
        $this->login = $login;
    }
	
	public function setPassword($password){
        $this->pass = $password;
    }
	
	public function setSU($su){
        $this->su = $su;
    }
	
	public function setActif($actif){
        $this->actif = $actif;
    }
	
	public function setConnected($connected){
        $this->connected = $connected;
    }
	
	public function setDateAdd($date_add){
        $this->date_add = $date_add;
    }
	
	public function setLastEdit($last_edit){
        $this->last_edit = $last_edit;
    }

	public function setTauxCommission($taux_commission){
        $this->taux_commission = $taux_commission;
    }
	
	
	
    public function disconnect(){
        $this->connected = false;
        $_SESSION['user'] = array();
        session_unset();
    }

    public function isActif(){
        return ($this->actif == 1) ? true : false;
    }

    public function isSuperUser(){
        return ($this->su == 1) ? true : false;
    }
	
	// action base de données
	public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (login, password, prenom, nom, email, tel, adresse, langue, photo, su, id_profil, actif, taux_commission, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->login, "text"),
            GetSQLValueString($this->pass, "text"),
            GetSQLValueString($this->prenom, "text"),
			GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->tel, "text"),
			GetSQLValueString($this->adresse, "text"),
            GetSQLValueString($this->langue, "text"),
			GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->su, "int"),
			GetSQLValueString($this->profil->getId(), "int"),
            GetSQLValueString($this->actif, "int"),
            GetSQLValueString($this->taux_commission, "double"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  login = %s, password = %s, prenom = %s, nom = %s, email = %s, tel = %s, adresse = %s, langue = %s, photo = %s, su = %s, id_profil = %s, actif = %s, taux_commission = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->login, "text"),
            GetSQLValueString($this->pass, "text"),
			GetSQLValueString($this->prenom, "text"),
			GetSQLValueString($this->nom, "text"),
			GetSQLValueString($this->email, "text"),
			GetSQLValueString($this->tel, "text"),
			GetSQLValueString($this->adresse, "text"),
			GetSQLValueString($this->langue, "text"),
			GetSQLValueString($this->photo, "text"),
			GetSQLValueString($this->su, "int"),
			GetSQLValueString($this->profil->getId(), "int"),
			GetSQLValueString($this->actif, "text"),
            GetSQLValueString($this->taux_commission, "double"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
			return 1;
        } else {
            return 0;
        }
    }
	
    public function editPass()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET password = %s WHERE id = %s",
			GetSQLValueString($this->pass, "text"),
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
            GetSQLValueString($this->id, "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }
	
    public function hasDroit($action, $module){
        global $db;
		//return true;
        if($this->getProfil()->hasDroit($action, $module)){
            return true;
        }else{
            return false;
        }
    }

    public static function isEmailValable($email){
        global $db;
        $result = $db->query("SELECT * FROM ".__prefixe_db__."users WHERE email = '".$email."' AND actif = 1 AND id_profil = 1");
        if ($db->num_rows($result) == 1){
            return true;
        }else{
            return false;
        }
    }

    public static function findAll($active = false, $ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT * FROM ".__prefixe_db__."users";
        if($active){
            $SQLselect .= " WHERE actif = 1";
        }
        if($ordre){
            $SQLselect .= " ORDER BY id DESC";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $user = static::build($data);
            array_push($items, $user);
        }
        return $items;
    }
	
	public static function find($id)
    {
        global $db;
        $user = new user();
        $SQLselect = sprintf("SELECT * FROM ".__prefixe_db__."users WHERE id = %s",
            GetSQLValueString($id, "int"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $user = static::build($data);
        }
        return $user;
    }
	
	public static function login($login, $password)
    {
        global $db;
        $user = new user();
        // Le mot de passe n'est plus dans la clause WHERE (impossible de toute façon dès qu'on
        // accepte password_hash() : chaque hash a son propre sel, donc pas de comparaison SQL
        // directe possible) - on va chercher l'utilisateur par login seul, puis on vérifie le mot
        // de passe côté PHP.
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE login = %s AND actif = 1",
            GetSQLValueString($login, "text"));

        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $storedHash = $data['password'];
            $valid = false;

            if (password_verify($password, $storedHash)) {
                $valid = true;
            } elseif (hash_equals($storedHash, hash('sha256', $password))) {
                // Ancien format (sha256 non salé, sans password_hash()) : encore accepté une
                // dernière fois puis remplacé immédiatement par un vrai hash bcrypt/argon2 - migration
                // silencieuse à la connexion, aucune action requise côté utilisateur ni reset forcé.
                $valid = true;
                $nouveauHash = password_hash($password, PASSWORD_DEFAULT);
                $db->query(sprintf(
                    "UPDATE " . static::$table . " SET password = %s WHERE id = %s",
                    GetSQLValueString($nouveauHash, "text"),
                    intval($data['id'])
                ));
            }

            if ($valid) {
                $user = static::build($data);
                $user->setConnected(true);
            }
        }
        return $user;
    }
    
    	public static function login_global($login)
    {
        global $db;
        $user = new user();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE login = %s AND actif = 1",
            GetSQLValueString($login, "text"));
		
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $user = static::build($data);
			$user->setConnected(true);
        }
        return $user;
    }
    
    public static function loginEspace($login)
    {
        global $db;
        $user = new user();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE login = %s AND actif = 1",
            GetSQLValueString($login, "text"));
		
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $user = static::build($data);
			$user->setConnected(true);
        }
        return $user;
    }

	public function isDev(){
        return ($this->dev == 1) ? true : false ;
    }

    public function setDev($dev) {
        $this->dev = $dev;
    }

    public static function build($data){
		global $db;
        $user = new user();
        $user->setId($data['id']);
        $user->setLogin($data['login']);
        $user->setPassword($data['password']);
        $user->setPrenom($data['prenom']);
        $user->setNom($data['nom']);
        $user->setEmail($data['email']);
        $user->setTel($data['tel']);
		$user->setAdresse($data['adresse']);
		$user->setLangue($data['langue']);
		$user->setPhoto($data['photo']);
		$user->setSU($data['su']);
		$user->setProfil(profil::find($data['id_profil']));
		$user->setActif($data['actif']);
		$user->setTauxCommission(isset($data['taux_commission']) ? $data['taux_commission'] : 0);
		$user->setDateAdd($data['date_add']);
		$user->setLastEdit($data['last_edit']);
        return $user;
    }
}
?>