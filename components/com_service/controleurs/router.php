<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addService' :
            if ($_SESSION['user']->hasDroit('add', 'com_service')) {
                include_once ("service/controleur.php");
            }
            break;
        case 'editService' :
            if ($_SESSION['user']->hasDroit('edit', 'com_service')) {
                include_once ("service/controleur.php");
            }
            break;
        case 'deleteService' :
            if ($_SESSION['user']->hasDroit('delete', 'com_service')) {
                include_once ("service/controleur.php");
            }
            break;
        case 'enableService' :
            if ($_SESSION['user']->hasDroit('edit', 'com_service')) {
                include_once ("service/controleur.php");
            }
            break;        
    }
}