<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'previewReleve' :
            if ($_SESSION['user']->hasDroit('add', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'confirmerReleve' :
            if ($_SESSION['user']->hasDroit('add', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'creerJustificatifManuel' :
            if ($_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'validerLigne' :
            if ($_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'ignorerLigne' :
            if ($_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'creerChargeEtLier' :
            if ($_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'listerBulletinsPaie' :
            if ($_SESSION['user']->hasDroit('view', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'supprimerLot' :
            if ($_SESSION['user']->hasDroit('delete', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
        case 'annulerRapprochementFacture' :
            if ($_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
                include_once ("rapprochement/controleur.php");
            }
            break;
    }
}
