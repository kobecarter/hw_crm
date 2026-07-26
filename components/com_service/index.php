<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_service')) {
            $action = "components/com_service/controleurs/router.php?task=addService";
            $submitName = "add";
            $submitValue = "Ajouter service";
			$categories = categorie::findAll($_SESSION["langue"], true, true);
            include_once("components/com_service/views/service/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_service')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $service = service::find($id, $_SESSION['langue']);
                $action = "components/com_service/controleurs/router.php?task=editService";
                $submitName = "edit";
                $submitValue = "Modifier service";
                $categories = categorie::findAll($_SESSION["langue"], true, true);
                include_once("components/com_service/views/service/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_service')) {
            $services = service::findAll($_SESSION["langue"], false, false, true);
            include_once("components/com_service/views/service/list.php");
        }
        break;
}