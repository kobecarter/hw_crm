<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_reclamation')) {
            $action = "components/com_reclamation/controleurs/router.php?task=addReclamation";
            $submitName = "add";
            $submitValue = "Ajouter reclamation";
			$clients = client::findAll(false,false,$_SESSION['agence']);
            include_once("components/com_reclamation/views/reclamation/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_reclamation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $reclamation = reclamation::find($id,$_SESSION['agence']);
                $action = "components/com_reclamation/controleurs/router.php?task=editReclamation";
                $submitName = "edit";
                $submitValue = "Modifier reclamation";
				$clients = client::findAll(false,false,$_SESSION['agence']);
                include_once("components/com_reclamation/views/reclamation/edit.php");
            }
        }
        break;	
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_reclamation')) {
            $reclamations = reclamation::findAll(false,false,$_SESSION['agence']);
            include_once("components/com_reclamation/views/reclamation/list.php");
        }
        break;
}