<?php
/* -------------------------------- installation -------------------------------- */
function install_com_accounting(){    
	$install = new installation();    
	$result1 = $install->init()        
		->table("cnss") 
		->column("id_agence","INT NULL")  
		->column("amount","DOUBLE NULL") 
		->column("increasion","DOUBLE NULL") 
		->column("date","DATE NULL")
		->column("status","INT NULL")      
		->column("remark","TEXT NULL")     
		->column("justification","VARCHAR(250) NULL")    	
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result2 = $install->init()        
		->table("tva") 
		->column("id_agence","INT NULL")  
		->column("amount","DOUBLE NULL") 
		->column("increasion","DOUBLE NULL")
		->column("date","DATE NULL")
		->column("status","INT NULL")      
		->column("remark","TEXT NULL")     
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result3 = $install->init()        
		->table("bilan") 
		->column("id_agence","INT NULL")  
		->column("date_of_depot","DATE NULL")
		->column("year","VARCHAR(255) NULL") 
		->column("amount","DOUBLE NULL") 
		->column("increasion","DOUBLE NULL")
		->column("date","DATE NULL")
		->column("status","INT NULL")      
		->column("remark","TEXT NULL")     
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result4 = $install->init()        
		->table("taxeprofessionnelle") 
		->column("id_agence","INT NULL")  
		->column("date_of_depot","DATE NULL")
		->column("year","VARCHAR(255) NULL") 
		->column("amount","DOUBLE NULL") 
		->column("increasion","DOUBLE NULL")
		->column("date","DATE NULL")
		->column("status","INT NULL")      
		->column("remark","TEXT NULL")     
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result5 = $install->init()->module("com_accounting")->addPermissions();    
	if($result1 && $result2 && $result3 && $result4 && $result5){        
		$install->init()->file("cnss", "images")->fileCreate(); 
		$install->init()->file("tva", "images")->fileCreate(); 
		return 1;    
	} else {        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_accounting(){    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("cnss")->drop();
	$result2 = $desinstall->init()->table("tva")->drop();
	$result3 = $desinstall->init()->table("bilan")->drop();
	$result4 = $desinstall->init()->table("taxeprofessionnelle")->drop();
	$result5 = $desinstall->init()->module("com_accounting")->revokePermissions();    
	if($result1 && $result2 && $result3 && $result4 && $result5){        
		return 1;    
	} else {        
		return 0;    
	}
}