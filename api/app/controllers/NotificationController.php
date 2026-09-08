<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Utils\GetV;
use App\Controllers\AuthController as Auth;

class NotificationController
{
    public function updatePreference()
    {
        try {
            Auth::middlware('auth');
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $data = request()->body();
            $enabled = GetV::value($data, 'enabled', true);
            \client::setNotificationsPreference($client->getId(), $enabled);
            return response()->json(
                array(
                    "success" => true,
                    "message" => ResponseMessages::messages('accountUpdated'),
                ),
                200
            );
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
