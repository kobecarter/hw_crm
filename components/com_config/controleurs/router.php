<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'updateConfig' :
            if ($_SESSION['user']->hasDroit('add', 'com_config')) {
                include_once ("config/controleur.php");
            }
            break;
		case 'deleteLogo' :
            if ($_SESSION['user']->hasDroit('delete', 'com_config')) {
                include_once ("config/controleur.php");
            }
            break;
		case 'switchLang' :
			include_once ("config/controleur.php");
            break;	
            break;
		case 'switchAgence' :
			include_once ("config/controleur.php");
            break;	
    }
}