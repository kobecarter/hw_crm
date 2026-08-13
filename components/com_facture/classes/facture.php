<?php

class facture
{
    static $table =  __prefixe_db__ . "facture";
    static $table2 =  __prefixe_db__ . "item_facture";
    static $table3 =  __prefixe_db__ . "payment";
    static $table4 =  __prefixe_db__ . "agence";
    static $table5 =  __prefixe_db__ . "client";

    private $id;
    private $user_added;
    private $user_edited;
    private $bank;
    private $numero;
    //
    private $sec_numero;
    //
    private $client;
    private $devis;
    private $date_facture;
    private $total;
    private $statu;
    private $devise;
    private $discount;
    private $discount_val;
    private $condition_paiment;
    private $remarque;
    private $remarque_moi;
    private $additional_information;
    private $proforma;
    private $show_signature;
    private $langue;
    private $date_debut;
    private $count_sending;
    private $date_fin;
    private $date_add;
    private $last_edit;
    private $avoir;
    private $archived;
    private $id_facture;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserAdded()
    {
        return $this->user_added;
    }

    public function getUserEdited()
    {
        return $this->user_edited;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getDevis()
    {
        return $this->devis;
    }

    public function getDateFacture()
    {
        return $this->date_facture;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getStatu()
    {
        return $this->statu;
    }

    public function getDevise()
    {
        return $this->devise;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function getDiscountVal()
    {
        return $this->discount_val;
    }

    public function getConditionPaiment()
    {
        return $this->condition_paiment;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function getRemarqueMoi()
    {
        return $this->remarque_moi;
    }
    
    public function getAdditionalInformation()
    {
        return $this->additional_information;
    }

    public function getProforma()
    {
        return $this->proforma;
    }
    
    public function getShowSignature()
    {
        return $this->show_signature;
    }

    public function getLangue()
    {
        return $this->langue;
    }

    public function isProforma()
    {
        return $this->proforma == 1 ? true : false;
    }
    
     public function isShowSignature()
    {
        return $this->show_signature == 1 ? true : false;
    }

    public function getSecNumero()
    {
        return $this->sec_numero;
    }

    public function getDateDebut()
    {
        return $this->date_debut;
    }

    public function getDateFin()
    {
        return $this->date_fin;
    }

    public function getCountSending()
    {
        return $this->count_sending;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function isAvoir()
    {
        return $this->avoir ? 1 : 0;
    }

    public function isArchived()
    {
        return $this->archived ? 1 : 0;
    }

    public function getIdFacture()
    {
        return $this->id_facture;
    }

    public function getDaysLeft()
    {
        $from = new DateTime();
        $to = new DateTime($this->date_fin);

        $interval = $from->diff($to);
        return $interval->format('%r%a');
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUserAdded($user_added)
    {
        $this->user_added = $user_added;
    }

    public function setUserEdited($user_edited)
    {
        $this->user_edited = $user_edited;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function setDevis($devis)
    {
        $this->devis = $devis;
    }

    public function setDateFacture($date_facture)
    {
        $this->date_facture = $date_facture;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function setStatu($statu)
    {
        $this->statu = $statu;
    }

    public function setDevise($devise)
    {
        $this->devise = $devise;
    }

    public function setConditionPaiment($condition_paiment)
    {
        $this->condition_paiment = $condition_paiment;
    }

    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;
    }

    public function setRemarqueMoi($remarque_moi)
    {
        $this->remarque_moi = $remarque_moi;
    }
    
    public function setAdditionalInformation($additional_information)
    {
        $this->additional_information = $additional_information;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setDiscountVal($discount_val)
    {
        $this->discount_val = $discount_val;
    }

    public function setProforma($proforma)
    {
        $this->proforma = $proforma;
    }
    
    public function setShowSignature($show_signature)
    {
        $this->show_signature = $show_signature;
    }

    public function setLangue($langue)
    {
        $this->langue = $langue;
    }

    public function setDateDebut($date_debut)
    {
        $this->date_debut = $date_debut;
    }

    public function setDateFin($date_fin)
    {
        $this->date_fin = $date_fin;
    }

    public function setCountSending($count_sending)
    {
        $this->count_sending = $count_sending;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function setAvoir($avoir)
    {
        $this->avoir = $avoir;
    }

    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    public function setIdFacture($id_facture)
    {
        $this->id_facture = $id_facture;
    }

    //
    public function setSecNumero($numero)
    {
        $this->sec_numero = $numero;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_user_added, id_bank, numero, sec_numero, id_client, id_devis, date_facture, total, statu, devise, discount, discount_val, condition_paiment, remarque, remarque_moi, additional_information, proforma, show_signature, langue, date_debut, date_fin, date_add, last_edit, avoir, archived, id_facture) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->user_added->getId(), "int"),
            GetSQLValueString($this->bank ? $this->bank->getId() : null, "int"),
            GetSQLValueString($this->numero, "text"),
            GetSQLValueString($this->sec_numero, "text"),
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->devis ? $this->devis->getId() : null, "int"),
            GetSQLValueString($this->date_facture, "date"),
            GetSQLValueString($this->total, "double"),
            GetSQLValueString($this->statu, "int"),
            GetSQLValueString($this->devise, "text"),
            GetSQLValueString($this->discount, "text"),
            GetSQLValueString($this->discount_val, "double"),
            GetSQLValueString($this->condition_paiment, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->remarque_moi, "text"),
            GetSQLValueString($this->additional_information, "text"),
            GetSQLValueString($this->proforma, "int"),
            GetSQLValueString($this->show_signature, "int"),
            GetSQLValueString($this->langue, "text"),
            GetSQLValueString($this->date_debut, "date"),
            GetSQLValueString($this->date_fin, "date"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->avoir, "int"),
            GetSQLValueString($this->archived, "int"),
            GetSQLValueString($this->id_facture, "int")
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_user_edited = %s, id_bank = %s, numero = %s, sec_numero = %s, id_client = %s, id_devis = %s, date_facture = %s, total = %s, statu = %s, devise = %s, discount = %s, discount_val = %s, condition_paiment=%s, remarque=%s, remarque_moi=%s, additional_information=%s, proforma = %s, show_signature = %s, langue = %s, date_debut = %s, date_fin = %s, last_edit = %s, avoir = %s, archived = %s, id_facture = %s WHERE id = %s",
            GetSQLValueString($this->user_edited->getId(), "int"),
            GetSQLValueString($this->bank ? $this->bank->getId() : null, "int"),
            GetSQLValueString($this->numero, "text"),
            GetSQLValueString($this->sec_numero, "text"), //
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->devis->getId(), "int"),
            GetSQLValueString($this->date_facture, "date"),
            GetSQLValueString($this->total, "double"),
            GetSQLValueString($this->statu, "int"),
            GetSQLValueString($this->devise, "text"),
            GetSQLValueString($this->discount, "text"),
            GetSQLValueString($this->discount_val, "double"),
            GetSQLValueString($this->condition_paiment, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->remarque_moi, "text"),
            GetSQLValueString($this->additional_information, "text"),
            GetSQLValueString($this->proforma, "int"),
            GetSQLValueString($this->show_signature, "int"),
            GetSQLValueString($this->langue, "text"),
            GetSQLValueString($this->date_debut, "date"),
            GetSQLValueString($this->date_fin, "date"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->avoir, "int"),
            GetSQLValueString($this->archived, "int"),
            GetSQLValueString($this->id_facture, "int"),
            GetSQLValueString($this->id, "int")
        );

        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function increaseCountSending()
    {
        global $db;
        $count_sending = intval($this->count_sending) + 1;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  count_sending = %s WHERE id = %s",
            GetSQLValueString($count_sending, "int"),
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
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        $SQLdelete2 = sprintf(
            "DELETE FROM " . static::$table2 . " WHERE id_facture = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id, $agence = 1)
    {
        global $db;
        $facture = new facture();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where A.id = %s",
            //GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        // echo $SQLselect;
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $facture = static::build($data);
        }
        return $facture;
    }

    public static function findLastInvoice($agence = 1)
    {
        global $db;
        $facture = new facture();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where B.id_agence = %s",
            GetSQLValueString($agence, "int")
        );
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        $SQLselect .= " ORDER BY A.id DESC LIMIT 1";
        // echo $SQLselect;
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $facture = static::build($data);
        }
        return $facture;
    }

    public static function findAll($statu = false, $from = false, $to = false, $ordre = false, $limit = false, $archived = false, $agence = 1, $currency = false, $year = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where C.id = $agence";

        if ($statu) {
            if ($statu == -1)
                $SQLselect .= " AND (A.statu IS NULL OR A.statu IN (0,2,3))";
            else
                $SQLselect .= " AND A.statu = " . intval($statu);
        }
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($from) {
            $SQLselect .= " AND A.date_facture >= '$from'";
        }
        if ($to) {
            $SQLselect .= " AND A.date_facture <= '$to'";
        }

        //facture avoir
        if ($archived) {
            $SQLselect .= " AND A.archived = 1";
        } else {
            $SQLselect .= " AND (A.archived = 0 OR A.archived IS NULL)";
        }
        //end

        if ($currency) {
            $SQLselect .= " AND A.devise = '$currency'";
        }

        if ($year) {
            $SQLselect .= " AND YEAR(A.date_facture) = " . intval($year);
        }

        //if ($ordre) {
        $SQLselect .= " ORDER BY A.ID DESC";
        //}
        if ($limit) {
            $SQLselect .= " LIMIT $limit";
        }
        //echo $SQLselect;

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $facture = static::build($data);
            array_push($items, $facture);
        }
        return $items;
    }

    // Recherche globale (bandeau de recherche du bandeau haut) : numéro de facture uniquement -
    // utilisé par com_search, jamais par le listing standard des factures.
    public static function search($terme, $agence = false)
    {
        global $db;
        $items = array();
        $like = GetSQLValueString('%' . $terme . '%', 'text');
        $SQLselect = "SELECT A.id as ID, A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id = B.id_agence"
            . " WHERE 1=1" . ($agence ? " AND C.id = " . intval($agence) : "") . " AND A.numero LIKE $like ORDER BY A.id DESC LIMIT 8";
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function findAllWillFinish()
    {
        global $db;
        $facture = new facture();
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.*,B.email,B.nom,B.id_agence FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where DATEDIFF(A.date_fin, CURDATE()) <= 10 and DATEDIFF(A.date_fin, CURDATE()) >0 and A.count_sending < 3;";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $facture->setId($data['ID']);
            $facture->setTotal($data['total']);
            $data['rest'] = strval($facture->getReste());
            array_push($items, $data);
        }
        return $items;
    }

    public static function findByDevis($devis_id, $agence = 1)
    {
        global $db;
        $facture = new facture();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where A.id_devis = %s",
            //GetSQLValueString($agence, "int"),
            GetSQLValueString($devis_id, "int")
        );
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        // echo $SQLselect;
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $facture = static::build($data);
        } else {
            $facture = [];
        }
        return $facture;
    }

    public static function ofClient($clientID = 0, $from = false, $to = false, $ordre = false, $limit = false, $archived = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT id as ID, A.* FROM " . static::$table . " as A WHERE 1 = 1";

        if ($clientID) {
            $SQLselect .= " AND id_client = " . intval($clientID);
        }
        if ($from) {
            $SQLselect .= " AND date_facture >= '$from'";
        }
        if ($to) {
            $SQLselect .= " AND date_facture <= '$to'";
        }

        //facture avoir
        if ($archived) {
            $SQLselect .= " AND archived = 1";
        } else {
            $SQLselect .= " AND (archived = 0 OR archived IS NULL)";
        }
        //end

        if ($ordre) {
            $SQLselect .= " ORDER BY date_facture DESC";
        }
        if ($limit) {
            $SQLselect .= " LIMIT $limit";
        }
        //echo $SQLselect;

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $facture = static::build($data);
            array_push($items, $facture);
        }
        return $items;
    }

