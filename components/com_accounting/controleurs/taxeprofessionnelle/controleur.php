<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addTaxeprofessionnelle':
            addTaxeprofessionnelle($_POST);
            break;
        case 'editTaxeprofessionnelle':
            editTaxeprofessionnelle($_POST);
            break;
        case 'deleteTaxeprofessionnelle':
            deleteTaxeprofessionnelle($_POST);
            break;
        case 'deleteDocTaxe':
            deleteDocTaxe($_POST);
            break;  
    }
}

function deleteDocTaxe($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $taxe = taxeprofessionnelle::find($data['id'],$_SESSION['agence']);
        $taxe->setDoc("");
        if($taxe->edit() == 1){
            @unlink("../../../images/taxe/" . $taxe->getDoc());
            echo "1";
        }
    } else {
        echo "0";
    }
}

function addTaxeprofessionnelle($data)
{
    $indices = array("id_agence", "amount", "date_of_depot", "year");
    if (fieldCheck($data, $indices)) {
        if (buildTaxeprofessionnelle($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editTaxeprofessionnelle($data)
{
    $indices = array("id_taxeprofessionnelle", "id_agence", "amount", "date_of_depot", "year");
    if (fieldCheck($data, $indices)) {
        if (buildTaxeprofessionnelle($data, $data['id_taxeprofessionnelle'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteTaxeprofessionnelle($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $taxeprofessionnelle = new taxeprofessionnelle();
        $taxeprofessionnelle->setId($data['id']);
        if ($taxeprofessionnelle->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildTaxeprofessionnelle($data, $id = null)
{
    $taxeprofessionnelle = new taxeprofessionnelle();
    
    $doc = array();
    if(isset($_FILES['doc']) && $_FILES['doc']['name'][0]!=''){
        $doc = uploadFiles('doc','../../../images/taxe/',  array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    }

    if($id){
        $taxeprofessionnelle = taxeprofessionnelle::find($id,$data['id_agence']);
    }
    if(isset($doc[0])) {
		$taxeprofessionnelle->setDoc($doc[0]);
	}

	$taxeprofessionnelle->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $taxeprofessionnelle->setDateOfDepot($data['date_of_depot']);
    $taxeprofessionnelle->setYear($data['year']);
    $taxeprofessionnelle->setAmount($data['amount']);
    $taxeprofessionnelle->setIncreasion($data['increasion']);
    $taxeprofessionnelle->setStatus($data['status']);
	$taxeprofessionnelle->setRemark($data['remark']);
    $taxeprofessionnelle->setDateAdd(date("Y-m-d"));
    $taxeprofessionnelle->setLastEdit(date("Y-m-d"));

    return $taxeprofessionnelle;
}