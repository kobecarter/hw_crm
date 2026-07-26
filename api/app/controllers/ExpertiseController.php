<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;

class ExpertiseController
{
    public function index()
    {
        try {
            $expertises = \expertise::findAll('fr', true, true);
            if (count($expertises) == 0) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            $data = array();
            foreach ($expertises as $expertise) {
                array_push($data, $expertise->toArray());
            }
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
