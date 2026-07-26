<?php
/* -------------------------------- installation -------------------------------- */
function install_com_contract()
{    
	$install = new installation();    
	
	$result1 = $install->init()
		->table("contract")
		->column("id_facture", "INT NULL") 
		->column("date", "DATE NULL")  
		->column("nombre_de_paiement","INT NULL")           
		->column("date_add", "DATETIME NULL")        
		->column("last_edit", "DATETIME NULL")        
		->create();    
	
	$result2 = $install->init()        
		->table("details_contract")        
		->column("id_contract","INT NULL")   
		->column("titre", "VARCHAR(250) NULL") 
		->column("duration", "VARCHAR(250) NULL") 
		->column("ville", "VARCHAR(250) NULL")      
		->column("tribunal", "VARCHAR(250) NULL") 
		->column("texte", "TEXT NULL")  
		->column("langue", "VARCHAR(3) NULL")     
		->create();    
	
	$result3 = $install->init()->module("com_contract")->addPermissions();    
	
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
function desinstall_com_contract()
{    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("contract")->drop();    

	$result2 = $desinstall->init()->table("details_contract")->drop();    
	$result3 = $desinstall->init()->module("com_contract")->revokePermissions();    
	if($result1 && $result2 && $result3)
	{        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}