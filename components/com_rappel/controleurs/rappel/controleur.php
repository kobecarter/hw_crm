<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addRappel':
            addRappel($_POST);
            break;
        case 'editRappel':
            editRappel($_POST);
            break;
        case 'deleteRappel':
            deleteRappel($_POST);
            break;
        case "renewRappel":
            renewRappel($_POST);
            break;
        case "sendMailRappel":
            sendMailRappel($_POST);
            break;
        case "archiveRappel":
            archiveRappel($_POST);
            break;
        case "desarchiveRappel":
            desarchiveRappel($_POST);
            break;
        case 'deleteRelance':
            deleteRelance($_POST);
            break;
        case 'findAllByClientApi':
            findAllByClientApi($_GET);
            break;
    }
}

function addRappel($data)
{
    $indices = array("domaine", "date_expir");
    if (fieldCheck($data, $indices)) {
        if (buildRappel($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editRappel($data)
{
    $indices = array("id", "domaine", "date_expir");
    if (fieldCheck($data, $indices)) {
        if (buildRappel($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteRappel($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $rappel = rappel::find($id,$_SESSION['agence']);
        if ($rappel->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function archiveRappel($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $rappel = rappel::find($id,$_SESSION['agence']);
        $rappel->setArchived(1);
        if ($rappel->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function desarchiveRappel($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $rappel = rappel::find($id,$_SESSION['agence']);
        $rappel->setArchived(0);
        if ($rappel->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function renewRappel($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $rappel = rappel::find($data['id'],$_SESSION['agence']);
        $date_expir = new DateTime($rappel->getDateExpir());
        $rappel->setDateExpir($date_expir->add(new DateInterval('P1Y'))->format('Y-m-d'));
        if ($rappel->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function sendMailRappel($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $rappel = rappel::find($data['id'],$_SESSION['agence']);
        if ($rappel->sendRelance() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildRappel($data, $id = null)
{
    $rappel = new rappel();

    if ($id) {
        $rappel = rappel::find($id,$_SESSION['agence']);
    }

    $rappel->setClient(client::find($data['client'],$_SESSION['agence']));
    $rappel->setType($data['type']);
    $rappel->setDomaine($data['domaine']);
    $rappel->setDateExpir(dateBD($data['date_expir']));
    $rappel->setRemarque($data['remarque']);
    $rappel->setFournisseursIds(isset($data['fournisseurs']) && is_array($data['fournisseurs']) ? $data['fournisseurs'] : array());
    $rappel->setDateAdd(date('Y-m-d'));
    $rappel->setLastEdit(date('Y-m-d'));
    $rappel->setArchived(isset($data['archived']) ? 1 : 0);

    return $rappel;
}

function deleteRelance($data)
{
    global $db;
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];

        $SQLdelete = "DELETE FROM " . __prefixe_db__ . "relances WHERE id=" . $id;
        if (!$db->query($SQLdelete)) {
            echo "1";
        } else echo "2";
    } else {
        echo "0";
    }
}

// API

function findAllByClientApi($data){
    $recalls = rappel::findAllByClientApi($data['client']);
    // Convert array to UTF-8
    array_walk_recursive($recalls, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
    echo json_encode($recalls);
}
