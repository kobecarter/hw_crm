<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case "generateJobOfferAI":
            generateJobOfferAI($_POST);
            break;
        case "submitJobOffer":
            submitJobOffer($_POST);
            break;
        case "marquerOffreSignee":
            marquerOffreSignee($_POST);
            break;
        case "deleteJobOffer":
            deleteJobOffer($_POST);
            break;
        case "telechargerOffreWord":
            telechargerOffreWord($_GET);
            break;
        case "telechargerOffrePDF":
            telechargerOffrePDF($_GET);
            break;
        case "validerOffreManuellement":
            validerOffreManuellement($_POST);
            break;
    }
}

// Validation manuelle depuis le CRM (sans passer par Slack) : même effet que la validation
// détectée par cronVerifierValidationSlackEndpoint() ci-dessous - statut, jeton d'acceptation,
// email au candidat avec la liste des documents requis - mais déclenchée directement par un
// utilisateur ayant les droits, pour ne pas dépendre uniquement d'une réponse Slack.
function validerOffreManuellement($data)
{
    header('Content-Type: application/json');
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Offre invalide.'));
        return;
    }
    $offer = joboffer::find($data['id']);
    if ($offer->getId() == 0) {
        echo json_encode(array('success' => 0, 'message' => 'Offre introuvable.'));
        return;
    }
    if ($offer->getStatut() !== joboffer::STATUT_EN_ATTENTE_SLACK) {
        echo json_encode(array('success' => 0, 'message' => 'Cette offre n\'est pas (ou plus) en attente de validation.'));
        return;
    }

    $acteur = isset($_SESSION['user']) ? $_SESSION['user']->getName() . ' (validation manuelle CRM)' : 'Validation manuelle (CRM)';
    $offer->setSlackValidatedBy($acteur);
    $offer->setSlackValidatedAt(date('Y-m-d H:i:s'));
    $offer->setStatut(joboffer::STATUT_VALIDEE);
    $token = bin2hex(random_bytes(32));
    $offer->setAcceptanceToken($token);
    $offer->edit();

    $emailEnvoye = envoyerEmailOffreValideeAuCandidat($offer, $token);

    echo json_encode(array('success' => 1, 'email_candidat_envoye' => $emailEnvoye));
}

function deleteJobOffer($data)
{
    header('Content-Type: application/json');
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Offre invalide.'));
        return;
    }
    $offer = joboffer::find($data['id']);
    if (!$offer->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Offre introuvable.'));
        return;
    }
    if ($offer->getSignedFile() && file_exists('../../../images/resourceshumaines/offres/' . $offer->getSignedFile())) {
        @unlink('../../../images/resourceshumaines/offres/' . $offer->getSignedFile());
    }
    if ($offer->delete() == 1) {
        echo json_encode(array('success' => 1));
    } else {
        echo json_encode(array('success' => 0, 'message' => 'Erreur lors de la suppression.'));
    }
}

// Export "Word" de l'offre : pas de vraie génération .docx (aucune librairie PhpWord installée,
// seule phpoffice/phpspreadsheet l'est - voir composer.json), mais Word ouvre nativement un fichier
// HTML servi avec l'extension .doc et le bon Content-Type, en conservant la mise en forme (gras,
// listes, titres) - technique standard, aucune dépendance supplémentaire nécessaire. Le candidat
// peut alors éditer le document dans Word comme n'importe quel .doc.
function telechargerOffreWord($data)
{
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        http_response_code(400);
        echo 'Offre invalide.';
        return;
    }
    $offer = joboffer::find($data['id']);
    if (!$offer->getId()) {
        http_response_code(404);
        echo 'Offre introuvable.';
        return;
    }
    $nomComplet = trim($offer->getResourcehumaine()->getFirstName() . ' ' . $offer->getResourcehumaine()->getLastName());
    $nomFichier = 'Offre emploi - ' . $nomComplet . '.doc';
    $nomFichier = str_replace(array('/', '\\'), '-', $nomFichier);

    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nomFichier . '"');
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">'
        . '<head><meta charset="utf-8"><title>' . htmlspecialchars($nomFichier) . '</title></head>'
        . '<body>' . $offer->getContenu() . '</body></html>';
}

