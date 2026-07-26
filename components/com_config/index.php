<?php

@$task = $_GET['task'];
switch ($task)
{
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_config')) {
            $config = new config($db, $_SESSION['langue']);
			$action = "components/com_config/controleurs/router.php?task=updateConfig";
			$submitValue = "Mettre à jour";
            include_once("components/com_config/views/config/form.php");
        }
        break;
}