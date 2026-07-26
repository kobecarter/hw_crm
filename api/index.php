<?php
require_once("../config.php");
require_once("../instanceDb.php");
require_once("../includes/functions/functions.php");

require 'vendor/autoload.php';

use App\Utils\Router;

$router = new Router();
$router->loadRoutes();
