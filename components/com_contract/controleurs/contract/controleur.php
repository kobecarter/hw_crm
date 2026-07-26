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
        $contract = contract::find($id, $_SESSION["langue"]);
        if ($contract->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function pdfContract($data){
    global $db;
    if (isset($data["id"]) && !empty($data["id"])) {

        require_once '../../../vendor/autoload.php';
        require_once '../../../includes/traduction.php';
        
        $dirPath = "../../../";

        $contract = contract::find($data["id"],$_SESSION['agence'], $_SESSION['langue']);
        $devis  = $contract->getDevis();
        $contract = contract::find($data["id"],$_SESSION['agence'], $devis->getLangue());
        $devis = $contract->getDevis();
        $client = $devis->getClient();
        $agence = agence::find($_SESSION["agence"],$_SESSION["langue"]);
        $color = $agence->getColor();
        $mpdf = new \Mpdf\Mpdf();

        $htmlInvoice = '';
        
        $prestations = $devis->getItems();
        $flagSEO = $flagSocial = $flagDev = false;
        foreach($prestations as $prestation){
            $id_service = $prestation->getService()->getId();
            if($id_service == 165 || $id_service == 164 || $id_service == 163 || $id_service == 134 || $id_service == 133) $flagSEO = true;
            if($id_service == 168 || $id_service == 167 || $id_service == 166 || $id_service == 142) $flagSocial = true;
            if($id_service == 121 || $id_service == 119 || $id_service == 117 || $id_service == 116 || $id_service == 115) $flagDev = true;
        }
        
        
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
            <sethtmlpagefooter name="myfooter" value="on" />';
            
        // Contrat type prestation site internet/SEO/social media ////////////////////////////////////////////////
        if($flagSEO && $flagDev){
            $htmlInvoice .= '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE COMBINÉ</h1>
            <h2 style="text-align:center">« '.$contract->getTitre().' »</h2>
            <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>
            <p style="margin-top:20px"><b>'.$agence->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$agence->getAdresse().', immatriculée au RC de '.$agence->getVille().' sous le numéro '.$agence->getRc().', représentée par '.$agence->getManager().', ci-après dénommée le <b>« PRESTATAIRE »</b>.</p>
            <p style="margin-top:20px"><b>ET</b></p>
            <p style="margin-top:20px"><b>'.$client->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$client->getAdresse().', immatriculée au RC de '.$client->getVille().' sous le numéro '.$client->getRC().', représentée par '.$client->getTitre().'. '.$client->getNom() .' '.$client->getPrenom() .', ci-après dénommée le <b>« CLIENT »</b>.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 1 – OBJET DU CONTRAT</u></h2>
            <p>Le présent contrat définit les conditions techniques, juridiques et financières dans lesquelles le Prestataire assure pour le compte du Client la réalisation de prestations numériques. Le périmètre technique et fonctionnel est exclusivement celui déterminé et listé dans le devis accepté par le Client. Les prestations objet du présent contrat regroupent:</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px">La conception et le développement d\'un site internet (Vitrine, E-commerce ou Sur-mesure).</li>
                <li style="margin-top:5px">L\'optimisation de la visibilité du site sur les moteurs de recherche (Référencement Naturel / SEO).</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 2 – DÉTAIL DES PRESTATIONS</u></h2>
            <h3 style="margin-top:15px"><u>2.1. Développement de Site Internet</u></h3>
            <p>Le Prestataire s\'engage à réaliser les missions suivantes selon le périmètre du devis :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Conception & Design</b> : Création de la charte graphique et de l\'ergonomie (UI/UX).</li>
                <li style="margin-top:5px"><b>Développement :</b> Intégration technique, programmation des fonctionnalités et gestion de la base de données. Le développement des fonctionnalités est strictement limité à celles mises en accord et listées dans le devis accepté.</li>
                <li style="margin-top:5px"><b>Contenu Spécifique :</b> Toute demande de création de contenu spécifique, de packs dédiés ou de fonctionnalités supplémentaires fera l\'objet d\'un accord mutuel préalable et d\'une mention spécifique dans le devis.</li>
                <li style="margin-top:5px"><b>Mise en ligne & Formation :</b> Déploiement sur le serveur et formation à l\'outil d\'administration.</li>
            </ul>
            <h3 style="margin-top:15px"><u>2.2. Référencement Naturel (SEO)</u></h3>
            <p>Le Prestataire accompagne le Client dans l\'optimisation de sa visibilité sur les moteurs de recherche via :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Sélection de mots-clés</b> : Identification de mots-clés pertinents. Cette liste est validée d\'un commun accord et figure en Annexe 1.</li>
                <li style="margin-top:5px"><b>Audit technique & sémantique :</b> Analyse complète de la structure du site.</li>
                <li style="margin-top:5px"><b>Optimisation On-Page :</b> Rédaction et optimisation technique (balises, contenu).</li>
                <li style="margin-top:5px"><b>Optimisation Off-Page :</b> Stratégie de netlinking (liens entrants).</li>
                <li style="margin-top:5px"><b>Suivi & Reporting :</b> Le Prestataire s\'engage à fournir un rapport mensuel détaillé et à collaborer étroitement avec le Client pour le tenir informé de l\'avancement.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 3 – ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
            <h3 style="margin-top:15px"><u>3.1. Normes Techniques et Éthique</u></h3>
            <p style="margin-top:15px"><b>Pour le Développement Web</b> : Le Prestataire s\'engage à appliquer les meilleures pratiques de l\'industrie (Clean Code), incluant le respect des standards W3C pour le HTML/CSS, l\'optimisation des performances (vitesse de chargement) et la mise en œuvre de protocoles de sécurité rigoureux. Le code sera structuré pour garantir sa pérennité et une compatibilité optimale avec les navigateurs modernes et les différents terminaux (Responsive Design).</p>
            <p style="margin-top:15px"><b>Pour le SEO</b> : Le Prestataire garantit qu\'il n\'utilisera aucune méthode de référencement illégale ou non éthique (dite "Black Hat SEO"). Seules les méthodes conformes aux consignes des moteurs de recherche ("White Hat SEO") seront appliquées.</p>
            <h3 style="margin-top:15px"><u>3.2. Obligation de Moyens (SEO)</u></h3>
            <p style="margin-top:15px">Concernant le référencement naturel, le Prestataire est soumis à une obligation de moyens. Les résultats dépendant d\'algorithmes tiers (moteurs de recherche) sur lesquels le Prestataire n\'a pas de contrôle direct, il ne peut garantir à 100 % une position précise.</p>
            <h3 style="margin-top:15px"><u>3.3. Exonération de Responsabilité</u></h3>
            <p style="margin-top:15px"><b>SEO</b> : Si le Prestataire prouve qu’il a mis en œuvre toutes les optimisations nécessaires et les actions techniques prévues, le Client ne pourra réclamer aucun dédommagement en cas de stagnation ou baisse de positionnement.</p>
            <p style="margin-top:15px"><b>Retard du Client</b> : Le Prestataire ne pourra être tenu pour responsable en cas de retard dans l’exécution des travaux dû au non-respect des engagements de coopération du Client. Tout retard de livraison résultant d\'un manquement du Client ne pourra lui être imputé.</p>
            <h3 style="margin-top:15px"><u>3.4. Ponctualité et Satisfaction (Web)</u></h3>
            <p style="margin-top:15px">Le Prestataire s’engage à respecter le calendrier de livraison par étapes déterminé dans l’annexe. Toutefois, ce respect des délais est strictement conditionné par la coopération et la réactivité du Client. En conséquence, le Prestataire ne pourra être tenu pour responsable de tout décalage si le Client prend un temps anormal à valider les étapes intermédiaires.</p>
            <p style="margin-top:15px">Le Prestataire s’engage formellement à assurer la satisfaction du Client concernant le livrable final du site internet.</p>
            <h3 style="margin-top:15px"><u>3.5. Droit de Suspension</u></h3>
            <p style="margin-top:15px">En cas de défaut de paiement, les prestations (notamment les actions SEO mensuelles) peuvent être interrompues après mise en demeure.</p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 4 – ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
            <h3 style="margin-top:15px"><u>4.1. Coopération, Ressources et Délais</u></h3>
            <p style="margin-top:15px">Le CLIENT s’engage, dans la mesure du possible, à faciliter du mieux qu’il peut le bon déroulement des missions du PRESTATAIRE.</p>
            <p style="margin-top:15px"><b>Délai de délivrance</b> : Le CLIENT s’engage à coopérer et, sous un délai de sept (7) jours après la date de l’acceptation de l’offre, à délivrer au PRESTATAIRE tous les éléments nécessaires à la réalisation des travaux (textes, images, logo).</p>
            <h3 style="margin-top:15px"><u>4.2. Accès</u></h3>
            <p style="margin-top:15px">Le Client fournit les accès nécessaires à la réalisation des deux services : accès à l\'hébergement, aux noms de domaine, à l\'interface d\'administration (CMS) et aux outils d\'analyse (Search Console, Analytics).</p>
            <h3 style="margin-top:15px"><u>4.3. Validations et Modifications</u></h3>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Développement Web</b> : Une fois une étape validée (Maquettes, Structure, Beta), toute demande de modification ultérieure sera évaluée. Si les modifications sont mineures et peu nombreuses, le Prestataire s\'engage à les offrir gracieusement. Si elles sont majeures ou conséquentes, elles feront l\'objet d\'un accord exprès et d\'un nouveau devis.</li>
                <li style="margin-top:5px"><b>SEO</b> : Le Client valide la liste des mots-clés en Annexe 1 avant le démarrage de la prestation SEO.</li>
            </ul>
            <h3 style="margin-top:15px"><u>4.4. Exclusivité Technique (SEO)</u></h3>
            <p style="margin-top:15px">Le Client s\'engage à ne pas modifier la structure technique du site sans consultation préalable du Prestataire, afin de ne pas compromettre les travaux de référencement.</p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 5 – CONDITIONS FINANCIÈRES</u></h2>
            <p style="margin-top:15px"><b>5.1. Honoraires</b> : Le prix est fixé selon le devis accepté par le Client.</p>
            <p style="margin-top:15px"><b>5.2. Modalités</b> : Le règlement est effectué selon les termes du devis (par étapes, mensuel ou total).</p>
            <p style="margin-top:15px"><b>5.3. Retard de Paiement</b> : Tout retard entraîne l\'application de pénalités de retard au taux légal en vigueur après mise en demeure.</p>
            <p style="margin-top:15px"><b>5.4. Propriété</b> : Le site et son code source deviennent la propriété du Client uniquement après le paiement intégral des honoraires dus.</p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 6 – DURÉE ET RÉSILIATION</u></h2>
            <p style="margin-top:15px"><b>6.1. Durée</b> :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px">Pour le <b>Développement Web</b> : Le contrat prend fin à la livraison finale et après la période de garantie technique.</li>
                <li style="margin-top:5px">Pour le <b>SEO</b> : Le contrat est conclu pour une durée initiale de '.$contract->getDuration().'.</li>
            </ul>
            <p style="margin-top:15px"><b>6.2. Reconduction (SEO)</b> : Tacite reconduction pour la prestation de référencement, sauf dénonciation avec un préavis de [30] jours.</p>
            <p style="margin-top:15px"><b>6.3. Résiliation (Web)</b> :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px">Par le Prestataire : Si le CLIENT ne remplit pas ses obligations ou si le paiement accuse un retard de plus de sept (7) jours.</li>
                <li style="margin-top:5px">Par le Client : Moyennant un préavis écrit de sept (7) jours. Dans ce cas, le PRESTATAIRE sera indemnisé pour tout le travail effectué jusqu\'à la date de résiliation.</li>
            </ul>
            
            <h2 style="margin-top:30px"><u>ARTICLE 7 – CONFIDENTIALITÉ</u></h2>
            <p style="margin-top:15px">Les parties s\'engagent à ne divulguer aucune information confidentielle concernant l\'activité de l\'autre partie durant toute la durée du projet.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 8 – PROMOTION ET RÉFÉRENCE</u></h2>
            <p style="margin-top:15px">Sous réserve d’une réponse favorable du CLIENT à l’e-mail qui lui sera adressé par le PRESTATAIRE au terme de la prestation, le CLIENT s’engage à faire figurer le logo de Hello World ainsi que la mention discrète « élément réalisé par Hello World », éventuellement accompagnée d’un lien hypertexte pointant vers le site www.helloworld-agency.com.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 9 – RESPONSABILITÉ</u></h2>
            <p style="margin-top:15px">La responsabilité du Prestataire est limitée aux fonctionnalités listées dans le devis. Pour la prestation SEO, la responsabilité du Prestataire est en outre limitée au montant des honoraires versés sur les 6 derniers mois.</p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 10 – DROIT APPLICABLE ET LITIGES</u></h2>
            <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant '.$contract->getTribunal().'.</p>
            <h2 style="margin-top:30px"><u>ANNEXE 1 : LISTE DES MOTS-CLÉS CIBLÉS (SEO)</u></h2>
            <div>'.$contract->getTexte().'</div>
            <p style="margin-top:40px;text-align:right">Fait à <b>'.$contract->getVille().'</b>, le <b>'.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b>, en deux exemplaires originaux.</p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">'.$agence->getRaisonSocial().'</th>
                    <th style="text-align:center">'.$client->getRaisonSocial().'</th>
                    
                </tr>
                <tr>
                    <td  style="text-align:center">'.($agence->getFonction() ? $agence->getFonction() : '.......').'</td>
                    <td style="text-align:center">'.($client->getFonction() ? $client->getFonction() : '.......').'</td>
                </tr>
                <tr>
                   
                    <th style="text-align:center">Mr. '.$agence->getManager().'</th>
                    <th style="text-align:center">'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                </tr>
            </table>';
        }
        
        // Contrat type prestation site internet/social media ////////////////////////////////////////////////
        elseif($flagSocial && $flagDev){
            $htmlInvoice .= '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE COMBINÉ</h1>
            <h2 style="text-align:center">« '.$contract->getTitre().' »</h2>
            <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>
            <p style="margin-top:20px"><b>'.$agence->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$agence->getAdresse().', immatriculée au RC de '.$agence->getVille().' sous le numéro '.$agence->getRc().', représentée par '.$agence->getManager().', ci-après dénommée le <b>« PRESTATAIRE »</b>.</p>
            <p style="margin-top:20px"><b>ET</b></p>
            <p style="margin-top:20px"><b>'.$client->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$client->getAdresse().', immatriculée au RC de '.$client->getVille().' sous le numéro '.$client->getRC().', représentée par '.$client->getTitre().'. '.$client->getNom() .' '.$client->getPrenom() .', ci-après dénommée le <b>« CLIENT »</b> D\'autre part.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 1 – OBJET DU CONTRAT</u></h2>
            <p>Le présent contrat définit les conditions techniques, juridiques et financières dans lesquelles le Prestataire assure pour le compte du Client la réalisation de prestations numériques. Le périmètre technique et fonctionnel est exclusivement celui déterminé et listé dans le devis accepté par le Client. Les prestations objet du présent contrat regroupent:</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px">La conception et le développement d\'un site internet (Vitrine, E-commerce ou Sur-mesure).</li>
                <li style="margin-top:5px">La gestion, l\'animation et l\'optimisation de la présence du Client sur les réseaux sociaux. Les plateformes spécifiques (Facebook, Instagram, LinkedIn, TikTok, etc.) sur lesquelles les parties se sont mises d\'accord sont exclusivement celles listées dans le devis accepté.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 2 – DÉTAIL DES PRESTATIONS</u></h2>
            <h3 style="margin-top:15px"><u>2.1. Développement de Site Internet</u></h3>
            <p>Le Prestataire s\'engage à réaliser les missions suivantes selon le périmètre du devis :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Conception & Design</b> : Création de la charte graphique et de l\'ergonomie (UI/UX).</li>
                <li style="margin-top:5px"><b>Développement :</b> Intégration technique, programmation des fonctionnalités et gestion de la base de données. Le développement des fonctionnalités est strictement limité à celles mises en accord et listées dans le devis accepté.</li>
                <li style="margin-top:5px"><b>Contenu Spécifique :</b> Toute demande de création de contenu spécifique, de packs dédiés ou de fonctionnalités supplémentaires fera l\'objet d\'un accord mutuel préalable et d\'une mention spécifique dans le devis.</li>
                <li style="margin-top:5px"><b>Mise en ligne & Formation :</b> Déploiement sur le serveur et formation à l\'outil d\'administration.</li>
            </ul>
            <h3 style="margin-top:15px"><u>2.2. Gestion des Réseaux Sociaux</u></h3>
            <p>Le Prestataire s\'engage à réaliser les missions suivantes sur les réseaux sociaux convenus :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Stratégie & Planning :</b> Élaboration d\'une ligne éditoriale et d\'un tableau de partage (planning éditorial) périodique.</li>
                <li style="margin-top:5px"><b>Création de Contenu :</b> Rédaction des textes et conception des visuels/vidéos. Toute demande de pack de création de contenu spécifique ou complexe fera l\'objet d\'un accord mutuel préalable entre les parties.</li>
                <li style="margin-top:5px"><b>Community Management :</b> Publication et modération des interactions.</li>
                <li style="margin-top:5px"><b>Suivi & Reporting :</b> Fourniture d\'un rapport de performance mensuel.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 3 – ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
            <h3 style="margin-top:15px"><u>3.1. Normes Techniques et Éthique</u></h3>
            <p style="margin-top:15px"><b>Pour le Développement Web</b> : Le Prestataire s\'engage à appliquer les meilleures pratiques de l\'industrie (Clean Code), incluant le respect des standards W3C pour le HTML/CSS, l\'optimisation des performances (vitesse de chargement) et la mise en œuvre de protocoles de sécurité rigoureux. Le code sera structuré pour garantir sa pérennité et une compatibilité optimale avec les navigateurs modernes et les différents terminaux (Responsive Design).</p>
            <p style="margin-top:15px"><b>Pour les Réseaux Sociaux</b> : Interdiction stricte d\'achat de faux abonnés ou de méthodes automatisées ("bots").</p>
            <h3 style="margin-top:15px"><u>3.2. Ponctualité</u></h3>
            <p style="margin-top:15px"><b>Web :</b> Le Prestataire s’engage à respecter le calendrier de livraison par étapes déterminé dans l’annexe. Toutefois, ce respect des délais est strictement conditionné par la coopération et la réactivité du Client. Le Prestataire ne pourra être tenu pour responsable de tout décalage si le Client prend un temps anormal à valider les étapes.</p>
            <p style="margin-top:15px"><b>Réseaux Sociaux :</b> Le Prestataire s\'engage au respect du partage ponctuel des posts aux dates et heures convenues dans le tableau de partage validé.</p>
            <h3 style="margin-top:15px"><u>3.3. Satisfaction Client</u></h3>
            <p style="margin-top:15px">Le Prestataire s’engage formellement à assurer la satisfaction du Client concernant le livrable final du site internet ainsi que le résultat des créations de contenu pour les réseaux sociaux. En cas de pack de contenu spécifique, le Prestataire travaillera en étroite collaboration avec le Client pour s\'assurer que le rendu final correspond aux attentes validées.</p>
            <h3 style="margin-top:15px"><u>3.4. Limitation de Responsabilité</u></h3>
            <p style="margin-top:15px">Le Prestataire est exclusivement responsable des fonctionnalités web listées dans le devis et des réseaux sociaux mentionnés dans celui-ci.</p>
            <h3 style="margin-top:15px"><u>3.5. Exonération de Responsabilité et Obligation de Moyens</u></h3>
            <p style="margin-top:15px"><b>Retard du Client :</b> Le Prestataire ne pourra être tenu pour responsable en cas de retard dans l’exécution des travaux dû au non-respect des engagements de coopération du Client.</p>
            <p style="margin-top:15px"><b>Réseaux Sociaux :</b> Les résultats dépendent des algorithmes tiers ; le Prestataire est soumis à une obligation de moyens. Si les actions techniques ont été réalisées, aucun dédommagement ne peut être exigé en cas de fluctuation de l\'engagement organique.</p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 4 – ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
            <h3 style="margin-top:15px"><u>4.1. Coopération, Ressources et Délais</u></h3>
            <p style="margin-top:15px">Le CLIENT s’engage, dans la mesure du possible, à faciliter du mieux qu’il peut le bon déroulement des missions du PRESTATAIRE. Il s\'engage à collaborer étroitement avec le Prestataire pour la réussite de la stratégie Social Media.</p>
            <p style="margin-top:15px"><b>Délai de délivrance</b> : Le CLIENT s’engage à coopérer et, sous un délai de sept (7) jours après la date de l’acceptation de l’offre, à délivrer au PRESTATAIRE tous les éléments nécessaires à la réalisation des travaux (textes, images, logos). Le Client fournit également les visuels et logos nécessaires en haute définition.</p>
            <h3 style="margin-top:15px"><u>4.2. Accès et Ressources</u></h3>
            <p style="margin-top:15px">Le Client fournit les accès nécessaires (hébergement, noms de domaine) pour le développement web. Pour les réseaux sociaux, il fournit les informations et ressources nécessaires sans délai.</p>
            <h3 style="margin-top:15px"><u>4.3. Validations et Modifications</u></h3>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Développement Web</b> : Une fois une étape validée (Maquettes, Structure, Beta), toute demande de modification ultérieure sera évaluée. Si les modifications sont mineures et peu nombreuses, le Prestataire s\'engage à les offrir gracieusement. Si elles sont majeures ou conséquentes, elles feront l\'objet d\'un accord exprès et d\'un nouveau devis.</li>
                <li style="margin-top:5px"><b>Réseaux Sociaux</b> : Le Prestataire valide avec le Client le tableau de partage avant diffusion. Une fois validé, ce dernier n\'est plus modifiable unilatéralement, sauf accord exprès du Prestataire. Toute demande de modification majeure du planning ou du contenu après validation fera l\'objet d\'un nouveau devis.</li>
            </ul>

            <h2 style="margin-top:30px"><u>ARTICLE 5 – CONDITIONS FINANCIÈRES</u></h2>
            <p style="margin-top:15px"><b>5.1. Honoraires</b> : Le prix est fixé selon le devis accepté par le Client. Le règlement peut être mensuel ou global pour la période du contrat.</p>
            <p style="margin-top:15px"><b>5.2. Modalités</b> : Les détails de paiement (par étapes, mensuel, total) sont ceux définis dans le devis accepté.</p>
            <p style="margin-top:15px"><b>5.3. Frais Annexes</b> : Les frais Ads (budgets publicitaires) sont à la charge exclusive du Client.</p>
            <p style="margin-top:15px"><b>5.4. Propriété (Web)</b> : Le site et son code source deviennent la propriété du Client uniquement après le paiement intégral des honoraires dus.</p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 6 – DURÉE ET RÉSILIATION</u></h2>
            <p style="margin-top:15px"><b>6.1. Durée</b> :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px">Pour le <b>Développement Web</b> : Le contrat prend fin à la livraison finale et après la période de garantie technique.</li>
                <li style="margin-top:5px">Pour les <b>Réseaux Sociaux</b> : Le contrat est conclu pour une durée de '.$contract->getDuration().'.</li>
            </ul>
            <p style="margin-top:15px"><b>6.2. Résiliation</b></p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Par le Prestataire (Web) :</b> Si le CLIENT ne remplit pas ses obligations ou si le paiement accuse un retard de plus de sept (7) jours</li>
                <li style="margin-top:5px"><b>Par le Client (Web) :</b> Moyennant un préavis écrit de sept (7) jours. Dans ce cas, le PRESTATAIRE sera indemnisé pour tout le travail effectué jusqu\'à la date de résiliation.</li>
                <li style="margin-top:5px"><b>Pour les Réseaux Sociaux :</b> Résiliation possible avec un préavis de [30] jours par lettre recommandée avec AR.</li>
            </ul>
            
            <h2 style="margin-top:30px"><u>ARTICLE 7 – CONFIDENTIALITÉ ET PROPRIÉTÉ</u></h2>
            <p style="margin-top:15px">Les parties s\'engagent à ne divulguer aucune information confidentielle concernant l\'activité de l\'autre partie durant toute la durée du projet.</p>
            <p style="margin-top:15px">Les contenus créés pour les réseaux sociaux deviennent la propriété du Client après paiement intégral des honoraires. De même pour le site web et son code source (voir Article 5.4).</p>
            <h2 style="margin-top:30px"><u>ARTICLE 8 – PROMOTION ET RÉFÉRENCE</u></h2>
            <p style="margin-top:15px">Sous réserve d’une réponse favorable du CLIENT à l’e-mail qui lui sera adressé par le PRESTATAIRE au terme de la prestation, le CLIENT s’engage à faire figurer le logo de Hello World ainsi que la mention discrète « élément réalisé par Hello World », éventuellement accompagnée d’un lien hypertexte pointant vers le site www.helloworld-agency.com.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 9 – RESPONSABILITÉ</u></h2>
            <p style="margin-top:15px">La responsabilité du Prestataire est limitée aux fonctionnalités listées dans le devis pour la partie web, et exclusivement aux réseaux mentionnés dans le devis pour la partie social media.    </p>
            
            <h2 style="margin-top:30px"><u>ARTICLE 10 – DROIT APPLICABLE ET LITIGES</u></h2>
            <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant '.$contract->getTribunal().'.</p>
            <p style="margin-top:40px;text-align:right">Fait à <b>'.$contract->getVille().'</b>, le <b>'.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b>, en deux exemplaires originaux.</p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">'.$agence->getRaisonSocial().'</th>
                    <th style="text-align:center">'.$client->getRaisonSocial().'</th>
                    
                </tr>
                <tr>
                    <td  style="text-align:center">'.($agence->getFonction() ? $agence->getFonction() : '.......').'</td>
                    <td style="text-align:center">'.($client->getFonction() ? $client->getFonction() : '.......').'</td>
                </tr>
                <tr>
                   
                    <th style="text-align:center">Mr. '.$agence->getManager().'</th>
                    <th style="text-align:center">'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                </tr>
            </table>';
        }
        
        // Contrat type prestation SEO ////////////////////////////////////////////////
        elseif($flagSEO){ 
            $htmlInvoice .= '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE</h1>
            <h2 style="text-align:center">« '.$contract->getTitre().' »</h2>
            <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>
            <p style="margin-top:20px"><b>'.$agence->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$agence->getAdresse().', immatriculée au RC de '.$agence->getVille().' sous le numéro '.$agence->getRc().', représentée par '.$agence->getManager().', ci-après dénommée le <b>« PRESTATAIRE »</b>.</p>
            <p style="margin-top:20px"><b>ET</b></p>
            <p style="margin-top:20px"><b>'.$client->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$client->getAdresse().', immatriculée au RC de '.$client->getVille().' sous le numéro '.$client->getRC().', représentée par '.$client->getTitre().'. '.$client->getNom() .' '.$client->getPrenom() .', ci-après dénommée le <b>« CLIENT »</b>.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 1 – OBJET DU CONTRAT</u></h2>
            <p>Le présent contrat définit les conditions dans lesquelles le Prestataire accompagne le Client dans l\'optimisation de la visibilité de son site internet sur les moteurs de recherche.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 2 – DÉTAIL DES PRESTATIONS</u></h2>
            <p>Le Prestataire s\'engage à réaliser les missions suivantes :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Sélection de mots-clés</b> : Identification de mots-clés pertinents. Cette liste est validée d\'un commun accord et figure en <b>Annexe 1</b>.</li>
                <li style="margin-top:5px"><b>Audit technique & sémantique :</b> Analyse complète de la structure du site.</li>
                <li style="margin-top:5px"><b>Optimisation On-Page :</b> Rédaction et optimisation technique (balises, contenu).</li>
                <li style="margin-top:5px"><b>Optimisation Off-Page :</b> Stratégie de netlinking (liens entrants).</li>
                <li style="margin-top:5px"><b>Suivi, Reporting & Collaboration :</b> Le Prestataire s\'engage à fournir un rapport mensuel détaillé et à collaborer étroitement avec le Client pour le tenir informé de l\'avancement et de chaque situation technique majeure.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 3 – ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
            <p style="margin-top:15px">3.1. <b>Éthique et Méthodes (White Hat)</b> : Le Prestataire garantit qu\'il n\'utilisera aucune méthode de référencement illégale ou non éthique (dite "Black Hat SEO"). Seules les méthodes conformes aux consignes des moteurs de recherche ("White Hat SEO") seront appliquées.</p>
            <p style="margin-top:15px">3.2. <b>Obligation de moyens</b> : Le Prestataire s\'engage à faire évoluer le positionnement des mots-clés. Toutefois, dépendant d\'algorithmes tiers, il ne peut garantir à 100 % une position précise.</p>
            <p style="margin-top:15px">3.3. <b>Exonération de responsabilité</b> : Si le Prestataire prouve qu’il a mis en œuvre toutes les optimisations nécessaires et les actions techniques prévues, le Client ne pourra réclamer aucun dédommagement en cas de stagnation ou baisse de positionnement.</p>
            <p style="margin-top:15px">3.4. <b>Droit de suspension</b> : En cas de défaut de paiement, les prestations peuvent être interrompues après mise en demeure.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 4 – ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
            <p style="margin-top:15px">4.1. <b>Collaboration active</b> : Le Client fournit les accès nécessaires (CMS, Search Console, etc.) sans délai.</p>
            <p style="margin-top:15px">4.2. <b>Validation</b> : Le Client valide la liste des mots-clés en Annexe 1 avant le démarrage.</p>
            <p style="margin-top:15px">4.3. <b>Exclusivité technique</b> : Le Client s\'engage à ne pas modifier la structure technique du site sans consultation préalable du Prestataire.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 5 – CONDITIONS FINANCIÈRES</u></h2>
            <p style="margin-top:15px">5.1. <b>Honoraires</b> : Le prix de la prestation est fixé dans le devis accepté par le Client. Le règlement peut être effectué, selon l\'accord convenu, soit de manière <b>mensuelle</b>, soit pour la <b>totalité de la période du contrat</b>. </p>
            <p style="margin-top:15px">5.2. <b>Modalités de paiement</b> : Les modalités spécifiques de règlement (échéancier, mode de paiement, acompte éventuel) sont celles définies dans le <b>devis accepté</b> par le Client, lequel fait partie intégrante du présent contrat. </p>
            <p style="margin-top:15px">5.3. <b>Retard</b> : Tout retard entraîne l\'application de pénalités de retard au taux légal en vigueur après mise en demeure.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 6 – DURÉE ET RÉSILIATION</u></h2>
            <p style="margin-top:15px">6.1. <b>Durée</b> : Le contrat est conclu pour une durée initiale de <b>'.$contract->getDuration().'</b>.</p>
            <p style="margin-top:15px">6.2. <b>Reconduction</b> : Tacite reconduction, sauf dénonciation avec un préavis de [30] jours.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 7 – CONFIDENTIALITÉ ET PROPRIÉTÉ</u></h2>
            <p style="margin-top:15px">Les parties s\'engagent à la confidentialité totale. Les travaux deviennent la propriété du Client après paiement intégral.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 8 – RESPONSABILITÉ ET FORCE MAJEURE</u></h2>
            <p style="margin-top:15px">La responsabilité du Prestataire est limitée au montant des honoraires versés sur les 6 derniers mois.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 9 – DROIT APPLICABLE ET LITIGES</u></h2>
            <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera soumis à la compétence exclusive du '.$contract->getTribunal().'.</p>
            <h2 style="margin-top:30px"><u>ANNEXE 1 : LISTE DES MOTS-CLÉS CIBLÉS</u></h2>
            <div>'.$contract->getTexte().'</div>
            <p style="margin-top:40px;text-align:right"><b>'.$contract->getVille().' '.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b></p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">'.$agence->getRaisonSocial().'</th>
                    <th style="text-align:center">'.$client->getRaisonSocial().'</th>
                    
                </tr>
                <tr>
                    <td  style="text-align:center">'.($agence->getFonction() ? $agence->getFonction() : '.......').'</td>
                    <td style="text-align:center">'.($client->getFonction() ? $client->getFonction() : '.......').'</td>
                </tr>
                <tr>
                   
                    <th style="text-align:center">Mr. '.$agence->getManager().'</th>
                    <th style="text-align:center">'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                </tr>
            </table>';
        }
        
        // Contrat type prestation social media ////////////////////////////////////////////////
        elseif($flagSocial){ 
            $htmlInvoice .= '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE</h1>
            <h2 style="text-align:center">« '.$contract->getTitre().' »</h2>
            <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>
            <p style="margin-top:20px"><b>'.$agence->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$agence->getAdresse().', immatriculée au RC de '.$agence->getVille().' sous le numéro '.$agence->getRc().', représentée par '.$agence->getManager().', ci-après dénommée le <b>« PRESTATAIRE »</b>.</p>
            <p style="margin-top:20px"><b>ET</b></p>
            <p style="margin-top:20px"><b>'.$client->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$client->getAdresse().', immatriculée au RC de '.$client->getVille().' sous le numéro '.$client->getRC().', représentée par '.$client->getTitre().'. '.$client->getNom() .' '.$client->getPrenom() .', ci-après dénommée le <b>« CLIENT »</b>.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 1 – OBJET DU CONTRAT</u></h2>
            <p>Le présent contrat définit les conditions dans lesquelles le Prestataire assure pour le compte du Client la gestion, l\'animation et l\'optimisation de sa présence sur les réseaux sociaux. Les plateformes spécifiques (Facebook, Instagram, LinkedIn, TikTok, etc.) sur lesquelles les parties se sont mises d\'accord sont <b>exclusivement celles déterminées et listées dans le devis accepté</b> par le Client.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 2 – DÉTAIL DES PRESTATIONS</u></h2>
            <p>Le Prestataire s\'engage à réaliser les missions suivantes sur les réseaux sociaux convenus :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Stratégie & Planning</b> : Élaboration d\'une ligne éditoriale et d\'un <b>tableau de partage (planning éditorial)</b> périodique.</li>
                <li style="margin-top:5px"><b>Création de Contenu</b> : Rédaction des textes et conception des visuels/vidéos. Toute demande de pack de création de contenu spécifique ou complexe fera l\'objet d\'un accord mutuel préalable entre les parties.</li>
                <li style="margin-top:5px"><b>Community Management</b> : Publication et modération des interactions.</li>
                <li style="margin-top:5px"><b>Suivi & Reporting</b> : Fourniture d\'un rapport de performance mensuel.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 3 – ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
            <p style="margin-top:15px">3.1. <b>Éthique & Méthodes</b> : Interdiction stricte d\'achat de faux abonnés ou de méthodes automatisées ("bots").</p>
            <p style="margin-top:15px">3.2. <b>Ponctualité des publications</b> : Le Prestataire s\'engage au respect du partage ponctuel des posts aux dates et heures convenues dans le tableau de partage validé.</p>
            <p style="margin-top:15px">3.3. <b>Satisfaction Client sur le Contenu</b> : Le Prestataire s’engage à assurer la satisfaction du Client concernant le résultat des créations de contenu. En cas de pack de contenu spécifique, le Prestataire travaillera en étroite collaboration avec le Client pour s\'assurer que le rendu final correspond aux attentes validées.</p>
            <p style="margin-top:15px">3.4. <b>Limitation de responsabilité</b> : Le Prestataire est exclusivement responsable des réseaux mentionnés dans le devis.</p>
            <p style="margin-top:15px">3.5. <b>Obligation de moyens</b> : Les résultats dépendent des algorithmes tiers ; le Prestataire est soumis à une obligation de moyens.</p>
            <p style="margin-top:15px">3.6. <b>Exonération de responsabilité</b> : Si les actions techniques ont été réalisées, aucun dédommagement ne peut être exigé en cas de fluctuation de l\'engagement organique.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 4 – ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
            <p style="margin-top:15px">4.1. <b>Collaboration Étroite</b> : Le Client s\'engage à collaborer étroitement avec le Prestataire pour la réussite de la stratégie, en fournissant les informations et ressources nécessaires sans délai.</p>
            <p style="margin-top:15px">4.2. <b>Validation du Tableau de Partage</b> : Le Prestataire valide avec le Client le tableau de partage avant diffusion. Une fois validé, ce dernier n\'est plus modifiable unilatéralement, sauf accord exprès du Prestataire.</p>
            <p style="margin-top:15px">4.3. <b>Modifications Majeures</b> : Toute demande de modification majeure du planning ou du contenu après validation fera l\'objet d\'un nouveau devis.</p>
            <p style="margin-top:15px">4.4. <b>Ressources</b> : Le Client fournit les visuels et logos nécessaires en haute définition.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 5 – CONDITIONS FINANCIÈRES</u></h2>
            <p style="margin-top:15px">5.1. <b>Honoraires</b> : Le prix est fixé selon le devis accepté. Le règlement est soit mensuel, soit global pour la période du contrat.</p>
            <p style="margin-top:15px">5.2. <b>Modalités</b> : Les détails de paiement sont ceux définis dans le devis accepté.</p>
            <p style="margin-top:15px">5.3. <b>Budgets Publicitaires</b> : Les frais Ads sont à la charge exclusive du Client.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 6 – DURÉE ET RÉSILIATION</u></h2>
            <p style="margin-top:15px">6.1. <b>Durée</b> : Le contrat est conclu pour une durée de <b>'.$contract->getDuration().'</b>.</p>
            <p style="margin-top:15px">6.2. <b>Préavis</b> : Résiliation possible avec un préavis de [30] jours par lettre recommandée avec AR.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 7 – CONFIDENTIALITÉ ET PROPRIÉTÉ</u></h2>
            <p style="margin-top:15px">Les parties s\'engagent à la confidentialité. Les contenus créés deviennent la propriété du Client après paiement intégral des honoraires.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 8 – DROIT APPLICABLE ET LITIGES</u></h2>
            <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant le '.$contract->getTribunal().'.</p>
            
            <p style="margin-top:40px;text-align:right"><b>'.$contract->getVille().' '.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b></p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">'.$agence->getRaisonSocial().'</th>
                    <th style="text-align:center">'.$client->getRaisonSocial().'</th>
                    
                </tr>
                <tr>
                    <td  style="text-align:center">'.($agence->getFonction() ? $agence->getFonction() : '.......').'</td>
                    <td style="text-align:center">'.($client->getFonction() ? $client->getFonction() : '.......').'</td>
                </tr>
                <tr>
                   
                    <th style="text-align:center">Mr. '.$agence->getManager().'</th>
                    <th style="text-align:center">'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                </tr>
            </table>';
        }
        
        /*if($contract->getShowSignature()){
            $htmlInvoice .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px">
                                <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="250">
                            </div>';
        }*/
        
        // Contrat type prestation création site internet ////////////////////////////////////////////////
        elseif($flagDev){
            $htmlInvoice .= '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE</h1>
            <h2 style="text-align:center">« '.$contract->getTitre().' »</h2>
            <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>
            <p style="margin-top:20px"><b>'.$agence->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$agence->getAdresse().', immatriculée au RC de '.$agence->getVille().' sous le numéro '.$agence->getRc().', représentée par '.$agence->getManager().', ci-après dénommée le <b>« PRESTATAIRE »</b>.</p>
            <p style="margin-top:20px"><b>ET</b></p>
            <p style="margin-top:20px"><b>'.$client->getRaisonSocial().'</b>, [Forme juridique] au capital de [Montant] €, dont le siège social est sis à '.$client->getAdresse().', immatriculée au RC de '.$client->getVille().' sous le numéro '.$client->getRC().', représentée par '.$client->getTitre().'. '.$client->getNom() .' '.$client->getPrenom() .', ci-après dénommée le <b>« CLIENT »</b>.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 1 – OBJET DU CONTRAT</u></h2>
            <p>Le présent contrat définit les conditions dans lesquelles le Prestataire assure pour le compte du Client la conception et le développement d\'un site internet (Vitrine, E-commerce ou Sur-mesure). Le périmètre technique et fonctionnel est <b>exclusivement celui déterminé et listé dans le devis accepté</b> par le Client.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 2 – DÉTAIL DES PRESTATIONS</u></h2>
            <p>Le Prestataire s\'engage à réaliser les missions suivantes selon le périmètre du devis :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px"><b>Conception & Design</b> : Création de la charte graphique et de l\'ergonomie (UI/UX).</li>
                <li style="margin-top:5px"><b>Développement</b> : Intégration technique, programmation des fonctionnalités et gestion de la base de données. Le développement des fonctionnalités est strictement limité à celles mises en accord et listées dans le devis accepté.</li>
                <li style="margin-top:5px"><b>Contenu Spécifique</b> : Toute demande de création de contenu spécifique, de packs dédiés ou de fonctionnalités supplémentaires fera l\'objet d\'un accord mutuel préalable et d\'une mention spécifique dans le devis.</li>
                <li style="margin-top:5px"><b>Mise en ligne & Formation</b> : Déploiement sur le serveur et formation à l\'outil d\'administration.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 3 – ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
            <p style="margin-top:15px">3.1. <b>Respect des standards et méthodes de développement</b> : Le Prestataire s\'engage à appliquer les meilleures pratiques de l\'industrie (Clean Code), incluant le respect des standards W3C pour le HTML/CSS, l\'optimisation des performances (vitesse de chargement) et la mise en œuvre de protocoles de sécurité rigoureux. Le code sera structuré pour garantir sa pérennité et une compatibilité optimale avec les navigateurs modernes et les différents terminaux (Responsive Design).</p>
            <p style="margin-top:15px">3.2. <b>Ponctualité et respect du planning</b> : Le Prestataire s’engage à respecter le calendrier de livraison par étapes déterminé dans l’annexe du présent contrat. Toutefois, ce respect des délais est strictement conditionné par la coopération et la réactivité du Client. En conséquence, le Prestataire ne pourra être tenu pour responsable de tout décalage ou retard dans le planning si le Client prend un temps anormal à valider les étapes intermédiaires ou à communiquer les éléments nécessaires.</p>
            <p style="margin-top:15px">3.3. <b>Satisfaction Client & Qualité du Livrable</b> : Le Prestataire s’engage formellement à assurer la satisfaction du Client concernant le livrable final du site internet.</p>
            <p style="margin-top:15px">3.4. <b>Limitation de responsabilité</b> : Le Prestataire est responsable uniquement des fonctionnalités listées dans le devis.</p>
            <p style="margin-top:15px">3.5. <b>Exonération pour retard du Client</b> : Le Prestataire ne pourra être tenu pour responsable en cas de retard dans l’exécution des travaux dû au non-respect des engagements de coopération du Client. Tout retard de livraison résultant d\'un manquement du Client ne pourra lui être imputé.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 4 – ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
            <p style="margin-top:15px">4.1. <b>Coopération et Facilitation</b> : Le CLIENT s’engage, dans la mesure du possible, à faciliter du mieux qu’il peut le bon déroulement des missions du PRESTATAIRE.</p>
            <p style="margin-top:15px">4.2. <b>Délai de délivrance des éléments</b> : Le CLIENT s’engage à coopérer et, sous un délai de trois (7) jours après la date de l’acceptation de l’offre, à délivrer au PRESTATAIRE tous les éléments nécessaires à la réalisation des travaux.</p>
            <p style="margin-top:15px">4.3. <b>Validation des Étapes et Modifications</b> : Une fois une étape validée (Maquettes, Structure, Beta), toute demande de modification ultérieure sera évaluée par le Prestataire. Si les modifications demandées sont mineures et peu nombreuses, le Prestataire s\'engage à les offrir gracieusement. En revanche, si les modifications sont jugées majeures ou conséquentes en volume, elles feront l\'objet d\'un accord exprès et de l\'établissement d\'un nouveau devis avant réalisation.</p>
            <p style="margin-top:15px">4.4. <b>Accès</b> : Le Client fournit les accès nécessaires (hébergement, noms de domaine).</p>
            <h2 style="margin-top:30px"><u>ARTICLE 5 – CONDITIONS FINANCIÈRES</u></h2>
            <p style="margin-top:15px">5.1. <b>Honoraires</b> : Le prix est fixé selon le devis accepté.</p>
            <p style="margin-top:15px">5.2. <b>Modalités</b> : Règlement par étapes, mensuel ou total selon le devis.</p>
            <p style="margin-top:15px">5.3. <b>Propriété</b> : Le site et son code source deviennent la propriété du Client uniquement après le paiement intégral.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 6 – DURÉE ET RÉSILIATION</u></h2>
            <p style="margin-top:15px">6.1. <b>Durée</b> : Le contrat prend fin à la livraison finale et après la période de garantie technique.</p>
            <p style="margin-top:15px">6.2. <b>Résiliation par le Prestataire</b> : Le PRESTATAIRE peut résilier le présent contrat si le CLIENT ne remplit pas ses obligations mentionnées ou si le paiement accuse un retard de plus de sept (7) jours.</p>
            <p style="margin-top:15px">6.3. <b>Résiliation par le Client</b> : Le CLIENT peut résilier le présent contrat moyennant un préavis écrit de sept (7) jours. Dans ce cas, le PRESTATAIRE sera indemnisé pour tout le travail effectué jusqu\'à la date de résiliation.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 7 – CONFIDENTIALITÉ</u></h2>
            <p style="margin-top:15px">Les parties s\'engagent à ne divulguer aucune information confidentielle concernant l\'activité de l\'autre partie durant toute la durée du projet.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 8 – PROMOTION ET RÉFÉRENCE</u></h2>
            <p style="margin-top:15px">Sous réserve d’une réponse favorable du CLIENT à l’e-mail qui lui sera adressé par le PRESTATAIRE au terme de la prestation, le CLIENT s’engage à faire figurer le logo de Hello World ainsi que la mention discrète « élément réalisé par Hello World », éventuellement accompagnée d’un lien hypertexte pointant vers le site www.helloworld-agency.com.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 9 – DROIT APPLICABLE ET LITIGES</u></h2>
            <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant le '.$contract->getTribunal().'.</p>
            
            <p style="margin-top:40px;text-align:right"><b>'.$contract->getVille().' '.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b></p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">'.$agence->getRaisonSocial().'</th>
                    <th style="text-align:center">'.$client->getRaisonSocial().'</th>
                    
                </tr>
                <tr>
                    <td  style="text-align:center">'.($agence->getFonction() ? $agence->getFonction() : '.......').'</td>
                    <td style="text-align:center">'.($client->getFonction() ? $client->getFonction() : '.......').'</td>
                </tr>
                <tr>
                   
                    <th style="text-align:center">Mr. '.$agence->getManager().'</th>
                    <th style="text-align:center">'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                </tr>
            </table>';
        }
        
        if($contract->getShowSignature()){
            $htmlInvoice .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px">
                                <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="250">
                            </div>';
        }
        
        $htmlInvoice .='</div>
        </body>
        </html>';
        
        /*
        if($devis->getLangue() == 'fr'){
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
                <sethtmlpagefooter name="myfooter" value="on" />
            
            <h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE</h1>
            <h2 style="text-align:center">« '.$contract->getTitre().' »</h2>
            <h2 style="margin-top:70px"><u>ENTRE LES SOUSIGNES</u></h2>
            <p style="margin-top:20px">La société <b>'.$agence->getRaisonSocial().'</b>, société à responsabilité limitée, dont le siège social est sis à <b>'.$agence->getAdresse().' '.$agence->getVille().'</b>, immatriculée au Registre du commerce de '.$agence->getVille().' sous le numéro <b>'.$agence->getRc().'</b>.</p>
            <p style="margin-top:20px">Représentée par <b>Mr. '.$agence->getManager().'</b> dûment habilité à signer les présentes.</p>
            <p>Ci –après dénommée « le <b>CONCEPTEUR</b> et/ou le <b>PRESTATAIRE</b> »</p>
            <p style="margin-top:30px;text-align:right"><b>D’UNE PART</b></p>
            <p style="text-align:right"><b>ET</b></p>
            <p style="margin-top:30px"><b>'.$client->getRaisonSocial().'</b>, immatriculée sous le numéro '.$client->getRc().' et représentée par <b>'.$client->getTitre().'. '.$client->getNom() .' '.$client->getPrenom() .'</b> agissant en sa qualité de '.($client->getFonction() ? $client->getFonction() : '......') .' dûment habilité à signer les présentes, </p>
            <p><b>Ci –après dénommée « le CLIENT »</b></p>
            <p style="margin-top:30px;text-align:right"><b>D’AUTRE PART</b></p>
            <p style="margin-top:30px"><b>'.$client->getRaisonSocial().'</b> et le <b>PRESTATAIRE</b> étant dénommés individuellement une <b>« Partie »</b> ou collectivement les <b>« Parties »</b>.</p>
            <h2 style="margin-top:30px"><u>Il est préalablement exposé : </u></h2>
            <p style="margin-top:30px"> - Que le <b>CLIENT</b> manifeste un besoin de services en marketing et communication.</p>
            <p style="margin-top:10px"> - Que le <b>PRESTATAIRE</b> est en mesure de mettre à la disposition du <b>CLIENT</b> les services dont il a besoin.</p>
            <p style="margin-top:10px"> - Que les parties ont estimé nécessaire d’insérer leurs droits et obligations ainsi que les conditions de leur relation dans un <b>CONTRAT</b> de prestation de service.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 1 – DEFINITION</u></h2>
            <p style="margin-top:20px">Pour les besoins du <b>CONTRAT</b>, les termes suivants débutant par une majuscule auront le sens qui leur est attribué ci-dessous :</p>
            <table style="width:100%;margin-top:30px">
                <tr>
                    <th style="width:20%">Contenu(s)</th>
                    <td style="padding-bottom:10px">Désigne l\'ensemble des API, Codes d’accès, Affectation de rôles, textes, images, photos, vidéos, questionnaires, sons qui seront publiés sur les réseaux sociaux.</td>
                </tr>
                <tr>
                    <th>Contrat</th>
                    <td style="padding-bottom:10px">Désigne le présent <b>CONTRAT</b> cadre (y compris son exposé préalable) ; ses annexes ainsi que ses avenants ultérieurs qui en font partie intégrante.</td>
                </tr>
                <tr>
                    <th>Prestations</th>
                    <td style="padding-bottom:10px">A la signification qui lui est attribuée à l’Article 4.</td>
                </tr>
                <tr>
                    <th>Devis</th>
                    <td style="padding-bottom:10px">Désigne l’offre commerciale du <b>PRESTATAIRE</b> signée par le <b>CLIENT</b> détaillant les Prestations souscrites et les prix applicables telle que définie à <b>l\'Annexe 1</b>.</td>
                </tr>
            </table>
            <h2 style="margin-top:30px"><u>ARTICLE 2 - OBJET DU CONTRAT</u></h2>
            <p style="margin-top:20px">Le présent <b>CONTRAT</b> a pour objet de définir : </p>
            <p style="margin-top:30px">- Les prestations à fournir par le <b>PRESTATAIRE</b> ;</p>
            <p>- Les conditions et les modalités des prestations ; </p>
            <p>- Les obligations du <b>PRESTATAIRE</b> et du <b>CLIENT</b> ;  </p>
            <p>- Les conditions commerciales. </p>
            <h2 style="margin-top:30px"><u>ARTICLE 3 – PIECES CONSTITUTIVES</u></h2>
            <p style="margin-top:20px">Le <b>CONTRAT</b> est constitué de l’ensemble des documents ci-dessous par ordre de priorité décroissante de telle sorte qu’en cas de contradiction entre les documents, le document ayant le rang le plus élevé prévaudra : </p>
            <blockquote>
                    <p><b>1. Le présent CONTRAT ; </b></p>
                    <p><b>2. Ses annexes (ci-après les « Annexes »).</b></p>
            </blockquote>
            <h2 style="margin-top:30px"><u>ARTICLE 4 - DESCRIPTION DES PRESTATIONS</u></h2>
            <p style="margin-top:20px">D’un commun accord, il a été décidé que Le <b>PRESTATAIRE</b> devra développer et/ou fournir les travaux suivants : </p>
            <h2 style="margin-top:30px"><u>Les prestations fournies par '.$agence->getRaisonSocial().'.</u></h2>
            <ol style="list-style-type: lower-alpha;">';
            
            foreach ($devis->getItems() as $key => $value) {
                $htmlInvoice .= '<li><h3 style="margin-top:20px;margin-bottom:0">'.$value->getTitre().'</li></h3>
                '.$value->getDescription();
            }
            $htmlInvoice .= '
            </ol>
            <p style="margin-top:20px">En général, les travaux susmentionnés correspondant aux prestations telles que spécifiés dans le devis fourni par le <b>PRESTATAIRE</b> et accepté par le <b>CLIENT</b>.</p>
            <p style="margin-top:20px">Davantage de précisions sur les taches sont expliquées sur le devis du <b>PRESTATAIRE</b>, qui porte le <u><b>N° '.$devis->getNumero().'</b></u><br/>Les Prestations sont confiées en exclusivité au <b>PRESTATAIRE</b>.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 5 – CONDITIONS ET MODALITES DE LA PRESTATION</u></h2>
            <ul style="padding-left:30px">
                <li style="margin-top:10px">Le <b>CLIENT</b> reconnaît avoir reçu du <b>PRESTATAIRE</b> toutes les informations et conseils qui lui étaient nécessaires pour souscrire au <b>CONTRAT</b>. Ainsi, les choix effectués par le <b>CLIENT</b> lors de sa Commande ou ultérieurement demeurent sous son entière responsabilité.</li>
                <li style="margin-top:20px">Le <b>CONTRAT</b> est considéré comme conclu à la réception du Devis cacheté et signé par le <b>CLIENT</b> avec la mention manuscrite « <b>BON POUR ACCORD</b> » et de l’acompte. Le début des travaux ne sera enclenché qu’après encaissement de l’acompte par le <b>PRESTATAIRE</b>.</li>
                <li style="margin-top:20px">Tous les éléments réalisés par le <b>PRESTATAIRE</b> lors de cette prestation de service doivent être validés obligatoirement via E-mail par le <b>CLIENT</b> avant toute utilisation.</li>
                <li style="margin-top:20px">Tous les éléments réalisés par le <b>PRESTATAIRE</b> lors de cette prestation de service deviennent la propriété du <b>Client</b>.</li>
            </ul>
            <h2 style="margin-top:30px"><u>ARTICLE 6 – OBLIGATIONS DU CLIENT</u></h2>
            <ul style="padding-left:30px">
                <li style="margin-top:10px">Le <b>CLIENT</b> s’engage à informer le <b>PRESTATAIRE</b> lors du changement des données qui nécessiterait une mise à jour des offres ou d’une manière générale, des données publiques sur internet par le <b>PRESTATAIRE</b>.</li>
                <li style="margin-top:20px">Le <b>CLIENT</b> s’engage, dans la mesure du possible, à faciliter du mieux qu’il peut le bon déroulement des missions du <b>PRESTATAIRE</b>. </li>
                <li style="margin-top:20px">Le <b>CLIENT</b> s’engage à coopérer, dans la mesure du possible, et sous un délai de sept (07) Jours après la date de l’acceptation de l’offre, afin de délivrer au <b>PRESTATAIRE</b> tous les éléments nécessaires à la réalisation du devis. Le <b>PRESTATAIRE</b> ne pourra être tenu pour responsable en cas de retard dans l’exécution des travaux dû au non-respect de cet engagement par le <b>CLIENT</b>.</li>
                <li style="margin-top:20px">Le <b>CLIENT</b> autorise le <b>PRESTATAIRE</b> à citer son nom et sa dénomination sociale ainsi que   son URL, les copies d’écran de ses pages web, à titre de références pour la promotion commerciale du PRESTATAIRE.</li>
                <li style="margin-top:20px">Le <b>CLIENT</b> s’engage à faire figurer le logo de Hello World la mention discrète « élément réalisé par Hello World » éventuellement accompagné d’un lien pointant vers '.$agence->getWebsite().'</li>
                <li style="margin-top:20px">Le <b>CLIENT</b> s’engage à fournir tous les éléments (textes, images, vidéos et sons, Base de données des produits) nécessaires à la réalisation du <b>CONTRAT</b> et à collaborer avec le <b>PRESTATAIRE</b> en mettant à sa disposition tout document ou information qui pourrait être demandé par le <b>PRESTATAIRE</b> dans les plus brefs délais.</li>
            </ul>
            <h2><u>ARTICLE 7 – OBLIGATIONS DU PRESTATAIRE</u></h2>
            <p style="margin-top:20px">Le <b>PRESTATAIRE</b> s’engage à :</p>
            <ul style="padding-left:30px">
                <li style="margin-top:5px">S’assurer que tous les éléments livrés, lors de cette prestation de service, donnent satisfaction auprès du <b>CLIENT</b> et s’avèrent utiles lors de l’utilisation.</li>
                <li style="margin-top:15px">Livrer les Prestations conformes à leur description remise au <b>CLIENT</b> et définie dans le Bon de Commande.</li>
                <li style="margin-top:15px">Réaliser ses Prestations dans le respect des règles de l’art et dans le respect des lois et règlementations en vigueur applicables à son activité.</li>
                <li style="margin-top:15px">Conseiller le <b>CLIENT</b> et le mettre en garde dans le cadre de l’exécution du <b>CONTRAT</b> eu égard aux Prestations commandées.</li>
                <li style="margin-top:15px">Mettre à disposition du <b>CLIENT</b> l’ensemble des informations pertinentes relatives aux Prestations, lui communiquer toute documentation pertinente et nécessaire à l’exécution du présent <b>CONTRAT</b> dans les meilleures conditions et à exécuter le <b>CONTRAT</b> avec loyauté et professionnalisme.</li>
                <li style="margin-top:15px">Prendre toutes les précautions nécessaires pour éviter les pertes, destructions, altérations ou erreurs dans ses données, fichiers et ses programmes, Compte tenu de la nature des Prestations.</li>
                <li style="margin-top:15px">Le <b>PRESTATAIRE</b> reconnait être tenu à une obligation générale de conseil, notamment d’information et de recommandations envers le <b>CLIENT</b>. A ce titre, le <b>PRESTATAIRE</b> doit fournir au <b>CLIENT</b> l’ensemble des conseils, mises en garde et recommandations nécessaires, notamment en termes de qualité de services, de continuité d’exploitation et de mise en l’état de l’art. </li>
                <li style="margin-top:15px">Partager avec le <b>CLIENT</b> sur une base hebdomadaire l’évolution des performances de notre présence digitale. Exemple : Diverses statistiques sur notre stratégie marketing, les publications, les interactions, le classement, etc…</li>
                <li style="margin-top:15px">Prouver ou partager avec le <b>CLIENT</b>, à sa demande les frais dépensés pour le compte de la société. Frais de publicité digitale, campagnes, frais de création de compte etc...</li>
                <li style="margin-top:15px">Le <b>PRESTATAIRE</b>, pour l\'exécution du <b>CONTRAT</b>, est tenu à une obligation de résultat. Le <b>PRESTATAIRE</b> s’engage à mettre les Prestations à la disposition du <b>CLIENT</b> en respectant les caractéristiques qui figurent au présent <b>CONTRAT</b>.</li>
                <li style="margin-top:15px">Le <b>PRESTATAIRE</b> s\'engage à faire respecter à ses représentants les obligations issues du présent <b>CONTRAT</b>. </li>
            </ul>
            <p>Dans le cadre de cette prestation de service, le <b>PRESTATAIRE</b> s’engage à livrer des rapports concernant l’état d’avancement des travaux, et les résultats obtenus à travers les actions réalisées.</p>
            <h2 style="margin-top:30px"><u>ARTICLE 8 - CONDITIONS FINANCIERS</u></h2>
            <p style="margin-top:20px">Dans le cadre de cette prestation de service, le <b>CLIENT</b> s’engage à régler au <b>PRESTATAIRE</b> la somme totale du devis pour un montant de <u><b>'.number_format($devis->getTotal(), 2, ',', ' ').' '.$devis->getDevise().' TTC</b></u> (pour toute la durée du <b>CONTRAT</b>) avec les modalités suivantes : </p>
            ';

            $htmlInvoice .= '<br>';
            foreach($devis->getConditions() as $conditon){
                $htmlInvoice .= '<b>' . $conditon->getMontant() . ' ' . $conditon->getCondition().'</b><br>';
            }

            //.$devis->getConditionPaiment().'
            $htmlInvoice .= '<h2 style="margin-top:25px"><u>ARTICLE 9 – MODIFICATION OU ANNULATION DE LA COMMANDE</u></h2>
            <ul>
                <li style="margin-top:5px">Toute modification ou annulation de la prestation par le <b>CLIENT</b> donnera lieu à un courriel de confirmation de la part du <b>PRESTATAIRE</b>.</li>
                <li style="margin-top:15px">La Prestation comprend uniquement les services spécifiés dans le <b>CONTRAT</b> et approuvés par le <b>CLIENT</b>. </li>
                <li style="margin-top:15px">La Commande ne peut être modifiée sans l’accord express de chacune des deux <b>Parties</b>. Toute modification ou ajout ultérieur apporté à la commande fera l’objet d’un nouveau devis, séparé.</li>
                <li style="margin-top:15px">Toute modification de devis demandée par le <b>CLIENT</b> et approuvée par le <b>PRESTATAIRE</b> pourra donner lieu à des délais de livraison supplémentaires.</li>
            </ul>
            <h2 style="margin-top:25px"><u>ARTICLE 10 – DELAI DE LIVRAISON</u></h2>
            <p style="margin-top:20px">Dans le cas où le <b>CLIENT</b> mettrait un temps anormalement long à fournir les éléments nécessaires à la bonne exécution du <b>CONTRAT</b>, le <b>PRESTATAIRE</b> se réserve le droit d’éditer une devis intermédiaire pour les travaux déjà réalisés. </p>
            <p style="margin-top:20px">Dans le cas où le <b>PRESTATAIRE</b> ne serait pas en mesure de délivrer les Prestations dans le respect des délais prévus, celui-ci s\'engage à en informer le <b>CLIENT</b> dès l\'apparition d\'indices lui permettant de penser qu\'il ne sera pas en mesure de délivrer les Prestations selon les délais.</p>
            
            ';
            if($contract->getGarantie()){
                $htmlInvoice .= '<h2 style="margin-top:25px"><u>ARTICLE 11 – GARANTIE</u></h2>
                <p style="margin-top:20px">La présente Garantie est d\'une durée de <b>'.$contract->getGarantie().'</b> à compter de la signature du PV de Recette pendant laquelle le <b>PRESTATAIRE</b> s’engage à corriger les Anomalies constatées sur le PV de Recette ou apparus pendant cette phase. </p>
                <p style="margin-top:20px">Pendant cette période, le <b>PRESTATAIRE</b> garantit le <b>CLIENT</b> contre toute survenance d’anomalies, incidents, erreurs ou défaut de fonctionnement de toute nature provenant d’erreurs de conception ou de réalisation. </p>
                <p style="margin-top:20px">La garantie ne porte que sur les prestations décrite dans le <b>CONTRAT</b> signé et accepté par le <b>CLIENT</b>. La garantie couvre tout vice caché ou problème pouvant survenir après la fin de la mission.</p>
                <p style="margin-top:20px">Le <b>PRESTATAIRE</b> s’engage à remédier à tout problème de fonctionnement résultant d’un défaut de conception ou d’exécution de ses prestations.</p>
                <p style="margin-top:20px">Toutefois, l’obligation de garantie du <b>PRESTATAIRE</b> est exclue dans les cas constatés de dysfonctionnements suivants : </p>
                <ul style="margin-top:20px">
                    <li><b>Dus à une mauvaise utilisation du <b>CLIENT</b> ;</b></li>
                    <li><b>Suite à une intervention du <b>CLIENT</b> ou d’un tiers autre que le <b>PRESTATAIRE</b>.</b></li>
                </ul>';
            }else{
                $htmlInvoice .= '<h2 style="margin-top:25px"><u>ARTICLE 11 : RESPONSABILITÉ</u></h2>
                <p style="margin-top:20px">Le Prestataire s’engage à mettre en œuvre tous les moyens nécessaires pour assurer la qualité des services fournis. Toutefois, sa responsabilité ne saurait être engagée en cas de dysfonctionnement dû à des erreurs ou omissions imputables au Client (par exemple, contenu incorrect ou erreur de manipulation).</p>';
            }
            
            $htmlInvoice .= '<h2 style="margin-top:25px"><u>ARTICLE 12 – PROPRIETE INTELLECTUELLE</u></h2>
            <p style="margin-top:20px">Les Parties conviennent que la propriété des Contenus réalisés par le <b>PRESTATAIRE</b> est la propriété du <b>CLIENT</b>. </p>
            <p style="margin-top:20px">Chacune des Parties s’engage à ne pas porter atteinte, directement ou indirectement à tous droits et mentions de propriété de l’autre Partie. </p>
            <p style="margin-top:20px">Le <b>PRESTATAIRE</b> cède et s’engage à céder au <b>CLIENT</b>, à titre exclusif, et au fur et à mesure de leur réalisation, l’ensemble des droits de propriété intellectuelle afférent à chaque Contenu, et notamment les droits de propriété intellectuelle définis ci-après, et ce pour toute la durée de protection légale prévue par le Code de la propriété intellectuelle, pour le monde entier.</p>
            <p style="margin-top:20px">Les droits de propriété intellectuelle sont cédés sans supplément de prix, le prix de la cession étant inclus dans la rémunération fixée à l’article 8 du présent <b>CONTRAT</b>.</p>
            <h2 style="margin-top:25px"><u>ARTICLE 13 - DUREE DU CONTRAT</u></h2>
            <p style="margin-top:20px">Le présent <b>CONTRAT</b> est valable pour une durée de <u><b>'.$contract->getDuration().'</b></u> à compter de la date de signature du <b>CONTRAT</b>.</p>
            <p style="margin-top:20px">A l’issue de cette période, les Parties pourront signer un nouveau <b>CONTRAT</b> selon les modalités et conditions à convenir entre elles. </p>
            <h2 style="margin-top:25px"><u>ARTICLE 14 - PROPOSITION FINANCIERE</u></h2>
            <p style="margin-top:20px">Le présent accord sera facturé d’un montant de <u><b>'.number_format($devis->getTotal(), 2, ',', ' ').' '.$devis->getDevise().'</b> TTC.</u></p>
            <h2 style="margin-top:25px"><u>ARTICLE 15- CONDITIONS DE PAIEMENT</u></h2>
            <p style="margin-top:20px"><b>'.$client->getRaisonSocial().'</b> émettra à <b>'.$agence->getRaisonSocial().'</b> <u>'.($contract->getNombreDePaiement() <= 9 ? "0".$contract->getNombreDePaiement() : $contract->getNombreDePaiement()).'</u> VIREMENT(s) ou CHEQUE(s).</p>
            <h2 style="margin-top:25px"><u>ARTICLE 16 - DATE DE DEBUT DE CONTRAT</u></h2>
            <p style="margin-top:20px">La société <b>'.$agence->getRaisonSocial().'</b> mettra à disposition de la société <b>'.$client->getRaisonSocial().'</b> l’ensemble de ses équipes <b>(technique, artistique, marketing…etc.)</b> ainsi que les moyens nécessaires pour le bon déroulement du partenariat à partir du <u><b>'.normalDate(date('Y-m-d',strtotime($contract->getDate().' +1 days'))) .'</b></u>. (Date prévisionnelle de début de contrat).</p>
            <h2 style="margin-top:25px"><u>ARTICLE 17 – PROTECTION DES DONNEES A CARACTERE PERSONNEL</u></h2>
            <p style="margin-top:20px">Chacune des Parties garantit l\'autre Partie du respect des obligations légales et réglementaires lui incombant au titre de la protection des données à caractère personnel, prévues par la loi n°09-08 relative à la protection des personnes physiques à l\'égard du traitement des données à caractère personnel.</p>
            <p style="margin-top:20px">En outre, au titre des données à caractère personnel auxquelles le <b>PRESTATAIRE</b> a accès dans le cadre de l’exécution des Prestations, le <b>PRESTATAIRE</b> sera considéré comme sous-traitant, agissant uniquement sur instructions du <b>CLIENT</b>.</p>
            <p style="margin-top:20px">De manière générale, le <b>PRESTATAIRE</b> s’engage à ne pas utiliser, céder ou mettre à disposition des tiers, pour quelque cause que ce soit, les données personnelles qu’il serait amené à traiter pour le compte du <b>CLIENT</b> au titre du <b>CONTRAT</b>.</p>
            <p style="margin-top:20px">A ce titre, le <b>PRESTATAIRE</b> garantit :</p>
            <ul>
                <li style="margin-top:5px">Qu’il traitera les données à caractère personnel pour le compte exclusif du <b>CLIENT</b>, conformément aux instructions de ce dernier, et uniquement sur instruction du <b>CLIENT</b>, et s’interdit de les utiliser pour son propre compte ou de les communiquer à des tiers ;</li>
                <li style="margin-top:15px">Qu’il a mis en œuvre les mesures techniques et d’organisations appropriées pour assurer la confidentialité et la sécurité des données à caractère personnel traitées dans le cadre du présent <b>CONTRAT</b> et, notamment, empêcher qu’elles soient déformées, endommagées, ou que des tiers non autorisés y aient accès.</li>
                <li style="margin-top:15px">En cas de faille de sécurité, le <b>PRESTATAIRE</b> s’engage à en informer immédiatement le <b>CLIENT</b> et à prendre toutes les mesures nécessaires pour corriger la faille dans les plus brefs délais.</li>
            </ul>
            <h2 style="margin-top:25px"><u>ARTICLE 18 – CONFIDENTIALITE</u></h2>
            <p style="margin-top:20px">Chacune des Parties s\'engage à observer la discrétion d\'usage en matière industrielle et commerciale. </p>
            <p style="margin-top:20px">Le <b>PRESTATAIRE</b> est tenu à l’obligation de discrétion pour tout ce qui concerne les faits, informations, études, développement spécifiques et décisions dont il aura connaissance au cours de l’exécution du présent <b>CONTRAT</b>.</p>
            <p style="margin-top:20px">L\'obligation de garder une information confidentielle ne s\'appliquera pas aux informations qui : </p>
            <ul>
                <li style="margin-top:5px">Sont légitimement connues du destinataire avant leur divulgation,</li>
                <li style="margin-top:15px">Ont été obtenues de plein droit par le destinataire d\'une tierce partie n\'ayant nulle obligation de confidentialité,</li>
                <li style="margin-top:15px">Sont rendues publiques par la partie qui les divulgue sans restriction aucune, </li>
                <li style="margin-top:15px">Sont divulguées par le destinataire avec accord écrit préalable émanant de la Partie qui a transmis à l\'origine les informations confidentielles.</li>
            </ul>
            <p style="margin-top:20px">Les obligations de confidentialité dureront (1) an après l\'expiration du présent <b>CONTRAT</b>.</p>
            <p style="margin-top:20px">Le <b>PRESTATAIRE</b> s’engage à restituer au <b>CLIENT</b>, sans aucune réserve ni condition préalable, tous documents, informations, sauvegardes, archives et autres supports au terme du présent <b>CONTRAT</b> pour quelque cause que ce soit et à détruire ces informations reçus en format électronique.</p>
            <p style="margin-top:20px">A cet effet, un procès-verbal contradictoire sera établi par les deux (2) Parties pour faire état de ladite restitution.</p>
            <p style="margin-top:20px">Le <b>PRESTATAIRE</b> s’interdit d’effectuer des copies ou de faire la rétention de données, traitements, fichiers, programmes ou tout autre élément appartenant au <b>CLIENT</b>, pour quelque cause que ce soit.</p>
            <p style="margin-top:20px">Chacune des Parties s’engage à conserver confidentiels, pendant la durée du <b>CONTRAT</b> et après son expiration, l’ensemble des informations, documents, savoir-faire, base de données, mots de passe et codes confidentiels en provenance de l’autre Partie dont elle pourrait avoir eu connaissance à l’occasion de l’exécution du <b>CONTRAT</b>, et ne devra les divulguer à quelques tiers que ce soit, ni les utiliser en dehors des besoins du <b>CONTRAT</b>.</p>
            <h2 style="margin-top:25px"><u>ARTICLE 19 – RESILIATION</u></h2>
            <p style="margin-top:20px">19.1 Le <b>CLIENT</b> peut demander à résilier le <b>CONTRAT</b> avant l’expiration des 15 jours premier, et sans que le <b>PRESTATAIRE</b> puisse prétendre à aucun paiement supplémentaire que ceux déjà exécutés, ni aucune indemnité, dans les cas suivants : </p>
            <ul>
                <li style="margin-top:5px"><b>Non-respect des délais et du planning établi d’un commun accord entre les parties, en ce qui concerne la livraison des travaux au <b>CLIENT</b>. Marge d’erreur tolérée 30%</b></li>
                <li style="margin-top:15px"><b>Toute action allant à l’encontre de l’image ou de l’intérêt du <b>CLIENT</b>, pouvant causer préjudice.</b></li>
                <li style="margin-top:15px"><b>Absence de communication des chiffres et performances du travail accompli.</b></li>
                <li style="margin-top:15px"><b>Absence de réponse suite à une sollicitation par écrit ou téléphone restée infructueuse.</b></li>
            </ul>
            <p style="margin-top:20px">19.2 En cas de non-respect par l\'une ou l\'autre des Parties de l\'une de ses obligations au titre du présent <b>CONTRAT</b>, et après une mise en demeure effectuée par lettre recommandée avec accusé de réception restée infructueuse pendant un délai de trente (30) jours, le <b>CONTRAT</b> pourra être résilié par la Partie non défaillante et ce, sans préjudice des dommages et intérêts auxquels cette Partie pourrait prétendre du fait des manquements constatés.</p>
            <p style="margin-top:20px">19.3 Le <b>CLIENT</b> aura également la faculté de résilier de plein droit le <b>CONTRAT</b> ou le Bon de Commande en tout ou en partie sans préavis ni indemnité au <b>PRESTATAIRE</b> en cas de manquement substantiel, incluant à titre non limitatif :</p>
            <ul>
                <li><b>La cessation volontaire d\'activité du PRESTATAIRE ;</b></li>
                <li><b>La suspension d’exécution de la Prestation sans l’accord de du <b>CLIENT</b> ;</b></li>
                <li><b>La non-conformité de la Prestation ;</b></li>
                <li><b>La violation de l’engagement de confidentialité prévu à l’article 18 ;</b></li>
            </ul>
            <p style="margin-top:20px">19.4 Le <b>PRESTATAIRE</b> peut demander à résilier le présent <b>CONTRAT</b> à tout moment, moyennant un préavis de trente (30) jours calendaires adressé au <b>CLIENT</b> par lettre recommandée avec accusé de réception.</p>
            <ul>
                <li style="margin-top:5px"><b> Non-paiement des montants établis d’un commun accord à leur échéance convenue.</b></li>
                <li style="margin-top:15px"><b> Absence de réponse suite à une sollicitation par écrit ou téléphone restée infructueuse pendant plus de 07 jours.</b></li>
            </ul>
            <p style="margin-top:20px">A l’expiration du présent <b>CONTRAT</b> ou en cas de résiliation, pour quelque cause que ce soit, tout document, fichier, rapport, étude etc, appartenant ou concernant le <b>CLIENT</b> qui serait en possession du <b>PRESTATAIRE</b> sous quelque support devra être restituer au <b>CLIENT</b> en sa qualité de propriétaire exclusif.</p>
            <h2 style="margin-top:25px"><u>ARTICLE 20 – INTUITU PERSONAE ET CESSION</u></h2>
            <p style="margin-top:20px">Le présent <b>CONTRAT</b> est conclu en considération de la personne du <b>PRESTATAIRE</b>, qui ne pourra substituer de tiers dans la réalisation des Prestations ci-dessus définies.</p>
            <p style="margin-top:20px">Le <b>CLIENT</b> sera libre de procéder à toute cession ou transfert, total ou partiel, de ses droits et obligations au titre du présent contrat.</p>
            <h2 style="margin-top:25px"><u>ARTICLE 21 – NOTIFICATION</u></h2>
            <p style="margin-top:20px">Toute notification au titre du présent <b>CONTRAT</b> devra être faite aux adresses suivantes :</p>
            <p style="margin-top:40px">Pour <b>CLIENT :</b></p>
            <p style="margin-top:10px"><b>'.$client->getRaisonSocial().'</b><br/>A l’attention de <b>'.$client->getTitre().'.</b> '.$client->getNom().' '.$client->getPrenom().',<br/>'.($client->getAdresse() ? $client->getAdresse() : '......').'-<br/>'.($client->getVille() ? $client->getVille() : '......').'</p>
            <p style="margin-top:40px">Pour <b>PRESTATAIRE :</b></p>
            <p style="margin-top:10px"><b>'.$agence->getRaisonSocial().'</b><br/>A l’attention de M. '.$agence->getManager().'<br/>'.($agence->getAdresse() ? $agence->getAdresse() : '......').'<br/>'.($agence->getVille() ? $agence->getVille() : '......').'</p>
            <p style="margin-top:40px">Ou à toute autre adresse notifiée par les Parties conformément au présent article.</p>
            <h2 style="margin-top:40px"><u>ARTICLE 22 – RECOURS EN CAS DE LITIGE</u></h2>
            <p style="margin-top:20px">Tout litige pouvant surgir à l\'occasion de l\'exécution du <b>CONTRAT</b> sera soumis au '.$contract->getTribunal().'</p>
            <p style="margin-top:20px">Le présent <b>CONTRAT</b> prend effet le <b>'.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b> portant la signature précédée de la mention « Lu et Approuvé, bon pour accord », daté et signé par <b>'.$client->getTitre().'.</b> '.$client->getNom().' '.$client->getPrenom().' ;</p>
            <p style="margin-top:20px">Le présent <b>CONTRAT</b> est établi en DEUX (2) exemplaires originaux entre les deux parties à Marrakech.</p>
            <p style="margin-top:40px;text-align:right"><b>'.$contract->getVille().' '.normalDate(date('Y-m-d',strtotime($contract->getDate()))) .'</b></p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">'.$agence->getRaisonSocial().'</th>
                    <th style="text-align:center">'.$client->getRaisonSocial().'</th>
                    
                </tr>
                <tr>
                    <td  style="text-align:center">'.($agence->getFonction() ? $agence->getFonction() : '.......').'</td>
                    <td style="text-align:center">'.($client->getFonction() ? $client->getFonction() : '.......').'</td>
                </tr>
                <tr>
                   
                    <th style="text-align:center">Mr. '.$agence->getManager().'</th>
                    <th style="text-align:center">'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                </tr>
            </table>
            ';
            
            if($contract->getShowSignature()){
                $htmlInvoice .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px">
                                    <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="250">
                                </div>';
            }
            
            $htmlInvoice .='</div>
            </body>
            </html>';
        }else{
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
                <sethtmlpagefooter name="myfooter" value="on" />
            
                <h1 style="text-align:center">'.$contract->getTitre().'</h1>
                <p style="margin-top:20px;border:1px solid black;padding:10px;text-align:center">This Services Agreement is entered into as of the '.$contract->getDate().' by and between:</p>
                <p style="margin-top:30px"><b><u>SERVICES PROVIDER/PROVIDER</u>: '.$agence->getRaisonSocial().'</b>, located at '.$agence->getAdresse().', hereinafter referred to as " <b>SERVICE PROVIDER</b> "</p>
                <p style="margin-top:20px;"><b>AND</b></p>
                <p style="margin-top:20px;"><b><u>CLIENT<u>:  '.$client->getRaisonSocial().'</b> located at <b>'.$client->getAdresse().'</b>, hereinafter referred to as " <b>CLIENT</b> "</p>
                <p style="margin-top:30px"><b><u>It is previously exposed :</u></b></p>
                <ul style="margin-top:20px">
                    <li>The <b>CLIENT</b> expresses a need for marketing services.</li>
                    <li>The <b>SERVICE PROVIDER</b> is capable of delivering the required services to the <b>CLIENT<b></li>
                    <li>The parties have agreed to formalize their relationship through a service contract NAMED (<b>'.$contract->getTitre().'</b>).</li>
                </ul>
                <h2 style="margin-top:25px"><u>CONTRACT VOCABULARY DEFINITION </u></h2>
                <p style="margin-top:20px">The capitalized terms used in this Contract are defined as follows:</p>
                <table style="width:100%;margin-top:20px">
                    <tr>
                        <th style="width:20%">CONTENT(s)</th>
                        <td style="padding-bottom:10px">Refers to all APIs, Access Codes, Role Assignments, texts, images, photos, videos, Survey, sounds that will be published on social networks.</td>
                    </tr>
                    <tr>
                        <th>CONTRACT</th>
                        <td style="padding-bottom:10px">Refers to this framework Contract (including its preamble); its appendices as well as its subsequent amendments which are an integral part of it.</td>
                    </tr>
                    <tr>
                        <th>SERVICES</th>
                        <td style="padding-bottom:10px">Has the meaning attributed to it in Article 2.</td>
                    </tr>
                    <tr>
                        <th>FINANCIAL PROPOSAL</th>
                        <td style="padding-bottom:10px">Refers to the <b>SERVICE PROVIDER</b> commercial offer signed by the <b>CLIENT</b> detailing the subscribed <b>SERVICES</b> and applicable price as defined in Appendix (<b>'.$devis->getNumero().'</b>). </td>
                    </tr>
                </table>
                <h2 style="margin-top:25px"><u>1 -	PURPOSE OF THE CONTRACT</u></h2>
                <p style="margin-top:20px;">The purpose of this contract is to define :</p>
                <ul style="margin-top:20px;">
                    <li>The services to be provided by the <b>SERVICE PROVIDER</b>.</li>
                    <li>The terms and conditions of the <b>SERVICES</b>.</li>
                    <li>The obligations of the <b>SERVICE PROVIDER</b> and the <b>CLIENT</b>.</li>
                    <li>Commercial condition</li>
                </ul>
                <h2 style="margin-top:25px"><u>2 -	SERVICES</u></h2>
                <p style="margin-top:20px;">By mutual agreement, it has been decided that the <b>SERVICE PROVIDER</b> must develop and/or provide the following work, To ensure the agreed deliverables are completed to the <b>CLIENT</b> satisfaction, the following <b>KPIs</b> (Key Performance Indicators) will be tracked and reported monthly:</p>
                <ul>';
            
                foreach ($devis->getItems() as $key => $value) {
                    $htmlInvoice .= '<li><h3 style="margin-top:20px;margin-bottom:0">'.$value->getTitre().'</li></h3>
                    '.$value->getDescription();
                }
                $htmlInvoice .= '
                </ul>
                <p style="margin-top:20px;">Completion of these KPIs, verified in monthly reports provided by <b>SERVICE PROVIDER</b>, will serve as proof of deliverable completion.</p>
                <p style="margin-top:20px;">The work performed constitutes fulfillment of the services specified in the <b>SERVICE PROVIDER\'s</b> Quote <u>#<b>'.$devis->getNumero().'</b></u>, which was duly accepted by the <b>CLIENT</b></p>
                <h2 style="margin-top:25px"><u>3 -	CONDITIONS AND TERMS OF THE SERVICE</u></h2>
                <ul style="margin-top:20px;">
                    <li>The <b>CLIENT</b> confirms having received comprehensive information and advice from the <b>SERVICE PROVIDER</b> regarding the service. The <b>CLIENT</b> understands that the decision to subscribe to the service and any subsequent choices are entirely at their own discretion.</li>
                    <li>The Contract shall be considered concluded upon receipt of the <b>SERVICE PROVIDER</b>\'s quote, duly signed and stamped by the <b>CLIENT</b> with the handwritten acknowledgment "<b>GOOD FOR AGREEMENT</b>," and the receipt of the initial down payment. </li>
                    <li>All the elements produced by the <b>PROVIDER</b> during this service provision become the property of the <b>CLIENT</b>.</li>
                </ul>
                <h2 style="margin-top:25px"><u>4 -	CLIENT OBLIGATIONS</u></h2>
                <ul style="margin-top:20px;">
                    <li>The <b>CLIENT</b> commits to notifying the <b>PROVIDER</b> of any marketing activities that could affect the Services included in the current contract</li>
                    <li>The <b>CLIENT</b> must cooperate with the <b>PROVIDER</b> to ensure the smooth execution of the <b>SERVICES</b>.</li>
                    <li>The <b>CLIENT</b> acknowledges and accepts the terms and conditions mentioned in the financial proposal.</li>
                    <li>The <b>CLIENT</b> shall review and verify the report within a period of five days maximum, after which the project will proceed to the next phase.</li>
                </ul>
                <h2 style="margin-top:25px"><u>5 -	FINANCIAL CONDITIONS</u></h2>
                <p style="margin-top:20px;">As part of this service, The <b>CLIENT</b> commits to paying the <b>PROVIDER</b> a total sum of <u><b>'.number_format($devis->getTotal(), 2, ',', ' ').' '.$devis->getDevise().'</b></u> for the Services rendered, as detailed in the quote <u><b>N° '.$devis->getNumero().'</b></u>.</p>
                '.$devis->getConditionPaiment().'
                <h2 style="margin-top:25px"><u>6 -	SERVICE PROVIDER\'S OBLIGATIONS</u></h2>
                <p style="margin-top:20px">The <b>SERVICE PROVIDER</b> undertakes to :</p>
                <ul style="margin-top:20px">
                    <li>Ensure that all the elements delivered, during this service, give satisfaction to the <b>CLIENT</b> and prove useful during use.</li>
                    <li>The <b>SERVICE PROVIDER</b> commits to delivering the project on time and within the specified deadlines, provided that the <b>CLIENT</b> provides all necessary information, approvals, and cooperation in a timely manner. Any delays caused by the <b>CLIENT</b> may result in corresponding delays in the project timeline</li>
                    <li>As part of this service, the <b>SERVICE PROVIDER</b> undertakes to deliver reports on the progress of the work, and the results obtained through the actions carried out.</li>
                </ul>
                <h2 style="margin-top:25px"><u>7 -	CONTRACT DURATION</u></h2>
                <p style="margin-top:20px">The Contract shall have a duration of <b>'.$contract->getDuration().'</b>. The warranty period for the <b>website</b> shall be three months, commencing on the date of Contract signing. However, the specific warranty period may vary based on the terms outlined in the signed quote</p>
                <h2 style="margin-top:25px">8 -	TERMINATION:</h2>
                <ul style="margin-top:20px">
                    <li>The <b>CLIENT</b> may terminate this Agreement with seven (7) days\' prior written notice to the <b>SERVICE PROVIDER</b>. The <b>SERVICE PROVIDER</b> will be 
                    compensated for all work completed up to the termination date, based on the itemized deliverables and <b>KPIs</b> outlined in this Agreement.
                    </li>
                    <li>The <b>SERVICE PROVIDER</b> may terminate this Agreement if the <b>CLIENT</b> fails to fulfill its obligations mentioned in the current Agreement or if payment is more than seven (7) days overdue.</li>
                </ul>
                <h2 style="margin-top:25px"><u>9 -	DELIVERY TIME</u></h2>
                <p style="margin-top:20px">In the event that the <b>CLIENT</b> takes an abnormally long time to provide the elements necessary for the proper performance of the contract, the <b>SERVICE PROVIDER</b> reserves the right to issue an interim invoice for the work already carried out.
                <br/>
                Delays in delivery cannot give rise to the payment of damages, indemnities or penalties.
                </p>';
                
                if($contract->getGarantie()){
                    $htmlInvoice .= '<h2 style="margin-top:25px"><u>10 - GUARANTEE </u></h2>
                        <ul style="margin-top:20px">
                            <li>The warranty only covers the services described in <b>the contract/quote</b> signed and accepted by the <b>CLIENT</b>. </li>
                            <li>The <b>SERVICE PROVIDER</b> undertakes to remedy any operating problem resulting from a defect in the design or execution of its services.</li>
                        </ul>
                        <p style="margin-top:20px">However, the <b>SERVICE PROVIDER\'s</b> warranty obligation is excluded in the cases of the following malfunctions:</p>
                        <ul style="margin-top:20px">
                            <li>Due to improper use by the <b>CLIENT</b></li>
                            <li>Following an intervention by the <b>CLIENT</b> or a third party other than the <b>PROVIDER</b>.</li>
                        </ul>';
                }else{
                    $htmlInvoice .= '<h2 style="margin-top:25px"><u>10 - RESPONSABILITY </u></h2>
                    <p style="margin-top:20px">The Service Provider agrees to implement all necessary means to ensure the quality of the services provided. However, their responsibility cannot be engaged in case of malfunction due to errors or omissions attributable to the Client (for example, incorrect content or user error).</p>';
                }
                
                $htmlInvoice .= '<h2 style="margin-top:25px"><u>11 - PRIVACY </u></h2>
                <p style="margin-top:20px">Each of the parties undertakes to keep confidential, during the term of the contract and after its expiry, all the information, documents, know-how, database, passwords, and confidential codes from the other party whose it may have had
                knowledge during the execution of the contract, and must not disclose them to any third parties whatsoever, nor use them outside the needs of the contract.
                </p>
                <h2 style="margin-top:25px"><u>12 - GOVERNING LAW </u></h2>
                <p style="margin-top:20px">This Agreement shall be governed by the laws of <b>'.$contract->getTribunal().'</b>.</p>
                <p style="margin-top:150px"></p>
                <table style="width:100%;text-align:center">
                    <tr>
                        <th style="width:50%">'.$agence->getRaisonSocial().'</th>
                        <th>'.$client->getRaisonSocial().'</th>
                    </tr>
                    <tr>
                        <td>'.$agence->getFonction().'</td>
                        <td>'.$client->getFonction().'</td>
                    </tr>
                    <tr>
                        <th>Mr. '.$agence->getManager().'</th>
                        <th>'.$client->getTitre().'. '.$client->getNom().' '.$client->getPrenom().'</th>
                    </tr>
                </table>
            ';
            
            if($contract->getShowSignature()){
                $htmlInvoice .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px">
                                    <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="250">
                                </div>';
            }
            
            $htmlInvoice .='</div>
            </body>
            </html>';
        } */

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

        $file_name = 'contract.pdf';
        $mpdf->Output($file_name, 'I');
    }
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
    $contract->setTribunal($data['tribunal']);
    $contract->setNombreDePaiement($data['nombre_de_paiement']);
	$contract->setTexte($data['texte']);
	$contract->setShowSignature(isset($data['show_signature']) ? 1 : 0);
    $contract->setStatus($data['status']);
    $contract->setDateAdd(date("Y-m-d H:i:s"));
    $contract->setLastEdit(date("Y-m-d H:i:s"));
    $contract->setLangue($_SESSION['langue']);

    return $contract;
}