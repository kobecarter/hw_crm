<?php

use PhpOffice\PhpSpreadsheet\Calculation\Engine\FormattedNumber;

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addContract':
            addContract($_POST);
            break;
        case 'editContract':
            editContract($_POST);
            break;
        case 'deleteContract':
            deleteContract($_POST);
            break;
        case 'removeContratPDF':
            removeContratPDF($_POST);
            break;
        case 'pdfContract':
            pdfContract($_GET);
            break;
        case 'docxContract':
            docxContract($_GET);
            break;
        case 'genererContratDepuisDevis':
            genererContratDepuisDevis($_POST);
            break;
        case 'enregistrerCorpsContrat':
            enregistrerCorpsContrat($_POST);
            break;
        case 'marquerContratSigne':
            marquerContratSigne($_POST, $_FILES);
            break;
        case 'envoyerContratSlack':
            envoyerContratSlack($_POST);
            break;
        case 'envoyerContratEmailSignature':
            envoyerContratEmailSignature($_POST);
            break;
    }
}

function removeContratPDF($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $contract = contract::find($id, $_SESSION['agence'], $_SESSION['langue']);
        $contract->setContratPDF('');
        if($contract->edit() == 1 && file_exists("../../../images/contracts/" . $contract->getContratPDF())){
            @unlink("../../../images/contracts/" . $contract->getContratPDF());
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function addContract($data)
{
    $indices = array("id_devis","date","titre","duration","ville","tribunal","nombre_de_paiement");
    if (fieldCheck($data, $indices)) {

        // check si le pdf existe au cas ou le statut est signé
        if($data['status'] == 3){
            if(!(isset($_FILES['contrat_pdf']) && $_FILES['contrat_pdf']['name'][0]!='')){
                echo "3"; exit;
            }
        }

        if (buildContract($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editContract($data)
{
    $indices = array("id","id_devis","date","titre","duration","ville","tribunal","nombre_de_paiement");
    if (fieldCheck($data, $indices)) {

        // check si le pdf existe au cas ou le statut est signé
        if($data['status'] == 3){
            $contract = contract::find($data['id'],$_SESSION["agence"],$_SESSION["langue"]);
            if($contract->getContratPDF() == '' && !(isset($_FILES['contrat_pdf']) && $_FILES['contrat_pdf']['name'][0]!='')){
                echo "3"; exit;
            }
        }
        if (buildContract($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteContract($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        // find() attend (id, agence, langue) - l'agence manquait ici, ce qui faisait toujours
        // échouer la recherche (id_agence tombait à 0) : delete() s'exécutait alors sur un
        // contrat vide (id=0) et rapportait quand même "succès" via la convention $db->query()
        // toujours-falsy, sans jamais réellement rien supprimer.
        $contract = contract::find($id, $_SESSION["agence"], $_SESSION["langue"]);
        if ($contract->getId() && $contract->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Construit le PDF du contrat (objet Mpdf prêt, contenu déjà écrit via WriteHTML) - factorisé pour
// être réutilisé aussi bien par pdfContract() (affichage direct navigateur) que par
// envoyerContratEmailSignature() (pièce jointe email), sans dupliquer tout le HTML/CSS/en-tête.
function construireMpdfContrat($contract)
{
    require_once '../../../vendor/autoload.php';
    // "global" doit être déclaré AVANT le require : traduction.php fait $traduction = array(...)
    // au premier chargement - si ce require s'exécute avant le global, l'affectation atterrit
    // dans la portée locale de cette fonction, et le "global $traduction" qui suit ne fait alors
    // que rebrancher la variable sur un global resté vide (d'où les avertissements "null").
    global $traduction;
    require_once '../../../includes/traduction.php';

    $devis = $contract->getDevis();
    $client = $devis->getClient();
    // L'agence du contrat est toujours celle du CLIENT du devis, jamais celle de la session en
    // cours (un superuser peut consulter un contrat pendant qu'une autre agence est sélectionnée
    // dans son propre menu - le logo/l'en-tête/le pied de page ne doivent jamais en dépendre).
    $agence = agence::find($client->getAgence()->getId(), $devis->getLangue());

    // Corps légal du contrat (Article 1, 2, ... signature) : construit par contractGenerator,
    // seule source de vérité partagée avec docxContract() ci-dessous - le modèle est choisi
    // automatiquement d'après les catégories de service réellement présentes dans le devis.
    $corpsContrat = $contract->getCorpsGenere() ? $contract->getCorpsGenere() : contractGenerator::genererCorpsContrat($contract);

    $htmlInvoice = '<html>
    <head>
    <style>
    body {
        font-family: montserrat;
        font-size: 10pt;
    }
    p {	margin: 0pt; }
    table.items {
    }
    td { vertical-align: top; }
    .items td {
        border-left: 0.1mm solid #FFF;
        border-right: 0.1mm solid #FFF;
        border-bottom: 0.1mm solid #CCC;
    }
    table thead td { background-color: #EEEEEE;
        text-align: center;
        border-left: 0.1mm solid #FFF;
        border-right: 0.1mm solid #FFF;
    }
    .items td.blanktotal {
        background-color: #EEEEEE;
        border: 0.1mm solid #FFF;
        background-color: #FFFFFF;
        border: 0mm none #000000;
    }
    .items td.totals {
        text-align: right;
        border-bottom: 0.1mm solid #CCC;
    }
    .items td.cost {
        text-align: "." center;
    }
    </style>
    </head>
    <body>
    <!--mpdf
    <htmlpageheader name="myheader">
        <table width="100%">
        <tr>
            <td><img src="../../../images/agences/' . $agence->getLogo() . '" width="100"></td>
            <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
            <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> ' . $agence->getWebsite() . '</p></td>
        </tr>
        </table>
        <hr>
        </htmlpageheader>
        <htmlpagefooter name="myfooter">
        <div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

        if($agence->getIce() != ''){
            $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> '. $agence->getIf() .' | <strong>TP</strong> '. $agence->getTp() .' | <strong>RC</strong> '. $agence->getRc() .' | <strong>ICE</strong> '. $agence->getIce() .'</p>';
        }

        $htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} '.$traduction['SUR']['fr'].' {nb}</div>
        </div>
        </htmlpagefooter>
        <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
        <sethtmlpagefooter name="myfooter" value="on" />'
        . $corpsContrat
        . '</div>
        </body>
        </html>';


    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new \Mpdf\Mpdf([
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 45,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10,

        'fontDir' => array_merge($fontDirs, [
            '../../../fonts/',
        ]),
        'fontdata' => $fontData + [
            'montserrat' => [
                'R' => 'Montserrat-Regular.ttf',
                'B' => 'Montserrat-Bold.ttf',
            ]
        ],
        'default_font' => 'montserrat'
    ]);

    $mpdf->SetTitle("Contract #" . $contract->getId());
    $mpdf->SetAuthor("Hello World");
    $mpdf->SetWatermarkText("");
    $mpdf->showWatermarkText = true;
    $mpdf->watermark_font = 'DejaVuSansCondensed';
    $mpdf->watermarkTextAlpha = 0.05;
    $mpdf->SetDisplayMode('fullpage');
    $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
    $mpdf->WriteHTML($htmlInvoice);

    return $mpdf;
}

function pdfContract($data){
    if (isset($data["id"]) && !empty($data["id"])) {
        $contract = contract::find($data["id"], $_SESSION['agence'], $_SESSION['langue']);
        $devis = $contract->getDevis();
        $contract = contract::find($data["id"], $_SESSION['agence'], $devis->getLangue());

        $mpdf = construireMpdfContrat($contract);
        $mpdf->Output('contract.pdf', 'I');
    }
}

// Export Word (.docx) du contrat - même corps HTML que pdfContract() (contractGenerator, seule
// source de vérité), simplement rendu par PhpWord au lieu de mPDF. Permet la modification finale
// du texte dans Word avant envoi au client pour signature, plutôt que d'être figé dans un PDF.
function docxContract($data)
{
    if (isset($data["id"]) && !empty($data["id"])) {
        require_once '../../../vendor/autoload.php';

        // sys_get_temp_dir() résout vers un dossier temporaire propre à l'utilisateur/session
        // shell courant sous macOS (/var/folders/.../T/...), inaccessible en écriture au worker
        // Apache qui exécute réellement cette requête - PhpWord y échoue silencieusement en
        // "Permission denied" au moment de fermer le zip. /tmp reste toujours accessible en
        // écriture à tous les processus, quel que soit l'utilisateur qui les exécute.
        \PhpOffice\PhpWord\Settings::setTempDir('/tmp');

        $contract = contract::find($data["id"], $_SESSION['agence'], $_SESSION['langue']);
        $devis = $contract->getDevis();
        $contract = contract::find($data["id"], $_SESSION['agence'], $devis->getLangue());
        $devis = $contract->getDevis();
        $client = $devis->getClient();
        $agence = agence::find($client->getAgence()->getId(), $devis->getLangue());

        $corpsContrat = $contract->getCorpsGenere() ? $contract->getCorpsGenere() : contractGenerator::genererCorpsContrat($contract);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection(array(
            'marginLeft' => 850,
            'marginRight' => 850,
            'marginTop' => 850,
            'marginBottom' => 850,
            'headerHeight' => 900,
            'footerHeight' => 900,
        ));

        // En-tête (logo + adresse) et pied de page (IF/TP/RC/ICE) de l'agence - même contenu que
        // le header/footer PDF de pdfContract(), pour que le Word ressemble au PDF déjà connu.
        $header = $section->addHeader();
        $logoPath = '../../../images/agences/' . $agence->getLogo();
        if ($agence->getLogo() != '' && file_exists($logoPath)) {
            $header->addImage($logoPath, array('width' => 90, 'height' => 45));
        }
        $header->addText(htmlspecialchars($agence->getAdresse()), array('size' => 8), array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END));
        $header->addText('t: ' . htmlspecialchars($agence->getTel()) . '  |  e: ' . htmlspecialchars($agence->getEmail()), array('size' => 8), array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END));

        $footer = $section->addFooter();
        if ($agence->getIce() != '') {
            $footer->addText(
                'IF ' . htmlspecialchars($agence->getIf()) . ' | TP ' . htmlspecialchars($agence->getTp()) . ' | RC ' . htmlspecialchars($agence->getRc()) . ' | ICE ' . htmlspecialchars($agence->getIce()),
                array('size' => 8),
                array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER)
            );
        }

        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $corpsContrat, false, false);

        $fileName = 'contrat-' . preg_replace('/[^A-Za-z0-9_-]/', '', $devis->getNumero() ? $devis->getNumero() : $contract->getId()) . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save('php://output');
        exit;
    }
}

// "Générer le contrat" depuis le devis (bouton + fenêtre de confirmation côté com_devis/form.php) :
// crée le contrat en un clic avec des valeurs par défaut raisonnables (ville/tribunal de
// l'agence, durée générique) et pré-remplit corps_genere via contractGenerator - l'utilisateur
// affine ensuite les champs et le texte dans l'éditeur dédié (task=editeur) plutôt que de tout
// saisir à la main comme l'ancien formulaire "add".
// Le devis doit être passé au moins par "Accepté" avant de pouvoir créer ou faire valider un
// contrat - jamais de contrat en préparation/signature pour une offre que le client n'a pas
// encore acceptée. Réutilisé par genererContratDepuisDevis() et marquerContratSigne().
function contratStatutsDevisAutorises()
{
    return array(devis::STATU_ACCEPTE, devis::STATU_CONTRAT_EN_ATTENTE, devis::STATU_PAIEMENT_EFFECTUE, devis::STATU_CONTRAT_SIGNE);
}

function genererContratDepuisDevis($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('add', 'com_contract')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id_devis']) || empty($data['id_devis'])) {
        echo json_encode(array('success' => 0, 'message' => 'Devis manquant'));
        return;
    }
    $devis = devis::find(intval($data['id_devis']), $_SESSION['agence']);
    if (!$devis->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Devis introuvable'));
        return;
    }
    if (!in_array($devis->getStatu(), contratStatutsDevisAutorises())) {
        echo json_encode(array('success' => 0, 'message' => "Merci d'accepter le devis d'abord pour pouvoir valider le contrat."));
        return;
    }
    // Un proforma (devis ou facture liée) n'est jamais une vente ferme - jamais de contrat tant
    // que ce statut n'est pas levé (repasser le devis/la facture en normal ailleurs dans l'app).
    if ($devis->getProforma() == 1) {
        echo json_encode(array('success' => 0, 'message' => "Impossible de générer un contrat pour un devis proforma."));
        return;
    }
    $factureLiee = $devis->getFacture();
    if ($factureLiee->getId() && $factureLiee->getProforma() == 1) {
        echo json_encode(array('success' => 0, 'message' => "Impossible de générer un contrat : la facture liée à ce devis est une facture proforma."));
        return;
    }

    $existant = contract::findByDevis($devis->getId(), $_SESSION['agence'], $devis->getLangue());
    if ($existant->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Un contrat existe déjà pour ce devis', 'id_contract' => $existant->getId()));
        return;
    }

    $client = $devis->getClient();
    $agence = agence::find($client->getAgence()->getId(), $devis->getLangue());
    $ville = $agence->getVille() != '' ? $agence->getVille() : 'Marrakech';

    require_once '../../../vendor/autoload.php';

    // Les champs sont pré-remplis avec des valeurs par défaut raisonnables (génération 1 clic
    // depuis la page devis), mais l'étape 1 de l'assistant "Ajouter un contrat"
    // (com_contract&task=add) permet de les personnaliser avant génération - on les reprend donc
    // si fournis, sans jamais rendre ces champs obligatoires côté client.
    $titre = isset($data['titre_contract']) && trim($data['titre_contract']) !== '' ? trim($data['titre_contract']) : 'Contrat de prestation - Devis N°' . $devis->getNumero();
    $duration = isset($data['duration_contract']) && trim($data['duration_contract']) !== '' ? trim($data['duration_contract']) : '12 mois';
    $garantie = isset($data['garantie_contract']) ? trim($data['garantie_contract']) : '';
    $villeContrat = isset($data['ville_contract']) && trim($data['ville_contract']) !== '' ? trim($data['ville_contract']) : $ville;
    $dateContrat = isset($data['date_contract']) && trim($data['date_contract']) !== '' ? dateBD($data['date_contract']) : date('Y-m-d');
    $tribunal = isset($data['tribunal_contract']) && trim($data['tribunal_contract']) !== '' ? trim($data['tribunal_contract']) : 'Tribunal de Commerce de ' . $ville;
    $nombreDePaiement = isset($data['nombre_de_paiement_contract']) && $data['nombre_de_paiement_contract'] !== '' ? intval($data['nombre_de_paiement_contract']) : 1;

    $contract = new contract();
    $contract->setDevis($devis);
    $contract->setType('genere_auto');
    $contract->setTitre($titre);
    $contract->setDuration($duration);
    $contract->setGarantie($garantie);
    $contract->setVille($villeContrat);
    $contract->setDate($dateContrat);
    $contract->setDateFin(null);
    $contract->setTribunal($tribunal);
    $contract->setNombreDePaiement($nombreDePaiement);
    $contract->setShowSignature(!empty($data['show_signature_contract']) ? 1 : 0);
    $contract->setStatus(contract::STATUT_PREPARATION);
    $contract->setContratPDF('');
    $contract->setLangue($devis->getLangue());
    $contract->setDateAdd(date('Y-m-d H:i:s'));
    $contract->setLastEdit(date('Y-m-d H:i:s'));

    // Généré une seule fois ici à partir des prestations réelles du devis, puis figé : l'éditeur
    // WYSIWYG dédié (task=editeur) modifie ensuite corps_genere directement
    // (pdfContract()/docxContract() l'utilisent tel quel dès qu'il est rempli, cf.
    // contract::getCorpsGenere()). Dupliqué dans "texte" pour que l'ancien formulaire
    // (com_contract&task=edit, qui n'affiche que ce champ) montre lui aussi le contenu généré au
    // lieu d'un champ vide - enregistrerCorpsContrat() garde les deux synchronisés ensuite.
    $corpsGenere = contractGenerator::genererCorpsContrat($contract);
    $contract->setCorpsGenere($corpsGenere);
    $contract->setTexte($corpsGenere);
    $contract->add();

    // add() insère en 2 requêtes (crm_contract puis crm_details_contract) - db->last_id() après
    // coup renverrait l'id de la 2e ligne, pas celui du contrat. On le retrouve donc par sa clé
    // naturelle (id_devis), fiable puisqu'on vient de vérifier qu'aucun contrat n'existait encore.
    $contratCree = contract::findByDevis($devis->getId(), $_SESSION['agence'], $devis->getLangue());

    contractSlackNotifierContratPret($contratCree, $devis, $client);

    $nomClient = trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
    echo json_encode(array(
        'success' => 1,
        'id_contract' => $contratCree->getId(),
        'corps_genere' => $contratCree->getCorpsGenere(),
        // Valeurs réellement enregistrées (après application des défauts serveur) - pour que le
        // récapitulatif de l'étape 3 de l'assistant reflète ce qui est vraiment en base, pas
        // uniquement ce que l'utilisateur a tapé (souvent laissé vide pour accepter les défauts).
        'recap' => array(
            'devis_numero' => $devis->getNumero(),
            'client' => $nomClient,
            'titre' => $contratCree->getTitre(),
            'duration' => $contratCree->getDuration(),
            'ville' => $contratCree->getVille(),
            'tribunal' => $contratCree->getTribunal()
        )
    ));
}

// Sauvegarde du texte modifié dans l'éditeur WYSIWYG (task=editeur) - ne touche que corps_genere,
// jamais les autres champs du contrat (titre/durée/ville/tribunal restent modifiables séparément
// depuis le formulaire classique com_contract&task=edit).
function enregistrerCorpsContrat($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_contract')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat manquant'));
        return;
    }
    $contract = contract::find(intval($data['id']), $_SESSION['agence'], $_SESSION['langue']);
    if (!$contract->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat introuvable'));
        return;
    }
    $corps = isset($data['corps_genere']) ? $data['corps_genere'] : '';
    $corps = contractGenerator::versLatin1Safe($corps);
    $contract->setCorpsGenere($corps);
    // Garde "texte" synchronisé avec corps_genere (cf. genererContratDepuisDevis()) pour que
    // l'ancien formulaire com_contract&task=edit continue d'afficher le même contenu.
    $contract->setTexte($corps);
    $contract->setLastEdit(date('Y-m-d H:i:s'));
    $contract->edit();
    echo json_encode(array('success' => 1));
}

// Upload du contrat signé (PDF) - même mécanique que
// com_resourcehumaine/controleurs/joboffer/controleur.php::marquerOffreSignee() (upload +
// changement de statut), adaptée aux contrats : le devis passe automatiquement à "Contrat signé"
// dès que ce PDF est déposé, sans action manuelle supplémentaire côté devis.
function marquerContratSigne($data, $files)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_contract')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat manquant'));
        return;
    }
    if (!isset($files['signed_file']['name'][0]) || empty($files['signed_file']['name'][0])) {
        echo json_encode(array('success' => 0, 'message' => 'Fichier PDF manquant'));
        return;
    }
    $contract = contract::find(intval($data['id']), $_SESSION['agence'], $_SESSION['langue']);
    if (!$contract->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat introuvable'));
        return;
    }
    if (!in_array($contract->getDevis()->getStatu(), contratStatutsDevisAutorises())) {
        echo json_encode(array('success' => 0, 'message' => "Merci d'accepter le devis d'abord pour pouvoir valider le contrat."));
        return;
    }

    $uploaded = uploadFiles('signed_file', '../../../images/contracts/', array('pdf', 'PDF'));
    if (empty($uploaded[0])) {
        echo json_encode(array('success' => 0, 'message' => "Échec de l'upload"));
        return;
    }

    $contract->setContratPDF($uploaded[0]);
    $contract->setStatus(contract::STATUT_SIGNE);
    $contract->setSignedAt(date('Y-m-d H:i:s'));
    $contract->setLastEdit(date('Y-m-d H:i:s'));
    $contract->edit();

    $devis = $contract->getDevis();
    $devis->setStatu(devis::STATU_CONTRAT_SIGNE);
    $devis->edit();

    echo json_encode(array('success' => 1));
}

