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
    case 'add':
        // die($facture->getId());
        if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
            $services = service::findAll($_SESSION['langue'], true);
            $clients = client::findAll(true,false,$_SESSION['agence']);
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $banks = bank::findAll($_SESSION['agence']);
            $action = "components/com_facture/controleurs/router.php?task=addFacture";
            $submitName = "add";
            $submitValue = "Ajouter facture";
            $preselectClientId = isset($_GET['id_client']) ? intval($_GET['id_client']) : 0;
            include_once("components/com_facture/views/facture/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $facture = facture::find($id,$_SESSION['agence']);
                $payments = payment::findAll($id);
                $clients = client::findAll(true,false,$_SESSION['agence']);
                $services = service::findAll($_SESSION['langue'], true);
                $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
                $banks = bank::findAll($_SESSION['agence']);
                $action = "components/com_facture/controleurs/router.php?task=editFacture";
                $submitName = "edit";
                $submitValue = "Modifier facture";
                include_once("components/com_facture/views/facture/edit.php");
            }
        }
        break;
    case 'avoir':
        if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $facture = facture::find($id,$_SESSION['agence']);
                $factureavoir = $facture;
                $clients = client::findAll(true,false,$_SESSION['agence']);
                $services = service::findAll($_SESSION['langue'], true);
                $banks = bank::findAll($_SESSION['agence']);
                $action = "components/com_facture/controleurs/router.php?task=addFactureAvoir";
                $submitName = "avoir";
                $submitValue = "Ajouter facture avoir";
                include_once("components/com_facture/views/facture/avoir.php");
            }
        }
        break;
    case 'show':
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $facture = facture::find($id,$_SESSION['agence']);
                $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
                if ($facture->isAvoir()) {
                    include_once("components/com_facture/views/facture/factureavoir.php");
                } else {
                    include_once("components/com_facture/views/facture/facture.php");
                }
            }
        }
        break;
    case 'showDetailsClient' :
        if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $client = client::find($id,$_SESSION['agence']);
                $action = "components/com_client/controleurs/router.php?task=editClient";
                $submitName = "edit";
                $submitValue = "Modifier client";
                $factures = $client->getFacture();
                include_once("components/com_client/views/client/detail.php");
            }
        }
        break;    
    case 'payment':
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $facture = facture::find($id,$_SESSION['agence']);
                $payments = payment::findAll($id);
                include_once("components/com_facture/views/facture/payment.php");
            }
        }
        break;
    case 'payments':
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            $payments = payment::findAll(false,false,$_SESSION['agence']);
            include_once("components/com_facture/views/facture/payments.php");
        }
        break;
    case 'unpaid':
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            $factures = facture::findAll(-1, false, false, true,false,false,$_SESSION['agence']);
            $clients = client::findAll(false,false,$_SESSION['agence']);
            $deviss = devis::findAll(false, true, false, $_SESSION['agence']);
            $contracts = contract::findAll($_SESSION["agence"],$_SESSION["langue"]);
            $allFactures = facture::findAll(false, false, false, true,false,false,$_SESSION['agence']);
            $payments = payment::findAll(false,false,$_SESSION['agence']);
            include_once("components/com_facture/views/facture/list.php");
        }
        break;
    case 'archive':
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            $factures = facture::findAll(false, false, false, false, false, true,$_SESSION['agence']);
            include_once("components/com_facture/views/facture/archive.php");
        }
        break;
    case 'invoice-by-currency':
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            if (isset($_GET['currency']) && !empty($_GET['currency']) && isset($_GET['year']) && !empty($_GET['year'])) {
                $payments = payment::findAll(false,true,$_SESSION['agence'],$_GET['currency'],$_GET['year']);
                $total = payment::total($_GET['year'],false,$_GET['currency'],$_SESSION['agence']);
                $client = isset($_GET['id']) ? client::find(intval($_GET['id'])) : false;            
                include_once("components/com_facture/views/facture/encaissements.php");
            }
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            $factures = facture::findAll(false, false, false, true,false,false,$_SESSION['agence']);
            $deviss = devis::findAll(false, true, false, $_SESSION['agence']);
            $contracts = contract::findAll($_SESSION["agence"],$_SESSION["langue"]);
            $clients = client::findAll(false,false,$_SESSION['agence']);
            $payments = payment::findAll(false,false,$_SESSION['agence']);
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $client = isset($_GET['id']) ? client::find(intval($_GET['id'])) : false;             
            
            include_once("components/com_facture/views/facture/facturation.php");
        }
        break;
}
