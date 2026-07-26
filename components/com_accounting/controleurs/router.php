<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addCnss' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("cnss/controleur.php");
            }
            break;
        case 'editCnss' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("cnss/controleur.php");
            }
            break;
        case 'deleteCnss' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("cnss/controleur.php");
            }
            break;
        case 'addTva' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'editTva' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'deleteTva' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;
        case 'deleteDocTva' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("tva/controleur.php");
            }
            break;            
        case 'addBilan' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;
        case 'editBilan' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;
        case 'deleteBilan' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;
        case 'deleteDocBilan' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("bilan/controleur.php");
            }
            break;    
        case 'addTaxeprofessionnelle' :
            if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
        case 'editTaxeprofessionnelle' :
            if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
        case 'deleteTaxeprofessionnelle' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;
        case 'deleteDocTaxe' :
            if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) {
                include_once ("taxeprofessionnelle/controleur.php");
            }
            break;    
    }
}