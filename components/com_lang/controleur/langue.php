<?php
include"../../../config.php";
require_once('../../../instanceDb.php');
require_once('../../../includes/functions/functions.php');
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    $task = $_GET['task'];
    switch ($task) {
        case 'addLangue' :
            addLangue($_POST);
            break;
        case 'editLangue' :
            editLangue($_POST);
            break;
        case 'deleteLangue' :
            deleteLangue($_POST);
            break;
        case 'enableLangue' :
            enableLangue($_POST);
            break;
        case 'disableLangue' :
            disableLangue($_POST);
            break;
    }
}

/* -------------------------------- editLangue -------------------------------- */
function editLangue($data){
    global $db, $URL;
    if(isset($data['id']) && !empty($data['id']) && isset($data['nom']) && !empty($data['nom']) && isset($data['code']) && !empty($data['code'])){

        $id = intval($data['id']);
        $l = new langue($id,$db);

        $photo = '';
        if(isset($_FILES['image']) && $_FILES['image']['name'][0]!=''){
            $var = uploadFiles('image','../../../../images/langues/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));

            $photo = "flag = ".GetSQLValueString($var[0], "text").", ";
        }

        $updateSQL = sprintf("UPDATE ".__prefixe_db__."langue SET $photo nom=%s, code=%s, defaut=%s, last_edit=%s WHERE id=%s",

            GetSQLValueString($data['nom'], "text"),
            GetSQLValueString($data['code'], "text"),
            GetSQLValueString(isset($data['defaut']) ? 1 : 0, "int"),
            GetSQLValueString(date('Y-m-d'), "date"),
            GetSQLValueString($id, "int"));

        if(!$db->query($updateSQL)){
            if(isset($data['defaut']) && !empty($data['defaut'])){
                $updateSQL2 =  sprintf("UPDATE ".__prefixe_db__."langue SET defaut=%s WHERE id<>%s",
                    GetSQLValueString(0, "int"),
                    GetSQLValueString($id, "int"));
                $db->query($updateSQL2);
            }
            // supprimer l'ancienne photo
            if($photo != ''){
                @unlink('../../../../images/langues/'.$l->getFlag());
            }
            seo();
            echo '1';
        }else
            echo '2';
    }
    else
        echo '0';
}

/* -------------------------------- addLangue-------------------------------- */
function addLangue($data){
    global $db, $URL;
    echo "test";
    if(isset($data['nom']) && !empty($data['nom']) && isset($data['code']) && !empty($data['code'])){

        $photo = '';
        if(isset($_FILES['image']) && $_FILES['image']['name'][0]!=''){
            $var = uploadFiles('image','../../../../images/langues/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));

            $photo = $var[0];
        }

        $insertSQL = sprintf("INSERT INTO ".__prefixe_db__."langue (nom, code, flag, actif, defaut, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($data['nom'], "text"),
            GetSQLValueString($data['code'], "text"),
            GetSQLValueString($photo, "text"),
            GetSQLValueString(0, "int"),
            GetSQLValueString(isset($data['defaut']) ? 1 : 0, "int"),
            GetSQLValueString(date('Y-m-d'), "date"),
            GetSQLValueString(date('Y-m-d'), "date"));

        if(!$db->query($insertSQL)){
            if(isset($data['defaut']) && !empty($data['defaut'])){
                $updateSQL2 =  sprintf("UPDATE ".__prefixe_db__."langue SET defaut=%s WHERE nom<>%s",
                    GetSQLValueString(0, "int"),
                    GetSQLValueString($data['nom'], "text"));
                $db->query($updateSQL2);
            }
            seo();
            echo '1';
        }else
            echo '2';
    }
    else
        echo '0'; // champs obligatoirs
}

/* -------------------------------- deleteLangue -------------------------------- */
function deleteLangue($data){
    global $db, $URL;
    if(isset($data['id']) && !empty($data['id'])){
        $id = intval($data['id']);
        $l = new langue($id,$db);
        $SQLdelete = "DELETE FROM ".__prefixe_db__."langue WHERE id = $id";
        if(!$db->query($SQLdelete)){
            // supprimer la photos
            @unlink('../../../../images/langues/'.$l->getflag());
            seo();
            echo '1';
        }else {
            echo '2';
        }
    }
    else
        echo '0';
}

/* -------------------------------- enableLangue -------------------------------- */
function enableLangue($data){
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $SQLupdate = sprintf("UPDATE ".__prefixe_db__."langue SET actif=%s WHERE id=%s",
            GetSQLValueString(1, "int"),
            GetSQLValueString($data['id'], "text"));
        if(!$db->query($SQLupdate)){
            echo 1;
        }else{
            echo 2;
        }
    }
}

/* -------------------------------- disableModule -------------------------------- */
function disableLangue($data){
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $SQLupdate = sprintf("UPDATE ".__prefixe_db__."langue SET actif=%s WHERE id=%s",
            GetSQLValueString(0, "int"),
            GetSQLValueString($data['id'], "text"));
        if(!$db->query($SQLupdate)){
            echo 1;
        }else{
            echo 2;
        }
    }
}
?>