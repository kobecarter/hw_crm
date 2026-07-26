<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addCategorie' :
            if ($_SESSION['user']->hasDroit('add', 'com_categorie')) {
                include_once ("categorie/controleur.php");
            }
            break;
        case 'editCategorie' :
            if ($_SESSION['user']->hasDroit('edit', 'com_categorie')) {
                include_once ("categorie/controleur.php");
            }
            break;
        case 'deleteCategorie' :
            if ($_SESSION['user']->hasDroit('delete', 'com_categorie')) {
                include_once ("categorie/controleur.php");
            }
            break;
        case 'enableCategorie' :
            if ($_SESSION['user']->hasDroit('edit', 'com_categorie')) {
                include_once ("categorie/controleur.php");
            }
            break;
    }
}