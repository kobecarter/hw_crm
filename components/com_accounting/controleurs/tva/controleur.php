<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addTva':
            addTva($_POST);
            break;
        case 'editTva':
            editTva($_POST);
            break;
        case 'deleteTva':
            deleteTva($_POST);
            break;
        case 'deleteDocTva':
            deleteDocTva($_POST);
            break;
    }
}

function deleteDocTva($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $tva = tva::find($data['id'],$_SESSION['agence']);
        $tva->setDoc("");
        if($tva->edit() == 1){
            @unlink("../../../images/tva/" . $tva->getDoc());
            echo "1";
        }
    } else {
        echo "0";
    }
}

function addTva($data)
{
    $indices = array("id_agence", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildTva($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editTva($data)
{
    $indices = array("id_tva", "id_agence", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildTva($data, $data['id_tva'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteTva($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $tva = new tva();
        $tva->setId($data['id']);
        if ($tva->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildTva($data, $id = null)
{
    $tva = new tva();

    if($id){
        $tva = tva::find($id);
    }

	$tva->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $tva->setAmount($data['amount']);
    $tva->setIncreasion($data['increasion']);
    $tva->setDate(date("Y-m-t",strtotime($data['date']."-1")));
    $tva->setStatus($data['status']);
	$tva->setRemark($data['remark']);
    $tva->setDateAdd(date("Y-m-d"));
    $tva->setLastEdit(date("Y-m-d"));

    return $tva;
}