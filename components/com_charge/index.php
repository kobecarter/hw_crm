<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_charge')) {
            $action = "components/com_charge/controleurs/router.php?task=addCharge";
            $submitName = "add";
            $submitValue = "Ajouter charge";
			$charges = charge::findAll(true,$_SESSION['agence']);
			$users = user::findAll();
            include_once("components/com_charge/views/charge/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_charge')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $charge = charge::find($id,$_SESSION['agence']);
                $users = user::findAll();
                $action = "components/com_charge/controleurs/router.php?task=editCharge";
                $submitName = "edit";
                $submitValue = "Modifier charge";
				$charges = charge::findAll(true,$_SESSION['agence']);
                include_once("components/com_charge/views/charge/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_charge')) {
            $charges = charge::findAll(true,$_SESSION['agence']);
            include_once("components/com_charge/views/charge/list.php");
        }
        break;
}