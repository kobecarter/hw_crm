<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addReclamation':
            addReclamation($_POST);
            break;
        case 'editReclamation':
            editReclamation($_POST);
            break;
        case 'deleteReclamation':
            deleteReclamation($_POST);
            break;
        case 'enableReclamation':
            enableReclamation($_POST);
            break;
        case 'findAllByClientApi':
            findAllByClientApi($_GET);
            break;
        case 'createReclamationApi':
            createReclamationApi($_POST);
            break;
            
    }
}

function addReclamation($data)
{
    $indices = array("id_client");
    if (fieldCheck($data, $indices)) {
        if (buildReclamation($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editReclamation($data)
{
    $indices = array("id", "id_client");
    if (fieldCheck($data, $indices)) {
        if (buildReclamation($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteReclamation($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $reclamation = new reclamation();
        $reclamation->setId($data['id']);
        if ($reclamation->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableReclamation($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices))
    {
        $reclamation = reclamation::find($data['id'],$_SESSION['agence']);
        $reclamation->setEtat($data['state'] == "oui" ? 0 : 1);
        if ($reclamation->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}


function buildReclamation($data, $id = null)
{
    $reclamation = new reclamation();
	
    if($id){
        $reclamation = reclamation::find($id,$_SESSION['agence']);
    }

	$reclamation->setClient(client::find($data['id_client'],$_SESSION['agence']));
    $reclamation->setDepartment($data['department']);
	$reclamation->setSujet($data['sujet']);
	$reclamation->setMessage($data['message']);
	$reclamation->setEtat(isset($data['etat']) ? 1 : 0);
	$reclamation->setDateAdd(date("Y-m-d"));
    return $reclamation;
}

// API

function findAllByClientApi($data){
    $reclamations = reclamation::findAllByClientApi($data['client']);
    // Convert array to UTF-8
    array_walk_recursive($reclamations, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
    echo json_encode($reclamations);
}

function createReclamationApi($data){
    echo reclamation::createReclamationApi($data);
}

