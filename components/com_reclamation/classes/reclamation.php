<?php

class reclamation
{
    static $table =  __prefixe_db__ . "reclamation";
    static $tableFacture =  __prefixe_db__ . "facture";
    static $tableClient =  __prefixe_db__ . "client";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $client;
    private $id_facture;
    private $department;
    private $sujet;
    private $message;
    private $reponse;
    private $etat;
    private $date_reponse;
    private $date_add;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isProcess()
    {
        return $this->etat ? 1 : 0;
    }

    public function getClient()
    {
        return $this->client;
    }
    
    public function getDepartment()
    {
        return $this->department;
    }

    public function getFactureId()
    {
        return $this->id_facture;
    }

    public function getSujet()
    {
        return $this->sujet;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getReponse()
    {
        return $this->reponse;
    }

    public function getDateReponse()
    {
        return $this->date_reponse;
    }

    public function hasReponse()
    {
        return trim((string) $this->reponse) !== '';
    }

    public function toArray()
    {
        return array(
            "id" => $this->id,
            "id_facture" => $this->id_facture,
            "department" => $this->department,
            "sujet" => $this->sujet,
            "message" => $this->message,
            "reponse" => $this->reponse,
            "date_reponse" => $this->date_reponse,
            "etat" => (int) $this->etat,
            "date_add" => $this->date_add,
        );
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function setFactureId($id_facture)
    {
        $this->id_facture = $id_facture;
    }

    public function setSujet($sujet)
    {
        $this->sujet = $sujet;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setReponse($reponse)
    {
        $this->reponse = $reponse;
    }

    public function setDateReponse($date_reponse)
    {
        $this->date_reponse = $date_reponse;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, id_facture, department, sujet, message, etat, date_add) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->id_facture, "int"),
            GetSQLValueString($this->department, "text"),
            GetSQLValueString($this->sujet, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->etat, "int"),
            GetSQLValueString($this->date_add, "date")
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
            "UPDATE " . static::$table . " SET  id_client = %s, department = %s, sujet = %s, message = %s, reponse = %s, date_reponse = %s, etat = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->department, "text"),
            GetSQLValueString($this->sujet, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->reponse, "text"),
            GetSQLValueString($this->date_reponse, "text"),
            GetSQLValueString($this->etat, "int"),
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
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }


    public static function find($id,$agence = 1)
    {
        global $db;
        $reclamation = new reclamation();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id WHERE C.id = %s and A.id = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $reclamation = static::build($data);
        }
        return $reclamation;
    }

    public static function findAll($etat = false, $client = false,$agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where C.id = %s",
            GetSQLValueString($agence, "int"),
        );

        if ($etat) {
            $SQLselect .= " AND etat = 1";
        }

        if ($client) {
            $SQLselect .= " AND A.id_client = " . intval($client);
        }

