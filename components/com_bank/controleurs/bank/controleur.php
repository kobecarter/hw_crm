<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addBank':
            addBank($_POST);
            break;
        case 'editBank':
            editBank($_POST);
            break;
        case 'deleteBank':
            deleteBank($_POST);
            break;
        case 'deleteBanks' :
            deleteBanks($_POST);
            break;
        case 'deleteLogo' :
            deleteLogo($_POST);
            break;
    }
}

function addBank($data)
{
    $indices = array("raison_sociale");
    if (fieldCheck($data, $indices)) {
        if (buildBank($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editBank($data)
{
    $indices = array("id", "raison_sociale");
    if (fieldCheck($data, $indices)) {
        if (buildBank($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBank($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $bank = bank::find($id, $_SESSION["langue"]);
        if ($bank->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBanks($data)
{
    $indices = array("ids");
    if (fieldCheck($data, $indices)) {
        if (bank::deleteMultiple($data) == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}   


function buildBank($data, $id = null)
{
    global $db;
    $bank = new bank();

    if ($id) {
        $bank = bank::find($id,$_SESSION["langue"]);
    }

    
    $bank->setRaisonSociale($data['raison_sociale']);
    $bank->setSiegeSocial($data['siege_social']);
    $bank->setNumeroRegistreCommerce($data['numero_registre_commerce']);
    $bank->setIce($data['ice']);
    $bank->setRib($data['rib']);
    $bank->setCodeSwift($data['code_swift']);
    $bank->setBanque($data['banque']);
    $bank->setIbanNumber($data['iban_number']);
    $bank->setCurrency($data['currency']);
    $bank->setDateAdd(date("Y-m-d H:i:s"));
    $bank->setLastEdit(date("Y-m-d H:i:s"));

    return $bank;
}