// PDF fidèle au modèle de référence : en-tête (logo+adresse+tel/email/site) et pied de page
// (IF/TP/RC/ICE) RÉPÉTÉS SUR CHAQUE PAGE via mPDF (joboffer::mpdfHeaderFooterBlock()), contrairement
// à l'ancien bouton "Imprimer" qui ouvrait juste le contenu dans une fenêtre pour impression
// navigateur (aucun en-tête/pied répété). Remplace ce bouton comme action principale de
// téléchargement ; ouvert en ligne (inline) pour prévisualisation avant impression/enregistrement.
function telechargerOffrePDF($data)
{
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        http_response_code(400);
        echo 'Offre invalide.';
        return;
    }
    $offer = joboffer::find($data['id']);
    if (!$offer->getId()) {
        http_response_code(404);
        echo 'Offre introuvable.';
        return;
    }

    require_once '../../../vendor/autoload.php';
    $resourcehumaine = $offer->getResourcehumaine();
    $agence = $resourcehumaine->getAgency();
    $nomComplet = trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName());

    $html = '<html><head><style>body{font-family: Calibri, Arial, sans-serif;}</style></head><body>'
        . joboffer::mpdfHeaderFooterBlock($agence)
        . $offer->getContenu()
        . '</body></html>';

    try {
        $mpdf = new \Mpdf\Mpdf(['margin_top' => 35, 'margin_header' => 8, 'margin_footer' => 12, 'margin_bottom' => 20]);
        $mpdf->WriteHTML($html);
        $nomFichier = str_replace(array('/', '\\'), '-', 'Offre emploi - ' . $nomComplet . '.pdf');
        $mpdf->Output($nomFichier, \Mpdf\Output\Destination::INLINE);
    } catch (\Exception $e) {
        http_response_code(500);
        echo 'Erreur lors de la génération du PDF : ' . htmlspecialchars($e->getMessage());
    }
}

// Repli manuel tant que SignWell n'est pas branché (voir config.secrets.php) : l'admin dépose le
// document signé en dehors du CRM (papier scanné, ou signature via un autre outil) et le CRM
// passe simplement l'offre au statut "signée" avec ce fichier attaché - même finalité que le
// webhook SignWell final, déclenchée à la main en attendant.
function marquerOffreSignee($data)
{
    header('Content-Type: application/json');
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Offre invalide.'));
        return;
    }
    if (!isset($_FILES['signed_file']) || $_FILES['signed_file']['name'][0] == '') {
        echo json_encode(array('success' => 0, 'message' => 'Merci de joindre le document signé.'));
        return;
    }
    $offer = joboffer::find($data['id']);
    if (!$offer->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Offre introuvable.'));
        return;
    }

    $files = uploadFiles('signed_file', '../../../images/resourceshumaines/offres', array('PDF', 'pdf', 'jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'));
    if (empty($files[0])) {
        echo json_encode(array('success' => 0, 'message' => 'Erreur lors du dépôt du fichier.'));
        return;
    }

    $offer->setStatut(joboffer::STATUT_SIGNEE);
    $offer->setSignedFile($files[0]);
    $offer->setSignedAt(date('Y-m-d H:i:s'));
    if ($offer->edit() == 1) {
        echo json_encode(array('success' => 1));
    } else {
        echo json_encode(array('success' => 0, 'message' => 'Erreur lors de l\'enregistrement.'));
    }
}

// Point d'intégration SignWell (Module 1.4) : tant que SIGNWELL_API_KEY n'est pas configurée,
// no-op silencieux (même convention que Google Drive ailleurs dans ce projet) - retourne false
// sans lever d'erreur, l'appelant doit alors proposer le repli manuel (marquerOffreSignee ci-dessus).
// À implémenter dès que la clé est disponible : POST https://www.signwell.com/api/v1/documents/
// avec le HTML de $offer->getContenu() en pièce à signer, puis stocker l'id retourné dans
// signwell_document_id et passer le statut à STATUT_ENVOYEE_SIGNATURE ; un webhook SignWell séparé
// (à créer, même patron que les endpoints cron ci-dessous : public + secret) recevra le statut de
// signature et appellera l'équivalent de marquerOffreSignee() automatiquement.
function envoyerOffrePourSignatureSignWell($offer)
{
    if (!defined('SIGNWELL_API_KEY') || SIGNWELL_API_KEY == '') {
        return false;
    }
    // TODO une fois la clé configurée.
    return false;
}

