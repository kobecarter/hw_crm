<?php

@$task = $_GET['task'];

switch ($task) {
    case 'envois' :
        if ($_SESSION['user']->hasDroit('view', 'com_facture') || $_SESSION['user']->hasDroit('view', 'com_devis')) {
            $envois = envoiEmail::findAll($_SESSION['agence']);
            include_once("components/com_journal/views/envoimail/list.php");
        } else {
            show404Error("404");
        }
        break;
    default :
        if ($_SESSION['user']->isSuperUser()) {
            $logs = activityLog::findAll($_SESSION['agence']);
            include_once("components/com_journal/views/journal/list.php");
        } else {
            show404Error("404");
        }
        break;
}
