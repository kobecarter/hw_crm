<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addReclamation' :
            if ($_SESSION['user']->hasDroit('add', 'com_reclamation')) {
                include_once ("reclamation/controleur.php");
            }
            break;
        case 'editReclamation' :
            if ($_SESSION['user']->hasDroit('edit', 'com_reclamation')) {
                include_once ("reclamation/controleur.php");
            }
            break;
        case 'deleteReclamation' :
            if ($_SESSION['user']->hasDroit('delete', 'com_reclamation')) {
                include_once ("reclamation/controleur.php");
            }
            break;
        case 'enableReclamation' :
            if ($_SESSION['user']->hasDroit('edit', 'com_reclamation')) {
                include_once ("reclamation/controleur.php");
            }
            break;
        case 'findAllByClientApi' :
                include_once ("reclamation/controleur.php");
            break;
        case 'createReclamationApi' :
                include_once ("reclamation/controleur.php");
            break;
        case 'updateReclamationApi' :
                include_once ("reclamation/controleur.php");
            break;

    }
}