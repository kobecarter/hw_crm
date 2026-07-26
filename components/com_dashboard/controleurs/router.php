<?php
require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'validateCodePin' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;    
        case 'stats' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;
        case 'statsGlobal' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;    
        case 'getChiffreYear' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;
        case 'getCAperCompany' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;    
    }
}