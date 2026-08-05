<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addBilan':
            addBilan($_POST);
            break;
        case 'editBilan':
            editBilan($_POST);
            break;
        case 'deleteBilan':
            deleteBilan($_POST);
            break;
        case 'deleteDocBilan':
            deleteDocBilan($_POST);
            break;    
    }
}

function deleteDocBilan($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $bilan = bilan::find($data['id'],$_SESSION['agence']);
        $bilan->setDoc("");
        if($bilan->edit() == 1){
            @unlink("../../../images/bilan/" . $bilan->getDoc());
            echo "1";
        }
    } else {
        echo "0";
    }
}

function addBilan($data)
{
    $indices = array("id_agence", "amount", "date_of_depot", "year");
    if (fieldCheck($data, $indices)) {
        if (buildBilan($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editBilan($data)
{
    $indices = array("id_bilan", "id_agence", "amount", "date_of_depot", "year");
    if (fieldCheck($data, $indices)) {
        if (buildBilan($data, $data['id_bilan'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBilan($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find($id, $agence) plutôt que new+setId() : sans ça, un id valide d'une autre agence se
        // faisait supprimer sans aucune vérification d'appartenance (IDOR).
        $bilan = bilan::find($data['id'], $_SESSION['agence']);
        if ($bilan->getId() == 0) {
            echo "2";
            return;
        }
        if ($bilan->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildBilan($data, $id = null)
{
    $bilan = new bilan();
    
    $doc = array();
    if(isset($_FILES['doc']) && $_FILES['doc']['name'][0]!=''){
        $doc = uploadFiles('doc','../../../images/bilan/',  array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    }

    if($id){
        $bilan = bilan::find($id,$data['id_agence']);
    }
    if(isset($doc[0])) {
		$bilan->setDoc($doc[0]);
	}

	$bilan->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $bilan->setDateOfDepot($data['date_of_depot']);
    $bilan->setYear($data['year']);
    $bilan->setAmount($data['amount']);
    $bilan->setIncreasion($data['increasion']);
    $bilan->setStatus($data['status']);
	$bilan->setRemark($data['remark']);
    $bilan->setDateAdd(date("Y-m-d"));
    $bilan->setLastEdit(date("Y-m-d"));

    return $bilan;
}