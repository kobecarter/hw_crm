<?php
/* -------------------------------- installation -------------------------------- */
function install_com_cheque(){    
	$install = new installation();    
	$result1 = $install        
		->init()        
		->table("cheque") 
		->column("id_agence","INT NULL")  
		->column("check_number","VARCHAR(255) NULL") 
		->column("file","VARCHAR(255) NULL") 
		->column("date", "DATE NULL") 
		->column("beneficiary","VARCHAR(255) NULL")
		->column("amount","DOUBLE NULL")
		->column("currency","VARCHAR(255) NULL")
		->column("status","VARCHAR(255) NULL")
		->column("reason", "TEXT NULL") 
		->column("comment", "TEXT NULL") 
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();    
		
	$result2 = $install->init()->module("com_cheque")->addPermissions();    
	if($result1 && $result2){ 
		$install->init()->file("cheques", "images")->fileCreate();        
		return 1;    
	} else {        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_cheque(){    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("cheque")->drop();    
	$result2 = $desinstall->init()->module("com_cheque")->revokePermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}