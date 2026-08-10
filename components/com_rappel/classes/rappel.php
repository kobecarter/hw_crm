<?php

class rappel
{
    static $table =  __prefixe_db__ . "rappel";
    static $table2 =  __prefixe_db__ . "relances";
    static $tableAgence =  __prefixe_db__ . "agence";
    static $tableClient =  __prefixe_db__ . "client";

    private $id;
    private $client;
    private $type;
    private $domaine;
    private $date_expir;
    private $remarque;
    private $fournisseurs_ids;
    private $date_add;
    private $last_edit;
    private $archived;

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

    public function getType()
    {
        return $this->type;
    }

    public function getDomaine()
    {
        return $this->domaine;
    }

    public function getDateExpir()
    {
        return $this->date_expir;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    // Fournisseurs associés (Module 2 - multi-sélection), JSON plutôt qu'une table pivot - même
    // choix et même raisonnement que charge::getFournisseursIds().
    public function getFournisseursIds()
    {
        return is_array($this->fournisseurs_ids) ? $this->fournisseurs_ids : array();
    }

    public function getFournisseurs()
    {
        $items = array();
        foreach ($this->getFournisseursIds() as $idFournisseur) {
            $fournisseur = fournisseur::find($idFournisseur);
            if ($fournisseur->getId()) {
                $items[] = $fournisseur;
            }
        }
        return $items;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function getDaysLeft()
    {
        $from = new DateTime();
        $to = new DateTime($this->date_expir);

        $interval = $from->diff($to);
        return $interval->format('%r%a');
    }

    public function isArchived()
    {
        return $this->archived ? 1 : 0;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setDomaine($domaine)
    {
        $this->domaine = $domaine;
    }

    public function setDateExpir($date_expir)
    {
        $this->date_expir = $date_expir;
    }

    public function setRemarque($seo_titre)
    {
        $this->remarque = $seo_titre;
    }

    public function setFournisseursIds($fournisseurs_ids)
    {
        $this->fournisseurs_ids = is_array($fournisseurs_ids) ? array_values(array_unique(array_map('intval', $fournisseurs_ids))) : array();
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, type, domaine, date_expir, remarque, fournisseurs_ids, date_add, last_edit, archived) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->domaine, "text"),
            GetSQLValueString($this->date_expir, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString(!empty($this->getFournisseursIds()) ? json_encode($this->getFournisseursIds()) : null, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->archived, "int")
        );
        
        //echo $SQLinsert;
        
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 2;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_client = %s, type = %s, domaine = %s, date_expir = %s, remarque = %s, fournisseurs_ids = %s, last_edit = %s , archived = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->domaine, "text"),
            GetSQLValueString($this->date_expir, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString(!empty($this->getFournisseursIds()) ? json_encode($this->getFournisseursIds()) : null, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->archived, "int"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 2;
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

    public static function find($id, $agence = 1)
    {
        global $db;
        $rappel = new rappel();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$tableClient . " B ON B.id = A.id_client INNER JOIN " . static::$tableAgence . " C ON C.id =B.id_agence WHERE  C.id = %s and A.ID = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $rappel = static::build($data);
        }
        return $rappel;
    }

    public static function findAll($archive,$agence = 1, $client = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$tableClient . " B ON B.id = A.id_client INNER JOIN " . static::$tableAgence . " C ON C.id =B.id_agence where C.id = %s",
            GetSQLValueString($agence, "int")
        );

        if ($client) {
            $SQLselect .= " AND A.id_client = " . intval($client);
        }
        
        if ($archive) {
            // $SQLselect .= " ORDER BY SIGN(DATEDIFF(date_expir,CURRENT_DATE)+1) DESC, ABS(DATEDIFF(date_expir,CURRENT_DATE))";
            $SQLselect .= " AND A.archived = 1";
        } else {
            $SQLselect .= " AND (A.archived IS NULL OR A.archived = 0)";
        }
        $SQLselect .= " ORDER BY A.date_expir";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $rappel = static::build($data);
            array_push($items, $rappel);
        }
        return $items;
    }

