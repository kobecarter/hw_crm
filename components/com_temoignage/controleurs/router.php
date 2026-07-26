<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addTemoignage' :
            if ($_SESSION['user']->hasDroit('add', 'com_temoignage')) {
                include_once ("temoignage/controleur.php");
            }
            break;
        case 'editTemoignage' :
            if ($_SESSION['user']->hasDroit('edit', 'com_temoignage')) {
                include_once ("temoignage/controleur.php");
            }
            break;
        case 'deleteTemoignage' :
            if ($_SESSION['user']->hasDroit('delete', 'com_temoignage')) {
                include_once ("temoignage/controleur.php");
            }
            break;
        case 'enableTemoignage' :
            if ($_SESSION['user']->hasDroit('edit', 'com_temoignage')) {
                include_once ("temoignage/controleur.php");
            }
            break;
        case 'deleteTemoignages' :
            if ($_SESSION['user']->hasDroit('delete', 'com_temoignage')) {
                include_once ("temoignage/controleur.php");
            }
            break;
        case 'enableTemoignages' :
            if ($_SESSION['user']->hasDroit('edit', 'com_temoignage')) {
                include_once ("temoignage/controleur.php");
            }
            break;
        
    }
}