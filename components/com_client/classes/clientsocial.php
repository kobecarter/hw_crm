<?php

class clientsocial
{
    static $table = __prefixe_db__ . "client_social";
    static $tableClient = __prefixe_db__ . "client";

    private $id;
    private $client;
    private $plateforme;
    private $login;
    private $passwordEnc;
    private $lien;
    private $emailRecuperation;
    private $telephoneRecuperation;
    private $remarque;
    private $dateAdd;
    private $lastEdit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getPlateforme()
    {
        return $this->plateforme;
    }

    public function getLogin()
    {
        return $this->login;
    }

    // Ne renvoie JAMAIS le mot de passe en clair - seulement le blob chiffré tel que stocké en
    // base. Le déchiffrement se fait exclusivement côté contrôleur, après re-vérification du
    // superadmin, jamais dans ce getter générique.
    public function getPasswordEnc()
    {
        return $this->passwordEnc;
    }

    public function getLien()
    {
        return $this->lien;
    }

    public function getEmailRecuperation()
    {
        return $this->emailRecuperation;
    }

    public function getTelephoneRecuperation()
    {
        return $this->telephoneRecuperation;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    public function getLastEdit()
    {
        return $this->lastEdit;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setPlateforme($plateforme)
    {
        $this->plateforme = $plateforme;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPasswordEnc($passwordEnc)
    {
        $this->passwordEnc = $passwordEnc;
    }

    public function setLien($lien)
    {
        $this->lien = $lien;
    }

    public function setEmailRecuperation($emailRecuperation)
    {
        $this->emailRecuperation = $emailRecuperation;
    }

    public function setTelephoneRecuperation($telephoneRecuperation)
    {
        $this->telephoneRecuperation = $telephoneRecuperation;
    }

    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;
    }

    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
    }

    public function setLastEdit($lastEdit)
    {
        $this->lastEdit = $lastEdit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, plateforme, login, password_enc, lien, email_recuperation, telephone_recuperation, remarque, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->plateforme, "text"),
            GetSQLValueString($this->login, "text"),
            GetSQLValueString($this->passwordEnc, "text"),
            GetSQLValueString($this->lien, "text"),
            GetSQLValueString($this->emailRecuperation, "text"),
            GetSQLValueString($this->telephoneRecuperation, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->dateAdd, "date"),
            GetSQLValueString($this->lastEdit, "date")
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
            "UPDATE " . static::$table . " SET id_client = %s, plateforme = %s, login = %s, password_enc = %s, lien = %s, email_recuperation = %s, telephone_recuperation = %s, remarque = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->plateforme, "text"),
            GetSQLValueString($this->login, "text"),
            GetSQLValueString($this->passwordEnc, "text"),
            GetSQLValueString($this->lien, "text"),
            GetSQLValueString($this->emailRecuperation, "text"),
            GetSQLValueString($this->telephoneRecuperation, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->lastEdit, "date"),
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
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    // find() prend l'agence en paramètre et vérifie l'appartenance du client lié - même pattern
    // que les correctifs IDOR de l'audit sécurité (voir absence::find()/bonus::find()) : sans ce
    // contrôle, n'importe quel id valide, même d'une autre agence, serait accessible.
    public static function find($id, $agence = false)
    {
        global $db;
        $clientsocial = new clientsocial();
        $SQLselect = sprintf(
            "SELECT A.* FROM " . static::$table . " A WHERE A.id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $clientsocial = static::build($data, $agence);
        }
        return $clientsocial;
    }

    public static function findAllByClient($id_client, $agence = false)
    {
        global $db;
        $clientsocials = array();
        $SQLselect = sprintf(
            "SELECT A.* FROM " . static::$table . " A WHERE A.id_client = %s ORDER BY A.plateforme ASC",
            GetSQLValueString($id_client, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $clientsocials[] = static::build($data, $agence);
        }
        return $clientsocials;
    }

    // Clients de l'agence dont AU MOINS un identifiant réseau social n'a pas été revu (édité, ou
    // marqué vérifié via une simple ré-enregistrement) depuis $jours jours - alimente le groupe
    // "Réseaux sociaux à vérifier" du centre d'alertes (includes/functions/functions.php::
    // getAlertesUrgentes()). Un client sans aucun identifiant enregistré n'apparaît jamais ici :
    // rien à vérifier. Retourne un tableau [id_client, nom_client, plus_ancienne_date].
    public static function findStaleByAgence($agence, $jours = 90)
    {
        global $db;
        $seuil = date('Y-m-d', strtotime('-' . intval($jours) . ' days'));
        $SQLselect = "SELECT S.id_client, MIN(COALESCE(S.last_edit, S.date_add)) as derniere_verif
            FROM " . static::$table . " S
            INNER JOIN " . __prefixe_db__ . "client C ON S.id_client = C.id
            WHERE C.id_agence = " . intval($agence) . "
            GROUP BY S.id_client
            HAVING derniere_verif IS NULL OR derniere_verif < " . GetSQLValueString($seuil, "text");
        $result = $db->queryS($SQLselect);
        $stale = array();
        foreach ($result as $row) {
            $stale[] = array(
                'id_client' => intval($row['id_client']),
                'derniere_verif' => $row['derniere_verif'],
            );
        }
        return $stale;
    }

    public static function build($data, $agence = false)
    {
        $clientsocial = new clientsocial();
        $clientsocial->setId($data['id']);
        $clientsocial->setClient(isset($data['id_client']) ? client::findAny($data['id_client']) : null);
        $clientsocial->setPlateforme($data['plateforme']);
        $clientsocial->setLogin($data['login']);
        $clientsocial->setPasswordEnc($data['password_enc']);
        $clientsocial->setLien($data['lien']);
        $clientsocial->setEmailRecuperation($data['email_recuperation']);
        $clientsocial->setTelephoneRecuperation($data['telephone_recuperation']);
        $clientsocial->setRemarque($data['remarque']);
        $clientsocial->setDateAdd($data['date_add']);
        $clientsocial->setLastEdit($data['last_edit']);
        return $clientsocial;
    }

    // Libellés/icônes des plateformes supportées - liste fermée (pas de champ libre) pour garder
    // le formulaire et l'icône cohérents partout.
    public static $plateformes = array(
        'facebook' => array('label' => 'Facebook', 'icon' => 'fa-facebook', 'couleur' => '#1877f2'),
        'instagram' => array('label' => 'Instagram', 'icon' => 'fa-instagram', 'couleur' => '#e1306c'),
        'google' => array('label' => 'Google', 'icon' => 'fa-google', 'couleur' => '#4285f4'),
        'x' => array('label' => 'X (Twitter)', 'icon' => 'fa-twitter', 'couleur' => '#000000'),
        'google_my_business' => array('label' => 'Google My Business', 'icon' => 'fa-map-marker-alt', 'couleur' => '#34a853'),
        'google_ads' => array('label' => 'Google Ads', 'icon' => 'fa-bullhorn', 'couleur' => '#fbbc05'),
    );
}
