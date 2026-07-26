<?php
/* -------------------------------- installation -------------------------------- */
function install_com_relance()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("relance")
		->column("id_client", "INT NULL")
		->column("type", "VARCHAR(250) NULL")
		->column("date", "DATETIME NULL")
		->column("remarque", "TEXT NULL")
		->column("date_add", "DATE NULL")
		->column("last_edit", "DATE NULL")
		->create();

	$result3 = $install->init()->module("com_relance")->addPermissions();
	if ($result1 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_relance()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("relance")->drop();
	$result3 = $desinstall->init()->module("com_relance")->revokePermissions();
	if ($result1 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
