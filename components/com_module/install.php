<?php

/* -------------------------------- installation -------------------------------- */
function install_com_module(){
    global $db;
    $createSQL = "CREATE TABLE IF NOT EXISTS ".__prefixe_db__."modules (".
        "id INT NOT NULL auto_increment,".
        "id_module VARCHAR(100) NOT NULL,".
        "enabled INT NOT NULL,".
        "installed INT NOT NULL,".
        "nom VARCHAR(255) NULL,".
        "classe VARCHAR(30) NULL,".
        "nom_table VARCHAR(30) NULL,".
        "translated INT NULL,".
        "url INT NULL,".
        "system INT NULL,".
        "icon VARCHAR(20) NULL,".
        "positioned VARCHAR(6) NULL,".
        "PRIMARY KEY(id)".
        ")";

    $droit1 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_module', 'view')";
    $droit2 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_module', 'add')";
    $droit3 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_module', 'edit')";
    $droit4 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_module', 'delete')";

    if(!$db->query($createSQL) && !$db->query($droit1) && !$db->query($droit2) && !$db->query($droit3) && !$db->query($droit4)){
        return 1;
    }else{
        return 0;
    }
}

/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_module(){
    global $db;
    $dropSQL = "DROP TABLE IF EXISTS ".__prefixe_db__."modules";
    $deleteSQL = "DELETE FROM ".__prefixe_db__."droits WHERE module = 'com_module'";
    if(!$db->query($dropSQL) && !$db->query($deleteSQL)){
        return 1;
    }else{
        return 0;
    }
}