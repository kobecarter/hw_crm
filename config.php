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
   // Client OAuth "iOS" (Google Cloud Console) de l'app mobile - un projet
   // Google Cloud DIFFERENT de GOOGLE_CLIENT_ID ci-dessus (celui du site web).
   // L'app mobile n'envoie pas de serverClientId à GoogleSignIn(), donc le
   // idToken natif iOS a pour audience CE client, pas GOOGLE_CLIENT_ID -
   // vérifier contre ce dernier rejetait donc systématiquement le token
   // ("Invalid Google token") quel que soit le compte. Doit rester identique
   // à GIDClientID dans mobile-app/ios/Runner/Info.plist.
   if (!defined("GOOGLE_CLIENT_ID_MOBILE_IOS")) define("GOOGLE_CLIENT_ID_MOBILE_IOS", "148130864337-bqkgg48t0dqq9uvavk9nkquq9pofhlsl.apps.googleusercontent.com");

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
