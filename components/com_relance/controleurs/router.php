<?php
require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'addRelance':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'editRelance':
            if ($_SESSION['user']->hasDroit('edit', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'deleteRelance':
            if ($_SESSION['user']->hasDroit('delete', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'prolongerRelance':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'getListeSelonCategorie':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'envoyerRelanceManuelle':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'genererEmailRelance':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'enregistrerRelanceEmail':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
            break;
        case 'envoyerRelancesProgrammeesCron':
            // Endpoint public (pas de cron serveur disponible), même principe que
            // runDailyReminders ci-dessous : protégé par un secret partagé vérifié en
            // constant-time, pas par hasDroit() puisqu'aucune session utilisateur n'est active.
            envoyerRelancesProgrammeesCronEndpoint();
            break;
        case 'runDailyReminders':
            // Endpoint public (pas de cron serveur disponible) : appelé une fois par jour par un
            // service externe planifié (ex: cron-job.org). Aucune session utilisateur active, donc
            // pas de hasDroit() ici (comme slackEventWebhook dans com_devis) - protégé à la place
            // par un secret partagé vérifié en constant-time.
            runDailyRemindersEndpoint();
            break;
    }
}

function runDailyRemindersEndpoint()
{
    header('Content-Type: application/json');

    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('RELANCE_CRON_SECRET') || RELANCE_CRON_SECRET === '' || !hash_equals(RELANCE_CRON_SECRET, (string) $provided)) {
        error_log('runDailyRemindersEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    echo json_encode(relance::runDailyReminders());
}

// Relances créées via "Programmer l'envoi" (catégorie facture/paiement/expiration domaine/
// hébergement/autre service, email déjà rédigé/relu à la création) dont la date prévue est
// atteinte - voir relance::envoyerRelancesProgrammees(). Même secret que runDailyReminders
// (même module, même frontière de confiance), à pinguer quotidiennement (cron-job.org) comme lui.
function envoyerRelancesProgrammeesCronEndpoint()
{
    header('Content-Type: application/json');

    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('RELANCE_CRON_SECRET') || RELANCE_CRON_SECRET === '' || !hash_equals(RELANCE_CRON_SECRET, (string) $provided)) {
        error_log('envoyerRelancesProgrammeesCronEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    $userId = defined('RELANCE_CRON_ACTING_USER_ID') ? RELANCE_CRON_ACTING_USER_ID : 5;
    $resumeGlobal = array('envoyees' => 0, 'erreurs' => 0);
    foreach (agence::findAll('fr') as $agence) {
        try {
            bootstrapSystemSession($userId, $agence->getId(), 'fr');
        } catch (\Throwable $e) {
            error_log('envoyerRelancesProgrammeesCronEndpoint - bootstrap agence ' . $agence->getId() . ' - ' . $e->getMessage());
            $resumeGlobal['erreurs']++;
            continue;
        }
        $resume = relance::envoyerRelancesProgrammees($agence->getId());
        $resumeGlobal['envoyees'] += $resume['envoyees'];
        $resumeGlobal['erreurs'] += $resume['erreurs'];
    }

    echo json_encode($resumeGlobal);
}