function generateJobOfferAI($data)
{
    header('Content-Type: application/json');
    $indices = array("id_resourcehumaine", "periode_essai", "salaire", "conditions_paiement");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Champs obligatoires manquants (période d\'essai, salaire, conditions de paiement).'));
        return;
    }
    $resourcehumaine = resourcehumaine::find($data['id_resourcehumaine']);
    if (!$resourcehumaine->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Employé introuvable.'));
        return;
    }
    try {
        // Seules ces deux sections sont rédigées par l'IA (spécifiques au poste) - tout le reste
        // de la lettre (en-tête société, mentions légales, salaire, horaires, politiques,
        // confidentialité, signature) est un modèle fixe assemblé de façon déterministe par
        // joboffer::construireLettreHTML(), jamais généré/altéré par l'IA.
        $fiche = aiJobOfferAssistant::genererObjectifEtBrief($resourcehumaine->getFunction(), $_SESSION['langue']);
        $html = joboffer::construireLettreHTML(
            $resourcehumaine,
            $data['periode_essai'],
            $data['salaire'],
            $data['conditions_paiement'],
            isset($data['commissions']) ? $data['commissions'] : '',
            $fiche['objectif'],
            $fiche['brief'],
            $_SESSION['langue']
        );
        echo json_encode(array('success' => 1, 'contenu' => $html));
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
    }
}

function submitJobOffer($data)
{
    header('Content-Type: application/json');
    $indices = array("id_resourcehumaine", "periode_essai", "salaire", "conditions_paiement", "contenu");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Champs obligatoires manquants.'));
        return;
    }
    $resourcehumaine = resourcehumaine::find($data['id_resourcehumaine']);
    if (!$resourcehumaine->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Employé introuvable.'));
        return;
    }

    $offer = new joboffer();
    $offer->setResourcehumaine($resourcehumaine);
    $offer->setPeriodeEssai($data['periode_essai']);
    $offer->setSalaire($data['salaire']);
    $offer->setConditionsPaiement($data['conditions_paiement']);
    $offer->setCommissions(isset($data['commissions']) ? $data['commissions'] : '');
    $offer->setContenu($data['contenu']);
    $offer->setStatut(joboffer::STATUT_EN_ATTENTE_SLACK);
    $offer->setUserAdded($_SESSION['user']);

    if ($offer->add() != 1) {
        echo json_encode(array('success' => 0, 'message' => 'Erreur lors de l\'enregistrement.'));
        return;
    }
    $idOffer = joboffer::getLastId();

    $nomComplet = trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName());
    $lienOffre = joboffreLienAdmin($resourcehumaine->getId());
    $texte = ":memo: *Nouvelle offre d'emploi à valider* — " . $nomComplet
        . " (" . $resourcehumaine->getFunction() . ")\n"
        . "Période d'essai : " . $data['periode_essai'] . " · Salaire : " . $data['salaire'] . " DH\n"
        . "Voir l'offre : " . $lienOffre . "\n"
        . "Pour valider : *Je valide l'offre de " . $nomComplet . "*"
        . " — pour refuser : *Je ne valide pas l'offre de " . $nomComplet . "*";

    $slackTs = joboffreSlackPostMessage(defined('SLACK_CHANNEL_ADMINISTRATION') ? SLACK_CHANNEL_ADMINISTRATION : 'administration', $texte);

    if ($slackTs) {
        $offerAvecSlack = joboffer::find($idOffer);
        $offerAvecSlack->setSlackTs($slackTs);
        $offerAvecSlack->setSlackLastReminder(date('Y-m-d H:i:s'));
        $offerAvecSlack->edit();
    }

    echo json_encode(array('success' => 1, 'id' => $idOffer, 'slack_envoye' => $slackTs ? true : false));
}

// Lien vers la fiche employé (onglet Offre d'emploi) inclus dans les messages Slack - nécessite
// une session CRM classique (les liens Slack sont destinés à l'équipe admin, pas au candidat, qui
// lui reçoit un lien séparé sans connexion, voir joboffreLienAcceptation() plus bas).
function joboffreLienAdmin($idResourcehumaine)
{
    global $siteURL;
    $baseUrl = (defined('PUBLIC_SITE_URL') && PUBLIC_SITE_URL) ? PUBLIC_SITE_URL : $siteURL;
    return $baseUrl . 'index.php?option=com_resourcehumaine&task=joboffer&id=' . intval($idResourcehumaine);
}

// Lien d'acceptation envoyé au candidat par email une fois l'offre validée sur Slack - autonome,
// aucune session CRM requise (même principe que components/com_client/controleurs/socialaccess/
// router.php pour les accès temporaires réseaux sociaux), protégé par un token long et aléatoire
// plutôt qu'un couple token+code (le lien part directement vers l'adresse email du candidat, pas
// besoin d'un second facteur communiqué séparément comme pour l'accès admin réseaux sociaux).
function joboffreLienAcceptation($token)
{
    global $siteURL;
    $baseUrl = (defined('PUBLIC_SITE_URL') && PUBLIC_SITE_URL) ? PUBLIC_SITE_URL : $siteURL;
    return $baseUrl . 'components/com_resourcehumaine/controleurs/jobofferaccept/router.php?token=' . urlencode($token);
}

