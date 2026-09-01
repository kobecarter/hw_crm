<?php

// Utilisateurs CRM autorisés (crm_users.id) pour ce module entier - ce CRM n'a que des droits par
// rôle (crm_droits/id_profil), pas par utilisateur individuel, et Hamid/Zakaria partagent le rôle
// Administrateur avec tous les autres admins. Cette liste, vérifiée en plus de hasDroit() dans
// index.php/controleurs/router.php (et dans le menu latéral), est le seul moyen de restreindre ce
// module à exactement ces deux personnes sans toucher au système de droits partagé.
define("CAISSENOIRE_USERS_AUTORISES", array(5, 6));

class caissenoire
{
    static $table = __prefixe_db__ . "caissenoire";

    private $id;
    private $utilisateur;
    private $titre;
    private $description;
    private $montant;
    private $date_charge;
    private $refunded;
    private $date_remboursement;
    private $justificatif;
    private $remarque;
    private $user_added;
    private $user_edited;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public static function estUtilisateurAutorise($idUtilisateur)
    {
        return in_array((int) $idUtilisateur, CAISSENOIRE_USERS_AUTORISES, true);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getMontant()
    {
        return $this->montant;
    }

    public function getDateCharge()
    {
        return $this->date_charge;
    }

    public function getRefunded()
    {
        return $this->refunded;
    }

    public function isRefunded()
    {
        return $this->refunded == 1;
    }

    public function getDateRemboursement()
    {
        return $this->date_remboursement;
    }

    public function getJustificatif()
    {
        return $this->justificatif;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function getUserAdded()
    {
        return $this->user_added;
    }

    public function getUserEdited()
    {
        return $this->user_edited;
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

    public function setUtilisateur($utilisateur)
    {
        $this->utilisateur = $utilisateur;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setMontant($montant)
    {
        $this->montant = $montant;
    }

    public function setDateCharge($date_charge)
    {
        $this->date_charge = $date_charge;
    }

    public function setRefunded($refunded)
    {
        $this->refunded = $refunded;
    }

    public function setDateRemboursement($date_remboursement)
    {
        $this->date_remboursement = $date_remboursement;
    }

    public function setJustificatif($justificatif)
    {
        $this->justificatif = $justificatif;
    }

    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;
    }

    public function setUserAdded($user_added)
    {
        $this->user_added = $user_added;
    }

    public function setUserEdited($user_edited)
    {
        $this->user_edited = $user_edited;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_utilisateur, titre, description, montant, date_charge, refunded, date_remboursement, justificatif, remarque, id_user_added, id_user_edited, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->utilisateur->getId(), "int"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->montant, "double"),
            GetSQLValueString($this->date_charge, "date"),
            GetSQLValueString($this->refunded, "int"),
            GetSQLValueString($this->date_remboursement, "date"),
            GetSQLValueString($this->justificatif, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->user_added->getId(), "int"),
            GetSQLValueString($this->user_edited->getId(), "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_utilisateur = %s, titre = %s, description = %s, montant = %s, date_charge = %s, refunded = %s, date_remboursement = %s, justificatif = %s, remarque = %s, id_user_edited = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->utilisateur->getId(), "int"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->montant, "double"),
            GetSQLValueString($this->date_charge, "date"),
            GetSQLValueString($this->refunded, "int"),
            GetSQLValueString($this->date_remboursement, "date"),
            GetSQLValueString($this->justificatif, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->user_edited->getId(), "int"),
            GetSQLValueString($this->last_edit, "date"),
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
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $caissenoire = new caissenoire();
        $SQLselect = sprintf("SELECT id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $caissenoire = static::build($data);
        }
        return $caissenoire;
    }

    public static function findAll($ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT id AS ID, " . static::$table . ".* FROM " . static::$table;
        if ($ordre) {
            $SQLselect .= " ORDER BY date_charge DESC";
        }
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function build($data)
    {
        $caissenoire = new caissenoire();
        $caissenoire->setId($data['ID']);
        $caissenoire->setUtilisateur(user::find($data['id_utilisateur']));
        $caissenoire->setTitre($data['titre']);
        $caissenoire->setDescription($data['description']);
        $caissenoire->setMontant($data['montant']);
        $caissenoire->setDateCharge($data['date_charge']);
        $caissenoire->setRefunded($data['refunded']);
        $caissenoire->setDateRemboursement($data['date_remboursement']);
        $caissenoire->setJustificatif($data['justificatif']);
        $caissenoire->setRemarque($data['remarque']);
        $caissenoire->setUserAdded(user::find($data['id_user_added']));
        $caissenoire->setUserEdited(user::find($data['id_user_edited']));
        $caissenoire->setDateAdd($data['date_add']);
        $caissenoire->setLastEdit($data['last_edit']);
        return $caissenoire;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }
}