    public function getItems()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table2 . " WHERE id_facture = %s ORDER BY ordre ASC",
            GetSQLValueString($this->id, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item_facture = item_facture::find($data['id']);
            array_push($items, $item_facture);
        }
        return $items;
    }

    public function getReste()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT SUM(montant) AS total FROM " . static::$table3 . " WHERE id_facture = %s",
            GetSQLValueString($this->id, "int")
        );
        $result = $db->query($SQLselect);
        $data = $db->fetch_array($result);

        return $this->total - $data['total'];
    }

    public function checkPayment()
    {
        if ($this->getReste() <= 0)
            $this->statu = 1;
        elseif ($this->getReste() < $this->total)
            $this->statu = 2;
        else
            $this->statu = 0;

        $this->edit();
    }

    public function getTotalItems()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            
            $total += $item->getTotal();
        }

        // test réduction
        if ($this->discount == 'percentage') {
            $total = $total - ($total * $this->discount_val / 100);
        } elseif ($this->discount == 'amount') {
            $total = $total - $this->discount_val;
        }

        // TVA
        $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
        $total += $total * ($agence->getTva()/100);

        return $total;
    }

    public function setTotalItems()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $discount = $item->getDiscount()>0 ? ($item->getDiscount() * $item->getTotal()) / 100 : 0;
            $total += ($item->getTotal() - $discount);
        }

        // test réduction
        if ($this->discount == 'percentage') {
            $total = $total - ($total * $this->discount_val / 100);
        } elseif ($this->discount == 'amount') {
            $total = $total - $this->discount_val;
        }

        // TVA
        $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
        if (!$this->isProforma()) $total += $total * ($agence->getTva()/100);

        $this->total = $total;
    }

    public function generateNumero()
    {
        $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
        $date = new DateTime($this->date_add);
        $numero = $date->format('Y') . $date->format('m') . str_pad($agence->getNumeroIncrementFacture(), 4, '0', STR_PAD_LEFT);
        if ($this->isProforma()) {
            $this->setSecNumero($numero);
        } else {
            $this->setNumero($numero);
        }
    }

    //
    public function getLastNumeroNotNull()
    {
        global $db;
        $SQLNumero = "SELECT numero FROM " . static::$table . " WHERE numero IS NOT NULL ORDER BY `id`  DESC LIMIT 1";
        $result = $db->query($SQLNumero);
        $data = $db->fetch_assoc($result);
        return $data["numero"];
    }



    public static function build($data)
    {
        $facture = new facture();
        $facture->setId($data['ID']);
        $facture->setUserAdded(isset($data['id_user_added']) ? user::find($data['id_user_added']) : new user());
        $facture->setUserEdited(isset($data['id_user_edited']) ? user::find($data['id_user_edited']) : new user());
        $facture->setBank(isset($data['id_bank']) ? bank::find($data['id_bank']) : new bank());
        $facture->setNumero($data['numero']);
        $facture->setSecNumero($data['sec_numero']);
        $facture->setClient(isset($data['id_client']) ? client::findAny($data['id_client']) : new client());
        $facture->setDevis(isset($data['id_devis']) ? devis::find($data['id_devis'], $_SESSION['agence']) : new devis);
        $facture->setDateFacture($data['date_facture']);
        $facture->setTotal($data['total']);
        $facture->setStatu($data['statu']);
        $facture->setDevise($data['devise']);
        $facture->setDiscount($data['discount']);
        $facture->setDiscountVal($data['discount_val']);
        $facture->setConditionPaiment($data['condition_paiment']);
        $facture->setRemarque($data['remarque']);
        $facture->setRemarqueMoi($data['remarque_moi']);
        $facture->setAdditionalInformation($data['additional_information']);
        $facture->setProforma($data['proforma']);
        $facture->setShowSignature($data['show_signature']);
        $facture->setLangue($data['langue']);
        $facture->setDateDebut($data['date_debut']);
        $facture->setDateFin($data['date_fin']);
        $facture->setDateAdd($data['date_add']);
        $facture->setLastEdit($data['last_edit']);
        $facture->setAvoir($data['avoir']);
        $facture->setArchived($data['archived']);
        $facture->setIdFacture($data['id_facture']);
        $facture->setCountSending($data['count_sending']);
        return $facture;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
        //155058/139558
    }

    public static function getCreance($year = false, $devise = 'DH', $agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT SUM(A.montant) AS totalpayment FROM " . static::$table3 . " A JOIN " . static::$table . " B ON A.id_facture = B.id inner join " . static::$table5 . " C on B.id_client = C.id inner join " . static::$table4 . " D on C.id_agence = D.id WHERE D.id = $agence AND (B.archived IS NULL OR B.archived = 0) AND B.devise = '$devise'");
        if ($year) {
            $SQLselect .= " AND YEAR(B.date_facture) = " . intval($year);
        }
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (B.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        $result = $db->query($SQLselect);
        $data = $db->fetch_array($result);
        $totalPayment = $data['totalpayment'];
        $SQLselect = sprintf("SELECT SUM(A.total) AS totalfacture FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence AND (A.archived IS NULL OR A.archived = 0) AND A.devise = '$devise'");
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($year) {
            $SQLselect .= " AND YEAR(A.date_facture) = " . intval($year);
        }
        $result = $db->query($SQLselect);
        $data = $db->fetch_array($result);
        $totalFacture = $data['totalfacture'];
        return $totalFacture - $totalPayment;
    }

    public static function getCreanceByDate($from = false, $to = false, $devise = 'DH', $agence = 1)
    {
        global $db;
        $items = array();
        
        if($agence)
            $SQLselect = sprintf("SELECT SUM(A.montant) AS totalpayment FROM " . static::$table3 . " A INNER JOIN " . static::$table . " B ON A.id_facture = B.id inner join " . static::$table5 . " C on B.id_client = C.id inner join " . static::$table4 . " D on C.id_agence = D.id WHERE D.id = $agence AND (B.archived IS NULL OR B.archived = 0) AND B.devise = '$devise'");
        else
            $SQLselect = sprintf("SELECT SUM(A.montant) AS totalpayment FROM " . static::$table3 . " A INNER JOIN " . static::$table . " B ON A.id_facture = B.id inner join " . static::$table5 . " C on B.id_client = C.id inner join " . static::$table4 . " D on C.id_agence = D.id WHERE (B.archived IS NULL OR B.archived = 0) AND B.devise = '$devise'");
        
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (B.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($from) {
            $SQLselect .= " AND B.date_facture >= '$from'";
        }
        if ($to) {
            $SQLselect .= " AND B.date_facture <= '$to'";
        }
        //echo $SQLselect;
        $result = $db->query($SQLselect);
        $data = $db->fetch_array($result);
        $totalPayment = $data['totalpayment'];

        if($agence)
            $SQLselect = sprintf("SELECT SUM(A.total) AS totalfacture FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence AND (A.archived IS NULL OR A.archived = 0) AND A.devise = '$devise'");
        else
            $SQLselect = sprintf("SELECT SUM(A.total) AS totalfacture FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE (A.archived IS NULL OR A.archived = 0) AND A.devise = '$devise'");
            
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($from) {
            $SQLselect .= " AND A.date_facture >= '$from'";
        }
        if ($to) {
            $SQLselect .= " AND A.date_facture <= '$to'";
        }
        $result = $db->query($SQLselect);
        $data = $db->fetch_array($result);
        $totalFacture = $data['totalfacture'];

        return $totalFacture - $totalPayment;
    }

    public static function count($statu = false, $year = false, $client = false, $agence = 1)
    {
        global $db;
        $SQLcount = "SELECT count(A.id) as c FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence and (A.archived IS NULL OR A.archived = 0)";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLcount .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($client) {
            $SQLcount .= " AND A.id_client = " . intval($client);
        }

        if ($statu) {
            if ($statu == 3)
                $SQLcount .= " AND (A.statu = NULL OR A.statu = 0)";
            else
                $SQLcount .= " AND A.statu = " . intval($statu);
        }
        if ($year) {
            $SQLcount .= " AND YEAR(A.date_facture) = " . intval($year);
        }
        //echo $SQLcount;
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function facturePerYearClient($statu = false, $year = false, $client = false, $agence = 1)
    {
        global $db;
        $factures = array();
        $SQLcount = "SELECT A.id FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence AND (A.archived IS NULL OR A.archived = 0)";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLcount .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($client) {
            $SQLcount .= " AND A.id_client = " . intval($client);
        }

        if ($statu) {
            if ($statu == 3)
                $SQLcount .= " AND (A.statu = NULL OR A.statu = 0)";
            else
                $SQLcount .= " AND A.statu = " . intval($statu);
        }
        if ($year) {
            $SQLcount .= " AND YEAR(A.date_facture) = " . intval($year);
        }

        //echo $SQLcount;
        $result = $db->queryS($SQLcount);
        foreach ($result as $data) {
            $facture = static::find($data['id']);
            array_push($factures, $facture);
        }
        /*if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }*/
        return $factures;
    }

    public static function total($statu = false, $year = false, $devise = 'DH', $agence = 1)
    {
        global $db;
        $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence and (A.archived IS NULL OR A.archived = 0) AND A.devise = '$devise'";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLcount .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($statu) {
            if ($statu == 3)
                $SQLcount .= " AND A.statu = NULL";
            else
                $SQLcount .= " AND A.statu = " . intval($statu);
        }
        if ($year) {
            $SQLcount .= " AND YEAR(A.date_facture) = " . intval($year);
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function getCAbyDate($statu = false, $from = false, $to = false, $devise = 'DH', $agence = 1)
    {
        global $db;
        
        if($agence)
            $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence AND (A.archived IS NULL OR A.archived = 0) AND A.devise = '$devise'";
        else
            $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE (A.archived IS NULL OR A.archived = 0) AND A.devise = '$devise'";
            
            
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLcount .= " AND (A.id_user_added = " . $_SESSION['user']->getId() . " )";
        }
        if ($statu) {
            if ($statu == 3)
                $SQLcount .= " AND A.statu = NULL";
            else
                $SQLcount .= " AND A.statu = " . intval($statu);
        }
        if ($from) {
            $SQLcount .= " AND A.date_facture >= '$from'";
        }
        if ($to) {
            $SQLcount .= " AND A.date_facture <= '$to'";
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public function getPayments()
    {
        return \payment::findAll($this->id);
    }

    public function getTva()
    {
        $tva = null;
        if (!$this->isProforma()) {
            $tva = ($this->getTotal() - $this->getTotal() / 1.2);
        }
        return $tva;
    }
    private static function formatPrice($price)
    {

        if ($price == 0 || !$price || empty($price) || !is_numeric($price)) {
            return '0,00';
        }
        return number_format($price, 2, ',', ' ');
    }
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'id_client' => $this->getClient()->getId(),
            'numero' => $this->getNumero(),
            'date_facture' => $this->getDateFacture(),
            'devise' => $this->getDevise(),
            'discount' => $this->getDiscount() != '' ? ($this->getDiscount() . ($this->getDiscount() == 'amount' ? ' ' . $this->getDevise() : '%')) : null,
            'total' => self::formatPrice($this->getTotal()),
            'reste' => self::formatPrice($this->getReste()),
            'statu' => $this->getStatu(),
            'pdf' => 'https://www.helloworld-agency.com/hw-label/new/components/com_facture/controleurs/router.php?task=pdfFacture&id=' . $this->getId(),
        );
    }
    function sendMailFacture($output = "show")
    {
        global $db;
        if (isset($data["id"]) && !empty($data["id"])) {

            require_once '../../../vendor/autoload.php';
            require_once '../../../includes/traduction.php';

            $dirPath = $output == "show" ? "../../../../" : "../../../";

            $facture = facture::find($data["id"], $_SESSION['agence']);
            $typefacture = $traduction['FACTURE'][$facture->getLangue()];
            if ($facture->getIdFacture()) {
                $facture_ref = facture::find($facture->getIdFacture(), $_SESSION['agence']);
                $typefacture = $traduction['AVOIR'][$facture->getLangue()];
            }
            $items = $facture->getItems();
            $client = $facture->getClient();
            $config = new config($db);
            $invoiceFor = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();

            $mpdf = new \Mpdf\Mpdf();

            $htmlInvoice = '<html>
<head>
<style>
body {
    font-family: montserrat;
    font-size: 10pt;
}
p {	margin: 0pt; }
table.items {
}
td { vertical-align: top; }
.items td {
    border-left: 0.1mm solid #FFF;
    border-right: 0.1mm solid #FFF;
    border-bottom: 0.1mm solid #CCC;
}
table thead td { background-color: #EEEEEE;
    text-align: center;
    border-left: 0.1mm solid #FFF;
    border-right: 0.1mm solid #FFF;
}
.items td.blanktotal {
    background-color: #EEEEEE;
    border: 0.1mm solid #FFF;
    background-color: #FFFFFF;
    border: 0mm none #000000;
}
.items td.totals {
    text-align: right;
    border-bottom: 0.1mm solid #CCC;
}
.items td.cost {
    text-align: "." center;
}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
    <td><img src="' . $dirPath . 'images/agences/' . $agence->getLogo() . '" width="100"></td>
    <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
    <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> ' . $agence->getWebsite() . '</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">
<p style="font-size:8pt;"><strong>IF</strong> ' . $agence->getIf() . ' | <strong>TP</strong> ' . $agence->getTp() . ' | <strong>RC</strong> ' . $agence->getRc() . ' | <strong>ICE</strong> ' . $agence->getIce() . '</p>
<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR'][$facture->getLangue()] . ' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">' . (isset($facture_ref) ? $traduction['AVOIR_FACTURE'][$facture->getLangue()] . ' N° ' . $facture_ref->getNumero() : $traduction['FACTURE_POUR'][$facture->getLangue()]) . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:#e3d3aa">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Total ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Date ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($facture->getDateFacture()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $facture->getNumero() . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">' . $traduction['PRIX_HT'][$facture->getLangue()] . '</td>
<td width="20%">' . $traduction['QTE'][$facture->getLangue()] . '</td>
<td width="20%" align="right">' . $traduction['TOTAL_HT'][$facture->getLangue()] . '</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
            $soustotal = 0;
            foreach ($items as $item) {
                $soustotal += $item->getTotal();
                $htmlInvoice .= '<tr>
<td><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
<td align="center" style="vertical-align:middle;">' . number_format($item->getPrix(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
<td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . getUnities()[$facture->getLangue()][$item->getUnite()] . '</td>
<td align="right" style="vertical-align:middle;" class="cost">' . number_format($item->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';
            }


            //$sstotal = $facture->isProforma() ? $facture->getTotal() : $soustotal;
            $sstotal = $soustotal;

            // Calcule Réduction
            if ($facture->getDiscount() != '') {
                $discoutSign = $facture->getDiscount() == 'amount' ? ' ' . $facture->getDevise() : '%';
                // test réduction
                if ($facture->getDiscount() == 'percentage') {
                    $soustotal = $soustotal - ($soustotal * $facture->getDiscountVal() / 100);
                } elseif ($facture->getDiscount() == 'amount') {
                    $soustotal = $soustotal - $facture->getDiscountVal();
                }

                echo '<tr>
<td class="totals">' . $traduction['REDUCTION'][$facture->getLangue()] . '</td>
<td class="totals cost">- ' . $facture->getDiscountVal() . $discoutSign . '</td>
</tr>';
            }


            $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">' . $traduction['SSTOTAL_HT'][$facture->getLangue()] . '</td>
<td class="totals cost">' . number_format($sstotal, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';



            // Calcule TVA		
            $tva = $facture->isProforma() ? 0 : $soustotal * $agence->getTva() / 100;

            $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TVA'][$facture->getLangue()] . '</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';

            if ($facture->getDiscount() != '') {
                $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TOTAL_TTC'][$facture->getLangue()] . '</td>
<td class="totals cost"> ' . number_format($sstotal + $tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';
            }










            // Calcule TVA		
            /*$tva = $facture->isProforma() ? 0 : $soustotal * 0.2;
        $htmlInvoice .= '<tr>
<td class="totals">'.$traduction['TVA'][$facture->getLangue()].'</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';

        if ($facture->getDiscount() != '') {
            $discoutSign = $facture->getDiscount() == 'amount' ? ' ' . $facture->getDevise() : '%';
            // test réduction
            if ($facture->getDiscount() == 'percentage') {
                $soustotal = $soustotal - ($soustotal * $facture->getDiscountVal / 100);
            } elseif ($facture->getDiscount() == 'amount') {
                $soustotal = $soustotal - $facture->getDiscountVal;
            }

            $htmlInvoice .= '<tr>
<td class="totals">'.$traduction['TOTAL_TTC'][$facture->getLangue()].'</td>
<td class="totals cost"> ' . number_format($sstotal + $tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr><tr>
<td class="totals">'.$traduction['REDUCTION'][$facture->getLangue()].'</td>
<td class="totals cost">- ' . $facture->getDiscountVal() . $discoutSign . '</td>
</tr>';
        }*/

            $totalLabel = $facture->isProforma() ? 'TOTAL' : $traduction['TOTAL_TTC'][$facture->getLangue()];
            $htmlInvoice .= '<tr style="background:#e3d3aa;">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
            if ($facture->getRemarque() != '') {
                $htmlInvoice .= '<strong>' . $traduction['REMARQUE'][$facture->getLangue()] . ': </strong>' . $facture->getRemarque();
            };
            $htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:#e3d3aa;">' . $traduction['MERCI_HW'][$facture->getLangue()] . ' !!</h3>
<p style="font-size:8pt;"><strong>' . $agence->getConditions() . '</p>
<p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS_PAYMENT'][$facture->getLangue()] . ': </strong>' . $facture->getConditionPaiment() . '</p>
</div>
</body>
</html>';

            $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new \Mpdf\Mpdf([
                'margin_left' => 20,
                'margin_right' => 15,
                'margin_top' => 48,
                'margin_bottom' => 25,
                'margin_header' => 10,
                'margin_footer' => 10,

                'fontDir' => array_merge($fontDirs, [
                    '../../../fonts/',
                ]),
                'fontdata' => $fontData + [
                    'montserrat' => [
                        'R' => 'Montserrat-Regular.ttf',
                        'B' => 'Montserrat-Bold.ttf',
                    ]
                ],
                'default_font' => 'montserrat'
            ]);

            $mpdf->SetProtection(array('print', 'copy'));
            $mpdf->SetTitle("Facture #" . $facture->getNumero());
            $mpdf->SetAuthor("Verse Concept");
            $mpdf->SetWatermarkText("");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.05;
            $mpdf->SetDisplayMode('fullpage');

            $mpdf->WriteHTML($htmlInvoice);

            $file_name = $traduction['FACTURE'][$facture->getLangue()] . ' - ' . $invoiceFor . '.pdf';
            $mpdf->Output('I', $file_name);
        }
    }

    function sendViaMailFacture($file_name = "")
    {

        require '../../../vendor/autoload.php';
        require '../../../includes/traduction.php';

        global $db, $siteURL;
        $config = new config($db);
        $mail = new PHPMailer();
        // Authentifié en SMTP (au lieu du relais local mail() par défaut) pour que ce mail soit
        // réellement envoyé depuis la boîte sales@ - condition nécessaire pour pouvoir ensuite en
        // déposer une copie dans son dossier "Envoyés" (cf. copierEmailEnvoyeVersDossierEnvoyes()).
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $client = $this->getClient();
        $mailBody = '<html>
    <body>
    <table border="0" width="100%">
        <tr>
            <td bgcolor="#F6F6F6" align="center">
                <table border="0" cellpadding="15" cellspacing="0" width="640">
                    <tr>
                        <td align="center"><img src="' . $siteURL . 'images/config/' . $config->getLogo() . '" width="100"></td>
                    </tr>

                    <tr bgcolor="#FFFFFF">
                        <td align="center">
                        <h1 style="font-weight:normal; margin-bottom:15px;margin-top:20px">
                        Facture N° ' . $this->getNumero() . '
                        </h1>
                    </td>
                    
                </tr>

                <tr bgcolor="#FFFFFF" style="font-size:20px;">
                    <td align="center">
                    Bonjour, Je vous prie de bien vouloir trouver ci-joint <span style="color:#15c;">la facture </span>relative aux services demandés. Cordialement.
                    </td>
                    </tr>

                    <tr>
                        <td align="center">
                            <p>
                                <font size="2" color="#666666"><br />
                                Hello World Contact
                                <!--footer info--><br/>
                                Email : ' . $config->getEmail() . '
                                <br>
                                Tél : ' . $config->getTel() . ' / ' . $config->getTel2() . '
                            </p>
                        </td>
                    </tr>
                    
                </table>

            </td>
        </tr>
    </table>
</body>

</html>';

        //Set who the message is to be sent from
        $mail->setFrom("sales@helloworld-agency.com");
        //Set an alternative reply-to address
        $mail->addReplyTo($config->getEmail(), $config->getNom());
        //Set who the message is to be sent to
        $mail->addAddress($client->getEmail(), $client->getNom() . ' ' . $client->getPrenom());
        $mail->addAddress("sales@helloworld-agency.com");
        //Set the subject line
        $mail->Subject = 'Facture ' . $config->getNom();
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($mailBody);
        //Attach an image file

        if ($file_name != '') $mail->addAttachment('../../../uploads/' . $file_name);

        if ($mail->send()) {
            copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
            echo "success";
        } else {
            echo "Error: " . $mail->ErrorInfo;
        }
    }




    function pdfFacture($output = "show")
    {
        global $db;


        require '../../../vendor/autoload.php';
        require '../../../includes/traduction.php';

        $dirPath = $output == "show" ? "../../../" : "../../../";



        $facture = $this;
        $typefacture = $traduction['FACTURE'][$facture->getLangue()];
        if ($facture->getIdFacture()) {
            $facture_ref = facture::find($facture->getIdFacture(), $_SESSION['agence']);
            $typefacture = $traduction['AVOIR'][$facture->getLangue()];
        }
        $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
        $items = $facture->getItems();
        $client = $facture->getClient();
        $config = new config($db);
        $invoiceFor = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();
        $color = $agence->getColor();
        $mpdf = new \Mpdf\Mpdf();

        $htmlInvoice = '<html>
<head>
<style>
body {
    font-family: montserrat;
    font-size: 10pt;
}
p {	margin: 0pt; }
table.items {
}
td { vertical-align: top; }
.items td {
    border-left: 0.1mm solid #FFF;
    border-right: 0.1mm solid #FFF;
    border-bottom: 0.1mm solid #CCC;
}
table thead td { background-color: #EEEEEE;
    text-align: center;
    border-left: 0.1mm solid #FFF;
    border-right: 0.1mm solid #FFF;
}
.items td.blanktotal {
    background-color: #EEEEEE;
    border: 0.1mm solid #FFF;
    background-color: #FFFFFF;
    border: 0mm none #000000;
}
.items td.totals {
    text-align: right;
    border-bottom: 0.1mm solid #CCC;
}
.items td.cost {
    text-align: "." center;
}
.div-signature{
    text-align:right;
}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
    <td><img src="' . $dirPath . 'images/agences/' . $agence->getLogo() . '" width="100"></td>
    <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
    <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> ' . $agence->getWebsite() . '</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

        if ($agence->getIce() != '') {
            $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> ' . $agence->getIf() . ' | <strong>TP</strong> ' . $agence->getTp() . ' | <strong>RC</strong> ' . $agence->getRc() . ' | <strong>ICE</strong> ' . $agence->getIce() . '</p>';
        }

        $htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR'][$facture->getLangue()] . ' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">' . (isset($facture_ref) ? $traduction['AVOIR_FACTURE'][$facture->getLangue()] . ' N° ' . $facture_ref->getNumero() : $traduction['FACTURE_POUR'][$facture->getLangue()]) . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:' . $color . '">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Total ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Date ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($facture->getDateFacture()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $facture->getNumero() . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />';
if($facture->getAdditionalInformation()){
    $htmlInvoice .= '<p>'.$facture->getAdditionalInformation().'</p><br />';
}

$htmlInvoice .= '<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">' . $traduction['PRIX_HT'][$facture->getLangue()] . '</td>
<td width="20%">' . $traduction['QTE'][$facture->getLangue()] . '</td>
<td width="20%" align="right">' . $traduction['TOTAL_HT'][$facture->getLangue()] . '</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
        $soustotal = 0;
        foreach ($items as $item) {
            $discount = $item->getDiscount()>0 ? ($item->getDiscount() * $item->getTotal()) / 100 : 0;
            $soustotal += ($item->getTotal() - $discount);
            $htmlInvoice .= '<tr>
                                <td><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
                                <td align="center" style="vertical-align:middle;">' . number_format($item->getPrix(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
                                <td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . getUnities()[$facture->getLangue()][$item->getUnite()] . '</td>
                                <td align="right" style="vertical-align:middle;" class="cost">' . ($item->getDiscount() > 0 ?  '<del>'.number_format($item->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise().'</del>' : number_format($item->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise()) . '</td>
                                </tr>';
                                if($item->getDiscount() > 0){
                                    $htmlInvoice .= '<tr>
                                        <td colspan="3" style="color:#4caf50"><strong>'.str_replace(array('text_value','discount_value'),array($item->getTitre(),$item->getDiscount()),$traduction['PRICE_AFTER_DISCOUNT'][$facture->getLangue()]).'</strong></td>
                                        <td align="right"  style="color:#4caf50"><strong>' . number_format($item->getTotal() - $discount , 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td>
                                    </tr>';
                                }
        }

        //$sstotal = $facture->isProforma() ? $facture->getTotal() : $soustotal;

        $sstotal = $soustotal;

        $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">' . $traduction['SSTOTAL_HT'][$facture->getLangue()] . '</td>
<td class="totals cost">' . number_format($sstotal, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';

        // Calcule TVA		
        $tva = $facture->isProforma() ? 0 : $sstotal * $agence->getTva() / 100;

        $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TVA'][$facture->getLangue()] . '</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';

        if ($facture->getDiscount() != '') {
            $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TOTAL_TTC'][$facture->getLangue()] . '</td>
<td class="totals cost"> ' . number_format($sstotal + $tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
</tr>';
        }

        // Calcule Réduction
        if ($facture->getDiscount() != '') {
            $discoutSign = $facture->getDiscount() == 'amount' ? ' ' . $facture->getDevise() : '%';
            // test réduction
            if ($facture->getDiscount() == 'percentage') {
                $sstotal = $sstotal - ($sstotal * $facture->getDiscountVal() / 100);
            } elseif ($facture->getDiscount() == 'amount') {
                $sstotal = $sstotal - $facture->getDiscountVal();
            }
            
            $htmlInvoice .=  '<tr>
        <td class="totals">'.$traduction['REDUCTION'][$facture->getLangue()].'</td>
        <td class="totals cost">- ' . $facture->getDiscountVal() . $discoutSign . '</td>
        </tr>';	
        }




        $totalLabel = $facture->isProforma() ? $traduction['TOTAL'][$facture->getLangue()] : $traduction['TOTAL_TTC'][$facture->getLangue()];
        $htmlInvoice .= '<tr style="background:' . $color . ';">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:10t;"><p style="font-size:8pt;">';
        if ($facture->getRemarque() != '') {
            $htmlInvoice .= '<strong>' . $traduction['REMARQUE'][$facture->getLangue()] . ': </strong>' . strip_tags($facture->getRemarque());
        };
        $htmlInvoice .= '</p></div>
<div style="margin-top:60t;">
<h3 style="color:' . $color . ';">' . $traduction['THANK_YOU_FOR'][$facture->getLangue()] . $agence->getNom() . ' !!</h3>
<p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS'][$facture->getLangue()] . ': </strong>' . $agence->getConditions() . '</p>
<p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS_PAYMENT'][$facture->getLangue()] . ': </strong>' . $facture->getConditionPaiment() . '</p>';

        if ($facture->getBank()) {
            $htmlInvoice .= '<p style="font-size:8pt;"><br>';
            if ($facture->getBank()->getRaisonSociale() != '') $htmlInvoice .= '<strong>' . $traduction['RAISON_SOCIALE'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getRaisonSociale() . ' <br>';
            if ($facture->getBank()->getRib() != '') $htmlInvoice .= '<strong>' . $traduction['ACCOUNT_NUMBER'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getRib() . ' <br>';
            if ($facture->getBank()->getIbanNumber() != '') $htmlInvoice .= '<strong>' . $traduction['IBAN'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getIbanNumber() . ' <br>';
            if ($facture->getBank()->getBanque() != '') $htmlInvoice .= '<strong>' . $traduction['BRANCH'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getBanque() . ' <br>';
            if ($facture->getBank()->getCodeSwift() != '') $htmlInvoice .= '<strong>' . $traduction['SWIFT_CODE'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getCodeSwift() . ' <br>';
            if ($facture->getBank()->getCurrency() != '') $htmlInvoice .= '<strong>' . $traduction['CURRENCY'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getCurrency() . ' <br>';

            $htmlInvoice .= '</p>';
        }
        
        if($facture->getShowSignature()){
            $htmlInvoice .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 0px;">
                                <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="250">
                            </div>';
        }

        $htmlInvoice .= '</div>
</body>
</html>';

        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10,

            'fontDir' => array_merge($fontDirs, [
                '../../../fonts/',
            ]),
            'fontdata' => $fontData + [
                'montserrat' => [
                    'R' => 'Montserrat-Regular.ttf',
                    'B' => 'Montserrat-Bold.ttf',
                ]
            ],
            'default_font' => 'montserrat'
        ]);

        $mpdf->SetProtection(array('print', 'copy'));
        $mpdf->SetTitle("Facture #" . $facture->getNumero());
        $mpdf->SetAuthor("Hello World");
        $mpdf->SetWatermarkText("");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.05;
        $mpdf->SetDisplayMode('fullpage');

        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($htmlInvoice);

        $file_name = $traduction['FACTURE'][$facture->getLangue()] . ' - ' . str_replace(array('/', '\\'), '-', $invoiceFor) . '.pdf';
        if ($output == "show") {

            $mpdf->Output($file_name, 'I');
        } else {


            $dirPath = $dirPath . "uploads/";
            $mpdf->Output($dirPath . $file_name, 'F');
            return $file_name;
        }
    }
    
    // $zipExterne (optionnel) : permet à un appelant (ex. export comptable TVA) d'ajouter les
    // PDF de factures à un ZipArchive qu'il gère lui-même (déjà ouvert, fermé/téléchargé par
    // lui) au lieu de créer/télécharger un ZIP dédié — dans ce mode, la fonction renvoie la
    // liste des PDF temporaires écrits sur disque (à l'appelant de les supprimer après avoir
    // fermé son ZIP, puisque ZipArchive ne lit le contenu des fichiers ajoutés qu'à la fermeture).
    public static function pdfFactures($factures, $zipExterne = null)
    {
        global $db;

        require '../../../vendor/autoload.php';
        require '../../../includes/traduction.php';

        $dirPath = "../../../";

        $zip = $zipExterne !== null ? $zipExterne : new ZipArchive();
        $zipFileName = "factures-" . date("Y-m-d H:i:s") . ".zip";
        $tempFiles = array();

        if ($zipExterne !== null || $zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($factures as $key => $facture) {
                $typefacture = $traduction['FACTURE'][$facture->getLangue()];
                if ($facture->getIdFacture()) {
                    $facture_ref = facture::find($facture->getIdFacture(), $_SESSION['agence']);
                    $typefacture = $traduction['AVOIR'][$facture->getLangue()];
                }
                $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
                $items = $facture->getItems();
                $client = $facture->getClient();
                $config = new config($db);
                $invoiceFor = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();
                $color = $agence->getColor();
                $mpdf = new \Mpdf\Mpdf();

                $htmlInvoice = '<html>
            <head>
            <style>
            body {
                font-family: montserrat;
                font-size: 10pt;
            }
            p {	margin: 0pt; }
            table.items {
            }
            td { vertical-align: top; }
            .items td {
                border-left: 0.1mm solid #FFF;
                border-right: 0.1mm solid #FFF;
                border-bottom: 0.1mm solid #CCC;
            }
            table thead td { background-color: #EEEEEE;
                text-align: center;
                border-left: 0.1mm solid #FFF;
                border-right: 0.1mm solid #FFF;
            }
            .items td.blanktotal {
                background-color: #EEEEEE;
                border: 0.1mm solid #FFF;
                background-color: #FFFFFF;
                border: 0mm none #000000;
            }
            .items td.totals {
                text-align: right;
                border-bottom: 0.1mm solid #CCC;
            }
            .items td.cost {
                text-align: "." center;
            }
            .div-signature{
                text-align:right;
            }
            </style>
            </head>
            <body>
            <!--mpdf
            <htmlpageheader name="myheader">
            <table width="100%">
            <tr>
                <td><img src="' . $dirPath . 'images/agences/' . $agence->getLogo() . '" width="100"></td>
                <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
                <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> ' . $agence->getWebsite() . '</p></td>
            </tr>
            </table>
            <hr>
            </htmlpageheader>
            <htmlpagefooter name="myfooter">
            <div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

                if ($agence->getIce() != '') {
                    $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> ' . $agence->getIf() . ' | <strong>TP</strong> ' . $agence->getTp() . ' | <strong>RC</strong> ' . $agence->getRc() . ' | <strong>ICE</strong> ' . $agence->getIce() . '</p>';
                }

                $htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR'][$facture->getLangue()] . ' {nb}</div>
            </div>
            </htmlpagefooter>
            <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
            <sethtmlpagefooter name="myfooter" value="on" />
            mpdf-->
            <table width="100%">
            <tr>
            <td width="35%" style="vertical-align: middle; font-size:8pt;">' . (isset($facture_ref) ? $traduction['AVOIR_FACTURE'][$facture->getLangue()] . ' N° ' . $facture_ref->getNumero() : $traduction['FACTURE_POUR'][$facture->getLangue()]) . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:' . $color . '">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
            <td width="30%"></td>

            <td width="35%" style="text-align: right;">

            <table style="margin-bottom:5pt;">
            <tr><td style="font-size:8pt;">Total ' . $typefacture . '</td></tr>
            <tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td></tr>
            </table>

            <table style="margin-bottom:5pt;">
            <tr><td style="font-size:8pt;">Date ' . $typefacture . '</td></tr>
            <tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($facture->getDateFacture()) . '</strong></td></tr>
            </table>

            <table>
            <tr><td style="font-size:8pt;">N° ' . $typefacture . '</td></tr>
            <tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $facture->getNumero() . '</strong></td></tr>
            </table>
            </td>
            </tr></table>
            <br />';
            if($facture->getAdditionalInformation()){
                $htmlInvoice .= '<p>'.$facture->getAdditionalInformation().'</p><br />';
            }
            
            $htmlInvoice .= '
            <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
            <thead>
            <tr>
            <td width="45%" style="text-align:left;">Description</td>
            <td width="15%">' . $traduction['PRIX_HT'][$facture->getLangue()] . '</td>
            <td width="20%">' . $traduction['QTE'][$facture->getLangue()] . '</td>
            <td width="20%" align="right">' . $traduction['TOTAL_HT'][$facture->getLangue()] . '</td>
            </tr>
            </thead>
            <tbody>
            <!-- ITEMS HERE -->';
                $soustotal = 0;
                foreach ($items as $item) {
                    $discount = $item->getDiscount() > 0 ? ($item->getDiscount() * $item->getTotal()) / 100 : 0;
                    $soustotal += ($item->getTotal() - $discount);
                    $htmlInvoice .= '<tr>
                                            <td><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
                                            <td align="center" style="vertical-align:middle;">' . number_format($item->getPrix(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
                                            <td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . getUnities()[$facture->getLangue()][$item->getUnite()] . '</td>
                                            <td align="right" style="vertical-align:middle;" class="cost">' . ($item->getDiscount() > 0 ?  '<del>' . number_format($item->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</del>' : number_format($item->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise()) . '</td>
                                            </tr>';
                    if ($item->getDiscount() > 0) {
                        $htmlInvoice .= '<tr>
                                                    <td colspan="3" style="color:#4caf50"><strong>' . str_replace(array('text_value', 'discount_value'), array($item->getTitre(), $item->getDiscount()), $traduction['PRICE_AFTER_DISCOUNT'][$facture->getLangue()]) . '</strong></td>
                                                    <td align="right"  style="color:#4caf50"><strong>' . number_format($item->getTotal() - $discount, 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td>
                                                </tr>';
                    }
                }

                //$sstotal = $facture->isProforma() ? $facture->getTotal() : $soustotal;

                $sstotal = $soustotal;

                $htmlInvoice .= '<!-- END ITEMS HERE -->
            <tr>
            <td class="blanktotal" colspan="2" rowspan="6"></td>
            <td class="totals">' . $traduction['SSTOTAL_HT'][$facture->getLangue()] . '</td>
            <td class="totals cost">' . number_format($sstotal, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
            </tr>';

                // Calcule TVA		
                $tva = $facture->isProforma() ? 0 : $sstotal * $agence->getTva() / 100;

                $htmlInvoice .= '<tr>
            <td class="totals">' . $traduction['TVA'][$facture->getLangue()] . '</td>
            <td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
            </tr>';

                if ($facture->getDiscount() != '') {
                    $htmlInvoice .= '<tr>
            <td class="totals">' . $traduction['TOTAL_TTC'][$facture->getLangue()] . '</td>
            <td class="totals cost"> ' . number_format($sstotal + $tva, 2, ',', ' ') . ' ' . $facture->getDevise() . '</td>
            </tr>';
                }

                // Calcule Réduction
                if ($facture->getDiscount() != '') {
                    $discoutSign = $facture->getDiscount() == 'amount' ? ' ' . $facture->getDevise() : '%';
                    // test réduction
                    if ($facture->getDiscount() == 'percentage') {
                        $sstotal = $sstotal - ($sstotal * $facture->getDiscountVal() / 100);
                    } elseif ($facture->getDiscount() == 'amount') {
                        $sstotal = $sstotal - $facture->getDiscountVal();
                    }

                    $htmlInvoice .=  '<tr>
                    <td class="totals">' . $traduction['REDUCTION'][$facture->getLangue()] . '</td>
                    <td class="totals cost">- ' . $facture->getDiscountVal() . $discoutSign . '</td>
                    </tr>';
                }




                $totalLabel = $facture->isProforma() ? $traduction['TOTAL'][$facture->getLangue()] : $traduction['TOTAL_TTC'][$facture->getLangue()];
                $htmlInvoice .= '<tr style="background:' . $color . ';">
            <td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
            <td class="totals cost" style="color:#FFF;"><strong>' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . '</strong></td>
            </tr>
            </tbody>
            </table><div style="margin-top:50t;"><p style="font-size:8pt;">';
                if ($facture->getRemarque() != '') {
                    $htmlInvoice .= '<strong>' . $traduction['REMARQUE'][$facture->getLangue()] . ': </strong>' . strip_tags($facture->getRemarque());
                };
                $htmlInvoice .= '</p></div>
            <div style="margin-top:100t;">
            <h3 style="color:' . $color . ';">' . $traduction['THANK_YOU_FOR'][$facture->getLangue()] . $agence->getNom() . ' !!</h3>
            <p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS'][$facture->getLangue()] . ': </strong>' . $agence->getConditions() . '</p>
            <p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS_PAYMENT'][$facture->getLangue()] . ': </strong>' . $facture->getConditionPaiment() . '</p>';

                if ($facture->getBank()) {
                    $htmlInvoice .= '<p style="font-size:8pt;"><br>';
                    if ($facture->getBank()->getRaisonSociale() != '') $htmlInvoice .= '<strong>' . $traduction['RAISON_SOCIALE'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getRaisonSociale() . ' <br>';
                    if ($facture->getBank()->getRib() != '') $htmlInvoice .= '<strong>' . $traduction['ACCOUNT_NUMBER'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getRib() . ' <br>';
                    if ($facture->getBank()->getIbanNumber() != '') $htmlInvoice .= '<strong>' . $traduction['IBAN'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getIbanNumber() . ' <br>';
                    if ($facture->getBank()->getBanque() != '') $htmlInvoice .= '<strong>' . $traduction['BRANCH'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getBanque() . ' <br>';
                    if ($facture->getBank()->getCodeSwift() != '') $htmlInvoice .= '<strong>' . $traduction['SWIFT_CODE'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getCodeSwift() . ' <br>';
                    if ($facture->getBank()->getCurrency() != '') $htmlInvoice .= '<strong>' . $traduction['CURRENCY'][$facture->getLangue()] . ':</strong> ' . $facture->getBank()->getCurrency() . ' <br>';

                    $htmlInvoice .= '</p>';
                }
                
                if($facture->getShowSignature()){
                    $htmlInvoice .= '<div class="div-signature" style="padding-top: 50px;padding-bottom: 50px;">
                                        <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="300">
                                    </div>';
                }

                $htmlInvoice .= '</div>
            </body>
            </html>';

                $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new \Mpdf\Mpdf([
                    'margin_left' => 20,
                    'margin_right' => 15,
                    'margin_top' => 48,
                    'margin_bottom' => 25,
                    'margin_header' => 10,
                    'margin_footer' => 10,

                    'fontDir' => array_merge($fontDirs, [
                        '../../../fonts/',
                    ]),
                    'fontdata' => $fontData + [
                        'montserrat' => [
                            'R' => 'Montserrat-Regular.ttf',
                            'B' => 'Montserrat-Bold.ttf',
                        ]
                    ],
                    'default_font' => 'montserrat'
                ]);

                $mpdf->SetProtection(array('print', 'copy'));
                $mpdf->SetTitle("Facture #" . $facture->getNumero());
                $mpdf->SetAuthor("Hello World");
                $mpdf->SetWatermarkText("");
                $mpdf->showWatermarkText = true;
                $mpdf->watermark_font = 'DejaVuSansCondensed';
                $mpdf->watermarkTextAlpha = 0.05;
                $mpdf->SetDisplayMode('fullpage');

                $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
                $mpdf->WriteHTML($htmlInvoice);

                // Numéro de facture inclus dans le nom : évite que deux factures d'un même
                // client (même $invoiceFor) ne s'écrasent l'une l'autre dans le ZIP.
                $pdfFileName = $traduction['FACTURE'][$facture->getLangue()] . ' ' . $facture->getNumero() . ' - ' . str_replace(array('/', '\\'), '-', $invoiceFor) . '.pdf';

                $dirPathFile = $dirPath . "uploads/";
                $mpdf->Output($dirPathFile.$pdfFileName, \Mpdf\Output\Destination::FILE);
                //    Add to ZIP
                if (file_exists($dirPathFile.$pdfFileName)) {
                    $zip->addFile($dirPathFile.$pdfFileName, basename($dirPathFile.$pdfFileName));
                    $tempFiles[] = $dirPathFile.$pdfFileName;
                }
            }
        }

        if ($zipExterne !== null) {
            return $tempFiles;
        }

        $zip->close();

        // Serve ZIP file for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . $zipFileName);
        readfile($zipFileName);

        // Cleanup - delete ZIP & individual PDFs
        unlink($zipFileName);
        foreach ($tempFiles as $tempFile) {
            @unlink($tempFile);
        }

        exit();
    }

    // API 

    public static function getResteApi($id, $total)
    {
        global $db;
        $SQLselect = sprintf(
            "SELECT SUM(montant) AS total FROM " . static::$table3 . " WHERE id_facture = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        $data = $db->fetch_array($result);

        return $total - $data['total'];
    }

    public static function buildApi($data)
    {
        $facture = array(
            'ID' => $data['ID'],
            'numero' => $data['numero'],
            'sec_numero' => $data['sec_numero'],
            'client' => client::ApiFindById($data['id_client']),
            'bank' => (isset($data['id_bank']) && !empty($data['id_bank'] && $data['id_bank'] != null) ? bank::ApiFindById($data['id_bank']) : array()),
            'devis' => (isset($data['id_devis']) && !empty($data['id_devis'] && $data['id_devis'] != null) ? devis::findByIdApi($data['id_devis']) : array()),
            'date_facture' => $data['date_facture'],
            'total' => $data['total'],
            'reste' => static::getResteApi($data['ID'], $data['total']),
            'statu' => $data['statu'],
            'devise' => $data['devise'],
            'discount' => $data['discount'],
            'discount_val' => $data['discount_val'],
            'condition_paiment' => $data['condition_paiment'],
            'remarque' => $data['remarque'],
            'remarque_moi' => $data['remarque_moi'],
            'additional_information' => $data['additional_information'],
            'proforma' => $data['proforma'],
            'show_signature' => $data['show_signature'],
            'langue' => $data['langue'],
            'pdf' => "com_facture/controleurs/router.php?task=pdfFactureApi&id=" . $data['ID'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
            'avoir' => $data['avoir'],
            'archived' => $data['archived'],
            'id_facture' => $data['id_facture'],
            'count_sending' => $data['count_sending'],
        );
        return $facture;
    }

    public static function findByIdApi($id)
    {
        if (getToken()) {
            global $db;
            $facture = new facture();
            $SQLselect = sprintf(
                "SELECT A.id as ID,A.*,B.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where A.id = %s",
                //GetSQLValueString($agence, "int"),
                GetSQLValueString($id, "int")
            );
            // echo $SQLselect;
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $facture = static::buildApi($data);
            }
            return $facture;
        } else {
            return json_encode(array("icon" => "error", "message" => "Unauthorized"));
        }
    }

    public static function findByDevisApi($id_devis)
    {
        if (getToken()) {
            global $db;
            $facture = new facture();
            $SQLselect = sprintf(
                "SELECT A.id as ID,A.*,B.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where (A.proforma = 0 or  A.proforma is null) and A.id_devis = %s",
                GetSQLValueString($id_devis, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $facture = static::buildApi($data);
            }
            return $facture;
        } else {
            return json_encode(array("icon" => "error", "message" => "Unauthorized"));
        }
    }

    public static function findAllByClientApi($clientID = 0, $from = false, $to = false, $ordre = false, $limit = false, $archived = false)
    {
        if (getToken()) {
            global $db;
            $items = array();
            $SQLselect = "SELECT A.id as ID,A.*,B.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where (A.proforma = 0 or  A.proforma is null)";

            if ($clientID) {
                $SQLselect .= " AND A.id_client = " . intval($clientID);
            }
            if ($from) {
                $SQLselect .= " AND A.date_facture >= '$from'";
            }
            if ($to) {
                $SQLselect .= " AND A.date_facture <= '$to'";
            }

            //facture avoir
            if ($archived) {
                $SQLselect .= " AND A.archived = 1";
            } else {
                $SQLselect .= " AND (A.archived = 0 OR A.archived IS NULL)";
            }
            //end

            if ($ordre) {
                $SQLselect .= " ORDER BY A.date_facture DESC";
            }
            if ($limit) {
                $SQLselect .= " LIMIT $limit";
            }
            //echo $SQLselect;

            $result = $db->queryS($SQLselect);
            foreach ($result as $data) {
                $facture = static::buildApi($data);
                array_push($items, $facture);
            }
            //var_dump($items);
            return $items;
        } else {
            return json_encode(array("icon" => "error", "message" => "Unauthorized"));
        }
    }

    public static function getItemsApi($id_facture)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table2 . " WHERE id_facture = %s ORDER BY ordre ASC",
            GetSQLValueString($id_facture, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item_facture = item_facture::findByIdApi($data['id']);
            array_push($items, $item_facture);
        }
        return $items;
    }

    public static function pdfFactureApi($id, $output = "show")
    {
        global $db;


        require '../../../vendor/autoload.php';
        require '../../../includes/traduction.php';

        $dirPath = $output == "show" ? "../../../" : "../../../";



        $facture = facture::findByIdApi($id);
        $typefacture = $traduction['FACTURE'][$facture["langue"]];
        if ($facture["id_facture"]) {
            $facture_ref = facture::findByIdApi($facture["id_facture"]);
            $typefacture = $traduction['AVOIR'][$facture["langue"]];
        }
        $client = $facture["client"];
        $agence = $client["agence"];
        $items = facture::getItemsApi($facture["ID"]);
        $config = new config($db);
        $invoiceFor = $client["raison_social"] != '' ? $client["raison_social"] : $client["nom"] . ' ' . $client["prenom"];
        $color = $agence["color"];

        $mpdf = new \Mpdf\Mpdf();

        $htmlInvoice = '<html>
<head>
<style>
body {
    font-family: montserrat;
    font-size: 10pt;
}
p {	margin: 0pt; }
table.items {
}
td { vertical-align: top; }
.items td {
    border-left: 0.1mm solid #FFF;
    border-right: 0.1mm solid #FFF;
    border-bottom: 0.1mm solid #CCC;
}
table thead td { background-color: #EEEEEE;
    text-align: center;
    border-left: 0.1mm solid #FFF;
    border-right: 0.1mm solid #FFF;
}
.items td.blanktotal {
    background-color: #EEEEEE;
    border: 0.1mm solid #FFF;
    background-color: #FFFFFF;
    border: 0mm none #000000;
}
.items td.totals {
    text-align: right;
    border-bottom: 0.1mm solid #CCC;
}
.items td.cost {
    text-align: "." center;
}
.div-signature{
    text-align:right;
}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
    <td><img src="' . $dirPath . 'images/agences/' . $agence["logo"] . '" width="100"></td>
    <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence["adresse"] . '</strong><br>
    <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence["tel"] . '  |  <strong>e:</strong> ' . $agence["email"] . ' | <strong>w:</strong> ' . $agence["website"] . '</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

        if ($agence["ice"] != '') {
            $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> ' . $agence["if"] . ' | <strong>TP</strong> ' . $agence["tp"] . ' | <strong>RC</strong> ' . $agence["rc"] . ' | <strong>ICE</strong> ' . $agence["ice"] . '</p>';
        }

        $htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR'][$facture["langue"]] . ' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">' . (isset($facture_ref) ? $traduction['AVOIR_FACTURE'][$facture["langue"]] . ' N° ' . $facture_ref["numero"] : $traduction['FACTURE_POUR'][$facture["langue"]]) . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:' . $color . '">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client["tel"] . '<br>' . $client["email"] . '<br>' . $client["ice"] . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Total ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($facture["total"], 2, ',', ' ') . ' ' . $facture["devise"] . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Date ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($facture["date_facture"]) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $typefacture . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $facture["numero"] . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />';
if($facture["additional_information"] != ''){
    $htmlInvoice .= '<p class="margin-bottom:20px">'.$facture["additional_information"].'</p>';
}

$htmlInvoice .= '
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">' . $traduction['PRIX_HT'][$facture["langue"]] . '</td>
<td width="20%">' . $traduction['QTE'][$facture["langue"]] . '</td>
<td width="20%" align="right">' . $traduction['TOTAL_HT'][$facture["langue"]] . '</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
        $soustotal = 0;
        foreach ($items as $item) {
            $discount = $item["discount"]>0 ? ($item["discount"] * $item["total"]) / 100 : 0;
            $soustotal += ($item["total"] - $discount);
            $htmlInvoice .= '<tr>
                                <td><strong>' . $item["titre"] . '</strong><div style="font-size:8pt; color:#999">' .  $item["description"] . '</div></td>
                                <td align="center" style="vertical-align:middle;">' . number_format($item["prix"], 2, ',', ' ') . ' ' . $facture["devise"] . '</td>
                                <td align="center" style="vertical-align:middle;">' . $item["qte"] . ' x ' . getUnities()[$facture["langue"]][$item["unite"]]  . '</td>
                                <td align="right" style="vertical-align:middle;" class="cost">' . number_format($item["total"], 2, ',', ' ') . ' ' . $facture["devise"] . '</td>
                                </tr>';
                                if($item["discount"] > 0){
                                    $htmlInvoice .= '<tr>
                                        <td colspan="3" style="color:#4caf50"><strong>'.str_replace(array('text_value','discount_value'),array($item["titre"],$item["discount"]),$traduction['PRICE_AFTER_DISCOUNT'][$facture["langue"]]).'</strong></td>
                                        <td align="right"  style="color:#4caf50"><strong>' . number_format($item["total"] - $discount , 2, ',', ' ') . ' ' . $facture["devise"] . '</strong></td>
                                    </tr>';
                                }
        }



        //$sstotal = $facture->isProforma() ? $facture->getTotal() : $soustotal;



        // Calcule Réduction
        if ($facture["discount"] != '') {
            $discoutSign = $facture["discount"] == 'amount' ? ' ' . $facture["devise"] : '%';
            // test réduction
            if ($facture["discount"] == 'percentage') {
                $soustotal = $soustotal - ($soustotal * $facture["discount_val"] / 100);
            } elseif ($facture["discount"] == 'amount') {
                $soustotal = $soustotal - $facture["discount_val"];
            }

            $htmlInvoice .=  '<tr>
            <td class="blanktotal" colspan="2"></td>
        <td class="totals">Discount</td>
        <td class="totals cost">- ' . $facture["discount_val"] . $discoutSign . '</td>
        </tr>';
        }

        $sstotal = $soustotal;

        $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">' . $traduction['SSTOTAL_HT'][$facture["langue"]] . '</td>
<td class="totals cost">' . number_format($soustotal, 2, ',', ' ') . ' ' . $facture["devise"] . '</td>
</tr>';



        // Calcule TVA		
        $tva = $facture["proforma"] ? 0 : $soustotal * $agence["tva"] / 100;

        $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TVA'][$facture["langue"]] . '</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $facture["devise"] . '</td>
</tr>';

        if ($facture["discount"] != '') {
            $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TOTAL_TTC'][$facture["langue"]] . '</td>
<td class="totals cost"> ' . number_format($sstotal + $tva, 2, ',', ' ') . ' ' . $facture["devise"] . '</td>
</tr>';
        }




        $totalLabel = $facture["proforma"] ? $traduction['TOTAL'][$facture["langue"]] : $traduction['TOTAL_TTC'][$facture["langue"]];
        $htmlInvoice .= '<tr style="background:' . $color . ';">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($facture["total"], 2, ',', ' ') . ' ' . $facture["devise"] . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
        if ($facture["remarque"] != '') {
            $htmlInvoice .= '<strong>' . $traduction['REMARQUE'][$facture["langue"]] . ': </strong>' . strip_tags($facture["remarque"]);
        };
        $htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:' . $color . ';">' . $traduction['THANK_YOU_FOR'][$facture["langue"]] . $agence["nom"] . ' !!</h3>
<p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS'][$facture["langue"]] . ': </strong>' . $agence["conditions"] . '</p>
<p style="font-size:8pt;"><strong>' . $traduction['CONDITIONS_PAYMENT'][$facture["langue"]] . ': </strong>' . $facture["condition_paiment"] . '</p>';

        if (is_array($facture["bank"]) && !empty($facture["bank"])) {
            $htmlInvoice .= '<p style="font-size:8pt;"><br>';
            if ($facture["bank"]["raison_sociale"] != '') $htmlInvoice .= '<strong>' . $traduction['RAISON_SOCIALE'][$facture["langue"]] . ':</strong> ' . $facture["bank"]["raison_sociale"] . ' <br>';
            if ($facture["bank"]["rib"] != '') $htmlInvoice .= '<strong>' . $traduction['ACCOUNT_NUMBER'][$facture["langue"]] . ':</strong> ' . $facture["bank"]["rib"] . ' <br>';
            if ($facture["bank"]["iban_number"] != '') $htmlInvoice .= '<strong>' . $traduction['IBAN'][$facture["langue"]] . ':</strong> ' . $facture["bank"]["iban_number"] . ' <br>';
            if ($facture["bank"]["banque"] != '') $htmlInvoice .= '<strong>' . $traduction['BRANCH'][$facture["langue"]] . ':</strong> ' . $facture["bank"]["banque"] . ' <br>';
            if ($facture["bank"]["code_swift"] != '') $htmlInvoice .= '<strong>' . $traduction['SWIFT_CODE'][$facture["langue"]] . ':</strong> ' . $facture["bank"]["code_swift"] . ' <br>';
            if ($facture["bank"]["currency"] != '') $htmlInvoice .= '<strong>' . $traduction['CURRENCY'][$facture["langue"]] . ':</strong> ' . $facture["bank"]["currency"] . ' <br>';

            $htmlInvoice .= '</p>';
        }
        
        $htmlInvoice .= '<div class="div-signature" style="padding-top: 50px;padding-bottom: 50px;">
                            <img src="' . $dirPath . 'images/agences/' . $agence["signature"] . '" width="300">
                        </div>';

        $htmlInvoice .= '</div>
</body>
</html>';

        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10,

            'fontDir' => array_merge($fontDirs, [
                '../../../fonts/',
            ]),
            'fontdata' => $fontData + [
                'montserrat' => [
                    'R' => 'Montserrat-Regular.ttf',
                    'B' => 'Montserrat-Bold.ttf',
                ]
            ],
            'default_font' => 'montserrat'
        ]);

        $mpdf->SetProtection(array('print', 'copy'));
        $mpdf->SetTitle("Invoice #" . $facture["numero"]);
        $mpdf->SetAuthor("Hello World");
        $mpdf->SetWatermarkText("");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.05;
        $mpdf->SetDisplayMode('fullpage');

        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($htmlInvoice);
        $file_name = 'invoice-' . $facture["ID"] . '-' . str_replace(array(' ', '/', '\\'), '-', trim($invoiceFor)) . '.pdf';
        if ($output == "show") {

            $mpdf->Output($file_name, 'I');
        } else {

            $dirPath = $dirPath . "uploads/";
            $mpdf->Output($dirPath . $file_name, 'F');
            return $file_name;
        }
    }
}
