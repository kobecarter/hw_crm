<?php
/* -------------------------------- installation -------------------------------- */
function install_com_temoignage()
{    
	$install = new installation();    
	
	$result1 = $install->init()
		->table("temoignage")             
		->column("photo","VARCHAR(250) NULL")        
		->column("active","INT(3) NULL")        
		->column("ordre", "INT NULL")        
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();    
	
	$result2 = $install->init()        
		->table("details_temoignage")        
		->column("id_temoignage","INT NULL")        
		->column("nom", "VARCHAR(250) NULL")        
		->column("fonction", "VARCHAR(250) NULL")        
		->column("email","VARCHAR(250) NULL")
		->column("sujet","VARCHAR(250) NULL")
		->column("temoignage","text NULL")      
		->column("langue", "VARCHAR(3) NULL")        
		->create();    
	
	$result3 = $install->init()->module("com_temoignage")->addPermissions();    
	
	if($result1 && $result2 && $result3)
	{        
		$install->init()->file("temoignages", "images")->fileCreate();        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_temoignage()
{    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("temoignage")->drop();    

	$result2 = $desinstall->init()->table("details_temoignage")->drop();    
	$result3 = $desinstall->init()->module("com_temoignage")->revokePermissions();    
	if($result1 && $result2 && $result3)
	{        
		$desinstall->init()->file("temoignages", "images")->fileRemove();        
		return 1;    
	} 
	else 
	{        
		return 0;    
	}
}