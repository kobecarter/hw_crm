<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Validators\ContactEmailValidator;
use App\Mails\ContactMail;
use App\Utils\ApiException;

class ContactController
{
    public function index()
    {
        try {
            // Coordonnées réelles du site (hw_config), pas le crm_config du
            // CRM qui contient des valeurs obsolètes (ex: un email hérité
            // d'un tout autre client, jamais mis à jour depuis).
            return response()->json(['success' => true, 'data' => \siteCatalog::siteConfig()]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                // 'message' => ResponseMessages::messages('generalError')
            ], 500);
        }
    }
    public function contact()
    {

        try {
            $data = request()->body();
            $validator = new ContactEmailValidator();
            if (!$validator->validate($data)) {
                return response()->json(
                    array(
                        "success" => false,
                        "message" => ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds()),
                    ),
                    400
                );
            }
            $mail = new ContactMail($data);

            if ($mail->send()) {
                return response()->json(
                    array(
                        "success" => true,
                        "message" => ResponseMessages::messages('contactMailSent')
                    ),
                    200
                );
            }
            throw (new ApiException($mail->getError(), 500));
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
