<?php
/* -------------------------------- installation -------------------------------- */
function install_com_fournisseur(){    
	$install = new installation();    
	$result1 = $install        ->init()        
		->table("fournisseur")        
		->column("titre","VARCHAR(10) NULL") 
		->column("prenom","VARCHAR(100) NULL") 
		->column("nom","VARCHAR(100) NULL")  
		->column("raison_social","VARCHAR(100) NULL")  
		->column("ice","VARCHAR(50) NULL")  
		->column("tel","VARCHAR(50) NULL")  
		->column("tel2","VARCHAR(50) NULL")  
		->column("tel3","VARCHAR(50) NULL")  
		->column("email","VARCHAR(250) NULL") 
		->column("password","VARCHAR(250) NULL")
		->column("cp","VARCHAR(50) NULL")        
		->column("adresse","VARCHAR(250) NULL") 
		->column("adresse2","VARCHAR(250) NULL") 
		->column("ville","VARCHAR(100) NULL") 
		->column("region","VARCHAR(100) NULL") 
		->column("pays","VARCHAR(100) NULL") 
		->column("photo","VARCHAR(250) NULL") 
		->column("active","INT(3) NULL")        
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();    
	
	$result2 = $install->init()->module("com_fournisseur")->addPermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_fournisseur(){    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("fournisseur")->drop();    
	$result2 = $desinstall->init()->module("com_fournisseur")->revokePermissions();    
	if($result1 && $result2){        
		return 1;    
	} else {        
		return 0;    
	}
}