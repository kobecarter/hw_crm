<?php

class envoiEmail
{
    static $table = __prefixe_db__ . "envoi_email";

    private $id;
    private $id_agence;
    private $id_user;
    private $entite;
    private $id_entite;
    private $numero;
    private $id_client;
    private $destinataire;
    private $type_envoi;
    private $statut;
    private $erreur;
    private $date_envoi;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdAgence()
    {
        return $this->id_agence;
    }

    public function getIdUser()
    {
        return $this->id_user;
    }

    public function getEntite()
    {
        return $this->entite;
    }

    public function getIdEntite()
    {
        return $this->id_entite;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getDestinataire()
    {
        return $this->destinataire;
    }

    public function getTypeEnvoi()
    {
        return $this->type_envoi;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getErreur()
    {
        return $this->erreur;
    }

    public function getDateEnvoi()
    {
        return $this->date_envoi;
    }

    public static function add($entite, $idEntite, $numero, $idClient, $destinataire, $typeEnvoi, $statut, $erreur = null)
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_agence, id_user, entite, id_entite, numero, id_client, destinataire, type_envoi, statut, erreur, date_envoi) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString(isset($_SESSION['agence']) ? $_SESSION['agence'] : null, "int"),
            GetSQLValueString(isset($_SESSION['user']) ? $_SESSION['user']->getId() : null, "int"),
            GetSQLValueString($entite, "text"),
            GetSQLValueString($idEntite, "int"),
            GetSQLValueString($numero, "text"),
            GetSQLValueString($idClient, "int"),
            GetSQLValueString($destinataire, "text"),
            GetSQLValueString($typeEnvoi, "text"),
            GetSQLValueString($statut, "text"),
            GetSQLValueString($erreur, "text"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 2;
        }
    }

    // Dernier envoi connu pour UN document (fiche détail) - pas de limite de volume ici,
    // ORDER BY + accès au premier élément suffit.
    public static function findByEntite($entite, $idEntite)
    {
        global $db;
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE entite = %s AND id_entite = %s ORDER BY date_envoi DESC",
            GetSQLValueString($entite, "text"),
            GetSQLValueString($idEntite, "int")
        );
        $rows = $db->queryS($SQLselect);
        return ($rows && count($rows)) ? self::build($rows[0]) : null;
    }

    // Dernier envoi connu pour PLUSIEURS documents d'un coup (page de liste) - une seule requête
    // au lieu d'une par ligne, même logique de regroupement que releveLot::compterLotsParTva()
    // (com_accounting). $ids vide ne doit jamais produire un "WHERE id_entite IN ()" invalide.
    public static function findLastByEntiteBatch($entite, array $ids)
    {
        $derniers = array();
        if (empty($ids)) {
            return $derniers;
        }
        global $db;
        $idsSql = implode(',', array_map('intval', $ids));
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE entite = %s AND id_entite IN (" . $idsSql . ") ORDER BY date_envoi DESC",
            GetSQLValueString($entite, "text")
        );
        $rows = $db->queryS($SQLselect);
        if ($rows) {
            foreach ($rows as $row) {
                // ORDER BY date_envoi DESC : le premier rencontré pour un id_entite donné est
                // forcément le plus récent, donc on ignore silencieusement les suivants pour ce même id.
                if (!isset($derniers[$row['id_entite']])) {
                    $derniers[$row['id_entite']] = self::build($row);
                }
            }
        }
        return $derniers;
    }

    public static function findAll($agence = null)
    {
        global $db;
        $SQLselect = "SELECT * FROM " . static::$table;
        if ($agence !== null) {
            $SQLselect .= " WHERE id_agence = " . intval($agence);
        }
        $SQLselect .= " ORDER BY date_envoi DESC";
        $rows = $db->queryS($SQLselect);
        $envois = array();
        if ($rows) {
            foreach ($rows as $row) {
                array_push($envois, self::build($row));
            }
        }
        return $envois;
    }

    public static function build($data)
    {
        $envoi = new envoiEmail();
        $envoi->id = $data['id'];
        $envoi->id_agence = $data['id_agence'];
        $envoi->id_user = $data['id_user'];
        $envoi->entite = $data['entite'];
        $envoi->id_entite = $data['id_entite'];
        $envoi->numero = $data['numero'];
        $envoi->id_client = $data['id_client'];
        $envoi->destinataire = $data['destinataire'];
        $envoi->type_envoi = $data['type_envoi'];
        $envoi->statut = $data['statut'];
        $envoi->erreur = $data['erreur'];
        $envoi->date_envoi = $data['date_envoi'];
        return $envoi;
    }
}
