<?php
/* -------------------------------- installation -------------------------------- */
function install_com_charge(){    
	$install = new installation();    
	$result1 = $install        
		->init()        
		->table("charge") 
		->column("id_agence","INT NULL")  
		->column("id_user","INT NULL")        
		->column("type","VARCHAR(20) NULL") 
		->column("titre","VARCHAR(250) NULL") 
		->column("description","TEXT NULL")  
		->column("total","DOUBLE NULL")
		->column("devise","VARCHAR(20) NULL")
		->column("paid","INT NULL")
		->column("facture","INT NULL")
		->column("date_charge", "DATE NULL") 
		->column("date_payment", "DATE NULL") 
		->column("mode_payment", "VARCHAR(20) NULL") 
		->column("photo", "VARCHAR(250) NULL") 
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();    
		
	$result2 = $install->init()->module("com_charge")->addPermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_charge(){    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("charge")->drop();    
	$result2 = $desinstall->init()->module("com_charge")->revokePermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}