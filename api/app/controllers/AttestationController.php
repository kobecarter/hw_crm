<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Controllers\AuthController as Auth;

class AttestationController
{
    public function __construct()
    {
        Auth::middlware('auth');
    }

    public function index()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $items = \attestation::findAllByClientRest($client->getId());
            return response()->json(['success' => true, 'data' => $items]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    public function sign($id)
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $data = request()->body();
            $nom = isset($data['signature_nom']) ? $data['signature_nom'] : '';
            $result = \attestation::signRest($client->getId(), $id, $nom);
            if (!$result['success']) {
                throw (new ApiException($result['message'], 400));
            }
            // +20 points, une seule fois par attestation (la contrainte "déjà signée"
            // de signRest() empêche de toute façon un second crédit sur la même attestation).
            \fidelite::awardPoints($client->getId(), \fidelite::POINTS_ATTESTATION, 'attestation', 'Attestation signée');
            return response()->json(['success' => true, 'message' => $result['message']]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    public function pdf($id)
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $result = \attestation::pdfRest($client->getId(), $id);
            if (!$result['success']) {
                throw (new ApiException($result['message'], 404));
            }
            return response()->json(['success' => true, 'data' => $result]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }
}
