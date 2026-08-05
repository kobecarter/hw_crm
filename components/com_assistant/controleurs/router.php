<?php
require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'addAssistantTache':
            if ($_SESSION['user']->hasDroit('add', 'com_assistant')) {
                include_once("assistanttache/controleur.php");
            }
            break;
        case 'editAssistantTache':
            if ($_SESSION['user']->hasDroit('edit', 'com_assistant')) {
                include_once("assistanttache/controleur.php");
            }
            break;
        case 'deleteAssistantTache':
            if ($_SESSION['user']->hasDroit('delete', 'com_assistant')) {
                include_once("assistanttache/controleur.php");
            }
            break;
        case 'toggleTermineAssistantTache':
            if ($_SESSION['user']->hasDroit('edit', 'com_assistant')) {
                include_once("assistanttache/controleur.php");
            }
            break;
    }
}
