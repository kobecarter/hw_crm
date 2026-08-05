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
        case 'getFactureByClient':
            if ($_SESSION['user']->hasDroit('add', 'com_relance')) {
                include_once("relance/controleur.php");
            }
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
