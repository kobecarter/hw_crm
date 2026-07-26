<?php

if(isset($_GET['task']) && !empty($_GET['task'])) {
    $task = $_GET['task'];
    switch ($task) {
        case 'login' :
            login($_POST);
            break;
        case 'login_global' :
            login_global();
            break;    
        case 'logout' :
            logout();
            break;
        case 'restaurePass':
            restaurePass($_POST);
            break;
        case 'newPass':
            newPass($_POST);
            break;
    }
}

function resetPasswordRequest(){
global $db, $siteURL;	
if(isset($_POST['email']) && !empty($_POST['email'])){
	$email = stripslashes($_POST['email']);
	$SQLselect = "SELECT * FROM 212_client WHERE email = '$email'";
	$result = $db->query($SQLselect);
	if($db->num_rows($result) == 1){
		$data = $db->fetch_array($result);
		$c = new client($data['idclient'],$db);
		$config = new config($db);
		$code = base64_encode("Zakaria".$c->getId()."EL HABOUSSI");
		/* -------------------------- Envoi mail -------------------------- */
		$message = '<html><body>
			<table width="100%" border="0">
				<tr><th><img src="'.$siteURL.'images/logo.png" width="100" style="margin:10px 0;" /></th>
				<th heigh="30"><h2>R&eacute;initialisation mot de passe du compte '.$config->getNom().'</h2></th></tr>
				<tr><td></td><td style="padding:10px; ligne-hight:20px;">
					<p>Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe</p>
					<p><a href="'.$siteURL.'index.php?option=com_login&task=newPassword&code='.$code.'">réinitialiser mon mot de passe</a></p>
				</td></tr>
			</table>
		</body></html>';
		
		$from = $config->getEmail();
		$headers ='From: <'.$from.'>'."\n";
		$headers .='Reply-To: '.$from."\n";
		$headers .='Content-Type: text/html; charset="utf-8"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit';
		mail($email, 'Reinitialisation mot de passe du compte '.$config->getNom(), $message, $headers);
		/* -------------------------- Envoi mail -------------------------- */
		
		echo '1';
	}
	else
	echo '2'; // email introuvable	
}
else
echo '0'; // champs requis
}
/* -------------------------------- checkConnexion -------------------------------- */
function checkConnexion(){
	if(isset($_SESSION['client']) && !empty($_SESSION['client'])){
		if($_SESSION['client']->isActif())
			echo '1'; // client actif connecté
		else
			echo '2'; // client inactif connecté
	}
	else
		echo '0'; // aucun client connecté
}
/* -------------------------------- Login -------------------------------- */
function login($data) {
   // Handle the standard login functionality
    if (isset($data['login']) && !empty($data['login']) && isset($data['password']) && !empty($data['password'])) {
        // Check if the username and password are correct for the last project
        $login = addslashes($data['login']);
        $password = $data['password'];
        $user = user::login($login, $password);
        if ($user->isConnected()) {
            $_SESSION['user'] = $user;
            echo '1'; // Connection successful
        } else {
            echo '2'; // Login and password are incorrect
        }
    } else {
        echo '0'; // Required fields not provided
    }
}

/* -------------------------------- Login -------------------------------- */
function login_global() {
    global $siteURL;
    if(isset($_GET['token']) && !empty($_GET['token'])){
       // Set the URL to make a request to
        $url = 'https://www.helloworld-agency.com/hw-label/new/components/com_login/controleurs/router.php?task=check_login_global&token='.$_GET['token']; // Replace with the actual API endpoint
          
        // Initialisez une session CURL.
        $ch = curl_init();  
          
        // Récupérer le contenu de la page
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
          
        //Saisir l'URL et la transmettre à la variable.
        curl_setopt($ch, CURLOPT_URL, $url); 
        //Désactiver la vérification du certificat puisque waytolearnx utilise HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Exécutez la requête 
        $result = curl_exec($ch); 
        $user = user::login_global("admin");
        if($result === '1' && $user->isConnected()){
            $_SESSION['user'] = $user;
            setcookie("login_global", "global", time() + 31536000, "/");
            header("location: ".$siteURL."index.php?option=com_dashboard");
        }else{
            header("location: ".$siteURL."index.php?option=com_login");
        }
    }else{
        header("location: ".$siteURL."index.php?option=com_login");
    }
    
    //Afficher le résultat
}

/* -------------------------------- logout -------------------------------- */
function logout(){
	if(isset($_SESSION['user'])){
		unset($_SESSION['user']);
		echo '1';
		// Unset or expire the cookies
		setcookie('login', '', time() - 3600, '/');
		setcookie('username', '', time() - 3600, '/');
		setcookie("login_global", "", time() + 31536000, "/");
		
	}
}
/* -------------------------------- restaurePass -------------------------------- */
function restaurePass($data){
    if(isset($data['email']) && !empty($data['email'])){
        if(user::isEmailValable($data['email'])){
            echo '1';
        }else{
            echo '2';
        }
    }else{
        echo '0';
    }
}
/* -------------------------------- newPass -------------------------------- */
function newPass($data){
    if(isset($data['code']) && !empty($data['code']) && isset($data['password']) && !empty($data['password'])){
        if($data['code'] == "code"){
            echo '1';
        }else{
            echo '2';
        }
    }else{
        echo '0';
    }
}
?>