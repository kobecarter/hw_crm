<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addAssistantTache':
            addAssistantTache($_POST);
            break;
        case 'editAssistantTache':
            editAssistantTache($_POST);
            break;
        case 'deleteAssistantTache':
            deleteAssistantTache($_POST);
            break;
        case 'toggleTermineAssistantTache':
            toggleTermineAssistantTache($_POST);
            break;
    }
}

function addAssistantTache($data)
{
    $indices = array("type", "titre");
    if (fieldCheck($data, $indices)) {
        if (buildAssistantTache($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editAssistantTache($data)
{
    $indices = array("id", "type", "titre");
    if (fieldCheck($data, $indices)) {
        if (buildAssistantTache($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteAssistantTache($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $tache = assistanttache::find($data['id'], $_SESSION['agence']);
        if ($tache->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Bascule "fait / à faire" depuis la liste (case à cocher rapide), sans passer par le formulaire
// complet - cf. le même besoin déjà couvert ailleurs par des actions ponctuelles similaires
// (rappel::renew, relance::prolonger...).
function toggleTermineAssistantTache($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $tache = assistanttache::find($data['id'], $_SESSION['agence']);
        if ($tache->getId() == 0) {
            echo "2";
            return;
        }
        $tache->setTermine($tache->isTermine() ? 0 : 1);
        $tache->setLastEdit(date('Y-m-d H:i:s'));
        if ($tache->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildAssistantTache($data, $id = null)
{
    $tache = new assistanttache();

    if ($id) {
        $tache = assistanttache::find($id, $_SESSION['agence']);
    } else {
        $tache->setAgence($_SESSION['agence']);
        $tache->setDateAdd(date('Y-m-d H:i:s'));
    }

    // "reunion" n'a pas de fiche associée (pas de module dédié) - la case "type_relation" suffit,
    // id_relation reste vide. Pour les autres types, id_relation n'est retenu que s'il est fourni
    // (l'utilisateur a coché un type mais pas forcément choisi une fiche précise).
    $typeRelation = isset($data['type_relation']) && !empty($data['type_relation']) ? $data['type_relation'] : null;
    $idRelation = null;
    if ($typeRelation && $typeRelation !== 'reunion' && isset($data['id_relation']) && !empty($data['id_relation'])) {
        $idRelation = intval($data['id_relation']);
    }
    $tache->setTypeRelation($typeRelation);
    $tache->setIdRelation($idRelation);
    $tache->setType($data['type']);
    $tache->setTitre($data['titre']);
    // <input type="datetime-local"> envoie "YYYY-MM-DDTHH:mm" - normalisé en "YYYY-MM-DD HH:mm:00"
    // pour la colonne DATETIME plutôt que de compter sur le parsing tolérant de MySQL du 'T'.
    $dateTache = null;
    if (isset($data['date_tache']) && !empty($data['date_tache'])) {
        $dateTache = str_replace('T', ' ', $data['date_tache']) . ':00';
    }
    $tache->setDateTache($dateTache);
    $tache->setRemarque(isset($data['remarque']) ? $data['remarque'] : null);
    $tache->setTermine(isset($data['termine']) ? 1 : 0);
    $tache->setLastEdit(date('Y-m-d H:i:s'));
    return $tache;
}
