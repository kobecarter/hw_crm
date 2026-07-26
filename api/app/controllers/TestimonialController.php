<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;

class TestimonialController
{
    public function index()
    {
        try {
            $testimonials = \temoignage::findAll('fr', true, true);
            if (count($testimonials) == 0) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            $data = array();
            foreach ($testimonials as $testimonial) {
                array_push($data, $testimonial->toArray());
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
