<?php
class payment
{
    static $table =  __prefixe_db__ . "payment";
    static $table2 =  __prefixe_db__ . "facture";
    static $tableAgence =  __prefixe_db__ . "agence";
    static $tableClient =  __prefixe_db__ . "client";

    private $id;
    private $facture;
    private $montant;
    private $date_payment;
    private $methode_payment;
    private $date_validation;
    private $detail;
    private $date_add;
    private $last_edit;
    private $reg_img;
    private $numero_sequence;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFacture()
    {
        return $this->facture;
    }

    public function getMontant()
    {
        return $this->montant;
    }

    public function getDatePayment()
    {
        return $this->date_payment;
    }

    public function getMethodePayment()
    {
        return $this->methode_payment;
    }

    public function getDateValidation()
    {
        return $this->date_validation;
    }

    public function getDetail()
    {
        return $this->detail;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function getRegImg()
    {
        return $this->reg_img;
    }

    // Numéro de reçu de paiement (facture.numero + '-' + ce numéro), attribué une seule fois à la
    // création via facture::assignNextPaymentSeq() et jamais recalculé/décrémenté ensuite - voir
    // ce commentaire côté facture::assignNextPaymentSeq() pour le pourquoi (éviter la réutilisation
    // d'un numéro déjà émis après suppression d'un paiement intermédiaire).
    public function getNumeroSequence()
    {
        return $this->numero_sequence;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFacture($facture)
    {
        $this->facture = $facture;
    }

    public function setMontant($montant)
    {
        $this->montant = $montant;
    }

    public function setDatePayment($date_payment)
    {
        $this->date_payment = $date_payment;
    }

    public function setMethodePayment($methode_payment)
    {
        $this->methode_payment = $methode_payment;
    }

    public function setDateValidation($date_validation)
    {
        $this->date_validation = $date_validation;
    }

    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function setRegImg($reg_img)
    {
        $this->reg_img = $reg_img;
    }

    public function setNumeroSequence($numero_sequence)
    {
        $this->numero_sequence = $numero_sequence;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_facture, montant, reg_img, date_payment, methode_payment, date_validation, detail, date_add, last_edit, numero_sequence) VALUES (%s, %s, %s, %s, %s, %s, %s, %s , %s, %s)",
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->montant, "double"),
            GetSQLValueString($this->reg_img, "text"),
            GetSQLValueString($this->date_payment, "date"),
            GetSQLValueString($this->methode_payment, "text"),
            GetSQLValueString($this->date_validation, "date"),
            GetSQLValueString($this->detail, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->numero_sequence, "int")
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
            "UPDATE " . static::$table . " SET  id_facture = %s, montant = %s, reg_img = %s, date_payment = %s, methode_payment = %s, date_validation = %s, detail = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->facture->getId(), "int"),
            GetSQLValueString($this->montant, "double"),
            GetSQLValueString($this->reg_img, "text"),
            GetSQLValueString($this->date_payment, "date"),
            GetSQLValueString($this->methode_payment, "text"),
            GetSQLValueString($this->date_validation, "date"),
            GetSQLValueString($this->detail, "text"),
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
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $payment = new payment();
        $SQLselect = sprintf(
            "SELECT A.id as ID,A.id_facture as ID_facture,A.*,B.* FROM " . static::$table . " A JOIN " . static::$table2 . " B ON A.id_facture = B.id inner join " . static::$tableClient . " C on B.id_client = C.id inner join " . static::$tableAgence . " D on C.id_agence = D.id WHERE A.id = %s",
            GetSQLValueString($id, "int")
        );

        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (B.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $payment = static::build($data);
        }
        return $payment;
    }

    public static function findAll($id_facture = null, $ordre = false,$agence = false ,$devise = false , $year = false)
    {
        global $db;
        $items = array();
        $SQLselect = "SELECT A.id as ID,A.id_facture as ID_facture,A.*,B.* FROM " . static::$table . " A JOIN " . static::$table2 . " B ON A.id_facture = B.id inner join " . static::$tableClient . " C on B.id_client = C.id inner join " . static::$tableAgence . " D on C.id_agence = D.id WHERE 1 = 1";
        
        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (B.id_user_added = ".$_SESSION['user']->getId()." )";
        }

