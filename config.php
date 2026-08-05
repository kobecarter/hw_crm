<?php
/* -- Connection à la base de données -- */

   $dbType = "mysql";
   $host = "localhost";
   $login = "keha1057_crm";
   $password = "_k0._W!XhIVB";
   $dataBaseName = "keha1057_crm";
   $secureCode = "hwadmin2025";

/* -------------------------------------- */

   $prefixe_db = "crm_";
   $siteURL = "http://localhost/hw_crm/";
   $hwaURL = "https://www.helloworld-agency.com/";
   $projet = "CRM Hello World";

/* -- Variables globales -- */

   define("__prefixe_db__", $prefixe_db);
   global $siteURL;
   global $projet;

   if (file_exists(__DIR__ . "/config.secrets.php")) {
       require_once(__DIR__ . "/config.secrets.php");
   }

/* -- Durcissement du cookie de session -- */
// httponly : le cookie de session devient invisible à document.cookie côté JS - si une faille XSS
// existe quelque part dans l'app, elle ne peut plus voler la session par ce biais. samesite=Lax
// (pas Strict) : le flux SSO externe (com_login/controleurs/login.php::login_global(), redirection
// GET depuis helloworld-agency.com) a besoin que le cookie soit envoyé sur une navigation
// top-level venant d'un autre site, ce que Strict bloquerait. "secure" n'est PAS activé ici : ce
// déploiement tourne en HTTP simple ($siteURL ci-dessus) - l'activer casserait toute connexion
// tant que le site n'est pas servi en HTTPS. À activer dès que ce sera le cas.
   if (session_status() === PHP_SESSION_NONE) {
       session_set_cookie_params(array(
           'httponly' => true,
           'samesite' => 'Lax',
       ));
   }