// Copie locale volontaire (même convention que com_devis/com_resourcehumaine : chaque module garde
// ses propres fonctions Slack, pas de fichier partagé) - notifie commercial-hw dès qu'un contrat
// généré depuis un devis est prêt pour relecture/validation.
function contractSlackResolveChannelId($nomCanal)
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
        if (empty($decoded['response_metadata']['next_cursor'])) {
            return null;
        }
        $cursor = $decoded['response_metadata']['next_cursor'];
    }
    return null;
}

function contractSlackPostMessage($channel, $text)
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
    return is_array($decoded) && !empty($decoded['ok']);
}

function contractSlackNotifierContratPret($contract, $devis, $client)
{
    $nomCanal = defined('SLACK_CHANNEL_COMMERCIAL') ? SLACK_CHANNEL_COMMERCIAL : 'commercial-hw';
    $channelId = contractSlackResolveChannelId($nomCanal);
    if (!$channelId) {
        return false;
    }
    $nomClient = trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
    global $siteURL;
    $baseUrl = (defined('PUBLIC_SITE_URL') && PUBLIC_SITE_URL) ? PUBLIC_SITE_URL : $siteURL;
    $lien = $baseUrl . 'index.php?option=com_contract&task=editeur&id=' . $contract->getId();
    $texte = ":page_facing_up: *Contrat prêt pour validation* — Devis N°" . $devis->getNumero() . "\n"
        . "*Client :* " . $nomClient . "\n"
        . "*Lien :* " . $lien;
    return contractSlackPostMessage($channelId, $texte);
}