// Petit helper local (chat.postMessage + bot token Slack) : même patron que tvaSlackPostMessage()
// dans com_accounting/controleurs/router.php - convention déjà établie dans ce code (chaque module
// Slack a sa propre petite fonction indépendante). Retourne le "ts" du message posté (identifiant
// utilisé pour le retrouver dans l'historique du canal), ou false si l'envoi a échoué.
function joboffreSlackPostMessage($channel, $text)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '' || empty($channel)) {
        return false;
    }
    $ch = curl_init('https://slack.com/api/chat.postMessage');
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . SLACK_BOT_TOKEN
        ),
        CURLOPT_POSTFIELDS => json_encode(array('channel' => $channel, 'text' => $text)),
        CURLOPT_TIMEOUT => 15
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    if (is_array($decoded) && !empty($decoded['ok']) && isset($decoded['ts'])) {
        return $decoded['ts'];
    }
    return false;
}

// conversations.history exige un ID de canal (Cxxxxxxx), pas un nom - contrairement à
// chat.postMessage qui accepte un nom brut (cf. joboffreSlackPostMessage() ci-dessus). Résout le
// nom configuré (SLACK_CHANNEL_ADMINISTRATION) une fois par appel de cron ; nécessite que le bot
// ait le scope channels:read (ou groups:read pour un canal privé).
function joboffreSlackResolveChannelId($nomCanal)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '') {
        return null;
    }
    $nomCanal = ltrim($nomCanal, '#');
    $cursor = '';
    for ($page = 0; $page < 5; $page++) {
        $url = 'https://slack.com/api/conversations.list?types=public_channel,private_channel&limit=200' . ($cursor ? '&cursor=' . urlencode($cursor) : '');
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . SLACK_BOT_TOKEN),
            CURLOPT_TIMEOUT => 15
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response, true);
        if (!is_array($decoded) || empty($decoded['ok']) || empty($decoded['channels'])) {
            return null;
        }
        foreach ($decoded['channels'] as $c) {
            if (strcasecmp($c['name'], $nomCanal) === 0) {
                return $c['id'];
            }
        }
        $cursor = isset($decoded['response_metadata']['next_cursor']) ? $decoded['response_metadata']['next_cursor'] : '';
        if (!$cursor) {
            break;
        }
    }
    return null;
}

function joboffreSlackFetchHistory($channelId, $limit = 50)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '') {
        return array();
    }
    $ch = curl_init('https://slack.com/api/conversations.history?channel=' . urlencode($channelId) . '&limit=' . intval($limit));
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . SLACK_BOT_TOKEN),
        CURLOPT_TIMEOUT => 15
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    if (!is_array($decoded) || empty($decoded['ok'])) {
        return array();
    }
    return isset($decoded['messages']) ? $decoded['messages'] : array();
}

function joboffreSlackResolveUserName($userId)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '') {
        return $userId;
    }
    $ch = curl_init('https://slack.com/api/users.info?user=' . urlencode($userId));
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . SLACK_BOT_TOKEN),
        CURLOPT_TIMEOUT => 10
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    if (is_array($decoded) && !empty($decoded['ok']) && isset($decoded['user']['real_name'])) {
        return $decoded['user']['real_name'];
    }
    return $userId;
}

