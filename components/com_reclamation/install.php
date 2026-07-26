<?php
/* -------------------------------- installation -------------------------------- */
function install_com_reclamation(){    
	$install = new installation();    
	$result1 = $install        ->init()        
		->table("reclamation")        
		->column("id_client","INT NOT NULL") 
		->column("sujet","VARCHAR(100) NULL") 
		->column("message","TEXT NULL")  
		->column("etat","INT(3) NULL")        
		->column("date_add", "DATE NULL")        
		->create();    
	
	$result2 = $install->init()->module("com_reclamation")->addPermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_reclamation(){    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("reclamation")->drop();    
	$result2 = $desinstall->init()->module("com_reclamation")->revokePermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}