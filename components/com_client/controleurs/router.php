<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();
if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addClient' :
            if ($_SESSION['user']->hasDroit('add', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'addClientForm' :
            if ($_SESSION['user']->hasDroit('add', 'com_client')) {
                $action = "components/com_client/controleurs/router.php?task=addClient";
                $submitName = "addclientrapide";
                $submitValue = "Ajouter client";
                include_once("../views/client/form.php");
            }
            break;    
        case 'editClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'deleteClient' :
            if ($_SESSION['user']->hasDroit('delete', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'enableClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
		case 'filterClient' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;	
		case 'exportClient' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;	
        case 'exportClientWithFilter' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;	
        case 'exportEmail' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                    include_once ("client/controleur.php");
                }
            break;
        case 'getClientSelect' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                    include_once ("client/controleur.php");
                }
            break;
        case 'loginApi' :
                include_once ("client/controleur.php");
            break;	
        case 'verifyEmailApi' :
                include_once ("client/controleur.php");
            break;
        case 'setNewPasswordApi' :
                include_once ("client/controleur.php");
            break;		
        case 'getInfoFromTokenApi' :
                include_once ("client/controleur.php");
            break;
        case 'findClientByIdApi' :
                include_once ("client/controleur.php");
            break;	
        case 'updateProfileApi' :
                include_once ("client/controleur.php");
            break;			
    }
}