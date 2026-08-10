<?php

class fileresourcehumaine
{
    static $table =  __prefixe_db__ . "fileresourcehumaine";

    private $id;
    private $resourcehumaine;
    private $title;
    private $document_type;
    private $file;

    // Types de documents suivis pour le contrôle de conformité du dossier employé (clé stable
    // utilisée en base, indépendante du libellé affiché qui peut être traduit/modifié).
    // "cin" et "engagement_confidentialite" sont communs aux deux listes, avec un libellé
    // légèrement différent pour les stagiaires (repris tel quel dans le formulaire de dépôt).
    public static $documentsRequisParStatut = array(
        'Titulaire' => array(
            'cin' => 'CIN',
            'offre_emploi' => "Offre d'emploi",
            'contrat_cdi' => 'Contrat CDI',
            'engagement_confidentialite' => 'Engagement de confidentialité',
            'reglement_interieur' => 'Règlement intérieur',
        ),
        'Periode De test' => array(
            'cin' => 'CIN',
            'offre_emploi' => "Offre d'emploi",
            'contrat_cdi' => 'Contrat CDI',
            'engagement_confidentialite' => 'Engagement de confidentialité',
            'reglement_interieur' => 'Règlement intérieur',
        ),
        'Stagaire' => array(
            'reglement_interieur' => 'Règlement intérieur',
            'cin' => 'Copie CIN',
            'engagement_confidentialite' => 'Accord de confidentialité',
            'certificat_residence' => 'Certificat de résidence',
        ),
    );

    // Documents requis pour un statut donné (repli sur la liste "Titulaire" si le statut est
    // inconnu/vide, pour ne jamais laisser un profil sans aucune exigence de conformité).
    public static function documentsRequis($statut)
    {
        if (isset(self::$documentsRequisParStatut[$statut])) {
            return self::$documentsRequisParStatut[$statut];
        }
        return self::$documentsRequisParStatut['Titulaire'];
    }

    public function __construct(){
        $this->id = 0;
    }

    public function getId(){
        return $this->id;
    }

    public function getResourcehumaine(){
        return $this->resourcehumaine;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getDocumentType(){
        return $this->document_type;
    }

    public function getFile(){
        return $this->file;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setResourcehumaine($resourcehumaine){
        $this->resourcehumaine = $resourcehumaine;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setDocumentType($document_type){
        $this->document_type = $document_type;
    }

    public function setFile($file){
        $this->file = $file;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, title, document_type, file) VALUES (%s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->document_type, "text"),
            GetSQLValueString($this->file, "text")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_resourcehumaine = %s, title = %s, document_type = %s, file = %s WHERE id = %s",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->document_type, "text"),
            GetSQLValueString($this->file, "text"),
            GetSQLValueString($this->getId(), "int")
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
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function deleteGroupe($id_resourcehumaine)
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id_resourcehumaine = %s",
            GetSQLValueString($id_resourcehumaine, "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $fileresourcehumaine = new fileresourcehumaine();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $fileresourcehumaine = static::build($data);
        }
        return $fileresourcehumaine;
    }

    public static function findAllByResourcehumaine($id_resourcehumaine)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s ORDER BY id ASC",
            GetSQLValueString($id_resourcehumaine, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $filesresourcehumaine = static::build($data);
            array_push($items, $filesresourcehumaine);
        }
        return $items;
    }

    public static function build($data){
        $fileresourcehumaine = new fileresourcehumaine();
        $fileresourcehumaine->setId($data['id']);
        $fileresourcehumaine->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $fileresourcehumaine->setTitle($data['title']);
        $fileresourcehumaine->setDocumentType(isset($data['document_type']) ? $data['document_type'] : null);
        $fileresourcehumaine->setFile($data['file']);
        return $fileresourcehumaine;
    }

    // Types de documents requis pour ce statut déjà déposés parmi $files (id_resourcehumaine
    // donné) — utilisé pour calculer les documents manquants (contrôle de conformité).
    public static function documentTypesPresents($files)
    {
        $presents = array();
        foreach ($files as $f) {
            if ($f->getDocumentType()) {
                $presents[$f->getDocumentType()] = true;
            }
        }
        return $presents;
    }

    // Liste des documents requis (statut donné) manquants parmi $files. Retourne un tableau
    // associatif [clé => libellé] des documents encore absents.
    public static function documentsManquants($statut, $files)
    {
        $requis = self::documentsRequis($statut);
        $presents = self::documentTypesPresents($files);
        $manquants = array();
        foreach ($requis as $cle => $libelle) {
            if (!isset($presents[$cle])) {
                $manquants[$cle] = $libelle;
            }
        }
        return $manquants;
    }

    public static function count(){
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

}

