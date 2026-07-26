<?php
class compte {
    private $id;
    private $username;
    private $password;
    private $firstname;
    private $lastname;
    private $email;
    private $tel;
    private $adresse;
	private $fonction;
	private $photo;
    private $id_profil;
    private $actif;
    private $su;
    private $langue;
    private $connected = false;
    private $dev;

    public function __construct($id, $db) {
        $result = $db->query("SELECT * FROM ".__prefixe_db__."users WHERE id = ".intval($id));
        if ($db->num_rows($result) == 1){

            $data = $db->fetch_assoc($result);
            $this->connected = true;
            $this->id = $data['id'];
            $this->username = $data['login'];
            $this->password = $data['password'];
            $this->firstname = $data['prenom'];
            $this->lastname = $data['nom'];
            $this->email = $data['email'];
            $this->tel = $data['tel'];
            $this->adresse = $data['adresse'];
			$this->fonction = $data['fonction'];
			$this->photo = $data['photo'];
            $this->id_profil = $data['id_profil'];
            $this->actif = $data['actif'];
            $this->su = $data['su'];
            $this->langue = $data['langue'];
            $this->dev = 0;
        }
        else
            $this->id = 0;
    }

    public function __destruct(){

    }

    public function getId(){
        return $this->id;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getAdresse(){
        return $this->adresse;
    }

    public function getName(){
        return ucfirst($this->firstname)." ".strtoupper($this->lastname);
    }

    public function getFirstName(){
        return $this->firstname;
    }

    public function getLastName(){
        return $this->lastname;
    }

    public function getUserName(){
        return $this->username;
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
	
	public function getPhoto(){
        return $this->photo;
    }

    public function getLangue(){
        return $this->langue;
    }

    public function getIdProfil(){
        return $this->id_profil;
    }

    public function getDetailsAssoc(){
        $details = array(	"id" => $this->id,
            "username" => $this->username,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "tel" => $this->tel,
            "adresse" => $this->adresse,
            "level" => $this->level,
            "logo" => $this->logo
        );

        return $details;
    }

    public function isConnected(){
        return $this->connected;
    }

    public function disconnect(){
        $this->connected = false;
        $_SESSION['user'] = array();
        session_unset();
    }

    public function isActif(){
        return ($this->actif == 1) ? true : false ;
    }

    public function isSuperUser(){
        return ($this->su == 1) ? true : false;
    }

    public function hasDroit($action, $module){
        global $db;
        $p = new profil($this->id_profil,$db);
        return $p->hasDroit($action, $module);
    }

    public function isDev(){
        return ($this->dev == 1) ? true : false ;
    }

    public function setDev($dev) {
        $this->dev = $dev;
    }

}
?>