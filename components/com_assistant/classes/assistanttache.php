<?php

class assistanttache
{
    static $table = __prefixe_db__ . "assistant_tache";

    // Types de relation possibles - "reunion" n'a pas de fiche associée (pas de module dédié),
    // c'est juste une catégorie qu'on coche ; les autres pointent vers une fiche réelle (id_relation).
    static $relationTypes = array(
        'client'      => array('label' => 'Client',       'icon' => 'fa-user-tie',            'hasRecord' => true),
        'fournisseur' => array('label' => 'Fournisseur',   'icon' => 'fa-truck',               'hasRecord' => true),
        'rh'          => array('label' => 'Employé (RH)',  'icon' => 'fa-id-badge',             'hasRecord' => true),
        'reclamation' => array('label' => 'Réclamation',   'icon' => 'fa-exclamation-circle',  'hasRecord' => true),
        'banque'      => array('label' => 'Banque',        'icon' => 'fa-university',           'hasRecord' => true),
        'reunion'     => array('label' => 'Réunion',       'icon' => 'fa-users',                 'hasRecord' => false),
    );

    private $id;
    private $agence;
    private $type_relation;
    private $id_relation;
    private $type;
    private $titre;
    private $date_tache;
    private $remarque;
    private $termine;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
        $this->termine = 0;
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getAgence()
    {
        return $this->agence;
    }
    public function setAgence($agence)
    {
        $this->agence = $agence;
    }

    public function getTypeRelation()
    {
        return $this->type_relation;
    }
    public function setTypeRelation($type_relation)
    {
        $this->type_relation = $type_relation;
    }

    public function getIdRelation()
    {
        return $this->id_relation;
    }
    public function setIdRelation($id_relation)
    {
        $this->id_relation = $id_relation;
    }

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getTitre()
    {
        return $this->titre;
    }
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getDateTache()
    {
        return $this->date_tache;
    }
    public function setDateTache($date_tache)
    {
        $this->date_tache = $date_tache;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;
    }

