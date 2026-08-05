<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addBonusResourceHumaine':
            addBonusResourceHumaine($_POST);
            break;
        case 'editBonusResourceHumaine':
            editBonusResourceHumaine($_POST);
            break;
        case 'deleteBonusResourceHumaine':
            deleteBonusResourceHumaine($_POST);
            break;
    }
}

function addBonusResourceHumaine($data)
{
    // echo json_encode($data);
    // die();
    $indices = array("id_resourcehumaine", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildBonusResourceHumaine($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editBonusResourceHumaine($data)
{
    $indices = array("id_bonus", "id_resourcehumaine", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildBonusResourceHumaine($data, $data['id_bonus'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBonusResourceHumaine($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find() + vérification manuelle de l'agence via l'employé lié (bonus::find($id) ne prend
        // pas d'agence en paramètre) - sans ce contrôle, n'importe quel id de prime valide, même
        // d'une autre agence, se faisait supprimer (IDOR).
        $bonus = bonus::find($data['id']);
        $employeBonus = $bonus->getResourcehumaine();
        if ($bonus->getId() == 0 || !$employeBonus || !$employeBonus->getAgency() || $employeBonus->getAgency()->getId() != $_SESSION['agence']) {
            echo "2";
            return;
        }
        if ($bonus->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildBonusResourceHumaine($data, $id = null)
{
    $bonus = new bonus();

    if($id){
        $bonus = bonus::find($id);
    }

	$bonus->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
    $bonus->setAmount($data['amount']);
    $bonus->setDate(date("Y-m-t",strtotime($data['date']."-1")));
    $bonus->setStatus($data['status']);
	$bonus->setRemark($data['remark']);
    $bonus->setDateAdd(date("Y-m-d"));
    $bonus->setLastEdit(date("Y-m-d"));

    return $bonus;
}