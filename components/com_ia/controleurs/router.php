<?php

require_once ("../../../config.php");
require_once ("../../../instanceDb.php");
require_once ("../../../includes/functions/functions.php");
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    @$task = $_GET['task'];
    switch ($task) {
        case 'extractPresentation':
            extractPresentation($_GET);
            break;
        case 'chatServiceAssistant':
            chatServiceAssistant($_POST);
            break;
        case 'generateClientSummary':
            generateClientSummary($_POST);
            break;
        case 'extractEmployeeDocument':
            extractEmployeeDocument($_FILES);
            break;
        case 'extractTvaDeclaration':
            extractTvaDeclaration($_FILES);
            break;
        case 'extractChargeDocument':
            extractChargeDocument($_FILES);
            break;
        case 'extractChatDevis':
            extractChatDevis($_POST);
            break;
        case 'rechercheClientChatDevis':
            rechercheClientChatDevis($_GET);
            break;
    }
}

// Recherche manuelle d'un autre client depuis l'étape de validation du chat devis (le client
// identifié automatiquement n'était pas le bon) - même forme de réponse que client_matches dans
// extractChatDevis(), cross-agence comme le reste de ce workflow.
function rechercheClientChatDevis($params)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('view', 'com_client')) {
        echo json_encode(array('success' => 0, 'matches' => array()));
        return;
    }

    $terme = isset($params['q']) ? trim($params['q']) : '';
    if (mb_strlen($terme) < 2) {
        echo json_encode(array('success' => 1, 'matches' => array()));
        return;
    }

    $matches = array();
    foreach (client::search($terme, false) as $c) {
        $nom = trim((string) $c->getRaisonSocial()) !== '' ? $c->getRaisonSocial() : trim($c->getPrenom() . ' ' . $c->getNom());
        $matches[] = array(
            'id' => $c->getId(),
            'nom' => $nom !== '' ? $nom : '(sans nom)',
            'email' => (string) $c->getEmail(),
            'tel' => (string) $c->getTel(),
            'agence_id' => $c->getAgence() ? $c->getAgence()->getId() : null,
            'agence_nom' => $c->getAgence() ? $c->getAgence()->getNom() : ''
        );
    }

    echo json_encode(array('success' => 1, 'matches' => $matches));
}

// Correspondance titre extrait -> service existant (id_service si correspondance exacte,
// suggested_service si seulement une forte ressemblance) - factorisé car identique à celle
// d'extractPresentation() ci-dessous, seule la source du texte diffère (chat vs PDF déposé).
function com_ia_matcherServicesExistants($extracted)
{
    $servicesExistants = service::findAll($_SESSION['langue'], true, false, true);
    $seuilSuggestion = 55; // % de similarité minimum pour proposer un service existant comme correspondance probable

    foreach ($extracted['services'] as &$ligne) {
        $ligne['id_service'] = null;
        $ligne['suggested_service'] = null;
        $titreLigne = trim(mb_strtolower(isset($ligne['titre']) ? $ligne['titre'] : ''));
        if ($titreLigne === '') {
            continue;
        }

        $meilleurScore = 0;
        $meilleurService = null;
        foreach ($servicesExistants as $s) {
            $titreExistant = trim(mb_strtolower($s->getTitre()));
            if ($titreExistant === $titreLigne) {
                $ligne['id_service'] = $s->getId();
                $ligne['unite'] = $s->getUnite();
                $meilleurService = null;
                break;
            }
            similar_text($titreExistant, $titreLigne, $score);
            if ($score > $meilleurScore) {
                $meilleurScore = $score;
                $meilleurService = $s;
            }
        }

        if (!$ligne['id_service'] && $meilleurService && $meilleurScore >= $seuilSuggestion) {
            $ligne['suggested_service'] = array(
                'id_service' => $meilleurService->getId(),
                'titre' => $meilleurService->getTitre(),
                'unite' => $meilleurService->getUnite(),
                'score' => round($meilleurScore)
            );
        }
    }
    unset($ligne);

    return $extracted;
}

