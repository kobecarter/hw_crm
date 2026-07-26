<?php
/* -------------------------------- installation -------------------------------- */
function install_com_service()
{    
	$install = new installation();    
	
	$result1 = $install->init()
		->table("service")
		->column("id_categorie", "INT NULL")        
		->column("prix", "DOUBLE NULL")  
		->column("ordre", "INT NULL")  
		->column("active","INT(3) NULL")        
		->column("date_add", "DATETIME NULL")        
		->column("last_edit", "DATETIME NULL")        
		->create();    
	
	$result2 = $install->init()        
		->table("details_service")        
		->column("id_service","INT NULL")        
		->column("titre", "VARCHAR(250) NULL")        
		->column("description", "text NULL")  
		->column("intervenant", "VARCHAR(250) NULL")    
		->column("unite", "VARCHAR(250) NULL")    
		->column("langue", "VARCHAR(3) NULL")        
		->create();    
	
	$result3 = $install->init()->module("com_service")->addPermissions();    
	
	if($result1 && $result2 && $result3)
	{        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_service()
{    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("service")->drop();    

	$result2 = $desinstall->init()->table("details_service")->drop();    
	$result3 = $desinstall->init()->module("com_service")->revokePermissions();    
	if($result1 && $result2 && $result3)
	{        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}