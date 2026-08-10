<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

class aiExtractor
{
    public static function extractText($absolutePath)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($absolutePath);
        $text = $pdf->getText();

        // Nettoyage UTF-8 : l'extraction de texte PDF produit parfois des séquences
        // d'octets invalides (polices mal encodées), ce qui fait échouer json_encode()
        // plus loin (payload vide envoyé à l'API Claude -> "zero-length, empty document").
        if (!mb_check_encoding($text, 'UTF-8')) {
            $cleaned = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
            $text = $cleaned !== false ? $cleaned : mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        return $text;
    }

    public static function extractStructuredData($text)
    {
        $schema = '{"client":{"prenom":"","nom":"","raison_social":"","fonction":"","email":"","tel":"","adresse":"","ville":"","pays":""},"services":[{"titre":"","description":"","qte":1,"prix":0,"unite":""}],"conditions":[{"montant":"","condition":""}]}';

        $systemPrompt = "Tu es un assistant qui extrait des informations structurées à partir du texte d'une présentation commerciale ou d'un brief client, pour un CRM. "
            . "Réponds UNIQUEMENT avec un objet JSON valide, sans aucun texte avant ou après, sans balises markdown. "
            . "Le JSON doit respecter exactement ce schéma: " . $schema . " "
            . "Le champ 'conditions' correspond aux conditions de paiement (ex: échéancier, acomptes) si elles sont explicitement mentionnées dans le texte : 'montant' est soit un pourcentage (ex: '50%') soit un montant fixe, 'condition' est une courte description (ex: 'Acompte à la signature', 'Solde à la livraison'). Ne crée une ligne de condition que si le texte mentionne clairement un échéancier de paiement ; sinon laisse le tableau 'conditions' vide. "
            . "Règle stricte: si une information n'est pas présente ou n'est pas certaine dans le texte, laisse le champ vide (chaîne vide, ou 0 pour les nombres). Ne devine jamais une valeur.";

        $messages = array(
            array("role" => "user", "content" => "Voici le texte extrait du document:\n\n" . mb_substr($text, 0, 15000))
        );

        $decoded = aiClaudeClient::call($systemPrompt, $messages, 2048);

        // avec le raisonnement étendu, le premier bloc peut être un bloc "thinking" :
        // on cherche le premier bloc de type "text" plutôt que de supposer l'index 0.
        $textBlock = aiClaudeClient::extractFirstTextBlock($decoded);
        if ($textBlock === null) {
            throw new Exception("Réponse IA invalide (aucun bloc texte trouvé): " . json_encode($decoded));
        }

        $jsonText = aiClaudeClient::extractJson($textBlock);

        $data = json_decode($jsonText, true);
        if (!is_array($data)) {
            throw new Exception("Réponse IA invalide (JSON non interprétable).");
        }
        if (!isset($data['client']) || !is_array($data['client'])) {
            $data['client'] = array();
        }
        if (!isset($data['services']) || !is_array($data['services'])) {
            $data['services'] = array();
        }
        if (!isset($data['conditions']) || !is_array($data['conditions'])) {
            $data['conditions'] = array();
        }

        return $data;
    }

    public static function extract($absolutePath)
    {
        $text = self::extractText($absolutePath);
        if (trim($text) === '') {
            throw new Exception("Impossible d'extraire du texte de ce PDF (fichier scanné/image non supporté).");
        }
        return self::extractStructuredData($text);
    }

    // Construit le(s) message(s) Claude pour un document donné : texte si un PDF a une
    // couche texte, sinon vision (PDF scanné en "document", image en "image"). Partagé par
    // toutes les extractions IA de fichier unique (CIN/CV, déclaration TVA...) pour éviter de
    // dupliquer ce branchement PDF texte / PDF vision / image à chaque nouvel usage.
    private static function construireMessagesDocument($absolutePath, $extension, $consigneTexte)
    {
        $extension = strtolower($extension);
        $mediaTypesImage = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp');

        if ($extension === 'pdf') {
            $texte = self::extractText($absolutePath);
            if (trim($texte) !== '') {
                return array(
                    array("role" => "user", "content" => "Voici le texte extrait du document:\n\n" . mb_substr($texte, 0, 15000))
                );
            }
            // PDF scanné (sans couche texte) : envoyé directement à Claude en vision.
            $base64 = base64_encode(file_get_contents($absolutePath));
            return array(array("role" => "user", "content" => array(
                array("type" => "document", "source" => array("type" => "base64", "media_type" => "application/pdf", "data" => $base64)),
                array("type" => "text", "text" => $consigneTexte)
            )));
        }

        if (isset($mediaTypesImage[$extension])) {
            $base64 = base64_encode(file_get_contents($absolutePath));
            return array(array("role" => "user", "content" => array(
                array("type" => "image", "source" => array("type" => "base64", "media_type" => $mediaTypesImage[$extension], "data" => $base64)),
                array("type" => "text", "text" => $consigneTexte)
            )));
        }

        throw new Exception("Format de fichier non supporté pour l'analyse IA.");
    }

