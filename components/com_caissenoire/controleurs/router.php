<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

// Même contrôle qu'index.php : réservé à Hamid/Zakaria (CAISSENOIRE_USERS_AUTORISES), en plus de
// hasDroit() ci-dessous - ce CRM n'a pas de droit par utilisateur individuel.
if (!isset($_SESSION['user']) || !caissenoire::estUtilisateurAutorise($_SESSION['user']->getId())) {
    http_response_code(403);
    exit;
}

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addCaisseNoire' :
            if ($_SESSION['user']->hasDroit('add', 'com_caissenoire')) {
                include_once ("caissenoire/controleur.php");
            }
            break;
        case 'editCaisseNoire' :
            if ($_SESSION['user']->hasDroit('edit', 'com_caissenoire')) {
                include_once ("caissenoire/controleur.php");
            }
            break;
        case 'deleteCaisseNoire' :
            if ($_SESSION['user']->hasDroit('delete', 'com_caissenoire')) {
                include_once ("caissenoire/controleur.php");
            }
            break;
        case 'toggleRembourse' :
            if ($_SESSION['user']->hasDroit('edit', 'com_caissenoire')) {
                include_once ("caissenoire/controleur.php");
            }
            break;
    }
}
