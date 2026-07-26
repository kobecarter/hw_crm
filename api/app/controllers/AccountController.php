<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Utils\GetV;
use App\Controllers\AuthController as Auth;
use App\Validators\AccountRequestValidator;
use App\Validators\GetResetPassCodeValidator;
use App\Validators\AccountPictureUpdateValidator;
use App\Validators\ResetPasswordValidator;
use App\Mails\PasswordChangedMail;
use App\Mails\GetResetPassCodeMail;
use App\Utils\FileUpload;

class AccountController
{

    public function index()
    {
        return response()->json(
            array(
                "success" => false,
                "message" => "Cannot access this resource",
            ),
            401
        );
    }
    public function show()
    {

        try {
            // this will handle if the use is allowed to access this ressource or not
            Auth::middlware('auth');
            // complete 
            $client = Auth::user();
            if ($client) {
                return response()->json(
                    array(
                        'success' => true,
                        'data' => $client->toArray()
                    ),
                    200
                );
            }
            throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getData(),
                ),
                $ae->getStatusCode()
            );
        }
    }
    // this because this function has the same behavoir as the signup function of AuthController 
    public function store()
    {
        return Auth::signup();
    }

    public function update()
    {
        try {
            Auth::middlware('auth');
            $validator = new AccountRequestValidator();
            if (!$validator->validate(request()->body())) {
                $message = ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds());
                throw (new ApiException($message, 400));
            }
            $data = request()->body();
            $client = Auth::user();
            // return response()->json(
            //     $client->toArray()
            // );
            if ($client) {
                $client->setTitre(GetV::value($data, 'titre', $client->getTitre()));
                $client->setPrenom(GetV::value($data, 'prenom', $client->getPrenom()));
                $client->setNom(GetV::value($data, 'nom', $client->getNom()));
                $client->setRaisonSocial(GetV::value($data, 'raison_social', $client->getRaisonSocial()));
                $client->setIce(GetV::value($data, 'ice', $client->getIce()));
                $client->setTel(GetV::value($data, 'tel', $client->getTel()));
                $client->setTel2(GetV::value($data, 'tel2', $client->getTel2()));
                $client->setTel3(GetV::value($data, 'tel3', $client->getTel3()));
                $client->setEmail(GetV::value($data, 'email', $client->getEmail()));
                $client->setCp(GetV::value($data, 'cp', $client->getCp()));
                $client->setAdresse(GetV::value($data, 'adresse', $client->getAdresse()));
                $client->setAdresse2(GetV::value($data, 'adresse2', $client->getAdresse2()));
                $client->setVille(GetV::value($data, 'ville', $client->getVille()));
                $client->setRegion(GetV::value($data, 'region', $client->getRegion()));
                $client->setPays(GetV::value($data, 'pays', $client->getPays()));

                if ($client->edit()) {
                    return response()->json(
                        array(
                            "success" => true,
                            "message" => ResponseMessages::messages('accountUpdated'),
                        ),
                        200
                    );
                }
                throw (new ApiException(ResponseMessages::messages('generalError'), 500));
            }
            throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getData(),
                ),
                $ae->getStatusCode()
            );
        }
    }


    public function updatePicture()
    {

        try {
            Auth::middlware('auth');
            $client = Auth::user();
            // $validator = new AccountPictureUpdateValidator();
            // if (!$validator->validate(request()->body())) {
            //     $message = ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds());
            //     throw (new ApiException($message, 400));
            // }
            if ($client) {
                $stored = FileUpload::store('photo', '../../../images/clients', array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
                if (!isset($stored) || !$stored['success']) {
                    throw (new ApiException($stored['message'], 500));
                }
                $client->setPhoto($stored['filename']);
                if ($client->edit()) {
                    return response()->json(
                        array(
                            "success" => true,
                            "message" => ResponseMessages::messages('accountUpdated'),
                            'data' => $client->toArray(),
                        ),
                        200
                    );
                }
                throw (new ApiException(ResponseMessages::messages('generalError'), 500));
            }
            throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getData(),
                ),
                $ae->getStatusCode()
            );
        }
    }
    public function sendResetPassCode()
    {
        try {
            $validator = new GetResetPassCodeValidator();
            $data = request()->body();
            if (!$validator->validate($data)) {
                $message = ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds());
                throw (new ApiException($message, 400));
            }

            $client = \client::findByEmail(GetV::value($data, 'email'));
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $passeRecovery = new \passerecovery($client->getId());
            $code = $passeRecovery->generateRawCode();
            $passeRecovery->generateCode();
            $saved = $passeRecovery->save();
            if ($saved) {

                $mail = new GetResetPassCodeMail($client, $code);
                if (!$mail->send()) {
                    throw (new ApiException(ResponseMessages::messages('mailNotSent'), 500));
                }
                return response()->json(
                    array(
                        "success" => true,
                        "message" => ResponseMessages::messages('resetPassCodeSent'),
                    ),
                    200
                );
            }
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getData(),
                ),
                $ae->getStatusCode()
            );
        }
    }


    public function resetPassword()
    {
        try {
            $validator = new ResetPasswordValidator();
            $data = request()->body();
            if (!$validator->validate($data)) {
                $message = ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds());
                throw (new ApiException($message, 400));
            }
            $passeRecovery = \passerecovery::findByCode(GetV::value($data, 'code'));
            if (!$passeRecovery) {
                throw (new ApiException(ResponseMessages::messages('WrongCode'), 400));
            }
            if ($passeRecovery->isExpired()) {
                throw (new ApiException(ResponseMessages::messages('codeExpired'), 400));
            }

            $client = \client::find($passeRecovery->getClientId());
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('wrongCode'), 400));
            }
            if ($client->getId() != $passeRecovery->getClientId()) {
                throw (new ApiException(ResponseMessages::messages('WrongCode'), 400));
            }
            if (GetV::value($data, 'new_password') != GetV::value($data, 'confirm_password')) {
                throw (new ApiException(ResponseMessages::messages('passwordNotMatch'), 400));
            }

            $client->setPassword(md5(GetV::value($data, 'new_password')));
            if (!$client->edit()) {
                throw (new ApiException(ResponseMessages::messages('generalError'), 500));
            }
            $passeRecovery->delete();
            $mail = new PasswordChangedMail($client);
            $mail->send();
            return response()->json(
                array(
                    "success" => true,
                    "message" => ResponseMessages::messages('passwordChanged'),
                ),
                200
            );
        } catch (ApiException $ae) {
            return response()->json(
                array(
                    "success" => false,
                    "message" => $ae->getData()
                ),

                $ae->getStatusCode()
            );
        }
    }
}
