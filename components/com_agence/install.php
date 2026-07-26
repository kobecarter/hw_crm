<?php
/* -------------------------------- installation -------------------------------- */
function install_com_agence()
{    
	$install = new installation();    
	
	$result1 = $install->init()
		->table("agence")
		->column("logo", "VARCHAR(250) NULL")        
		->column("email", "VARCHAR(250) NULL")   
		->column("tel", "VARCHAR(250) NULL")   
		->column("tel2", "VARCHAR(250) NULL")   
		->column("fax", "VARCHAR(250) NULL")   
		->column("numero_increment_facture", "INT NULL")   
		->column("numero_increment_devis", "INT NULL") 
		->column("tva", "INT NULL")   
		->column("website", "VARCHAR(250) NULL") 
		->column("if", "INT NULL") 
		->column("tp", "INT NULL") 
		->column("rc", "INT NULL") 
		->column("ice", "INT NULL") 
		->column("date_add", "DATETIME NULL")        
		->column("last_edit", "DATETIME NULL")        
		->create();    
	
	$result2 = $install->init()        
		->table("details_agence")        
		->column("id_agence","INT NULL")        
		->column("nom", "VARCHAR(250) NULL") 
		->column("adresse", "VARCHAR(250) NULL")          
		->column("ville", "VARCHAR(250) NULL")  
		->column("fonction", "VARCHAR(250) NULL")  
		->column("condition_de_paiement", "VARCHAR(250) NULL") 
		->column("conditions", "TEXT NULL")    
		->column("langue", "VARCHAR(3) NULL")     
		->create();    
	
	$result3 = $install->init()->module("com_agence")->addPermissions();    
	
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
function desinstall_com_agence()
{    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("agence")->drop();    

	$result2 = $desinstall->init()->table("details_agence")->drop();    
	$result3 = $desinstall->init()->module("com_agence")->revokePermissions();    
	if($result1 && $result2 && $result3)
	{        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}