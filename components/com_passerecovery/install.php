<?php

/* -------------------------------- installation -------------------------------- */
function install_com_passerecovery()
{
    $install = new installation();
    $result1 = $install
        ->init()
        ->table("passerecovery")
        ->column("id_client", "INT NOT NULL")
        ->column("code", "VARCHAR(255) NOT NULL")
        ->column("date_add", "DATETIME NOT NULL")
        ->column("last_edit", "DATETIME NOT NULL")
        ->create();

    $result3 = $install->init()->module("com_passerecovery")->addPermissions();

    if ($result1 && $result3) {
        return 1;
    } else {
        return 0;
    }
}
/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_localisation()
{
    $desinstall = new installation();
    $result1 = $desinstall->init()->table("passerecovery")->drop();
    $result3 = $desinstall->init()->module("com_passerecovery")->revokePermissions();
    if ($result1 && $result3) {
        return 1;
    } else {
        return 0;
    }
}