// Relance toutes les offres en attente de validation depuis ≥4h sans nouvelle relance depuis
// autant de temps (joboffer::findAllEnAttenteARelancer()) - à appeler toutes les heures par un
// service de cron externe (cron-job.org), avec ?secret=JOBOFFER_CRON_SECRET.
function cronRelanceOffreEmploiEndpoint()
{
    header('Content-Type: application/json');
    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('JOBOFFER_CRON_SECRET') || JOBOFFER_CRON_SECRET === '' || !hash_equals(JOBOFFER_CRON_SECRET, (string) $provided)) {
        error_log('cronRelanceOffreEmploiEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }
    bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, 1, 'fr');

    $resultats = array();
    foreach (joboffer::findAllEnAttenteARelancer(4) as $offer) {
        $nomComplet = trim($offer->getResourcehumaine()->getFirstName() . ' ' . $offer->getResourcehumaine()->getLastName());
        $texte = ":alarm_clock: *Relance* — l'offre d'emploi de " . $nomComplet . " est toujours en attente de validation.\n"
            . "Voir l'offre : " . joboffreLienAdmin($offer->getResourcehumaine()->getId()) . "\n"
            . "Pour valider : *Je valide l'offre de " . $nomComplet . "*"
            . " — pour refuser : *Je ne valide pas l'offre de " . $nomComplet . "*";
        $ts = joboffreSlackPostMessage(defined('SLACK_CHANNEL_ADMINISTRATION') ? SLACK_CHANNEL_ADMINISTRATION : 'administration', $texte);
        if ($ts) {
            $offer->setSlackLastReminder(date('Y-m-d H:i:s'));
            $offer->edit();
        }
        $resultats[] = array('offre' => $nomComplet, 'relance_envoyee' => $ts ? true : false);
    }

    echo json_encode(array('success' => 1, 'resultats' => $resultats));
}

// Lit l'historique récent du canal SLACK_CHANNEL_ADMINISTRATION à la recherche d'un message
// "je valide l'offre de {nom}" (accepté) ou "je ne valide pas l'offre de {nom}" / "je valide pas..."
// / "je refuse l'offre de {nom}" (refusé), et met à jour l'offre correspondante - à appeler toutes
// les 5-10 minutes par le même service de cron externe. Pas de restriction sur QUI peut
// valider/refuser (la demande initiale mentionne Hamid "notamment", pas exclusivement) - le nom de
// la personne qui a posté est simplement conservé pour traçabilité (slack_validated_by).
// Le refus est vérifié EN PREMIER : "je valide pas" contient le mot "valide" mais ne doit jamais
// matcher le pattern d'acceptation (qui exige "valide" immédiatement suivi de "l'offre").
function cronVerifierValidationSlackEndpoint()
{
    header('Content-Type: application/json');
    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('JOBOFFER_CRON_SECRET') || JOBOFFER_CRON_SECRET === '' || !hash_equals(JOBOFFER_CRON_SECRET, (string) $provided)) {
        error_log('cronVerifierValidationSlackEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }
    bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, 1, 'fr');

    $pendantes = joboffer::findAllEnAttenteValidation();
    if (empty($pendantes)) {
        echo json_encode(array('success' => 1, 'resultats' => array(), 'message' => 'Aucune offre en attente.'));
        return;
    }

    $channelId = joboffreSlackResolveChannelId(defined('SLACK_CHANNEL_ADMINISTRATION') ? SLACK_CHANNEL_ADMINISTRATION : 'administration');
    if (!$channelId) {
        echo json_encode(array('success' => 0, 'message' => 'Canal Slack introuvable ou bot non membre du canal (vérifier SLACK_CHANNEL_ADMINISTRATION et que le bot a bien rejoint le canal).'));
        return;
    }

    $messages = joboffreSlackFetchHistory($channelId, 50);
    $resultats = array();

    foreach ($messages as $msg) {
        if (empty($msg['text'])) {
            continue;
        }
        // Un message posté par le bot lui-même (annonce initiale ou relance) contient littéralement
        // "Je ne valide pas l'offre de X" comme texte d'INSTRUCTION ("pour refuser : ...") - sans ce
        // filtre, ce message se matche lui-même comme un refus dès le premier passage du cron, avant
        // même qu'un humain n'ait eu la moindre chance de répondre. Slack distingue les messages bot
        // par la présence de bot_id (absent des messages humains).
        if (isset($msg['bot_id'])) {
            continue;
        }
        $texteMsg = trim($msg['text']);

        $estRefus = preg_match('/je\s+(?:ne\s+valide\s+pas|valide\s+pas|refuse)\s+l[\'’]offre\s+de\s+(.+)/iu', $texteMsg, $m);
        $estValidation = !$estRefus && preg_match('/je\s+valide\s+l[\'’]offre\s+de\s+(.+)/iu', $texteMsg, $m);
        if (!$estRefus && !$estValidation) {
            continue;
        }
        $nomCible = rtrim(trim($m[1]), " .!");

        foreach ($pendantes as $offer) {
            $nomComplet = trim($offer->getResourcehumaine()->getFirstName() . ' ' . $offer->getResourcehumaine()->getLastName());
            if (mb_stripos($nomCible, $nomComplet) === false && mb_stripos($nomComplet, $nomCible) === false) {
                continue;
            }
            // Relit l'offre au cas où un message précédent dans cette même boucle (ou un appel de
            // cron précédent) l'aurait déjà traitée - évite un double traitement.
            $fraiche = joboffer::find($offer->getId());
            if ($fraiche->getStatut() !== joboffer::STATUT_EN_ATTENTE_SLACK) {
                continue;
            }
            $acteur = isset($msg['user']) ? joboffreSlackResolveUserName($msg['user']) : 'Slack';
            $fraiche->setSlackValidatedBy($acteur);
            $fraiche->setSlackValidatedAt(date('Y-m-d H:i:s', isset($msg['ts']) ? (int) floatval($msg['ts']) : time()));

            if ($estRefus) {
                $fraiche->setStatut(joboffer::STATUT_REFUSEE);
                $fraiche->edit();
                $resultats[] = array('offre' => $nomComplet, 'action' => 'refusee', 'par' => $acteur);
            } else {
                $fraiche->setStatut(joboffer::STATUT_VALIDEE);
                $token = bin2hex(random_bytes(32));
                $fraiche->setAcceptanceToken($token);
                $fraiche->edit();
                $emailEnvoye = envoyerEmailOffreValideeAuCandidat($fraiche, $token);
                $resultats[] = array('offre' => $nomComplet, 'action' => 'validee', 'par' => $acteur, 'email_candidat_envoye' => $emailEnvoye);
            }
        }
    }

    echo json_encode(array('success' => 1, 'resultats' => $resultats));
}

