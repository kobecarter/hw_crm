<?php
/* -------------------------------- installation -------------------------------- */
// Pas de table CRM à créer : les points vivent dans hw_points_client, côté
// site (voir classes/fidelite.php) — ce module n'ajoute que les permissions.
function install_com_fidelite(){
	$install = new installation();
	$result = $install->init()->module("com_fidelite")->addPermissions();
	if($result){
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_fidelite(){
	$desinstall = new installation();
	$result = $desinstall->init()->module("com_fidelite")->revokePermissions();
	if($result){
		return 1;
	} else {
		return 0;
	}
}
