<?php
require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'addRappel':
            if ($_SESSION['user']->hasDroit('add', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'editRappel':
            if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'deleteRappel':
            if ($_SESSION['user']->hasDroit('delete', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'archiveRappel':
            if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'desarchiveRappel':
            if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'renewRappel':
            if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'sendMailRappel':
            if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'deleteRelance':
            if ($_SESSION['user']->hasDroit('delete', 'com_rappel')) {
                include_once("rappel/controleur.php");
            }
            break;
        case 'findAllByClientApi':
                include_once("rappel/controleur.php");
        break;
        case 'cronRelancesExpirationRappel':
            // Endpoint public (pas de cron serveur disponible) : appelé une fois par jour par un
            // service externe planifié (ex: cron-job.org), même principe que
            // com_accounting/controleurs/router.php task=cronCheckEcheanceTva. Aucune session
            // utilisateur active, donc pas de hasDroit() ici - protégé par un secret partagé.
            cronRelancesExpirationRappelEndpoint();
            break;
    }
}

function cronRelancesExpirationRappelEndpoint()
{
    header('Content-Type: application/json');

    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('RAPPEL_EXPIRATION_CRON_SECRET') || RAPPEL_EXPIRATION_CRON_SECRET === '' || !hash_equals(RAPPEL_EXPIRATION_CRON_SECRET, (string) $provided)) {
        error_log('cronRelancesExpirationRappelEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    // Aucune session utilisateur active dans ce contexte (appel externe direct) - nécessaire
    // pour client::find()/rappel::findAll() appelés en interne, qui lisent $_SESSION['agence'].
    bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, 1, 'fr');

    echo json_encode(rappel::cronEnvoyerRelancesExpiration());
}
