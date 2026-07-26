<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addFournisseur' :
            if ($_SESSION['user']->hasDroit('add', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;
        case 'editFournisseur' :
            if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;
        case 'deleteFournisseur' :
            if ($_SESSION['user']->hasDroit('delete', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;
        case 'enableFournisseur' :
            if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;
		case 'filterFournisseur' :
            if ($_SESSION['user']->hasDroit('view', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;	
		case 'exportFournisseur' :
            if ($_SESSION['user']->hasDroit('view', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;	
        case 'removeDoc' :
            if ($_SESSION['user']->hasDroit('delete', 'com_fournisseur')) {
                include_once ("fournisseur/controleur.php");
            }
            break;	    
    }
}