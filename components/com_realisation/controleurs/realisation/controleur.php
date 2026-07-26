<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addRealisation':
            addRealisation($_POST);
            break;
        case 'editRealisation':
            editRealisation($_POST);
            break;
        case 'deleteRealisation':
            deleteRealisation($_POST);
            break;
    }
}

function addRealisation($data)
{


    $indices = array("titre", "texte", "url_project", "extrait", "ordre");
    if (fieldCheck($data, $indices)) {
        if (buildRealisation($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editRealisation($data)
{
    $indices = array("id", "titre", "texte", "url_project", "extrait", "ordre");
    if (fieldCheck($data, $indices)) {
        if (buildRealisation($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteRealisation($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $realisation = new realisation();
        $realisation->setId($data['id']);
        if ($realisation->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}


function buildRealisation($data, $id = null)
{
    $realisation = new realisation();

    if ($id) {
        $realisation = realisation::find($id);
    }

    $photo = array();
    if (isset($_FILES['photo']) && $_FILES['photo']['name'][0] != '') {
        $saveDir = '../../../images/realisation';
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        $photo = uploadFiles('photo', $saveDir,  array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
    }
    if (isset($photo[0])) {
        $realisation->setPhoto($photo[0]);
    }

    $realisation->setTitre($data['titre']);
    $realisation->setTexte($data['texte']);
    $realisation->setUrlProject($data['url_project']);
    $realisation->setExtrait($data['extrait']);
    $realisation->setOrdre($data['ordre']);
    $realisation->setDateAdd(date("Y-m-d"));

    return $realisation;
}
