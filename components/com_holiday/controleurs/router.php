<?php
require_once("../../../config.php");
require_once("../../../instanceDb.php");
require_once("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
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
