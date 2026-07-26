<?php

class bank
{
    static $table =  __prefixe_db__ . "bank";

    private $id;
    private $raison_sociale;
    private $siege_social;
    private $numero_registre_commerce;
    private $ice;
    private $rib;
    private $code_swift;
    private $banque;
    private $iban_number;
    private $currency;
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

    public function getRaisonSociale()
    {
        return $this->raison_sociale;
    }
	
	public function getSiegeSocial()
    {
        return $this->siege_social;
    }

    public function getNumeroRegistreCommerce()
    {
        return $this->numero_registre_commerce;
    }

	public function getIce()
    {
        return $this->ice;
    }

    public function getRib()
    {
        return $this->rib;
    }

    public function getCodeSwift()
    {
        return $this->code_swift;
    }
    
    public function getBanque()
    {
        return $this->banque;
    }
    
    public function getIbanNumber()
    {
        return $this->iban_number;
    }
    
    public function getCurrency()
    {
        return $this->currency;
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

	
    public function setRaisonSociale($raison_sociale)
    {
        $this->raison_sociale = $raison_sociale;
    }
	
	public function setSiegeSocial($siege_social)
    {
        $this->siege_social = $siege_social;
    }

    public function setNumeroRegistreCommerce($numero_registre_commerce)
    {
        $this->numero_registre_commerce = $numero_registre_commerce;
    }

	public function setIce($ice)
    {
        $this->ice = $ice;
    }

    public function setRib($rib)
    {
        $this->rib = $rib;
    }

    public function setCodeSwift($code_swift)
    {
        $this->code_swift = $code_swift;
    }
    
    public function setBanque($banque)
    {
        $this->banque = $banque;
    }
    
    public function setIbanNumber($iban_number)
    {
        $this->iban_number = $iban_number;
    }
    
    public function setCurrency($currency)
    {
        $this->currency = $currency;
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

        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (raison_sociale, siege_social, numero_registre_commerce, ice, rib, code_swift, banque, iban_number, currency, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($this->raison_sociale, "text"),
            GetSQLValueString($this->siege_social, "text"),
            GetSQLValueString($this->numero_registre_commerce, "text"),
            GetSQLValueString($this->ice, "text"),
            GetSQLValueString($this->rib, "text"),
            GetSQLValueString($this->code_swift, "text"),
            GetSQLValueString($this->banque, "text"),
            GetSQLValueString($this->iban_number, "text"),
            GetSQLValueString($this->currency, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );

        if (!$db->query($SQLinsert)) 
        {
            return 1;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  raison_sociale = %s, siege_social = %s, numero_registre_commerce = %s, ice = %s, rib = %s, code_swift = %s, banque = %s, iban_number = %s, currency = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->raison_sociale, "text"),
            GetSQLValueString($this->siege_social, "text"),
            GetSQLValueString($this->numero_registre_commerce, "text"),
            GetSQLValueString($this->ice, "text"),
            GetSQLValueString($this->rib, "text"),
            GetSQLValueString($this->code_swift, "text"),
            GetSQLValueString($this->banque, "text"),
            GetSQLValueString($this->iban_number, "text"),
            GetSQLValueString($this->currency, "text"),
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

    public static function find($id)
    {
        global $db;
        $bank = new bank();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $bank = static::build($data);
        }
        return $bank;
    }

    public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table);

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $bank = static::build($data);
            array_push($items, $bank);
        }
        return $items;
    }

    public static function build($data){
        global $db;
        $bank = new bank();
        
        $bank->setId($data['id']);
        $bank->setRaisonSociale($data['raison_sociale']);
        $bank->setSiegeSocial($data['siege_social']);
		$bank->setNumeroRegistreCommerce($data['numero_registre_commerce']);
        $bank->setIce($data['ice']);
        $bank->setRib($data['rib']);
        $bank->setCodeSwift($data['code_swift']);
        $bank->setBanque($data['banque']);
        $bank->setIbanNumber($data['iban_number']);
        $bank->setCurrency($data['currency']);
        $bank->setDateAdd($data['date_add']);
        $bank->setLastEdit($data['last_edit']);
        return $bank;
    }
    

    public static function getLastId(){
        global $db;
        return $db->last_id();
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

    public static function deleteMultiple($data){
        
        global $db;	
        if(isset($data['ids']) && !empty($data['ids'])){
            extract($data);
            $SQLdelete = "DELETE FROM ". static::$table ." WHERE id in $ids";
            if(!$db->query($SQLdelete)) {
                //seo();
                return 1;
            }else
                return 2;
        }
        else
            return 0;
    }

    public static function buildApi($data){
        $bank = [
            'id' => $data['id'],
            'raison_sociale' => $data['raison_sociale'],
            'siege_social' => $data['siege_social'],
            'numero_registre_commerce' => $data['numero_registre_commerce'],
            'ice' => $data['ice'],
            'rib' => $data['rib'],
            'code_swift' => $data['code_swift'],
            'banque' => $data['banque'],
            'iban_number' => $data['iban_number'],
            'currency' => $data['currency'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit']
        ];
        return $bank;
    }

    public static function ApiFindById($id)
    {
        if(getToken()){
            global $db;
            $bank = new bank();
            $SQLselect = sprintf("SELECT * FROM " . static::$table . " where id = %s",
                GetSQLValueString($id, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $bank = static::buildApi($data);
            }
            return $bank;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }
}