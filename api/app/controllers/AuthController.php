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
            if (\client::doLogin($email, $password)) {
                $client = \client::findByEmail($email);
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
}
