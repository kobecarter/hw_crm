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
    private $site_web;
    private $ia_recap;
    private $ia_recap_date;
    private $user_added;
    private $user_edited;
    private $active;
    private $archived;
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

    public function getSiteWeb()
    {
        return $this->site_web;
    }

    public function getIaRecap()
    {
        return $this->ia_recap;
    }

    public function getIaRecapDate()
    {
        return $this->ia_recap_date;
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

    public function isArchived()
    {
        return $this->archived ? 1 : 0;
    }

    public function getArchived()
    {
        return $this->archived;
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

    public function toArray()
    {
        return array(
            "id" => $this->id,
            "titre" => $this->titre,
            "nom" => $this->nom,
            "prenom" => $this->prenom,
            "raison_social" => $this->raison_social,
            "ice" => $this->ice,
            "tel" => $this->tel,
            "email" => $this->email,
            "cp" => $this->cp,
            "adresse" => $this->adresse,
            "ville" => $this->ville,
            "region" => $this->region,
            "pays" => $this->pays,
            "photo" => $this->photo,
            "active" => (bool) $this->active,
        );
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

    public function setSiteWeb($site_web)
    {
        $this->site_web = $site_web;
    }

    public function setIaRecap($ia_recap)
    {
        $this->ia_recap = $ia_recap;
    }

    public function setIaRecapDate($ia_recap_date)
    {
        $this->ia_recap_date = $ia_recap_date;
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

    public function setArchived($archived)
    {
        $this->archived = $archived;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence,id_user_added, active, archived, source, site_web, titre, prenom, nom, raison_social, fonction, ice, rc, tel, tel2, tel3, email, password, cp, adresse, adresse2, ville, region, pays, photo, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->user_added->getId(), "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->archived, "int"),
            GetSQLValueString($this->source, "text"),
            GetSQLValueString($this->site_web, "text"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  id_agence = %s, id_user_edited = %s,  active = %s, archived = %s, source = %s, site_web = %s, titre = %s, prenom = %s, nom = %s, raison_social = %s, fonction = %s, ice = %s, rc = %s, tel = %s, tel2 = %s, tel3 = %s, email = %s, password = %s, cp = %s, adresse = %s, adresse2 = %s, ville = %s, region = %s, pays =%s, photo =%s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->user_edited->getId(), "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->archived, "int"),
            GetSQLValueString($this->source, "text"),
            GetSQLValueString($this->site_web, "text"),
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

    // Génère un nouveau mot de passe aléatoire pour l'espace client, l'enregistre en base et
    // l'envoie par email avec le lien du portail - même distinction Maroc/Dubaï (agence 2) que
    // devis::sendViaMailDevis(), via getMailCredentialsForAgence()/espaceClientLink().
    public function envoyerAccesEspaceClient()
    {
        if ($this->email == '') {
            return array("success" => false, "message" => "Ce client n'a pas d'adresse email");
        }

        $plainPassword = random(10);
        $this->setPassword($plainPassword);
        if ($this->edit() != 1) {
            return array("success" => false, "message" => "Erreur lors de la mise à jour du mot de passe");
        }

        global $siteURL;
        $agence = $this->getAgence();
        $mailCreds = getMailCredentialsForAgence($agence->getId());
        $lien = espaceClientLink($agence->getId());

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $mailCreds['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailCreds['username'];
        $mail->Password = $mailCreds['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $mailCreds['port'];

        $mailBody = '<html>
    <body>
    <table border="0" width="100%">
        <tr>
            <td bgcolor="#F6F6F6" align="center">
                <table border="0" cellpadding="15" cellspacing="0" width="640">
                    <tr>
                        <td align="center"><img src="' . $siteURL . 'images/agences/' . $agence->getLogo() . '" width="100"></td>
                    </tr>

                    <tr bgcolor="#FFFFFF">
                        <td align="center">
                        <h1 style="font-weight:normal; margin-bottom:15px;margin-top:20px">
                        Votre espace client
                        </h1>
                    </td>

                </tr>

                <tr bgcolor="#FFFFFF" style="font-size:18px;">
                    <td align="center">
                    Bonjour ' . $this->prenom . ',<br><br>Voici vos accès pour vous connecter à votre espace client :
                    </td>
                    </tr>

                    <tr bgcolor="#FFFFFF">
                        <td align="center" style="padding-top:15px;">
                            <p style="font-size:16px;">
                                <strong>Email :</strong> ' . $this->email . '<br>
                                <strong>Mot de passe :</strong> ' . $plainPassword . '
                            </p>
                            <a style="padding:10px 20px;background:' . $agence->getColor() . ';color:white;font-weight:bold;margin:15px auto;display:block;width:fit-content;text-decoration:none" href="' . $lien . '">Accéder à mon espace client</a>
                        </td>
                    </tr>

                    <tr>
                        <td align="center">
                            <p>
                                <font size="2" color="#666666"><br />
                                ' . $agence->getNom() . '
                                <br/>
                                Email : ' . $agence->getEmail() . '
                                <br>
                                Tél : ' . $agence->getTel() . ' / ' . $agence->getTel2() . '
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>

</html>';

        $mail->setFrom($mailCreds['username']);
        $mail->addReplyTo($agence->getEmail(), $agence->getNom());
        $mail->addAddress($this->email, trim($this->prenom . ' ' . $this->nom));

        $mail->Subject = 'Accès à votre espace client - ' . $agence->getNom();
        $mail->msgHTML($mailBody);

        if ($mail->send()) {
            copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage(), $mailCreds['host'], $mailCreds['username'], $mailCreds['password']);
            return array("success" => true, "message" => "Email envoyé avec succès");
        }
        return array("success" => false, "message" => "Erreur lors de l'envoi : " . $mail->ErrorInfo);
    }

	public static function doLogin($email,$password){
		global $db;
        $client = new client();
        $SQLselect = sprintf("SELECT " . static::$table . ".id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE email = %s AND password = %s AND active = %s",
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

    // Chargement sans filtre d'agence ni dépendance à $_SESSION['user']. Deux usages légitimes :
    // 1) accès temporaire "Réseaux sociaux" via jeton+code (components/com_client/classes/
    //    clientsocialtoken.php et clientsocial.php) - ce parcours n'a jamais de session CRM
    //    classique, donc find() ci-dessus ferait une erreur fatale (isSuperUser() sur null).
    // 2) résolution du client à l'intérieur de devis::build()/facture::build() : le devis/la
    //    facture est déjà chargé(e) par son propre id (leur find() ne filtre déjà plus par agence,
    //    voir le commentaire dans devis::find()), donc filtrer le client par $_SESSION['agence']
    //    à ce stade n'ajoute aucune sécurité réelle - ça ne fait que renvoyer un client vide (sans
    //    email/téléphone/nom) dès que l'agence sélectionnée dans l'UI diffère de celle du client,
    //    ce qui cassait silencieusement l'envoi d'email et l'affichage sur la fiche devis/facture.
    // Ne PAS utiliser ailleurs dans l'app en dehors de ces deux cas - find() reste la référence
    // pour tout listing/recherche qui doit rester filtré par agence.
    public static function findAny($id)
    {
        global $db;
        $client = new client();
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.* FROM " . static::$table . " A WHERE A.id = %s",
            GetSQLValueString($id, "int")
        );
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

    public static function findAll($active = false, $year = false,$agence = 1, $archived = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where A.id_agence = %s",
            GetSQLValueString($agence, "int")
        );
        if($active){
            $SQLselect .= " AND A.active = 1";
        }
        if ($archived) {
            $SQLselect .= " AND A.archived = 1";
        } else {
            $SQLselect .= " AND (A.archived = 0 OR A.archived IS NULL)";
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

    // Recherche globale (bandeau de recherche du bandeau haut) : nom, prénom, raison sociale,
    // email, téléphone, ICE ou RC - utilisé par com_search, jamais par le listing standard des
    // clients. Inclure ICE/RC/tel2/tel3 permet à une saisie purement numérique de retomber
    // naturellement sur le bon client sans logique de détection de format séparée.
    // $agence = false : pas de filtre d'agence du tout (recherche globale "autres agences" du
    // bandeau haut, com_search/controleurs/search/controleur.php - déclenchée seulement après un
    // premier essai sans résultat dans l'agence en cours, jamais par défaut).
    public static function search($terme, $agence = false)
    {
        global $db;
        $items = array();
        $like = GetSQLValueString('%' . $terme . '%', 'text');
        $SQLselect = "SELECT id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE 1=1"
            . ($agence ? " AND id_agence = " . intval($agence) : "")
            . " AND (nom LIKE $like OR prenom LIKE $like OR raison_social LIKE $like OR email LIKE $like OR tel LIKE $like OR tel2 LIKE $like OR tel3 LIKE $like OR ice LIKE $like OR rc LIKE $like)"
            . " ORDER BY id DESC LIMIT 8";
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    // Carte id_client => "sans aucune activité" (0 devis, 0 facture, 0 relance, 0 paiement),
    // pour le filtre du listing. Volontairement une seule requête agrégée (sous-requêtes
    // corrélées) plutôt qu'un appel getFacture()/getDevis() par client : ces méthodes
    // hydratent chaque facture/devis individuellement (facture::find() par ligne), ce qui
    // serait bien trop coûteux répété pour chaque ligne d'un listing de centaines de clients.
    public static function activityMap($agence = 1)
    {
        global $db;
        $map = array();
        $restrictionUser = '';
        if ($_SESSION['user']->isSuperUser() == false) {
            $restrictionUser = " AND id_user_added = " . intval($_SESSION['user']->getId());
        }
        $SQLselect = "SELECT c.id,
                (SELECT COUNT(*) FROM " . static::$tableDevis . " d WHERE d.id_client = c.id" . $restrictionUser . ") AS nb_devis,
                (SELECT COUNT(*) FROM " . static::$tableFacture . " f WHERE f.id_client = c.id" . $restrictionUser . ") AS nb_facture,
                (SELECT COUNT(*) FROM " . __prefixe_db__ . "relance r WHERE r.id_client = c.id) AS nb_relance,
                (SELECT COUNT(*) FROM " . __prefixe_db__ . "payment p INNER JOIN " . static::$tableFacture . " f2 ON f2.id = p.id_facture WHERE f2.id_client = c.id) AS nb_payment
            FROM " . static::$table . " c
            WHERE c.id_agence = " . GetSQLValueString($agence, "int");
        $result = $db->queryS($SQLselect);
        foreach ($result as $row) {
            $map[$row['id']] = ($row['nb_devis'] == 0 && $row['nb_facture'] == 0 && $row['nb_relance'] == 0 && $row['nb_payment'] == 0);
        }
        return $map;
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
    
    // CA total = somme des paiements réellement effectués (total - reste, calculé en live sur
    // chaque facture), regroupé par devise. Inclut donc aussi les paiements partiels sur des
    // factures encore ouvertes, pas seulement les factures intégralement soldées.
    public function getChiffreAffaireParDevise()
    {
        $totaux = array();
        foreach ($this->getFacture() as $facture) {
            $montantPaye = $facture->getTotal() - $facture->getReste();
            if ($montantPaye <= 0) {
                continue;
            }
            $devise = $facture->getDevise();
            if (!isset($totaux[$devise])) {
                $totaux[$devise] = 0;
            }
            $totaux[$devise] += $montantPaye;
        }
        return $totaux;
    }

    // Liste chronologique des paiements réels (toutes factures confondues) : utilisée pour
    // tracer l'évolution de l'encaissement dans le temps sur la fiche client.
    public function getPaymentsChronologiques()
    {
        $paiements = array();
        foreach ($this->getFacture() as $facture) {
            foreach (payment::findAll($facture->getId()) as $paiement) {
                $paiements[] = array(
                    'date' => $paiement->getDatePayment(),
                    'montant' => (float) $paiement->getMontant(),
                    'devise' => $facture->getDevise()
                );
            }
        }
        usort($paiements, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        return $paiements;
    }

    // Télécharge le logo du site (Clearbit puis repli favicon Google, mêmes sources que l'aperçu
    // côté formulaire) et l'enregistre dans images/clients/ pour servir de photo de client.
    // Retourne le nom de fichier généré, ou null si aucune des deux sources n'a rien retourné
    // d'exploitable (n'importe quelle erreur ici ne doit jamais bloquer l'enregistrement du client).
    public static function generateLogoPhoto($siteWeb)
    {
        $domaine = preg_replace('#^https?://#i', '', trim($siteWeb));
        $domaine = preg_replace('#^www\.#i', '', $domaine);
        $domaine = explode('/', $domaine)[0];
        if ($domaine == '') {
            return null;
        }

        $sources = array(
            'https://logo.clearbit.com/' . $domaine,
            'https://www.google.com/s2/favicons?domain=' . $domaine . '&sz=128'
        );

        foreach ($sources as $url) {
            try {
                $ch = curl_init($url);
                curl_setopt_array($ch, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => true
                ));
                $contenu = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // Clearbit répond parfois 200 avec un minuscule PNG "placeholder" quand le
                // domaine n'est pas dans sa base : on l'écarte via un seuil de taille plutôt
                // que de se fier uniquement au code HTTP.
                if ($contenu === false || $httpCode !== 200 || strlen($contenu) < 200) {
                    continue;
                }

                $nomFichier = 'logo_' . md5($domaine . microtime()) . '.png';
                $chemin = __DIR__ . '/../../../images/clients/' . $nomFichier;
                if (file_put_contents($chemin, $contenu) !== false) {
                    return $nomFichier;
                }
            } catch (\Throwable $e) {
                error_log('client::generateLogoPhoto - ' . $e->getMessage());
            }
        }
        return null;
    }

    // Mise à jour ciblée du récapitulatif IA (sans repasser par edit(), qui exige tous les autres champs)
    public function updateIaRecap($texte)
    {
        global $db;
        $this->ia_recap = $texte;
        $this->ia_recap_date = date('Y-m-d H:i:s');
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET ia_recap = %s, ia_recap_date = %s WHERE id = %s",
            GetSQLValueString($this->ia_recap, "text"),
            GetSQLValueString($this->ia_recap_date, "date"),
            GetSQLValueString($this->id, "int")
        );
        $db->query($SQLupdate);
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
	
	public static function filterClient($year = false,$agence = 1, $from = false, $to = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " C ON C.id =A.id_agence";
		if($year){
			$SQLselect .= " INNER JOIN ". static::$tableFacture . " B ON A.id = B.id_client";
		}
        $SQLselect .= " WHERE A.id_agence = " . intval($agence);
        if($year){
            $SQLselect .= " AND YEAR(A.date_add) = " . intval($year);
        }
        // Filtre par période (Date début/Date fin) : remplace le filtre par année sur le listing
        // principal, plus flexible (n'exige pas non plus qu'une facture existe, contrairement au
        // filtre par année ci-dessus qui a un usage distinct pour l'export "par année").
        if ($from) {
            $SQLselect .= " AND A.date_add >= " . GetSQLValueString($from, "date");
        }
        if ($to) {
            $SQLselect .= " AND A.date_add <= " . GetSQLValueString($to . " 23:59:59", "date");
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
        $client->setAgence(agence::find($data['id_agence'], $_SESSION['langue'] ?? 'fr'));
        $client->setUserAdded(user::find($data['id_user_added']));
        $client->setUserEdited(user::find($data['id_user_edited']));
        $client->setActive($data['active']);
        $client->setArchived(isset($data['archived']) ? $data['archived'] : 0);
        $client->setSource($data['source']);
        $client->setSiteWeb(isset($data['site_web']) ? $data['site_web'] : null);
        $client->setIaRecap(isset($data['ia_recap']) ? $data['ia_recap'] : null);
        $client->setIaRecapDate(isset($data['ia_recap_date']) ? $data['ia_recap_date'] : null);
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
			$SQLcount .= " AND YEAR(A.date_add) = " . intval($year);
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
        $token = getToken();
        if($token){
            // Un client ne doit voir que SA PROPRE fiche (cette méthode est aussi
            // appelée en interne par facture/devis/reclamation/rappel::buildApi(),
            // toujours avec l'id_client déjà validé côté appelant).
            if ((int) $id !== (int) $token->id) {
                return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
            }
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

    /* ------------------------------------------------------------------ */
    /* Connexion sociale (espace client) — Google / Facebook.
       Politique : clients EXISTANTS uniquement. On vérifie le jeton du
       fournisseur côté serveur, on en extrait l'email vérifié, puis on
       délègue à socialLoginByEmail() qui renvoie EXACTEMENT la même forme
       que loginApi (client + token JWT) pour que le reste de l'espace
       client fonctionne à l'identique. Aucun compte n'est créé ici.       */
    /* ------------------------------------------------------------------ */

    private static function socialHttpGet($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array('body' => $resp, 'http' => $http);
    }

    // Rattache un email vérifié à un client actif et émet un token. Ne crée jamais de compte.
    public static function socialLoginByEmail($email, $provider = 'social')
    {
        global $db;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE email = %s AND active = %s",
            GetSQLValueString($email, "text"),
            GetSQLValueString(1, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) > 0) {
            $data = $db->fetch_assoc($result);
            $data['ID'] = $data['id'];
            $client = static::buildApi($data);
            $token = setToken($client);
            return json_encode(array("icon" => "success", "message" => "Successful connection", "client" => $client, "token" => $token));
        }
        return json_encode(array("icon" => "warning", "message" => "No account is linked to this email address. Please contact us.", "code" => "no_account", "provider" => $provider));
    }

    public static function googleLoginApi($data)
    {
        if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '') {
            return json_encode(array("icon" => "error", "message" => "Google sign-in is not configured", "code" => "not_configured"));
        }
        $credential = isset($data['credential']) ? trim($data['credential']) : '';
        if ($credential === '') {
            return json_encode(array("icon" => "warning", "message" => "Missing Google credential", "code" => "missing"));
        }
        // Vérifie signature + expiration du jeton d'identité directement auprès de Google.
        $r = static::socialHttpGet("https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($credential));
        if ($r['body'] === false || $r['http'] !== 200) {
            return json_encode(array("icon" => "error", "message" => "Google verification failed", "code" => "verify_failed"));
        }
        $info = json_decode($r['body']);
        // L'audience DOIT être notre propre client ID (sinon jeton émis pour une autre app).
        if (!is_object($info) || !isset($info->aud) || $info->aud !== GOOGLE_CLIENT_ID) {
            return json_encode(array("icon" => "error", "message" => "Invalid Google token", "code" => "invalid_token"));
        }
        $emailVerified = isset($info->email_verified) && ($info->email_verified === true || $info->email_verified === 'true');
        if (!$emailVerified || empty($info->email)) {
            return json_encode(array("icon" => "warning", "message" => "Google email not verified", "code" => "email_unverified"));
        }
        return static::socialLoginByEmail($info->email, 'google');
    }

    // Scaffold Facebook : actif dès que FACEBOOK_APP_ID / FACEBOOK_APP_SECRET sont renseignés.
    public static function facebookLoginApi($data)
    {
        if (!defined('FACEBOOK_APP_ID') || FACEBOOK_APP_ID === '' || !defined('FACEBOOK_APP_SECRET') || FACEBOOK_APP_SECRET === '') {
            return json_encode(array("icon" => "error", "message" => "Facebook sign-in is not configured", "code" => "not_configured"));
        }
        $token = isset($data['access_token']) ? trim($data['access_token']) : '';
        if ($token === '') {
            return json_encode(array("icon" => "warning", "message" => "Missing Facebook token", "code" => "missing"));
        }
        // 1) Le jeton appartient-il bien à NOTRE app ? (anti-substitution de jeton)
        $appToken = FACEBOOK_APP_ID . '|' . FACEBOOK_APP_SECRET;
        $dbg = json_decode(static::socialHttpGet("https://graph.facebook.com/debug_token?input_token=" . urlencode($token) . "&access_token=" . urlencode($appToken))['body']);
        if (!is_object($dbg) || !isset($dbg->data) || empty($dbg->data->is_valid) || (string) $dbg->data->app_id !== (string) FACEBOOK_APP_ID) {
            return json_encode(array("icon" => "error", "message" => "Invalid Facebook token", "code" => "invalid_token"));
        }
        // 2) Email vérifié depuis le profil.
        $me = json_decode(static::socialHttpGet("https://graph.facebook.com/me?fields=email&access_token=" . urlencode($token))['body']);
        if (!is_object($me) || empty($me->email)) {
            return json_encode(array("icon" => "warning", "message" => "Facebook email unavailable", "code" => "missing"));
        }
        return static::socialLoginByEmail($me->email, 'facebook');
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
            $db->query($SQLupdate);
            // NB : mysql::query() ne renvoie jamais rien d'exploitable (voir
            // com_config/classes/mysql.php) — on vérifie le vrai succès via
            // l'erreur mysqli, pas la valeur de retour de query().
            if (!$db->getLink()->error) {
                $config = new config($db);
                $mail = new PHPMailer();
                if (defined('SMTP_HOST') && SMTP_HOST != '') {
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
                }
                $mailBody = '<html><body>
                        		<table border="0" width="100%">
                        		<tr>
                        		<td bgcolor="#F6F6F6" align="center" style="padding-top:20px">
                        
                        		<table border="0" cellpadding="15" cellspacing="0" style="100%">
                        			<tr>
                        				<td align="center"><img src="'.$siteURL.'images/logo_hello_world.png" alt="Hello World Agency" height="64" /></td>
                        			</tr>
                        			<tr bgcolor="#FFFFFF">
                        				<td align="center" style="padding-bottom:0px"><h1 style="font-weight:normal; margin-bottom:0px;">Réinitialisation du mot de passe</h1></td>
                        			</tr>
                        			<tr bgcolor="#FFFFFF">
                        				<td style="padding-bottom:0px">
                        				<table border="0" cellpadding="5">
                        				<tr style="text-align:center">
                        					<td>
                        					    <p style="color: grey;text-align: center;margin-top: 0;">Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous pour en créer un nouveau. Si vous n’êtes pas à l’origine de cette demande, ignorez simplement cet email.</p>
                        					</td>
                        				</tr>
                        				<tr style="text-align:center">
                        				    <td style="padding-bottom: 30px;">
                        				        <a style="padding: 10px 20px;background: #0ac3e0;color: white;font-weight: bold;margin: auto;display: block;width: fit-content;text-decoration:none" href="'.$hwaURL.'create-a-new-password/'.$email.'/'.$token.'/">Réinitialiser mon mot de passe</a>
                        				    </td>
                        				</tr>
                        				</table>
                        				</td>
                        			</tr>
                        			<tr>
                        				<td align="center"><p><font size="-3" color="#666666">Contact Hello World Agency<br/>
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
                // Doit correspondre au compte SMTP authentifié (SMTP_USERNAME = sales@) sinon
                // certains serveurs rejettent l'envoi pour mismatch From / compte authentifié.
                $mail->setFrom((defined('SMTP_HOST') && SMTP_HOST != '') ? SMTP_USERNAME : "contact@helloworld-agency.com");
                //Set an alternative reply-to address
                $mail->addReplyTo($config->getEmail(), $config->getNom());
                //Set who the message is to be sent to
                $mail->addAddress($data['email'], $data['nom'].' '.$data['prenom']);
                $mail->addAddress("contact@helloworld-agency.com");
                //Set the subject line
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                // Email HTML simple (même mécanisme que le formulaire de contact).
                // NB : ne pas utiliser msgHTML() ici — il génère un message
                // multipart/alternative dont l'en-tête Content-Type est mal
                // transmis par le transport mail() du serveur, ce qui fait
                // afficher le MIME brut côté client. Un seul bloc text/html règle ça.
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                $mail->Body = $mailBody;

                if($mail->send()) {
                    if (function_exists('copierEmailEnvoyeVersDossierEnvoyes')) {
                        copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
                    }
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
        $token = getToken();
        if($token){
            // Un client ne doit voir que SA PROPRE fiche.
            if ((int) $id !== (int) $token->id) {
                return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
            }
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
        $token = getToken();
        if($token){
            global $db;
            // Un client ne doit pouvoir changer QUE son propre mot de passe : on
            // ignore l'id_client fourni par l'appelant (potentiellement falsifié
            // — sinon n'importe quel client authentifié pourrait écraser le mot
            // de passe de n'importe quel autre client) et on scope sur le token.
            $SQLinsert = sprintf(
                "UPDATE " . static::$table . " SET  password = %s, last_edit = %s WHERE id = %s",
                GetSQLValueString(md5($data['password']), "text"),
                GetSQLValueString(date("Y-m-d"), "date"),
                GetSQLValueString((int) $token->id, "int")
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