<?php

@$task = $_GET['task'];
switch ($task) {
    default:
        if (!$_SESSION['user']->hasDroit('view', 'com_config')) {
            header('Location: index.php');
        }
        break;
}
