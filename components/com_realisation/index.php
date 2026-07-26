<?php

@$task = $_GET['task'];
switch ($task) {
    case 'add':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
            $action = "components/com_realisation/controleurs/router.php?task=addRealisation";
            $submitName = "add";
            $submitValue = "Ajouter realisation";
            $clients = client::findAll();
            include_once("components/com_realisation/views/realisation/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $realisation = realisation::find($id);
                $action = "components/com_realisation/controleurs/router.php?task=editRealisation";
                $submitName = "edit";
                $submitValue = "Modifier realisation";
                $clients = client::findAll();
                include_once("components/com_realisation/views/realisation/edit.php");
            }
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_realisation')) {
            $realisations = realisation::findAll();
            include_once("components/com_realisation/views/realisation/list.php");
        }
        break;
}
