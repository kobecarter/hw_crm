<?php

// Jeton d'accès temporaire (24h) à la page "Réseaux sociaux" d'UN client, sans compte CRM -
// généré par un admin, envoyé (lien) au responsable social media par un canal, le CODE de
// sécurité étant communiqué séparément "à titre personnel" (jamais dans le même message que le
// lien). Voir includes/functions/functions.php::socialAccessAllowed() pour le contrôle d'accès
// réel appliqué sur les endpoints (clientsocial/controleur.php).
class clientsocialtoken
{
    static $table = __prefixe_db__ . "client_social_token";

    private $id;
    private $client;
    private $token;
    private $codeHash;
    private $scopeType;
    private $scopePlateforme;
    private $createdBy;
    private $expiresAt;
    private $revoked;
    private $lastUsedAt;
    private $dateAdd;

    public function __construct()
    {
        $this->id = 0;
        $this->revoked = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getCodeHash()
    {
        return $this->codeHash;
    }

    public function getScopeType()
    {
        return $this->scopeType;
    }

    public function getScopePlateforme()
    {
        return $this->scopePlateforme;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function isRevoked()
    {
        return $this->revoked == 1;
    }

    public function getLastUsedAt()
    {
        return $this->lastUsedAt;
    }

    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    public function isExpired()
    {
        return !$this->expiresAt || strtotime($this->expiresAt) < time();
    }

    public function isUsable()
    {
        return $this->id != 0 && !$this->isRevoked() && !$this->isExpired();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setCodeHash($codeHash)
    {
        $this->codeHash = $codeHash;
    }

    public function setScopeType($scopeType)
    {
        $this->scopeType = $scopeType;
    }

    public function setScopePlateforme($scopePlateforme)
    {
        $this->scopePlateforme = $scopePlateforme;
    }

    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function setRevoked($revoked)
    {
        $this->revoked = $revoked;
    }

    public function setLastUsedAt($lastUsedAt)
    {
        $this->lastUsedAt = $lastUsedAt;
    }

    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, token, code_hash, scope_type, scope_plateforme, created_by, expires_at, revoked, date_add) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->token, "text"),
            GetSQLValueString($this->codeHash, "text"),
            GetSQLValueString($this->scopeType, "text"),
            GetSQLValueString($this->scopePlateforme, "text"),
            GetSQLValueString($this->createdBy, "int"),
            GetSQLValueString($this->expiresAt, "text"),
            GetSQLValueString($this->revoked, "int"),
            GetSQLValueString($this->dateAdd, "text")
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
            "UPDATE " . static::$table . " SET last_used_at = %s WHERE id = %s",
            GetSQLValueString(date('Y-m-d H:i:s'), "text"),
            GetSQLValueString($this->id, "int")
        );
        $db->query($SQLupdate);
    }

    public function revoke()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET revoked = 1 WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $t = new clientsocialtoken();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $t = static::build($data);
        }
        return $t;
    }

    public static function findByToken($token)
    {
        global $db;
        $t = new clientsocialtoken();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE token = %s",
            GetSQLValueString($token, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $t = static::build($data);
        }
        return $t;
    }

    public static function findAllByClient($id_client)
    {
        global $db;
        $tokens = array();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_client = %s ORDER BY date_add DESC",
            GetSQLValueString($id_client, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $tokens[] = static::build($data);
        }
        return $tokens;
    }

    public static function build($data)
    {
        $t = new clientsocialtoken();
        $t->setId($data['id']);
        $t->setClient(isset($data['id_client']) ? client::findAny($data['id_client']) : null);
        $t->setToken($data['token']);
        $t->setCodeHash($data['code_hash']);
        $t->setScopeType($data['scope_type']);
        $t->setScopePlateforme($data['scope_plateforme']);
        $t->setCreatedBy($data['created_by']);
        $t->setExpiresAt($data['expires_at']);
        $t->setRevoked($data['revoked']);
        $t->setLastUsedAt($data['last_used_at']);
        $t->setDateAdd($data['date_add']);
        return $t;
    }

    // Génère un jeton (48 caractères hex, dans l'URL) + un code de sécurité à 6 chiffres (jamais
    // stocké en clair, seulement son hash bcrypt) valable 24h. Retourne le token/code EN CLAIR
    // une seule fois ici, à l'instant de la création - impossible de les retrouver ensuite, donc
    // l'appelant doit les afficher/transmettre immédiatement.
    public static function generate($client, $scopeType, $scopePlateforme, $adminUserId)
    {
        $token = bin2hex(random_bytes(24));
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $t = new clientsocialtoken();
        $t->setClient($client);
        $t->setToken($token);
        $t->setCodeHash(password_hash($code, PASSWORD_DEFAULT));
        $t->setScopeType($scopeType);
        $t->setScopePlateforme($scopeType === 'specific' ? $scopePlateforme : null);
        $t->setCreatedBy($adminUserId);
        $t->setExpiresAt(date('Y-m-d H:i:s', strtotime('+24 hours')));
        $t->setRevoked(0);
        $t->setDateAdd(date('Y-m-d H:i:s'));
        $t->add();

        return array('token' => $token, 'code' => $code);
    }

    // Vérifie un couple token+code soumis sur la page d'accès public. Ne révèle jamais si c'est
    // le token ou le code qui est en cause (même message d'erreur générique côté appelant).
    public static function verify($token, $code)
    {
        $t = static::findByToken($token);
        if (!$t->isUsable()) {
            return false;
        }
        if (!password_verify((string) $code, $t->getCodeHash())) {
            return false;
        }
        return $t;
    }
}