// Module 4 (Assistant IA devis) : point d'entrée du chat "décrivez le besoin" - même extraction
// structurée que le dépôt de présentation PDF (aiExtractor::extractStructuredData), appliquée
// directement au texte tapé par l'utilisateur au lieu du texte d'un fichier. En plus des
// prestations, cherche des clients existants correspondant au client mentionné, pour l'étape de
// validation demandée avant toute génération de devis (jamais de création silencieuse).
function extractChatDevis($params)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('add', 'com_devis')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    $message = isset($params['message']) ? trim($params['message']) : '';
    if ($message === '') {
        echo json_encode(array('success' => 0, 'message' => 'Merci de décrire le besoin.'));
        return;
    }

    try {
        $extracted = aiExtractor::extractStructuredData($message);
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    $extracted = com_ia_matcherServicesExistants($extracted);

    $clientInfo = $extracted['client'];
    $terme = trim((string) (isset($clientInfo['raison_social']) ? $clientInfo['raison_social'] : ''));
    if ($terme === '') {
        $terme = trim(trim((string) (isset($clientInfo['prenom']) ? $clientInfo['prenom'] : '')) . ' ' . trim((string) (isset($clientInfo['nom']) ? $clientInfo['nom'] : '')));
    }
    if ($terme === '') {
        $terme = trim((string) (isset($clientInfo['email']) ? $clientInfo['email'] : ''));
    }

    $clientMatches = array();
    if ($terme !== '' && $_SESSION['user']->hasDroit('view', 'com_client')) {
        foreach (client::search($terme, false) as $c) {
            $nom = trim((string) $c->getRaisonSocial()) !== '' ? $c->getRaisonSocial() : trim($c->getPrenom() . ' ' . $c->getNom());
            $clientMatches[] = array(
                'id' => $c->getId(),
                'nom' => $nom !== '' ? $nom : '(sans nom)',
                'email' => (string) $c->getEmail(),
                'tel' => (string) $c->getTel(),
                'agence_id' => $c->getAgence() ? $c->getAgence()->getId() : null,
                'agence_nom' => $c->getAgence() ? $c->getAgence()->getNom() : ''
            );
        }
    }

    echo json_encode(array(
        'success' => 1,
        'extracted' => $extracted,
        'client_recherche' => $terme,
        'client_matches' => $clientMatches
    ));
}

function extractEmployeeDocument($files)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    if (!isset($files['document']) || $files['document']['name'] == '') {
        echo json_encode(array('success' => 0, 'message' => 'Aucun fichier reçu'));
        return;
    }

    if ($files['document']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(array('success' => 0, 'message' => "Erreur lors du téléversement du fichier"));
        return;
    }

    $extension = strtolower(pathinfo($files['document']['name'], PATHINFO_EXTENSION));
    $extensionsAutorisees = array('pdf', 'jpg', 'jpeg', 'png', 'webp');
    if (!in_array($extension, $extensionsAutorisees)) {
        echo json_encode(array('success' => 0, 'message' => 'Format non supporté (PDF, JPG, PNG, WEBP uniquement)'));
        return;
    }

    try {
        $extracted = aiExtractor::extractEmployeeInfo($files['document']['tmp_name'], $extension);
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    echo json_encode(array('success' => 1, 'extracted' => $extracted));
}

function extractTvaDeclaration($files)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('add', 'com_accounting')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    if (!isset($files['document']) || $files['document']['name'] == '') {
        echo json_encode(array('success' => 0, 'message' => 'Aucun fichier reçu'));
        return;
    }

    if ($files['document']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(array('success' => 0, 'message' => "Erreur lors du téléversement du fichier"));
        return;
    }

    $extension = strtolower(pathinfo($files['document']['name'], PATHINFO_EXTENSION));
    $extensionsAutorisees = array('pdf', 'jpg', 'jpeg', 'png', 'webp');
    if (!in_array($extension, $extensionsAutorisees)) {
        echo json_encode(array('success' => 0, 'message' => 'Format non supporté (PDF, JPG, PNG, WEBP uniquement)'));
        return;
    }

    try {
        $extracted = aiExtractor::extractTvaDeclaration($files['document']['tmp_name'], $extension);
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    echo json_encode(array('success' => 1, 'extracted' => $extracted));
}

