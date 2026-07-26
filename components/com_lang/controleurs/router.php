<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addLang' :
            if ($_SESSION['user']->hasDroit('add', 'com_lang')) {
                include_once ("lang/controleur.php");
            }
            break;
        case 'editLang' :
            if ($_SESSION['user']->hasDroit('edit', 'com_lang')) {
                include_once ("lang/controleur.php");
            }
            break;
        case 'deleteLang' :
            if ($_SESSION['user']->hasDroit('delete', 'com_lang')) {
                include_once ("lang/controleur.php");
            }
            break;
        case 'enableLang' :
            if ($_SESSION['user']->hasDroit('edit', 'com_lang')) {
                include_once ("lang/controleur.php");
            }
            break;        
    }
}