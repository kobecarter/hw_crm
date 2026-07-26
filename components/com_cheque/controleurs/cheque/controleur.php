<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addCheque':
            addCheque($_POST);
            break;
        case 'editCheque':
            editCheque($_POST);
            break;
        case 'deleteCheque':
            deleteCheque($_POST);
            break;
    }
}

function addCheque($data)
{
   // die(json_encode($data));
    $indices = array("check_number","date","beneficiary","amount","currency","status");
    if (fieldCheck($data, $indices)) {
        if (buildCheque($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editCheque($data)
{
    $indices = array("id", "check_number","date","beneficiary","amount","currency","status");
    if (fieldCheck($data, $indices)) {
        if (buildCheque($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteCheque($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $cheque = cheque::find($id,$_SESSION['agence']);
        if ($cheque->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildCheque($data, $id = null)
{
    $cheque = new cheque();
	
	$file = array();
    if(isset($_FILES['file_check']) && $_FILES['file_check']['name'][0]!=''){
        $file = uploadFiles('file_check','../../../images/cheques/',  array('jpg','jpeg','gif','png','webp','svg','pdf','JPG','JPEG','GIF','PNG','WEBP','SVG','PDF'));
    }
	
    if($id){
		$cheque = cheque::find($id,$_SESSION['agence']);
    }
	if(isset($file[0])) {
		$cheque->setFile($file[0]);
	}
	$cheque->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
	$cheque->setCheckNumber($data['check_number']);
	$cheque->setDate(dateBD($data['date']));
	$cheque->setBeneficiary($data['beneficiary']);
	$cheque->setAmount($data['amount']);
    $cheque->setCurrency($data['currency']);
	$cheque->setStatus($data['status']);
    $cheque->setReason($data['reason']);
	$cheque->setComment($data['comment']);
    $cheque->setDateAdd(date("Y-m-d"));
    $cheque->setLastEdit(date("Y-m-d"));

    return $cheque;
}