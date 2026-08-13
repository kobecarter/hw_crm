<?php

class aiClientSummary
{
    public static function generate($client, $langue = 'fr')
    {
        $contexte = self::buildContext($client);
        $nomLangue = aiClaudeClient::nomLangue($langue);
        $instructionLangue = aiClaudeClient::instructionLangue($langue);

        $systemPrompt = $instructionLangue . " "
            . "Tu es un assistant intégré à un CRM d'agence qui aide à synthétiser l'historique "
            . "d'un client à partir de ses devis, factures et paiements. Réponds en " . $nomLangue . ", en 3 à 6 phrases "
            . "factuelles (pas de liste à puces, un paragraphe court), qui couvrent : le statut global de la "
            . "relation (nouveau client, client actif, client historique...), le volume global engagé "
            . "(montants/devises), les principaux services/prestations réalisés, et la situation de paiement "
            . "(à jour, factures en attente, retards éventuels). Ne mets rien d'autre que ce paragraphe dans ta "
            . "réponse (pas de titre, pas de formule de politesse). " . $instructionLangue;

        $messages = array(
            array("role" => "user", "content" => $contexte)
        );

        $decoded = aiClaudeClient::call($systemPrompt, $messages, 600);
        $texte = aiClaudeClient::extractFirstTextBlock($decoded);
        if ($texte === null || trim($texte) === '') {
            throw new Exception("Réponse IA invalide (aucun texte retourné).");
        }
        return trim($texte);
    }

    private static function buildContext($client)
    {
        $nom = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
        $contexte = "Client : " . $nom . "\n";
        $contexte .= "Client depuis le : " . $client->getDateAdd() . "\n\n";

        $deviss = $client->getDevis();
        $contexte .= "Devis (" . count($deviss) . ") :\n";
        foreach ($deviss as $devis) {
            $contexte .= "- N°" . $devis->getNumero() . " du " . $devis->getDateDevis() . " : " . number_format($devis->getTotal(), 2, ',', ' ') . " " . $devis->getDevise() . " (statut " . $devis->getStatu() . ")\n";
            foreach ($devis->getItems() as $item) {
                $contexte .= "    service : " . $item->getTitre() . "\n";
            }
        }

        $factures = $client->getFacture();
        $contexte .= "\nFactures (" . count($factures) . ") :\n";
        foreach ($factures as $facture) {
            $reste = $facture->getReste();
            $etat = $reste <= 0 ? "soldée" : ($reste < $facture->getTotal() ? "partiellement payée (reste " . number_format($reste, 2, ',', ' ') . " " . $facture->getDevise() . ")" : "impayée");
            $contexte .= "- N°" . $facture->getNumero() . " du " . $facture->getDateFacture() . " : " . number_format($facture->getTotal(), 2, ',', ' ') . " " . $facture->getDevise() . " - " . $etat . "\n";
        }

        return $contexte;
    }
}
