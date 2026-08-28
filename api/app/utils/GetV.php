<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class GetV
{

    private static $jwtSecrect = 'QtBU4ivNGachQDztXYNM3tB5cLY8RwYSlNkYAzswEoQsDVlxn9BFzF53lApOAf2aOo4hUAhIqvcPHIy73bzoXg==';
    private static $jwtAlgorithm = 'HS512';
    static private function clean($v)
    {
        $v = trim($v);
        $v = stripslashes($v);
        $v = htmlspecialchars($v);
        return $v;
    }
    public static function value($collection, $key, $default = null)
    {
        return isset($collection[$key]) ? self::clean($collection[$key]) : $default;
    }

    public static function jwtSecret()
    {
        return self::$jwtSecrect;
    }
    public static function jwtAlgorithm()
    {
        return self::$jwtAlgorithm;
    }

    // Jeton dédié au téléchargement d'un PDF facture/devis (ouvert dans le
    // navigateur système du téléphone, donc visible en clair dans l'URL -
    // historique du navigateur, logs serveur, éventuel bot de prévisualisation
    // de lien). Réutiliser le JWT de session (15 jours) là-dedans exposerait
    // un jeton d'accès complet et longue durée ; celui-ci n'est valable que
    // 5 minutes et porte un "scope" que PdfController vérifie explicitement,
    // donc même intercepté il ne donne accès qu'à ce téléchargement précis,
    // et seulement pendant une fenêtre très courte.
    public static function generateDownloadToken($clientId)
    {
        $issuedAt = new \DateTimeImmutable();
        $data = [
            'iat' => $issuedAt->getTimestamp(),
            'exp' => $issuedAt->modify('+5 minutes')->getTimestamp(),
            'user_id' => $clientId,
            'scope' => 'pdf_download',
        ];
        return JWT::encode($data, self::$jwtSecrect, self::$jwtAlgorithm);
    }

    // Décode un jeton de téléchargement et vérifie qu'il porte bien le scope
    // 'pdf_download' (pas un JWT de session détourné de son usage). Retourne
    // le user_id ou null si invalide/expiré/mauvais scope.
    public static function verifyDownloadToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$jwtSecrect, self::$jwtAlgorithm));
            if (!isset($decoded->scope) || $decoded->scope !== 'pdf_download') {
                return null;
            }
            return (int) $decoded->user_id;
        } catch (\Throwable $th) {
            return null;
        }
    }

    // Base absolue de cette API (schéma + hôte + chemin jusqu'à index.php),
    // dérivée de la requête courante pour rester correcte aussi bien en dev
    // (IP locale, dossier hw_crm/api) qu'en prod (domaine, hw-label/new/api)
    // sans dupliquer cette logique à chaque endroit qui construit une URL
    // absolue (ex. lien PDF facture/devis).
    public static function apiBaseUrl()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        return $scheme . '://' . $host . $base . '/';
    }

    public static function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#$%^&*()_+-=[]{};:';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
    public static function formatPrice($price)
    {
        if ($price == 0 || !$price || empty($price) || !is_numeric($price)) {
            return '0,00';
        }
        return number_format($price, 2, ',', ' ');
    }
    static  $hwLabelEmail = 'noreply@helloworld-agency.com';
    static  $hwLabelContactMail = 'contact@helloworld-agency.com';
}
