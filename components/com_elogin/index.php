<?php

@$task = $_GET['task'];
switch ($task)
{
    default :
		include_once("views/elogin/login.php");
        break;
}