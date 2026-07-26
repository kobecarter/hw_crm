<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addAgence' :
            if ($_SESSION['user']->hasDroit('add', 'com_agence')) {
                include_once ("agence/controleur.php");
            }
            break;
        case 'editAgence' :
            if ($_SESSION['user']->hasDroit('edit', 'com_agence')) {
                include_once ("agence/controleur.php");
            }
            break;
        case 'deleteAgence' :
            if ($_SESSION['user']->hasDroit('delete', 'com_agence')) {
                include_once ("agence/controleur.php");
            }
            break; 
        case 'deleteLogo' :
            if ($_SESSION['user']->hasDroit('delete', 'com_agence')) {
                include_once ("agence/controleur.php");
            }
            break; 
        case 'deleteSignature' :
            if ($_SESSION['user']->hasDroit('delete', 'com_agence')) {
                include_once ("agence/controleur.php");
            }
            break; 

    }
}