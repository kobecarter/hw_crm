<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Controllers\AuthController as Auth;
use App\Validators\TestimonialValidator;

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

    public function mine()
    {
        try {
            Auth::middlware('auth');
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $testimonial = \temoignage::findByClient($client->getId());
            if (!$testimonial) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            return response()->json([
                'success' => true,
                'data' => $testimonial->toArray(),
            ]);
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

    public function store()
    {
        try {
            Auth::middlware('auth');
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $validator = new TestimonialValidator();
            $data = request()->body();
            if (!$validator->validate($data)) {
                $message = ResponseMessages::messages('fieldRequired') . ' ' . implode(', ', $validator->getFileds());
                throw (new ApiException($message, 400));
            }
            if (!\temoignage::createOrUpdateApi($client, $data)) {
                throw (new ApiException(ResponseMessages::messages('generalError'), 500));
            }
            // Points fidélité : un seul crédit "avis" par client, même règle que côté site.
            if (!\fidelite::hasPointsOfType($client->getId(), 'avis')) {
                \fidelite::awardPoints($client->getId(), \fidelite::POINTS_AVIS, 'avis', 'Avis client déposé');
            }
            return response()->json([
                'success' => true,
                'message' => ResponseMessages::messages('testimonialSaved'),
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
