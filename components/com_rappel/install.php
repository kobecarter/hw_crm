<?php
/* -------------------------------- installation -------------------------------- */
function install_com_rappel()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("rappel")
		->column("id_client", "INT NULL")
		->column("type", "VARCHAR(250) NULL")
		->column("domaine", "VARCHAR(250) NULL")
		->column("date_expir", "DATE NULL")
		->column("remarque", "TEXT NULL")
		->column("date_add", "DATE NULL")
		->column("last_edit", "DATE NULL")
		->column("archived", "INT NULL")
		->create();

	$result2 = $install
		->init()
		->table("relances")
		->column("id_rappel", "INT NULL")
		->column("date_send", "DATETIME NULL")
		->column("message", "TEXT NULL")
		->create();

	$result3 = $install->init()->module("com_rappel")->addPermissions();
	if ($result1 && $result2 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_rappel()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("rappel")->drop();
	$result2 = $desinstall->init()->table("relances")->drop();
	$result3 = $desinstall->init()->module("com_rappel")->revokePermissions();
	if ($result1 && $result2 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
