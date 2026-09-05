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
        case 'updateReclamationApi':
            updateReclamationApi($_POST);
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
        // Réponse existante avant modif, pour ne notifier que sur une réponse nouvelle/changée.
        $existing = reclamation::find($data['id'], $_SESSION['agence']);
        $oldReponse = $existing->getReponse();
        $reclamation = buildReclamation($data, $data['id']);
        if ($reclamation->edit() == 1) {
            $newReponse = $reclamation->getReponse();
            if ($newReponse !== null && trim($newReponse) !== '' && $newReponse !== $oldReponse) {
                $reclamation->sendReponseEmail();
                pushNotifier::send($reclamation->getClient(), 'Réponse à votre message', 'Nous avons répondu à votre réclamation.', array('type' => 'reclamation', 'id' => $reclamation->getId()));
            }
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
        // find($id, $agence) plutôt que new+setId() : sans ça, un id valide d'une autre agence se
        // faisait supprimer sans aucune vérification d'appartenance (IDOR).
        $reclamation = reclamation::find($data['id'], $_SESSION['agence']);
        if ($reclamation->getId() == 0) {
            echo "2";
            return;
        }
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
    $oldReponse = $reclamation->getReponse();
    $oldDateReponse = $reclamation->getDateReponse();

	$reclamation->setClient(client::find($data['id_client'],$_SESSION['agence']));
    $reclamation->setDepartment($data['department']);
	$reclamation->setSujet($data['sujet']);
	$reclamation->setMessage($data['message']);
	// Réponse de l'admin : date_reponse figée à la première réponse, rafraîchie si le texte change.
	$newReponse = (isset($data['reponse']) && trim($data['reponse']) !== '') ? $data['reponse'] : null;
	$reclamation->setReponse($newReponse);
	if ($newReponse === null) {
		$reclamation->setDateReponse(null);
	} elseif ($newReponse === $oldReponse && $oldDateReponse) {
		$reclamation->setDateReponse($oldDateReponse);
	} else {
		$reclamation->setDateReponse(date("Y-m-d H:i:s"));
	}
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

function updateReclamationApi($data){
    echo reclamation::updateReclamationApi($data);
}

