<?php
require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'validateCodePin' :
            // Gate générique du tableau de bord (index.php : tout profil autre que "Commercial"
            // doit saisir ce code, indépendamment de ses droits modules) - ne doit donc dépendre
            // que d'être connecté, jamais d'un droit précis comme "view com_facture" : un profil
            // sans ce droit (ex: un profil RH/ops tout neuf) ne pouvait jusqu'ici JAMAIS valider
            // le code PIN, quel qu'il soit, car cette tâche était silencieusement bloquée avant
            // même d'être exécutée.
            if (isset($_SESSION['user']) && $_SESSION['user']->isConnected()) {
                include_once ("dashboard/controleur.php");
            }
            break;
        case 'stats' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;
        case 'statsGlobal' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;    
        case 'getChiffreYear' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;
        case 'getCAperCompany' :
            if ($_SESSION['user']->hasDroit('view', 'com_facture')) {
                include_once ("dashboard/controleur.php");
            }
            break;    
    }
}