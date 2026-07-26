<?php

namespace App\Utils;

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
    static  $hwLabelContactMail = 'support@helloworld-agency.com';
}
