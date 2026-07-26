<?php

require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'addExpertise':
            if ($_SESSION['user']->hasDroit('add', 'com_expertise')) {
                $parents = expertise::findAll('fr', true, true);
                include_once("expertise/controleur.php");
            }
            break;
        case 'editExpertise':
            if ($_SESSION['user']->hasDroit('edit', 'com_expertise')) {
                $parents = expertise::findAll('fr', true, true);
                include_once("expertise/controleur.php");
            }
            break;
        case 'deleteExpertise':
            if ($_SESSION['user']->hasDroit('delete', 'com_expertise')) {
                include_once("expertise/controleur.php");
            }
            break;
        case 'enableExpertise':
            if ($_SESSION['user']->hasDroit('edit', 'com_expertise')) {
                include_once("expertise/controleur.php");
            }
            break;
        case 'deleteExpertises':
            if ($_SESSION['user']->hasDroit('delete', 'com_expertise')) {
                include_once("expertise/controleur.php");
            }
            break;
        case 'enableExpertises':
            if ($_SESSION['user']->hasDroit('edit', 'com_expertise')) {
                include_once("expertise/controleur.php");
            }
            break;
    }
}
