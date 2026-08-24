<?php
class devis
{
    static $table =  __prefixe_db__ . "devis";
    static $table2 =  __prefixe_db__ . "item_devis";
    static $tableFacture =  __prefixe_db__ . "facture";
    static $table4 =  __prefixe_db__ . "agence";
    static $table5 =  __prefixe_db__ . "client";
    static $tableCondition =  __prefixe_db__ . "condition";

    // Valeurs de statu déjà en usage (jamais formalisées en constantes avant - juste des <option
    // value="X"> dupliquées entre views/devis/form.php, son miroir JS devisStatusMap, et
    // views/devis/list.php). STATU_CONTRAT_SIGNE est la seule valeur réellement nouvelle : les 5
    // autres existaient déjà, y compris STATU_CONTRAT_EN_ATTENTE.
    const STATU_BROUILLON = 0;
    const STATU_ENVOYE = 1;
    const STATU_ACCEPTE = 2;
    const STATU_CONTRAT_EN_ATTENTE = 3;
    const STATU_PAIEMENT_EFFECTUE = 4;
    const STATU_REFUSE = 5;
    const STATU_CONTRAT_SIGNE = 6;

    private $id;
    private $user_added;
    private $user_edited;
    private $numero;
    private $client;
    private $date_devis;
    private $total;
    private $statu;
    private $devise;
    private $discount;
    private $discount_val;
    private $condition_paiment;
    private $remarque;
    private $proforma;
    private $pack;
    private $tva;
    private $langue;
    private $date_add;
    private $last_edit;
    private $bank;

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
    
    public function getFacture()
    {
        global $db;
        $facture = new facture();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$tableFacture . " WHERE id_devis = %s",
            GetSQLValueString($this->id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $facture = facture::find($data['id'],$_SESSION['agence']);
        }
        return $facture;
    }

