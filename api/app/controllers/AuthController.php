<?php

// declare(strict_types=1);
namespace App\Controllers;

use  Leaf\Http\Headers as rheaders;





use App\Validators\LoginRequestValidator;
use App\Utils\GetV;
use Firebase\JWT\JWT;
use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Validators\AccountRequestValidator;
use Firebase\JWT\Key;
use App\Mails\AccountRegistrationMail;

class AuthController
{
    private static $userID;



    public static function middlware($mode)
    {
        if ($mode === 'auth') {
            if (!self::check()) {
                die(response()->json(
                    array(
                        "success" => false,
                        "message" => ResponseMessages::messages('authRequired')
                    ),
                    401
                ));
            }
        }
    }

    public static function userID()
    {
        return self::$userID;
    }

    public static function user()
    {

        try {
            if (self::$userID == 0) return null;
            return \client::find(self::$userID);
        } catch (\Throwable $th) {
            return null;
        }
    }



    public static function check()
    {

        try {
            $token = rheaders::get("Authorization");
            if (!$token) {
                $token = rheaders::get("authorization");
            }
            if (!$token) {
                return false;
            }
            if (!preg_match('/^Bearer\s+(.*)$/i', $token, $matches)) {
                return false;
            }
            $token = $matches[1];
            $decoded = JWT::decode($token,  new Key(GetV::jwtSecret(), GetV::jwtAlgorithm()));
            self::$userID = $decoded->user_id;
            // Beaucoup de classes du CRM (client, reclamation, devis, facture, temoignage...)
            // supposent une session web active (agence/langue/utilisateur système pour les
            // contrôles de droits) qui n'existe jamais dans ce contexte API stateless.
            // On la simule une fois ici pour tout le reste de la requête plutôt que de
            // patcher chaque site d'appel individuellement.
            if (!isset($_SESSION['user']) || !isset($_SESSION['agence'])) {
                bootstrapSystemSession(
                    defined('SLACK_BOT_ACTING_USER_ID') ? SLACK_BOT_ACTING_USER_ID : 5,
                    1,
                    'fr'
                );
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
    private static function generateJWToken($clientID)
    {
        $secretKey  = GetV::jwtSecret();
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify('+15 days')->getTimestamp();      // Add 60 seconds
        $serverName = "hellworld-agency.com";
        // Retrieved from filtered POST data

        $data = [
            'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $serverName,                       // Issuer
            'nbf'  => $issuedAt->getTimestamp(),         // Not before
            'exp'  => $expire,                           // Expire
            'user_id' => $clientID                    // User id
        ];

        $token =  JWT::encode(
            $data,
            $secretKey,
            GetV::jwtAlgorithm()
        );
        return $token;
    }





    /*
    *creates an account and generates a JWT token 
    */

    public static function signup()
    {
        $validator = new AccountRequestValidator();
        if (!$validator->validate(request()->body())) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds()),
                ),
                400
            );
        }

