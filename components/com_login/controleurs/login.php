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
        case 'resetPasswordRequest':
            resetPasswordRequest($_POST);
            break;
        case 'resetPasswordSubmit':
            resetPasswordSubmit($_POST);
            break;
        case 'googleLogin':
            googleLogin($_POST);
            break;
        case 'googleLink':
            googleLink($_POST);
            break;
        case 'googleUnlink':
            googleUnlink();
            break;
    }
}

/* -------------------------------- Mot de passe oublié -------------------------------- */
// Étape 1/2 : demande de réinitialisation. Ne révèle JAMAIS si l'email correspond à un compte
// (message générique identique dans les deux cas) - sinon ce formulaire deviendrait un moyen de
// vérifier quels emails existent dans le CRM. Le token en clair ne transite QUE par l'email envoyé
// - voir userpasswordreset.php pour le détail du stockage (hash sha256, jamais le token lui-même).
function resetPasswordRequest($data)
{
    header('Content-Type: application/json');
    if (!isset($data['email']) || empty($data['email'])) {
        echo json_encode(array('success' => 0, 'message' => 'Veuillez indiquer votre email.'));
        return;
    }

    $messageGenerique = "Si cet email correspond à un compte, un lien de réinitialisation vient de lui être envoyé.";
    $user = user::findByEmail(trim($data['email']));

    if ($user->getId() != 0 && defined('SMTP_HOST') && SMTP_HOST != '') {
        global $siteURL;
        $token = userpasswordreset::generate($user->getId());
        $lienReset = $siteURL . 'index.php?option=com_login&task=resetPassword&token=' . urlencode($token);

        require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_USERNAME, 'Hello World CRM');
            $mail->addAddress($user->getEmail(), trim($user->getPrenom() . ' ' . $user->getNom()));
            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body = '<p>Bonjour ' . htmlspecialchars($user->getPrenom()) . ',</p>'
                . '<p>Vous avez demandé la réinitialisation de votre mot de passe sur le CRM Hello World.</p>'
                . '<p><a href="' . htmlspecialchars($lienReset) . '">Cliquez ici pour choisir un nouveau mot de passe</a></p>'
                . '<p>Ce lien est valable 1 heure. Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement cet email.</p>';
            $mail->AltBody = "Réinitialisez votre mot de passe : " . $lienReset . " (valable 1 heure)";
            $mail->send();
            copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
        } catch (\Exception $e) {
            // Volontairement silencieux côté réponse (message générique inchangé) - une erreur
            // SMTP ne doit jamais laisser deviner si l'email existait ou non.
        }
    }

    echo json_encode(array('success' => 1, 'message' => $messageGenerique));
}

// Étape 2/2 : le lien reçu par email ramène ici avec le token en clair - vérifié contre son hash
// (userpasswordreset::findValidByToken()), jamais réutilisable une fois consommé (markUsed()).
function resetPasswordSubmit($data)
{
    header('Content-Type: application/json');
    if (!isset($data['token']) || empty($data['token']) || !isset($data['password']) || empty($data['password'])) {
        echo json_encode(array('success' => 0, 'message' => 'Champs requis manquants.'));
        return;
    }
    if (strlen($data['password']) < 8) {
        echo json_encode(array('success' => 0, 'message' => 'Le mot de passe doit contenir au moins 8 caractères.'));
        return;
    }

    $reset = userpasswordreset::findValidByToken($data['token']);
    if (!$reset->isUsable()) {
        echo json_encode(array('success' => 0, 'message' => 'Ce lien de réinitialisation est invalide ou a expiré. Refaites une demande.'));
        return;
    }

    $user = user::find($reset->getIdUser());
    if ($user->getId() == 0) {
        echo json_encode(array('success' => 0, 'message' => 'Compte introuvable.'));
        return;
    }

    $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
    $user->editPass();
    $reset->markUsed();

    echo json_encode(array('success' => 1, 'message' => 'Mot de passe mis à jour. Vous pouvez vous connecter.'));
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
            if (!empty($data['remember'])) {
                poserCookieSeRappeler($user->getId());
            }
            echo '1'; // Connection successful
        } else {
            echo '2'; // Login and password are incorrect
        }
    } else {
        echo '0'; // Required fields not provided
    }
}