    public function getDateDevis()
    {
        return $this->date_devis;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getStatu()
    {
        return $this->statu;
    }

    // Le module Contrat est-il "activé" pour ce devis ? Détermine si les statuts liés au contrat
    // (STATU_CONTRAT_EN_ATTENTE / STATU_CONTRAT_SIGNE) doivent apparaître dans le <select> statut
    // (views/devis/form.php) - jamais de statut "contrat" proposé tant qu'aucun contrat n'existe.
    public function hasContrat()
    {
        $contrat = contract::findByDevis($this->id, $_SESSION['agence'], $this->langue);
        return $contrat->getId() > 0;
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
    
    public function getProforma()
    {
        return $this->proforma;
    }

    public function isProforma()
    {
        return $this->proforma == 1 ? true : false;
    }
    
    public function getPack()
    {
        return $this->pack;
    }

    public function isPack()
    {
        return $this->pack == 1 ? true : false;
    }

    public function getTauxTVA()
    {
        return $this->tva;
    }

    public function getLangue()
    {
        return $this->langue;
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

    public function setDateDevis($date_devis)
    {
        $this->date_devis = $date_devis;
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

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function setDiscountVal($discount_val)
    {
        $this->discount_val = $discount_val;
    }

    public function setConditionPaiment($condition_paiment)
    {
        $this->condition_paiment = $condition_paiment;
    }

    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;
    }
    
    public function setProforma($proforma)
    {
        $this->proforma = $proforma;
    }
    
    public function setPack($pack)
    {
        $this->pack = $pack;
    }

    public function setTVA($tva)
    {
        $this->tva = $tva;
    }

    public function setLangue($langue)
    {
        $this->langue = $langue;
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
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_user_added, id_bank, numero, id_client, date_devis, total, statu, devise, discount, discount_val, condition_paiment, remarque, proforma, pack, tva, langue, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->user_added->getId(), "int"),
            GetSQLValueString($this->bank ? $this->bank->getId() : null, "int"),
            GetSQLValueString($this->numero, "text"),
            GetSQLValueString($this->client ? $this->client->getId() : null , "int"),
            GetSQLValueString($this->date_devis, "date"),
            GetSQLValueString($this->total, "double"),
            GetSQLValueString($this->statu, "int"),
            GetSQLValueString($this->devise, "text"),
            GetSQLValueString($this->discount, "text"),
            GetSQLValueString($this->discount_val, "double"),
            GetSQLValueString($this->condition_paiment, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->proforma, "int"),
            GetSQLValueString($this->pack, "int"),
            GetSQLValueString($this->tva, "int"),
            GetSQLValueString($this->langue, "text"),
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  id_user_edited = %s, id_bank = %s, numero = %s, id_client = %s, date_devis = %s, total = %s, statu = %s, devise = %s, discount = %s, discount_val = %s, condition_paiment=%s, remarque=%s, proforma=%s, pack=%s, tva=%s, langue=%s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->user_edited->getId(), "int"),
            GetSQLValueString($this->bank ? $this->bank->getId() : null, "int"),
            GetSQLValueString($this->numero, "text"),
            GetSQLValueString($this->client ? $this->client->getId() : null , "int"),
            GetSQLValueString($this->date_devis, "date"),
            GetSQLValueString($this->total, "double"),
            GetSQLValueString($this->statu, "int"),
            GetSQLValueString($this->devise, "text"),
            GetSQLValueString($this->discount, "text"),
            GetSQLValueString($this->discount_val, "double"),
            GetSQLValueString($this->condition_paiment, "text"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->proforma, "int"),
            GetSQLValueString($this->pack, "int"),
            GetSQLValueString($this->tva, "int"),
            GetSQLValueString($this->langue, "text"),
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
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        $SQLdelete2 = sprintf(
            "DELETE FROM " . static::$table2 . " WHERE id_devis = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getConditions()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$tableCondition . " WHERE id_devis = %s ORDER BY id ASC",
            GetSQLValueString($this->id, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $condition = condition::find($data['id'], $_SESSION['agence']);
            array_push($items, $condition);
        }
        return $items;
    }

    public static function find($id,$agence = 1)
    {
        global $db;
        $devis = new devis();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where A.id = %s",
            // GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $devis = static::build($data);
        }
        return $devis;
    }
    
    // Lookup utilisée par le webhook Slack (aucune session utilisateur active) :
    // détermine d'abord l'agence réelle du devis en base, puis initialise
    // $_SESSION['agence'] avant d'appeler find(), car build() résout le client
    // via client::find($id, $_SESSION['agence']) qui exige une correspondance exacte.
    public static function findByNumero($numero)
    {
        global $db;
        $devis = new devis();
        $SQLselect = sprintf(
            "SELECT A.id as ID, B.id_agence FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client WHERE A.numero = %s",
            GetSQLValueString($numero, "text")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $row = $db->fetch_assoc($result);
            $_SESSION['agence'] = $row['id_agence'];
            if (!isset($_SESSION['langue']) || empty($_SESSION['langue'])) {
                $_SESSION['langue'] = $row['id_agence'] == 2 ? 'en' : 'fr';
            }
            // client::find() déréférence $_SESSION['user']->isSuperUser() sans garde :
            // il faut un "utilisateur système" en session avant d'appeler find() ci-dessous.
            if (!isset($_SESSION['user']) || !($_SESSION['user'] instanceof user)) {
                $_SESSION['user'] = user::find(defined('SLACK_BOT_ACTING_USER_ID') ? SLACK_BOT_ACTING_USER_ID : 5);
            }
            $devis = static::find($row['ID']);
        }
        return $devis;
    }

    // Convertit une description CKEditor (HTML) en texte lisible sur Trello : les <li>/<br>/<p>
    // deviennent des retours à la ligne (les balises sont sinon simplement concaténées bout à bout).
    public static function descriptionToPlainText($html)
    {
        if ($html === null || $html === '') {
            return '';
        }
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<li[^>]*>/i', "- ", $text);
        $text = preg_replace('/<\/(li|p|div|h1|h2|h3|h4|h5|h6|tr)>/i', "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }

    public static function findLastQuote($agence = 1)
        {
            global $db;
            $devis = new devis();
            $SQLselect = sprintf(
                "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where B.id_agence = %s",
                GetSQLValueString($agence, "int")
            );
            if($_SESSION['user']->isSuperUser() == false){
                $SQLselect .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
            }
            $SQLselect .= " ORDER BY A.ID DESC limit 1";
            // echo $SQLselect;
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $devis = static::build($data);
            }
            return $devis;
        }

    public static function findAll($statu = false, $ordre = false, $limit = false, $agence = 1)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where C.id = $agence";

        if ($statu) {
            $SQLselect .= " AND A.statu = " . intval($statu);
        }
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        if ($ordre) {
            $SQLselect .= " ORDER BY A.ID DESC";
        }
        if ($limit) {
            $SQLselect .= " LIMIT $limit";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $devis = static::build($data);
            array_push($items, $devis);
        }
        return $items;
    }

    // Recherche globale (bandeau de recherche du bandeau haut) : numéro de devis uniquement -
    // utilisé par com_search, jamais par le listing standard des devis.
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

    public static function ofClient($clientID = 0, $statu = false, $ordre = false, $limit = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT " . static::$table . ".id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE 1 = 1";
        if ($clientID) {
            $SQLselect .= " AND id_client = " . intval($clientID);
        }
        if ($statu) {
            $SQLselect .= " AND statu = " . intval($statu);
        }
        if ($ordre) {
            $SQLselect .= " ORDER BY date_devis DESC";
        }
        if ($limit) {
            $SQLselect .= " LIMIT $limit";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $devis = static::build($data);
            array_push($items, $devis);
        }
        return $items;
    }

    public function getItems()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table2 . " WHERE id_devis = %s ORDER BY ordre ASC",
            GetSQLValueString($this->id, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item_devis = item_devis::find($data['id'], $_SESSION['agence']);
            array_push($items, $item_devis);
        }
        return $items;
    }