        $data = request()->body();
        if (\client::findByEmail($data['email'])) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => ResponseMessages::messages('emailExists')
                ),
                400
            );
        }

        $client = new \client();
        $client->setEmail(GetV::value($data, 'email'));
        $client->setRaisonSocial(GetV::value($data, 'raison_social'));
        $client->setNom(GetV::value($data, 'nom'));
        $client->setPrenom(GetV::value($data, 'prenom'));
        $client->setIce(GetV::value($data, 'ice'));
        // $client->setTitre(GetV::value($data, 'titre'));
        // $client->setTel(GetV::value($data, 'tel'));
        // $client->setTel2(GetV::value($data, 'tel2'));
        // $client->setTel3(GetV::value($data, 'tel3'));
        // $client->setAdresse(GetV::value($data, 'adresse'));
        // $client->setAdresse2(GetV::value($data, 'adresse2'));
        // $client->setVille(GetV::value($data, 'ville'));
        // $client->setPays(GetV::value($data, 'pays'));
        // $client->setRegion(GetV::value($data, 'region'));
        $password = GetV::randomPassword();
        $client->setPassword(md5($password));
        $client->setActive(1);
        if ($client->add()) {
            $mail = new AccountRegistrationMail($client, $password);
            $mail->send();
            return  response()->json(
                array(
                    "success" => true,
                    "data" => array(
                        "user" => $client->toArray(),
                    ),
                    "message" => ResponseMessages::messages('accountCreated')
                ),
                200
            );
        } else {
            return response()->json(
                array(
                    "success" => false,
                    "message" => ResponseMessages::messages('generalError')
                ),
                500
            );
        }
    }

    /*
    *Logs in using email and password and generates a JWT token 
    */
    public static function login()
    {
        try {
            $validator = new LoginRequestValidator();
            if (!$validator->validate(request()->body())) {
                return response()->json(
                    array(
                        "success" => false,
                        "message" => ResponseMessages::messages('fieldRequired') . implode(', ', $validator->getFileds())
                    ),
                    400
                );
            }

            //TEST ?email=zineb.benkirane@mrbricolage.ma&password=zineb123


            $data = request()->body();
            $email = $data['email'];
            $password = $data['password'];
            $client = \client::doLogin($email, $password);
            if ($client) {
                $token = self::generateJWToken($client->getId());
                return  response()->json(
                    array(
                        "success" => true,
                        "data" => array(
                            "token" => $token,
                            "user" => $client->toArray(),
                        ),
                        "message" => ResponseMessages::messages('credentialsOk')
                    ),
                    200
                );
            } else throw (new ApiException(array(), 401, ResponseMessages::messages('credentialsFail')));
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getMessage()
                ),
                $ae->getStatusCode()
            );
        }
    }

    /*
    * Connexion (jamais création de compte) via Google Sign-In natif : le
    * client mobile envoie l'ID token Google (JWT signé par Google, pas le
    * nôtre), vérifié ici directement auprès de Google avant de faire
    * confiance à l'email qu'il contient. Volontairement séparé de
    * \client::googleLoginApi() (components/com_client/classes/client.php) :
    * cette méthode legacy émet un jeton via l'ANCIEN système (setToken(),
    * secret/algo différents - voir functions.php) incompatible avec
    * Auth::check() ci-dessus, exactement le bug JWT déjà corrigé ailleurs
    * dans cette API. On réutilise seulement la logique de vérification
    * Google (même endpoint, mêmes contrôles), pas l'émission de jeton.
    */
    public static function googleLogin()
    {
        try {
            if (!defined('GOOGLE_CLIENT_ID_MOBILE_IOS') || GOOGLE_CLIENT_ID_MOBILE_IOS === '') {
                throw (new ApiException(array(), 500, 'Google sign-in is not configured'));
            }
            $data = request()->body();
            $credential = isset($data['credential']) ? trim($data['credential']) : '';
            if ($credential === '') {
                throw (new ApiException(array(), 400, ResponseMessages::messages('fieldRequired') . ' credential'));
            }

            $ch = curl_init('https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $body = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($body === false || $httpCode !== 200) {
                throw (new ApiException(array(), 401, 'Google verification failed'));
            }
            $info = json_decode($body);
            // L'audience DOIT être notre propre client ID iOS (GOOGLE_CLIENT_ID_MOBILE_IOS,
            // config.php - un projet Google Cloud différent de GOOGLE_CLIENT_ID, celui du
            // site web), sinon le jeton a été émis pour une autre application
            // (anti-substitution de jeton).
            if (!is_object($info) || !isset($info->aud) || $info->aud !== GOOGLE_CLIENT_ID_MOBILE_IOS) {
                throw (new ApiException(array(), 401, 'Invalid Google token'));
            }
            $emailVerified = isset($info->email_verified) && ($info->email_verified === true || $info->email_verified === 'true');
            if (!$emailVerified || empty($info->email)) {
                throw (new ApiException(array(), 401, 'Google email not verified'));
            }

            $client = \client::findByEmail($info->email);
            if (!$client || !$client->isActive()) {
                // Volontairement pas de création de compte ici (voir demande
                // produit : Google sert à retrouver un compte existant, pas à
                // s'inscrire) - le client doit d'abord avoir un compte créé
                // normalement (email/mot de passe) une première fois.
                throw (new ApiException(array(), 404, "Aucun compte actif n'est lié à cette adresse Google. Connectez-vous d'abord avec votre email et votre mot de passe."));
            }

            $token = self::generateJWToken($client->getId());
            return response()->json(
                array(
                    "success" => true,
                    "data" => array(
                        "token" => $token,
                        "user" => $client->toArray(),
                    ),
                    "message" => ResponseMessages::messages('credentialsOk')
                ),
                200
            );
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getMessage()
                ),
                $ae->getStatusCode()
            );
        }
    }
}