// Bouton "Envoyer par Slack pour validation" (page devis, éditeur de contrat, étape 3 de
// l'assistant d'ajout) - notification à la demande, réutilise le même message que celui envoyé
// automatiquement à la génération.
function envoyerContratSlack($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('view', 'com_contract')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat manquant'));
        return;
    }
    $contract = contract::find(intval($data['id']), $_SESSION['agence'], $_SESSION['langue']);
    if (!$contract->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat introuvable'));
        return;
    }
    $devis = $contract->getDevis();
    $client = $devis->getClient();
    if (!contractSlackNotifierContratPret($contract, $devis, $client)) {
        echo json_encode(array('success' => 0, 'message' => "Envoi Slack impossible (canal introuvable ou bot absent du canal)."));
        return;
    }
    echo json_encode(array('success' => 1));
}

// Bouton "Envoyer au client par email pour signature" - même mécanique que
// com_devis/controleurs/devis/controleur.php::sendDevisPdfEmailToClient() (PHPMailer, PDF en
// pièce jointe), adaptée au contrat : PDF généré à la volée via construireMpdfContrat() (jamais
// stocké sur disque plus que le temps de l'envoi), et passe le contrat en "En attente de
// signature" pour refléter l'action.
function envoyerContratEmailSignature($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_contract')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat manquant'));
        return;
    }
    $contract = contract::find(intval($data['id']), $_SESSION['agence'], $_SESSION['langue']);
    if (!$contract->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Contrat introuvable'));
        return;
    }
    $devis = $contract->getDevis();
    $client = $devis->getClient();
    if (!$client->getEmail()) {
        echo json_encode(array('success' => 0, 'message' => "Le client n'a pas d'adresse email."));
        return;
    }
    if (!defined('SMTP_HOST') || SMTP_HOST == '') {
        echo json_encode(array('success' => 0, 'message' => 'SMTP non configuré (config.secrets.php).'));
        return;
    }

    require_once '../../../vendor/autoload.php';
    $mpdf = construireMpdfContrat($contract);
    // /tmp plutôt que sys_get_temp_dir() : ce dernier résout vers un dossier propre à la
    // session/l'utilisateur shell courant sous macOS, inaccessible en écriture au worker Apache
    // qui exécute réellement cette requête (même piège déjà rencontré avec PhpWord/docxContract()).
    $tmpPath = '/tmp/contrat_' . $contract->getId() . '_' . uniqid() . '.pdf';
    $mpdf->Output($tmpPath, 'F');

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

        $mail->setFrom(SMTP_USERNAME, 'Hello World');
        $mail->addAddress($client->getEmail(), trim($client->getPrenom() . ' ' . $client->getNom()));
        $mail->addCC('contact@helloworld-agency.com');
        $mail->addAttachment($tmpPath, 'Contrat-' . $devis->getNumero() . '.pdf');
        $mail->isHTML(true);

        $isEn = ($devis->getLangue() == 'en');
        if ($isEn) {
            $mail->Subject = "Your contract for quote N°" . $devis->getNumero() . " — signature required";
            $mail->Body = "Hello " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "Please find attached your contract for quote N°" . $devis->getNumero() . ". Please review, sign, and return it to us.<br><br>"
                . "Feel free to reach out with any question.<br><br>Have a great day,<br>The Hello World team";
            $mail->AltBody = "Hello " . $client->getPrenom() . ", please find attached your contract for quote N°" . $devis->getNumero() . ".";
        } else {
            $mail->Subject = "Votre contrat pour le devis N°" . $devis->getNumero() . " — signature requise";
            $mail->Body = "Bonjour " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "Veuillez trouver ci-joint votre contrat pour le devis N°" . $devis->getNumero() . ". Merci de le relire, le signer, et nous le retourner.<br><br>"
                . "N'hésitez pas à revenir vers nous pour la moindre question.<br><br>Belle journée à vous,<br>L'équipe Hello World";
            $mail->AltBody = "Bonjour " . $client->getPrenom() . ", veuillez trouver ci-joint votre contrat pour le devis N°" . $devis->getNumero() . ".";
        }

        $mail->send();
        copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
        @unlink($tmpPath);
    } catch (\Exception $e) {
        @unlink($tmpPath);
        echo json_encode(array('success' => 0, 'message' => $mail->ErrorInfo));
        return;
    }

    $contract->setStatus(contract::STATUT_ATTENTE_SIGNATURE);
    $contract->setLastEdit(date('Y-m-d H:i:s'));
    $contract->edit();

    // Toujours basculer le devis sur "Contrat en attente de signature" à l'envoi pour signature -
    // seule exception : un devis déjà marqué "Contrat signé" ne doit jamais reculer en arrière
    // (ex: renvoi de l'email après coup, cas rare mais ne doit pas défaire un signalement déjà à
    // jour).
    if ($devis->getStatu() != devis::STATU_CONTRAT_SIGNE) {
        $devis->setStatu(devis::STATU_CONTRAT_EN_ATTENTE);
        $devis->edit();
    }

    echo json_encode(array('success' => 1));
}

