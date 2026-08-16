<?php

class parrainage
{
    static $table =  __prefixe_db__ . "parrainage";

    // Statuts : 0 en attente (soumis par l'employé, jamais auto-validé même si un client
    // correspondant a été trouvé), 1 validé (commission due, montant figé au moment de la
    // validation admin), 2 refusé.
    const STATUT_EN_ATTENTE = 0;
    const STATUT_VALIDE = 1;
    const STATUT_REFUSE = 2;

    private $id;
    private $resourcehumaine;
    private $nom;
    private $prenom;
    private $email;
    private $raison_social;
    private $client;
    private $statut;
    private $montant_commission;
    private $date_add;
    private $date_validation;

    public function __construct(){
        $this->id = 0;
        $this->statut = self::STATUT_EN_ATTENTE;
    }

    public function getId(){
        return $this->id;
    }

    public function getResourcehumaine(){
        return $this->resourcehumaine;
    }

    public function getNom(){
        return $this->nom;
    }

    public function getPrenom(){
        return $this->prenom;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getRaisonSocial(){
        return $this->raison_social;
    }

    public function getClient(){
        return $this->client;
    }

    public function getStatut(){
        return $this->statut;
    }

    public function getMontantCommission(){
        return $this->montant_commission;
    }

    public function getDateAdd(){
        return $this->date_add;
    }

    public function getDateValidation(){
        return $this->date_validation;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setResourcehumaine($resourcehumaine){
        $this->resourcehumaine = $resourcehumaine;
    }

    public function setNom($nom){
        $this->nom = $nom;
    }

    public function setPrenom($prenom){
        $this->prenom = $prenom;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function setRaisonSocial($raison_social){
        $this->raison_social = $raison_social;
    }

    public function setClient($client){
        $this->client = $client;
    }

    public function setStatut($statut){
        $this->statut = $statut;
    }

    public function setMontantCommission($montant_commission){
        $this->montant_commission = $montant_commission;
    }

    public function setDateAdd($date_add){
        $this->date_add = $date_add;
    }

    public function setDateValidation($date_validation){
        $this->date_validation = $date_validation;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, nom, prenom, email, raison_social, id_client, statut, montant_commission, date_add, date_validation) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->prenom, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->raison_social, "text"),
            GetSQLValueString($this->client ? $this->client->getId() : null, "int"),
            GetSQLValueString($this->statut, "int"),
            GetSQLValueString($this->montant_commission, "double"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->date_validation, "date")
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET nom = %s, prenom = %s, email = %s, raison_social = %s, id_client = %s, statut = %s, montant_commission = %s, date_validation = %s WHERE id = %s",
            GetSQLValueString($this->nom, "text"),
            GetSQLValueString($this->prenom, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->raison_social, "text"),
            GetSQLValueString($this->client ? $this->client->getId() : null, "int"),
            GetSQLValueString($this->statut, "int"),
            GetSQLValueString($this->montant_commission, "double"),
            GetSQLValueString($this->date_validation, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete(){
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

    public static function find($id)
    {
        global $db;
        $parrainage = new parrainage();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $parrainage = static::build($data);
        }
        return $parrainage;
    }

    public static function findAllByResourcehumaine($id_resourcehumaine)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s ORDER BY id DESC",
            GetSQLValueString($id_resourcehumaine, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    // Total des commissions dues (montant figé à la validation) pour un employé - utilisé pour
    // le KPI "commission cumulée" du tableau de bord.
    public static function totalCommissionValidee($id_resourcehumaine)
    {
        global $db;
        $SQLselect = sprintf("SELECT SUM(montant_commission) as total FROM " . static::$table . " WHERE id_resourcehumaine = %s AND statut = %s",
            GetSQLValueString($id_resourcehumaine, "int"),
            GetSQLValueString(self::STATUT_VALIDE, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["total"] ? floatval($data["total"]) : 0;
        }
        return 0;
    }

    public static function build($data){
        $parrainage = new parrainage();
        $parrainage->setId($data['id']);
        $parrainage->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
        $parrainage->setNom($data['nom']);
        $parrainage->setPrenom($data['prenom']);
        $parrainage->setEmail($data['email']);
        $parrainage->setRaisonSocial($data['raison_social']);
        // findAny() (pas find()) : le client correspondant peut appartenir à n'importe quelle
        // agence, jamais seulement celle de la session en cours.
        $parrainage->setClient(!empty($data['id_client']) ? client::findAny($data['id_client']) : null);
        $parrainage->setStatut($data['statut']);
        $parrainage->setMontantCommission($data['montant_commission']);
        $parrainage->setDateAdd($data['date_add']);
        $parrainage->setDateValidation($data['date_validation']);
        return $parrainage;
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
