<?php

class aiClaudeClient
{
    // 529 = surcharge temporaire de l'API Anthropic, 429 = rate limit, 5xx = erreur serveur ponctuelle :
    // ces cas valent la peine d'être retentés avant d'échouer, contrairement à une erreur de payload (4xx).
    private static $retryableCodes = array(429, 500, 502, 503, 529);

    public static function call($systemPrompt, $messages, $maxTokens = 1500)
    {
        if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY == '') {
            throw new Exception("Clé API Anthropic non configurée (config.secrets.php).");
        }
        $model = defined('ANTHROPIC_MODEL') ? ANTHROPIC_MODEL : 'claude-sonnet-5';

        $payload = array(
            "model" => $model,
            "max_tokens" => $maxTokens,
            "system" => $systemPrompt,
            "messages" => $messages
        );

        // json_encode() renvoie silencieusement false (donc un corps de requête vide envoyé
        // à l'API, qui répond alors "zero-length, empty document") si $payload contient une
        // séquence UTF-8 invalide — fréquent avec le texte extrait d'un PDF (police mal
        // encodée). On le détecte ici avec un message clair plutôt que de laisser partir une
        // requête vide.
        $body = json_encode($payload);
        if ($body === false) {
            throw new Exception("Erreur d'encodage JSON de la requête (" . json_last_error_msg() . "). Le texte extrait du fichier contient probablement des caractères invalides.");
        }

        $maxAttempts = 4;
        $lastHttpCode = null;
        $lastMsg = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $ch = curl_init("https://api.anthropic.com/v1/messages");
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => array(
                    "x-api-key: " . ANTHROPIC_API_KEY,
                    "anthropic-version: 2023-06-01",
                    "content-type: application/json"
                ),
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_TIMEOUT => 60
            ));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                throw new Exception("Erreur de connexion à l'API Claude: " . $curlError);
            }

            if ($httpCode === 200) {
                return json_decode($response, true);
            }

            $decoded = json_decode($response, true);
            $lastHttpCode = $httpCode;
            $lastMsg = isset($decoded['error']['message']) ? $decoded['error']['message'] : $response;

            if (!in_array($httpCode, self::$retryableCodes) || $attempt === $maxAttempts) {
                break;
            }

            // backoff exponentiel : 1s, 2s, 4s
            usleep((int) (pow(2, $attempt - 1) * 1000000));
        }

        throw new Exception("Erreur API Claude (" . $lastHttpCode . "): " . $lastMsg);
    }

    public static function nomLangue($code)
    {
        $noms = array('fr' => 'français', 'en' => 'anglais', 'ar' => 'arabe');
        return isset($noms[$code]) ? $noms[$code] : 'français';
    }

    // Rédigée DANS la langue cible (pas juste "en anglais" au milieu d'un prompt français) et
    // répétée en tête ET en fin de system prompt par les appelants : un unique mot-clé de langue
    // noyé dans un prompt par ailleurs entièrement français (champs JSON, contexte métier fourni
    // en français) s'est révélé un signal trop faible, l'API répondant quand même en français.
    public static function instructionLangue($code)
    {
        $phrases = array(
            'fr' => "IMPORTANT : rédige l'intégralité de ta réponse (tous les textes destinés à l'utilisateur final) en français, même si les données fournies en entrée sont dans une autre langue.",
            'en' => "IMPORTANT: write your entire response (all end-user-facing text) in English, even if the input data provided below is in another language.",
            'ar' => "مهم: اكتب ردك بالكامل (كل النصوص الموجهة للمستخدم النهائي) باللغة العربية، حتى لو كانت البيانات المدخلة أدناه بلغة أخرى.",
        );
        return isset($phrases[$code]) ? $phrases[$code] : $phrases['fr'];
    }

    public static function extractFirstTextBlock($decoded)
    {
        if (isset($decoded['content']) && is_array($decoded['content'])) {
            foreach ($decoded['content'] as $block) {
                if (isset($block['type']) && $block['type'] === 'text' && isset($block['text'])) {
                    return $block['text'];
                }
            }
        }
        return null;
    }

    public static function extractJson($textBlock)
    {
        $jsonText = trim($textBlock);
        $jsonText = preg_replace('/^```(json)?/i', '', $jsonText);
        $jsonText = preg_replace('/```$/', '', $jsonText);
        $jsonText = trim($jsonText);

        // Filet de sécurité si Claude a malgré tout ajouté une phrase avant/après le JSON
        // (l'instruction "réponds UNIQUEMENT avec du JSON" n'est pas garantie à 100%) : on
        // isole la zone entre la première accolade/crochet ouvrant et la dernière fermante
        // correspondante, plutôt que de se fier uniquement aux balises ```json``` en tête/pied.
        $premiereAccolade = mb_strpos($jsonText, '{');
        $premierCrochet = mb_strpos($jsonText, '[');
        if ($premiereAccolade !== false || $premierCrochet !== false) {
            $debut = ($premiereAccolade !== false && ($premierCrochet === false || $premiereAccolade < $premierCrochet)) ? $premiereAccolade : $premierCrochet;
            $caractereFermant = $jsonText[$debut] === '{' ? '}' : ']';
            $fin = mb_strrpos($jsonText, $caractereFermant);
            if ($fin !== false && $fin >= $debut) {
                $jsonText = mb_substr($jsonText, $debut, $fin - $debut + 1);
            }
        }

        return trim($jsonText);
    }
}
