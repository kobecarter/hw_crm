<?php

class payslip
{
    static $table =  __prefixe_db__ . "payslip";

    private $id;
    private $resourcehumaine;
    private $title;
    private $date;
    private $file;
    private $amount;
    private $id_charge;

    public function __construct(){
        $this->id = 0;
    }

    // Convention de nommage unique pour tous les bulletins de paie (quel que soit le point
    // d'entrée : ajout direct sur la fiche employé, ou dropzone IA de la page Charges) :
    // "Bulletin de paie {Prénom Nom} {MM/YYYY}". Le titre n'est plus jamais saisi à la main.
    public static function titreAuto($resourcehumaine, $mois, $annee)
    {
        return 'Bulletin de paie ' . trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName())
            . ' ' . sprintf('%02d/%04d', $mois, $annee);
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
    
    public function getDate(){
        return $this->date;
    }

    public function getFile(){
        return $this->file;
    }

    // Net à payer extrait par IA du PDF (voir aiExtractor::extractPayslipAmount()), mis en
    // cache ici pour ne jamais ré-extraire un bulletin déjà traité lors d'un recalcul suivant.
    // Null tant que le bulletin n'a jamais été passé par le recalcul de bonus.
    public function getAmount(){
        return $this->amount;
    }

    public function getIdCharge(){
        return $this->id_charge;
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
    
    public function setDate($date){
        $this->date = $date;
    }

    public function setFile($file){
        $this->file = $file;
    }

    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function setIdCharge($id_charge){
        $this->id_charge = $id_charge;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, title, date, file, amount, id_charge) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->file, "text"),
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->id_charge, "int")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_resourcehumaine = %s, title = %s, date = %s, file = %s, amount = %s, id_charge = %s WHERE id = %s",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->title, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->file, "text"),
            GetSQLValueString($this->amount, "double"),
            GetSQLValueString($this->id_charge, "int"),
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

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function find($id)
    {
        global $db;
        $payslip = new payslip();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $payslip = static::build($data);
        }
        return $payslip;
    }

    // Utilisé côté formulaire Charges (mode édition) pour afficher un encart "déjà lié à ce
    // bulletin de paie" quand la charge en cours d'édition a été créée depuis la dropzone IA.
    public static function findByIdCharge($id_charge)
    {
        global $db;
        $payslip = new payslip();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_charge = %s",
            GetSQLValueString($id_charge, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $payslip = static::build($data);
        }
        return $payslip;
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
        $payslip = new payslip();
        $payslip->setId($data['id']);
        $payslip->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $payslip->setTitle($data['title']);
        $payslip->setDate($data['date']);
        $payslip->setFile($data['file']);
        $payslip->setAmount(isset($data['amount']) && $data['amount'] !== '' ? $data['amount'] : null);
        $payslip->setIdCharge(isset($data['id_charge']) ? $data['id_charge'] : null);
        return $payslip;
    }

    // Mois sans bulletin de paie entre la signature du contrat (repli sur la date de début si la
    // signature n'est pas renseignée) et aujourd'hui (ou la date de fin si l'employé n'est plus
    // actif) - alimente le bandeau d'alerte en haut de la liste des bulletins de paie. Retourne
    // les mois manquants du plus récent au plus ancien (les plus urgents à régulariser en premier).
    public static function missingMonths($resourcehumaine)
    {
        $depart = $resourcehumaine->getContractSigningDate() ? $resourcehumaine->getContractSigningDate() : $resourcehumaine->getStartDate();
        if (!$depart) {
            return array();
        }
        $fin = $resourcehumaine->getEndDate() && strtotime($resourcehumaine->getEndDate()) < time() ? $resourcehumaine->getEndDate() : date('Y-m-d');

        $presents = array();
        foreach (self::findAllByResourcehumaine($resourcehumaine->getId()) as $p) {
            if ($p->getDate()) {
                $presents[date('Y-m', strtotime($p->getDate()))] = true;
            }
        }

        $manquants = array();
        $curseur = new DateTime(date('Y-m-01', strtotime($depart)));
        $limite = new DateTime(date('Y-m-01', strtotime($fin)));
        while ($curseur <= $limite) {
            $ym = $curseur->format('Y-m');
            if (!isset($presents[$ym])) {
                $manquants[] = array('ym' => $ym, 'label' => $curseur->format('m/Y'));
            }
            $curseur->modify('+1 month');
        }

        return array_reverse($manquants);
    }

    // Ids des charges déjà liées à un bulletin de paie (créé automatiquement depuis la page
    // Charges) — utilisé par la liste des charges pour afficher un badge "Bulletin" sans
    // faire une requête par ligne.
    public static function findAllIdChargeLies()
    {
        global $db;
        $ids = array();
        $result = $db->queryS("SELECT id_charge FROM " . static::$table . " WHERE id_charge IS NOT NULL");
        foreach ($result as $row) {
            $ids[] = (int) $row['id_charge'];
        }
        return $ids;
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

