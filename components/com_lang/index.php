<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_lang')) {
            $action = "components/com_lang/controleurs/router.php?task=addLang";
            $submitName = "add";
            $submitValue = "Ajouter langue";
            include_once("components/com_lang/views/lang/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_lang')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $lg = langue::find($id, $_SESSION['langue']);
                $action = "components/com_lang/controleurs/router.php?task=editLang";
                $submitName = "edit";
                $submitValue = "Modifier langue";
                include_once("components/com_lang/views/lang/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_lang')) {
            $langs = langue::findAll();
            include_once("components/com_lang/views/lang/list.php");
        }
        break;
}