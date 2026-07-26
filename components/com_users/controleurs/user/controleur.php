<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addUser':
            addUser($_POST);
            break;
        case 'editUser':
            editUser($_POST);
            break;
        case 'editPassword':
            editPassword($_POST);
            break;
        case 'editUser':
            editUser($_POST);
            break;
        case 'deleteUser':
            deleteUser($_POST);
            break;
        case "enableOperation":
            enableOperation($_POST);
            break;
    }
}

function addUser($data)
{
    
    $indices = array("login","password", "email", "nom", "prenom", "profil");
    if (fieldCheck($data, $indices)) {
        if (buildUser($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editUser($data)
{
    $indices = array("id", "login", "email", "nom", "prenom", "profil");
    if (fieldCheck($data, $indices)) {
        if (buildUser($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editPassword($data)
{
    $indices = array("id","password");
    if (fieldCheck($data, $indices)) {
        if (buildUserWithPassword($data, $data['id'])->editPass() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteUser($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $user = user::find($id);
        if ($user->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableOperation($data)
{
    $indices = array("id", "state");
    if (validateOperation($data, $indices))
    {
        $operation = new operation();
        $operation->setId($data['id']);
        $operation->setActive($data['state'] == "oui" ? 0 : 1);
        if ($operation->enable() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildUser($data, $id = null)
{
	global $db;
    $user = new user();
	
	$photo = array();
    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
        $photo = uploadFiles('photo','../../../images/users/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
    }
	
    if($id){
        $user = user::find($id);
        if(isset($data['password']) && !empty($data['password'])){
            $user->setPassword(hash('sha256',$data['password']));
        }
    }else{
        $user->setPassword(hash('sha256',$data['password']));
    }
	
	
    if(isset($photo[0]) ) {
		$user->setPhoto($photo[0]);
	}
	

    $user->setLogin($data['login']);
	$user->setNom($data['nom']);
    $user->setPrenom($data['prenom']);
	$user->setEmail($data['email']);
	$user->setTel($data['tel']);
	$user->setAdresse($data['adresse']);
	$user->setLangue($data['langue']);
    $user->setProfil(profil::find($data['profil']));
    $user->setSU(isset($data['su']) ? 1 : 0);
    $user->setActif(isset($data['actif']) ? 1 : 0);
    
	$user->setDateAdd(date("Y-m-d H:i:s"));
    $user->setLastEdit(date("Y-m-d H:i:s"));

    return $user;
}
function buildUserWithPassword($data, $id = null)
{
    $user = new user();

    if ($id) {
        $user = user::find($id);
    }

    if (isset($data['password']) && !empty($data['password'])) {
        $user->setPassword(hash('sha256', $data['password']));
    }

    return $user;
}
