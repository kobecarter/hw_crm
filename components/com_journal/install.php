<?php
/* -------------------------------- installation -------------------------------- */
function install_com_journal()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("log")
		->column("id_agence", "INT NULL")
		->column("id_user", "INT NULL")
		->column("entite", "VARCHAR(50) NOT NULL")
		->column("id_entite", "INT NULL")
		->column("action", "VARCHAR(20) NOT NULL")
		->column("description", "VARCHAR(255) NOT NULL")
		->column("ip", "VARCHAR(45) NULL")
		->column("date_add", "DATETIME NULL")
		->create();

	if ($result1) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_journal()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("log")->drop();
	if ($result1) {
		return 1;
	} else {
		return 0;
	}
}
