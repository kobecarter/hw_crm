<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addManualPoints':
            addManualPoints($_POST);
            break;
        case 'markRewardGiven':
            markRewardGiven($_POST);
            break;
        case 'deletePoint':
            deletePoint($_POST);
            break;
    }
}

function addManualPoints($data)
{
    $indices = array("id_client", "points");
    if (fieldCheck($data, $indices)) {
        $ok = fidelite::addManualPoints($data['id_client'], $data['points'], isset($data['libelle']) ? $data['libelle'] : null);
        echo $ok ? "1" : "2";
    } else {
        echo "0";
    }
}

function markRewardGiven($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $affectePar = isset($_SESSION['user']) ? ($_SESSION['user']->getNom() . ' ' . $_SESSION['user']->getPrenom()) : '';
        $ok = fidelite::markRewardGiven($data['id'], $affectePar);
        echo $ok ? "1" : "2";
    } else {
        echo "0";
    }
}

function deletePoint($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $ok = fidelite::deletePointEntry($data['id']);
        echo $ok ? "1" : "2";
    } else {
        echo "0";
    }
}
