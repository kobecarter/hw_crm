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
        case 'addServiceForm' :
            if ($_SESSION['user']->hasDroit('add', 'com_service')) {
                $action = "components/com_service/controleurs/router.php?task=addService";
                $submitName = "addservicerapide";
                $submitValue = "Ajouter service";
                $categories = categorie::findAll($_SESSION["langue"], true, true);
                $prefillTitre = isset($_GET['titre']) ? $_GET['titre'] : '';
                $prefillPrix = isset($_GET['prix']) ? $_GET['prix'] : '';
                $prefillUnite = isset($_GET['unite']) ? $_GET['unite'] : '';
                $prefillDescription = isset($_GET['description']) ? $_GET['description'] : '';
                include_once("../views/service/form.php");
            }
            break;
        case 'getServiceOptions' :
            if ($_SESSION['user']->hasDroit('add', 'com_service')) {
                include_once ("service/controleur.php");
            }
            break;
        case 'updateServiceDescriptionOnly' :
            if ($_SESSION['user']->hasDroit('edit', 'com_service')) {
                include_once ("service/controleur.php");
            }
            break;
    }
}