<?php

namespace App\Controllers;

use App\Controllers\AuthController as Auth;
use App\Utils\ResponseMessages;
use \App\Utils\ApiException;
use  Leaf\Http\Headers as rheaders;

class RealisationController
{


    public static function index()
    {
        try {

            $data = array();
            $realisations = \realisation::findAll();
            if (!$realisations) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            foreach ($realisations as $devi) {
                array_push($data, $devi->toArray());
            }
            if (count($data) == 0) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            return response()->json($data);
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
