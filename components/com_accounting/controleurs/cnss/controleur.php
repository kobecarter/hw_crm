<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addCnss':
            addCnss($_POST);
            break;
        case 'editCnss':
            editCnss($_POST);
            break;
        case 'deleteCnss':
            deleteCnss($_POST);
            break;
    }
}

function addCnss($data)
{
    $indices = array("id_agence", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildCnss($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editCnss($data)
{
    $indices = array("id_cnss", "id_agence", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildCnss($data, $data['id_cnss'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteCnss($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $cnss = new cnss();
        $cnss->setId($data['id']);
        if ($cnss->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildCnss($data, $id = null)
{
    $cnss = new cnss();
    $justification = array();
    if(isset($_FILES['justification']) && $_FILES['justification']['name'][0]!=''){
        $justification = uploadFiles('justification','../../../images/accounting/cnss',  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
    }

    if($id){
        $cnss = cnss::find($id,$data['id_agence']);
    }
	
	if(isset($justification[0])) {
		$cnss->setJustification($justification[0]);
	}

	$cnss->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $cnss->setAmount($data['amount']);
    $cnss->setIncreasion($data['increasion']);
    $cnss->setDate(date("Y-m-t",strtotime($data['date']."-1")));
    $cnss->setStatus($data['status']);
	$cnss->setRemark($data['remark']);
    $cnss->setDateAdd(date("Y-m-d"));
    $cnss->setLastEdit(date("Y-m-d"));

    return $cnss;
}