<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'deleteModule' :
            if ($_SESSION['user']->hasDroit('delete', 'com_module')) {
                include_once ("module/controleur.php");
            }
            break;
        case 'installModule' :
            if ($_SESSION['user']->hasDroit('add', 'com_module')) {
                include_once ("module/controleur.php");
            }
            break;
		case 'desinstallModule' :
            if ($_SESSION['user']->hasDroit('delete', 'com_module')) {
                include_once ("module/controleur.php");
            }
            break;	
        case 'enableModule' :
            if ($_SESSION['user']->hasDroit('edit', 'com_module')) {
                include_once ("module/controleur.php");
            }
            break;
        case 'disableModule' :
            if ($_SESSION['user']->hasDroit('edit', 'com_module')) {
                include_once ("module/controleur.php");
            }
            break;
    }
}