    public static function build($data)
    {
        $rappel = new rappel();
        $rappel->setId($data['ID']);
        $rappel->setClient(client::find($data['id_client'],$_SESSION['agence']));
        $rappel->setType($data['type']);
        $rappel->setDomaine($data['domaine']);
        $rappel->setDateExpir($data['date_expir']);
        $rappel->setRemarque($data['remarque']);
        $rappel->setFournisseursIds(!empty($data['fournisseurs_ids']) ? json_decode($data['fournisseurs_ids'], true) : array());
        $rappel->setDateAdd($data['date_add']);
        $rappel->setLastEdit($data['last_edit']);
        $rappel->setArchived($data['archived']);
        return $rappel;
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

    // Module 1 (cahier des charges "charges d'achat -> rappels") : appelée depuis
    // com_charge/controleurs/charge/controleur.php (addCharge/editCharge) quand une charge validée
    // correspond à l'un des 3 services suivis (getServiceConcerne() = 'domaine'/'hosting'/'ssl',
    // mêmes slugs que le champ "type" ci-dessus) ET porte un client. Cherche un rappel actif du
    // même client + même type : s'il existe, pousse son échéance à date_charge+365 jours et
    // l'annote "Renouvelé" ; sinon en crée un nouveau avec cette échéance. Dans les deux cas, les
    // fournisseurs de la charge sont fusionnés (jamais remplacés) avec ceux déjà sur le rappel.
    // Ne fait jamais échouer la sauvegarde de la charge elle-même (appelant responsable du
    // try/catch si besoin) - une charge doit toujours pouvoir s'enregistrer même si cette
    // synchronisation échoue pour une raison quelconque.
    public static function synchroniserDepuisCharge($charge)
    {
        $servicesSuivis = array('domaine', 'hosting', 'ssl');
        if (!$charge->getClient() || $charge->getClient()->getId() == 0) {
            return null;
        }
        if (!in_array($charge->getServiceConcerne(), $servicesSuivis, true)) {
            return null;
        }

        $nouvelleExpir = date('Y-m-d', strtotime($charge->getDateCharge() . ' +365 days'));
        $fournisseursCharge = $charge->getFournisseursIds();

        $correspondant = null;
        foreach (self::findAll(false, $_SESSION['agence'], $charge->getClient()->getId()) as $r) {
            if ($r->getType() === $charge->getServiceConcerne()) {
                $correspondant = $r;
                break;
            }
        }

        if ($correspondant) {
            $correspondant->setDateExpir($nouvelleExpir);
            $note = 'Renouvelé automatiquement le ' . date('d/m/Y') . ' (charge #' . $charge->getId() . ' - ' . $charge->getTitre() . ').';
            $remarqueActuelle = trim((string) $correspondant->getRemarque());
            $correspondant->setRemarque($remarqueActuelle !== '' ? $note . "\n" . $remarqueActuelle : $note);
            $correspondant->setFournisseursIds(array_merge($correspondant->getFournisseursIds(), $fournisseursCharge));
            $correspondant->edit();
            return $correspondant;
        }

        $nouveau = new rappel();
        $nouveau->setClient($charge->getClient());
        $nouveau->setType($charge->getServiceConcerne());
        $nouveau->setDomaine($charge->getTitre());
        $nouveau->setDateExpir($nouvelleExpir);
        $nouveau->setRemarque('Créé automatiquement depuis la charge #' . $charge->getId() . ' (' . $charge->getTitre() . ').');
        $nouveau->setFournisseursIds($fournisseursCharge);
        $nouveau->setDateAdd(date('Y-m-d'));
        $nouveau->setLastEdit(date('Y-m-d'));
        $nouveau->setArchived(0);
        $nouveau->add();
        return $nouveau;
    }

    // Relances automatiques d'expiration (cron quotidien, voir com_rappel/controleurs/router.php
    // task=cronRelancesExpirationRappel) : contrairement à sendRelance() ci-dessous (déclenchement
    // manuel depuis le listing, "moins de 30 jours" au sens large), celle-ci envoie exactement à
    // J-30, J-20, J-5 et J0 - une seule fois par palier et par rappel (voir relanceDejaEnvoyee()),
    // toutes agences confondues puisqu'un cron externe n'a pas de notion d'agence courante.
    public static function cronEnvoyerRelancesExpiration()
    {
        $seuils = array(30, 20, 5, 0);
        $resume = array('envoyees' => 0, 'deja_envoyees' => 0, 'hors_palier' => 0, 'erreurs' => 0, 'details' => array());

        foreach (agence::findAll('fr') as $ag) {
            // build() résout le client via client::find($id, $_SESSION['agence']) qui filtre
            // strictement par agence (voir com_client/classes/client.php:492) - sans ce réglage
            // par itération, tous les rappels des agences != celle fixée par bootstrapSystemSession()
            // se retrouveraient avec un client vide et n'enverraient jamais d'email, silencieusement.
            $_SESSION['agence'] = $ag->getId();
            foreach (self::findAll(false, $ag->getId()) as $r) {
                // Pas getDaysLeft() ici : elle diffuse depuis new DateTime() (heure actuelle
                // incluse), donc son résultat dérive selon l'heure d'exécution du cron - inadapté
                // à des paliers calendaires fixes. On reproduit plutôt le calcul de
                // com_accounting/controleurs/router.php::cronCheckEcheanceTvaEndpoint() (diff
                // depuis 'today', minuit) pour un nombre de jours entiers stable toute la journée.
                $joursRestants = (int) (new DateTime('today'))->diff(new DateTime($r->getDateExpir()))->format('%r%a');
                if (!in_array($joursRestants, $seuils, true)) {
                    $resume['hors_palier']++;
                    continue;
                }
                if (self::relanceDejaEnvoyee($r->getId(), $joursRestants)) {
                    $resume['deja_envoyees']++;
                    continue;
                }

                $nomClient = $r->getClient() ? trim($r->getClient()->getRaisonSocial()) : '';
                try {
                    $message = self::envoyerEmailExpiration($r, $joursRestants);
                    self::enregistrerRelance($r->getId(), $joursRestants, $message);
                    $resume['envoyees']++;
                    $resume['details'][] = array('rappel' => $r->getId(), 'client' => $nomClient, 'seuil' => $joursRestants, 'action' => 'envoyee');
                } catch (\Throwable $e) {
                    error_log('rappel::cronEnvoyerRelancesExpiration - rappel #' . $r->getId() . ' - ' . $e->getMessage());
                    $resume['erreurs']++;
                    $resume['details'][] = array('rappel' => $r->getId(), 'client' => $nomClient, 'seuil' => $joursRestants, 'action' => 'erreur');
                }
            }
        }
        return $resume;
    }

    private static function relanceDejaEnvoyee($idRappel, $seuilJours)
    {
        global $db;
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table2 . " WHERE id_rappel = %s AND seuil_jours = %s LIMIT 1",
            GetSQLValueString($idRappel, "int"),
            GetSQLValueString($seuilJours, "int")
        );
        $result = $db->query($SQLselect);
        return $db->num_rows($result) > 0;
    }

