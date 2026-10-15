<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addCheque':
            addCheque($_POST);
            break;
        case 'editCheque':
            editCheque($_POST);
            break;
        case 'deleteCheque':
            deleteCheque($_POST);
            break;
    }
}

function addCheque($data)
{
    $indices = array("type", "tiers_type", "check_number", "date", "date_encaissement", "amount", "currency", "status");
    if (fieldCheck($data, $indices) && tiersChequeValide($data)) {
        if (buildCheque($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editCheque($data)
{
    $indices = array("id", "type", "tiers_type", "check_number", "date", "date_encaissement", "amount", "currency", "status");
    if (fieldCheck($data, $indices) && tiersChequeValide($data)) {
        if (buildCheque($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteCheque($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $cheque = cheque::find($id, $_SESSION['agence']);
        if ($cheque->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Le tiers (client ou fournisseur) choisi doit correspondre à tiers_type ET avoir été réellement
// sélectionné - fieldCheck() seul ne suffit pas ici (tiers_type='client' avec un champ 'client'
// vide passerait fieldCheck si le champ 'fournisseur', lui, est rempli).
function tiersChequeValide($data)
{
    if ($data['tiers_type'] === 'client') {
        return isset($data['client']) && !empty($data['client']);
    }
    if ($data['tiers_type'] === 'fournisseur') {
        return isset($data['fournisseur']) && !empty($data['fournisseur']);
    }
    return false;
}

function buildCheque($data, $id = null)
{
    $cheque = new cheque();

    $file = array();
    if (isset($_FILES['file_check']) && $_FILES['file_check']['name'][0] != '') {
        $file = uploadFiles('file_check', '../../../images/cheques/', array('jpg', 'jpeg', 'gif', 'png', 'webp', 'svg', 'pdf', 'JPG', 'JPEG', 'GIF', 'PNG', 'WEBP', 'SVG', 'PDF'));
    }

    if ($id) {
        $cheque = cheque::find($id, $_SESSION['agence']);
    }
    if (isset($file[0])) {
        $cheque->setFile($file[0]);
    }
    $cheque->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $cheque->setType($data['type']);
    $cheque->setTiersType($data['tiers_type']);
    if ($data['tiers_type'] === 'client') {
        $client = client::find(intval($data['client']), $_SESSION['agence']);
        $cheque->setClient($client);
        $cheque->setBeneficiary(trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom()));
    } else {
        $fournisseur = fournisseur::find(intval($data['fournisseur']), $_SESSION['agence']);
        $cheque->setFournisseur($fournisseur);
        $cheque->setBeneficiary(trim((string) $fournisseur->getRaisonSocial()) !== '' ? $fournisseur->getRaisonSocial() : trim($fournisseur->getPrenom() . ' ' . $fournisseur->getNom()));
    }
    $cheque->setCheckNumber($data['check_number']);
    $cheque->setDate(dateBD($data['date']));
    $cheque->setDateEncaissement(dateBD($data['date_encaissement']));
    $cheque->setAmount($data['amount']);
    $cheque->setCurrency($data['currency']);
    $cheque->setStatus($data['status']);
    $cheque->setReason(isset($data['reason']) ? $data['reason'] : '');
    $cheque->setComment(isset($data['comment']) ? $data['comment'] : '');
    $cheque->setDateAdd($id ? $cheque->getDateAdd() : date("Y-m-d"));
    $cheque->setLastEdit(date("Y-m-d"));

    return $cheque;
}
