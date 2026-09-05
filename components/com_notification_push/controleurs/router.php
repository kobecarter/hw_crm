<?php
require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'cronCheckNewCatalogItems':
            // Endpoint public (pas de cron serveur disponible) : appelé une fois par jour par un
            // service externe planifié (ex: cron-job.org), même principe que
            // com_rappel/controleurs/router.php task=cronRelancesExpirationRappel. Aucune session
            // utilisateur active, donc protégé par un secret partagé.
            cronCheckNewCatalogItemsEndpoint();
            break;
    }
}

function cronCheckNewCatalogItemsEndpoint()
{
    header('Content-Type: application/json');

    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('CATALOG_NOTIFY_CRON_SECRET') || CATALOG_NOTIFY_CRON_SECRET === '' || !hash_equals(CATALOG_NOTIFY_CRON_SECRET, (string) $provided)) {
        error_log('cronCheckNewCatalogItemsEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    // Aucune session utilisateur active dans ce contexte (appel externe direct) - nécessaire pour
    // client::findAll()/toute logique interne qui lit $_SESSION['agence'].
    bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, 1, 'fr');

    echo json_encode(catalogNotifier::runDiff());
}
