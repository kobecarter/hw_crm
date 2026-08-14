<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addParrainage' :
            addParrainage($_POST);
            break;
        case 'validateParrainage' :
            validateParrainage($_POST);
            break;
        case 'rejectParrainage' :
            rejectParrainage($_POST);
            break;
    }
}

// Soumission self-service (espace employé, com_elogin) - jamais de hasDroit ici (voir
// router.php, gated isResourceHumaine() uniquement). id_resourcehumaine forcé à la session,
// jamais au champ du formulaire. Le statut reste TOUJOURS "en attente" à la soumission, même
// si client::search() trouve une correspondance : seul un admin peut faire passer un
// parrainage à "validé" (et donc déclencher la commission) - voir validateParrainage().
function addParrainage($data)
{
    if (!$_SESSION['user']->isResourceHumaine()) {
        echo "2";
        return;
    }
    $indices = array("nom", "prenom", "raison_social");
    foreach ($indices as $indice) {
        if (!isset($data[$indice]) || empty(trim($data[$indice]))) {
            echo "0";
            return;
        }
    }

    $resourcehumaine = $_SESSION['user'];

    $parrainage = new parrainage();
    $parrainage->setResourcehumaine($resourcehumaine);
    $parrainage->setNom($data['nom']);
    $parrainage->setPrenom($data['prenom']);
    $parrainage->setEmail(isset($data['email']) ? $data['email'] : '');
    $parrainage->setRaisonSocial($data['raison_social']);
    // Recherche tous agences confondues (client::search($terme, false), même convention que la
    // recherche globale du bandeau haut) - correspondance informative seulement, ne valide
    // jamais automatiquement le parrainage.
    $clientsCorrespondants = client::search($data['raison_social'], false);
    $parrainage->setClient(!empty($clientsCorrespondants) ? $clientsCorrespondants[0] : null);
    $parrainage->setStatut(parrainage::STATUT_EN_ATTENTE);
    $parrainage->setDateAdd(date("Y-m-d"));

    if ($parrainage->add() == 1) {
        $notification = [
            "id" => null,
            "user_id" => null,
            "type_user" => "user",
            "title" => "Nouveau parrainage de " . $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName(),
            "message" => $data['prenom'] . " " . $data['nom'] . " (" . $data['raison_social'] . ")",
            "type_message" => "success",
            "url" => "index.php?option=com_resourcehumaine&task=parrainage&id=" . $resourcehumaine->getId(),
            "class" => "parrainage",
            "data" => parrainage::getLastId(),
            "is_read" => 0,
            "date_add" => date("Y-m-d"),
            "date_edit" => date("Y-m-d")
        ];
        if (class_exists('notification')) {
            notification::build($notification)->add();
        }
        echo "1";
    } else {
        echo "2";
    }
}

function validateParrainage($data)
{
    if (!isset($data['id']) || empty($data['id'])) {
        echo "0";
        return;
    }
    $parrainage = parrainage::find($data['id']);
    if ($parrainage->getId() == 0) {
        echo "2";
        return;
    }
    // Montant figé au moment de la validation (snapshot) : un changement ultérieur de la
    // commission définie sur la fiche employé ne doit jamais modifier rétroactivement un
    // parrainage déjà validé.
    $parrainage->setMontantCommission($parrainage->getResourcehumaine()->getCommissionParrainage());
    $parrainage->setStatut(parrainage::STATUT_VALIDE);
    $parrainage->setDateValidation(date("Y-m-d"));
    if ($parrainage->edit() == 1) {
        echo "1";
    } else {
        echo "2";
    }
}

function rejectParrainage($data)
{
    if (!isset($data['id']) || empty($data['id'])) {
        echo "0";
        return;
    }
    $parrainage = parrainage::find($data['id']);
    if ($parrainage->getId() == 0) {
        echo "2";
        return;
    }
    $parrainage->setStatut(parrainage::STATUT_REFUSE);
    $parrainage->setDateValidation(date("Y-m-d"));
    if ($parrainage->edit() == 1) {
        echo "1";
    } else {
        echo "2";
    }
}
