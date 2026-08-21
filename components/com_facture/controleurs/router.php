<?php

require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();
if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'addFacture':
            if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'verifierDossierDriveClient':
            if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'addFactureAvoir':
            if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'editFacture':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'deleteFacture':
            if ($_SESSION['user']->hasDroit('delete', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'archiveFacture':
            if ($_SESSION['user']->hasDroit('delete', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'retablirFacture':
            if ($_SESSION['user']->hasDroit('delete', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'getRowFacture':
            if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'removeItemFacture':
            if ($_SESSION['user']->hasDroit('delete', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'customItemFacture':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'editItemFacture':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'getServicePrice':
            include_once("facture/controleur.php");
            break;
        case 'getServiceUnite':
            include_once("facture/controleur.php");
            break;    
        case 'paymentForm':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("payment/controleur.php");
            }
            break;
        case 'getRowRappel':
            if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
                include_once("payment/controleur.php");
            }
            break;
        case 'addPayment':
            if ($_SESSION['user']->hasDroit('add', 'com_facture')) {
                include_once("payment/controleur.php");
            }
            break;
        case 'editPayment':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("payment/controleur.php");
            }
            break;
        case 'deletePayment':
            if ($_SESSION['user']->hasDroit('delete', 'com_facture')) {
                include_once("payment/controleur.php");
            }
            break;
        case 'removePhotoPayment':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("payment/controleur.php");
            }
            break; 
        case 'pdfFacture':
            //if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
            include_once("facture/controleur.php");
            //}
            break;
        case 'pdfFactures':
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'filterFacture':
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'sendViaMailFacture':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'exportFacture':
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'exportFactureTest':
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once("facture/controleur.php");
            }
            break;
        case 'findAllByClientApi':
            include_once("facture/controleur.php");
        break;
        case 'getPaymentsByClientApi':
            include_once("facture/controleur.php");
        break;
        case 'sendEmailsForeachFacture':
            include_once("facture/controleur.php");
            break;
        case 'pdfFactureApi':
            include_once("facture/controleur.php");
        break;
         case 'pdfPayment':
            include_once("payment/controleur.php");
        break;
        case 'sendPaymentRequestPdf':
            if ($_SESSION['user']->hasDroit('edit', 'com_facture')) {
                include_once("payment/controleur.php");
            }
        break;
        case 'change':
            include_once("facture/controleur.php");
        break;
    }
}
