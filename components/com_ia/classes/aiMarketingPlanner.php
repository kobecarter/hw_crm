<?php

class aiMarketingPlanner
{
    public static function generateBrief($description, $langue = 'fr')
    {
        $nomLangue = aiClaudeClient::nomLangue($langue);
        $instructionLangue = aiClaudeClient::instructionLangue($langue);
        $schema = '{'
            . '"titre":"",'
            . '"resume":"",'
            . '"objectifs":[""],'
            . '"etapes":[{"titre":"","description":"","duree_estimee":""}],'
            . '"canaux_recommandes":[""],'
            . '"budget_estime":{"montant_min":0,"montant_max":0,"devise":"MAD","justification":""},'
            . '"kpis":[""]'
            . '}';

        $systemPrompt = $instructionLangue . " "
            . "Tu es un consultant en stratégie marketing digital travaillant pour l'agence de communication Hello World. "
            . "Un client de l'agence décrit un besoin ou projet marketing en langage libre. Tu dois transformer cette description "
            . "en un brief de projet structuré et actionnable, réaliste pour une petite ou moyenne entreprise. "
            . "Réponds UNIQUEMENT avec un objet JSON valide respectant strictement ce schéma: " . $schema . ". "
            . "Règles: "
            . "- 'titre': un titre court et clair pour le projet (5-8 mots). "
            . "- 'resume': 2-3 phrases résumant la stratégie proposée. "
            . "- 'objectifs': 3 à 5 objectifs concrets et mesurables. "
            . "- 'etapes': 4 à 6 étapes chronologiques du projet, chacune avec un titre court, une description d'une phrase, et une durée estimée (ex: '2 semaines'). "
            . "- 'canaux_recommandes': 3 à 6 canaux marketing pertinents pour ce besoin (ex: Instagram, Google Ads, SEO, Email marketing, ...). "
            . "- 'budget_estime': une fourchette réaliste en MAD (dirhams marocains) pour ce type de projet au Maroc, avec une justification d'une phrase. "
            . "- 'kpis': 3 à 5 indicateurs de performance pour mesurer le succès. "
            . "Base-toi uniquement sur les informations et le secteur d'activité mentionnés par le client ; si des détails manquent, fais des hypothèses raisonnables plutôt que de laisser des champs vides. "
            . "Ne mets jamais autre chose que du JSON dans ta réponse. " . $instructionLangue;

        $messages = array(
            array("role" => "user", "content" => $description)
        );

        $decoded = aiClaudeClient::call($systemPrompt, $messages, 2500);

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
}
