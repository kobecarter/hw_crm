<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addCharge' :
            if ($_SESSION['user']->hasDroit('add', 'com_charge')) {
                include_once ("charge/controleur.php");
            }
            break;
        case 'editCharge' :
            if ($_SESSION['user']->hasDroit('edit', 'com_charge')) {
                include_once ("charge/controleur.php");
            }
            break;
        case 'deleteCharge' :
            if ($_SESSION['user']->hasDroit('delete', 'com_charge')) {
                include_once ("charge/controleur.php");
            }
            break;
        case "enableCharge":
            if ($_SESSION['user']->hasDroit('edit', 'com_charge')) {
                include_once ("charge/controleur.php");
            }
            break;
        case "exportCharges":
            if ($_SESSION['user']->hasDroit('view', 'com_charge')) {
                include_once ("charge/controleur.php");
            }
            break;
    }
}