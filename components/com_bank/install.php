<?php
/* -------------------------------- installation -------------------------------- */
function install_com_bank()
{    
	$install = new installation();    
	
	$result1 = $install->init()
		->table("bank")
		->column("raison_sociale", "VARCHAR(250) NULL")        
		->column("siege_social", "VARCHAR(250) NULL")   
		->column("numero_registre_commerce", "VARCHAR(250) NULL")   
		->column("ice", "VARCHAR(250) NULL")   
		->column("rib", "VARCHAR(250) NULL")   
		->column("code_swift", "VARCHAR(250) NULL")   
		->column("date_add", "DATETIME NULL")        
		->column("last_edit", "DATETIME NULL")        
		->create();    
	
	$result3 = $install->init()->module("com_bank")->addPermissions();    
	
	if($result1 && $result3)
	{        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_bank()
{    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("bank")->drop();    

	$result3 = $desinstall->init()->module("com_bank")->revokePermissions();    
	if($result1 && $result3)
	{        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}