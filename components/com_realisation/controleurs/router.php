<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addRealisation' :
            if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
                include_once ("realisation/controleur.php");
            }
            break;
        case 'editRealisation' :
            if ($_SESSION['user']->hasDroit('edit', 'com_realisation')) {
                include_once ("realisation/controleur.php");
            }
            break;
        case 'deleteRealisation' :
            if ($_SESSION['user']->hasDroit('delete', 'com_realisation')) {
                include_once ("realisation/controleur.php");
            }
            break;
    }
}