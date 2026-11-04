<?php

if ($_SESSION['user']->isSuperUser()) {
    $logs = activityLog::findAll($_SESSION['agence']);
    include_once("components/com_journal/views/journal/list.php");
} else {
    show404Error("404");
}