    private static function enregistrerRelance($idRappel, $seuilJours, $message)
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table2 . " (id_rappel, seuil_jours, date_send, message) VALUES (%s, %s, %s, %s)",
            GetSQLValueString($idRappel, "int"),
            GetSQLValueString($seuilJours, "int"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date"),
            GetSQLValueString($message, "text")
        );
        $db->query($SQLinsert);
    }

    // Construit et envoie l'email d'expiration via PHPMailer/SMTP (contrairement à sendRelance()
    // ci-dessous, resté sur l'ancien mail() natif) - retourne le corps envoyé, pour l'archiver tel
    // quel dans crm_relances.message comme le fait déjà sendRelance().
    private static function envoyerEmailExpiration($rappel, $joursRestants)
    {
        if (!defined('SMTP_HOST') || SMTP_HOST == '') {
            return null;
        }
        $client = $rappel->getClient();
        if (!$client || !$client->getEmail()) {
            return null;
        }

        require_once __DIR__ . '/../../../vendor/autoload.php';

        $typeLabels = array('domaine' => 'Nom de domaine', 'hosting' => 'Hébergement web', 'ssl' => 'Certificat SSL');
        $typeLabel = isset($typeLabels[$rappel->getType()]) ? $typeLabels[$rappel->getType()] : $rappel->getType();
        $dateExpirFormatee = date('d/m/Y', strtotime($rappel->getDateExpir()));
        $domaineTexte = trim((string) $rappel->getDomaine()) !== '' ? ' (' . htmlspecialchars($rappel->getDomaine()) . ')' : '';

        if ($joursRestants == 0) {
            $sujet = "Expiration aujourd'hui - " . $typeLabel . $domaineTexte;
            $phraseEcheance = "expire <strong>aujourd'hui</strong> (" . $dateExpirFormatee . ")";
        } else {
            $sujet = "Expiration dans " . $joursRestants . " jour" . ($joursRestants > 1 ? 's' : '') . " - " . $typeLabel . $domaineTexte;
            $phraseEcheance = "expire dans <strong>" . $joursRestants . " jour" . ($joursRestants > 1 ? 's' : '') . "</strong>, le " . $dateExpirFormatee;
        }

        $nomClient = trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
        $corps = "Bonjour " . htmlspecialchars($nomClient) . ",<br><br>"
            . "Nous vous informons que votre service <strong>" . htmlspecialchars($typeLabel) . "</strong>" . $domaineTexte . " " . $phraseEcheance . ".<br><br>"
            . "Merci de nous confirmer le renouvellement de ce service afin d'éviter toute interruption.<br><br>"
            . "Cordialement.";

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_USERNAME, 'Hello World');
        $mail->addAddress($client->getEmail(), $nomClient);
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $corps;
        $mail->AltBody = strip_tags($corps);
        $mail->send();

        return $corps;
    }

    public function sendRelance()
    {
        global $siteURL, $db;
        $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
        $config = new config($db, $_SESSION['langue']);

        if ($this->getDaysLeft() < 30) {
            switch ($this->type) {
                case 'domaine':
                    $type = "Nom de domaine";
                    break;
                case 'hosting':
                    $type = "Hébergement";
                    break;
                case 'ssl':
                    $type = "Certificat SSL";
                    break;
            }
            $mail = '
		<table border="0" width="100%">
		<tr>
		<td bgcolor="#F6F6F6" align="center">

		<table border="0" cellpadding="15" cellspacing="0" width="640">
			<tr>
				<td align="center"><img src="' . $siteURL . '/images/agences/' . $agence->getLogo() . '" alt="' . $agence->getNom() . '" height="64" /></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td align="center"><h1 style="font-weight:normal; margin-bottom:15px;">Relance renouvellement ' . $type . ' (<font color="#eeb609">' . $this->domaine . '</font>)</h1></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td>';

            if ($this->getDaysLeft() < 0)
                $mail .= '<p align="center">Votre ' . $type . ' (' . $this->domaine . ') est expiré</p>';
            else
                $mail .= '<p align="center">Votre ' . $type . ' (' . $this->domaine . ') expire dans ' . $this->getDaysLeft() . ' jours</p>';

            $mail .= '<p>Merci de nous confirmer le renouvellement du service en question</p>
					<p>Cordialement</p>
				</td>
			</tr>
			<tr>
				<td align="center"><p><font size="-3" color="#666666">' . $agence->getNom() . ' Contact<br/>
		Email: ' . $agence->getEmail() . '<br/>
		Tél. : ' . $agence->getTel() . ' / ' . $agence->getTel2() . ' / ' . $agence->getFax() . '</font></p></td>
			</tr>
		</table>

		</td>
		</tr>
		</table>';

            $mailBody = '<html><body>' . $mail . '</body></html>';

            // envoi mail client	
            $headers = 'From: <' . $agence->getEmail() . '>' . "\n";
            $headers .= 'Reply-To: ' . $agence->getEmail() . "\n";
            $headers .= 'Content-Type: text/html; charset="utf-8"' . "\n";
            $headers .= 'Content-Transfer-Encoding: 8bit';
            mail($this->client->getEmail(), 'Relance renouvellement', $mailBody, $headers);

            // date d'envoi
            date_default_timezone_set("Africa/Casablanca");
            $sent = date("Y-m-d H:i:s");



            global $db;
            $SQLinsert = sprintf(
                "INSERT INTO " . static::$table2 . " (id_rappel, date_send, message) VALUES (%s, %s, %s)",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($sent, "date"),
                GetSQLValueString($mail, "text")
            );

            if (!$db->query($SQLinsert))
                return 1;
            else return 3;
        } else {
            return 2;
        }
    }

    // API

    public static function buildApi($data)
    {
        $rappel = array(
            'ID' => $data['ID'],
            'client' => client::ApiFindById($data['id_client']),
            'type' => $data['type'],
            'domaine' => $data['domaine'],
            'date_expir' => $data['date_expir'],
            'remarque' => $data['remarque'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
            'archived' => $data['archived'],
        );
        return $rappel;
    }

    public static function findAllByClientApi($clientID = 0)
    {
        if(getToken()){
            global $db;
            $items = array();
            $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$tableClient . " B ON B.id = A.id_client INNER JOIN " . static::$tableAgence . " C ON C.id =B.id_agence where A.id_client = %s",
                GetSQLValueString($clientID, "int")
            );

            $SQLselect .= " ORDER BY A.date_expir DESC";
            $result = $db->queryS($SQLselect);
            foreach ($result as $data) {
                $rappel = static::buildApi($data);
                array_push($items, $rappel);
            }
            return $items;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }
}
