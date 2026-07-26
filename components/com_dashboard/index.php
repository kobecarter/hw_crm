<?php

@$task = $_GET['task'];
@$secure = $_SESSION['secure'];

if($_SESSION['user']->getProfil()->getProfil() != "Commercial" && !$_SESSION['user']->isResourceHumaine() ){
    if(!isset($secure) || $secure != $secureCode){
        include_once("components/com_dashboard/views/dashboard/password.php");
        return;
    }
}

switch ($task) {
    case 'stats' : 
		$from = date('Y-01-01');
		$to = date('Y-m-d');
		$creances = facture::getCreanceByDate($from, $to,'DH', $_SESSION['agence']);
		$creancesEuro = facture::getCreanceByDate($from, $to,'€', $_SESSION['agence']);
		$creancesPound = facture::getCreanceByDate($from, $to,'£', $_SESSION['agence']);
		$creancesDollar = facture::getCreanceByDate($from, $to,'$', $_SESSION['agence']);
		$creancesAed = facture::getCreanceByDate($from, $to,'AED', $_SESSION['agence']);

		$factureTotal = facture::getCAbyDate(false, $from, $to,'DH', $_SESSION['agence']);
		$factureTotalEuro = facture::getCAbyDate(false, $from, $to,'€', $_SESSION['agence']);
		$factureTotalPound = facture::getCAbyDate(false, $from, $to,'£', $_SESSION['agence']);
		$factureTotalDollar = facture::getCAbyDate(false, $from, $to,'$', $_SESSION['agence']);
		$factureTotalAed = facture::getCAbyDate(false, $from, $to,'AED', $_SESSION['agence']);
		
		$totalReglement = payment::getReglementbyDate($from, $to, 'DH', $_SESSION['agence']);
		$totalReglementEuro = payment::getReglementbyDate($from, $to,'€', $_SESSION['agence']);
		$totalReglementPound = payment::getReglementbyDate($from, $to,'£', $_SESSION['agence']);
		$totalReglementDollar = payment::getReglementbyDate($from, $to,'$', $_SESSION['agence']);
		$totalReglementAed = payment::getReglementbyDate($from, $to,'AED', $_SESSION['agence']);
        $charges = charge::getCharge($from, $to, $_SESSION['agence']);

		include_once("components/com_dashboard/views/dashboard/stats.php");
		break;
	case 'globalStats' : 
		$from = date('Y-01-01');
		$to = date('Y-m-d');
		$creances = facture::getCreanceByDate($from, $to,'DH', false);
		$creancesEuro = facture::getCreanceByDate($from, $to,'€', false);
		$creancesPound = facture::getCreanceByDate($from, $to,'£', false);
		$creancesDollar = facture::getCreanceByDate($from, $to,'$', false);
		$creancesAed = facture::getCreanceByDate($from, $to,'AED', false);

		$factureTotal = facture::getCAbyDate(false, $from, $to,'DH', false);
		$factureTotalEuro = facture::getCAbyDate(false, $from, $to,'€', false);
		$factureTotalPound = facture::getCAbyDate(false, $from, $to,'£', false);
		$factureTotalDollar = facture::getCAbyDate(false, $from, $to,'$', false);
		$factureTotalAed = facture::getCAbyDate(false, $from, $to,'AED', false);
		
		$totalReglement = payment::getReglementbyDate($from, $to, 'DH', false);
		$totalReglementEuro = payment::getReglementbyDate($from, $to,'€', false);
		$totalReglementPound = payment::getReglementbyDate($from, $to,'£', false);
		$totalReglementDollar = payment::getReglementbyDate($from, $to,'$', false);
		$totalReglementAed = payment::getReglementbyDate($from, $to,'AED', false);
        
        $charges = charge::getCharge($from, $to, false, 'DH');
        $chargesEuro = charge::getCharge($from, $to, false, '€');
        $chargesPound = charge::getCharge($from, $to, false, '£');
        $chargesDollar = charge::getCharge($from, $to, false, '$');
        $chargesAed = charge::getCharge($from, $to, false, 'AED');

		include_once("components/com_dashboard/views/dashboard/stats-global.php");
		break;	
    case 'absences':
        if($_SESSION['user']->isResourceHumaine()){
            $resourcehumaine = $_SESSION['user'];
            $absences = absence::findAllByResourcehumaine($resourcehumaine->getId());
            include_once("components/com_resourcehumaine/views/absence/profile_absences.php");
        }
        break;
    case 'files':
        if($_SESSION['user']->isResourceHumaine()){
            $resourcehumaine = $_SESSION['user'];
            $files = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
            include_once("components/com_resourcehumaine/views/fileresourcehumaine/profile_filesresourcehumaine.php");
        }
        break;

    case 'payslips':
        if($_SESSION['user']->isResourceHumaine()){
            $resourcehumaine = $_SESSION['user'];
            $payslips = payslip::findAllByResourcehumaine($resourcehumaine->getId());
            include_once("components/com_resourcehumaine/views/payslip/profile_payslips.php");
        }
        break;
    case 'requests':
        if($_SESSION['user']->isResourceHumaine()){
            $resourcehumaine = $_SESSION['user'];
            $action1 = "components/com_resourcehumaine/controleurs/router.php?task=addRequest";
            $action2 = "components/com_resourcehumaine/controleurs/router.php?task=editRequest";
            $action3 = "components/com_resourcehumaine/controleurs/router.php?task=deleteRequest";
            $requests = request::findAllByResourcehumaine($resourcehumaine->getId());
            if(isset($_GET['id_request']) && !empty($_GET['id_request'])){
                $id_request = intval($_GET['id_request']);
                $request = request::find($id_request);
            }
            include_once("components/com_resourcehumaine/views/request/profile_requests.php");
        }
        break;
    case 'bonuses':
        if($_SESSION['user']->isResourceHumaine()){
            $resourcehumaine = $_SESSION['user'];
            $bonuses = bonus::findAllByResourcehumaine($resourcehumaine->getId());
            include_once("components/com_resourcehumaine/views/bonus/profile_bonuses.php");
        }
        break;
	default:
		if ($_SESSION['user']->hasDroit('view', 'com_dashboard')) {
            if($_SESSION['user']->isResourceHumaine()){
                $resourcehumaine = $_SESSION['user'];
                $files = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
                $absences = absence::findAllByResourcehumaine($resourcehumaine->getId());
            }else{
                $devisNumber = devis::count(false, date('Y'), $_SESSION['agence']);
                $devisNumberInvalide = devis::count(2, date('Y'), $_SESSION['agence']);
                $devisNumberInvalide2 = devis::count(2, date('Y') - 1, $_SESSION['agence']);
                $devisNumberInvalide3 = devis::count(2, date('Y') - 2, $_SESSION['agence']);
    
                $factureNumber = facture::count(false, date('Y'),false,$_SESSION['agence']);
                $factureTotal = facture::total(false, date('Y'),'DH',$_SESSION['agence']);
                $factureTotalEur = facture::total(false, date('Y'),'€',$_SESSION['agence']);
                $factureTotalPound = facture::total(false, date('Y'), '£',$_SESSION['agence']);
                $factureTotalDollar = facture::total(false, date('Y'), '$',$_SESSION['agence']);
                $factureTotalAed = facture::total(false, date('Y'), 'AED',$_SESSION['agence']);
                
                // Les créances
                $creances = facture::getCreance(false,'DH',$_SESSION['agence']);
                $creancesYear = facture::getCreance(date('Y'),'DH',$_SESSION['agence']);
                $creancesYearEuro = facture::getCreance(date('Y'),'€',$_SESSION['agence']);
                $creancesYearPound = facture::getCreance(date('Y'),'£',$_SESSION['agence']);
                $creancesYearDollar = facture::getCreance(date('Y'),'$',$_SESSION['agence']);
                $creancesYearAed = facture::getCreance(date('Y'),'AED',$_SESSION['agence']);
    
                $clientNumber = client::count(false,$_SESSION['agence']);
                $clientNumberYear = client::count(date('Y'),$_SESSION['agence']);
                $clientNumberYear2 = client::count(date('Y') - 1,$_SESSION['agence']);
                $clientNumberYear3 = client::count(date('Y') - 2,$_SESSION['agence']);
    
                $lastFactures = facture::findAll(false, false, false, true, 5,false,$_SESSION['agence']);
                $lastDevis = devis::findAll(false, true, 5, $_SESSION['agence']);
            }
			
			include_once("components/com_dashboard/views/dashboard/list.php");
		}
		break;
}