    private static function appelerEtDecoder($systemPrompt, $messages, $maxTokens, $champsAttendus)
    {
        $decoded = aiClaudeClient::call($systemPrompt, $messages, $maxTokens);

        $textBlock = aiClaudeClient::extractFirstTextBlock($decoded);
        if ($textBlock === null) {
            throw new Exception("Réponse IA invalide (aucun bloc texte trouvé).");
        }

        $jsonText = aiClaudeClient::extractJson($textBlock);
        $data = json_decode($jsonText, true);
        if (!is_array($data)) {
            // Cas fréquent avec un document volumineux (ex: relevé bancaire avec beaucoup de
            // lignes) : la réponse est coupée avant la fin du JSON car elle atteint la limite
            // de tokens - Claude le signale via stop_reason="max_tokens". Un message dédié
            // évite de deviner à l'aveugle la cause d'un "JSON non interprétable" générique.
            if (isset($decoded['stop_reason']) && $decoded['stop_reason'] === 'max_tokens') {
                error_log('aiExtractor::appelerEtDecoder - réponse tronquée (max_tokens=' . $maxTokens . '). Extrait: ' . mb_substr($jsonText, -300));
                throw new Exception("Le document est trop volumineux pour être analysé en une fois (réponse de l'IA tronquée). Essayez avec un relevé couvrant une période plus courte, ou contactez le support pour augmenter la limite.");
            }
            error_log('aiExtractor::appelerEtDecoder - JSON non interprétable: ' . mb_substr($jsonText, 0, 500));
            throw new Exception("Réponse IA invalide (JSON non interprétable).");
        }

        foreach ($champsAttendus as $champ) {
            if (!isset($data[$champ])) {
                $data[$champ] = '';
            }
        }

        return $data;
    }

    // Pré-remplissage fiche employé depuis une carte CIN (le plus souvent une photo) ou un CV
    // (PDF) : contrairement à extract() ci-dessus qui suppose toujours du texte PDF, une CIN
    // est envoyée en image et doit passer par la vision de Claude plutôt que par PdfParser.
    public static function extractEmployeeInfo($absolutePath, $extension)
    {
        $schema = '{"prenom":"","nom":"","cin":"","adresse":"","ville":"","email":"","tel":"","fonction":""}';
        $systemPrompt = "Tu es un assistant qui extrait des informations d'identité depuis une carte d'identité nationale marocaine (CIN) ou un CV, pour pré-remplir la fiche d'un nouvel employé dans un CRM RH. "
            . "Réponds UNIQUEMENT avec un objet JSON valide, sans aucun texte avant ou après, sans balises markdown. "
            . "Le JSON doit respecter exactement ce schéma: " . $schema . " "
            . "Règle stricte: si une information n'est pas présente ou n'est pas certaine sur le document, laisse le champ vide. Ne devine jamais une valeur, n'invente jamais de donnée.";

        $messages = self::construireMessagesDocument($absolutePath, $extension, "Analyse ce document (CIN ou CV) et extrait les informations demandées.");

        return self::appelerEtDecoder($systemPrompt, $messages, 1024, array('prenom', 'nom', 'cin', 'adresse', 'ville', 'email', 'tel', 'fonction'));
    }

