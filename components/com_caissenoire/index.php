<?php

// Module réservé à Hamid et Zakaria (CAISSENOIRE_USERS_AUTORISES, components/com_caissenoire/classes/caissenoire.php)
// - ce CRM n'a que des droits par rôle, pas par utilisateur individuel, donc ce contrôle d'id
// s'ajoute à hasDroit() plutôt que de le remplacer. Même convention que le reste du CRM en cas
// d'accès refusé (ex: com_charge/index.php) : la zone de contenu reste simplement vide, pas de
// page d'erreur dédiée.
if (caissenoire::estUtilisateurAutorise($_SESSION['user']->getId())) {

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_caissenoire')) {
            $action = "components/com_caissenoire/controleurs/router.php?task=addCaisseNoire";
            $submitName = "add";
            $submitValue = "Ajouter";
            include_once("components/com_caissenoire/views/caissenoire/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_caissenoire')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $caissenoire = caissenoire::find($id);
                $action = "components/com_caissenoire/controleurs/router.php?task=editCaisseNoire";
                $submitName = "edit";
                $submitValue = "Modifier";
                include_once("components/com_caissenoire/views/caissenoire/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_caissenoire')) {
            $entrees = caissenoire::findAll(true);
            include_once("components/com_caissenoire/views/caissenoire/list.php");
        }
        break;
}

}
