<?php
require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    // Toutes les tâches de ce routeur lisent $_SESSION['user']->hasDroit(...) : sans session admin
    // active, l'appel plantait (fatal sur null) au lieu de renvoyer une réponse propre -- pour
    // filterHolidays (appelée en AJAX au chargement de la page), ça se traduisait par un tableau
    // et un calendrier qui ne s'affichaient jamais, sans aucune erreur visible côté client.
    if (!isset($_SESSION['user']) || !$_SESSION['user']->isConnected()) {
        header('location: ../../../index.php?option=com_login');
        exit;
    }
    switch ($task) {
        case 'addHoliday':
            if ($_SESSION['user']->hasDroit('add', 'com_holiday')) {
                include_once("holiday/controleur.php");
            }
            break;
        case 'editHoliday':
            if ($_SESSION['user']->hasDroit('edit', 'com_holiday')) {
                include_once("holiday/controleur.php");
            }
            break;
        case 'deleteHoliday':
            if ($_SESSION['user']->hasDroit('delete', 'com_holiday')) {
                include_once("holiday/controleur.php");
            }
            break;
        case 'filterHolidays':
            if ($_SESSION['user']->hasDroit('view', 'com_holiday')) {
                include_once("holiday/controleur.php");
            }
            break;
    }
}
