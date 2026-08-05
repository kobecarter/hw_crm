<?php

class aiServiceAssistant
{
    private static function callClaude($systemPrompt, $userMessage, $maxTokens = 1500)
    {
        $messages = array(
            array("role" => "user", "content" => $userMessage)
        );

        $decoded = aiClaudeClient::call($systemPrompt, $messages, $maxTokens);

        $textBlock = aiClaudeClient::extractFirstTextBlock($decoded);
        if ($textBlock === null) {
            throw new Exception("Réponse IA invalide (aucun bloc texte trouvé).");
        }

        $jsonText = aiClaudeClient::extractJson($textBlock);

        $data = json_decode($jsonText, true);
        if (!is_array($data)) {
            throw new Exception("Réponse IA invalide (JSON non interprétable).");
        }
        return $data;
    }

    public static function interpretRequest($message, $currentTitre, $currentDescription)
    {
        $schema = '{"intent":"update_description|scan_website|unknown","proposed_description":"","url":""}';
        $systemPrompt = "Tu es un assistant intégré à un CRM qui aide à gérer une fiche 'service' (une prestation du catalogue). "
            . "Réponds UNIQUEMENT avec un objet JSON valide respectant ce schéma: " . $schema . ". "
            . "Règles: "
            . "- Si l'utilisateur demande de modifier/améliorer/réécrire la description du service, mets intent='update_description' et rédige dans 'proposed_description' une nouvelle description tenant compte de la demande et du contexte actuel (titre: '" . addslashes($currentTitre) . "', description actuelle: '" . addslashes($currentDescription) . "'). Format obligatoire : une liste à puces (chaque ligne commence par '- ' suivi de '<br>' à la fin), PAS un paragraphe de texte continu. "
            . "- Si l'utilisateur demande de scanner/analyser un site web pour en lister les pages, mets intent='scan_website' et extrait l'URL mentionnée dans 'url' (avec https:// si absent). Si aucune URL n'est trouvée dans le message, laisse 'url' vide. "
            . "- Si la demande ne correspond à aucun de ces deux cas, mets intent='unknown'. "
            . "Ne mets jamais autre chose que du JSON dans ta réponse.";

        return self::callClaude($systemPrompt, $message);
    }

    public static function extractPagesFromHtml($url, $html)
    {
        // extraction brute des liens (titre + href) pour donner un contexte structuré à l'IA,
        // plutôt que de lui envoyer le HTML complet (bruyant et volumineux)
        preg_match_all('/<a\s+[^>]*href=["\']([^"\'#]+)["\'][^>]*>(.*?)<\/a>/is', $html, $matches, PREG_SET_ORDER);
        $liens = array();
        foreach ($matches as $m) {
            $texte = trim(strip_tags($m[2]));
            $href = trim($m[1]);
            if ($texte === '' || $href === '') {
                continue;
            }
            $liens[] = $texte . ' => ' . $href;
        }
        $liens = array_slice(array_unique($liens), 0, 200);

        $schema = '{"pages":[{"titre":"","url":""}]}';
        $systemPrompt = "Tu es un assistant qui identifie les pages principales d'un site web (celles visibles dans le menu de navigation) à partir d'une liste brute de liens extraits du HTML. "
            . "Réponds UNIQUEMENT avec un objet JSON respectant ce schéma: " . $schema . ". "
            . "Règles: ignore les liens de réseaux sociaux, mentions légales génériques, liens externes vers d'autres domaines, ancres internes (#), et doublons. "
            . "Convertis les URLs relatives en URLs absolues en te basant sur l'URL de base: " . $url . ". "
            . "Le 'titre' doit être le texte visible du lien (nettoyé). Ne garde que les pages qui semblent correspondre à une vraie page du site (accueil, à propos, services, contact, blog, etc.).";

        $userMessage = "URL de base: " . $url . "\n\nListe des liens trouvés:\n" . implode("\n", $liens);

        return self::callClaude($systemPrompt, $userMessage, 2000);
    }
}
