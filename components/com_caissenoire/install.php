<?php
/* -------------------------------- installation -------------------------------- */
function install_com_caissenoire(){
	$install = new installation();
	$result1 = $install
		->init()
		->table("caissenoire")
		->column("id_utilisateur","INT NULL")
		->column("titre","VARCHAR(250) NULL")
		->column("description","TEXT NULL")
		->column("montant","DOUBLE NULL")
		->column("date_charge", "DATE NULL")
		->column("refunded","INT NULL")
		->column("date_remboursement", "DATE NULL")
		->column("justificatif", "VARCHAR(250) NULL")
		->column("remarque","TEXT NULL")
		->column("id_user_added","INT NULL")
		->column("id_user_edited","INT NULL")
		->column("date_add", "DATE NULL")
		->column("last_edit", "DATE NULL")
		->create();

	$result2 = $install->init()->module("com_caissenoire")->addPermissions();
	if($result1 && $result2){
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_caissenoire(){
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("caissenoire")->drop();
	$result2 = $desinstall->init()->module("com_caissenoire")->revokePermissions();
	if($result1 && $result2){
		return 1;
	} else {
		return 0;
	}
}
