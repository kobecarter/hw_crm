<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addCheque' :
            if ($_SESSION['user']->hasDroit('add', 'com_cheque')) {
                include_once ("cheque/controleur.php");
            }
            break;
        case 'editCheque' :
            if ($_SESSION['user']->hasDroit('edit', 'com_cheque')) {
                include_once ("cheque/controleur.php");
            }
            break;
        case 'deleteCheque' :
            if ($_SESSION['user']->hasDroit('delete', 'com_cheque')) {
                include_once ("cheque/controleur.php");
            }
            break;
        case "enableCheque" :
            if ($_SESSION['user']->hasDroit('edit', 'com_cheque')) {
                include_once ("cheque/controleur.php");
            }
            break;
    }
}