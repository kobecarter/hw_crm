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
    }
}
