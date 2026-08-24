<?php

// Attestation de référence : un document officiel (PDF ou Word) que l'agence
// reçoit du client — ou prépare pour lui — attestant qu'ils ont travaillé
// ensemble (ex. lettre de référence signée par une administration/entreprise
// partenaire). L'agence dépose ce document depuis la fiche CRM du client ; le
// client le "signe" dans son espace (nom tapé + confirmation, horodaté + IP —
// c'est le "système de signature en ligne" : pas une signature manuscrite),
// ce qui débloque le téléchargement du document original et des points de
// fidélité (voir CL_POINTS_ATTESTATION côté site).
class attestation
{
    static $table = __prefixe_db__ . "attestation";
    static $tableClient = __prefixe_db__ . "client";
    static $tableAgence = __prefixe_db__ . "agence";
    static $uploadDir = "attestations"; // sous-dossier de uploads/

    const STATU_EN_ATTENTE = 0;
    const STATU_SIGNEE = 1;

    private $id;
    private $id_client;
    private $id_agence;
    private $titre;
    private $message;
    private $fichier;
    private $statu;
    private $signature_nom;
    private $signature_date;
    private $signature_ip;
    private $download_date;
    private $download_ip;
    private $id_user_added;
    private $date_add;

    public function __construct()
    {
        $this->id = 0;
        $this->statu = self::STATU_EN_ATTENTE;
    }

    public function getId() { return $this->id; }
    public function getIdClient() { return $this->id_client; }
    public function getTitre() { return $this->titre; }
    public function getMessage() { return $this->message; }
    public function getFichier() { return $this->fichier; }
    public function getStatu() { return $this->statu; }
    public function getSignatureNom() { return $this->signature_nom; }
    public function getSignatureDate() { return $this->signature_date; }
    public function getDownloadDate() { return $this->download_date; }
    public function getDateAdd() { return $this->date_add; }

    public function setIdClient($v) { $this->id_client = $v; return $this; }
    public function setIdAgence($v) { $this->id_agence = $v; return $this; }
    public function setTitre($v) { $this->titre = $v; return $this; }
    public function setMessage($v) { $this->message = $v; return $this; }
    public function setFichier($v) { $this->fichier = $v; return $this; }
    public function setIdUserAdded($v) { $this->id_user_added = $v; return $this; }

    private static function hydrate($data)
    {
        $a = new attestation();
        $a->id = (int) $data['id'];
        $a->id_client = (int) $data['id_client'];
        $a->id_agence = (int) $data['id_agence'];
        $a->titre = $data['titre'];
        $a->message = $data['message'];
        $a->fichier = $data['fichier'];
        $a->statu = (int) $data['statu'];
        $a->signature_nom = $data['signature_nom'];
        $a->signature_date = $data['signature_date'];
        $a->signature_ip = $data['signature_ip'];
        $a->download_date = isset($data['download_date']) ? $data['download_date'] : null;
        $a->download_ip = isset($data['download_ip']) ? $data['download_ip'] : null;
        $a->id_user_added = $data['id_user_added'];
        $a->date_add = $data['date_add'];
        return $a;
    }

    // ---- Côté admin (session CRM) ----

