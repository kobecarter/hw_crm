<?php
/* -------------------------------- installation -------------------------------- */
function install_com_assistant()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("assistant_tache")
		->column("id_agence", "INT NULL")
		->column("id_client", "INT NULL")
		->column("type", "VARCHAR(50) NULL")
		->column("titre", "VARCHAR(250) NULL")
		->column("date_tache", "DATETIME NULL")
		->column("remarque", "TEXT NULL")
		->column("termine", "INT NULL")
		->column("date_add", "DATETIME NULL")
		->column("last_edit", "DATETIME NULL")
		->create();

	$result2 = $install->init()->module("com_assistant")->addPermissions();
	if ($result1 && $result2) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_assistant()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("assistant_tache")->drop();
	$result2 = $desinstall->init()->module("com_assistant")->revokePermissions();
	if ($result1 && $result2) {
		return 1;
	} else {
		return 0;
	}
}