    public function isTermine()
    {
        return $this->termine == 1;
    }
    public function setTermine($termine)
    {
        $this->termine = $termine;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }
    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }
    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    // Jours restants avant l'échéance (négatif = en retard) - même convention que
    // rappel::getDaysLeft() / relance::getDaysLeft(), réutilisée par le Centre d'alertes.
    public function getDaysLeft()
    {
        if (!$this->date_tache) {
            return null;
        }
        $today = new DateTime('today');
        $echeance = new DateTime(substr($this->date_tache, 0, 10));
        return (int) $today->diff($echeance)->format('%r%a');
    }

    public function getRelationIcon()
    {
        if (!$this->type_relation || !isset(static::$relationTypes[$this->type_relation])) {
            return null;
        }
        return static::$relationTypes[$this->type_relation]['icon'];
    }

    // Objet réel pointé par (type_relation, id_relation) - null si "reunion" (pas de fiche) ou
    // si aucune relation n'est définie. Un seul point d'entrée pour list.php/le Centre d'alertes
    // plutôt que de dupliquer ce switch partout où on affiche une tâche.
    public function getRelationObject()
    {
        if (!$this->type_relation || empty($this->id_relation)) {
            return null;
        }
        switch ($this->type_relation) {
            case 'client':
                return client::find($this->id_relation, $this->agence);
            case 'fournisseur':
                return fournisseur::find($this->id_relation, $this->agence);
            case 'rh':
                return resourcehumaine::find($this->id_relation);
            case 'reclamation':
                return reclamation::find($this->id_relation, $this->agence);
            case 'banque':
                return bank::find($this->id_relation);
            default:
                return null;
        }
    }

    // Libellé lisible de la relation ("Réunion" pour ce type sans fiche, le nom de la fiche pour
    // les autres, ou null si aucune relation).
    public function getRelationLabel()
    {
        if (!$this->type_relation || !isset(static::$relationTypes[$this->type_relation])) {
            return null;
        }
        if (!static::$relationTypes[$this->type_relation]['hasRecord']) {
            return static::$relationTypes[$this->type_relation]['label'];
        }
        $objet = $this->getRelationObject();
        if (!$objet || $objet->getId() == 0) {
            return null;
        }
        switch ($this->type_relation) {
            case 'client':
            case 'fournisseur':
                $nom = trim((string) $objet->getRaisonSocial());
                return $nom !== '' ? $nom : trim($objet->getPrenom() . ' ' . $objet->getNom());
            case 'rh':
                return trim($objet->getFirstName() . ' ' . $objet->getLastName());
            case 'reclamation':
                return $objet->getSujet();
            case 'banque':
                $label = $objet->getLabel();
                if ($label !== null && $label !== '') {
                    return $label;
                }
                $rs = $objet->getRaisonSociale();
                return ($rs !== null && $rs !== '') ? $rs : $objet->getBanque();
            default:
                return null;
        }
    }

    // URL vers la fiche liée - null si "reunion" ou aucune relation (pas de lien à afficher).
    public function getRelationUrl()
    {
        if (!$this->type_relation || empty($this->id_relation) || !static::$relationTypes[$this->type_relation]['hasRecord']) {
            return null;
        }
        switch ($this->type_relation) {
            case 'client':
                return 'index.php?option=com_client&task=showDetails&id=' . $this->id_relation;
            case 'fournisseur':
                return 'index.php?option=com_fournisseur&task=edit&id=' . $this->id_relation;
            case 'rh':
                return 'index.php?option=com_resourcehumaine&task=show&id=' . $this->id_relation;
            case 'reclamation':
                return 'index.php?option=com_reclamation&task=edit&id=' . $this->id_relation;
            case 'banque':
                return 'index.php?option=com_bank&task=edit&id=' . $this->id_relation;
            default:
                return null;
        }
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_agence, type_relation, id_relation, type, titre, date_tache, remarque, termine, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence, "int"),
            GetSQLValueString($this->type_relation, "text"),
            GetSQLValueString($this->id_relation, "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->date_tache, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->termine, "int"),
            GetSQLValueString($this->date_add, "text"),
            GetSQLValueString($this->last_edit, "text")
        );
        // La méthode query() de com_config/classes/mysql.php ne retourne jamais rien (toujours
        // null/falsy, même en cas de succès) - convention de tout le reste du codebase (cf.
        // relance::delete()) : tester !$db->query(...), pas $db->query(...).
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
            "UPDATE " . static::$table . " SET type_relation = %s, id_relation = %s, type = %s, titre = %s, date_tache = %s, remarque = %s, termine = %s, last_edit = %s WHERE id = %s AND id_agence = %s",
            GetSQLValueString($this->type_relation, "text"),
            GetSQLValueString($this->id_relation, "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->date_tache, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->termine, "int"),
            GetSQLValueString($this->last_edit, "text"),
            GetSQLValueString($this->id, "int"),
            GetSQLValueString($this->agence, "int")
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
            "DELETE FROM " . static::$table . " WHERE id = %s AND id_agence = %s",
            GetSQLValueString($this->id, "int"),
            GetSQLValueString($this->agence, "int")
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
        $tache = new assistanttache();
        $SQLselect = sprintf(
            "SELECT id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE id = %s AND id_agence = %s",
            GetSQLValueString($id, "int"),
            GetSQLValueString($agence, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $tache = static::build($data);
        }
        return $tache;
    }

    // $termine : null = toutes, false = à faire seulement, true = terminées seulement.
    public static function findAll($agence, $termine = null)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE id_agence = %s",
            GetSQLValueString($agence, "int")
        );
        if ($termine === true) {
            $SQLselect .= " AND termine = 1";
        } elseif ($termine === false) {
            $SQLselect .= " AND (termine IS NULL OR termine = 0)";
        }
        $SQLselect .= " ORDER BY (date_tache IS NULL), date_tache ASC";
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function build($data)
    {
        $tache = new assistanttache();
        $tache->setId($data['ID']);
        $tache->setAgence($data['id_agence']);
        $tache->setTypeRelation(isset($data['type_relation']) ? $data['type_relation'] : null);
        $tache->setIdRelation(isset($data['id_relation']) ? $data['id_relation'] : null);
        $tache->setType($data['type']);
        $tache->setTitre($data['titre']);
        $tache->setDateTache($data['date_tache']);
        $tache->setRemarque($data['remarque']);
        $tache->setTermine($data['termine']);
        $tache->setDateAdd($data['date_add']);
        $tache->setLastEdit($data['last_edit']);
        return $tache;
    }
}
