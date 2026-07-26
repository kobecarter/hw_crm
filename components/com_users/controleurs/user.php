<?php
include "../../../config.php";
require_once('../../../instanceDb.php');
require_once('../../../includes/functions/functions.php');
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    $task = $_GET['task'];
    switch ($task) {
        case 'addUser' :
            addUser($_POST);
            break;
        case 'editUser' :
            editUser($_POST);
            break;
        case 'editPassword' :
            editPassword($_POST);
            break;
        case 'deleteUser' :
            deleteUser($_POST);
            break;
        case 'enableUser' :
            enableUser($_POST);
            break;
        case 'addProfil' :
            addProfil($_POST);
            break;
        case 'editProfil' :
            editProfil($_POST);
            break;
        case 'deleteProfil' :
            deleteProfil($_POST);
            break;
        case 'setDroit' :
            setDroit($_POST);
            break;
        case 'dev' :
            dev($_POST);
            break;
    }
}

/* -------------------------------- editUser -------------------------------- */
function editUser($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id']) && isset($data['prenom']) && !empty($data['prenom']) && isset($data['nom']) && !empty($data['nom']) && isset($data['email']) && !empty($data['email'])) {
		
		$u = new compte($data['id'], $db);
		
		
		$photo = '';
        if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
            $var = uploadFiles('photo','../../../../images/users/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
            $photo = "photo = ".GetSQLValueString($var[0], "text").", ";
        }

        $updateSQL = sprintf("UPDATE " . __prefixe_db__ . "users SET prenom=%s, nom=%s, email=%s, tel=%s, adresse=%s, fonction=%s, $photo langue=%s, id_profil=%s WHERE id=%s",

            GetSQLValueString($data['prenom'], "text"),
            GetSQLValueString($data['nom'], "text"),
            GetSQLValueString($data['email'], "text"),
            GetSQLValueString($data['tel'], "text"),
            GetSQLValueString($data['adresse'], "text"),
			GetSQLValueString($data['fonction'], "text"),				 
            GetSQLValueString($data['langue'], "text"),
            GetSQLValueString($data['profil'], "int"),
            GetSQLValueString($data['id'], "int"));

        $db->query($updateSQL);

        $user = new compte($data['id'], $db);
        $_SESSION['user'] = $user;
		
		//Supprimer l'ancienne image
		if($photo != ''){
			@unlink("../../../../images/users/".$u->getPhoto());
		}

        echo '1';
    } else
        echo '0';
}

function editPassword($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id']) && isset($data['password']) && !empty($data['password']) ) {
		
		$u = new compte($data['id'], $db);
		
        if ($data['password'] != "") {
            $pass = hash('sha256', $data['password']);
            $password = " password= " . GetSQLValueString($pass, "text") . ",";
        } else
            $password = "";
        $updateSQL = sprintf("UPDATE " . __prefixe_db__ . "users SET " . $password . " WHERE id=%s",

            GetSQLValueString($data['id'], "int"));

        $db->query($updateSQL);

        $user = new compte($data['id'], $db);
        $_SESSION['user'] = $user;
		
        echo '1';
    } else
        echo '0';
        
        
}

/* -------------------------------- addUser -------------------------------- */
function addUser($data)
{
    global $db;
    if (isset($data['prenom']) && !empty($data['prenom']) && isset($data['nom']) && !empty($data['nom']) && isset($data['login']) && !empty($data['login']) && isset($data['password']) && !empty($data['password']) && isset($data['email']) && !empty($data['email'])) {

		// teste d'unicité du login	
        $SQLselect = "SELECT * FROM " . __prefixe_db__ . "users WHERE login = '" . $data['login'] . "'";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 0) {

            $pass = hash('sha256', $data['password']);
			
			$photo = '';
			if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
				$var = uploadFiles('photo','../../../../images/users/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
				$photo = $var[0];
			}

            $insertSQL = sprintf("INSERT INTO " . __prefixe_db__ . "users (login, password, prenom, nom, email, tel, adresse, fonction, photo, langue, su, id_profil) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($data['login'], "text"),
                GetSQLValueString($pass, "text"),
                GetSQLValueString($data['prenom'], "text"),
                GetSQLValueString($data['nom'], "text"),
                GetSQLValueString($data['email'], "text"),
                GetSQLValueString($data['tel'], "text"),
                GetSQLValueString($data['adresse'], "text"),
				GetSQLValueString($data['fonction'], "text"),
				GetSQLValueString($photo, "text"),
                GetSQLValueString($data['langue'], "text"),
                GetSQLValueString(0, "int"),
                GetSQLValueString($data['profil'], "int"));

            if (!$db->query($insertSQL))
                echo '1';
            else
                echo '3';
        } else
            echo '2'; // login existe deja dans la base de données
    } else
        echo '0'; // champs obligatoirs
}

