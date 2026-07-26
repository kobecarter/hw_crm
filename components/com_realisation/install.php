<?php
/* -------------------------------- installation -------------------------------- */
function install_com_realisation()
{
	$install = new installation();
	$result1 = $install->init()
		->table("realisation")
		->column("ordre", "INT")
		->column("titre", "VARCHAR(100) NULL")
		->column("extrait", "TEXT NOT NULL")
		->column("texte", "TEXT NOT NULL")
		->column("photo", "VARCHAR(255) NULL")
		->column("url_project", "VARCHAR(255) NULL")
		->column("date_add", "DATE NULL")
		->create();

	$result2 = $install->init()->module("com_realisation")->addPermissions();
	if ($result1 && $result2) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_realisation()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("realisation")->drop();
	$result2 = $desinstall->init()->module("com_realisation")->revokePermissions();
	if ($result1 && $result2) {
		return 1;
	} else {
		return 0;
	}
}
