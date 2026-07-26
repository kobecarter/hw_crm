<?php

namespace App\Controllers;

use App\Controllers\AuthController as Auth;
use App\Utils\ResponseMessages;
use \App\Utils\ApiException;
use \App\Utils\GetV;
use  Leaf\Http\Headers as rheaders;

class FacturesController
{


    // auth middlware
    public function __construct()
    {
        Auth::middlware('auth');
    }
    public static function index()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $data = array();
            $factures = \facture::ofClient($client->getId());
            if (!$factures) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            foreach ($factures as $facture) {
                array_push($data, $facture->toArray());
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
            if (!$id) throw (new ApiException(ResponseMessages::messages('factureIdRquired'), 400));
            $clientID = Auth::userID();
            if (!$clientID) throw (new ApiException(ResponseMessages::messages('authRequired'), 401));
            $facture = \facture::find($id,$_SESSION['agence']);
            if (!$facture) throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            if (!$facture->getClient()) throw (new ApiException(ResponseMessages::messages('noClientRelatedFacture'), 404));
            if ($facture->getClient()->getId() != $clientID) throw (new ApiException(ResponseMessages::messages('factureDoesntBelong'), 400));
            global $db, $siteURL;
            $config =  new \config($db, 'fr');

            $agencyData = array(
                'logo' => $config->getLogo() ? $siteURL . 'images/config/' . $config->getLogo() : null,
                'name' => $config->getNom(),
                'address' => $config->getAdresse(),
            );

            $clientData = array(
                'logo' => $facture->getClient()->getPhoto() ? $siteURL . 'images/clients/' . $facture->getClient()->getPhoto() : null,
                'name' => $facture->getClient()->getRaisonSocial(),
                'address' => $facture->getClient()->getAdresse(),
                'ice' => $facture->getClient()->getICE(),
            );
            $payments = array();
            foreach ($facture->getPayments() as $payment) {
                array_push($payments, $payment->toArray());
            }
            $items = array();
            foreach ($facture->getItems() as $item) {
                array_push($items, $item->toArray());
            }
            $factureData = $facture->toArray();
            $factureData['total'] = GetV::formatPrice($facture->getTotal());
            $factureData['tva'] = GetV::formatPrice($facture->getTva());
            if ($facture->getDiscount() != '') {
                $type = $facture->getDiscount() == 'amount' ? ' ' . $facture->getDevise() : '%';
                $factureData['discount'] = $facture->getDiscountVal() . ' ' . $type;
            }

            $factureData = array_merge(
                $factureData,
                array(
                    'payments' => $payments,
                    'items' => $items,
                    'agency' => $agencyData,
                    'client' => $clientData
                )
            );


            return response()->json(
                [
                    'success' => true,
                    'data' => $factureData,
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
