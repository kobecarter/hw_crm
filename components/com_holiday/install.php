<?php
/* -------------------------------- installation -------------------------------- */
function install_com_holiday()
{
	$install = new installation();
	$result1 = $install
		->init()
		->table("holiday")
		->column("name", "VARCHAR(250) NULL")
		->column("start_date", "DATE NULL")
		->column("end_date", "DATE NULL")
		->column("remarque", "TEXT NULL")
		->column("date_add", "DATE NULL")
		->column("last_edit", "DATE NULL")
		->create();

	$result2 = $install->init()->module("com_holiday")->addPermissions();
	if ($result1 && $result2) {
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_holiday()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("holiday")->drop();
	$result2 = $desinstall->init()->module("com_holiday")->revokePermissions();
	if ($result1 && $result2) {
		return 1;
	} else {
		return 0;
	}
}