function buildContract($data, $id = null)
{
    global $db;
    $contract = new contract();

    if ($id) {
        $contract = contract::find($id,$_SESSION["agence"],$_SESSION["langue"]);
    }

    if(isset($_FILES['contrat_pdf']) && $_FILES['contrat_pdf']['name'][0]!=''){
        $photo = uploadFiles('contrat_pdf','../../../images/contracts/',  array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));    
    }

    if(isset($photo[0])) {
		$contract->setContratPDF($photo[0]);
	}

    $contract->setDevis(devis::find($data['id_devis'],$_SESSION['agence']));
    $contract->setTitre($data['titre']);
    $contract->setDuration($data['duration']);
    $contract->setGarantie($data['garantie']);
    $contract->setVille($data['ville']);
    $contract->setDate(dateBD($data['date']));
    $contract->setDateFin(isset($data['date_fin']) ? dateBD($data['date_fin']) : null);
    $contract->setTribunal($data['tribunal']);
    $contract->setNombreDePaiement($data['nombre_de_paiement']);
	// "Texte" (formulaire classique "Champs du contrat") et corps_genere (task=editeur, seule
	// source lue par pdfContract()/docxContract()) sont deux champs séparés qui doivent rester
	// synchronisés dans les deux sens - sans ça, modifier "Texte" ici n'a aucun effet visible sur
	// le PDF généré, qui continue d'utiliser l'ancien corps_genere. Même contournement latin1 que
	// enregistrerCorpsContrat() (cf. contractGenerator::versLatin1Safe()).
	$texteSync = contractGenerator::versLatin1Safe($data['texte']);
	$contract->setTexte($texteSync);
	$contract->setCorpsGenere($texteSync);
	$contract->setShowSignature(isset($data['show_signature']) ? 1 : 0);
    $contract->setStatus($data['status']);
    $contract->setDateAdd(date("Y-m-d H:i:s"));
    $contract->setLastEdit(date("Y-m-d H:i:s"));
    $contract->setLangue($_SESSION['langue']);

    return $contract;
}