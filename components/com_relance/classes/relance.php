<?php

class relance
{
    static $table =  __prefixe_db__ . "relance";
    static $tableClient =  __prefixe_db__ . "client";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $client;
    private $facture;
    private $type;
    private $id_condition;
    private $etape;
    private $date;
    private $photo;
    private $remarque;
    private $traite;
    private $date_envoi;
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

    public function getClient()
    {
        return $this->client;
    }

    public function getFacture()
    {
        return $this->facture;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getIdCondition()
    {
        return $this->id_condition;
    }

    public function getEtape()
    {
        return $this->etape;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDateEnvoi()
    {
        return $this->date_envoi;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function getTraite()
    {
        return $this->traite;
    }

    public function isTraite()
    {
        return $this->traite == 1 ? true : false;
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

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setFacture($facture)
    {
        $this->facture = $facture;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setIdCondition($id_condition)
    {
        $this->id_condition = $id_condition;
    }

    public function setEtape($etape)
    {
        $this->etape = $etape;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setDateEnvoi($date_envoi)
    {
        $this->date_envoi = $date_envoi;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setRemarque($seo_titre)
    {
        $this->remarque = $seo_titre;
    }

    public function setTraite($traite)
    {
        $this->traite = $traite;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function getDaysLeft()
    {
        $from = new DateTime();
        $to = new DateTime($this->date);

        $interval = $from->diff($to);
        return $interval->format('%r%a');
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, id_facture, id_condition, type, etape, date, remarque, photo, traite, date_envoi, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->id_condition, "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->etape, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->traite, "int"),
            GetSQLValueString($this->date_envoi, "date"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date"),
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_client = %s, id_facture = %s, id_condition = %s, type = %s, etape = %s, date = %s, remarque = %s, photo = %s, traite = %s, date_envoi = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->id_condition, "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->etape, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->traite, "text"),
            GetSQLValueString($this->date_envoi, "date"),
            GetSQLValueString($this->last_edit, "date"),
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

    public static function find($id,$agence = 1)
    {
        global $db;
        $relance = new relance();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id WHERE C.id = %s and A.id = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $relance = static::build($data);
        }
        return $relance;
    }

    public static function findAll($agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where C.id = %s",
        GetSQLValueString($agence, "int"),
    );
        $SQLselect .= " ORDER BY A.date";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }
    
    public static function findAllNonTraite($agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id WHERE C.id = %s AND traite = 0",
        GetSQLValueString($agence, "int"),
    );
        $SQLselect .= " ORDER BY A.date";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }

    public static function findByClient($client_id,$agence=1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A inner join " . static::$tableClient . " B on A.id_client = B.id inner join " . static::$tableAgence . " C on B.id_agence = C.id where C.id = %s and A.id_client = %s",
        GetSQLValueString($agence, "int"),
        GetSQLValueString($client_id, "int")
    );
        $SQLselect .= " ORDER BY date";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }

    public static function findByFacture($facture)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID,A.* FROM " . static::$table . " A WHERE id_facture = %s",
            GetSQLValueString($facture, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $relance = static::build($data);
            array_push($items, $relance);
        }
        return $items;
    }

    public static function build($data)
    {
        $relance = new relance();
        $relance->setId($data['id']);
        $relance->setClient(client::find($data['id_client'],$_SESSION['agence']));
        $relance->setFacture(facture::find($data['id_facture'],$_SESSION['agence']));
        $relance->setType($data['type']);
        $relance->setIdCondition(isset($data['id_condition']) ? $data['id_condition'] : null);
        $relance->setEtape(isset($data['etape']) ? $data['etape'] : null);
        $relance->setDate($data['date']);
        $relance->setRemarque($data['remarque']);
        $relance->setPhoto($data['photo']);
        $relance->setTraite($data['traite']);
        $relance->setDateEnvoi(isset($data['date_envoi']) ? $data['date_envoi'] : null);
        $relance->setDateAdd($data['date_add']);
        $relance->setLastEdit($data['last_edit']);
        return $relance;
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

    public static function countByClient($client_id){
        global $db;
        $SQLcount = sprintf("SELECT count(id) as c FROM " . static::$table . " where id_client = %s",
            GetSQLValueString($client_id, "int")
        );
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    // Relances de paiement automatiques (échéancier) ------------------------------

    public static function existsForCondition($idCondition, $etape)
    {
        global $db;
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table . " WHERE id_condition = %s AND etape = %s LIMIT 1",
            GetSQLValueString($idCondition, "int"),
            GetSQLValueString($etape, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return static::build($data);
        }
        return null;
    }

    public static function findLastRetardByFacture($idFacture)
    {
        global $db;
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_facture = %s AND etape = 'retard' ORDER BY date DESC, id DESC LIMIT 1",
            GetSQLValueString($idFacture, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return static::build($data);
        }
        return null;
    }

    // Point d'entrée appelé quotidiennement par l'endpoint public (pas de cron serveur
    // disponible, voir com_relance/controleurs/router.php task=runDailyReminders). Idempotent :
    // peut être rappelé plusieurs fois le même jour sans double-envoi (chaque relance garde la
    // date de son dernier envoi réel dans date_envoi).
    public static function runDailyReminders()
    {
        date_default_timezone_set("Africa/Casablanca");
        $today = date('Y-m-d');
        $summary = array('created' => 0, 'sent' => 0, 'errors' => 0);

        $userId = defined('RELANCE_CRON_ACTING_USER_ID') ? RELANCE_CRON_ACTING_USER_ID : 5;
        $agences = agence::findAll('fr');

        foreach ($agences as $agence) {
            try {
                bootstrapSystemSession($userId, $agence->getId(), 'fr');
            } catch (\Throwable $e) {
                error_log('relance::runDailyReminders - bootstrap agence ' . $agence->getId() . ' - ' . $e->getMessage());
                $summary['errors']++;
                continue;
            }

            $factures = facture::findAll(false, false, false, false, false, false, $agence->getId());
            foreach ($factures as $facture) {
                try {
                    if ($facture->getReste() <= 0) {
                        continue;
                    }
                    $devis = $facture->getDevis();
                    if (!$devis || !$devis->getId()) {
                        continue;
                    }
                    self::processFactureReminders($facture, $devis, $today, $summary);
                } catch (\Throwable $e) {
                    error_log('relance::runDailyReminders - facture ' . $facture->getId() . ' - ' . $e->getMessage());
                    $summary['errors']++;
                }
            }
        }

        return $summary;
    }

    private static function processFactureReminders($facture, $devis, $today, &$summary)
    {
        $etapesOffsets = array('J-15' => -15, 'J-10' => -10, 'J-5' => -5, 'J0' => 0);
        $derniereEcheance = null;

        foreach ($devis->getConditions() as $condition) {
            $dueDate = $condition->getDate();
            if (!$dueDate) {
                continue;
            }
            if ($derniereEcheance === null || $dueDate > $derniereEcheance) {
                $derniereEcheance = $dueDate;
            }
            foreach ($etapesOffsets as $etape => $offsetJours) {
                $dateRelance = date('Y-m-d', strtotime($dueDate . ' ' . $offsetJours . ' days'));
                if ($dateRelance > $today) {
                    continue; // échéance pas encore atteinte
                }
                $relance = self::existsForCondition($condition->getId(), $etape);
                if (!$relance) {
                    $relance = self::createReminder($facture, $condition->getId(), $etape, $dateRelance);
                    $summary['created']++;
                }
                self::sendIfNotAlreadySentToday($relance, $facture, $condition, $etape, $today, $summary);
            }
        }

        // Retard : au-delà de la dernière échéance, tant que la facture reste impayée -
        // une nouvelle relance "retard" toutes les 5 jours (basé sur la dernière réellement
        // envoyée, pas sur un modulo, pour rester auto-résilient si le déclencheur externe
        // rate un jour).
        if ($derniereEcheance !== null && $today > $derniereEcheance) {
            $dernierRetard = self::findLastRetardByFacture($facture->getId());
            if (!$dernierRetard) {
                $relance = self::createReminder($facture, null, 'retard', $today);
                $summary['created']++;
                self::sendIfNotAlreadySentToday($relance, $facture, null, 'retard', $today, $summary);
            } else {
                $seuil = date('Y-m-d', strtotime(substr($dernierRetard->getDate(), 0, 10) . ' +5 days'));
                if ($seuil <= $today && substr($dernierRetard->getDate(), 0, 10) != $today) {
                    $relance = self::createReminder($facture, null, 'retard', $today);
                    $summary['created']++;
                    self::sendIfNotAlreadySentToday($relance, $facture, null, 'retard', $today, $summary);
                } else {
                    // pas encore le moment d'un nouveau retard : on retente juste l'envoi du
                    // dernier si, pour une raison quelconque (crash/timeout), il n'était pas parti
                    self::sendIfNotAlreadySentToday($dernierRetard, $facture, null, 'retard', $today, $summary);
                }
            }
        }
    }

    private static function createReminder($facture, $idCondition, $etape, $dateRelance)
    {
        $relance = new relance();
        $relance->setClient($facture->getClient());
        $relance->setFacture($facture);
        $relance->setType('Email');
        $relance->setIdCondition($idCondition);
        $relance->setEtape($etape);
        $relance->setDate($dateRelance);
        $relance->setTraite(0);
        $relance->setDateAdd(date('Y-m-d'));
        $relance->setLastEdit(date('Y-m-d'));
        $relance->add();
        $relance->setId(self::getLastId());
        return $relance;
    }

    private static function sendIfNotAlreadySentToday($relance, $facture, $condition, $etape, $today, &$summary)
    {
        if ($relance->getDateEnvoi() && substr($relance->getDateEnvoi(), 0, 10) === $today) {
            return; // déjà envoyée aujourd'hui, idempotence : on ne refait rien
        }
        try {
            self::sendReminderEmail($facture, $condition, $etape);
            $relance->setDateEnvoi(date('Y-m-d H:i:s'));
            $relance->edit();
            $summary['sent']++;
        } catch (\Throwable $e) {
            error_log('relance::sendIfNotAlreadySentToday - relance ' . $relance->getId() . ' - ' . $e->getMessage());
            $summary['errors']++;
        }
    }

    private static function sendReminderEmail($facture, $condition, $etape)
    {
        if (!defined('SMTP_HOST') || SMTP_HOST == '') {
            return;
        }
        $client = $facture->getClient();
        if (!$client->getEmail()) {
            return;
        }

        require_once __DIR__ . '/../../../vendor/autoload.php';

        $isEn = ($facture->getLangue() == 'en');
        $montant = number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise();

        if ($etape === 'retard') {
            $joursRetard = (int) ((strtotime(date('Y-m-d')) - strtotime($condition ? $condition->getDate() : $facture->getDateFacture())) / 86400);
            $sujet = $isEn ? "Payment overdue - Invoice N°" . $facture->getNumero() : "Retard de paiement - Facture N°" . $facture->getNumero();
            $corps = $isEn
                ? "Hello,<br><br>Invoice N°" . $facture->getNumero() . " (" . $montant . " remaining) is now overdue. Please regularize this payment as soon as possible.<br><br>Thank you."
                : "Bonjour,<br><br>La facture N°" . $facture->getNumero() . " (" . $montant . " restant) est désormais en retard de paiement. Merci de bien vouloir régulariser ce règlement dans les meilleurs délais.<br><br>Cordialement.";
        } elseif ($etape === 'J0') {
            $sujet = $isEn ? "Payment due today - Invoice N°" . $facture->getNumero() : "Échéance de paiement aujourd'hui - Facture N°" . $facture->getNumero();
            $corps = $isEn
                ? "Hello,<br><br>This is a reminder that the payment of " . $montant . " for invoice N°" . $facture->getNumero() . " is due today.<br><br>Thank you."
                : "Bonjour,<br><br>Nous vous rappelons que le règlement de " . $montant . " pour la facture N°" . $facture->getNumero() . " arrive à échéance aujourd'hui.<br><br>Cordialement.";
        } else {
            $joursRestants = array('J-15' => 15, 'J-10' => 10, 'J-5' => 5)[$etape];
            $sujet = $isEn ? "Upcoming payment - Invoice N°" . $facture->getNumero() : "Échéance à venir - Facture N°" . $facture->getNumero();
            $corps = $isEn
                ? "Hello,<br><br>This is a reminder that the payment of " . $montant . " for invoice N°" . $facture->getNumero() . " is due in " . $joursRestants . " days.<br><br>Thank you."
                : "Bonjour,<br><br>Nous vous rappelons que le règlement de " . $montant . " pour la facture N°" . $facture->getNumero() . " arrive à échéance dans " . $joursRestants . " jours.<br><br>Cordialement.";
        }

        $mailCreds = getMailCredentialsForAgence($client->getAgence() ? $client->getAgence()->getId() : 0);

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $mailCreds['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailCreds['username'];
        $mail->Password = $mailCreds['password'];
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mailCreds['port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mailCreds['username'], 'Hello World');
        $mail->addAddress($client->getEmail(), trim($client->getPrenom() . ' ' . $client->getNom()));
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $corps;
        $mail->AltBody = strip_tags($corps);
        $mail->send();
        copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage(), $mailCreds['host'], $mailCreds['username'], $mailCreds['password']);
    }
}
