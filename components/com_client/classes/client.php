<?php

class client
{
    static $table =  __prefixe_db__ . "client";
    static $tableAgence =  __prefixe_db__ . "agence";
	static $tableFacture =  __prefixe_db__ . "facture";
    static $tableDevis =  __prefixe_db__ . "devis";

    private $id;
    private $agence;
    private $source;
    private $user_added;
    private $user_edited;
    private $active;
    private $titre;
	private $prenom;
    private $nom;
    private $raison_social;
    private $fonction;
	private $ice;
    private $rc;
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
    private $recovery_code;
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
    
    public function getUserAdded()
    {
        return $this->user_added;
    }
    
    public function getUserEdited()
    {
        return $this->user_edited;
    }

    public function isActive()
    {
        return $this->active ? 1 : 0;
    }

    public function getActive()
    {
        return $this->active;
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

    public function getFonction()
    {
        return $this->fonction;
    }
	
	public function getICE()
    {
        return $this->ice;
    }

    public function getRc()
    {
        return $this->rc;
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

    public function getPays()
    {
        return $this->pays;
    }
	
	public function getPhoto()
    {
        return $this->photo;
    }

    public function getRecoveryCode()
    {
        return $this->recovery_code;
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
    
    public function setUserAdded($user_added)
    {
        $this->user_added = $user_added;
    }
    
    public function setUserEdited($user_edited)
    {
        $this->user_edited = $user_edited;
    }

    public function setActive($active)
    {
        $this->active = $active;
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

    public function setFonction($fonction)
    {
        $this->fonction = $fonction;
    }
	
	public function setICE($ice)
    {
        $this->ice = $ice;
    }

    public function setRc($rc)
    {
        $this->rc = $rc;
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

    public function setPays($pays)
    {
        $this->pays = $pays;
    }
	
	public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setRecoveryCode($recovery_code)
    {
        $this->recovery_code = $recovery_code;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence,id_user_added, active, source, titre, prenom, nom, raison_social, fonction, ice, rc, tel, tel2, tel3, email, password, cp, adresse, adresse2, ville, region, pays, photo, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->user_added->getId(), "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->source, "text"),
            GetSQLValueString($this->titre, "text"),
			GetSQLValueString($this->prenom, "text"),
            GetSQLValueString($this->nom, "text"),
			GetSQLValueString($this->raison_social, "text"),
            GetSQLValueString($this->fonction, "text"),
			GetSQLValueString($this->ice, "text"),	
            GetSQLValueString($this->rc, "text"),				 
            GetSQLValueString($this->tel, "text"),
			GetSQLValueString($this->tel2, "text"),
			GetSQLValueString($this->tel3, "text"),				 
            GetSQLValueString($this->email, "text"),
			GetSQLValueString(md5($this->password), "text"),				 
            GetSQLValueString($this->cp, "text"),
            GetSQLValueString($this->adresse, "text"),
			GetSQLValueString($this->adresse2, "text"),				 
            GetSQLValueString($this->ville, "text"),
			GetSQLValueString($this->region, "text"),				 
            GetSQLValueString($this->pays, "text"),
			GetSQLValueString($this->photo, "text"),				 
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  id_agence = %s, id_user_edited = %s,  active = %s, source = %s, titre = %s, prenom = %s, nom = %s, raison_social = %s, fonction = %s, ice = %s, rc = %s, tel = %s, tel2 = %s, tel3 = %s, email = %s, password = %s, cp = %s, adresse = %s, adresse2 = %s, ville = %s, region = %s, pays =%s, photo =%s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->user_edited->getId(), "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->source, "text"),
            GetSQLValueString($this->titre, "text"),
			GetSQLValueString($this->prenom, "text"),				 
            GetSQLValueString($this->nom, "text"),
			GetSQLValueString($this->raison_social, "text"),
            GetSQLValueString($this->fonction, "text"),
			GetSQLValueString($this->ice, "text"),				 
            GetSQLValueString($this->rc, "text"),	
			GetSQLValueString($this->tel, "text"),
			GetSQLValueString($this->tel2, "text"),
			GetSQLValueString($this->tel3, "text"),				 
            GetSQLValueString($this->email, "text"),
			GetSQLValueString(md5($this->password), "text"),				 
            GetSQLValueString($this->cp, "text"),
            GetSQLValueString($this->adresse, "text"),
			GetSQLValueString($this->adresse2, "text"),	
            GetSQLValueString($this->ville, "text"),
			GetSQLValueString($this->region, "text"),				 
            GetSQLValueString($this->pays, "text"),
			GetSQLValueString($this->photo, "text"),				 
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
	
	public static function doLogin($email,$password){
		global $db;
        $client = new client();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s AND password = %s AND active = %s",
            GetSQLValueString($email, "text"),
			GetSQLValueString(md5($password), "text"),
			GetSQLValueString(1, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $client = static::build($data);
			return $client;
        }
		else
			return null;
	}

    public static function find($id,$agence = 1)
    {
        global $db;
        $client = new client();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id = %s and A.id_agence = %s",
            GetSQLValueString($id, "int"),
            GetSQLValueString($agence, "int")
        );
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $client = static::build($data);
        }
        return $client;
    }
    
    public static function findById($id)
    {
        global $db;
        $client = new client();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id = %s",
            GetSQLValueString($id, "int")
        );
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $client = static::build($data);
        }
        return $client;
    }
	
	public static function findByEmail($email)
    {
        global $db;
        $client = null;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s",
            GetSQLValueString($email, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $client = static::build($data);
        }
        return $client;
    }

    public static function findAll($active = false, $year = false,$agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s",
            GetSQLValueString($agence, "int")
        );
        if($active){
            $SQLselect .= " AND A.active = 1";
        }
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
		$SQLselect .= " ORDER BY A.date_add DESC, A.id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $client = static::build($data);
            array_push($items, $client);
        }
        return $items;
    }

