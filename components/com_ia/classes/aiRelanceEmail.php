<?php

// Rédaction du CORPS d'un email de relance (le sujet reste déterministe, voir
// libelleCategorie() - pré-rempli mais éditable dans le formulaire, comme le corps) - même
// squelette que aiClientSummary::generate() (texte libre via aiClaudeClient::call() +
// extractFirstTextBlock(), pas aiExtractor qui est verrouillé sur une sortie JSON).
class aiRelanceEmail
{
    public static function generate($client, $facture, $categorie, $langue = 'fr', $domaineInfo = null)
    {
        $contexte = self::buildContext($client, $facture, $categorie, $domaineInfo);
        $nomLangue = aiClaudeClient::nomLangue($langue);
        $instructionLangue = aiClaudeClient::instructionLangue($langue);

        // Catégorie "paiement" : il s'agit de réclamer un montant dû (pas un simple rappel
        // informatif) - le contexte transmis par buildContext() inclut déjà le reste dû de la
        // facture liée au règlement choisi dans le formulaire.
        $consigneCategorie = '';
        if ($categorie === 'paiement') {
            $consigneCategorie = "Cet email doit explicitement réclamer au client le règlement du "
                . "montant dû (voir le \"Reste dû\" indiqué dans le contexte ci-dessous), en "
                . "mentionnant ce montant précis et la facture concernée, et en invitant clairement "
                . "le client à régler cette somme dans les meilleurs délais. Le ton reste courtois "
                . "mais direct et ferme sur la demande de paiement, sans tourner autour du sujet. ";
        }

        $systemPrompt = $instructionLangue . " "
            . "Tu rédiges, pour une agence, le corps d'un email de relance professionnel et courtois "
            . "adressé à un client, sur le sujet suivant : " . self::libelleCategorie($categorie) . ". "
            . $consigneCategorie
            . "Réponds en " . $nomLangue . ", en HTML simple (balises <p>, <br>, <strong> uniquement, "
            . "pas de <html>/<head>/<body>), sans sujet ni objet (juste le corps), en commençant par "
            . "une formule d'appel (ex: \"Bonjour,\") et en terminant par une formule de politesse. "
            . "Reste concis (4 à 8 phrases). Ne mets rien d'autre que ce corps dans ta réponse. "
            . $instructionLangue;

        $messages = array(
            array("role" => "user", "content" => $contexte)
        );

        $decoded = aiClaudeClient::call($systemPrompt, $messages, 800);
        $texte = aiClaudeClient::extractFirstTextBlock($decoded);
        if ($texte === null || trim($texte) === '') {
            throw new Exception("Réponse IA invalide (aucun texte retourné).");
        }
        return trim($texte);
    }

    // Sujet déterministe par catégorie (pas d'IA ici) : reste pré-rempli et éditable dans le
    // formulaire, mais ne dépend pas de la disponibilité/variabilité de l'IA pour un simple
    // intitulé court.
    public static function libelleCategorie($categorie)
    {
        $libelles = array(
            'facture' => 'Rappel de facture en attente de règlement',
            'paiement' => 'Rappel de paiement',
            'expiration_domaine' => 'Expiration prochaine de votre nom de domaine',
            'hebergement' => 'Renouvellement de votre hébergement',
            'autre_service' => 'Relance concernant votre service',
        );
        return isset($libelles[$categorie]) ? $libelles[$categorie] : 'Relance';
    }

    private static function buildContext($client, $facture, $categorie, $domaineInfo = null)
    {
        $nom = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
        $agenceNom = $client->getAgence() ? $client->getAgence()->getNom() : '';

        $contexte = "Client : " . $nom . "\n";
        $contexte .= "Agence qui envoie cet email : " . $agenceNom . "\n";
        $contexte .= "Sujet de la relance : " . self::libelleCategorie($categorie) . "\n";

        if ($facture && $facture->getId() != 0) {
            $contexte .= "\nFacture concernée :\n";
            $contexte .= "- N°" . $facture->getNumero() . " du " . $facture->getDateFacture() . "\n";
            $contexte .= "- Montant total : " . number_format($facture->getTotal(), 2, ',', ' ') . " " . $facture->getDevise() . "\n";
            $contexte .= "- Reste dû : " . number_format($facture->getReste(), 2, ',', ' ') . " " . $facture->getDevise() . "\n";
        }

        if (!empty($domaineInfo['domaine'])) {
            $estHebergement = $categorie === 'hebergement';
            $contexte .= "\n" . ($estHebergement ? "Hébergement concerné" : "Domaine concerné") . " :\n";
            $contexte .= "- " . ($estHebergement ? "Désignation" : "Nom de domaine") . " : " . $domaineInfo['domaine'] . "\n";
            if (!empty($domaineInfo['expiration'])) {
                $contexte .= "- Date d'expiration : " . $domaineInfo['expiration'] . "\n";
            }
        }

        return $contexte;
    }
}
