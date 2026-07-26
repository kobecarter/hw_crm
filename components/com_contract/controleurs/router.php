<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addContract' :
            if ($_SESSION['user']->hasDroit('add', 'com_contract')) {
                include_once ("contract/controleur.php");
            }
            break;
        case 'editContract' :
            if ($_SESSION['user']->hasDroit('edit', 'com_contract')) {
                include_once ("contract/controleur.php");
            }
            break;
        case 'deleteContract' :
            if ($_SESSION['user']->hasDroit('delete', 'com_contract')) {
                include_once ("contract/controleur.php");
            }
            break;
        case 'removeContratPDF' :
            if ($_SESSION['user']->hasDroit('delete', 'com_contract')) {
                include_once ("contract/controleur.php");
            }
            break;    
        case 'pdfContract' :
            if ($_SESSION['user']->hasDroit('view', 'com_contract')) {
                include_once ("contract/controleur.php");
            }
            break;

    }
}