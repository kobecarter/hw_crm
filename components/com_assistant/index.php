<?php

// Listes nécessaires pour les 6 select dépendants "Lié à" (client/fournisseur/rh/reclamation/
// banque - "reunion" n'a pas de fiche donc pas de liste) - factorisé ici puisque add.php ET
// edit.php en ont tous les deux besoin.
function assistantChargerListesRelation()
{
    $clients = client::findAll(true, false, $_SESSION['agence']);
    $fournisseurs = fournisseur::findAll(false, $_SESSION['agence']);
    $employes = array_values(array_filter(resourcehumaine::findAll(), function ($e) {
        return $e->getAgency() && $e->getAgency()->getId() == $_SESSION['agence'];
    }));
    $reclamations = reclamation::findAll(false, false, $_SESSION['agence']);
    $banks = bank::findAll($_SESSION['agence']);
    return compact('clients', 'fournisseurs', 'employes', 'reclamations', 'banks');
}

@$task = $_GET['task'];
switch ($task) {
    case 'add':
        if ($_SESSION['user']->hasDroit('add', 'com_assistant')) {
            $action = "components/com_assistant/controleurs/router.php?task=addAssistantTache";
            $submitName = "add";
            $submitValue = "Ajouter";
            $preselectTypeRelation = isset($_GET['type_relation']) ? $_GET['type_relation'] : null;
            $preselectIdRelation = isset($_GET['id_relation']) ? intval($_GET['id_relation']) : null;
            $preselectType = isset($_GET['type']) ? $_GET['type'] : null;
            extract(assistantChargerListesRelation());
            include_once("components/com_assistant/views/assistanttache/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_assistant')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $tache = assistanttache::find($id, $_SESSION['agence']);
                $action = "components/com_assistant/controleurs/router.php?task=editAssistantTache";
                $submitName = "edit";
                $submitValue = "Modifier";
                extract(assistantChargerListesRelation());
                include_once("components/com_assistant/views/assistanttache/edit.php");
            }
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_assistant')) {
            $taches = assistanttache::findAll($_SESSION['agence']);
            include_once("components/com_assistant/views/assistanttache/list.php");
        }
        break;
}