// Email envoyé au candidat dès que l'offre est validée sur Slack : récapitulatif + liste des
// documents obligatoires pour son statut (même source que le contrôle de conformité du dossier,
// fileresourcehumaine::documentsRequis()) + lien d'acceptation autonome (signature électronique
// "maison" tant que SignWell n'est pas configuré, voir jobofferaccept/router.php).
function envoyerEmailOffreValideeAuCandidat($offer, $token)
{
    $resourcehumaine = $offer->getResourcehumaine();
    if (!defined('SMTP_HOST') || SMTP_HOST == '' || !$resourcehumaine->getEmail()) {
        return false;
    }
    require_once '../../../vendor/autoload.php';

    $documents = fileresourcehumaine::documentsRequis($resourcehumaine->getStatus());
    $listeDocsHtml = '';
    foreach ($documents as $libelle) {
        $listeDocsHtml .= '<li>' . htmlspecialchars($libelle) . '</li>';
    }
    $lienAcceptation = joboffreLienAcceptation($token);
    $nomComplet = trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName());
    $agence = $resourcehumaine->getAgency();
    $nomAgence = $agence ? trim($agence->getNom()) : 'Hello World';

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet = 'UTF-8';

        // From explicitement direction@ (pas SMTP_USERNAME/sales@) -- même convention que
        // envoyerEmailDocumentsManquantsRH() (includes/functions/functions.php) pour les
        // emails adressés aux employés. setFrom() ne dépend pas des identifiants SMTP
        // authentifiés (Username/Password ci-dessus), qui restent sales@.
        $mail->setFrom('direction@helloworld-agency.com', $nomAgence);
        $mail->addAddress($resourcehumaine->getEmail(), $nomComplet);
        $mail->isHTML(true);
        $mail->Subject = "Votre offre d'emploi chez " . $nomAgence . " a été validée !";
        $mail->Body = "Bonjour " . htmlspecialchars($resourcehumaine->getFirstName()) . ",<br><br>"
            . "Excellente nouvelle : votre offre d'emploi chez <strong>" . htmlspecialchars($nomAgence) . "</strong> vient d'être validée par la direction !<br><br>"
            . "Pour finaliser votre embauche, merci de :<br>"
            . "<ol><li>Consulter et accepter votre offre d'emploi en ligne : <a href=\"" . $lienAcceptation . "\">" . $lienAcceptation . "</a></li>"
            . "<li>Nous transmettre les documents suivants dès que possible :</li></ol>"
            . "<ul>" . $listeDocsHtml . "</ul>"
            . "N'hésitez pas à revenir vers nous pour toute question.<br><br>"
            . "Cordialement,<br>L'équipe " . htmlspecialchars($nomAgence);
        $mail->AltBody = "Votre offre d'emploi chez " . $nomAgence . " a été validée. Consultez-la et acceptez-la ici : " . $lienAcceptation
            . ". Documents à fournir : " . implode(', ', $documents);

        $sent = $mail->send();
        if ($sent) {
            copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
        }
        return $sent;
    } catch (\Exception $e) {
        return false;
    }
}
