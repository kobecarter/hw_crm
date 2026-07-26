<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_cheque')) {
            $action = "components/com_cheque/controleurs/router.php?task=addCheque";
            $submitName = "add";
            $submitValue = "Ajouter un cheque";
			$cheques = cheque::findAll(true,$_SESSION['agence']);
			$users = user::findAll();
            include_once("components/com_cheque/views/cheque/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_cheque')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $cheque = cheque::find($id,$_SESSION['agence']);
                $users = user::findAll();
                $action = "components/com_cheque/controleurs/router.php?task=editCheque";
                $submitName = "edit";
                $submitValue = "Modifier le cheque";
				$cheques = cheque::findAll(true,$_SESSION['agence']);
                include_once("components/com_cheque/views/cheque/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_cheque')) {
            $cheques = cheque::findAll(true,$_SESSION['agence']);
            include_once("components/com_cheque/views/cheque/list.php");
        }
        break;
}