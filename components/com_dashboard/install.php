<?php

/* -------------------------------- installation -------------------------------- */
function install_com_dashboard(){

    $install = new installation();

    $result = $install->init()->module("com_dashboard")->addPermissions();

    if($result){
        return 1;
    } else {
        return 0;
    }
}

/* -------------------------------- désinstallation -------------------------------- */
function desinstall_com_dashboard(){

    $desinstall = new installation();

    $result = $desinstall->init()->module("com_dashboard")->revokePermissions();

    if($result){
        return 1;
    } else {
        return 0;
    }
}
