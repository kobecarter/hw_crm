<?php

@$task = $_GET['task'];
switch ($task) {
    case 'add':
        if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
            $action = "components/com_relance/controleurs/router.php?task=addRelance";
            $submitName = "add";
            $submitValue = "Ajouter relance";
            $client =null;
            if (isset($_GET['id_client']) && !empty($_GET['id_client'])) {
                $id = intval($_GET['id_client']);
                $client = client::find($id,$_SESSION['agence']);
                $factures = facture::ofClient($id, false, false, true);
            }
            $clients = client::findAll(true,false,$_SESSION['agence']);
            include_once("components/com_relance/views/relance/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_relance')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $relance = relance::find($id, $_SESSION['agence']);
                $action = "components/com_relance/controleurs/router.php?task=editRelance";
                $submitName = "edit";
                $submitValue = "Modifier relance";
                $clients = client::findAll(true,false,$_SESSION['agence']);
                $factures = facture::ofClient($relance->getClient()->getId(), false, false, true);
                include_once("components/com_relance/views/relance/edit.php");
            }
        }
        break;
    case 'client':
        if ($_SESSION['user']->hasDroit('view', 'com_relance')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $client = client::find($id,$_SESSION['agence']);
                $relances = relance::findByClient($id,$_SESSION['agence']);
                include_once("components/com_relance/views/relance/view.php");
            } else{
                $clients = client::findWhereHaveRelance(true,false,$_SESSION['agence']);
                include_once("components/com_relance/views/relance/list.php");
            }
                
        }
        break;
    case 'list':
        if ($_SESSION['user']->hasDroit('view', 'com_relance')) {
                $relances = relance::findAllNonTraite($_SESSION['agence']);
                include_once("components/com_relance/views/relance/list2.php");
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_relance')) {
            $clients = client::findWhereHaveRelance(true,false,$_SESSION['agence']);
            include_once("components/com_relance/views/relance/list.php");
        }
        break;
}
