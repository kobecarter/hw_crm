<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

// Composant sans vue ni entrée de menu : uniquement le endpoint AJAX de la recherche globale du
// bandeau haut (includes/tpl/top.php). Jamais listé dans crm_module - accessible uniquement via
// ce router, comme com_ia et les autres endpoints "utilitaires" de l'app.
if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'rechercheGlobale':
            if (isset($_SESSION['user'])) {
                include_once ("search/controleur.php");
            }
            break;
    }
}
