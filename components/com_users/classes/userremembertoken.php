<?php

// "Se rappeler de moi" (com_login) : cookie longue durée (30 jours) séparé de la session PHP
// classique (qui reste expirée à la fermeture du navigateur, config.php ne définit aucune durée
// de cookie de session) - motif "sélecteur/validateur" (Barry Jaspan, repris par Laravel/Symfony)
// plutôt qu'un simple id_user en cookie : le sélecteur sert de clé de recherche RAPIDE (index,
// comparaison directe sans fuite de timing possible), le validateur n'est jamais comparé
// directement - seul son hash sha256 est stocké, la comparaison utilise hash_equals() (résistant
// aux attaques temporelles). Un cookie volé seul dans la base ne suffit donc jamais à se
// reconnecter (il faudrait aussi le validateur en clair, qui ne quitte jamais le navigateur de
// l'utilisateur).
class userremembertoken
{
    static $table = __prefixe_db__ . "user_remember_token";

    const EXPIRE_DURATION_SEC = 2592000; // 30 jours
    const COOKIE_NAME = "hw_remember";

    private $id;
    private $idUser;
    private $selector;
    private $validatorHash;
    private $expiresAt;
    private $dateAdd;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdUser()
    {
        return $this->idUser;
    }

    public function getSelector()
    {
        return $this->selector;
    }

    public function getValidatorHash()
    {
        return $this->validatorHash;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    public function setSelector($selector)
    {
        $this->selector = $selector;
    }

    public function setValidatorHash($validatorHash)
    {
        $this->validatorHash = $validatorHash;
    }

    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
    }

    public function isExpired()
    {
        return !$this->expiresAt || strtotime($this->expiresAt) < time();
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_user, selector, validator_hash, expires_at, date_add) VALUES (%s, %s, %s, %s, %s)",
            GetSQLValueString($this->idUser, "int"),
            GetSQLValueString($this->selector, "text"),
            GetSQLValueString($this->validatorHash, "text"),
            GetSQLValueString($this->expiresAt, "date"),
            GetSQLValueString($this->dateAdd, "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s", GetSQLValueString($this->id, "int"));
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function deleteBySelector($selector)
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE selector = %s", GetSQLValueString($selector, "text"));
        $db->query($SQLdelete);
    }

    public static function build($data)
    {
        $t = new userremembertoken();
        $t->setId($data['id']);
        $t->setIdUser($data['id_user']);
        $t->setSelector($data['selector']);
        $t->setValidatorHash($data['validator_hash']);
        $t->setExpiresAt($data['expires_at']);
        $t->setDateAdd($data['date_add']);
        return $t;
    }

    public static function findBySelector($selector)
    {
        global $db;
        $t = new userremembertoken();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE selector = %s LIMIT 1", GetSQLValueString($selector, "text"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $t = static::build($data);
        }
        return $t;
    }

    // Émet un nouveau jeton pour cet utilisateur et retourne la paire EN CLAIR (sélecteur +
    // validateur) à assembler en valeur de cookie ("selector:validator") - le validateur en clair
    // n'est jamais conservé au-delà de cet appel.
    public static function generate($idUser)
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(32));

        $t = new userremembertoken();
        $t->setIdUser($idUser);
        $t->setSelector($selector);
        $t->setValidatorHash(hash('sha256', $validator));
        $t->setExpiresAt(date('Y-m-d H:i:s', time() + self::EXPIRE_DURATION_SEC));
        $t->setDateAdd(date('Y-m-d H:i:s'));
        $t->add();

        return array('selector' => $selector, 'validator' => $validator);
    }

    // Vérifie une paire sélecteur/validateur reçue du cookie. hash_equals() (pas ==/===) pour la
    // comparaison du validateur - résiste aux attaques par mesure de temps. Retourne l'id_user si
    // valide, sinon null (jeton introuvable, expiré, ou validateur incorrect - jamais distingué,
    // même traitement dans les trois cas : la session ne redémarre pas silencieusement).
    public static function verify($selector, $validator)
    {
        $t = static::findBySelector($selector);
        if ($t->getId() == 0 || $t->isExpired()) {
            return null;
        }
        if (!hash_equals($t->getValidatorHash(), hash('sha256', (string) $validator))) {
            return null;
        }
        return $t->getIdUser();
    }
}
