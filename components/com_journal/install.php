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

	// Journal des envois de devis/factures par email (date, destinataire, statut) - table
	// distincte de "log" ci-dessus : besoin de colonnes structurées (destinataire, statut) que
	// le schéma générique entite/action/description de activityLog ne porte pas.
	$result2 = $install
		->init()
		->table("envoi_email")
		->column("id_agence", "INT NULL")
		->column("id_user", "INT NULL")
		->column("entite", "VARCHAR(20) NOT NULL")
		->column("id_entite", "INT NULL")
		->column("numero", "VARCHAR(50) NULL")
		->column("id_client", "INT NULL")
		->column("destinataire", "VARCHAR(255) NULL")
		->column("type_envoi", "VARCHAR(30) NOT NULL")
		->column("statut", "VARCHAR(10) NOT NULL")
		->column("erreur", "VARCHAR(255) NULL")
		->column("date_envoi", "DATETIME NOT NULL")
		->create();

	if ($result1 || $result2) {
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
	$result2 = $desinstall->init()->table("envoi_email")->drop();
	if ($result1 || $result2) {
		return 1;
	} else {
		return 0;
	}
}
