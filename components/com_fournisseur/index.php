<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_fournisseur')) {
            $action = "components/com_fournisseur/controleurs/router.php?task=addFournisseur";
            $submitName = "add";
            $submitValue = "Ajouter fournisseur";
            include_once("components/com_fournisseur/views/fournisseur/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $fournisseur = fournisseur::find($id,$_SESSION['agence']);
                $action = "components/com_fournisseur/controleurs/router.php?task=editFournisseur";
                $submitName = "edit";
                $submitValue = "Modifier fournisseur";
                include_once("components/com_fournisseur/views/fournisseur/edit.php");
            }
        }
        break;	
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_fournisseur')) {
            include_once("components/com_fournisseur/views/fournisseur/list.php");
        }
        break;
}