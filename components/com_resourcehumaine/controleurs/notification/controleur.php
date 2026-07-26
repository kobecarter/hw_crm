<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case "markAsRead":
            markAsRead($_POST);
            break;
    }
}

function markAsRead($data){
    $indices = array("id");
    if (validateNotification($data, $indices)) {
        $notification = notification::find($data['id']);
        if((!$_SESSION['user']->isResourceHumaine() && $notification->getUser()->getId() == 0) || ($_SESSION['user']->isResourceHumaine() && $notification->getUser()->getId() == $_SESSION['user']->getId() && $notification->getTypeUser() == 'resourcehumaine')){
            $notification->setIsRead(1);
            if($notification->edit() == 1){
                echo "1";
            } else {
                echo "2";
            }
        }else {
            echo "3";
        }
    } else {
        echo "0";
    }
}

function validateNotification($data = array(), $indices = array())
{
    foreach ($indices as $indice) {
        if ($indice == "file") {
            if (!isset($_FILES["file"]) || $_FILES["file"]["name"][0] == "") {
                return false;
            }
        } else {
            if (!isset($data[$indice]) || empty($data[$indice])) {
                return false;
            }
        }
    }
    return true;
}