        if ($id_facture) {
            $SQLselect .= " AND A.id_facture = " . intval($id_facture);
        }

        if($agence){
            $SQLselect .= " AND C.id_agence = " . intval($agence);
        }

        if($devise){
            $SQLselect .= " AND B.devise = '$devise'";
        }

        if($year){
            $SQLselect .= " AND YEAR(A.date_payment) = " . intval($year);
        }

        if ($ordre) {
            $SQLselect .= " ORDER BY date_payment DESC";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $payment = static::build($data);
            array_push($items, $payment);
        }
        return $items;
    }

    public static function build($data)
    {
        $payment = new payment();
        $payment->setId($data['ID']);
        $payment->setFacture(isset($data['ID_facture']) ? facture::find($data['ID_facture'],$_SESSION['agence']) : null);
        $payment->setMontant($data['montant']);
        $payment->setRegImg($data['reg_img']);
        $payment->setDatePayment($data['date_payment']);
        $payment->setMethodePayment($data['methode_payment']);
        $payment->setDateValidation($data['date_validation']);
        $payment->setDetail($data['detail']);
        $payment->setDateAdd($data['date_add']);
        $payment->setLastEdit($data['last_edit']);
        $payment->setNumeroSequence(isset($data['numero_sequence']) ? $data['numero_sequence'] : null);
        return $payment;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count()
    {
        global $db;
        $SQLcount = "SELECT count(A.id) as c FROM " . static::$table . " A JOIN " . static::$table2 . " B ON A.id_facture = B.id where 1=1";
        if($_SESSION['user']->isSuperUser() == false){
            $SQLcount .= " AND (B.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function total($year = false, $month = false, $devise = 'DH',$agence = 1,$invoice = false)
    {
        global $db;
        $SQLcount = "SELECT SUM(A.montant) as c FROM " . static::$table . " A JOIN " . static::$table2 . " B ON A.id_facture = B.id inner join " . static::$tableClient . " C on B.id_client = C.id inner join " . static::$tableAgence . " D on C.id_agence = D.id
		WHERE D.id = $agence AND (B.archived IS NULL OR B.archived = 0) AND B.devise = '$devise'";
        
        if($_SESSION['user']->isSuperUser() == false){
            $SQLcount .= " AND (B.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        if ($year) {
            $SQLcount .= " AND YEAR(B.date_facture) = " . intval($year);
        }

        if ($month) {
            $SQLcount .= " AND MONTH(B.date_facture) = " . intval($month);
        }

        if($invoice){
            $SQLcount .= " AND B.id = " . intval($invoice);
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return intval($data["c"]);
        }
        return 0;
    }
    
    public static function getReglementbyDate($from = false, $to = false, $devise = 'DH',$agence = 1)
    {
        global $db;
        if($agence)
            $SQLcount = "SELECT SUM(A.montant) as c FROM " . static::$table . " A JOIN " . static::$table2 . " B ON A.id_facture = B.id inner join " . static::$tableClient . " C on B.id_client = C.id inner join " . static::$tableAgence . " D on C.id_agence = D.id
		WHERE D.id = $agence AND (B.archived IS NULL OR B.archived = 0) AND B.devise = '$devise'";
		else
            $SQLcount = "SELECT SUM(A.montant) as c FROM " . static::$table . " A JOIN " . static::$table2 . " B ON A.id_facture = B.id inner join " . static::$tableClient . " C on B.id_client = C.id inner join " . static::$tableAgence . " D on C.id_agence = D.id
		WHERE (B.archived IS NULL OR B.archived = 0) AND B.devise = '$devise'";
		
        if($_SESSION['user']->isSuperUser() == false){
            $SQLcount .= " AND (A.id_user_added = ".$_SESSION['user']->getId()." )";
        }
        if ($from) {
            $SQLcount .= " AND A.date_payment >= '$from'";
        }

        if ($to) {
            $SQLcount .= " AND A.date_payment <= '$to'";
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return intval($data["c"]);
        }
        return 0;
    }

    public function toArray()
    {
        global $siteURL;

        return array(
            'id' => $this->id,
            'montant' => $this->montant,
            'date_payment' => $this->date_payment,
            'methode_payment' => $this->getMethodePayment(), // ? mb_convert_encoding($this->getMethodePayment(), 'Windows-1252', 'UTF-8') : 'Chèque',
            'reg_img' => $this->reg_img ? $siteURL . 'images/reglements/' . $this->reg_img : null,
        );
    }

    function pdfPayment($output="show",$indexPayment=1){
        global $db;

            // Le numéro stocké (assigné une seule fois à la création, cf. facture::assignNextPaymentSeq())
            // prime toujours sur le paramètre légataire $indexPayment. Le fallback ne sert plus qu'au flux
            // "demande d'acompte" (sendPaymentRequestPdf) qui construit un payment jamais persisté.
            if ($this->numero_sequence !== null && $this->numero_sequence !== '') {
                $indexPayment = $this->numero_sequence;
            }

            require '../../../vendor/autoload.php';
            require '../../../includes/traduction.php';

            $dirPath = $output == "show" ? "../../../" : "../../../";

            

            $payment = $this;
            $typePayment = $traduction['FACTURE'][$payment->getFacture()->getLangue()];
            if ($payment->getFacture()->getIdFacture()) {
                $facture_ref = facture::find($payment->getFacture()->getIdFacture(),$_SESSION['agence']);
                $typePayment = $traduction['AVOIR'][$payment->getFacture()->getLangue()];
            }
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $items = $payment->getFacture()->getItems();
            $client = $payment->getFacture()->getClient();
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
        <td><img src="'.$dirPath.'images/agences/' . $agence->getLogo() . '" width="100"></td>
        <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
        <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> ' . $agence->getWebsite() . '</p></td>
    </tr>
    </table>
    <hr>
    </htmlpageheader>
    <htmlpagefooter name="myfooter">
    <div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

    if($agence->getIce() != ''){
        $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> '. $agence->getIf() .' | <strong>TP</strong> '. $agence->getTp() .' | <strong>RC</strong> '. $agence->getRc() .' | <strong>ICE</strong> '. $agence->getIce() .'</p>';
    }

    $htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} '.$traduction['SUR'][$payment->getFacture()->getLangue()].' {nb}</div>
    </div>
    </htmlpagefooter>
    <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
    <sethtmlpagefooter name="myfooter" value="on" />
    mpdf-->
    <table width="100%">
    <tr>
    <td width="35%" style="vertical-align: middle; font-size:8pt;">' . (isset($facture_ref) ? $traduction['AVOIR_FACTURE'][$payment->getFacture()->getLangue()]. ' N° ' . $facture_ref->getNumero() : $traduction['FACTURE_POUR'][$payment->getFacture()->getLangue()]) . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:#e3d3aa">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
    <td width="30%"></td>

    <td width="35%" style="text-align: right;">

    <table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Total ' . $typePayment . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise() . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Date ' . $typePayment . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($payment->getDatePayment()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $typePayment . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $payment->getFacture()->getNumero() .'-'.$indexPayment. '</strong></td></tr>
<tr><td>' . ($payment->getRegImg() != '' ? '<strong style="color:#2e7d32;">' . mb_strtoupper($traduction['PAYE'][$payment->getFacture()->getLangue()]) . '</strong>' : '<strong style="color:#c62828;">' . mb_strtoupper($traduction['NON_PAYE'][$payment->getFacture()->getLangue()]) . '</strong>') . '</td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">'.$traduction['PRIX_HT'][$payment->getFacture()->getLangue()].'</td>
<td width="20%">'.$traduction['QTE'][$payment->getFacture()->getLangue()].'</td>
<td width="20%" align="right">'.$traduction['TOTAL_HT'][$payment->getFacture()->getLangue()].'</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
    $soustotal = 0;
    foreach ($items as $item) {
        $soustotal += $item->getTotal();
        $htmlInvoice .= '<tr>
<td><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
<td align="center" style="vertical-align:middle;">-</td>
<td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . getUnit($item->getUnite(),$payment->getFacture()->getLangue()) . '</td>
<td align="right" style="vertical-align:middle;" class="cost">-</td>
</tr>';
    }


    //$sstotal = $facture->isProforma() ? $facture->getTotal() : $soustotal;
    $sstotal = $soustotal;

    // Calcule Réduction
//     if ($payment->getFacture()->getDiscount() != '') {
//         $discoutSign = $payment->getFacture()->getDiscount() == 'amount' ? ' ' . $payment->getFacture()->getDevise() : '%';
//         // test réduction
//         if ($payment->getFacture()->getDiscount() == 'percentage') {
//             $soustotal = $soustotal - ($soustotal * $payment->getFacture()->getDiscountVal() / 100);
//         } elseif ($payment->getFacture()->getDiscount() == 'amount') {
//             $soustotal = $soustotal - $payment->getFacture()->getDiscountVal();
//         }
        
//     $htmlInvoice .= '<tr>
// <td class="totals">'.$traduction['REDUCTION'][$payment->getFacture()->getLangue()].'</td>
// <td class="totals cost">- ' . $payment->getFacture()->getDiscountVal() . $discoutSign . '</td>
// </tr>';	
//     }
    
    
    $sousTotal = $payment->getFacture()->isProforma() ? number_format($payment->getMontant(), 2, ',', ' ') : number_format($payment->getMontant() / (1 + ($agence->getTva()/100)), 2, ',', ' ');
    $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">'.$traduction['SSTOTAL_HT'][$payment->getFacture()->getLangue()].'</td>
<td class="totals cost">' . $sousTotal . ' ' . $payment->getFacture()->getDevise() . '</td>
</tr>';

    
    
    // Calcule TVA	
    if(!$payment->getFacture()->isProforma()){
    $tva = $payment->getMontant() - ($payment->getMontant() / (1 + ($agence->getTva()/100)));
    
    $htmlInvoice .= '<tr>
<td class="totals">'.$traduction['TVA'][$payment->getFacture()->getLangue()].'</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise() . '</td>
</tr>';
    }
    
//     if ($payment->getFacture()->getDiscount() != '') {
//         $htmlInvoice .= '<tr>
// <td class="totals">'.$traduction['TOTAL_TTC'][$payment->getFacture()->getLangue()].'</td>
// <td class="totals cost"> ' . number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise() . '</td>
// </tr>';
//     }
    
    
    
    
    
    
    
    
    
    
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

    $totalLabel = $payment->getFacture()->isProforma() ? 'TOTAL' : $traduction['TOTAL_TTC'][$payment->getFacture()->getLangue()];
    $htmlInvoice .= '<tr style="background:#e3d3aa;">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
    if ($payment->getFacture()->getRemarque() != '') {
        $htmlInvoice .= '<strong>'.$traduction['REMARQUE'][$payment->getFacture()->getLangue()].': </strong>' . $payment->getFacture()->getRemarque();
    };
    $htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:#e3d3aa;">'.$traduction['THANK_YOU_FOR'][$payment->getFacture()->getLangue()].$agence->getNom().' !!</h3>
<p style="font-size:8pt;"><strong>' . $agence->getConditions() . '</p>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS_PAYMENT'][$payment->getFacture()->getLangue()].': </strong>' . $payment->getFacture()->getConditionPaiment() . '</p>';
 if($payment->getFacture()->getBank()){
        $htmlInvoice .= '<p style="font-size:8pt;"><br>';
        if($payment->getFacture()->getBank()->getRaisonSociale() != '') $htmlInvoice .= '<strong>'.$traduction['RAISON_SOCIALE'][$payment->getFacture()->getLangue()].':</strong> '. $payment->getFacture()->getBank()->getRaisonSociale() .' <br>';
        if($payment->getFacture()->getBank()->getRib() != '') $htmlInvoice .= '<strong>'.$traduction['ACCOUNT_NUMBER'][$payment->getFacture()->getLangue()].':</strong> '. $payment->getFacture()->getBank()->getRib() .' <br>';
        if($payment->getFacture()->getBank()->getIbanNumber() != '') $htmlInvoice .= '<strong>'.$traduction['IBAN'][$payment->getFacture()->getLangue()].':</strong> '. $payment->getFacture()->getBank()->getIbanNumber() .' <br>';
        if($payment->getFacture()->getBank()->getBanque() != '') $htmlInvoice .= '<strong>'.$traduction['BRANCH'][$payment->getFacture()->getLangue()].':</strong> '. $payment->getFacture()->getBank()->getBanque() .' <br>';
        if($payment->getFacture()->getBank()->getCodeSwift() != '') $htmlInvoice .= '<strong>'.$traduction['SWIFT_CODE'][$payment->getFacture()->getLangue()].':</strong> '. $payment->getFacture()->getBank()->getCodeSwift() .' <br>';
        if($payment->getFacture()->getBank()->getCurrency() != '') $htmlInvoice .= '<strong>'.$traduction['CURRENCY'][$payment->getFacture()->getLangue()].':</strong> '. $payment->getFacture()->getBank()->getCurrency() .' <br>';
        
        $htmlInvoice .= '</p>';
    }
    
    if($payment->getFacture()->getShowSignature()){
        $htmlInvoice .= '<div class="div-signature" style="padding-top: 50px;padding-bottom: 50px;text-align:right">
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
            $mpdf->SetTitle("Facture #" .$payment->getFacture()->getNumero().$indexPayment);
            $mpdf->SetAuthor("Hello World");
            $mpdf->SetWatermarkText("");
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->watermarkTextAlpha = 0.05;
            $mpdf->SetDisplayMode('fullpage');

            $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
            $mpdf->WriteHTML($htmlInvoice);
            $file_name = $traduction['FACTURE'][$payment->getFacture()->getLangue()] . '-'.$payment->getFacture()->getNumero().$indexPayment.'-'.$invoiceFor.'.pdf';
            if($output == "show"){

                $mpdf->Output($file_name, 'I');
            }
            else{


                $dirPath = $dirPath."uploads/";
                $mpdf->Output($dirPath.$file_name,'F');
                return $file_name;

            }



    }

    // Reçu de paiement (espace client, API à jeton) : même principe que
    // facture::pdfFactureApi() -- entièrement basé sur des tableaux (jamais
    // $_SESSION['agence']/$_SESSION['langue']) pour rester appelable hors
    // d'une session admin CRM. Un client ne peut télécharger que SES PROPRES
    // paiements : vérifié via la facture parente (findByIdApi() refuse déjà
    // tout id_client qui ne correspond pas au token).
    public static function pdfPaymentApi($idClient, $idPayment, $output = "download")
    {
        $token = getToken();
        if (!$token || (int) $token->id !== (int) $idClient) {
            return array("icon" => "error", "message" => "Unauthorized");
        }
        global $db;

        require '../../../vendor/autoload.php';
        require '../../../includes/traduction.php';

        $rows = $db->queryS(sprintf(
            "SELECT id, id_facture, montant, date_payment, numero_sequence, reg_img FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString((int) $idPayment, "int")
        ));
        if (!is_array($rows) || count($rows) === 0) {
            return array("icon" => "error", "message" => "Not found");
        }
        $paymentData = $rows[0];

        $facture = facture::findByIdApi((int) $paymentData['id_facture']);
        if (!is_array($facture)) {
            // Facture introuvable ou n'appartenant pas au client authentifié.
            return array("icon" => "error", "message" => "Unauthorized");
        }
        // Même règle que l'admin (pdfPayment côté controleur) : si la facture n'a
        // en réalité qu'un seul paiement couvrant tout le total, pas de reçu de
        // paiement séparé -- le client doit télécharger la facture globale.
        if (facture::isGlobalPdfAllowedApi($facture["ID"], (float) $facture["total"])) {
            return array("icon" => "warning", "message" => "Cette facture a ete reglee en un seul paiement, merci de telecharger la facture globale.");
        }

        $dirPath = "../../../";
        $typePayment = $traduction['FACTURE'][$facture["langue"]];
        if ($facture["id_facture"]) {
            $facture_ref = facture::findByIdApi($facture["id_facture"]);
            $typePayment = $traduction['AVOIR'][$facture["langue"]];
        }
        $client = $facture["client"];
        $agence = $client["agence"];
        $items = facture::getItemsApi($facture["ID"]);
        $invoiceFor = $client["raison_social"] != '' ? $client["raison_social"] : $client["nom"] . ' ' . $client["prenom"];
        $color = $agence["color"];
        $indexPayment = ($paymentData['numero_sequence'] !== null && $paymentData['numero_sequence'] !== '') ? $paymentData['numero_sequence'] : 1;

        $htmlInvoice = '<html>
<head>
<style>
body {
    font-family: montserrat;
    font-size: 10pt;
}
p { margin: 0pt; }
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
<tr><td style="font-size:8pt;">Total ' . $typePayment . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format((float) $paymentData['montant'], 2, ',', ' ') . ' ' . $facture["devise"] . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Date ' . $typePayment . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($paymentData['date_payment']) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $typePayment . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $facture["numero"] . '-' . $indexPayment . '</strong></td></tr>
<tr><td>' . ($paymentData['reg_img'] != '' ? '<strong style="color:#2e7d32;">' . mb_strtoupper($traduction['PAYE'][$facture["langue"]]) . '</strong>' : '<strong style="color:#c62828;">' . mb_strtoupper($traduction['NON_PAYE'][$facture["langue"]]) . '</strong>') . '</td></tr>
</table>
</td>
</tr></table>
<br />
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
        foreach ($items as $item) {
            $unite = getUnities()[$facture["langue"]][$item["unite"]] ?? '';
            $htmlInvoice .= '<tr>
<td><strong>' . $item["titre"] . '</strong><div style="font-size:8pt; color:#999">' . $item["description"] . '</div></td>
<td align="center" style="vertical-align:middle;">-</td>
<td align="center" style="vertical-align:middle;">' . $item["qte"] . ' x ' . $unite . '</td>
<td align="right" style="vertical-align:middle;" class="cost">-</td>
</tr>';
        }

        $sousTotal = $facture["proforma"] ? number_format((float) $paymentData['montant'], 2, ',', ' ') : number_format((float) $paymentData['montant'] / (1 + ($agence["tva"] / 100)), 2, ',', ' ');
        $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">' . $traduction['SSTOTAL_HT'][$facture["langue"]] . '</td>
<td class="totals cost">' . $sousTotal . ' ' . $facture["devise"] . '</td>
</tr>';

        if (!$facture["proforma"]) {
            $tva = (float) $paymentData['montant'] - ((float) $paymentData['montant'] / (1 + ($agence["tva"] / 100)));
            $htmlInvoice .= '<tr>
<td class="totals">' . $traduction['TVA'][$facture["langue"]] . '</td>
<td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $facture["devise"] . '</td>
</tr>';
        }

        $htmlInvoice .= '</tbody>
</table>
<div style="margin-top:100t;">
<h3 style="color:' . $color . ';">' . $traduction['THANK_YOU_FOR'][$facture["langue"]] . $agence["nom"] . ' !!</h3>
</div>
</body>
</html>';

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10,
            'fontDir' => array_merge($fontDirs, ['../../../fonts/']),
            'fontdata' => $fontData + [
                'montserrat' => [
                    'R' => 'Montserrat-Regular.ttf',
                    'B' => 'Montserrat-Bold.ttf',
                ]
            ],
            'default_font' => 'montserrat'
        ]);

        $mpdf->SetProtection(array('print', 'copy'));
        $mpdf->SetTitle($traduction['FACTURE'][$facture["langue"]] . " #" . $facture["numero"] . '-' . $indexPayment);
        $mpdf->SetAuthor("Hello World");
        $mpdf->SetDisplayMode('fullpage');

        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($htmlInvoice);
        $file_name = 'payment-' . $paymentData['id'] . '-' . str_replace(array(' ', '/', '\\'), '-', trim($invoiceFor)) . '.pdf';
        if ($output == "show") {
            $mpdf->Output($file_name, 'I');
        } else {
            $dirPath = $dirPath . "uploads/";
            $mpdf->Output($dirPath . $file_name, 'F');
            return $file_name;
        }
    }
}
