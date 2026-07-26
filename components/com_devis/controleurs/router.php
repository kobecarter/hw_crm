<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addDevis' :
            if ($_SESSION['user']->hasDroit('add', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'editDevis' :
            if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'checkContrat' :
            if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'deleteDevis' :
            if ($_SESSION['user']->hasDroit('delete', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'getRowDevis' :
            if ($_SESSION['user']->hasDroit('add', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'getRowCondition' :
            if ($_SESSION['user']->hasDroit('add', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;  
        case 'removeCondition' :
            if ($_SESSION['user']->hasDroit('delete', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;    
		case 'removeItemDevis' :
            if ($_SESSION['user']->hasDroit('delete', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
		case 'customItemDevis' :
            if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
		case 'editItemDevis' :
            if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;	
        case 'switchUnitiesByLanguage' :
            if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;	
		case 'getServicePrice' :
			include_once ("devis/controleur.php");
            break;
        case 'getServiceUnite' :
			include_once ("devis/controleur.php");
            break;    
		case 'pdfDevis' :
            if ($_SESSION['user']->hasDroit('view', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'pdfDevisTexte' :
            if ($_SESSION['user']->hasDroit('view', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;    
		case 'createInvoice' :
            if ($_SESSION['user']->hasDroit('add', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'sendViaMailDevis' :
            if ($_SESSION['user']->hasDroit('edit', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'duplicateDevis' :
            if ($_SESSION['user']->hasDroit('add', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'findAllByClientApi' :
            include_once ("devis/controleur.php");
        break;
        case 'pdfDevisApi':
            include_once("devis/controleur.php");
        break;
            
    }
}