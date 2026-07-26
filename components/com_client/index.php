<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_client')) {
            $agences = agence::findAll($_SESSION['langue']);
            $action = "components/com_client/controleurs/router.php?task=addClient";
            $submitName = "add";
            $submitValue = "Ajouter client";
            include_once("components/com_client/views/client/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $client = client::find($id,$_SESSION['agence']);
                if($client->getId() != 0){
                    $agences = agence::findAll($_SESSION['langue']);
                    $action = "components/com_client/controleurs/router.php?task=editClient";
                    $submitName = "edit";
                    $submitValue = "Modifier client";
                    $factures = $client->getFacture();
                    include_once("components/com_client/views/client/edit.php");
                }else{
                    $clients = client::findAll(false,false,$_SESSION['agence']);
                    include_once("components/com_client/views/client/list.php");
                }
                
            }
        }
        break;
    case 'showDetails' :
        if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $client = client::find($id,$_SESSION['agence']);
                $factures = $client->getFacture();
                $deviss = $client->getDevis();
                $rappels = rappel::findAll(false,$_SESSION['agence'],$id);
                $relances = relance::findByClient($id,$_SESSION['agence']);
                include_once("components/com_client/views/client/detail.php");
            }
        }
        break;    	
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_client')) {			
            $clients = client::findAll(false,false,$_SESSION['agence']);
            include_once("components/com_client/views/client/list.php");
        }
        break;
}