    public function getTotalItems()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $discount =(($item->getDiscount() * $item->getTotal()) / 100);
            $total += $item->getTotal() - $discount;
        }

        // test réduction
        if ($this->discount == 'percentage') {
            $total = $total - ($total * $this->discount_val / 100);
        } elseif ($this->discount == 'amount') {
            $total = $total - $this->discount_val;
        }
        // TVA
        $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
        $tva = $agence->getTva();
        if($this->tva > 0) $tva = $this->tva;
        if (!$this->isProforma()) $total += $total * ($tva/100);

        return $total;
    }

    public function setTotalItems()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $discount =(($item->getDiscount() * $item->getTotal()) / 100);
            $total += $item->getTotal() - $discount;
        }

        // test réduction
        if ($this->discount == 'percentage') {
            $total = $total - ($total * $this->discount_val / 100);
        } elseif ($this->discount == 'amount') {
            $total = $total - $this->discount_val;
        }

        // TVA
        $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
        $tva = $agence->getTva();
        if($this->tva > 0) $tva = $this->tva;
        if (!$this->isProforma()) $total += $total * ($tva/100);

        $this->total = $total;
    }

    public function generateNumero()
    {
        $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
        $date = new DateTime($this->date_add);
        $numero = $date->format('Y') . $date->format('m') . str_pad($agence->getNumeroIncrementDevis(), 4, '0', STR_PAD_LEFT);
        $this->setNumero($numero);
    }

    public static function build($data)
    {
        $devis = new devis();
        $devis->setId($data['ID']);
        $devis->setUserAdded(isset($data['id_user_added']) ? user::find($data['id_user_added']) : new user());
        $devis->setUserEdited(isset($data['id_user_edited']) ? user::find($data['id_user_edited']) : new user());
        $devis->setBank(isset($data['id_bank']) ? bank::find($data['id_bank']) : new bank());
        $devis->setNumero($data['numero']);
        $devis->setClient(isset($data['id_client']) ? client::findAny($data['id_client']) : new client());
        $devis->setDateDevis($data['date_devis']);
        $devis->setTotal($data['total']);
        $devis->setStatu($data['statu']);
        $devis->setDevise($data['devise']);
        $devis->setDiscount($data['discount']);
        $devis->setDiscountVal($data['discount_val']);
        $devis->setConditionPaiment($data['condition_paiment']);
        $devis->setRemarque($data['remarque']);
        $devis->setProforma($data['proforma']);
        $devis->setPack($data['pack']);
        $devis->setTVA($data['tva']);
        $devis->setLangue($data['langue']);
        $devis->setDateAdd($data['date_add']);
        $devis->setLastEdit($data['last_edit']);
        return $devis;
    }
    

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count($statu = false, $year = false,$agence = 1)
    {
        global $db;
        $SQLcount = "SELECT count(A.id) as c FROM " . static::$table . " A inner join " . static::$table5 . " B on A.id_client = B.id inner join " . static::$table4 . " C on B.id_agence = C.id WHERE C.id = $agence";
        if($_SESSION['user']->isSuperUser() == false){
            $SQLcount .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        if ($statu) {
            if ($statu == 2)
                $SQLcount .= " AND A.statu = 0";
            else
                $SQLcount .= " AND A.statu = " . intval($statu);
        }
        if ($year) {
            $SQLcount .= " AND YEAR(date_devis) = " . intval($year);
        }
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }



    public function getTva()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $discount =(($item->getDiscount() * $item->getTotal()) / 100);
            $total += $item->getTotal() - $discount;
        }

        // test réduction
        if ($this->discount == 'percentage') {
            $total = $total - ($total * $this->discount_val / 100);
        } elseif ($this->discount == 'amount') {
            $total = $total - $this->discount_val;
        }

        $tva = null;
        $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
        $taux = $agence->getTva();
        if($this->tva > 0) $taux = $this->tva;

        if (!$this->isProforma()) {
            //$tva = ($this->getTotal() - $this->getTotal() / 1.2);
            $tva = $total * $taux / 100;
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
            'numero' => $this->getNumero(),
            'date_devis' => $this->getDateDevis(),
            'total' => $this->getTotal(),
            'statu' => $this->getStatu(),
            'devise' => $this->getDevise(),
            'discount' => $this->getDiscount() != '' ? ($this->getDiscount() . ($this->getDiscount() == 'amount' ? ' ' . $this->getDevise() : '%')) : null,
            'condition_paiment' => $this->getConditionPaiment(), // ? mb_convert_encoding($this->getConditionPaiment(), mb_detect_encoding($this->getConditionPaiment()), 'UTF-8') : '',
            'remarque' => $this->getRemarque(), // ? mb_convert_encoding($this->getRemarque(), mb_detect_encoding($this->getRemarque()), 'UTF-8') : '',
            'langue' => $this->getLangue(),
            'pdf' => 'https://www.helloworld-agency.com/hw-label/new/components/com_devis/controleurs/router.php?task=pdfDevis&id=' . $this->getId(),
        );
    }
    
    function pdfDevis($output="show")
{
	global $db;
    
		
		require '../../../vendor/autoload.php';
		require '../../../includes/traduction.php';

        $dirPath = $output == "show" ? "../../../" : "../../../";


        $devis = $this;

        $typedevis = $traduction['DEVIS'][$devis->getLangue()];
		
		$agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
		$items = $devis->getItems();
		$client = $devis->getClient();
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
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
	<td><img src="'.$dirPath.'/images/agences/' . $agence->getLogo() . '" width="200"></td>
	<td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>'.$agence->getNom().', ' . $agence->getAdresse() . '</strong><br>
	<p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> '. $agence->getWebsite() .'</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

if($agence->getIce() != ''){
$htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> ' . $agence->getIf() . ' | <strong>TP</strong> ' . $agence->getTp() . ' | <strong>RC</strong> ' . $agence->getRc() . ' | <strong>ICE</strong> ' . $agence->getIce() . '</p>';
}

$htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} '.$traduction['SUR'][$devis->getLangue()].' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">'.$traduction['DEVIS_POUR'][$devis->getLangue()].'<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:'.$color.'">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">'.$traduction['TOTAL_DEVIS'][$devis->getLangue()].'</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">'.$traduction['DATE_DEVIS'][$devis->getLangue()].'</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($devis->getDateDevis()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° '.$traduction['DEVIS'][$devis->getLangue()].'</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $devis->getNumero() . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">'.$traduction['PRIX_HT'][$devis->getLangue()].'</td>
<td width="20%">'.$traduction['QTE'][$devis->getLangue()].'</td>
<td width="20%" align="right">'.$traduction['TOTAL_HT'][$devis->getLangue()].'</td>
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
                <td align="center" style="vertical-align:middle;">' . number_format($item->getPrix(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
                <td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . getUnities()[$devis->getLangue()][$item->getUnite()] . '</td>
                <td align="right" style="vertical-align:middle;" class="cost">' . ($item->getDiscount() > 0 ?  '<del>'.number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise().'</del>' : number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise()) . '</td>
                </tr>';
                if($item->getDiscount() > 0){
                    $htmlInvoice .= '<tr>
                        <td colspan="3" style="color:#4caf50"><strong>'.str_replace(array('text_value','discount_value'),array($item->getTitre(),$item->getDiscount()),$traduction['PRICE_AFTER_DISCOUNT'][$devis->getLangue()]).'</strong></td>
                        <td align="right"  style="color:#4caf50"><strong>' . number_format($item->getTotal() - $discount , 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
                    </tr>';
                }
		}

		$htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">'.$traduction['SSTOTAL_HT'][$devis->getLangue()].'</td>
