<?php

// Jeton "mot de passe oublié" pour crm_users (le personnel CRM - com_login) : un token haute
// entropie (32 octets aléatoires) envoyé UNE SEULE FOIS par email, jamais stocké en clair - seul
// son hash sha256 (déterministe, donc indexable par une recherche directe) vit en base. Valable 1h,
// usage unique (colonne "used"). Même esprit sécurité que clientsocialtoken.php (com_client), mais
// volontairement plus simple : un lien email n'a besoin que d'UN secret haute entropie, pas d'un
// second facteur "code à 6 chiffres" séparé (utile là-bas car communiqué oralement, pas ici).
class userpasswordreset
{
    static $table = __prefixe_db__ . "user_password_reset";

    const EXPIRE_DURATION_SEC = 3600;

    private $id;
    private $idUser;
    private $tokenHash;
    private $expiresAt;
    private $used;
    private $dateAdd;

    public function __construct()
    {
        $this->id = 0;
        $this->used = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function getTokenHash()
    {
        return $this->tokenHash;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function isUsed()
    {
        return $this->used == 1;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function setTokenHash($tokenHash)
    {
        $this->tokenHash = $tokenHash;
    }

    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function setUsed($used)
    {
        $this->used = $used;
    }

    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
    }

    public function isExpired()
    {
        return !$this->expiresAt || strtotime($this->expiresAt) < time();
    }

    public function isUsable()
    {
        return $this->id != 0 && !$this->isUsed() && !$this->isExpired();
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_user, token_hash, expires_at, used, date_add) VALUES (%s, %s, %s, %s, %s)",
            GetSQLValueString($this->idUser, "int"),
            GetSQLValueString($this->tokenHash, "text"),
            GetSQLValueString($this->expiresAt, "date"),
            GetSQLValueString($this->used, "int"),
            GetSQLValueString($this->dateAdd, "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function markUsed()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET used = 1 WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function build($data)
    {
        $r = new userpasswordreset();
        $r->setId($data['id']);
        $r->setIdUser($data['id_user']);
        $r->setTokenHash($data['token_hash']);
        $r->setExpiresAt($data['expires_at']);
        $r->setUsed($data['used']);
        $r->setDateAdd($data['date_add']);
        return $r;
    }

    // Émet un jeton pour cet utilisateur et retourne le token EN CLAIR (jamais récupérable
    // ensuite) - à insérer immédiatement dans le lien envoyé par email.
    public static function generate($idUser)
    {
        global $db;
        $token = bin2hex(random_bytes(32));

        $r = new userpasswordreset();
        $r->setIdUser($idUser);
        $r->setTokenHash(hash('sha256', $token));
        $r->setExpiresAt(date('Y-m-d H:i:s', time() + self::EXPIRE_DURATION_SEC));
        $r->setUsed(0);
        $r->setDateAdd(date('Y-m-d H:i:s'));
        $r->add();
        unset($db);

        return $token;
    }

    // Retrouve le jeton EN COURS DE VALIDITÉ correspondant au token en clair reçu dans l'URL -
    // ne révèle jamais si c'est le token qui est introuvable, expiré ou déjà utilisé : l'appelant
    // affiche toujours le même message générique dans les trois cas (isUsable() == false).
    public static function findValidByToken($token)
    {
        global $db;
        $r = new userpasswordreset();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE token_hash = %s ORDER BY id DESC LIMIT 1",
            GetSQLValueString(hash('sha256', (string) $token), "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $r = static::build($data);
        }
        return $r;
    }
}
