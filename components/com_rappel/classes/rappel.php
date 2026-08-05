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
            "INSERT INTO " . static::$table . " (id_client, type, domaine, date_expir, remarque, date_add, last_edit, archived) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->domaine, "text"),
            GetSQLValueString($this->date_expir, "date"),
            GetSQLValueString($this->remarque, "text"),
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
            "UPDATE " . static::$table . " SET  id_client = %s, type = %s, domaine = %s, date_expir = %s, remarque = %s, last_edit = %s , archived = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->domaine, "text"),
            GetSQLValueString($this->date_expir, "date"),
            GetSQLValueString($this->remarque, "text"),
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
