<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addImpot':
            addImpot($_POST);
            break;
        case 'editImpot':
            editImpot($_POST);
            break;
        case 'deleteImpot':
            deleteImpot($_POST);
            break;
        case 'deleteDocImpot':
            deleteDocImpot($_POST);
            break;
    }
}

function deleteDocImpot($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $impot = impot::find($data['id'],$_SESSION['agence']);
        $impot->setDoc("");
        if($impot->edit() == 1){
            @unlink("../../../images/impot/" . $impot->getDoc());
            echo "1";
        }
    } else {
        echo "0";
    }
}

function addImpot($data)
{
    $indices = array("id_agence", "type", "amount", "date_of_depot", "year");
    if (fieldCheck($data, $indices)) {
        if (buildImpot($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editImpot($data)
{
    $indices = array("id_impot", "id_agence", "type", "amount", "date_of_depot", "year");
    if (fieldCheck($data, $indices)) {
        if (buildImpot($data, $data['id_impot'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteImpot($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find($id, $agence) plutôt que new+setId() : sans ça, un id valide d'une autre agence se
        // faisait supprimer sans aucune vérification d'appartenance (IDOR) - même correction déjà
        // appliquée aux autres modules com_accounting.
        $impot = impot::find($data['id'], $_SESSION['agence']);
        if ($impot->getId() == 0) {
            echo "2";
            return;
        }
        if ($impot->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildImpot($data, $id = null)
{
    $impot = new impot();

    $doc = array();
    if(isset($_FILES['doc']) && $_FILES['doc']['name'][0]!=''){
        $doc = uploadFiles('doc','../../../images/impot/',  array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    }

    if($id){
        $impot = impot::find($id,$data['id_agence']);
    }
    if(isset($doc[0])) {
        $impot->setDoc($doc[0]);
    }

    $impot->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $impot->setType($data['type']);
    $impot->setDateOfDepot($data['date_of_depot']);
    $impot->setYear($data['year']);
    $impot->setAmount($data['amount']);
    $impot->setIncreasion($data['increasion']);
    $impot->setStatus($data['status']);
    $impot->setRemark($data['remark']);
    $impot->setDateAdd(date("Y-m-d"));
    $impot->setLastEdit(date("Y-m-d"));

    return $impot;
}