// "Se rappeler de moi" : cookie longue durée (30 jours) séparé de la session PHP classique - voir
// userremembertoken.php pour le détail sécurité (motif sélecteur/validateur). Le cookie ne
// contient jamais l'id_user en clair, seulement "selector:validator".
function poserCookieSeRappeler($idUser)
{
    $paire = userremembertoken::generate($idUser);
    setcookie(
        userremembertoken::COOKIE_NAME,
        $paire['selector'] . ':' . $paire['validator'],
        time() + userremembertoken::EXPIRE_DURATION_SEC,
        '/',
        '',
        false,
        true // httponly - jamais lisible en JS, seule protection réelle contre un vol par XSS
    );
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
        // La vérification du certificat était désactivée ici - sur un flux qui, si la requête
        // renvoie exactement "1", connecte l'appelant en tant qu'admin SANS AUCUNE vérification de
        // mot de passe côté CRM (tout est délégué à ce endpoint distant). Désactiver la vérif TLS
        // dessus ouvrait une attaque MITM triviale (n'importe qui en position d'intercepter le
        // trafic vers ce domaine peut répondre "1" à la place du vrai serveur et devenir admin).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
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
		// "Se rappeler de moi" : révoque le jeton en base (pas seulement le cookie côté navigateur)
		// - une simple expiration du cookie laisserait le jeton valide en base indéfiniment.
		if (isset($_COOKIE[userremembertoken::COOKIE_NAME])) {
			$paire = explode(':', $_COOKIE[userremembertoken::COOKIE_NAME], 2);
			if (!empty($paire[0])) {
				userremembertoken::deleteBySelector($paire[0]);
			}
			setcookie(userremembertoken::COOKIE_NAME, '', time() - 3600, '/');
		}
	}
}

/* -------------------------------- Google (Se connecter avec Google) -------------------------------- */
// Vérifie le jeton d'identité Google (ID token / "credential", déjà obtenu côté navigateur par
// Google Identity Services) auprès de l'endpoint public tokeninfo de Google - même mécanisme,
// sans SDK, que client::googleLoginApi() (components/com_client/classes/client.php), déjà en
// production pour l'espace client. GOOGLE_CLIENT_ID (config.php) est un identifiant public,
// jamais un secret - le réutiliser ici pour le CRM ne demande qu'une chose côté Google Cloud
// Console : ajouter l'origine de ce CRM (localhost en dev, le domaine réel en prod) aux "Authorized
// JavaScript origins" du client OAuth existant.
function verifierIdTokenGoogle($credential)
{
    if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '') {
        return array('success' => 0, 'message' => 'Connexion Google non configurée.');
    }
    $ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $info = json_decode((string) $body);
    if ($httpCode !== 200 || !is_object($info) || !isset($info->aud) || $info->aud !== GOOGLE_CLIENT_ID) {
        return array('success' => 0, 'message' => 'Jeton Google invalide.');
    }
    $emailVerifie = isset($info->email_verified) && ($info->email_verified === true || $info->email_verified === 'true');
    if (!$emailVerifie || empty($info->email)) {
        return array('success' => 0, 'message' => 'Email Google non vérifié.');
    }
    return array('success' => 1, 'email' => $info->email);
}

// "Se connecter avec Google" (page de login, utilisateur pas encore authentifié) : n'authentifie
// QUE si ce compte Google a déjà été explicitement lié au préalable (colonne google_email, jamais
// un rapprochement automatique par simple coïncidence avec la colonne "email" de connexion).
function googleLogin($data)
{
    header('Content-Type: application/json');
    if (empty($data['credential'])) {
        echo json_encode(array('success' => 0, 'message' => 'Requête invalide.'));
        return;
    }
    $verif = verifierIdTokenGoogle($data['credential']);
    if (!$verif['success']) {
        echo json_encode($verif);
        return;
    }
    $user = user::findByGoogleEmail($verif['email']);
    if ($user->getId() == 0) {
        echo json_encode(array('success' => 0, 'message' => "Aucun compte CRM n'est lié à ce compte Google. Connectez-vous avec votre login habituel puis liez votre compte Google depuis votre profil."));
        return;
    }
    $user->setConnected(true);
    $_SESSION['user'] = $user;
    echo json_encode(array('success' => 1));
}

// Lie le compte Google au compte CRM de l'utilisateur ACTUELLEMENT connecté (jamais utilisé pour
// se connecter la première fois - action volontaire depuis un profil déjà authentifié).
function googleLink($data)
{
    header('Content-Type: application/json');
    if (!isset($_SESSION['user']) || !$_SESSION['user']->isConnected()) {
        echo json_encode(array('success' => 0, 'message' => 'Vous devez être connecté.'));
        return;
    }
    if (empty($data['credential'])) {
        echo json_encode(array('success' => 0, 'message' => 'Requête invalide.'));
        return;
    }
    $verif = verifierIdTokenGoogle($data['credential']);
    if (!$verif['success']) {
        echo json_encode($verif);
        return;
    }
    $dejaLie = user::findByGoogleEmail($verif['email']);
    if ($dejaLie->getId() != 0 && $dejaLie->getId() != $_SESSION['user']->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Ce compte Google est déjà lié à un autre compte CRM.'));
        return;
    }
    $_SESSION['user']->linkGoogleAccount($verif['email']);
    echo json_encode(array('success' => 1, 'google_email' => $verif['email']));
}

function googleUnlink()
{
    header('Content-Type: application/json');
    if (!isset($_SESSION['user']) || !$_SESSION['user']->isConnected()) {
        echo json_encode(array('success' => 0, 'message' => 'Vous devez être connecté.'));
        return;
    }
    $_SESSION['user']->unlinkGoogleAccount();
    echo json_encode(array('success' => 1));
}
?>