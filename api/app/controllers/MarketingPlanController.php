<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Controllers\AuthController as Auth;

class MarketingPlanController
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
            $data = array();
            foreach (\marketingPlan::findAllByClient($client->getId()) as $plan) {
                array_push($data, $plan->toArray());
            }
            return response()->json($data);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    public function show($id)
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $plan = \marketingPlan::find($id, $client->getId());
            if (!$plan) {
                throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            }
            return response()->json(['success' => true, 'data' => $plan->toArray()]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    // Génère un brief IA à partir de la description du client et le sauvegarde en brouillon.
    public function generate()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $data = request()->body();
            $description = isset($data['description']) ? trim((string) $data['description']) : '';
            if ($description === '') {
                throw (new ApiException(ResponseMessages::messages('fieldRequired') . ' description', 400));
            }
            if (mb_strlen($description) < 15) {
                throw (new ApiException('Merci de décrire votre besoin avec un peu plus de détails.', 400));
            }

            try {
                $brief = \aiMarketingPlanner::generateBrief($description, 'fr');
            } catch (\Exception $e) {
                throw (new ApiException('Le générateur IA est momentanément indisponible, veuillez réessayer.', 502));
            }

            $plan = new \marketingPlan();
            $plan->setClient($client);
            $plan->setDescription($description);
            $plan->setBriefJson(json_encode($brief, JSON_UNESCAPED_UNICODE));
            $plan->setStatut(\marketingPlan::STATUT_BROUILLON);
            $planId = $plan->add();

            return response()->json([
                'success' => true,
                'data' => array(
                    'id' => $planId,
                    'description' => $description,
                    'brief' => $brief,
                    'statut' => \marketingPlan::STATUT_BROUILLON,
                ),
            ]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    // Envoie un brief (déjà généré) à l'agence comme demande formelle.
    public function submit($id)
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $plan = \marketingPlan::find($id, $client->getId());
            if (!$plan) {
                throw (new ApiException(ResponseMessages::messages('noDataFound'), 404));
            }
            if ($plan->getStatut() == \marketingPlan::STATUT_SOUMIS) {
                throw (new ApiException('Cette demande a déjà été envoyée.', 400));
            }
            $plan->markSubmitted();

            return response()->json([
                'success' => true,
                'message' => 'Votre projet a été envoyé à l\'agence Hello World, notre équipe vous contactera prochainement.',
            ]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }
}
