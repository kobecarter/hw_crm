<?php
/* -------------------------------- installation -------------------------------- */
function install_com_resourcehumaine(){    
	$install = new installation();    
	$result1 = $install->init()        
		->table("resourcehumaine")  
		->column("reference","VARCHAR(100) NULL") 
		->column("reference_pointage","VARCHAR(100) NULL") 
		->column("id_agency","INT NOT NULL") 
		->column("cin","VARCHAR(100) NULL") 
		->column("firstname","VARCHAR(100) NULL")  
		->column("lastname","VARCHAR(100) NULL")  
		->column("email","VARCHAR(50) NULL")  
		->column("phone","VARCHAR(50) NULL")  
		->column("address","VARCHAR(50) NULL")  
		->column("city","VARCHAR(50) NULL")
		->column("prospecting_source","VARCHAR(255) NULL")
		->column("function","VARCHAR(250) NULL") 
		->column("status","VARCHAR(250) NULL") 
		->column("start_date","DATE NULL")
		->column("contract_signing_date","DATE NULL")
		->column("end_date","DATE NULL")        
		->column("photo","VARCHAR(250) NULL")   
		->column("remark","TEXT NULL")        
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result2 = $install        
		->init()        
		->table("fileresourcehumaine")        
		->column("id_resourcehumaine","INT NULL") 
		->column("title","VARCHAR(250) NULL")           
		->column("file","VARCHAR(250) NULL")            
		->create();      
	$result3 = $install->init()        
		->table("absence") 
		->column("id_resourcehumaine","INT NULL")  
		->column("number_of_days","DOUBLE NULL") 
		->column("nature_of_absence","INT NULL") 
		->column("start_date","DATE NULL")
		->column("end_date","DATE NULL")   
		->column("status","INT NULL")      
		->column("remark","TEXT NULL")     	
		->column("justification","VARCHAR(250) NULL")      
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result4 = $install->init()        
		->table("bonus") 
		->column("id_resourcehumaine","INT NULL")  
		->column("amount","DOUBLE NULL") 
		->column("date","DATE NULL")
		->column("status","INT NULL")      
		->column("remark","TEXT NULL")     	
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result5 = $install->init()        
		->table("pointage") 
		->column("id_resourcehumaine","INT NULL")  
		->column("date","DATE NULL") 
		->column("hour1","TIME NULL") 
		->column("hour2","TIME NULL")
		->column("hour3","TIME NULL")   
		->column("hour4","TIME NULL")      
		->column("delay","VARCHAR(250) NULL")  
		->column("overtime","VARCHAR(250) NULL")     	
		->column("date_add", "DATE NULL")        
		->column("last_edit", "DATE NULL")        
		->create();
	$result6 = $install        
		->init()        
		->table("payslip")        
		->column("id_resourcehumaine","INT NULL") 
		->column("title","VARCHAR(250) NULL") 
		->column("date","DATE NULL") 
		->column("file","VARCHAR(250) NULL")            
		->create();
	$result7 = $install        
		->init()        
		->table("request")        
		->column("id_resourcehumaine","INT NULL") 
		->column("title","VARCHAR(250) NULL")
		->column("description","TEXT NULL")
		->column("response","TEXT NULL") 
		->column("status","INT NULL")   
		->column("date_add", "DATE NULL")        
		->column("date_edit", "DATE NULL") 
		->create();
	$result8 = $install        
		->init()        
		->table("notification")        
		->column("user_id","INT NULL") 
		->column("type_user","VARCHAR(250) NULL")
		->column("title","VARCHAR(250) NULL")
		->column("message","TEXT NULL")
		->column("type_message","VARCHAR(250) NULL")
		->column("class","VARCHAR(250) NULL")
		->column("data","VARCHAR(250) NULL")
		->column("is_read","INT NULL")   
		->column("date_add", "DATE NULL")        
		->column("date_edit", "DATE NULL") 
		->create();
	$result9 = $install->init()->module("com_resourcehumaine")->addPermissions();    
	if($result1 && $result2 && $result3 && $result4 &&  $result5 &&  $result6 && $result7 && $result8 && $result9){        
		$install->init()->file("resourceshumaines", "images")->fileCreate(); 
		$install->init()->file("files", "images/resourceshumaines")->fileCreate();
		$install->init()->file("absences", "images/resourceshumaines")->fileCreate(); 
		$install->init()->file("payslips", "images/resourceshumaines")->fileCreate();
		return 1;    
	} else {        
		return 0;    
	}
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_resourcehumaine(){    
	$desinstall = new installation();    
	$result1 = $desinstall->init()->table("resourcehumaine")->drop();
	$result2 = $desinstall->init()->table("fileresourcehumaine")->drop();    
	$result3 = $desinstall->init()->table("absence")->drop();   
	$result4 = $desinstall->init()->table("bonus")->drop(); 
	$result5 = $desinstall->init()->table("pintage")->drop();
	$result6 = $desinstall->init()->table("payslip")->drop();
	$result7 = $desinstall->init()->table("request")->drop();
	$result8 = $desinstall->init()->table("notification")->drop();
	$result9 = $desinstall->init()->module("com_resourcehumaine")->revokePermissions();    
	if($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7 && $result8 && $result9){        
		return 1;    
	} else {        
		return 0;    
	}
}