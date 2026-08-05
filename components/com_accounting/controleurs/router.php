<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addCnss' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("cnss/controleur.php");
            }
            break;
        case 'editCnss' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("cnss/controleur.php");
            }
            break;
        case 'deleteCnss' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("cnss/controleur.php");
            }
            break;
        case 'addTva' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'editTva' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'deleteTva' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'deleteDocTva' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'exportTvaComptable' :
            if ($_SESSION['user']->hasDroit('view', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'cronCheckEcheanceTva' :
            // Endpoint public (pas de cron serveur disponible) : appelé une fois par jour par un
            // service externe planifié (ex: cron-job.org), même principe que
            // com_relance/controleurs/router.php task=runDailyReminders. Aucune session
            // utilisateur active, donc pas de hasDroit() ici - protégé par un secret partagé.
            cronCheckEcheanceTvaEndpoint();
            break;
        case 'addBilan' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;
        case 'editBilan' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;
        case 'deleteBilan' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;
        case 'deleteDocBilan' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;    
        case 'addTaxeprofessionnelle' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
        case 'editTaxeprofessionnelle' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
        case 'deleteTaxeprofessionnelle' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
        case 'deleteDocTaxe' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
    }
}

function cronCheckEcheanceTvaEndpoint()
{
    header('Content-Type: application/json');

    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('TVA_CRON_SECRET') || TVA_CRON_SECRET === '' || !hash_equals(TVA_CRON_SECRET, (string) $provided)) {
        error_log('cronCheckEcheanceTvaEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    // Aucune session utilisateur active dans ce contexte (appel externe direct) - nécessaire
    // pour agence::find() appelé en interne par tva::build(), qui lit $_SESSION['langue'].
    bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, 1, 'fr');

    $resultats = array();
    $aujourdHui = date('Y-m-d');

    // Pas de filtre "active" : ce champ n'est renseigné sur aucune agence réelle en base
    // (toujours NULL) - même convention que com_relance/classes/relance.php:412.
    foreach (agence::findAll('fr') as $ag) {
        $periodicite = $ag->getTvaPeriodicite() === 'trimestriel' ? 'trimestriel' : 'mensuel';
        $periode = tva::periodeReference($periodicite, null, -1);

        $declaree = true;
        $curseur = clone $periode['debut'];
        while ($curseur <= $periode['fin']) {
            if (empty(tva::findByDate($ag->getId(), $curseur->format('Y-m')))) {
                $declaree = false;
                break;
            }
            $curseur->modify('+1 month');
        }

        if ($declaree) {
            $resultats[] = array('agence' => $ag->getNom(), 'action' => 'a_jour');
            continue;
        }

        $joursRestants = (int) (new DateTime('today'))->diff($periode['limite'])->format('%r%a');
        $dejaAlerteAujourdhui = $ag->getTvaSlackDerniereAlerte() === $aujourdHui;

        if ($joursRestants > 5 || $dejaAlerteAujourdhui) {
            $resultats[] = array('agence' => $ag->getNom(), 'action' => $dejaAlerteAujourdhui ? 'deja_alerte_aujourdhui' : 'pas_encore_urgent', 'jours' => $joursRestants);
            continue;
        }

        $texte = ':warning: *Échéance TVA proche* — ' . $ag->getNom() . ' : TVA de ' . $periode['libelle']
            . ' à déposer avant le ' . $periode['limite']->format('d/m/Y')
            . ($joursRestants < 0
                ? ' (*en retard de ' . abs($joursRestants) . ' jour' . (abs($joursRestants) > 1 ? 's' : '') . '*)'
                : ' (dans ' . $joursRestants . ' jour' . ($joursRestants > 1 ? 's' : '') . ')');

        $envoye = tvaSlackPostMessage(defined('SLACK_CHANNEL_TVA') ? SLACK_CHANNEL_TVA : 'commercial-hw', $texte);
        if ($envoye) {
            agence::marquerAlerteTvaEnvoyee($ag->getId());
        }
        $resultats[] = array('agence' => $ag->getNom(), 'action' => $envoye ? 'alerte_envoyee' : 'echec_envoi_slack', 'jours' => $joursRestants);
    }

    echo json_encode(array('success' => 1, 'resultats' => $resultats));
}

// Petit helper local (chat.postMessage + bot token Slack) : même patron que slackPostMessage()
// dans com_devis/controleurs/devis/controleur.php, dupliqué ici plutôt que partagé - convention
// déjà établie dans ce code (chaque module Slack a sa propre petite fonction indépendante).
function tvaSlackPostMessage($channel, $text)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '' || empty($channel)) {
        return false;
    }
    $ch = curl_init('https://slack.com/api/chat.postMessage');
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . SLACK_BOT_TOKEN
        ),
        CURLOPT_POSTFIELDS => json_encode(array('channel' => $channel, 'text' => $text)),
        CURLOPT_TIMEOUT => 15
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    return is_array($decoded) && !empty($decoded['ok']);
}