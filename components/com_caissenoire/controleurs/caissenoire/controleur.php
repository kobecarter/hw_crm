<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addCaisseNoire':
            addCaisseNoire($_POST);
            break;
        case 'editCaisseNoire':
            editCaisseNoire($_POST);
            break;
        case 'deleteCaisseNoire':
            deleteCaisseNoire($_POST);
            break;
        case 'toggleRembourse':
            toggleRembourse($_POST);
            break;
    }
}

function addCaisseNoire($data)
{
    $indices = array("titre", "id_utilisateur", "montant", "date_charge");
    if (fieldCheck($data, $indices)) {
        if (!caissenoire::estUtilisateurAutorise($data['id_utilisateur'])) {
            echo "0";
            return;
        }
        if (buildCaisseNoire($data)->add() == 1) {
            echo "1|" . caissenoire::getLastId();
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editCaisseNoire($data)
{
    $indices = array("id", "titre", "id_utilisateur", "montant", "date_charge");
    if (fieldCheck($data, $indices)) {
        if (!caissenoire::estUtilisateurAutorise($data['id_utilisateur'])) {
            echo "0";
            return;
        }
        if (buildCaisseNoire($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteCaisseNoire($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $entree = caissenoire::find($data['id']);
        if ($entree->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Bascule remboursé/non remboursé - même forme que enableCharge() (com_charge/controleurs/charge/
// controleur.php), avec en plus la date de remboursement, remplie/effacée avec la bascule.
function toggleRembourse($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices)) {
        $entree = caissenoire::find($data['id']);
        $nouvelEtat = $data['state'] == "oui" ? 0 : 1;
        $entree->setRefunded($nouvelEtat);
        $entree->setDateRemboursement($nouvelEtat == 1 ? date("Y-m-d") : null);
        $entree->setUserEdited($_SESSION['user']);
        $entree->setLastEdit(date("Y-m-d"));
        if ($entree->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildCaisseNoire($data, $id = null)
{
    $entree = new caissenoire();

    $justificatif = array();
    if (isset($_FILES['justificatif']) && $_FILES['justificatif']['name'][0] != '') {
        $justificatif = uploadFiles('justificatif', '../../../images/caissenoire/', array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'JPG', 'JPEG', 'GIF', 'PNG', 'PDF'));
    }

    if ($id) {
        $entree = caissenoire::find($id);
        $entree->setUserEdited($_SESSION['user']);
    } else {
        $entree->setUserAdded($_SESSION['user']);
        $entree->setUserEdited($_SESSION['user']);
        $entree->setDateAdd(date("Y-m-d"));
    }

    if (isset($justificatif[0])) {
        $entree->setJustificatif($justificatif[0]);
    }

    $entree->setUtilisateur(user::find($data['id_utilisateur']));
    $entree->setTitre($data['titre']);
    $entree->setDescription(isset($data['description']) ? $data['description'] : '');
    $entree->setMontant($data['montant']);
    $entree->setDateCharge(dateBD($data['date_charge']));
    $entree->setRefunded(isset($data['refunded']) ? 1 : 0);
    $entree->setDateRemboursement(isset($data['refunded']) && !empty($data['date_remboursement']) ? dateBD($data['date_remboursement']) : null);
    $entree->setRemarque(isset($data['remarque']) ? $data['remarque'] : '');
    $entree->setLastEdit(date("Y-m-d"));

    return $entree;
}