    public static function findByClient($id_client)
    {
        global $db;
        $items = array();
        $rows = $db->queryS(sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_client = %s ORDER BY date_add DESC",
            GetSQLValueString($id_client, "int")
        ));
        if (is_array($rows)) {
            foreach ($rows as $row) { $items[] = self::hydrate($row); }
        }
        return $items;
    }

    // Supprime une demande d'attestation (accès contrôlé par hasDroit('delete',
    // 'com_client') côté contrôleur) : la ligne en base, et le document déposé
    // sur le disque s'il existe encore.
    public static function deleteApi($id)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT fichier FROM " . static::$table . " WHERE id = %s LIMIT 1",
            GetSQLValueString((int) $id, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) {
            return false;
        }
        $fichier = $rows[0]['fichier'];
        $db->query(sprintf("DELETE FROM " . static::$table . " WHERE id = %s", GetSQLValueString((int) $id, "int")));
        if (!empty($fichier)) {
            $path = '../../../uploads/' . static::$uploadDir . '/' . $fichier;
            if (file_exists($path)) { @unlink($path); }
        }
        return true;
    }

    // Dépose le fichier (PDF/DOC/DOCX) envoyé depuis le formulaire admin dans
    // uploads/attestations/, avec un nom unique. Retourne le nom de fichier
    // stocké, ou null si aucun fichier valide n'a été envoyé.
    public static function handleUpload($fileField = 'fichier')
    {
        if (!isset($_FILES[$fileField]) || empty($_FILES[$fileField]['name'])) {
            return null;
        }
        $allowed = array('pdf', 'doc', 'docx');
        $name = $_FILES[$fileField]['name'];
        $ext = strtolower(substr($name, strrpos($name, '.') + 1));
        if (!in_array($ext, $allowed)) {
            return null;
        }
        $dir = '../../../uploads/' . static::$uploadDir . '/';
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        $base = preg_replace('/[^a-z0-9_-]/i', '_', pathinfo($name, PATHINFO_FILENAME));
        $base = strtolower($base) . '-' . time() . '-' . substr(md5(uniqid('', true)), 0, 6);
        $stored = $base . '.' . $ext;
        if (@move_uploaded_file($_FILES[$fileField]['tmp_name'], $dir . $stored)) {
            @chmod($dir . $stored, 0644);
            return $stored;
        }
        return null;
    }

    public function add()
    {
        global $db;
        $now = date("Y-m-d H:i:s");
        $db->query(sprintf(
            "INSERT INTO " . static::$table . " (id_client, id_agence, titre, message, fichier, statu, id_user_added, date_add) VALUES (%s, %s, %s, %s, %s, 0, %s, %s)",
            GetSQLValueString($this->id_client, "int"),
            GetSQLValueString($this->id_agence, "int"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->message, "text"),
            GetSQLValueString($this->fichier, "text"),
            GetSQLValueString($this->id_user_added, "int"),
            GetSQLValueString($now, "text")
        ));
        return $db->last_id();
    }

    // ---- Côté client (API à jeton) ----

    public static function findAllByClientApi($id_client)
    {
        if (!getToken()) return array();
        global $db;
        $items = array();
        $rows = $db->queryS(sprintf(
            "SELECT A.* FROM " . static::$table . " A INNER JOIN " . static::$tableClient . " B ON B.id = A.id_client WHERE A.id_client = %s ORDER BY A.date_add DESC",
            GetSQLValueString((int) $id_client, "int")
        ));
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $a = self::hydrate($row);
                $items[] = array(
                    'id' => $a->id,
                    'titre' => $a->titre,
                    'message' => $a->message,
                    'has_fichier' => !empty($a->fichier),
                    'statu' => $a->statu,
                    'signature_nom' => $a->signature_nom,
                    'signature_date' => $a->signature_date,
                    'download_date' => $a->download_date,
                    'date_add' => $a->date_add,
                );
            }
        }
        return $items;
    }

    // Nombre d'attestations en attente (pour la pastille de la cloche de
    // notification côté site).
    public static function countPendingByClientApi($id_client)
    {
        if (!getToken()) return 0;
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT COUNT(*) AS n FROM " . static::$table . " WHERE id_client = %s AND statu = 0",
            GetSQLValueString((int) $id_client, "int")
        ));
        return (is_array($rows) && count($rows) > 0) ? (int) $rows[0]['n'] : 0;
    }

    // Le client "signe" : id + nom saisi. On vérifie que l'attestation
    // appartient bien au client du jeton (pas d'IDOR) et qu'elle est encore
    // en attente (pas de re-signature).
    public static function signApi($id_client, $id_attestation, $signatureNom)
    {
        if (!getToken()) {
            return array("icon" => "error", "message" => "Unauthorized");
        }
        global $db;
        $signatureNom = trim((string) $signatureNom);
        if ($signatureNom === '') {
            return array("icon" => "warning", "message" => "Nom manquant", "code" => "missing");
        }
        $rows = $db->queryS(sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s AND id_client = %s LIMIT 1",
            GetSQLValueString((int) $id_attestation, "int"), GetSQLValueString((int) $id_client, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) {
            return array("icon" => "error", "message" => "Not found", "code" => "notfound");
        }
        $a = self::hydrate($rows[0]);
        if ($a->statu == self::STATU_SIGNEE) {
            return array("icon" => "warning", "message" => "Déjà signée", "code" => "already");
        }
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $now = date("Y-m-d H:i:s");
        $db->query(sprintf(
            "UPDATE " . static::$table . " SET statu = 1, signature_nom = %s, signature_date = %s, signature_ip = %s WHERE id = %s",
            GetSQLValueString($signatureNom, "text"), GetSQLValueString($now, "text"),
            GetSQLValueString(trim((string) $ip), "text"), GetSQLValueString($a->id, "int")
        ));
        return array("icon" => "success", "message" => "Attestation signée", "id" => $a->id);
    }

    // Document original (PDF/Word déposé par l'agence), renvoyé en base64.
    // Le proxy site le redécode et le stream directement au navigateur (pas
    // de dossier uploads/ partagé entre les deux applications). On ne
    // régénère rien : c'est le fichier tel que déposé, la signature reste une
    // métadonnée à côté (nom + date), pas un tampon apposé sur le document.
    // Le téléchargement est possible que l'attestation soit signée ou non
    // (le client peut vouloir le lire avant de signer, ou choisir de le
    // télécharger et le renvoyer signé par un autre moyen) : le premier
    // téléchargement est daté ("download_date") et déclenche aussi des
    // points côté site (même logique que la signature, une seule fois).
    public static function pdfApi($id_client, $id_attestation)
    {
        if (!getToken()) {
            return array("icon" => "error", "message" => "Unauthorized");
        }
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s AND id_client = %s LIMIT 1",
            GetSQLValueString((int) $id_attestation, "int"), GetSQLValueString((int) $id_client, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) {
            return array("icon" => "error", "message" => "Not found");
        }
        $a = self::hydrate($rows[0]);
        if (empty($a->fichier)) {
            return array("icon" => "error", "message" => "No file");
        }
        $path = '../../../uploads/' . static::$uploadDir . '/' . $a->fichier;
        if (!file_exists($path)) {
            return array("icon" => "error", "message" => "File missing on server");
        }
        $firstDownload = empty($a->download_date);
        if ($firstDownload) {
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
            $db->query(sprintf(
                "UPDATE " . static::$table . " SET download_date = %s, download_ip = %s WHERE id = %s",
                GetSQLValueString(date("Y-m-d H:i:s"), "text"), GetSQLValueString(trim((string) $ip), "text"),
                GetSQLValueString($a->id, "int")
            ));
        }
        $ext = strtolower(pathinfo($a->fichier, PATHINFO_EXTENSION));
        $mime = $ext === 'pdf' ? 'application/pdf' : ($ext === 'docx' ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' : 'application/msword');
        $bytes = file_get_contents($path);
        return array(
            "icon" => "success",
            "first_download" => $firstDownload,
            "filename" => 'attestation-' . $a->id . '.' . $ext,
            "mime" => $mime,
            "pdf_base64" => base64_encode($bytes),
        );
    }

    // ---- Côté client (API REST mobile, JWT via AuthController::middlware) ----
    // Mêmes requêtes que les méthodes *Api() ci-dessus, mais sans le garde
    // getToken() (mécanisme de jeton legacy incompatible avec le JWT de
    // l'API REST) - l'authentification est déjà vérifiée en amont par le
    // contrôleur Leaf. Ne modifie aucune méthode existante, pour ne pas
    // risquer de casser un appelant legacy encore actif.

    public static function findAllByClientRest($id_client)
    {
        return self::findAllByClientApiInternal($id_client);
    }

    private static function findAllByClientApiInternal($id_client)
    {
        global $db;
        $items = array();
        $rows = $db->queryS(sprintf(
            "SELECT A.* FROM " . static::$table . " A INNER JOIN " . static::$tableClient . " B ON B.id = A.id_client WHERE A.id_client = %s ORDER BY A.date_add DESC",
            GetSQLValueString((int) $id_client, "int")
        ));
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $a = self::hydrate($row);
                $items[] = array(
                    'id' => $a->id,
                    'titre' => $a->titre,
                    'message' => $a->message,
                    'has_fichier' => !empty($a->fichier),
                    'statu' => $a->statu,
                    'signature_nom' => $a->signature_nom,
                    'signature_date' => $a->signature_date,
                    'download_date' => $a->download_date,
                    'date_add' => $a->date_add,
                );
            }
        }
        return $items;
    }

    public static function signRest($id_client, $id_attestation, $signatureNom)
    {
        global $db;
        $signatureNom = trim((string) $signatureNom);
        if ($signatureNom === '') {
            return array("success" => false, "message" => "Nom manquant", "code" => "missing");
        }
        $rows = $db->queryS(sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s AND id_client = %s LIMIT 1",
            GetSQLValueString((int) $id_attestation, "int"), GetSQLValueString((int) $id_client, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) {
            return array("success" => false, "message" => "Attestation introuvable", "code" => "notfound");
        }
        $a = self::hydrate($rows[0]);
        if ($a->statu == self::STATU_SIGNEE) {
            return array("success" => false, "message" => "Déjà signée", "code" => "already");
        }
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $now = date("Y-m-d H:i:s");
        $db->query(sprintf(
            "UPDATE " . static::$table . " SET statu = 1, signature_nom = %s, signature_date = %s, signature_ip = %s WHERE id = %s",
            GetSQLValueString($signatureNom, "text"), GetSQLValueString($now, "text"),
            GetSQLValueString(trim((string) $ip), "text"), GetSQLValueString($a->id, "int")
        ));
        return array("success" => true, "message" => "Attestation signée", "id" => $a->id);
    }

    // Renvoie le document en base64 (mêmes règles que pdfApi : téléchargeable
    // signée ou non, le premier téléchargement est daté).
    public static function pdfRest($id_client, $id_attestation)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s AND id_client = %s LIMIT 1",
            GetSQLValueString((int) $id_attestation, "int"), GetSQLValueString((int) $id_client, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) {
            return array("success" => false, "message" => "Attestation introuvable");
        }
        $a = self::hydrate($rows[0]);
        if (empty($a->fichier)) {
            return array("success" => false, "message" => "Aucun fichier");
        }
        $path = __DIR__ . '/../../../uploads/' . static::$uploadDir . '/' . $a->fichier;
        if (!file_exists($path)) {
            return array("success" => false, "message" => "Fichier introuvable sur le serveur");
        }
        $firstDownload = empty($a->download_date);
        if ($firstDownload) {
            $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
            $db->query(sprintf(
                "UPDATE " . static::$table . " SET download_date = %s, download_ip = %s WHERE id = %s",
                GetSQLValueString(date("Y-m-d H:i:s"), "text"), GetSQLValueString(trim((string) $ip), "text"),
                GetSQLValueString($a->id, "int")
            ));
        }
        $ext = strtolower(pathinfo($a->fichier, PATHINFO_EXTENSION));
        $mime = $ext === 'pdf' ? 'application/pdf' : ($ext === 'docx' ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' : 'application/msword');
        $bytes = file_get_contents($path);
        return array(
            "success" => true,
            "first_download" => $firstDownload,
            "filename" => 'attestation-' . $a->id . '.' . $ext,
            "mime" => $mime,
            "pdf_base64" => base64_encode($bytes),
        );
    }

    // Consultation du document par l'agence (fiche CRM du client) : contrôle
    // d'accès délégué à hasDroit('view','com_client') côté contrôleur — pas
    // besoin que l'attestation soit signée, l'agence doit pouvoir vérifier ce
    // qu'elle a envoyé à tout moment.
    public static function streamFileAdmin($id_attestation)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s LIMIT 1",
            GetSQLValueString((int) $id_attestation, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) { return false; }
        $a = self::hydrate($rows[0]);
        if (empty($a->fichier)) { return false; }
        $path = '../../../uploads/' . static::$uploadDir . '/' . $a->fichier;
        if (!file_exists($path)) { return false; }
        $ext = strtolower(pathinfo($a->fichier, PATHINFO_EXTENSION));
        $mime = $ext === 'pdf' ? 'application/pdf' : ($ext === 'docx' ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' : 'application/msword');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="attestation-' . $a->id . '.' . $ext . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        return true;
    }
}