<td class="totals cost">' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr>
<tr>';
// Calcule TVA		
//$tva = $devis->isProforma() ? 0 : $soustotal * $agence->getTva()/100;	
$tva = $this->getTva();
		
$htmlInvoice .= '<td class="totals">'.$traduction['TVA'][$devis->getLangue()].'</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr>';
if ($devis->getDiscount() != '') {
			$discoutSign = $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%';
			// test réduction
			if ($devis->getDiscount() == 'percentage') {
				$soustotal = $soustotal - ($soustotal * $devis->getDiscountVal() / 100);
			} elseif ($devis->getDiscount() == 'amount') {
				$soustotal = $soustotal - $devis->getDiscountVal();
			}

            $totalLabel = $devis->isProforma() ? 'TOTAL' : $traduction['TOTAL_TTC'][$devis->getLangue()];
			$htmlInvoice .= '<tr>
<td class="totals">'.$totalLabel.'</td>
<td class="totals cost"> ' . number_format($soustotal + $tva, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr><tr>
<td class="totals">'.$traduction['REDUCTION'][$devis->getLangue()].'</td>
<td class="totals cost">- ' . $devis->getDiscountVal() . $discoutSign . '</td>
</tr>';
		}
		
$htmlInvoice .= '<tr style="background:'.$color.';">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>'.$traduction['TOTAL_TTC'][$devis->getLangue()].'</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
		if ($devis->getRemarque() != '') {
			$htmlInvoice .= '<strong>'.$traduction['REMARQUE'][$devis->getLangue()].': </strong>' . strip_tags($devis->getRemarque());
		};
		$htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:'.$color.';">'.$traduction['THANK_YOU_FOR'][$devis->getLangue()].$agence->getNom().' !!</h3>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS'][$devis->getLangue()].': </strong>'. $agence->getConditions() .'</p>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS_PAYMENT'][$devis->getLangue()].': </strong>' . $devis->getConditionPaiment() . '</p>';

