<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addManualPoints' :
            if ($_SESSION['user']->hasDroit('edit', 'com_fidelite')) {
                include_once ("fidelite/controleur.php");
            }
            break;
        case 'markRewardGiven' :
            if ($_SESSION['user']->hasDroit('edit', 'com_fidelite')) {
                include_once ("fidelite/controleur.php");
            }
            break;
        case 'deletePoint' :
            if ($_SESSION['user']->hasDroit('delete', 'com_fidelite')) {
                include_once ("fidelite/controleur.php");
            }
            break;
    }
}
