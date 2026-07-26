<?php

@$task = $_GET['task'];
switch ($task) {
    case 'add':
        if ($_SESSION['user']->hasDroit('add', 'com_rappel')) {
            $action = "components/com_rappel/controleurs/router.php?task=addRappel";
            $submitName = "add";
            $submitValue = "Ajouter rappel";
            $clients = client::findAll(true,false,$_SESSION['agence']);
            include_once("components/com_rappel/views/rappel/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $rappel = rappel::find($id,$_SESSION['agence']);
                $action = "components/com_rappel/controleurs/router.php?task=editRappel";
                $submitName = "edit";
                $submitValue = "Modifier rappel";
                $clients = client::findAll(true,false,$_SESSION['agence']);
                include_once("components/com_rappel/views/rappel/edit.php");
            }
        }
        break;
    case 'archive':
        if ($_SESSION['user']->hasDroit('view', 'com_rappel')) {
            $rappels = rappel::findAll(true,$_SESSION['agence']);
            include_once("components/com_rappel/views/archive/list.php");
        }
        break;
    case 'relances':
        if ($_SESSION['user']->hasDroit('view', 'com_rappel')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);

                include_once("components/com_rappel/views/relance/view.php");
            } else
                include_once("components/com_rappel/views/relance/list.php");
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_rappel')) {
            $rappels = rappel::findAll(false,$_SESSION['agence']);
            include_once("components/com_rappel/views/rappel/list.php");
        }
        break;
}
