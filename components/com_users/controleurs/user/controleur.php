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
        case 'editMyProfile':
            editMyProfile($_POST);
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

function editMyProfile($data)
{
    $indices = array("id", "email", "nom", "prenom");
    if (fieldCheck($data, $indices)) {
        // Un utilisateur ne peut modifier que SON PROPRE compte, jamais un autre - l'id soumis
        // (champ hidden du formulaire) est vérifié contre la session, pas seulement pré-rempli côté
        // vue. Sans ce contrôle serveur, changer cet id à la main permettrait de modifier n'importe
        // quel compte en contournant complètement les droits admin.
        if (intval($data['id']) !== intval($_SESSION['user']->getId())) {
            echo "2";
            return;
        }
        if (buildUserSelf($data)->edit() == 1) {
            // Le header (nom/photo affichés dans le dropdown avatar) lit $_SESSION['user'] -
            // sans ce rafraîchissement l'utilisateur verrait son ancienne photo/nom jusqu'à la
            // prochaine connexion. user::find() ne positionne jamais connected=true (seul le vrai
            // login le fait) : sans setConnected(true) explicite ici, modules/login/index.php
            // verrait isConnected()===false dès la requête suivante et renverrait vers le login.
            $refreshedUser = user::find($_SESSION['user']->getId());
            $refreshedUser->setConnected(true);
            $_SESSION['user'] = $refreshedUser;
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
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        }
    }else{
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
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
    $user->setTauxCommission(isset($data['taux_commission']) && $data['taux_commission'] !== '' ? floatval($data['taux_commission']) : 0);
    
	$user->setDateAdd(date("Y-m-d H:i:s"));
    $user->setLastEdit(date("Y-m-d H:i:s"));

    return $user;
}

function buildUserSelf($data)
{
    // Auto-édition : ne touche QUE les champs personnels. On repart de l'enregistrement existant
    // en base (pas d'un objet neuf comme buildUser()) pour que login/profil/su/actif/taux_commission
    // - réservés à l'admin via le formulaire admin/buildUser() - gardent leur valeur actuelle telle
    // quelle, sans qu'aucun setter dessus ne soit jamais appelé ici. Le login n'est même pas lu
    // depuis $data : même trafiqué dans la requête POST, il n'a aucun effet.
    $user = user::find($_SESSION['user']->getId());

    $photo = array();
    if (isset($_FILES['photo']) && $_FILES['photo']['name'][0] != '') {
        $photo = uploadFiles('photo', '../../../images/users/', array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
    }
    if (isset($photo[0])) {
        $user->setPhoto($photo[0]);
    }

    if (isset($data['password']) && !empty($data['password'])) {
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
    }

    $user->setNom($data['nom']);
    $user->setPrenom($data['prenom']);
    $user->setEmail($data['email']);
    $user->setTel($data['tel']);
    $user->setAdresse($data['adresse']);
    $user->setLangue($data['langue']);
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
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
    }

    return $user;
}