// Nom des mois en français, pour composer les libellés de doublon ci-dessous.
function nomMoisChargeDocument($numero)
{
    $noms = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
    return isset($noms[$numero]) ? $noms[$numero] : '';
}

// Dropzone universelle de la page Charges : accepte n'importe quel justificatif (bulletin de
// paie, déclaration CNSS/TVA, bilan, taxe professionnelle, facture...). L'IA classifie
// d'abord le type de document (aiExtractor::extractChargeDocument()), puis selon ce type :
// - bulletin_paie : tentative de rapprochement employé (CIN exacte, sinon similarité nom/
//   prénom), exactement comme avant.
// - cnss/tva/bilan/taxe_professionnelle : vérifie qu'une déclaration pour la même période
//   n'existe pas déjà dans le module de gestion correspondant, pour éviter les doublons —
//   ces 4 types ne créent JAMAIS d'enregistrement automatique dans leur module (contrairement
//   au bulletin de paie), seulement une alerte si un doublon est trouvé.
function extractChargeDocument($files)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('add', 'com_charge')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    if (!isset($files['document']) || $files['document']['name'] == '') {
        echo json_encode(array('success' => 0, 'message' => 'Aucun fichier reçu'));
        return;
    }

    if ($files['document']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(array('success' => 0, 'message' => "Erreur lors du téléversement du fichier"));
        return;
    }

    $extension = strtolower(pathinfo($files['document']['name'], PATHINFO_EXTENSION));
    $extensionsAutorisees = array('pdf', 'jpg', 'jpeg', 'png', 'webp');
    if (!in_array($extension, $extensionsAutorisees)) {
        echo json_encode(array('success' => 0, 'message' => 'Format non supporté (PDF, JPG, PNG, WEBP uniquement)'));
        return;
    }

    try {
        $extracted = aiExtractor::extractChargeDocument($files['document']['tmp_name'], $extension);
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    $typeDocument = isset($extracted['type_document']) ? $extracted['type_document'] : 'autre';
    $agence = $_SESSION['agence'];

    $employeMatch = null;
    $doublon = null;

    if ($typeDocument === 'bulletin_paie') {
        // Rapprochement employé : CIN exacte d'abord (confiance haute, l'utilisateur voit/
        // valide côté formulaire avant soumission — jamais d'auto-liaison silencieuse), sinon
        // similarité nom+prénom (même mécanique que le rapprochement de services plus haut
        // dans ce fichier, seuil plus élevé car une mauvaise liaison toucherait un dossier de paie).
        $cin = trim(isset($extracted['cin']) ? $extracted['cin'] : '');
        if ($cin !== '') {
            $parCin = resourcehumaine::findByCin($cin);
            if ($parCin && $parCin->getId() > 0) {
                $employeMatch = array('id' => $parCin->getId(), 'nom_complet' => $parCin->getFullName(), 'confiance' => 'haute');
            }
        }

        if ($employeMatch === null) {
            $nomLigne = trim(mb_strtolower(isset($extracted['nom']) ? $extracted['nom'] : ''));
            $prenomLigne = trim(mb_strtolower(isset($extracted['prenom']) ? $extracted['prenom'] : ''));
            if ($nomLigne !== '' || $prenomLigne !== '') {
                $seuilSuggestion = 65;
                $meilleurScore = 0;
                $meilleurEmploye = null;
                foreach (resourcehumaine::findAll() as $employe) {
                    $nomComplet = trim(mb_strtolower($employe->getFirstName() . ' ' . $employe->getLastName()));
                    $cible = trim($prenomLigne . ' ' . $nomLigne);
                    similar_text($nomComplet, $cible, $score);
                    if ($score > $meilleurScore) {
                        $meilleurScore = $score;
                        $meilleurEmploye = $employe;
                    }
                }
                if ($meilleurEmploye && $meilleurScore >= $seuilSuggestion) {
                    $employeMatch = array('id' => $meilleurEmploye->getId(), 'nom_complet' => $meilleurEmploye->getFullName(), 'confiance' => 'basse');
                }
            }
        }
    } elseif (in_array($typeDocument, array('cnss', 'tva', 'bilan', 'taxe_professionnelle'))) {
        $annee = isset($extracted['annee']) ? trim($extracted['annee']) : '';
        $mois = isset($extracted['mois']) ? trim($extracted['mois']) : '';

        if ($typeDocument === 'cnss' && $annee !== '' && $mois !== '') {
            $periode = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT);
            $existants = cnss::findByDate($agence, $periode);
            if (!empty($existants)) {
                $doublon = array(
                    'module' => 'CNSS',
                    'libelle' => 'Une déclaration CNSS de ' . nomMoisChargeDocument((int) $mois) . ' ' . $annee . ' existe déjà.',
                    'montant_existant' => (float) $existants[0]->getAmount(),
                    'lien' => 'index.php?option=com_accounting&task=cnss'
                );
            }
        } elseif ($typeDocument === 'tva' && $annee !== '' && $mois !== '') {
            $periode = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT);
            $existants = tva::findByDate($agence, $periode);
            if (!empty($existants)) {
                $doublon = array(
                    'module' => 'TVA',
                    'libelle' => 'Une déclaration TVA de ' . nomMoisChargeDocument((int) $mois) . ' ' . $annee . ' existe déjà.',
                    'montant_existant' => (float) $existants[0]->getAmount(),
                    'lien' => 'index.php?option=com_accounting&task=tva'
                );
            }
        } elseif ($typeDocument === 'bilan' && $annee !== '') {
            $existants = bilan::findByYear($agence, $annee);
            if (!empty($existants)) {
                $doublon = array(
                    'module' => 'Bilan',
                    'libelle' => 'Un bilan comptable pour ' . $annee . ' existe déjà.',
                    'montant_existant' => (float) $existants[0]->getAmount(),
                    'lien' => 'index.php?option=com_accounting&task=bilan'
                );
            }
        } elseif ($typeDocument === 'taxe_professionnelle' && $annee !== '') {
            $existants = taxeprofessionnelle::findByYear($agence, $annee);
            if (!empty($existants)) {
                $doublon = array(
                    'module' => 'Taxe professionnelle',
                    'libelle' => 'Une taxe professionnelle pour ' . $annee . ' existe déjà.',
                    'montant_existant' => (float) $existants[0]->getAmount(),
                    'lien' => 'index.php?option=com_accounting&task=taxeprofessionnelle'
                );
            }
        }
    }

    echo json_encode(array('success' => 1, 'extracted' => $extracted, 'employe_match' => $employeMatch, 'doublon' => $doublon));
}

