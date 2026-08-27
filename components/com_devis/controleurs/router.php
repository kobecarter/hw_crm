<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    // Toutes les tâches ci-dessous sauf celles-ci lisent $_SESSION['user']->hasDroit(...) : sans
    // session admin active, l'appel plantait (fatal sur null) et affichait une page blanche plutôt
    // que de renvoyer vers la connexion (cas vécu : lien PDF ouvert depuis un navigateur mobile où
    // la session n'était pas/plus active). Les tâches listées ici sont authentifiées autrement
    // (jeton client, webhook Slack, secret cron) et n'ont jamais de $_SESSION['user'].
    $__tachesSansSessionAdmin = array('findAllByClientApi', 'pdfDevisApi', 'slackEventWebhook', 'cronVerifierValidationDevisSlack');
    if (!in_array($task, $__tachesSansSessionAdmin, true) && (!isset($_SESSION['user']) || !$_SESSION['user']->isConnected())) {
        header('location: ../../../index.php?option=com_login');
        exit;
    }
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
        case 'sendSlackDevis' :
            if ($_SESSION['user']->hasDroit('view', 'com_devis')) {
                include_once ("devis/controleur.php");
            }
            break;
        case 'sendEmailDevis' :
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
        case 'slackEventWebhook':
            // Endpoint public appelé par Slack (Events API) : aucune session
            // utilisateur active, donc pas de hasDroit() ici (comme pdfDevisApi).
            include_once("devis/controleur.php");
        break;
        case 'cronVerifierValidationDevisSlack':
            // Endpoint public (pas de cron serveur disponible, et l'Events API ci-dessus ne peut
            // pas atteindre ce déploiement local) : appelé toutes les 5-10 min par un service
            // externe planifié (cron-job.org), protégé par ?secret= plutôt que par une session.
            include_once("devis/controleur.php");
        break;

    }
}