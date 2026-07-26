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
    }
}
