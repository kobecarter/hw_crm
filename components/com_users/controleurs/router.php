<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addUser' :
            if ($_SESSION['user']->hasDroit('add', 'com_users')) {
                include_once ("user/controleur.php");
            }
            break;
        case 'editUser' :
            if ($_SESSION['user']->hasDroit('edit', 'com_users')) {
                include_once ("user/controleur.php");
            }
            break;
        case 'editMyProfile' :
            // Auto-édition : pas de hasDroit('edit','com_users') ici, volontairement - ce droit est
            // réservé aux admins qui gèrent tout le monde. Tout utilisateur connecté peut modifier
            // SON PROPRE compte ; editMyProfile() vérifie déjà en interne que l'id soumis correspond
            // bien à $_SESSION['user']->getId() avant toute écriture.
            include_once ("user/controleur.php");
            break;
        case 'editPassword' :
            if ($_SESSION['user']->hasDroit('editPass', 'com_users')) {
                include_once ("user/controleur.php");
            }
            break;
        case 'deleteUser' :
            if ($_SESSION['user']->hasDroit('delete', 'com_users')) {
                include_once ("user/controleur.php");
            }
            break;
        case 'enableOperation' :
            if ($_SESSION['user']->hasDroit('edit', 'com_operation')) {
                include_once ("operation/controleur.php");
            }
            break;
		case 'addProfil' :
            if ($_SESSION['user']->hasDroit('add', 'com_users')) {
                include_once ("profil/controleur.php");
            }
            break;
        case 'editProfil' :
            if ($_SESSION['user']->hasDroit('edit', 'com_users')) {
                include_once ("profil/controleur.php");
            }
            break;	
		case 'deleteProfil' :
            if ($_SESSION['user']->hasDroit('delete', 'com_users')) {
                include_once ("profil/controleur.php");
            }
            break;
		case 'setDroit' :
            if ($_SESSION['user']->hasDroit('edit', 'com_users')) {
                include_once ("profil/controleur.php");
            }
            break;		
    }
}