    // Pré-remplissage d'une déclaration de TVA depuis le document envoyé par le comptable
    // (montant dû, majoration éventuelle, périodicité et mois/trimestre/année concernés).
    // Les vraies déclarations DGI marocaines portent un en-tête explicite "Déclaration
    // Mensuelle" ou "Déclaration Trimestrielle" ; dans ce dernier cas, le champ "Période" du
    // document désigne le numéro du TRIMESTRE (1 à 4), pas un mois — confirmé sur les
    // déclarations Verse Concept 2024 traitées manuellement dans le CRM.
    public static function extractTvaDeclaration($absolutePath, $extension)
    {
        $schema = '{"montant":"","majoration":"","periodicite":"","mois":"","trimestre":"","annee":""}';
        $systemPrompt = "Tu es un assistant qui extrait les informations d'une déclaration de TVA marocaine (document envoyé par un comptable), pour pré-remplir un CRM. "
            . "Réponds UNIQUEMENT avec un objet JSON valide, sans aucun texte avant ou après, sans balises markdown. "
            . "Le JSON doit respecter exactement ce schéma: " . $schema . " "
            . "'montant' est le montant net de TVA dû (à payer), en dirhams, sans le symbole monétaire (juste le nombre, ex: '1850.50'). "
            . "'majoration' est une éventuelle pénalité/majoration de retard mentionnée, sinon laisse vide. "
            . "'periodicite' vaut 'mensuel' si l'en-tête du document indique 'Déclaration Mensuelle', ou 'trimestriel' si l'en-tête indique 'Déclaration Trimestrielle'. "
            . "Si periodicite='mensuel': 'mois' est le numéro du mois concerné (1 à 12, sans zéro devant, lu dans le champ 'Période'), laisse 'trimestre' vide. "
            . "Si periodicite='trimestriel': 'trimestre' est le numéro du trimestre (1 à 4, lu dans le champ 'Période'), laisse 'mois' vide. "
            . "'annee' est l'année sur 4 chiffres. "
            . "Règle stricte: si une information n'est pas présente ou n'est pas certaine sur le document, laisse le champ vide. Ne devine jamais une valeur, n'invente jamais de donnée.";

        $messages = self::construireMessagesDocument($absolutePath, $extension, "Analyse cette déclaration de TVA et extrait les informations demandées.");

        return self::appelerEtDecoder($systemPrompt, $messages, 1024, array('montant', 'majoration', 'periodicite', 'mois', 'trimestre', 'annee'));
    }

    // Pré-remplissage d'une charge depuis N'IMPORTE QUEL document déposé sur la page Charges :
    // bulletin de paie, déclaration CNSS/TVA, bilan comptable, taxe professionnelle, ou une
    // simple facture/quittance. Un seul appel IA classifie D'ABORD le type de document, puis
    // extrait les champs pertinents pour ce type — évite un aller-retour supplémentaire pour
    // classifier avant d'extraire. Le contrôleur (com_ia/controleurs/router.php) se charge
    // ensuite, selon 'type_document', du matching employé (bulletin_paie) ou de la
    // vérification anti-doublon (cnss/tva/bilan/taxe_professionnelle).
    public static function extractChargeDocument($absolutePath, $extension)
    {
        $schema = '{"type_document":"","montant":"","mois":"","annee":"","nom":"","prenom":"","cin":"","titre_suggere":""}';
        $systemPrompt = "Tu es un assistant qui analyse un justificatif de charge d'entreprise marocaine, pour pré-remplir automatiquement une charge dans un CRM. "
            . "Réponds UNIQUEMENT avec un objet JSON valide, sans aucun texte avant ou après, sans balises markdown. "
            . "Le JSON doit respecter exactement ce schéma: " . $schema . " "
            . "Détermine D'ABORD 'type_document' parmi exactement ces valeurs: "
            . "'bulletin_paie' (bulletin de salaire d'un employé), "
            . "'cnss' (déclaration/attestation de cotisations CNSS), "
            . "'tva' (déclaration de TVA), "
            . "'bilan' (bilan comptable annuel), "
            . "'taxe_professionnelle' (taxe professionnelle / patente), "
            . "'autre' (facture, quittance, ou tout autre justificatif de charge qui n'est aucun des cas ci-dessus). "
            . "Puis remplis les champs pertinents SEULEMENT pour ce type: "
            . "si 'bulletin_paie': 'nom', 'prenom' (du salarié), 'cin' (si mentionné), 'montant' (le NET A PAYER, pas le brut), 'mois' (1-12), 'annee'. "
            . "si 'cnss' ou 'tva': 'montant' (montant dû/déclaré), 'mois' (1-12), 'annee' — laisse 'nom'/'prenom'/'cin' vides. "
            . "si 'bilan' ou 'taxe_professionnelle': 'montant', 'annee' seulement (pas de mois, ces déclarations sont annuelles) — laisse 'mois' vide. "
            . "si 'autre': 'montant', 'titre_suggere' (un titre court résumant le document, ex: 'Facture Orange Juin 2026'), et 'annee'/'mois' si une date claire apparaît. "
            . "'montant' est toujours en dirhams, sans symbole monétaire (juste le nombre, ex: '4500.00'). "
            . "Règle stricte: si une information n'est pas présente ou n'est pas certaine sur le document, laisse le champ vide. Ne devine jamais une valeur, n'invente jamais de donnée.";

        $messages = self::construireMessagesDocument($absolutePath, $extension, "Analyse ce document et extrait les informations demandées.");

        return self::appelerEtDecoder($systemPrompt, $messages, 1024, array('type_document', 'montant', 'mois', 'annee', 'nom', 'prenom', 'cin', 'titre_suggere'));
    }

