<?php

namespace App\Controllers;

use App\Controllers\AuthController as Auth;
use App\Utils\ResponseMessages;
use \App\Utils\ApiException;
use \App\Utils\GetV;

class DevisController
{


    // auth middlware
    public function __construct()
    {
        Auth::middlware('auth');
    }

    // Voir FacturesController::pdfUrl() - même logique (jeton dédié 5 min,
    // scope 'pdf_download', pas le JWT de session). Le champ 'pdf' de
    // devis::toArray() pointe lui aussi vers une tâche admin-only inutilisable
    // depuis l'app.
    private static function pdfUrl($devisId)
    {
        $clientID = Auth::userID();
        if (!$clientID) {
            return null;
        }
        $token = GetV::generateDownloadToken($clientID);
        return GetV::apiBaseUrl() . 'devis/' . $devisId . '/pdf?token=' . urlencode($token);
    }

    public static function index()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $data = array();
            $devis = \devis::ofClient($client->getId(), null, true);
            if (!$devis) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            foreach ($devis as $devi) {
                $devisData = $devi->toArray();
                $devisData['pdf'] = self::pdfUrl($devi->getId());
                array_push($data, $devisData);
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

    public function show($id)
    {
        try {
            if (!$id) throw (new ApiException(ResponseMessages::messages('devisIdRquired'), 400));
            $clientID = Auth::userID();
            if (!$clientID) throw (new ApiException(ResponseMessages::messages('authRequired'), 401));
            $devis = \devis::find($id);
            if (!$devis) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            if (!$devis->getClient()) throw (new ApiException(ResponseMessages::messages('noClientRelatedDevis'), 404));
            if ($devis->getClient()->getId() != $clientID) throw (new ApiException(ResponseMessages::messages('devisDoesntBelong'), 400));
            global $db, $siteURL;
            $config =  new \config($db, 'fr');

            $agencyData = array(
                'logo' => $config->getLogo() ? $siteURL . 'images/config/' . $config->getLogo() : null,
                'name' => $config->getNom(),
                'address' => $config->getAdresse(),
            );
            $client = Auth::user();
            $clientData = array(
                'logo' => $client->getPhoto() ? $siteURL . 'images/clients/' . $client->getPhoto() : null,
                'name' => $client->getRaisonSocial(),
                'address' => $client->getAdresse(),
                'ice' => $client->getICE(),
            );
            $items = array();
            foreach ($devis->getItems() as $item) {
                array_push($items, $item->toArray());
            }
            $devisData = $devis->toArray();
            $devisData['pdf'] = self::pdfUrl($devis->getId());
            $devisData['total'] = GetV::formatPrice($devis->getTotal());
            $devisData['tva'] = GetV::formatPrice($devis->getTva());
            if ($devis->getDiscount() != '') {
                $type = $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%';
                $devisData['discount'] = $devis->getDiscountVal() . ' ' . $type;
            }

            $devisData = array_merge(
                $devisData,
                array(
                    'items' => $items,
                    'agency' => $agencyData,
                    'client' => $clientData
                )
            );


            return response()->json(
                [
                    'success' => true,
                    'data' => $devisData,
                ]
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


    public function accept()
    {
        try {
            $clientID = Auth::userID();
            if (!$clientID) throw (new ApiException(ResponseMessages::messages('authRequired'), 401));
            $devis = \devis::find(request()->get('id'));
            if (!$devis) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            if ($devis->getClient()->getId() != $clientID) throw (new ApiException(ResponseMessages::messages('devisDoesntBelong'), 400));
            if ($devis->getStatu() == 1) throw (new ApiException(ResponseMessages::messages('devisAlreadyAccepted'), 200));
            $devis->setStatu(1);
            if (!$devis->edit()) throw (new ApiException(ResponseMessages::messages('generalError'), 500));
            return response()->json(
                [
                    'success' => true,
                    'data' => $devis->toArray(),
                ]
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
