<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_agence')) {
            $action = "components/com_agence/controleurs/router.php?task=addAgence";
            $submitName = "add";
            $submitValue = "Ajouter agence";
            include_once("components/com_agence/views/agence/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_agence')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $agenceToEdit = agence::find($id, $_SESSION['langue']);
                $action = "components/com_agence/controleurs/router.php?task=editAgence";
                $submitName = "edit";
                $submitValue = "Modifier agence";
                include_once("components/com_agence/views/agence/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_agence')) {
            $agences = agence::findAll($_SESSION["langue"], false, false, true);
            include_once("components/com_agence/views/agence/list.php");
        }
        break;
}