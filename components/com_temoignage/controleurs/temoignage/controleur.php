<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addTemoignage':
            addTemoignage($_POST);
            break;
        case 'editTemoignage':
            editTemoignage($_POST);
            break;
        case 'deleteTemoignage':
            deleteTemoignage($_POST);
            break;
        case "enableTemoignage":
            enableTemoignage($_POST);
            break;
        case 'deleteTemoignages':
            deleteTemoignages($_POST);
            break;
        case 'enableTemoignages':
            enableTemoignages($_POST);
            break;
    }
}

function addTemoignage($data)
{
    $indices = array("nom");
    if (fieldcheck($data, $indices)) {
        if (buildTemoignage($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editTemoignage($data)
{
    $indices = array("id", "nom");
    if (fieldcheck($data, $indices)) {
        if (buildTemoignage($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteTemoignage($data)
{
    $indices = array("id");
    if (fieldcheck($data, $indices)) {
        $temoignage = temoignage::find($data["id"], $_SESSION["langue"]);
        if ($temoignage->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteTemoignages($data)
{
    $indices = array("ids");
    if (fieldcheck($data, $indices)) {
        $photos = temoignage::findPhotosName($data['ids']);
        if (temoignage::deleteMultiple($data) == 1) {
            if ($photos)
                foreach ($photos as $photo) {
                    if (file_exists("../../../../images/temoignages/" . $photo)) {
                        @unlink("../../../../images/temoignages/" . $photo);
                    }
                }

            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableTemoignage($data)
{
    $indices = array("id", "state");
    if (fieldcheck($data, $indices)) {
        $temoignage = new temoignage();
        $temoignage->setId($data['id']);
        $temoignage->setActive($data['state'] == "oui" ? 0 : 1);
        if ($temoignage->enable() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableTemoignages($data)
{
    $indices = array("ids", "active");
    if (fieldcheck($data, $indices)) {
        $data["active"] = ($data["active"] == "oui") ? 1 : 0;
        $res = temoignage::enableMultiple($data);
        if ($res == 1)
            echo '1';
        else
            echo '2';
    } else
        echo '0';
}



function buildTemoignage($data, $id = null)
{
    $temoignage = new temoignage();

    if ($id) {
        $temoignage = temoignage::find($id);
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['name'][0] != '') {
        if (!empty($temoignage->getPhoto())) {
            @unlink("../../../images/temoignages/" . $temoignage->getPhoto());
        }
        $var = uploadFiles('photo', '../../../images/temoignages/', array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
        $temoignage->setPhoto($var[0]);
    }

    $temoignage->setActive(isset($data['active']) ? 1 : 0);
    $temoignage->setOrdre($data['ordre']);
    $temoignage->setNom($data['nom']);
    $temoignage->setFonction($data['fonction']);
    $temoignage->setEmail($data['email']);
    $temoignage->setSujet($data['sujet']);
    $temoignage->setTemoignage($data['temoignage']);
    $temoignage->setDateAdd(date("Y-m-d"));
    $temoignage->setLastEdit(date("Y-m-d"));
    $temoignage->setLangue($_SESSION['langue']);

    return $temoignage;
}
