<?php

class cheque
{
    static $table =  __prefixe_db__ . "cheque";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $agence;
    private $check_number;
    private $file;
    private $date;
	private $beneficiary;
    private $amount;
    private $currency;
    private $status;
	private $reason;
    private $comment;
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
    
    public function getCheckNumber()
    {
        return $this->check_number;
    }

    public function getFile()
    {
        return $this->file;
    }
	
	public function getDate()
    {
        return $this->date;
    }

    public function getBeneficiary()
    {
        return $this->beneficiary;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
	
	public function getStatus()
    {
        return $this->status;
    }

    public function getReason()
    {
        return $this->reason;
    }
	
	public function getComment()
    {
        return $this->comment;
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
    
    public function setCheckNumber($check_number)
    {
        $this->check_number = $check_number;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }
	
	public function setDate($date)
    {
        $this->date = $date;
    }

    public function setBeneficiary($beneficiary)
    {
        $this->beneficiary = $beneficiary;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
	
	public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;
    }
	
	public function setComment($comment)
    {
        $this->comment = $comment;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence,check_number,file,date, beneficiary, amount, status, reason, comment, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->check_number, "text"),
			GetSQLValueString($this->file, "text"),				 
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->beneficiary, "text"),
			GetSQLValueString($this->amount, "double"),	
            GetSQLValueString($this->currency, "text"),				 
			GetSQLValueString($this->status, "text"),
			GetSQLValueString($this->reason, "text"),
			GetSQLValueString($this->comment, "text"),					 
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_agence = %s, check_number = %s, file = %s, date = %s, beneficiary = %s, amount = %s, currency = %s, status = %s, reason = %s, comment = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->check_number, "text"),
			GetSQLValueString($this->file, "text"),				 
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->beneficiary, "text"),
			GetSQLValueString($this->amount, "double"),	
            GetSQLValueString($this->currency, "text"),				 
			GetSQLValueString($this->status, "text"),
			GetSQLValueString($this->reason, "text"),
			GetSQLValueString($this->comment, "text"),	
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
        $cheque = new cheque();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id WHERE B.id = %s and A.ID = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $cheque = static::build($data);
        }
        return $cheque;
    }

    public static function findAll($ordre = false,$agence=1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where B.id = %s",
            GetSQLValueString($agence, "int")
        );
		
        if($ordre){
            $SQLselect .= " ORDER BY A.date_add DESC";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $cheque = static::build($data);
            array_push($items, $cheque);
        }
        return $items;
    }

    public static function build($data){
        $cheque = new cheque();
        $cheque->setId($data['ID']);
        $cheque->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
        $cheque->setCheckNumber($data['check_number']);
        $cheque->setFile($data['file']);
		$cheque->setDate($data['date']);
        $cheque->setBeneficiary($data['beneficiary']);
        $cheque->setAmount($data['amount']);
        $cheque->setCurrency($data['currency']);
        $cheque->setStatus($data['status']);
		$cheque->setReason($data['reason']);
        $cheque->setComment($data['comment']);
        $cheque->setDateAdd($data['date_add']);
        $cheque->setLastEdit($data['last_edit']);
        return $cheque;
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
}