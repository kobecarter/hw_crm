<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addBank' :
            if ($_SESSION['user']->hasDroit('add', 'com_bank')) {
                include_once ("bank/controleur.php");
            }
            break;
        case 'editBank' :
            if ($_SESSION['user']->hasDroit('edit', 'com_bank')) {
                include_once ("bank/controleur.php");
            }
            break;
        case 'deleteBank' :
            if ($_SESSION['user']->hasDroit('delete', 'com_bank')) {
                include_once ("bank/controleur.php");
            }
            break; 

    }
}