<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_expertise')) {
            $action = "components/com_expertise/controleurs/router.php?task=addExpertise";
            $submitName = "add";
            $submitValue = "Ajouter expertise";
            $parents = expertise::findAll($_SESSION["langue"], false, true);
            include_once("components/com_expertise/views/expertise/add.php");
        }
        break;
    case 'edit' :
		
        if ($_SESSION['user']->hasDroit('edit', 'com_expertise')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
				
                $expertise = expertise::find($id, $_SESSION['langue']);
				
                $action = "components/com_expertise/controleurs/router.php?task=editExpertise";
                $submitName = "edit";
                $submitValue = "Modifier expertise";
				
                $parents = expertise::findAll($_SESSION["langue"], false, true);
				//echo "test";
				
                include_once("components/com_expertise/views/expertise/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_expertise')) {
            $expertises = expertise::findAll($_SESSION["langue"], false, true);
            include_once("components/com_expertise/views/expertise/list.php");
        }
        break;
}