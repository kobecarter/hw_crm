<?php

@$task = $_GET['task'];
switch ($task) {
    case 'add':
        if ($_SESSION['user']->hasDroit('add', 'com_devis')) {
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $services = service::findAll($_SESSION['langue'], true);
            $clients = client::findAll(true,false,$_SESSION['agence']);
            $banks = bank::findAll();
            $action = "components/com_devis/controleurs/router.php?task=addDevis";
            $submitName = "add";
            $submitValue = "Ajouter devis";
            include_once("components/com_devis/views/devis/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
                $devis = devis::find($id, $_SESSION['agence']);
                $clients = client::findAll(true,false,$_SESSION['agence']);
                $services = service::findAll($_SESSION['langue'], true);
                $banks = bank::findAll();
                $action = "components/com_devis/controleurs/router.php?task=editDevis";
                $submitName = "edit";
                $submitValue = "Modifier devis";
                include_once("components/com_devis/views/devis/edit.php");
            }
        }
        break;
    case 'show':
        if ($_SESSION['user']->hasDroit('view', 'com_devis')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
                $devis = devis::find($id, $_SESSION['agence']);
                include_once("components/com_devis/views/devis/devis.php");
            }
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_devis')) {
            $deviss = devis::findAll(false, true, false, $_SESSION['agence']);
            include_once("components/com_devis/views/devis/list.php");
        }
        break;
}
