<?php

namespace App\Controllers;

use App\Utils\ResponseMessages;
use App\Utils\ApiException;
use App\Controllers\AuthController as Auth;

class LoyaltyController
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
            $clientId = $client->getId();
            $total = \fidelite::getTotalByClient($clientId);
            $history = \fidelite::findHistoryByClient($clientId);
            $rewards = \fidelite::findRewardsByClient($clientId);

            $tiers = array();
            foreach (\fidelite::REWARD_THRESHOLDS as $seuil => $libelle) {
                $reward = null;
                foreach ($rewards as $r) {
                    if ((int) $r['seuil'] === (int) $seuil) {
                        $reward = $r;
                        break;
                    }
                }
                $tiers[] = array(
                    'seuil' => (int) $seuil,
                    'libelle' => $libelle,
                    'debloque' => $total >= $seuil,
                    'donne' => $reward ? ((int) $reward['statut'] === 1) : false,
                );
            }

            $historyOut = array();
            foreach ($history as $h) {
                $historyOut[] = array(
                    'points' => (int) $h['points'],
                    'type' => $h['type'],
                    'libelle' => $h['libelle'],
                    'date_add' => $h['date_add'],
                );
            }

            return response()->json([
                'success' => true,
                'data' => array(
                    'total' => $total,
                    'history' => $historyOut,
                    'tiers' => $tiers,
                ),
            ]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }

    // Action déclarative "j'ai suivi vos réseaux" - un seul crédit par client.
    public function socialFollow()
    {
        try {
            $client = Auth::user();
            if (!$client) {
                throw (new ApiException(ResponseMessages::messages('noUserFound'), 404));
            }
            $clientId = $client->getId();
            if (\fidelite::hasPointsOfType($clientId, 'social_follow')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Déjà crédité précédemment.',
                    'already' => true,
                ]);
            }
            \fidelite::awardPoints($clientId, \fidelite::POINTS_SOCIAL, 'social_follow', 'Réseaux sociaux suivis');
            return response()->json([
                'success' => true,
                'message' => '+' . \fidelite::POINTS_SOCIAL . ' points ajoutés, merci de nous suivre !',
            ]);
        } catch (ApiException $ae) {
            return response()->json(
                array("success" => false, "message" => $ae->getData()),
                $ae->getStatusCode()
            );
        }
    }
}
