<?php
require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'login' :
			include_once ("login.php");
            break;
        case 'login_global' :
			include_once ("login.php");
            break;
        case 'logout' :
			include_once ("login.php");
            break;
        case 'deleteLocalisation' :
            if ($_SESSION['user']->hasDroit('delete', 'com_localisation')) {
                include_once ("localisation/controleur.php");
            }
            break;
        case 'enableLocalisation' :
            if ($_SESSION['user']->hasDroit('edit', 'com_localisation')) {
                include_once ("localisation/controleur.php");
            }
            break;
    }
}