        $SQLselect .= " ORDER BY A.date_add DESC, id DESC";

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $reclamation = static::build($data);
            array_push($items, $reclamation);
        }
        return $items;
    }

    public static function build($data)
    {
        $reclamation = new reclamation();
        $reclamation->setId($data['id']);
        $reclamation->setClient(client::find($data['id_client'],$_SESSION['agence']));
        $reclamation->setFactureId(isset($data['id_facture']) ? $data['id_facture'] : null);
        $reclamation->setDepartment($data['department']);
        $reclamation->setSujet($data['sujet']);
        $reclamation->setMessage($data['message']);
        $reclamation->setReponse(isset($data['reponse']) ? $data['reponse'] : null);
        $reclamation->setDateReponse(isset($data['date_reponse']) ? $data['date_reponse'] : null);
        $reclamation->setEtat($data['etat']);
        $reclamation->setDateAdd($data['date_add']);
        return $reclamation;
    }
    

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count($year = false)
    {
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;

        if ($year) {
            $SQLcount .= " WHERE YEAR(date_add) = " . intval($year);
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    // API
    public static function buildApi($data)
    {
        $reclamation = array(
            'id' => $data['id'],
            'client' => client::ApiFindById($data['id_client']),
            'id_facture' => isset($data['id_facture']) ? $data['id_facture'] : null,
            'department' => $data['department'],
            'sujet' => $data['sujet'],
            'message' => $data['message'],
            'reponse' => isset($data['reponse']) ? $data['reponse'] : null,
            'date_reponse' => isset($data['date_reponse']) ? $data['date_reponse'] : null,
            'etat' => $data['etat'],
            'date_add' => $data['date_add'],
        );
        return $reclamation;
    }

    public static function findAllByClientApi($clientID = false)
    {
        $token = getToken();
        if($token){
            global $db;
            $items = array();
            // Un client ne doit voir que SES PROPRES réclamations : on ignore le
            // clientID fourni par l'appelant et on scope sur le token vérifié.
            $clientID = (int) $token->id;
            $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where A.id_client = %s",
                GetSQLValueString($clientID, "int"),
            );

            $SQLselect .= " ORDER BY A.date_add DESC, id DESC";

            $result = $db->queryS($SQLselect);
            foreach ($result as $data) {
                $reclamation = static::buildApi($data);
                array_push($items, $reclamation);
            }
            return $items;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function createReclamationApi($data)
    {
        $token = getToken();
        if($token){
            global $db;
            // On ignore l'id_client fourni par l'appelant (falsifiable) et on
            // attribue toujours la réclamation à l'auteur réel du token.
            $SQLinsert = sprintf(
                "INSERT INTO " . static::$table . " (id_client, department, sujet, message, etat, date_add) VALUES (%s, %s, %s, %s, %s, %s)",
                GetSQLValueString((int) $token->id, "int"),
                GetSQLValueString($data['department'], "text"),
                GetSQLValueString($data['sujet'], "text"),
                GetSQLValueString($data['message'], "text"),
                GetSQLValueString(0, "int"),
                GetSQLValueString(date("Y-m-d"), "date")
            );
            if (!$db->query($SQLinsert)) {
                return json_encode(array("icon"=>"success","message"=>"The request has been successfully sent"));
            } else {
                return json_encode(array("icon"=>"warning","message"=>"The request has not been sent"));
            }
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    // Modification d'une réclamation par le client depuis l'espace client.
    // Sécurité : le client (identifié par le token, PAS par un id_client posté) ne peut
    // modifier QUE sa propre réclamation, et seulement tant qu'elle n'a pas reçu de réponse.
    public static function updateReclamationApi($data)
    {
        $auth = getToken();
        if (!is_object($auth) || !isset($auth->id)) {
            return json_encode(array("icon" => "error", "message" => "Unauthorized"));
        }
        if (!isset($data['id']) || empty($data['id']) || !isset($data['sujet']) || trim((string) $data['sujet']) === '' || !isset($data['message']) || trim((string) $data['message']) === '') {
            return json_encode(array("icon" => "warning", "message" => "All fields must be filled in"));
        }
        global $db;
        $SQLselect = sprintf("SELECT id_client, reponse FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($data['id'], "int"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) != 1) {
            return json_encode(array("icon" => "warning", "message" => "Request not found"));
        }
        $row = $db->fetch_assoc($result);
        // Appartenance : la réclamation doit être celle du client authentifié.
        if ((int) $row['id_client'] !== (int) $auth->id) {
            return json_encode(array("icon" => "error", "message" => "Unauthorized"));
        }
        // Verrou : une réclamation déjà répondue n'est plus modifiable.
        if (trim((string) $row['reponse']) !== '') {
            return json_encode(array("icon" => "warning", "message" => "This request has already been answered and can no longer be edited"));
        }
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET department = %s, sujet = %s, message = %s WHERE id = %s AND id_client = %s",
            GetSQLValueString(isset($data['department']) ? $data['department'] : null, "text"),
            GetSQLValueString($data['sujet'], "text"),
            GetSQLValueString($data['message'], "text"),
            GetSQLValueString($data['id'], "int"),
            GetSQLValueString($auth->id, "int")
        );
        if (!$db->query($SQLupdate)) {
            return json_encode(array("icon" => "success", "message" => "The request has been successfully updated"));
        }
        return json_encode(array("icon" => "warning", "message" => "The request has not been updated"));
    }

    // Notifie le client par email qu'une réponse a été apportée à sa réclamation.
    // Même config SMTP que le reste du CRM ; en local (SMTP non défini) on trace juste.
    public function sendReponseEmail()
    {
        $client = $this->getClient();
        if (!$client || !$client->getEmail()) {
            return false;
        }
        $espaceUrl = espaceClientLink($_SESSION['agence']);
        $nomClient = trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
        $sujet = "Réponse à votre réclamation : " . $this->getSujet();
        $corps = "Bonjour " . htmlspecialchars($nomClient) . ",<br><br>"
            . "Notre équipe a répondu à votre réclamation <strong>&laquo; " . htmlspecialchars($this->getSujet()) . " &raquo;</strong> :<br><br>"
            . "<div style=\"border-left:3px solid #09A1BE;padding:12px 16px;background:#f4fbfd;color:#222222;border-radius:0 8px 8px 0\">" . nl2br(htmlspecialchars($this->getReponse())) . "</div><br>"
            . ($espaceUrl ? "Retrouvez cette réponse dans votre espace client :<br><a href=\"" . $espaceUrl . "\">" . $espaceUrl . "</a><br><br>" : "")
            . "Cordialement,<br>L'équipe Hello World.";

        // SMTP non configuré (ex. en local) : on trace et on sort proprement.
        if (!defined('SMTP_HOST') || SMTP_HOST == '') {
            error_log('[reclamation reponse] email non envoye (SMTP non configure) -> ' . $client->getEmail() . ' | sujet: ' . $sujet);
            return false;
        }

        require_once __DIR__ . '/../../../vendor/autoload.php';
        try {
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
            $mail->AltBody = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), "\n", $corps));
            $mail->send();
            copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
            return true;
        } catch (\Throwable $e) {
            error_log('[reclamation reponse email] ' . $e->getMessage());
            return false;
        }
    }
}
