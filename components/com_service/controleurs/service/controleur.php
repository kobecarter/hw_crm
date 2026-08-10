<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addService':
            addService($_POST);
            break;
        case 'editService':
            editService($_POST);
            break;
        case 'deleteService':
            deleteService($_POST);
            break;
        case "enableService":
            enableService($_POST);
            break;
        case 'deleteServices' :
            deleteServices($_POST);
            break;
        case 'enableServices' :
            enableServices($_POST);
            break;
        case 'getServiceOptions' :
            getServiceOptions();
            break;
        case 'updateServiceDescriptionOnly' :
            updateServiceDescriptionOnly($_POST);
            break;
    }
}

function updateServiceDescriptionOnly($data)
{
    header('Content-Type: application/json');

    $id_service = isset($data['id_service']) ? intval($data['id_service']) : 0;
    $description = isset($data['description']) ? $data['description'] : '';

    if (!$id_service) {
        echo json_encode(array('success' => 0, 'message' => 'Service invalide'));
        return;
    }

    // on part toujours d'un service existant fraîchement rechargé, pour ne modifier QUE
    // la description et laisser tous ses autres champs (catégorie, prix, etc.) intacts
    $service = service::find($id_service, $_SESSION['langue']);
    if (!$service->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Service introuvable'));
        return;
    }

    $service->setDescription($description);
    $service->edit();

    echo json_encode(array('success' => 1));
}

function getServiceOptions()
{
    $lastId = service::getLastId();
    $services = service::findAll($_SESSION['langue'], true, false, true);
    foreach ($services as $service) {
        $sl = $lastId == $service->getId() ? "selected" : "";
        echo '<option data-title="' . htmlspecialchars($service->getTitre()) . '" data-description="' . htmlspecialchars(addslashes($service->getDescription())) . '" value="' . $service->getId() . '" ' . $sl . '>' . htmlspecialchars($service->getTitre()) . '</option>';
    }
}

function addService($data)
{
    $indices = array("titre");
    if (fieldCheck($data, $indices)) {
        if (buildService($data)->add() == 1) {
            echo "1|" . service::getLastId();
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editService($data)
{
    $indices = array("id", "titre");
    if (fieldCheck($data, $indices)) {
        if (buildService($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteService($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $service = service::find($id, $_SESSION["langue"]);
        if ($service->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableService($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices))
    {
        $service = new service();
        $service = service::find($data['id'],$_SESSION['langue']);
        $service->setActive($data['state'] == "oui" ? 0 : 1);
        if ($service->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildService($data, $id = null)
{
    global $db;
    $service = new service();

    if($id){
        $service = service::find($id,$_SESSION["langue"]);
    }
	
    $service->setCategorie(categorie::find($data["id_categorie"], $_SESSION["langue"]));
	$service->setOrdre($data['ordre']);
	$service->setFacturation($data['facturation']);
    $service->setActive(isset($data['active']) ? 1 : 0);
    $service->setTitre($data['titre']);
    $service->setUnite($data['unite']);
    $service->setPrix($data['prix']);
    $service->setIntervenant($data['intervenant']);
    $service->setDescription($data['description']);
    $service->setDateAdd(date("Y-m-d H:i:s"));
    $service->setLastEdit(date("Y-m-d H:i:s"));
    $service->setLangue($_SESSION['langue']);

    return $service;
}