<?php
/* -------------------------------- installation -------------------------------- */
function install_com_devis()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("devis")
		->column("numero", "VARCHAR(20) NULL")
		->column("id_client", "INT NULL")
		->column("date_devis", "DATE NULL")
		->column("total", "DOUBLE NULL")
		->column("statu", "INT(3) NULL")
		->column("devise", "VARCHAR(10) NULL")
		->column("discount", "VARCHAR(20) NULL")
		->column("discount_val", "DOUBLE NULL")
		->column("condition_paiment", "TEXT NULL")
		->column("remarque", "TEXT NULL")
		->column("langue", "VARCHAR(5) NULL")
		->column("date_add", "DATETIME NULL")
		->column("last_edit", "DATETIME NULL")
		->create();
	$result2 = $install
		->init()
		->table("item_devis")
		->column("id_devis", "INT NULL")
		->column("id_service", "INT NULL")
		->column("qte", "INT NULL")
		->column("prix", "DOUBLE NULL")
		->column("total", "DOUBLE NULL")
		->column("unite", "VARCHAR(100) NULL")
		->column("titre", "VARCHAR(250) NULL")
		->column("description", "TEXT NULL")
		->column("ordre", "INT NULL")
		->create();

	$result3 = $install->init()->module("com_devis")->addPermissions();

	if ($result1 && $result2 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_localisation()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("devis")->drop();
	$result2 = $desinstall->init()->table("item_devis")->drop();
	$result3 = $desinstall->init()->module("com_devis")->revokePermissions();
	if ($result1 && $result2 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