// Export data base
$id_agence = 2;
/*
// client table
$SQLselect = "SELECT * FROM hwd_client";
$result = $db->queryS($SQLselect);
foreach($result as $data){
    $client = new client();
    $client->setAgence(agence::find($id_agence, $_SESSION['langue']));
    $client->setActive($data['active']);
    $client->setTitre($data['titre']);
    $client->setPrenom($data['prenom']);
    $client->setNom($data['nom']);
    $client->setRaisonSocial($data['raison_social']);
    $client->setICE($data['ice']);
    $client->setTel($data['tel']);
    $client->setTel2($data['tel2']);
    $client->setTel3($data['tel3']);
    $client->setEmail($data['email']);
    $client->setPassword($data['password']);
    $client->setCp($data['cp']);
    $client->setAdresse($data['adresse']);
    $client->setAdresse2($data['adresse2']);
    $client->setVille($data['ville']);
    $client->setRegion($data['region']);
    $client->setPays($data['pays']);
    $client->setPhoto($data['photo']);
    $client->setDateAdd($data['date_add']);
    $client->setLastEdit($data['last_edit']);
    if($client->add() == 1){
        $id_client = client::getLastId();
        // Les devis
        $SQLselect2 = "SELECT * FROM hwd_devis WHERE id_client = " . $data['id'];
        $result2 = $db->queryS($SQLselect2);
        foreach($result2 as $data2){
            $devis = new devis();
            $devis->setNumero($data2['numero']);
            $devis->setClient(client::find($id_client,$_SESSION['agence']));
            $devis->setDateDevis($data2['date_devis']);
            $devis->setTotal($data2['total']);
            $devis->setStatu($data2['statu']);
            $devis->setDevise($data2['devise']);
            $devis->setDiscount($data2['discount']);
            $devis->setDiscountVal($data2['discount_val']);
            $devis->setConditionPaiment($data2['condition_paiment']);
            $devis->setRemarque($data2['remarque']);
            $devis->setProforma($data2['proforma']);
            $devis->setLangue($data2['langue']);
            $devis->setDateAdd($data2['date_add']);
            $devis->setLastEdit($data2['last_edit']);
            if($devis->add() == 1){
                $id_devis = devis::getLastId();
                
                // Les élements du devis
                $SQLselect3 = "SELECT * FROM hwd_item_devis WHERE id_devis = " . $data2['id'];
                $result3 = $db->queryS($SQLselect3);
                foreach($result3 as $data3){
                    $item_devis = new item_devis();
                    $item_devis->setDevis(devis::find($id_devis, $_SESSION['agence']));
                    $item_devis->setService(service::find($data3['id_service']));
                    $item_devis->setQte($data3['qte']);
                    $item_devis->setPrix($data3['prix']);
                    $item_devis->setTotal($data3['total']);
                    $item_devis->setUnite($data3['unite']);
                    $item_devis->setTitre($data3['titre']);
                    $item_devis->setDescription($data3['description']);
                    $item_devis->setOrdre($data3['ordre']);
                    $item_devis->add();
                }                
            }
        }


        // Les factures
        $SQLselect4 = "SELECT * FROM hwd_facture WHERE id_client = " . $data['id'];
        $result4 = $db->queryS($SQLselect4);
        foreach($result4 as $data4){
            $facture = new facture();
            $facture->setNumero($data4['numero']);
            $facture->setSecNumero($data4['sec_numero']);
            $facture->setClient(client::find($id_client,$_SESSION['agence']));
            //$facture->setDevis(devis::find($data['id_devis']));
            $facture->setDateFacture($data4['date_facture']);
            $facture->setTotal($data4['total']);
            $facture->setStatu($data4['statu']);
            $facture->setDevise($data4['devise']);
            $facture->setDiscount($data4['discount']);
            $facture->setDiscountVal($data4['discount_val']);
            $facture->setConditionPaiment($data4['condition_paiment']);
            $facture->setRemarque($data4['remarque']);
            $facture->setProforma($data4['proforma']);
            $facture->setLangue($data4['langue']);
            $facture->setDateAdd($data4['date_add']);
            $facture->setLastEdit($data4['last_edit']);
            $facture->setAvoir($data4['avoir']);
            $facture->setArchived($data4['archived']);
            //$facture->setIdFacture($data4['id_facture']);

            if($facture->add() == 1){
                $id_facture = facture::getLastId();
                // Les élements de facture
                $SQLselect5 = "SELECT * FROM hwd_item_facture WHERE id_facture = " . $data4['id'];
                $result5 = $db->queryS($SQLselect5);
                foreach($result5 as $data5){
                    $item_facture = new item_facture();
                    $item_facture->setFacture(facture::find($id_facture,$_SESSION['agence']));
                    $item_facture->setService(service::find($data5['id_service']));
                    $item_facture->setQte($data5['qte']);
                    $item_facture->setPrix($data5['prix']);
                    $item_facture->setTotal($data5['total']);
                    $item_facture->setUnite($data5['unite']);
                    $item_facture->setTitre($data5['titre']);
                    $item_facture->setDescription($data5['description']);
                    $item_facture->setOrdre($data5['ordre']);
                    $item_facture->add();
                }

                // Les reglements de facture
                $SQLselect6 = "SELECT * FROM hwd_payment WHERE id_facture = " . $data4['id'];
                $result6 = $db->queryS($SQLselect6);
                foreach($result6 as $data6){
                    $payment = new payment();
                    $payment->setFacture(facture::find($id_facture,$_SESSION['agence']));
                    $payment->setMontant($data6['montant']);
                    $payment->setRegImg($data6['reg_img']);
                    $payment->setDatePayment($data6['date_payment']);
                    $payment->setMethodePayment($data6['methode_payment']);
                    $payment->setDateValidation($data6['date_validation']);
                    $payment->setDetail($data6['detail']);
                    $payment->setDateAdd($data6['date_add']);
                    $payment->setLastEdit($data6['last_edit']);
                    $payment->add();
                }
            }
        }

        // Les rappels
        $SQLselect7 = "SELECT * FROM hwd_rappel WHERE id_client = " . $data['id'];
        $result7 = $db->queryS($SQLselect7);
        foreach($result7 as $data7){
            $rappel = new rappel();
            $rappel->setClient(client::find($id_client,$_SESSION['agence']));
            $rappel->setType($data7['type']);
            $rappel->setDomaine($data7['domaine']);
            $rappel->setDateExpir($data7['date_expir']);
            $rappel->setRemarque($data7['remarque']);
            $rappel->setDateAdd($data7['date_add']);
            $rappel->setLastEdit($data7['last_edit']);
            $rappel->setArchived($data7['archived']);
            $rappel->add();
        }
    }
    
}    

// Les charges
$SQLselect7 = "SELECT * FROM hwd_charge";
$result7 = $db->queryS($SQLselect7);
foreach($result7 as $data7){
    $charge = new charge();
    $charge->setId($data['ID']);
    $charge->setAgence(agence::find($id_agence,$_SESSION['langue']));
    $charge->setUser(user::find(1));
    $charge->setType($data7['type']);
    $charge->setTitre($data7['titre']);
    $charge->setDescription($data7['description']);
    $charge->setTotal($data7['total']);
    $charge->setDevise($data7['devise']);
    $charge->setPaid($data7['paid']);
    $charge->setFacture($data7['facture']);
    $charge->setDateCharge($data7['date_charge']);
    $charge->setDatePayment($data7['date_payment']);
    $charge->setModePayment($data7['mode_payment']);
    $charge->setPhoto($data7['photo']);
    $charge->setDateAdd($data7['date_add']);
    $charge->setLastEdit($data7['last_edit']);
    $charge->add();
}

// Les fournisseurs
$SQLselect8 = "SELECT * FROM hwd_fournisseur";
$result8 = $db->queryS($SQLselect8);
foreach($result8 as $data8){
    $fournisseur = new fournisseur();
    $fournisseur->setAgence(agence::find($id_agence,$_SESSION['langue']));
    $fournisseur->setActive($data8['active']);
    $fournisseur->setTitre($data8['titre']);
    $fournisseur->setPrenom($data8['prenom']);
    $fournisseur->setNom($data8['nom']);
    $fournisseur->setRaisonSocial($data8['raison_social']);
    $fournisseur->setICE($data8['ice']);
    $fournisseur->setTel($data8['tel']);
    $fournisseur->setTel2($data8['tel2']);
    $fournisseur->setTel3($data8['tel3']);
    $fournisseur->setEmail($data8['email']);
    $fournisseur->setPassword($data8['password']);
    $fournisseur->setCp($data8['cp']);
    $fournisseur->setAdresse($data8['adresse']);
    $fournisseur->setAdresse2($data8['adresse2']);
    $fournisseur->setVille($data8['ville']);
    $fournisseur->setRegion($data8['region']);
    $fournisseur->setPays($data8['pays']);
    $fournisseur->setPhoto($data8['photo']);
    $fournisseur->setDateAdd($data8['date_add']);
    $fournisseur->setLastEdit($data8['last_edit']);
    $fournisseur->add();
}
*/

