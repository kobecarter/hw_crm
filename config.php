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

/* -- Connexion sociale (espace client) --
   Vérification serveur des jetons Google / Facebook. Renseignez ces valeurs
   (idéalement dans config.secrets.php, qui a priorité car inclus au-dessus).
   - GOOGLE_CLIENT_ID : l'ID client OAuth "Web" (Google Cloud Console). DOIT être
     identique à celui utilisé côté site (hwm_new) pour afficher le bouton.
   - FACEBOOK_APP_ID / FACEBOOK_APP_SECRET : app Meta for Developers. Laisser vide
     tant que Facebook n'est pas activé (le endpoint refuse alors proprement). */
   if (!defined("GOOGLE_CLIENT_ID"))     define("GOOGLE_CLIENT_ID", "953284949892-4miu903begvg1f3tnel0db8s17f8hjid.apps.googleusercontent.com");
   if (!defined("FACEBOOK_APP_ID"))      define("FACEBOOK_APP_ID", "");
   if (!defined("FACEBOOK_APP_SECRET"))  define("FACEBOOK_APP_SECRET", "");

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