function generateClientSummary($data)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('edit', 'com_client')) {
        echo json_encode(array('icon' => 'error', 'message' => 'Accès refusé'));
        return;
    }

    $idClient = isset($data['client']) ? intval($data['client']) : 0;
    $client = client::find($idClient, $_SESSION['agence']);
    if (!$client || !$client->getId()) {
        echo json_encode(array('icon' => 'error', 'message' => 'Client introuvable'));
        return;
    }

    try {
        $recap = aiClientSummary::generate($client);
    } catch (Exception $e) {
        echo json_encode(array('icon' => 'error', 'message' => $e->getMessage()));
        return;
    }

    $client->updateIaRecap($recap);
    echo json_encode(array('icon' => 'success', 'recap' => $recap));
}

function chatServiceAssistant($data)
{
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || (!$_SESSION['user']->hasDroit('edit', 'com_service') && !$_SESSION['user']->hasDroit('add', 'com_service'))) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    // id_service vaut 0 quand le service n'existe pas encore (création rapide depuis un devis/facture) :
    // dans ce cas on se base sur le titre/la description actuellement saisis dans le formulaire, pas sur la base.
    $id_service = isset($data['id_service']) ? intval($data['id_service']) : 0;
    $message = isset($data['message']) ? trim($data['message']) : '';
    $titreActuel = isset($data['titre']) ? $data['titre'] : '';
    $descriptionActuelle = isset($data['description']) ? $data['description'] : '';

    if ($message === '') {
        echo json_encode(array('success' => 0, 'message' => 'Message vide'));
        return;
    }

    try {
        $interpretation = aiServiceAssistant::interpretRequest($message, $titreActuel, $descriptionActuelle);
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    $intent = isset($interpretation['intent']) ? $interpretation['intent'] : 'unknown';

    if ($intent === 'update_description') {
        echo json_encode(array(
            'success' => 1,
            'intent' => 'update_description',
            'proposed_description' => isset($interpretation['proposed_description']) ? $interpretation['proposed_description'] : ''
        ));
        return;
    }

    if ($intent === 'scan_website') {
        $url = isset($interpretation['url']) ? trim($interpretation['url']) : '';
        if ($url === '') {
            echo json_encode(array('success' => 1, 'intent' => 'need_url'));
            return;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; HW-CRM-Bot/1.0)',
            CURLOPT_ENCODING => ''
        ));
        $html = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($html === false || $html === '') {
            echo json_encode(array('success' => 0, 'message' => "Impossible de récupérer le site (" . $curlError . ")"));
            return;
        }

        try {
            $pagesResult = aiServiceAssistant::extractPagesFromHtml($url, $html);
        } catch (Exception $e) {
            echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
            return;
        }

        echo json_encode(array(
            'success' => 1,
            'intent' => 'scan_website',
            'url' => $url,
            'pages' => isset($pagesResult['pages']) ? $pagesResult['pages'] : array()
        ));
        return;
    }

    echo json_encode(array(
        'success' => 1,
        'intent' => 'unknown',
        'message' => "Je n'ai pas compris la demande. Essayez par exemple : \"Améliore la description de ce service\" ou \"Scanne https://exemple.com et liste les pages du menu\"."
    ));
}

