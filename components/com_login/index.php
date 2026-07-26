<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_operation')) {
            $action = "components/com_operation/controleurs/router.php?task=addOperation";
            $submitName = "add";
            $submitValue = "Ajouter operation";
            include_once("views/operation/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_operation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $operation = operation::find($id, $_SESSION['langue']);
                $action = "components/com_operation/controleurs/router.php?task=editOperation";
                $submitName = "edit";
                $submitValue = "Modifier operation";
                include_once("views/operation/edit.php");
            }
        }
        break;
    default :
		include_once("views/login/login.php");
        break;
}