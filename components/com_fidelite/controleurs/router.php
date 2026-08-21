<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addManualPoints' :
            if ($_SESSION['user']->hasDroit('edit', 'com_fidelite')) {
                include_once ("fidelite/controleur.php");
            }
            break;
        case 'markRewardGiven' :
            if ($_SESSION['user']->hasDroit('edit', 'com_fidelite')) {
                include_once ("fidelite/controleur.php");
            }
            break;
        case 'deletePoint' :
            if ($_SESSION['user']->hasDroit('delete', 'com_fidelite')) {
                include_once ("fidelite/controleur.php");
            }
            break;
        // apiGetTotal/apiGetHistory/apiGetRewards/apiAddPoints : endpoints publics appelés par
        // l'espace client (site, pas de session CRM) - protégés par secret partagé vérifié en
        // constant-time, même patron que runDailyRemindersEndpoint() dans com_relance/controleurs/
        // router.php. Le CRM est la source de vérité pour les points/récompenses.
        case 'apiGetTotal' :
            fideliteApiGetTotal();
            break;
        case 'apiGetHistory' :
            fideliteApiGetHistory();
            break;
        case 'apiGetRewards' :
            fideliteApiGetRewards();
            break;
        case 'apiAddPoints' :
            fideliteApiAddPoints();
            break;
    }
}

function fideliteApiSecretOk()
{
    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    return defined('FIDELITE_API_SECRET') && FIDELITE_API_SECRET !== '' && hash_equals(FIDELITE_API_SECRET, (string) $provided);
}

function fideliteApiForbidden()
{
    error_log('com_fidelite API - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
    http_response_code(403);
    echo json_encode(array('error' => 'forbidden'));
}

function fideliteApiGetTotal()
{
    header('Content-Type: application/json');
    if (!fideliteApiSecretOk()) { fideliteApiForbidden(); return; }
    $idClient = isset($_GET['id_client']) ? intval($_GET['id_client']) : 0;
    echo json_encode(array('id_client' => $idClient, 'total' => fidelite::getTotalByClient($idClient)));
}

function fideliteApiGetHistory()
{
    header('Content-Type: application/json');
    if (!fideliteApiSecretOk()) { fideliteApiForbidden(); return; }
    $idClient = isset($_GET['id_client']) ? intval($_GET['id_client']) : 0;
    echo json_encode(array('id_client' => $idClient, 'history' => fidelite::findHistoryByClient($idClient)));
}

function fideliteApiGetRewards()
{
    header('Content-Type: application/json');
    if (!fideliteApiSecretOk()) { fideliteApiForbidden(); return; }
    $idClient = isset($_GET['id_client']) ? intval($_GET['id_client']) : 0;
    echo json_encode(array('id_client' => $idClient, 'rewards' => fidelite::findRewardsByClient($idClient)));
}

// POST uniquement (mutation) : id_client, points (peut être négatif), type, libelle - lus dans le
// corps JSON plutôt qu'en query string, pour rester cohérent avec une action d'écriture.
function fideliteApiAddPoints()
{
    header('Content-Type: application/json');
    if (!fideliteApiSecretOk()) { fideliteApiForbidden(); return; }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(array('error' => 'method_not_allowed'));
        return;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) { $data = $_POST; }
    $idClient = isset($data['id_client']) ? intval($data['id_client']) : 0;
    $points = isset($data['points']) ? intval($data['points']) : 0;
    $type = isset($data['type']) ? (string) $data['type'] : '';
    $libelle = isset($data['libelle']) ? (string) $data['libelle'] : '';
    $ok = fidelite::addPoints($idClient, $points, $type, $libelle);
    echo json_encode(array('success' => $ok, 'total' => fidelite::getTotalByClient($idClient)));
}
