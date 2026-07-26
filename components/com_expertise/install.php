<?php
/* -------------------------------- installation -------------------------------- */
function install_com_expertise()
{
	$install = new installation();

	$result1 = $install->init()
		->table("expertise")
		->column("slug", "VARCHAR(100) NULL")
		->column("id_parent", "INT NULL")
		->column("photo", "VARCHAR(250) NULL")
		->column("photo_banniere", "VARCHAR(250) NULL")
		->column("ordre", "INT NULL")
		->column("active", "INT(3) NULL")
		->column("date_add", "DATE NULL")
		->column("last_edit", "DATE NULL")
		->create();

	$result2 = $install->init()
		->table("details_expertise")
		->column("id_expertise", "INT NULL")
		->column("titre", "VARCHAR(250) NULL")
		->column("sous_titre", "VARCHAR(250) NULL")
		->column("texte_accueil", "text NULL")
		->column("extrait", "text NULL")
		->column("texte", "text NULL")
		->column("langue", "VARCHAR(3) NULL")
		->create();

	$result3 = $install->init()->module("com_expertise")->addPermissions();

	if ($result1 && $result2 && $result3) {
		$install->init()->file("expertises", "images")->fileCreate();
		return 1;
	} else {
		return 0;
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_expertise()
{
	$desinstall = new installation();
	$result1 = $desinstall->init()->table("expertise")->drop();

	$result2 = $desinstall->init()->table("details_expertise")->drop();
	$result3 = $desinstall->init()->module("com_expertise")->revokePermissions();
	if ($result1 && $result2 && $result3) {
		$desinstall->init()->file("expertises", "images")->fileRemove();
		return 1;
	} else {
		return 0;
	}
}