    public function getFacture($active = false)
    {
        global $db;
        $factures = array();
        $SQLselect = sprintf("SELECT id FROM " . static::$tableFacture . "  WHERE id_client = %s",
            GetSQLValueString($this->id, "int")
        );
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (id_user_added = ".$_SESSION['user']->getId()." )";
        }
        if($active){
            $SQLselect .= " AND A.active = 1";
        }
		$SQLselect .= " ORDER BY date_facture DESC, id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $facture = facture::find($data['id']);
            array_push($factures, $facture);
        }
        return $factures;
    }

    public function getDevis($active = false)
    {
        global $db;
        $deviss = array();
        $SQLselect = sprintf("SELECT id FROM " . static::$tableDevis . "  WHERE id_client = %s",
            GetSQLValueString($this->id, "int")
        );
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (id_user_added = ".$_SESSION['user']->getId()." )";
        }
        if($active){
            $SQLselect .= " AND A.active = 1";
        }
		$SQLselect .= " ORDER BY date_devis DESC, id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $devis = devis::find($data['id']);
            array_push($deviss, $devis);
        }
        return $deviss;
    }
    
    public static function findWhereHaveRelance($active = false, $year = false,$agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT " . static::$table . ".id as ID ," . static::$table . ".* FROM " . static::$table . " where EXISTS (select * from ".__prefixe_db__ ."relance where id_client = " . static::$table . ".id) and id_agence = %s",
        GetSQLValueString($agence, "int")
    );
        if($active){
            $SQLselect .= " and active = 1";
        }
		
		$SQLselect .= " ORDER BY date_add DESC, id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $client = static::build($data);
            array_push($items, $client);
        }
        return $items;
    }
	
	public static function filterClient($year = false,$agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " C ON C.id =A.id_agence";
		if($year){
			$SQLselect .= " INNER JOIN ". static::$tableFacture . " B ON A.id = B.id_client";
		}
        $SQLselect .= " WHERE A.id_agence = $agence";
        if($year){
            $SQLselect .= " AND YEAR(A.date_add) = $year";
        }
		$SQLselect .= " GROUP BY A.id ORDER BY A.date_add DESC, A.id DESC";
		//echo $SQLselect;
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $client = static::build($data);
            array_push($items, $client);
        }
        return $items;
    }

    public static function build($data){
        $client = new client();
        $client->setId($data['ID']);
        $client->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
        $client->setUserAdded(user::find($data['id_user_added']));
        $client->setUserEdited(user::find($data['id_user_edited']));
        $client->setActive($data['active']);
        $client->setSource($data['source']);
        $client->setTitre($data['titre']);
		$client->setPrenom($data['prenom']);
        $client->setNom($data['nom']);
		$client->setRaisonSocial($data['raison_social']);
        $client->setFonction($data['fonction']);
		$client->setICE($data['ice']);
        $client->setRc($data['rc']);
        $client->setTel($data['tel']);
		$client->setTel2($data['tel2']);
		$client->setTel3($data['tel3']);
        $client->setEmail($data['email']);
		$client->setPassword($data['password']);
        $client->setCp($data['cp']);
        $client->setAdresse($data['adresse']);
		$client->setAdresse2($data['adresse2']);
        $client->setVille($data['ville']);
		$client->setRegion($data['region']);
        $client->setPays($data['pays']);
		$client->setPhoto($data['photo']);
        $client->setDateAdd($data['date_add']);
        $client->setLastEdit($data['last_edit']);
        return $client;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    public static function count($year = false,$agence = 1){
        global $db;
        $SQLcount = "SELECT count(A.id) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id where B.id = $agence";
		if($_SESSION['user']->isSuperUser() == false){
            $SQLcount .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
		if($year){
			$SQLcount .= " AND YEAR(A.date_add) = $year";
		}
		
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    // API

    public static function buildApi($data){
        $client = array(
            'id' => $data['ID'],
            'agence' => agence::ApiFindById($data['id_agence']),
            'active' => $data['active'],
            'titre' => $data['titre'],
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'raison_social' => $data['raison_social'],
            'fonction' => $data['fonction'],
            'ice' => $data['ice'],
            'rc' => $data['rc'],
            'tel' => $data['tel'],
            'tel2' => $data['tel2'],
            'tel3' => $data['tel3'],
            'email' => $data['email'],
            'password' => $data['password'],
            'cp' => $data['cp'],
            'adresse' => $data['adresse'],
            'adresse2' => $data['adresse2'],
            'ville' => $data['ville'],
            'region' => $data['region'],
            'pays' => $data['pays'],
            'photo' => $data['photo'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
        );
        return $client;
    }

    public static function ApiFindById($id)
    {
        if(getToken()){
            global $db;
            $client = new client();
            $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id = %s",
                GetSQLValueString($id, "int"),
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $client = static::buildApi($data);
            }
            return $client;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function loginApi($email,$password){
		global $db;
        $client = new client();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s AND password = %s AND active = %s",
            GetSQLValueString($email, "text"),
			GetSQLValueString(md5($password), "text"),
			GetSQLValueString(1, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) > 0) {
            $data = $db->fetch_assoc($result);
            $data['ID'] = $data['id'];
            $client = static::buildApi($data);
            $token = setToken($client);
			return json_encode(array("icon"=>"success","message"=>"Successful connection","client"=>$client,"token"=>$token));
        }
		return json_encode(array("icon"=>"warning","message"=>"Email or password incorrect","client"=>$db->num_rows($result)));
	}

    public static function verifyEmailApi($email){
        require '../../../vendor/autoload.php';
	    require '../../../includes/traduction.php';
    
        global $db, $siteURL,$hwaURL;

        global $db;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s AND active = %s",
            GetSQLValueString($email, "text"),
			GetSQLValueString(1, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) > 0) {
            $data = $db->fetch_assoc($result);
            $token = md5(rand(0000,1111));
            $SQLupdate = sprintf("UPDATE " . static::$table . " SET  recovery_code = %s WHERE id = %s",				 
                GetSQLValueString($token, "text"),
                GetSQLValueString($data['id'], "int")
            );
            if (!$db->query($SQLupdate)) {
                $config = new config($db);
                $mail = new PHPMailer();
                $mailBody = '<html><body>
                        		<table border="0" width="100%">
                        		<tr>
                        		<td bgcolor="#F6F6F6" align="center" style="padding-top:20px">
                        
                        		<table border="0" cellpadding="15" cellspacing="0" style="100%">
                        			<tr>
                        				<td align="center"><img src="'.$siteURL.'images/logo_hello_world.png" alt="Hello World Agency" height="64" /></td>
                        			</tr>
                        			<tr bgcolor="#FFFFFF">
                        				<td align="center" style="padding-bottom:0px"><h1 style="font-weight:normal; margin-bottom:0px;">Password Recovery</h1></td>
                        			</tr>
                        			<tr bgcolor="#FFFFFF">
                        				<td style="padding-bottom:0px">
                        				<table border="0" cellpadding="5">
                        				<tr style="text-align:center">
                        					<td>
                        					    <p style="color: grey;text-align: center;margin-top: 0;">If you have lost your password or wish to rest it, use the link below to get started</p>
                        					</td>
                        				</tr>
                        				<tr style="text-align:center">
                        				    <td style="padding-bottom: 30px;">
                        				        <a style="padding: 10px 20px;background: #0ac3e0;color: white;font-weight: bold;margin: auto;display: block;width: fit-content;text-decoration:none" href="'.$hwaURL.'create-a-new-password/'.$email.'/'.$token.'/">Reset your password</a>
                        				    </td>
                        				</tr>
                        				</table>
                        				</td>
                        			</tr>
                        			<tr>
                        				<td align="center"><p><font size="-3" color="#666666">Hello World Agenct Contact<br/>
                        		Email: contact@helloworld-agency.com<br/>
                        		Phone. : +212 5 24 42 31 56 / +212 6 75 47 20 01</font></p></td>
                        			</tr>
                        		</table>
                        		</td>
                        		</tr>
                        		</table>
                        		</body>
                        		</html>';

                //Set who the message is to be sent from
                $mail->setFrom("contact@helloworld-agency.com");
                //Set an alternative reply-to address
                $mail->addReplyTo($config->getEmail(), $config->getNom());
                //Set who the message is to be sent to
                $mail->addAddress($data['email'], $data['nom'].' '.$data['prenom']);
                $mail->addAddress("contact@helloworld-agency.com");
                //Set the subject line
                $mail->Subject = 'Recover Password '.$config->getNom();
                //Read an HTML message body from an external file, convert referenced images to embedded,
                //convert HTML into a basic plain-text alternative body
                $mail->msgHTML($mailBody);
                //Attach an image file
                                
                if($mail->send()) {
                    return json_encode(array("icon"=>"success","message"=>"The password recovery link has been successfully sent to your email. Please check your inbox","link"=>'<a href="'.$hwaURL.'create-a-new-password/'.$email.'/'.$token.'/">Click here</a> to reset your password'));
                } 
                else {
                    return json_encode(array("icon"=>"error","message"=> $mail->ErrorInfo));
                } 
            } else {
                return json_encode(array("icon"=>"warning","message"=>"The password recovery link has not been sent to your email"));
            }
            
        }
		return json_encode(array("icon"=>"warning","message"=>"This account is not exists"));
        

		
	}

    public static function setNewPasswordApi($data){
        global $db;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s and recovery_code = %s",
            GetSQLValueString($data['email'], "text"),
            GetSQLValueString($data['token'], "text"),
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) > 0) {
            $resultData = $db->fetch_assoc($result);
            $SQLupdate = sprintf("UPDATE " . static::$table . " SET  password = %s, recovery_code = %s WHERE id = %s",				 
                GetSQLValueString(md5($data['password']), "text"),
                GetSQLValueString(null, "text"),
                GetSQLValueString($resultData['id'], "int")
            );
            if (!$db->query($SQLupdate)) {
                return json_encode(array("icon"=>"success","message"=>"Your password has successfully recovered"));
            } else {
                return json_encode(array("icon"=>"warning","message"=>"Your password has not recovered, try again"));
            }
            
        }
		return json_encode(array("icon"=>"warning","message"=>"The attempt failed, try again"));
	}

    public static function getInfoFromTokenApi($token){
		$info = getInfoFromToken($token);
		return json_encode(array("icon"=>"success","message"=>"Info token","info"=>$info ));
	}


    public static function findClientByIdApi($id)
    {
        if(getToken()){
            global $db;
            $client = new client();
            $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A  where A.id = %s",
                GetSQLValueString($id, "int"),
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) > 0) {
                $data = $db->fetch_assoc($result);
                $client = static::buildApi($data);
            }
            return json_encode($client);
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function updateProfileApi($data)
    {
        if(getToken()){
            global $db;
            $SQLinsert = sprintf(
                "UPDATE " . static::$table . " SET  password = %s, last_edit = %s WHERE id = %s",
                GetSQLValueString(md5($data['password']), "text"),
                GetSQLValueString(date("Y-m-d"), "date"),
                GetSQLValueString($data['id_client'], "int")
            );
            if (!$db->query($SQLinsert)) {
                return json_encode(array("icon"=>"success","message"=>"The profile has been successfully updated"));
            } else {
                return json_encode(array("icon"=>"warning","message"=>"The profile has not been updated"));
            }
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }
	
}