// Conditions de paiement
foreach($this->getConditions() as $condition){
    $htmlInvoice .= '<p>'. $condition->getMontant() .' '. $condition->getCondition() .'</p>';
}

if($devis->getBank()){
    $htmlInvoice .= '<p style="font-size:8pt;"><br>';
    if($devis->getBank()->getRaisonSociale() != '') $htmlInvoice .= '<strong>'.$traduction['RAISON_SOCIALE'][$devis->getLangue()].':</strong> '. $devis->getBank()->getRaisonSociale() .' <br>';
    if($devis->getBank()->getRib() != '') $htmlInvoice .= '<strong>'.$traduction['ACCOUNT_NUMBER'][$devis->getLangue()].':</strong> '. $devis->getBank()->getRib() .' <br>';
    if($devis->getBank()->getIbanNumber() != '') $htmlInvoice .= '<strong>'.$traduction['IBAN'][$devis->getLangue()].':</strong> '. $devis->getBank()->getIbanNumber() .' <br>';
    if($devis->getBank()->getBanque() != '') $htmlInvoice .= '<strong>'.$traduction['BRANCH'][$devis->getLangue()].':</strong> '. $devis->getBank()->getBanque() .' <br>';
    if($devis->getBank()->getCodeSwift() != '') $htmlInvoice .= '<strong>'.$traduction['SWIFT_CODE'][$devis->getLangue()].':</strong> '. $devis->getBank()->getCodeSwift() .' <br>';
    if($devis->getBank()->getCurrency() != '') $htmlInvoice .= '<strong>'.$traduction['CURRENCY'][$devis->getLangue()].':</strong> '. $devis->getBank()->getCurrency() .' <br>';
    
    $htmlInvoice .= '</p>';
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
		$mpdf->SetTitle("Devis #" . $devis->getNumero());
		$mpdf->SetAuthor("Hello World");
		$mpdf->SetWatermarkText("");
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.05;
		$mpdf->SetDisplayMode('fullpage');

        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
		$mpdf->WriteHTML($htmlInvoice);
		
		$file_name = $traduction['DEVIS'][$devis->getLangue()] . ' - '.$invoiceFor.'.pdf';
        
		if($output == "show"){
            
		    $mpdf->Output($file_name, 'I');
		}
		else{
                
                 $dirPath = $dirPath."uploads/";
                 $mpdf->Output($dirPath.$file_name,'F');
                 return $file_name;
            
		}
	
}
function sendViaMailDevis($file_name = ""){

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
    $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
	$mailBody = '<html>
    <body>
    <table border="0" width="100%">
        <tr>
            <td bgcolor="#F6F6F6" align="center">
                <table border="0" cellpadding="15" cellspacing="0" width="640">
                    <tr>
                        <td align="center"><img src="'.$siteURL.'images/agences/' . $agence->getLogo() . '" width="100"></td>
                    </tr>

                    <tr bgcolor="#FFFFFF">
                        <td align="center">
                        <h1 style="font-weight:normal; margin-bottom:15px;margin-top:20px">
                        Devis N° ' . $this->getNumero() . '
                        </h1>
                    </td>
                    
                </tr>

                <tr bgcolor="#FFFFFF" style="font-size:20px;">
                    <td align="center">
                    Bonjour, Je vous prie de bien vouloir trouver ci-joint <span style="color:#15c;">le devis </span>relatif aux services demandés. Cordialement.
                    </td>
                    </tr>

                    <tr>
                        <td align="center">
                            <p>
                                <font size="2" color="#666666"><br />
                                Hello World Contact
                                <!--footer info--><br/>
                                Email : ' . $agence->getEmail() . '
                                <br>
                                Tél : ' . $agence->getTel() . ' / ' . $agence->getTel2(). '
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
	$mail->addReplyTo($agence->getEmail(), $agence->getNom());
	//Set who the message is to be sent to

    $mail->addAddress($client->getEmail(), $client->getNom().' '.$client->getPrenom());
    $mail->addAddress("sales@helloworld-agency.com");

	//Set the subject line
	$mail->Subject = 'Devis '.$agence->getNom();
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($mailBody);
	//Attach an image file
    
	if($file_name != '') $mail->addAttachment('../../../uploads/'.$file_name);
		
	if($mail->send()) {
		copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
		echo "success";
	}
	else {
		echo "Error: " . $mail->ErrorInfo;
	}

}

