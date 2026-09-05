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
        case 'oneTimeMigrateNotifications':
            // À visiter une seule fois dans le navigateur (ou curl) après le déploiement, pour
            // créer notifications_enabled/crm_catalog_notified en prod sans avoir à toucher
            // phpMyAdmin. Idempotent (vérifie avant de créer) - se relancer sans risque ne fait
            // rien la deuxième fois. Ne touche à aucune donnée existante, uniquement au schéma.
            oneTimeMigrateNotificationsEndpoint();
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

function oneTimeMigrateNotificationsEndpoint()
{
    header('Content-Type: application/json');
    global $db;

    $provided = isset($_GET['secret']) ? $_GET['secret'] : '';
    if (!defined('CATALOG_NOTIFY_CRON_SECRET') || CATALOG_NOTIFY_CRON_SECRET === '' || !hash_equals(CATALOG_NOTIFY_CRON_SECRET, (string) $provided)) {
        error_log('oneTimeMigrateNotificationsEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    $result = array();

    // 1) crm_client.notifications_enabled - colonne additive, valeur par défaut 1, aucune ligne
    // existante affectée au-delà de recevoir cette valeur par défaut.
    $columnExists = $db->queryS("SHOW COLUMNS FROM crm_client LIKE 'notifications_enabled'");
    if (is_array($columnExists) && count($columnExists) > 0) {
        $result['crm_client.notifications_enabled'] = 'déjà présente, rien fait';
    } else {
        $db->query("ALTER TABLE crm_client ADD COLUMN notifications_enabled TINYINT(1) NOT NULL DEFAULT 1 AFTER archived");
        $result['crm_client.notifications_enabled'] = empty($db->getLink()->error) ? 'colonne ajoutée' : ('erreur: ' . $db->getLink()->error);
    }

    // 2) crm_catalog_notified - nouvelle table, vide au départ, n'existe pas encore en prod.
    $tableExists = $db->queryS("SHOW TABLES LIKE 'crm_catalog_notified'");
    if (is_array($tableExists) && count($tableExists) > 0) {
        $result['crm_catalog_notified'] = 'déjà présente, rien fait';
    } else {
        $db->query("CREATE TABLE crm_catalog_notified (
            id INT(11) NOT NULL AUTO_INCREMENT,
            type ENUM('formation','service') NOT NULL,
            external_id VARCHAR(64) NOT NULL,
            date_notified DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_type_external (type, external_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $result['crm_catalog_notified'] = empty($db->getLink()->error) ? 'table créée' : ('erreur: ' . $db->getLink()->error);
    }

    echo json_encode($result);
}