/* -------------------------------- deleteUser -------------------------------- */
function deleteUser($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {
        $id = intval($data['id']);
		$u = new compte($id, $db);
        $SQLdelete = "DELETE FROM " . __prefixe_db__ . "users WHERE id = $id";
        if (!$db->query($SQLdelete)){
            echo '1';
			@unlink("../../../../images/users/".$u->getPhoto());
		}
        else
            echo '2';
    } else
        echo '0';
}

/* -------------------------------- enableUser -------------------------------- */
function enableUser($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {
        $id = intval($data['id']);
        $u = new compte($id, $db);
        $val = $u->isActif() ? 0 : 1;
        $SQLdelete = "UPDATE " . __prefixe_db__ . "users SET actif = $val WHERE id = $id";
        if (!$db->query($SQLdelete))
            echo '1';
        else
            echo '2';
    } else
        echo '0';
}

/* -------------------------------- addProfil -------------------------------- */
function addProfil($data)
{
    global $db;
    if (isset($data['profil']) && !empty($data['profil'])) {

        $insertSQL = sprintf("INSERT INTO " . __prefixe_db__ . "profils (profil) VALUES (%s)",
            GetSQLValueString($data['profil'], "text"));

        if (!$db->query($insertSQL))
            echo '1';
        else
            echo '2';
    } else
        echo '0'; // champs obligatoirs
}

/* -------------------------------- editProfil -------------------------------- */
function editProfil($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id']) && isset($data['profil']) && !empty($data['profil'])) {

        /// Modification de la table users
        $updateSQL = sprintf("UPDATE " . __prefixe_db__ . "profils SET  profil=%s WHERE id=%s",

            GetSQLValueString($data['profil'], "text"),
            GetSQLValueString($data['id'], "int"));

        $db->query($updateSQL);
        /// Fin Modification de la table users

        echo '1';
    } else
        echo '0';
}

/* -------------------------------- deleteProfil -------------------------------- */
function deleteProfil($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {
        $id = intval($data['id']);
        $SQLdelete = "DELETE FROM " . __prefixe_db__ . "profils WHERE id = $id";
        if (!$db->query($SQLdelete))
            echo '1';
        else
            echo '2';
    } else
        echo '0';
}

/* -------------------------------- setDroit -------------------------------- */
function setDroit($data)
{
    global $db;
    if (isset($data['task']) && !empty($data['task']) && isset($data['profil']) && !empty($data['profil']) && isset($data['module']) && !empty($data['module']) && isset($data['action']) && !empty($data['action'])) {
        $task = $data['task'];

        // activer un droit
        if ($task == 'enable') {
            $SQLinsert = sprintf("INSERT INTO " . __prefixe_db__ . "droits (id_profil, module, action) VALUES (%s, %s, %s)",
                GetSQLValueString($data['profil'], "int"),
                GetSQLValueString($data['module'], "text"),
                GetSQLValueString($data['action'], "text"));

            if (!$db->query($SQLinsert))
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

/* -------------------------------- dev -------------------------------- */
function dev($data)
{
    $code = "hw@mode_dev";
    if (isset($_SESSION["user"]) && $_SESSION["user"]->isConnected()) {
       if($_SESSION["user"]->isDev()) {
           $_SESSION["user"]->setDev(0);
           echo "0";
       } else {
           if(isset($data["code"]) && !empty($data["code"])){
               if($data["code"] == $code) {
                   $_SESSION["user"]->setDev(1);
                   echo "1";
               } else {
                   echo "2";
               }
           } else {
               echo "2";
           }
       }
    }
}

?>