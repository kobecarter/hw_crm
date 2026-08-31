<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();
if(isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task)
    {
        case 'addClient' :
            if ($_SESSION['user']->hasDroit('add', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'addClientForm' :
            if ($_SESSION['user']->hasDroit('add', 'com_client')) {
                $action = "components/com_client/controleurs/router.php?task=addClient";
                $submitName = "addclientrapide";
                $submitValue = "Ajouter client";
                $agences = agence::findAll($_SESSION['langue']);
                $prefillClient = isset($_GET['prefill']) ? json_decode($_GET['prefill'], true) : array();
                include_once("../views/client/form.php");
            }
            break;
        case 'editClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'deleteClient' :
            if ($_SESSION['user']->hasDroit('delete', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'enableClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'archiveClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'envoyerAccesEspaceClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'retablirClient' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
		case 'filterClient' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;	
		case 'exportClient' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;	
        case 'exportClientWithFilter' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;	
        case 'exportEmail' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                    include_once ("client/controleur.php");
                }
            break;
        case 'getClientSelect' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                    include_once ("client/controleur.php");
                }
            break;
        case 'loginApi' :
                include_once ("client/controleur.php");
            break;
        case 'googleLoginApi' :
                include_once ("client/controleur.php");
            break;
        case 'facebookLoginApi' :
                include_once ("client/controleur.php");
            break;
        case 'verifyEmailApi' :
                include_once ("client/controleur.php");
            break;
        case 'setNewPasswordApi' :
                include_once ("client/controleur.php");
            break;		
        case 'getInfoFromTokenApi' :
                include_once ("client/controleur.php");
            break;
        case 'findClientByIdApi' :
                include_once ("client/controleur.php");
            break;	
        case 'updateProfileApi' :
                include_once ("client/controleur.php");
            break;
        case 'addAttestation' :
            if ($_SESSION['user']->hasDroit('edit', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'deleteAttestation' :
            if ($_SESSION['user']->hasDroit('delete', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        case 'findAttestationsByClientApi' :
                include_once ("client/controleur.php");
            break;
        case 'signAttestationApi' :
                include_once ("client/controleur.php");
            break;
        case 'pdfAttestationApi' :
                include_once ("client/controleur.php");
            break;
        case 'countPendingAttestationsApi' :
                include_once ("client/controleur.php");
            break;
        case 'findClientSocialsByClientApi' :
                include_once ("client/controleur.php");
            break;
        case 'findParrainagesByClientApi' :
                include_once ("client/controleur.php");
            break;
        case 'createParrainageApi' :
                include_once ("client/controleur.php");
            break;
        case 'downloadAttestationAdmin' :
            if ($_SESSION['user']->hasDroit('view', 'com_client')) {
                include_once ("client/controleur.php");
            }
            break;
        // Pas de contrôle hasDroit() ici pour les 4 tâches ci-dessous, contrairement au reste de ce
        // routeur : un accès temporaire (token+code, components/com_client/controleurs/
        // socialaccess/router.php) n'a PAS de $_SESSION['user'] du tout - y accéder ferait une
        // erreur fatale. Chaque fonction du contrôleur appelle elle-même socialAccessAllowed()/
        // socialRevealAllowed() (includes/functions/functions.php), qui gèrent les deux cas
        // (session CRM normale OU jeton temporaire valide) - c'est la vraie porte d'entrée.
        case 'addClientSocial' :
            include_once ("clientsocial/controleur.php");
            break;
        case 'editClientSocial' :
            include_once ("clientsocial/controleur.php");
            break;
        case 'deleteClientSocial' :
            include_once ("clientsocial/controleur.php");
            break;
        case 'revealClientSocialPassword' :
            include_once ("clientsocial/controleur.php");
            break;
        case 'generateClientSocialToken' :
            include_once ("clientsocial/controleur.php");
            break;
        case 'revokeClientSocialToken' :
            include_once ("clientsocial/controleur.php");
            break;
    }
}