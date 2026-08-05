<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addResourceHumaine' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("resourcehumaine/controleur.php");
            }
            break;
        case 'duplicateResourceHumaine' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("resourcehumaine/controleur.php");
            }
            break;
        case 'editResourceHumaine' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("resourcehumaine/controleur.php");
            }
            break;
        case 'deleteResourceHumaine' :
            if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) {
                include_once ("resourcehumaine/controleur.php");
            }
            break;
        case 'exportAbsenceFile' :
            if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
                include_once ("resourcehumaine/controleur.php");
            }
            break;	
        case 'getRowWorkDate' :
                include_once ("resourcehumaine/controleur.php");
            break;
        case 'removeRowWorkDate' :
                include_once ("resourcehumaine/controleur.php");
            break;
        case 'addFileResourceHumaine' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("fileresourcehumaine/controleur.php");
            }
            break;
        case 'editFileResourceHumaine' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("fileresourcehumaine/controleur.php");
            }
            break;
        case 'deleteFileResourceHumaine' :
            if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) {
                include_once ("fileresourcehumaine/controleur.php");
            }
            break;	
        case 'addAbsenceResourceHumaine' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("absence/controleur.php");
            }
            break;
        case 'editAbsenceResourceHumaine' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("absence/controleur.php");
            }
            break;
        case 'deleteAbsenceResourceHumaine' :
            if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) {
                include_once ("absence/controleur.php");
            }
            break;	
        case 'addBonusResourceHumaine' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("bonus/controleur.php");
            }
            break;
        case 'editBonusResourceHumaine' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("bonus/controleur.php");
            }
            break;
        case 'deleteBonusResourceHumaine' :
            if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) {
                include_once ("bonus/controleur.php");
            }
            break;
        case 'importPointage' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("pointage/controleur.php");
            }
            break;	
        case 'filterPointage' :
            if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
                include_once ("pointage/controleur.php");
            }
            break;	
        case 'addPayslip' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("payslip/controleur.php");
            }
            break;
        case 'editPayslip' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("payslip/controleur.php");
            }
            break;
        case 'deletePayslip' :
            if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) {
                include_once ("payslip/controleur.php");
            }
            break;
        case 'generateJobOfferAI' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("joboffer/controleur.php");
            }
            break;
        case 'submitJobOffer' :
            if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
                include_once ("joboffer/controleur.php");
            }
            break;
        case 'marquerOffreSignee' :
            if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
                include_once ("joboffer/controleur.php");
            }
            break;
        case 'deleteJobOffer' :
            if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) {
                include_once ("joboffer/controleur.php");
            }
            break;
        case 'telechargerOffreWord' :
            if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
                include_once ("joboffer/controleur.php");
            }
            break;
        case 'telechargerOffrePDF' :
            if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
                include_once ("joboffer/controleur.php");
            }
            break;
        case 'cronRelanceOffreEmploi' :
            // Endpoint public (pas de cron serveur disponible) : appelé toutes les heures par un
            // service externe planifié (ex: cron-job.org), même principe que
            // com_accounting/controleurs/router.php task=cronCheckEcheanceTva. Aucune session
            // utilisateur active, donc pas de hasDroit() ici - protégé par un secret partagé.
            include_once ("joboffer/controleur.php");
            cronRelanceOffreEmploiEndpoint();
            break;
        case 'cronVerifierValidationSlack' :
            // Idem, à appeler toutes les 5-10 minutes.
            include_once ("joboffer/controleur.php");
            cronVerifierValidationSlackEndpoint();
            break;
        case 'addRequest' :
            if ($_SESSION['user']->isResourceHumaine()) {
                include_once ("request/controleur.php");
            }
            break;
        case 'editRequest' :
            if ($_SESSION['user']->isResourceHumaine()) {
                include_once ("request/controleur.php");
            }
            break;
        case 'deleteRequest' :
            if ($_SESSION['user']->isResourceHumaine()) {
                include_once ("request/controleur.php");
            }
            break;
        case 'showRequest' :
                include_once ("request/controleur.php");
            break;
        case 'changeStatusRequest' :
            if($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')){
                include_once ("request/controleur.php");
            }
            break;
        case 'showSetResponseRequest' :
            if($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')){
                include_once ("request/controleur.php");
            }
            break;
        case 'setResponseRequest' :
            if($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')){
                include_once ("request/controleur.php");
            }
            break;
        case 'markAsRead' :
            include_once ("notification/controleur.php");
        break;
    }
}