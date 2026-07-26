<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_temoignage')) {
            $action = "components/com_temoignage/controleurs/router.php?task=addTemoignage";
            $submitName = "add";
            $submitValue = "Ajouter témoignage";
            $parents = temoignage::findAll($_SESSION["langue"], false, false);
            include_once("components/com_temoignage/views/temoignage/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_temoignage')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $temoignage = temoignage::find($id, $_SESSION['langue']);
                $action = "components/com_temoignage/controleurs/router.php?task=editTemoignage";
                $submitName = "edit";
                $submitValue = "Modifier témoignage";
                $parents = temoignage::findAll($_SESSION["langue"], false, false);
                include_once("components/com_temoignage/views/temoignage/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_temoignage')) {
            $temoignages = temoignage::findAll($_SESSION["langue"], false, false);
            include_once("components/com_temoignage/views/temoignage/list.php");
        }
        break;
}