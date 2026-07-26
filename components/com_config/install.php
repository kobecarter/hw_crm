<?php

/* -------------------------------- installation -------------------------------- */
function install_com_config()
{
    global $db;

    $createSQL = "CREATE TABLE IF NOT EXISTS " . __prefixe_db__ . "config (" .
        "id INT NOT NULL," .
        "logo VARCHAR(255) NULL," .
        "email VARCHAR(200) NULL," .
        "tel VARCHAR(200) NULL," .
        "tel2 VARCHAR(200) NULL," .
        "fax VARCHAR(200) NULL," .
        "x VARCHAR(30) NULL," .
        "y VARCHAR(30) NULL," .
        "id_slider INT NULL," .
        "facebook VARCHAR(255) NULL," .
        "twitter VARCHAR(255) NULL," .
        "gplus VARCHAR(255) NULL," .
        "youtube VARCHAR(255) NULL," .
        "instagram VARCHAR(255) NULL," .
        "pinterest VARCHAR(255) NULL," .
        "linkedin VARCHAR(255) NULL," .
        "snapshat VARCHAR(255) NULL," .
        "tumblr VARCHAR(255) NULL," .
        "viadeo VARCHAR(255) NULL," .
        "analytic TEXT NULL," .
        "condition_paiment TEXT NULL," .
        "PRIMARY KEY(id)" .
        ")";

    $createSQL2 = "CREATE TABLE IF NOT EXISTS " . __prefixe_db__ . "details_config (" .
        "id_config INT NOT NULL," .
        "nom VARCHAR(200) NULL," .
        "titre VARCHAR(200) NULL," .
        "description VARCHAR(250) NULL," .
        "adresse VARCHAR(250) NULL," .
        "langue VARCHAR(3) NULL" .
        ")";

    $droit1 = "INSERT INTO " . __prefixe_db__ . "droits (id_profil, module, action) VALUES (1, 'com_config', 'view')";
    $droit2 = "INSERT INTO " . __prefixe_db__ . "droits (id_profil, module, action) VALUES (1, 'com_config', 'add')";
    $droit3 = "INSERT INTO " . __prefixe_db__ . "droits (id_profil, module, action) VALUES (1, 'com_config', 'edit')";
    $droit4 = "INSERT INTO " . __prefixe_db__ . "droits (id_profil, module, action) VALUES (1, 'com_config', 'delete')";

    $insertSQL = "INSERT INTO " . __prefixe_db__ . "config(id) VALUES (0)";

    if (!$db->query($createSQL) && !$db->query($createSQL2) && !$db->query($droit1) && !$db->query($droit2) && !$db->query($droit3) && !$db->query($droit4)) {
        if (!$db->query($insertSQL)) {
            if (!is_dir("../images/config")) {
                mkdir("../images/config");
            }
            return 1;
        }
        return 2;
    } else {
        return 0;
    }
}

/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_config()
{
    global $db;
    $dropSQL = "DROP TABLE IF EXISTS " . __prefixe_db__ . "config";
    $deleteSQL = "DELETE FROM " . __prefixe_db__ . "droits WHERE module = 'com_config'";
    if (!$db->query($dropSQL) && !$db->query($deleteSQL)) {
        if (is_dir("../../../../images/config")) {
            rmdir_recursive("../../../../images/config");
        }
        return 1;
    } else {
        return 0;
    }
}
