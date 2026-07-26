<?php

class charge
{
    static $table =  __prefixe_db__ . "charge";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $agence;
    private $paid_by;
    private $user;
	private $type;
    private $titre;
    private $description;
    private $total;
	private $devise;
    private $paid;
	private $facture;
	private $refunded;
    private $date_charge;
	private $date_payment;
	private $mode_payment;
	private $photo;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAgence()
    {
        return $this->agence;
    }
    
    public function getPaidBy()
    {
        return $this->paid_by;
    }

    public function getUser()
    {
        return $this->user;
    }
	
	public function getType()
    {
        return $this->type;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTotal()
    {
        return $this->total;
    }
	
	public function getDevise()
    {
        return $this->devise;
    }

    public function getPaid()
    {
        return $this->paid;
    }
	
	public function getFacture()
    {
        return $this->facture;
    }
    
    public function getRefunded()
    {
        return $this->refunded;
    }
	
	public function isPaid()
    {
        return $this->paid == 1 ? true : false;
    }
    
    public function isRefunded()
    {
        return $this->refunded == 1 ? true : false;
    }
	
	public function hasFacture()
    {
        return $this->facture == 1 ? true : false;
    }

    public function getDatePayment()
    {
        return $this->date_payment;
    }
	
	public function getModePayment()
    {
        return $this->mode_payment;
    }
	
	public function getDateCharge()
    {
        return $this->date_charge;
    }
	
	public function getPhoto()
    {
        return $this->photo;
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

    public function setAgence($agence)
    {
        $this->agence = $agence;
    }
    
    public function setPaidBy($paid_by)
    {
        $this->paid_by = $paid_by;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
	
	public function setType($type)
    {
        $this->type = $type;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }
	
	public function setDevise($devise)
    {
        $this->devise = $devise;
    }

    public function setPaid($paid)
    {
        $this->paid = $paid;
    }
	
	public function setFacture($facture)
    {
        $this->facture = $facture;
    }
    
    public function setRefunded($refunded)
    {
        $this->refunded = $refunded;
    }

    public function setDatePayment($date_payment)
    {
        $this->date_payment = $date_payment;
    }
	
	public function setModePayment($mode_payment)
    {
        $this->mode_payment = $mode_payment;
    }
	
	public function setDateCharge($date_charge)
    {
        $this->date_charge = $date_charge;
    }
	
	public function setPhoto($photo)
    {
        $this->photo = $photo;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence,paid_by,id_user,type, titre, description, total, devise, paid, facture, refunded, date_charge, date_payment, mode_payment, photo, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->paid_by->getId(), "int"),
            GetSQLValueString($this->user->getId(), "int"),    
            GetSQLValueString($this->type, "text"),
			GetSQLValueString($this->titre, "text"),				 
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->total, "double"),
			GetSQLValueString($this->devise, "text"),				 
			GetSQLValueString($this->paid, "int"),
			GetSQLValueString($this->facture, "int"),
			GetSQLValueString($this->refunded, "int"),
			GetSQLValueString($this->date_charge, "date"),
			GetSQLValueString($this->date_payment, "date"),	
			GetSQLValueString($this->mode_payment, "text"),	
			GetSQLValueString($this->photo, "text"),					 
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)) {
			return 1;
        } else {
            return 2;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_agence = %s, paid_by = %s, id_user = %s, type = %s, titre = %s, description = %s, total = %s, devise = %s, paid = %s, facture = %s, refunded = %s, date_charge = %s, date_payment = %s, mode_payment = %s, photo = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->paid_by->getId(), "int"),
            GetSQLValueString($this->user->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->total, "double"),
			GetSQLValueString($this->devise, "text"),				 
			GetSQLValueString($this->paid, "int"),
			GetSQLValueString($this->facture, "int"),
			GetSQLValueString($this->refunded, "int"),
			GetSQLValueString($this->date_charge, "date"),				 
			GetSQLValueString($this->date_payment, "date"),
			GetSQLValueString($this->mode_payment, "text"),
			GetSQLValueString($this->photo, "text"),
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
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id,$agence=1)
    {
        global $db;
        $charge = new charge();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id WHERE B.id = %s and A.ID = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $charge = static::build($data);
        }
        return $charge;
    }

    public static function findAll($ordre = false,$agence=1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where B.id = %s",
            GetSQLValueString($agence, "int")
        );
        
        if ($from) {
            $SQLselect .= " AND A.date_charge >= '$from'";
        }
        if ($to) {
            $SQLselect .= " AND A.date_charge <= '$to'";
        }
		
        if($ordre){
            $SQLselect .= " ORDER BY A.date_charge DESC";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $charge = static::build($data);
            array_push($items, $charge);
        }
        return $items;
    }

    public static function build($data){
        $charge = new charge();
        $charge->setId($data['ID']);
        $charge->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
        $charge->setPaidBy(user::find($data['paid_by']));
        $charge->setUser(user::find($data['id_user']));
		$charge->setType($data['type']);
        $charge->setTitre($data['titre']);
        $charge->setDescription($data['description']);
        $charge->setTotal($data['total']);
		$charge->setDevise($data['devise']);
        $charge->setPaid($data['paid']);
		$charge->setFacture($data['facture']);
		$charge->setRefunded($data['refunded']);
        $charge->setDateCharge($data['date_charge']);
		$charge->setDatePayment($data['date_payment']);
		$charge->setModePayment($data['mode_payment']);
		$charge->setPhoto($data['photo']);
        $charge->setDateAdd($data['date_add']);
        $charge->setLastEdit($data['last_edit']);
        return $charge;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    public static function count($agence = 1){
        global $db;
        $SQLcount = "SELECT count(A.id) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE B.id = $agence";
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }
	
	public static function total($year = false, $month = false, $agence = 1)
    {
        global $db;
        $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE B.id = $agence AND A.paid = 1";

        if ($year) {
            $SQLcount .= " AND YEAR(A.date_payment) = $year";
        }

        if ($month) {
            $SQLcount .= " AND MONTH(A.date_payment) = $month";
        }
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return intval($data["c"]);
        }
        return 0;
    }
    
    public static function getCharge($from = false, $to = false,$agence = 1, $devise = false)
    {
        global $db;
        if($agence)
            $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE B.id = $agence AND A.paid = 1";
        else
            $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE A.paid = 1";

        if ($from) {
            $SQLcount .= " AND A.date_payment >= '$from'";
        }

        if ($to) {
            $SQLcount .= " AND A.date_payment <= '$to'";
        }
        
        if ($devise) {
            $SQLcount .= " AND A.devise = '$devise'";
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return intval($data["c"]);
        }
        return 0;
    }
}