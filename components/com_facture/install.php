<?php
/* -------------------------------- installation -------------------------------- */
function install_com_facture()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("facture")
		->column("numero", "VARCHAR(20) NULL")
		->column("id_client", "INT NULL")
		->column("date_facture", "DATE NULL")
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
		->column("avoir", "INT NULL")
		->column("archived", "INT NULL")
		->column("id_facture", "INT NULL")
		->create();
	$result2 = $install
		->init()
		->table("item_facture")
		->column("id_facture", "INT NULL")
		->column("id_service", "INT NULL")
		->column("qte", "INT NULL")
		->column("prix", "DOUBLE NULL")
		->column("total", "DOUBLE NULL")
		->column("unite", "VARCHAR(100) NULL")
		->column("titre", "VARCHAR(250) NULL")
		->column("description", "TEXT NULL")
		->column("ordre", "INT NULL")
		->create();

	$result3 = $install
		->init()
		->table("payment")
		->column("id_facture", "INT NULL")
		->column("montant", "DOUBLE NULL")
		->column("date_payment", "DATE NULL")
		->column("methode_payment", "VARCHAR(20) NULL")
		->column("date_validation", "DATE NULL")
		->column("detail", "VARCHAR(250) NULL")
		->column("date_add", "DATETIME NULL")
		->column("last_edit", "DATETIME NULL")
		->create();

	$result4 = $install->init()->module("com_facture")->addPermissions();

	if ($result1 && $result2 && $result3 && $result4) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_localisation()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("facture")->drop();
	$result2 = $desinstall->init()->table("item_facture")->drop();
	$result2 = $desinstall->init()->table("payment")->drop();
	$result3 = $desinstall->init()->module("com_facture")->revokePermissions();
	if ($result1 && $result2 && $result3) {
		return 1;
	} else {
		return 0;
	}
}
