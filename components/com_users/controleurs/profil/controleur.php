<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addProfil':
            addProfil($_POST);
            break;
        case 'editProfil':
            editProfil($_POST);
            break;
        case 'deleteProfil':
            deleteProfil($_POST);
            break;
        case "setDroit":
            setDroit($_POST);
            break;
    }
}

function addProfil($data)
{
    $indices = array("profil");
    if (fieldCheck($data, $indices)) {
        if (buildProfil($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editProfil($data)
{
    $indices = array("id", "profil");
    if (fieldCheck($data, $indices)) {
        if (buildProfil($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteProfil($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $profil = profil::find($id);
        if ($profil->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function setDroit($data)
{
    global $db;
	
    $indices = array("task","profil","module","action");
    if (fieldCheck($data, $indices)) {
        $task = $data['task'];

        // activer un droit
        if ($task == 'enable') {
			$droit = new droit();
			$droit->setProfil(profil::find($data['profil']));
			$droit->setModule($data['module']);
			$droit->setAction($data['action']);
            if ($droit->add())
                echo '1';
            else
                echo '2';
        }

        // desactiver un droit
        if ($task == 'disable') {
            $SQLdelete = sprintf("DELETE FROM " . __prefixe_db__ . "droits WHERE id_profil = %s AND module = %s AND action = %s",
                GetSQLValueString($data['profil'], "int"),
                GetSQLValueString($data['module'], "text"),
                GetSQLValueString($data['action'], "text"));

            if (!$db->query($SQLdelete))
                echo '1';
            else
                echo '2';
        }

    }
}

function buildProfil($data, $id = null)
{
	global $db;
    $profil = new profil();
	
    if($id){
        $profil = profil::find($id);
    }
	
    $profil->setProfil($data['profil']);

    return $profil;
}