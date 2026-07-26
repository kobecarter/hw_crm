<?php

/* -------------------------------- installation -------------------------------- */
function install_com_users(){
    global $db;
    $createSQL = "CREATE TABLE IF NOT EXISTS ".__prefixe_db__."users (".
        "id int(11) NOT NULL auto_increment,".
        "login varchar(255) NOT NULL,".
        "password varchar(255) NOT NULL,".
        "prenom varchar(255) NULL,".
        "nom varchar(255) NULL,".
        "email varchar(255) NOT NULL,".
        "tel varchar(255) NULL,".
        "adresse varchar(255) NULL,".
        "langue varchar(3) NULL,".
        "su int(11) NOT NULL,".
        "id_profil int(11) NOT NULL,".
        "actif int(11) NULL,".
        "PRIMARY KEY(id)".
        ")";

    $createSQL2 = "CREATE TABLE IF NOT EXISTS ".__prefixe_db__."profils (".
        "id int(11) NOT NULL auto_increment,".
        "profil varchar(100) NOT NULL,".
        "PRIMARY KEY(id)".
        ")";

    $createSQL3 = "CREATE TABLE IF NOT EXISTS ".__prefixe_db__."droits (".
        "id int(11) NOT NULL auto_increment,".
        "id_profil int(11) NOT NULL,".
        "module varchar(20) NOT NULL,".
        "action varchar(10) NOT NULL,".
        "PRIMARY KEY(id)".
        ")";

    $droit1 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_users', 'view')";
    $droit2 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_users', 'add')";
    $droit3 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_users', 'edit')";
    $droit4 = "INSERT INTO ".__prefixe_db__."droits (id_profil, module, action) VALUES (1, 'com_users', 'delete')";

    if(!$db->query($createSQL) && !$db->query($createSQL2) && !$db->query($createSQL3)){
        if(!$db->query($droit1) && !$db->query($droit2) && !$db->query($droit3) && !$db->query($droit4)){
            return 1;
        }
        return 2;
    }else{
        return 0;
    }
}

/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_users(){
    global $db;
    $dropSQL = "DROP TABLE IF EXISTS ".__prefixe_db__."users";
    $dropSQL2 = "DROP TABLE IF EXISTS ".__prefixe_db__."profils";
    $dropSQL3 = "DROP TABLE IF EXISTS ".__prefixe_db__."droits";
    $deleteSQL = "DELETE FROM ".__prefixe_db__."droits WHERE module = 'com_users'";
    if(!$db->query($dropSQL) && !$db->query($dropSQL2) && !$db->query($dropSQL3) && !$db->query($deleteSQL)){
        return 1;
    }else{
        return 0;
    }
}