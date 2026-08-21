<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'manage' :
        if ($_SESSION['user']->hasDroit('edit', 'com_fidelite')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $client = client::find($id, $_SESSION['agence']);
                $history = fidelite::findHistoryByClient($id);
                $total = fidelite::getTotalByClient($id);
                $parrainages = fidelite::findParrainagesByClient($id);
                $rewards = fidelite::findRewardsByClient($id);
                include_once("components/com_fidelite/views/fidelite/manage.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_fidelite')) {
            $items = fidelite::findAllClientsWithTotals($_SESSION['agence']);
            include_once("components/com_fidelite/views/fidelite/list.php");
        }
        break;
}
