<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Controllers\AuthController as Auth;
use App\Validators\ReclamationValidator;

class ReclamationController
{
    public function __construct()
    {
        Auth::middlware('auth');
    }

    public function add()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $validator = new ReclamationValidator();
            $data = request()->body();
            if (!$validator->validate(request()->body())) {
                $message = ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds());
                throw (new ApiException($message, 400));
            }
            $reclamation = new \reclamation();
            $reclamation->setClient($client);
            $reclamation->setSujet($data['sujet']);
            $reclamation->setMessage($data['message']);
            $reclamation->setDateAdd(date('Y-m-d H:i:s'));
            $reclamation->setEtat(0);
            if (!$reclamation->add()) {
                throw (new ApiException(ResponseMessages::messages('generalError'), 500));
            }
            return response()->json([
                'success' => true,
                'message' => ResponseMessages::messages('reclamationAdded')
            ], 200);
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
}