// API

    public static function buildApi($data)
    {
        $devis= array(
            'id' => $data['ID'],
            'numero' => $data['numero'],
            'client' => client::ApiFindById($data['id_client']),
            'bank' => (isset($data['id_bank']) && !empty($data['id_bank'] && $data['id_bank'] != null) ? bank::ApiFindById($data['id_bank']) : array()),
            //'facture' => facture::findByDevisApi($data['ID']),
            'date_devis' => $data['date_devis'],
            'total' => $data['total'],
            'statu' => $data['statu'],
            'devise' => $data['devise'],
            'discount' => $data['discount'],
            'discount_val' => $data['discount_val'],
            'condition_paiment' => $data['condition_paiment'],
            'remarque' => $data['remarque'],
            'proforma' => $data['proforma'],
            'langue' => $data['langue'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
        );
        return $devis;
    }

    public static function ApiFindById($id)
    {
        $token = getToken();
        if($token){
            global $db;
            $devis = new devis();
            $SQLselect = sprintf(
                "SELECT A.id as ID,A.*,B.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where A.id = %s",
                GetSQLValueString($id, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                // Un client ne doit voir/télécharger que SES PROPRES devis.
                if ((int) $data['id_client'] !== (int) $token->id) {
                    return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
                }
                $devis = static::buildApi($data);
            }
            return $devis;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function findByIdApi($id)
    {
        $token = getToken();
        if($token){
            global $db;
            $devis = new devis();
            $SQLselect = sprintf(
                "SELECT A.id as ID,A.*,B.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence where A.id = %s",
                GetSQLValueString($id, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                // Un client ne doit voir/télécharger que SES PROPRES devis.
                if ((int) $data['id_client'] !== (int) $token->id) {
                    return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
                }
                $devis = static::buildApi($data);
            }
            return $devis;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function findAllByClientApi($clientID = 0, $statu = false, $ordre = false, $limit = false)
    {
        $token = getToken();
        if($token){
            global $db;
            $items = array();
            // Un client ne doit voir que SES PROPRES devis : on ignore le
            // clientID fourni par l'appelant et on scope sur le token vérifié.
            $clientID = (int) $token->id;
            $SQLselect = "SELECT A.id as ID,A.*,B.* FROM " . static::$table . " A INNER JOIN " . static::$table5 . " B ON B.id = A.id_client INNER JOIN " . static::$table4 . " C ON C.id =B.id_agence";
            if ($clientID) {
                $SQLselect .= " AND A.id_client = " . intval($clientID);
            }
            if ($statu) {
                $SQLselect .= " AND A.statu = " . intval($statu);
            }
            if ($ordre) {
                $SQLselect .= " ORDER BY A.date_devis DESC";
            }
            if ($limit) {
                $SQLselect .= " LIMIT $limit";
            }
            $result = $db->queryS($SQLselect);
            foreach ($result as $data) {
                $devis = static::buildApi($data);
                array_push($items, $devis);
            }
            return $items;
        }else{
            return (array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

    public static function getItemsApi($id_devis)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT id FROM " . static::$table2 . " WHERE id_devis = %s ORDER BY ordre ASC",
            GetSQLValueString($id_devis, "int")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $item_devis = item_devis::findByIdApi($data['id']);
            array_push($items, $item_devis);
        }
        return $items;
    }

    public static function pdfDevisApi($id)
{
    global $db;
    require '../../../vendor/autoload.php';
    require '../../../includes/traduction.php';
    
    if (isset($id) && !empty($id)) {
        $dirPath = "../../../";
        $devis = devis::findByIdApi($id);
        if (!is_array($devis)) {
            // Devis introuvable ou n'appartenant pas au client authentifié
            // (findByIdApi renvoie alors une chaîne JSON, pas un tableau).
            return $devis;
        }
        $client = $devis["client"];
        $agence = $client["agence"];
		$items = devis::getItemsApi($devis["id"]);
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
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
	<td><img src="'.$dirPath.'images/agences/' . $agence["logo"] . '" width="100"></td>
	<td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence["adresse"] . '</strong><br>
	<p style="font-size: 8pt;"><strong>t:</strong> ' . $agence["tel"] . '  |  <strong>e:</strong> ' . $agence["email"] . ' | <strong>w:</strong> ' . $agence["website"] . '</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

if($agence["ice"] != ''){
    $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> '. $agence["if"] .' | <strong>TP</strong> '. $agence["tp"] .' | <strong>RC</strong> '. $agence["rc"] .' | <strong>ICE</strong> '. $agence["ice"] .'</p>';
}

$htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} '.$traduction['SUR'][$devis["langue"]].' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">'.$traduction['DEVIS_POUR'][$devis["langue"]].'<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:'.$color.'">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client["tel"] . '<br>' . $client["email"] . '<br>' . $client["ice"] . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">'.$traduction['TOTAL_DEVIS'][$devis["langue"]].'</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($devis["total"], 2, ',', ' ') . ' ' . $devis["devise"] . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">'.$traduction['DATE_DEVIS'][$devis["langue"]].'</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($devis["date_devis"]) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° '.$traduction['DEVIS'][$devis["langue"]].'</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $devis["numero"] . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">'.$traduction['PRIX_HT'][$devis["langue"]].'</td>
<td width="20%">'.$traduction['QTE'][$devis["langue"]].'</td>
<td width="20%" align="right">'.$traduction['TOTAL_HT'][$devis["langue"]].'</td>
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
                                <td align="center" style="vertical-align:middle;">' . number_format($item["prix"], 2, ',', ' ') . ' ' . $devis["devise"] . '</td>
                                <td align="center" style="vertical-align:middle;">' . $item["qte"] . ' x ' . getUnities()[$devis["langue"]][$item["unite"]] . '</td>
                                <td align="right" style="vertical-align:middle;" class="cost">' . ($item["discount"] > 0 ?  '<del>'.number_format($item["total"], 2, ',', ' ') . ' ' . $devis["devise"].'</del>' : number_format($item["total"], 2, ',', ' ') . ' ' . $devis["devise"]) . '</td>
                            </tr>';
                            if($item["discount"] > 0){
                                $htmlInvoice .= '<tr>
                                    <td colspan="3" style="color:#4caf50"><strong>'.str_replace(array('text_value','discount_value'),array($item["titre"],$item["discount"]),$traduction['PRICE_AFTER_DISCOUNT'][$devis["langue"]]).'</strong></td>
                                    <td align="right"  style="color:#4caf50"><strong>' . number_format($item["total"] - $discount , 2, ',', ' ') . ' ' . $devis["devise"] . '</strong></td>
                                </tr>';
                            }
        }

        $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">'.$traduction['SSTOTAL_HT'][$devis["langue"]].'</td>
<td class="totals cost">' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis["devise"] . '</td>
</tr>
<tr>';
        // Calcule TVA		
        $tva = $devis["proforma"] ? 0 : $soustotal * $agence["tva"]/100;

        $htmlInvoice .= '<td class="totals">' . $traduction['TVA'][$devis["langue"]] . '</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $devis["devise"] . '</td>
</tr>';
        if ($devis["discount"] != '') {
            $discoutSign = $devis["discount"] == 'amount' ? ' ' . $devis["devise"] : '%';
            // test réduction
            if ($devis["discount"] == 'percentage') {
                $soustotal = $soustotal - ($soustotal * $devis["discount_val"] / 100);
            } elseif ($devis["discount"] == 'amount') {
                $soustotal = $soustotal - $devis["discount_val"];
            }
            
            $totalLabel = $devis["proforma"] ? 'TOTAL' : $traduction['TOTAL_TTC'][$devis["langue"]];
            
            $htmlInvoice .= '<tr>
<td class="totals">'. $totalLabel .'</td>
<td class="totals cost"> ' . number_format($soustotal + $tva, 2, ',', ' ') . ' ' . $devis["devise"] . '</td>
</tr><tr>
<td class="totals">'.$traduction['REDUCTION'][$devis["langue"]].'</td>
<td class="totals cost">- ' . $devis["discount_val"] . $discoutSign . '</td>
</tr>';
        }
        
        $htmlInvoice .= '<tr style="background:'.$color.';">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>'.$traduction['TOTAL_TTC'][$devis["langue"]].'</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($devis["total"], 2, ',', ' ') . ' ' . $devis["devise"] . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
        if ($devis["remarque"] != '') {
            $htmlInvoice .= '<strong>'.$traduction['REMARQUE'][$devis["langue"]].': </strong>' . strip_tags($devis["remarque"]);
        };
        $htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:'.$color.';">'.$traduction['THANK_YOU_FOR'][$devis["langue"]] . $agence["nom"] . ' !!</h3>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS'][$devis["langue"]].': </strong>'.$agence["conditions"].'</p>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS_PAYMENT'][$devis["langue"]].': </strong>' . $devis["condition_paiment"] . '</p>';
if(is_array($devis["bank"]) && !empty($devis["bank"])){
    $htmlInvoice .= '<p style="font-size:8pt;"><br>';
    if($devis["bank"]["raison_sociale"] != '') $htmlInvoice .= '<strong>'.$traduction['RAISON_SOCIALE'][$devis["langue"]].':</strong> '. $devis["bank"]["raison_sociale"] .' <br>';
    if($devis["bank"]["rib"] != '') $htmlInvoice .= '<strong>'.$traduction['ACCOUNT_NUMBER'][$devis["langue"]].':</strong> '. $devis["bank"]["rib"] .' <br>';
    if($devis["bank"]["iban_number"] != '') $htmlInvoice .= '<strong>'.$traduction['IBAN'][$devis["langue"]].':</strong> '. $devis["bank"]["iban_number"] .' <br>';
    if($devis["bank"]["banque"] != '') $htmlInvoice .= '<strong>'.$traduction['BRANCH'][$devis["langue"]].':</strong> '. $devis["bank"]["banque"] .' <br>';
    if($devis["bank"]["code_swift"] != '') $htmlInvoice .= '<strong>'.$traduction['SWIFT_CODE'][$devis["langue"]].':</strong> '. $devis["bank"]["code_swift"] .' <br>';
    if($devis["bank"]["currency"] != '') $htmlInvoice .= '<strong>'.$traduction['CURRENCY'][$devis["langue"]].':</strong> '. $devis["bank"]["currency"] .' <br>';
    $htmlInvoice .= '</p>';
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
        $mpdf->SetTitle("Quote #" . $devis["numero"]);
        $mpdf->SetAuthor("Hello World");
        $mpdf->SetWatermarkText("");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.05;
        $mpdf->SetDisplayMode('fullpage');
        
        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($htmlInvoice);

        $file_name = 'quote-'.$devis["id"].'-'.str_replace(' ','-',trim($invoiceFor)).'.pdf';
        $dirPath = $dirPath."uploads/";
        $mpdf->Output($dirPath.$file_name,'F');
        return $file_name;
}
}
}
