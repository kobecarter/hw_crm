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
        case 'envoyerFactureEmailAvecCc':
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
        case 'exportProjetsEnCours':
            // Rapport multi-agences (toutes les agences, pas seulement $_SESSION['agence']) -
            // réservé au superuser pour éviter qu'un utilisateur dont les factures sont filtrées
            // par id_user_added (voir facture::findAll()) ne génère un rapport silencieusement
            // incomplet en pensant avoir une vue globale.
            if ($_SESSION['user']->hasDroit('view', 'com_facture') && $_SESSION['user']->isSuperUser()) {
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
        case 'pdfPaymentApi':
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
        case 'cronExportProjetsEnCoursHebdomadaire':
            // Endpoint public (pas de cron serveur disponible), même principe que les autres crons
            // du CRM (ex: com_relance/controleurs/router.php) : appelé une fois par semaine (lundi)
            // par un service externe planifié (cron-job.org), protégé par un secret partagé vérifié
            // en constant-time - aucune session utilisateur active, donc pas de hasDroit() ici.
            cronExportProjetsEnCoursHebdomadaireEndpoint();
            break;
    }
}

function cronExportProjetsEnCoursHebdomadaireEndpoint()
{
    header('Content-Type: application/json');

    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('PROJETS_EXPORT_CRON_SECRET') || PROJETS_EXPORT_CRON_SECRET === '' || !hash_equals(PROJETS_EXPORT_CRON_SECRET, (string) $provided)) {
        error_log('cronExportProjetsEnCoursHebdomadaireEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    // facture::findAll() filtre par id_user_added sauf pour un superuser (voir le case
    // 'exportProjetsEnCours' ci-dessus) : réutilise RELANCE_CRON_ACTING_USER_ID (déjà superadmin,
    // même convention que les autres crons de ce CRM) pour garantir une vue multi-agences complète.
    $userId = defined('RELANCE_CRON_ACTING_USER_ID') ? RELANCE_CRON_ACTING_USER_ID : 5;
    try {
        bootstrapSystemSession($userId, 1, 'fr');
    } catch (\Throwable $e) {
        error_log('cronExportProjetsEnCoursHebdomadaireEndpoint - bootstrap - ' . $e->getMessage());
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    include_once("facture/controleur.php");
    echo json_encode(cronPublierProjetsEnCoursSurGoogleSheets());
}
