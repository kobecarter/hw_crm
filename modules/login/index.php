<?php
// Module Login
$option = @$_GET['option'];
if (!in_array($option,['com_login','com_elogin'])){
	// "Se rappeler de moi" : avant de rediriger vers le login faute de session active, tente une
	// reconnexion silencieuse via le cookie longue durée (30 jours) posé à la connexion - voir
	// userremembertoken.php pour le détail sécurité. Ne s'applique QUE si la session est
	// réellement absente/déconnectée (jamais pour écraser une session déjà active).
	if ((!isset($_SESSION['user']) || !$_SESSION['user']->isConnected()) && isset($_COOKIE[userremembertoken::COOKIE_NAME])) {
		$paireCookie = explode(':', $_COOKIE[userremembertoken::COOKIE_NAME], 2);
		if (count($paireCookie) === 2) {
			$idUserRappel = userremembertoken::verify($paireCookie[0], $paireCookie[1]);
			if ($idUserRappel) {
				$userRappel = user::find($idUserRappel);
				if ($userRappel->getId() != 0 && $userRappel->isActif()) {
					$userRappel->setConnected(true);
					$_SESSION['user'] = $userRappel;
				}
			}
		}
	}
	if (isset($_SESSION['user'])) {
		$user = $_SESSION['user'];
		if (!$user->isConnected()){
			header ("location:index.php?option=com_login");
			exit;
		}
	}
	else{
		header ("location:index.php?option=com_login");
		exit;
	}
}

// Déconnexion
if ($option == "doLogout"){
	if(isset($_SESSION['user'])){
		$user = $_SESSION['user'];
		$user->disconnect();
	}
	if(isset($_SESSION['client'])){
		$client = $_SESSION['client'];
		$client->disconnect();
	}
	session_destroy();
	header ("location:index.php?option=com_login");
	exit;
}
?>