    // Extraction des lignes d'un relevé bancaire PDF (Rapprochement Bancaire) : contrairement aux
    // autres extracteurs ci-dessus qui remplissent un seul enregistrement, un relevé contient de
    // nombreuses transactions - le schéma retourne donc un TABLEAU de lignes plutôt qu'un objet
    // unique, et le budget de tokens est nettement plus généreux pour couvrir un relevé complet.
    public static function extractReleveBancaire($absolutePath, $extension)
    {
        $schema = '{"banque_detectee":"","rib_detecte":"","iban_detecte":"","transactions":[{"date":"JJ/MM/AAAA","libelle":"","debit":"","credit":""}]}';
        $systemPrompt = "Tu es un assistant qui extrait les lignes d'un relevé bancaire marocain (BCP, BMCE, ou autre), pour alimenter un module de rapprochement bancaire dans un CRM. "
            . "Réponds UNIQUEMENT avec un objet JSON valide, sans aucun texte avant ou après, sans balises markdown. "
            . "Le JSON doit respecter exactement ce schéma: " . $schema . " "
            . "'banque_detectee' est le nom de la banque émettrice du relevé (ex: 'Banque Populaire', 'BMCE Bank'), lu sur l'en-tête/logo du document. "
            . "'rib_detecte' et 'iban_detecte' sont le RIB et/ou l'IBAN du titulaire du compte s'ils apparaissent sur le relevé (sinon laisse vide) - ils servent à identifier automatiquement le compte concerné, sans que l'utilisateur ait à le sélectionner manuellement. "
            . "'transactions' est la liste de TOUTES les opérations du relevé, une entrée par ligne. "
            . "'date' est la date de l'opération au format JJ/MM/AAAA. "
            . "'libelle' est l'intitulé/la description de l'opération telle qu'écrite sur le relevé (nom du bénéficiaire/émetteur, référence). "
            . "'debit' est le montant débité (sortie d'argent), en dirhams, sans symbole monétaire (juste le nombre, ex: '450.00') - laisse vide si l'opération est un crédit. "
            . "'credit' est le montant crédité (entrée d'argent), même règle - laisse vide si l'opération est un débit. "
            . "Une ligne ne peut jamais avoir à la fois 'debit' ET 'credit' remplis. "
            . "N'inclus PAS le solde initial/final ni les lignes de total/récapitulatif - uniquement les opérations individuelles. "
            . "Règle stricte: si une information (banque, RIB, IBAN, ligne ambiguë) n'est pas clairement lisible, laisse-la vide/ignore la ligne plutôt que d'inventer des valeurs. Ne devine jamais un montant, une date, ou une identité de banque.";

        $messages = self::construireMessagesDocument($absolutePath, $extension, "Analyse ce relevé bancaire et extrait toutes les transactions.");

        $decoded = self::appelerEtDecoder($systemPrompt, $messages, 8192, array('banque_detectee', 'rib_detecte', 'iban_detecte', 'transactions'));
        if (!isset($decoded['transactions']) || !is_array($decoded['transactions'])) {
            $decoded['transactions'] = array();
        }
        return $decoded;
    }
}
