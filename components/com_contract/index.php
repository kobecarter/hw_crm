<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_contract')) {
            $action = "components/com_contract/controleurs/router.php?task=addContract";
            $submitName = "add";
            $submitValue = "Ajouter contrat";
            $devises = devis::findAll(false,false,false,$_SESSION['agence']);
            include_once("components/com_contract/views/contract/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_contract')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $contract = contract::find($id, $_SESSION['agence'], $_SESSION['langue']);
                $devises = devis::findAll(false,false,false,$_SESSION['agence']);
                $action = "components/com_contract/controleurs/router.php?task=editContract";
                $submitName = "edit";
                $submitValue = "Modifier contract";
                include_once("components/com_contract/views/contract/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_contract')) {
            $contracts = contract::findAll($_SESSION["agence"],$_SESSION["langue"]);
            include_once("components/com_contract/views/contract/list.php");
        }
        break;
}