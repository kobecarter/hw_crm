<?php

// Envoi de notifications push mobile via OneSignal (REST API, pas de SDK). Même contrat que
// projectNotifier (com_facture/classes/projectNotifier.php) : ne lève jamais d'exception vers
// l'appelant, ne bloque jamais l'action métier d'origine (création facture/devis, réponse
// réclamation, palier fidélité...), journalise en cas d'échec via error_log().
//
// Identité côté mobile : OneSignal.login(idClient) lie l'appareil à un "external_id" = l'id CRM
// du client, ce qui évite d'avoir à stocker un token d'appareil côté CRM - on cible juste
// include_aliases.external_id sur l'API OneSignal.
class pushNotifier
{
    private static function configured()
    {
        return defined('ONESIGNAL_APP_ID') && ONESIGNAL_APP_ID !== '' && defined('ONESIGNAL_REST_API_KEY') && ONESIGNAL_REST_API_KEY !== '';
    }

    private static function post($payload)
    {
        try {
            $ch = curl_init('https://api.onesignal.com/notifications');
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Key ' . ONESIGNAL_REST_API_KEY,
                ),
                CURLOPT_POSTFIELDS => json_encode($payload),
            ));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            if ($response === false || $httpCode < 200 || $httpCode >= 300) {
                error_log('pushNotifier::post - échec (HTTP ' . $httpCode . ', ' . $curlError . ') : ' . $response);
            }
        } catch (\Throwable $e) {
            error_log('pushNotifier::post exception - ' . $e->getMessage());
        }
    }

    // Envoi à un seul client. $data porte le payload de deep-link mobile, ex:
    // array('type' => 'facture', 'id' => 123).
    public static function send($client, $title, $message, $data = array())
    {
        if (!self::configured() || !$client || !$client->getId()) {
            return;
        }
        if (method_exists($client, 'isNotificationsEnabled') && !$client->isNotificationsEnabled()) {
            return;
        }
        self::post(array(
            'app_id' => ONESIGNAL_APP_ID,
            'include_aliases' => array('external_id' => array((string) $client->getId())),
            'target_channel' => 'push',
            'headings' => array('fr' => $title, 'en' => $title),
            'contents' => array('fr' => $message, 'en' => $message),
            'data' => $data,
        ));
    }

    // Diffusion à plusieurs clients (catalogue formation/service - pas de destinataire unique).
    // $clientIds : tableau d'ids CRM déjà filtrés sur notifications_enabled=1 par l'appelant.
    public static function broadcast($clientIds, $title, $message, $data = array())
    {
        if (!self::configured() || empty($clientIds)) {
            return;
        }
        // Limite documentée OneSignal : 2000 external_id par appel.
        foreach (array_chunk(array_map('strval', $clientIds), 2000) as $chunk) {
            self::post(array(
                'app_id' => ONESIGNAL_APP_ID,
                'include_aliases' => array('external_id' => $chunk),
                'target_channel' => 'push',
                'headings' => array('fr' => $title, 'en' => $title),
                'contents' => array('fr' => $message, 'en' => $message),
                'data' => $data,
            ));
        }
    }
}
