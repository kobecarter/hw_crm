<?php
require_once("config.php");
require_once("instanceDb.php");
require_once('includes/functions/functions.php');

if (!isset($_SESSION)) {
    session_start();
}
if(!isset($_SESSION['langue']) || empty($_SESSION['langue'])){
    $_SESSION['langue'] = 'fr';
}
if(!isset($_SESSION['agence']) || empty($_SESSION['agence']) ){
    $agencesToTakeDefault = agence::findAll($_SESSION['langue']);
    $_SESSION['agence'] = $agencesToTakeDefault[0]->getId();
}
require_once('includes/traduction.php');
require_once('modules/login/index.php');

$option = isset($_GET['option']) ? $_GET['option'] : "com_dashboard";
$config = new config($db);
if (in_array($option, array('com_login', 'com_elogin'))) {
    include("includes/tpl/top-login.php");
} else if (isset($_SESSION['user']) && $_SESSION['user'] instanceof resourcehumaine) {
    // Espace self-service employé : shell dédié (verre/GSAP), jamais le sidebar admin.
    include("includes/tpl/employee-top.php");
} else {
    include("includes/tpl/top.php");
    include("includes/tpl/sidebar.php");
}
if (preg_match("/com_/i", $option) && file_exists("components/" . $option . "/index.php")) {
    if ($option == "com_module" || $option == "com_lang") {
        //if($_SESSION["user"]->isDev()){
        include("components/" . $option . "/index.php");
        //} else {
        //show404Error("404");
        //}
    } else {
        include("components/" . $option . "/index.php");
    }
} else if ($option == "") {
    @header("location:index.php?option=com_dashboard");
} else {
    show404Error("404");
}
if (in_array($option, array('com_login', 'com_elogin')))
    include("includes/tpl/bottom-login.php");
else
    include("includes/tpl/bottom.php");
    
    
