<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Controllers\AuthController as Auth;

class ParrainageController
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
            $rows = \fidelite::findParrainagesByClient($client->getId());
            $data = array();
            foreach ($rows as $row) {
                $data[] = array(
                    'filleul_nom' => $row['filleul_nom'],
                    'filleul_entreprise' => $row['filleul_entreprise'],
                    'filleul_email' => $row['filleul_email'],
                    'statut' => (int) $row['statut'],
                    'recompense' => $row['recompense'],
                    'recompense_donnee' => (bool) $row['recompense_donnee'],
                    'date_add' => $row['date_add'],
                );
            }
            return response()->json(['success' => true, 'data' => $data]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    public function create()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $data = request()->body();
            $result = \fidelite::createParrainage(
                $client,
                isset($data['filleul_nom']) ? $data['filleul_nom'] : '',
                isset($data['filleul_entreprise']) ? $data['filleul_entreprise'] : '',
                isset($data['filleul_email']) ? $data['filleul_email'] : '',
                isset($data['filleul_tel']) ? $data['filleul_tel'] : '',
                isset($data['message']) ? $data['message'] : ''
            );

            if (!$result['success']) {
                throw (new ApiException($result['message'], 400));
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }
}
