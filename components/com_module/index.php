<?php
@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_localisation')) {
            $action = "components/com_localisation/controleurs/router.php?task=addLocalisation";
            $submitName = "add";
            $submitValue = $trad_com_localisation['AJOUT_LOCALISATION'][$_SESSION['user']->getLangue()];
            include_once("components/com_localisation/views/localisation/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_localisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $localisation = localisation::find($id, $_SESSION['langue']);
                $action = "components/com_localisation/controleurs/router.php?task=editLocalisation";
                $submitName = "edit";
                $submitValue = $trad_com_localisation['MODIF_LOCATION'][$_SESSION['user']->getLangue()];
                include_once("components/com_localisation/views/localisation/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_module')) {
            $modules = module::findAll($_SESSION['langue']);
            include_once("components/com_module/views/module/list.php");
        }
        break;
}