<?php

// Rédaction de la promesse/offre d'emploi (com_resourcehumaine, onglet "Offre d'emploi").
//
// L'IA ne génère QUE les deux sections qui varient réellement d'un poste à l'autre ("Objectif du
// Poste" et "Brief du Poste") : tout le reste de la lettre (en-tête société, mentions légales,
// paragraphe d'accueil, salaire, horaires, politiques, confidentialité, signature) est un modèle
// juridique fixe assemblé de façon déterministe par joboffer::construireLettreHTML() - jamais par
// l'IA, pour ne jamais risquer qu'un numéro ICE, une clause de confidentialité ou une adresse soit
// altérée/hallucinée dans un document destiné à être signé.
class aiJobOfferAssistant
{
    public static function genererObjectifEtBrief($poste)
    {
        $schema = '{"objectif":"","brief":["", "", "..."]}';
        $systemPrompt = "Tu rédiges la fiche de poste (deux sections) d'une lettre d'offre d'emploi professionnelle en français, pour une agence de communication/marketing (Hello World). "
            . "Réponds UNIQUEMENT avec un objet JSON respectant ce schéma: " . $schema . ". "
            . "'objectif' : un paragraphe (2-3 phrases) résumant la mission générale du poste. "
            . "'brief' : une liste de 6 à 10 tâches concrètes et spécifiques à ce poste précis (pas de généralités interchangeables d'un poste à l'autre), "
            . "rédigées comme des puces de cahier des charges (verbe à l'infinitif en début de puce). "
            . "Adapte entièrement le contenu au poste donné : les tâches d'un·e Designer graphique, d'un·e Social Media Manager, d'un·e Développeur·se web, "
            . "d'un·e Photographe, d'une Assistante de direction ou d'un·e Commercial·e doivent être clairement différentes et spécifiques à ce métier.";

        $userMessage = "Poste : " . $poste;

        $messages = array(array("role" => "user", "content" => $userMessage));
        $decoded = aiClaudeClient::call($systemPrompt, $messages, 1200);

        $textBlock = aiClaudeClient::extractFirstTextBlock($decoded);
        if ($textBlock === null) {
            throw new Exception("Réponse IA invalide (aucun bloc texte trouvé).");
        }

        $jsonText = aiClaudeClient::extractJson($textBlock);
        $data = json_decode($jsonText, true);
        if (!is_array($data) || !isset($data['objectif']) || !isset($data['brief']) || !is_array($data['brief'])) {
            throw new Exception("Réponse IA invalide (JSON non interprétable).");
        }
        return $data;
    }
}
