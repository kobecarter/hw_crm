<?php

/* -------------------------------- installation -------------------------------- */
function install_com_lang(){
    global $db;
    $createSQL = "CREATE TABLE IF NOT EXISTS ".__prefixe_db__."langue (".
        "id INT NOT NULL auto_increment,".
        "nom VARCHAR(30) NOT NULL,".
        "code VARCHAR(3) NULL,".
        "flag VARCHAR(200) NULL,".
        "defaut INT NULL,".
        "actif INT NULL,".
        "date_add DATE NULL,".
        "last_edit DATE NULL,".
        "PRIMARY KEY(id)".
        ")";

    $droit1 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_lang', 'view')";
    $droit2 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_lang', 'add')";
    $droit3 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_lang', 'edit')";
    $droit4 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_lang', 'delete')";

    if(!$db->query($createSQL) && !$db->query($droit1) && !$db->query($droit2) && !$db->query($droit3) && !$db->query($droit4)){
        if(!is_dir("../images/langues")){
            mkdir("../images/langues");
        }
        return 1;
    }else{
        return 0;
    }
}

/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_lang(){
    global $db;
    $dropSQL = "DROP TABLE IF EXISTS ".__prefixe_db__."langue";
    $deleteSQL = "DELETE FROM ".__prefixe_db__."droits WHERE module = 'com_lang'";
    if(!$db->query($dropSQL) && !$db->query($deleteSQL)){
        if(is_dir("../../../../images/langues")){
            rmdir_recursive("../../../../images/langues");
        }
        return 1;
    }else{
        return 0;
    }
}