function extractPresentation($params)
{
    header('Content-Type: application/json');

    $modulesAutorises = array('client' => 'com_client', 'devis' => 'com_devis', 'facture' => 'com_facture');
    $context = isset($params['context']) ? $params['context'] : '';

    if (!isset($modulesAutorises[$context])) {
        echo json_encode(array('success' => 0, 'message' => 'Contexte invalide'));
        return;
    }

    $module = $modulesAutorises[$context];
    if (!isset($_SESSION['user']) || !$_SESSION['user']->hasDroit('add', $module)) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    if (!isset($_FILES['presentation']) || $_FILES['presentation']['name'][0] == '') {
        echo json_encode(array('success' => 0, 'message' => 'Aucun fichier reçu'));
        return;
    }

    $fichiers = uploadFiles('presentation', '../../../images/presentations/', array('pdf', 'PDF'));
    if (!isset($fichiers[0])) {
        echo json_encode(array('success' => 0, 'message' => "Échec de l'upload (seul le format PDF est accepté)"));
        return;
    }
    $fichier = $fichiers[0];
    $absolutePath = realpath("../../../images/presentations/" . $fichier);

    try {
        $extracted = aiExtractor::extract($absolutePath);
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage(), 'fichier' => $fichier));
        return;
    }

    $extracted = com_ia_matcherServicesExistants($extracted);

    echo json_encode(array(
        'success' => 1,
        'fichier' => $fichier,
        'extracted' => $extracted
    ));
}
