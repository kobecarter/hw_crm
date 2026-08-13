<?php

// Générateur de contrat (com_contract) : construit le CORPS HTML (h1/h2/p/ul/table, aucune
// balise spécifique à mPDF) d'un contrat à partir du devis lié - réutilisé tel quel par
// pdfContract() (mPDF) et docxContract() (PhpWord, cf. controleurs/contract/controleur.php) pour
// n'avoir qu'une seule source de vérité du contenu légal.
//
// Sélection du modèle : les prestations du devis (item_devis::getService()->getCategorie()) sont
// regroupées par catégorie réelle (crm_categorie : 1=Développement, 2=Référencement SEO,
// 4=Marketing - catégorie la plus proche de la gestion réseaux sociaux, aucune catégorie
// "Réseaux sociaux" dédiée n'existe dans crm_categorie). Les combinaisons Dev+SEO+Social,
// Dev seul et Social seul utilisent les modèles fournis le 2026-08-13 (mêmes contrats que ceux
// déjà signés chez Verse Concept/HW Label, juste paramétrés par agence/client/devis au lieu
// d'être remplis à la main). Les combinaisons Dev+SEO, Dev+Social et SEO seul n'ont pas reçu de
// nouveau modèle : le texte d'origine de pdfContract() est conservé tel quel (jamais réécrit sans
// exemple validé), seule la liste des prestations de l'Article 2 devient dynamique (portée par
// tous les modèles, anciens et nouveaux, cf. "dans tous les cas on prend le détail du devis").
class contractGenerator
{
    const CATEGORIE_DEV = 1;
    const CATEGORIE_SEO = 2;
    const CATEGORIE_SOCIAL = 4;

    public static function genererCorpsContrat(contract $contract)
    {
        $devis = $contract->getDevis();
        $client = $devis->getClient();
        // L'agence du contrat est TOUJOURS celle du client du devis - jamais celle de la session
        // en cours (un superuser peut consulter un contrat pendant qu'une autre agence est
        // sélectionnée dans son propre menu, le logo/l'en-tête/le pied de page ne doivent jamais
        // dépendre de ça).
        $agence = agence::find($client->getAgence()->getId(), $devis->getLangue());

        $parCategorie = self::grouperItemsParCategorie($devis);
        $flagDev = !empty($parCategorie[self::CATEGORIE_DEV]);
        $flagSeo = !empty($parCategorie[self::CATEGORIE_SEO]);
        $flagSocial = !empty($parCategorie[self::CATEGORIE_SOCIAL]);

        if ($flagDev && $flagSeo && $flagSocial) {
            $html = self::templateDevSeoSocial($contract, $agence, $client, $parCategorie);
        } elseif ($flagSeo && $flagDev) {
            $html = self::templateDevSeo($contract, $agence, $client, $parCategorie);
        } elseif ($flagSocial && $flagDev) {
            $html = self::templateDevSocial($contract, $agence, $client, $parCategorie);
        } elseif ($flagSeo) {
            $html = self::templateSeoSeul($contract, $agence, $client, $parCategorie);
        } elseif ($flagSocial) {
            $html = self::templateSocialSeul($contract, $agence, $client, $parCategorie);
        } elseif ($flagDev) {
            $html = self::templateDevSeul($contract, $agence, $client, $parCategorie);
        } else {
            $html = self::templateGenerique($contract, $agence, $client, $devis);
        }
        return self::versLatin1Safe($html);
    }

    // crm_contract.corps_genere est en charset "latin1" (cf. com_config/classes/mysql.php) : les
    // prestations viennent du devis en HTML riche CKEditor et contiennent souvent des caractères
    // hors Latin-1 (€, •, ™, tirets/guillemets typographiques...), ce que latin1 ne peut pas
    // stocker et fait échouer l'INSERT ("Incorrect string value"). On les convertit en entités
    // HTML numériques (&#8364; etc.) - stockable en ASCII pur, et qui s'affiche identiquement une
    // fois rendu en HTML. Les caractères Latin-1 (accents français) ne sont pas touchés : ils
    // continuent de profiter du même contournement double-encodage que le reste de la base.
    public static function versLatin1Safe($html)
    {
        return mb_encode_numericentity((string) $html, array(0x100, 0xffff, 0, 0xffff), 'UTF-8');
    }

    // Regroupe les lignes du devis par catégorie de service réelle - remplace les listes d'IDs de
    // service codées en dur de l'ancienne version (14 IDs sur 182 services au total, silencieux
    // dès qu'un devis utilisait un service en dehors de cette liste figée).
    private static function grouperItemsParCategorie(devis $devis)
    {
        $parCategorie = array();
        foreach ($devis->getItems() as $item) {
            $service = $item->getService();
            if (!$service || !$service->getId() || !$service->getCategorie()) {
                continue;
            }
            $idCategorie = $service->getCategorie()->getId();
            if (!isset($parCategorie[$idCategorie])) {
                $parCategorie[$idCategorie] = array();
            }
            $parCategorie[$idCategorie][] = $item;
        }
        return $parCategorie;
    }

    // Liste des prestations d'un groupe - le titre/la description viennent du devis (jamais d'un
    // texte générique figé), c'est la ligne réellement vendue au client. La description d'un
    // item_devis est du HTML riche produit par CKEditor (souvent ses propres <ul>/<li>/<h3>) -
    // jamais échappée, et rendue en bloc séparé plutôt qu'imbriquée dans un <li> pour rester un
    // HTML valide même quand la description contient elle-même une liste.
    private static function listeItemsHtml($items)
    {
        $html = '<ul style="padding-left:30px">';
        foreach ($items as $item) {
            $titre = trim((string) $item->getTitre());
            if ($titre === '' && $item->getService()) {
                $titre = $item->getService()->getTitre();
            }
            $html .= '<li style="margin-top:5px"><b>' . htmlspecialchars($titre) . '</b></li>';
        }
        $html .= '</ul>';

        foreach ($items as $item) {
            $description = trim((string) $item->getDescription());
            if ($description === '') {
                continue;
            }
            $titre = trim((string) $item->getTitre());
            if ($titre === '' && $item->getService()) {
                $titre = $item->getService()->getTitre();
            }
            $html .= '<div style="margin-top:8px;margin-left:15px"><p style="margin-bottom:3px"><i>Détail - ' . htmlspecialchars($titre) . '</i></p>' . $description . '</div>';
        }
        return $html;
    }

    private static function nomClient(client $client)
    {
        return trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom());
    }

    private static function representantClient(client $client)
    {
        return trim($client->getTitre() . '. ' . $client->getNom() . ' ' . $client->getPrenom());
    }

    // Bloc "ENTRE LES SOUSSIGNÉS" + bloc signature en pied de contrat - identiques d'un modèle à
    // l'autre (mêmes champs agence/client), factorisés pour ne pas les dupliquer 6 fois.
    private static function blocIdentification(agence $agence, client $client)
    {
        return '<p style="margin-top:20px"><b>' . $agence->getRaisonSocial() . '</b>, dont le siège social est sis à ' . $agence->getAdresse() . ', immatriculée au RC de ' . $agence->getVille() . ' sous le numéro ' . $agence->getRc() . ', représentée par ' . $agence->getManager() . ', ci-après dénommée le <b>« PRESTATAIRE »</b>.</p>
            <p style="margin-top:20px"><b>ET</b></p>
            <p style="margin-top:20px"><b>' . self::nomClient($client) . '</b>, dont le siège social est sis à ' . $client->getAdresse() . ', immatriculée au RC de ' . $client->getVille() . ' sous le numéro ' . $client->getRc() . ', représentée par ' . self::representantClient($client) . ', ci-après dénommée le <b>« CLIENT »</b>.</p>';
    }

    private static function blocSignature(contract $contract, agence $agence, client $client)
    {
        return '<p style="margin-top:40px;text-align:right">Fait à <b>' . $contract->getVille() . '</b>, le <b>' . normalDate(date('Y-m-d', strtotime($contract->getDate()))) . '</b>, en deux exemplaires originaux.</p>
            <table style="margin-top:40px;width:100%;text-align:center">
                <tr>
                    <th style="width:50%;text-align:center">' . $agence->getRaisonSocial() . '</th>
                    <th style="text-align:center">' . self::nomClient($client) . '</th>
                </tr>
                <tr>
                    <td style="text-align:center">' . ($agence->getFonction() ? $agence->getFonction() : '.......') . '</td>
                    <td style="text-align:center">' . ($client->getFonction() ? $client->getFonction() : '.......') . '</td>
                </tr>
                <tr>
                    <th style="text-align:center">Mr. ' . $agence->getManager() . '</th>
                    <th style="text-align:center">' . self::representantClient($client) . '</th>
                </tr>
            </table>';
    }

    // ---- Nouveau modèle (2026-08-13) : Développement Web + SEO + Réseaux Sociaux combinés -----
    private static function templateDevSeoSocial(contract $contract, agence $agence, client $client, $parCategorie)
    {
        $html = '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE COMBINÉ</h1>
        <h2 style="text-align:center">DÉVELOPPEMENT WEB - RÉFÉRENCEMENT NATUREL (SEO) - GESTION DES RÉSEAUX SOCIAUX</h2>
        <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>'
        . self::blocIdentification($agence, $client)
        . '<h2 style="margin-top:30px"><u>ARTICLE 1 - OBJET DU CONTRAT</u></h2>
        <p>Le présent contrat définit les conditions techniques, juridiques et financières dans lesquelles le Prestataire assure pour le compte du Client un ensemble de services numériques intégrés. Le périmètre technique et fonctionnel est exclusivement celui déterminé et listé dans le devis accepté par le Client. Les prestations objet du présent contrat regroupent :</p>
        <ul style="padding-left:30px">
            <li style="margin-top:5px">La conception et le développement d\'un site internet (Vitrine, E-commerce ou Sur-mesure).</li>
            <li style="margin-top:5px">L\'optimisation de la visibilité du site sur les moteurs de recherche (Référencement Naturel / SEO).</li>
            <li style="margin-top:5px">La gestion, l\'animation et l\'optimisation de la présence du Client sur les réseaux sociaux.</li>
        </ul>
        <h2 style="margin-top:30px"><u>ARTICLE 2 - DÉTAIL DES PRESTATIONS</u></h2>
        <h3 style="margin-top:15px"><u>2.1. Développement de Site Internet</u></h3>
        <p>Le Prestataire s\'engage à réaliser les missions suivantes selon le périmètre du devis :</p>'
        . self::listeItemsHtml($parCategorie[self::CATEGORIE_DEV])
        . '<h3 style="margin-top:15px"><u>2.2. Référencement Naturel (SEO)</u></h3>
        <p>Le Prestataire accompagne le Client dans l\'optimisation de sa visibilité sur les moteurs de recherche via :</p>'
        . self::listeItemsHtml($parCategorie[self::CATEGORIE_SEO])
        . '<h3 style="margin-top:15px"><u>2.3. Gestion des Réseaux Sociaux</u></h3>
        <p>Le Prestataire assure la gestion sur les plateformes convenues (Facebook, Instagram, LinkedIn, TikTok, etc.) :</p>'
        . self::listeItemsHtml($parCategorie[self::CATEGORIE_SOCIAL])
        . '<h2 style="margin-top:30px"><u>ARTICLE 3 - ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
        <h3 style="margin-top:15px"><u>3.1. Normes Techniques et Éthique</u></h3>
        <p style="margin-top:15px"><b>Pour le Développement Web</b> : Le Prestataire s\'engage à appliquer les meilleures pratiques de l\'industrie (Clean Code), incluant le respect des standards W3C pour le HTML/CSS, l\'optimisation des performances (vitesse de chargement) et la mise en œuvre de protocoles de sécurité rigoureux. Le code sera structuré pour garantir sa pérennité et une compatibilité optimale avec les navigateurs modernes et les différents terminaux (Responsive Design).</p>
        <p style="margin-top:15px"><b>Pour le SEO</b> : Le Prestataire garantit qu\'il n\'utilisera aucune méthode de référencement illégale ou non éthique (dite "Black Hat SEO"). Seules les méthodes conformes aux consignes des moteurs de recherche ("White Hat SEO") seront appliquées.</p>
        <p style="margin-top:15px"><b>Pour les Réseaux Sociaux</b> : Il est fait interdiction stricte d\'achat de faux abonnés ou de l\'utilisation de méthodes automatisées ("bots").</p>
        <h3 style="margin-top:15px"><u>3.2. Obligation de Moyens et Limitations (SEO &amp; Social)</u></h3>
        <p style="margin-top:15px">Le Prestataire est soumis à une obligation de moyens. Les résultats dépendent d\'algorithmes tiers (Google, Meta, etc.) sur lesquels le Prestataire n\'a pas de contrôle direct. Par conséquent :</p>
        <ul style="padding-left:30px">
            <li style="margin-top:5px">En SEO : Il ne peut garantir à 100 % une position précise.</li>
            <li style="margin-top:5px">Sur les Réseaux Sociaux : Aucun dédommagement ne peut être exigé en cas de fluctuation de l\'engagement organique si les actions techniques prévues ont été réalisées.</li>
        </ul>
        <h3 style="margin-top:15px"><u>3.3. Exonération de Responsabilité</u></h3>
        <p style="margin-top:15px"><b>SEO</b> : Si le Prestataire prouve qu\'il a mis en œuvre toutes les optimisations nécessaires et les actions techniques prévues, le Client ne pourra réclamer aucun dédommagement en cas de stagnation ou baisse de positionnement.</p>
        <p style="margin-top:15px"><b>Retard du Client</b> : Le Prestataire ne pourra être tenu pour responsable en cas de retard dans l\'exécution des travaux dû au non-respect des engagements de coopération du Client. Tout retard de livraison résultant d\'un manquement du Client ne pourra lui être imputé.</p>
        <h3 style="margin-top:15px"><u>3.4. Ponctualité et Satisfaction</u></h3>
        <p style="margin-top:15px">Le Prestataire s\'engage à respecter le calendrier de livraison par étapes (Web) et le partage ponctuel des posts aux dates et heures convenues (Social). Toutefois, ce respect des délais est strictement conditionné par la coopération et la réactivité du Client. En conséquence, le Prestataire ne pourra être tenu pour responsable de tout décalage si le Client prend un temps anormal à valider les étapes intermédiaires.</p>
        <p style="margin-top:15px">Le Prestataire s\'engage formellement à assurer la satisfaction du Client concernant le livrable final du site internet et le résultat des créations de contenu social.</p>
        <h3 style="margin-top:15px"><u>3.5. Droit de Suspension</u></h3>
        <p style="margin-top:15px">En cas de défaut de paiement, les prestations (notamment SEO et gestion sociale) peuvent être interrompues après mise en demeure restée infructueuse.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 4 - ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
        <h3 style="margin-top:15px"><u>4.1. Coopération, Ressources et Délais</u></h3>
        <p style="margin-top:15px">Le CLIENT s\'engage à coopérer étroitement avec le Prestataire.</p>
        <ul style="padding-left:30px">
            <li style="margin-top:5px"><b>Délai de délivrance</b> : Le Client s\'engage, sous un délai de sept (7) jours après la date de l\'acceptation de l\'offre, à délivrer au PRESTATAIRE tous les éléments nécessaires à la réalisation des travaux (Web).</li>
            <li style="margin-top:5px"><b>Accès et Ressources</b> : Le Client fournit sans délai les accès nécessaires (hébergement, noms de domaine, CMS, Search Console, comptes réseaux sociaux) ainsi que les visuels et logos en haute définition.</li>
        </ul>
        <h3 style="margin-top:15px"><u>4.2. Validations et Modifications</u></h3>
        <ul style="padding-left:30px">
            <li style="margin-top:5px"><b>Développement Web</b> : Une fois une étape validée (Maquettes, Structure, Beta), toute demande de modification ultérieure sera évaluée. Si les modifications sont mineures et peu nombreuses, le Prestataire s\'engage à les offrir gracieusement. Si elles sont majeures ou conséquentes, elles feront l\'objet d\'un accord exprès et d\'un nouveau devis.</li>
            <li style="margin-top:5px"><b>SEO</b> : Le Client valide la liste des mots-clés en Annexe 1 avant le démarrage.</li>
            <li style="margin-top:5px"><b>Réseaux Sociaux</b> : Le Prestataire valide avec le Client le tableau de partage avant diffusion. Une fois validé, ce dernier n\'est plus modifiable unilatéralement, sauf accord exprès. Toute modification majeure après validation fera l\'objet d\'un nouveau devis.</li>
        </ul>
        <h3 style="margin-top:15px"><u>4.3. Exclusivité Technique (SEO)</u></h3>
        <p style="margin-top:15px">Le Client s\'engage à ne pas modifier la structure technique du site sans consultation préalable du Prestataire, afin de ne pas compromettre les travaux de référencement.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 5 - CONDITIONS FINANCIÈRES</u></h2>
        <p style="margin-top:15px">5.1. <b>Honoraires</b> : Le prix est fixé selon le devis accepté par le Client.</p>
        <p style="margin-top:15px">5.2. <b>Modalités</b> : Le règlement peut être effectué, selon l\'accord convenu dans le devis, soit de manière mensuelle, soit par étapes, soit pour la totalité de la période. Les modalités spécifiques (échéancier, acompte) font partie intégrante du présent contrat.</p>
        <p style="margin-top:15px">5.3. <b>Retard de Paiement</b> : Tout retard entraîne l\'application de pénalités de retard au taux légal en vigueur après mise en demeure.</p>
        <p style="margin-top:15px">5.4. <b>Frais Annexes</b> : Les frais d\'hébergement, de noms de domaine et les budgets publicitaires (Ads) sont à la charge exclusive du Client.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 6 - DURÉE ET RÉSILIATION</u></h2>
        <p style="margin-top:15px">6.1. <b>Durée</b> : Pour le développement web, le contrat prend fin à la livraison finale et après la période de garantie technique. Pour le SEO et les Réseaux Sociaux, le contrat est conclu pour une durée initiale de <b>' . $contract->getDuration() . '</b>.</p>
        <p style="margin-top:15px">6.2. <b>Reconduction (SEO/Social)</b> : Tacite reconduction, sauf dénonciation avec un préavis de [30] jours par lettre recommandée avec AR.</p>
        <p style="margin-top:15px">6.3. <b>Résiliation pour le Développement Web</b> : Le CLIENT peut résilier le contrat moyennant un préavis écrit de sept (7) jours. Dans ce cas, le PRESTATAIRE sera indemnisé pour tout le travail effectué jusqu\'à la date de résiliation. Le PRESTATAIRE peut résilier le contrat si le CLIENT ne remplit pas ses obligations ou si le paiement accuse un retard de plus de sept (7) jours.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 7 - CONFIDENTIALITÉ ET PROPRIÉTÉ</u></h2>
        <p style="margin-top:15px">Les parties s\'engagent à ne divulguer aucune information confidentielle concernant l\'activité de l\'autre partie durant toute la durée du projet.</p>
        <p style="margin-top:15px">Le site internet et son code source, ainsi que les contenus créés pour les réseaux sociaux, deviennent la propriété du Client uniquement après le paiement intégral des honoraires.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 8 - PROMOTION ET RÉFÉRENCE</u></h2>
        <p style="margin-top:15px">Sous réserve d\'une réponse favorable du CLIENT à l\'e-mail qui lui sera adressé par le PRESTATAIRE au terme de la prestation, le CLIENT s\'engage à faire figurer le logo de Hello World ainsi que la mention discrète « élément réalisé par Hello World », éventuellement accompagnée d\'un lien hypertexte pointant vers le site www.helloworld-agency.com. Le Prestataire se réserve le droit d\'utiliser les réalisations à titre de référence.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 9 - RESPONSABILITÉ</u></h2>
        <p style="margin-top:15px">La responsabilité du Prestataire est limitée aux fonctionnalités et réseaux listés dans le devis. Pour la prestation SEO, la responsabilité du Prestataire est limitée au montant des honoraires versés sur les 6 derniers mois.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 10 - DROIT APPLICABLE ET LITIGES</u></h2>
        <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera soumis à la compétence exclusive du ' . $contract->getTribunal() . '.</p>
        <h2 style="margin-top:30px"><u>ANNEXE 1 : LISTE DES MOTS-CLÉS CIBLÉS (SEO)</u></h2>
        <div>' . $contract->getTexte() . '</div>'
        . self::blocSignature($contract, $agence, $client);

        if ($contract->getShowSignature()) {
            $html .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px"><img src="../../../images/agences/' . $agence->getSignature() . '" width="250"></div>';
        }
        return $html;
    }

    // ---- Nouveau modèle (2026-08-13) : Développement Web seul --------------------------------
    private static function templateDevSeul(contract $contract, agence $agence, client $client, $parCategorie)
    {
        $html = '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE : DÉVELOPPEMENT DE SITE INTERNET</h1>
        <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>'
        . self::blocIdentification($agence, $client)
        . '<h2 style="margin-top:30px"><u>ARTICLE 1 - OBJET DU CONTRAT</u></h2>
        <p>Le présent contrat définit les conditions dans lesquelles le Prestataire assure pour le compte du Client la conception et le développement d\'un site internet (Vitrine, E-commerce ou Sur-mesure). Le périmètre technique et fonctionnel est exclusivement celui déterminé et listé dans le devis accepté par le Client.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 2 - DÉTAIL DES PRESTATIONS</u></h2>
        <p>Le Prestataire s\'engage à réaliser les missions suivantes selon le périmètre du devis :</p>'
        . self::listeItemsHtml($parCategorie[self::CATEGORIE_DEV])
        . '<h2 style="margin-top:30px"><u>ARTICLE 3 - ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
        <p style="margin-top:15px">3.1. <b>Respect des standards et méthodes de développement</b> : Le Prestataire s\'engage à appliquer les meilleures pratiques de l\'industrie (Clean Code), incluant le respect des standards W3C pour le HTML/CSS, l\'optimisation des performances (vitesse de chargement) et la mise en œuvre de protocoles de sécurité rigoureux. Le code sera structuré pour garantir sa pérennité et une compatibilité optimale avec les navigateurs modernes et les différents terminaux (Responsive Design).</p>
        <p style="margin-top:15px">3.2. <b>Ponctualité et respect du planning</b> : Le Prestataire s\'engage à respecter le calendrier de livraison par étapes déterminé dans l\'annexe du présent contrat. Toutefois, ce respect des délais est strictement conditionné par la coopération et la réactivité du Client. En conséquence, le Prestataire ne pourra être tenu pour responsable de tout décalage ou retard dans le planning si le Client prend un temps anormal à valider les étapes intermédiaires ou à communiquer les éléments nécessaires.</p>
        <p style="margin-top:15px">3.3. <b>Satisfaction Client &amp; Qualité du Livrable</b> : Le Prestataire s\'engage formellement à assurer la satisfaction du Client concernant le livrable final du site internet.</p>
        <p style="margin-top:15px">3.4. <b>Limitation de responsabilité</b> : Le Prestataire est responsable uniquement des fonctionnalités listées dans le devis.</p>
        <p style="margin-top:15px">3.5. <b>Exonération pour retard du Client</b> : Le Prestataire ne pourra être tenu pour responsable en cas de retard dans l\'exécution des travaux dû au non-respect des engagements de coopération du Client. Tout retard de livraison résultant d\'un manquement du Client ne pourra lui être imputé.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 4 - ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
        <p style="margin-top:15px">4.1. <b>Coopération et Facilitation</b> : Le CLIENT s\'engage, dans la mesure du possible, à faciliter du mieux qu\'il peut le bon déroulement des missions du PRESTATAIRE.</p>
        <p style="margin-top:15px">4.2. <b>Délai de délivrance des éléments</b> : Le CLIENT s\'engage à coopérer et, sous un délai de sept (7) jours après la date de l\'acceptation de l\'offre, à délivrer au PRESTATAIRE tous les éléments nécessaires à la réalisation des travaux.</p>
        <p style="margin-top:15px">4.3. <b>Validation des Étapes et Modifications</b> : Une fois une étape validée (Maquettes, Structure, Beta), toute demande de modification ultérieure sera évaluée par le Prestataire. Si les modifications demandées sont mineures et peu nombreuses, le Prestataire s\'engage à les offrir gracieusement. En revanche, si les modifications sont jugées majeures ou conséquentes en volume, elles feront l\'objet d\'un accord exprès et de l\'établissement d\'un nouveau devis avant réalisation.</p>
        <p style="margin-top:15px">4.4. <b>Accès</b> : Le Client fournit les accès nécessaires (hébergement, noms de domaine).</p>
        <h2 style="margin-top:30px"><u>ARTICLE 5 - CONDITIONS FINANCIÈRES</u></h2>
        <p style="margin-top:15px">5.1. <b>Honoraires</b> : Le prix est fixé selon le devis accepté.</p>
        <p style="margin-top:15px">5.2. <b>Modalités</b> : Règlement par étapes, mensuel ou total selon le devis.</p>
        <p style="margin-top:15px">5.3. <b>Propriété</b> : Le site et son code source deviennent la propriété du Client uniquement après le paiement intégral.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 6 - DURÉE ET RÉSILIATION</u></h2>
        <p style="margin-top:15px">6.1. <b>Durée</b> : Le contrat prend fin à la livraison finale et après la période de garantie technique.</p>
        <p style="margin-top:15px">6.2. <b>Résiliation par le Prestataire</b> : Le PRESTATAIRE peut résilier le présent contrat si le CLIENT ne remplit pas ses obligations mentionnées ou si le paiement accuse un retard de plus de sept (7) jours.</p>
        <p style="margin-top:15px">6.3. <b>Résiliation par le Client</b> : Le CLIENT peut résilier le présent contrat moyennant un préavis écrit de sept (7) jours. Dans ce cas, le PRESTATAIRE sera indemnisé pour tout le travail effectué jusqu\'à la date de résiliation.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 7 - CONFIDENTIALITÉ</u></h2>
        <p style="margin-top:15px">Les parties s\'engagent à ne divulguer aucune information confidentielle concernant l\'activité de l\'autre partie durant toute la durée du projet.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 8 - PROMOTION ET RÉFÉRENCE</u></h2>
        <p style="margin-top:15px">Sous réserve d\'une réponse favorable du CLIENT à l\'e-mail qui lui sera adressé par le PRESTATAIRE au terme de la prestation, le CLIENT s\'engage à faire figurer le logo de Hello World ainsi que la mention discrète « élément réalisé par Hello World », éventuellement accompagnée d\'un lien hypertexte pointant vers le site www.helloworld-agency.com.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 9 - DROIT APPLICABLE ET LITIGES</u></h2>
        <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant le ' . $contract->getTribunal() . '.</p>'
        . self::blocSignature($contract, $agence, $client);

        if ($contract->getShowSignature()) {
            $html .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px"><img src="../../../images/agences/' . $agence->getSignature() . '" width="250"></div>';
        }
        return $html;
    }

    // ---- Nouveau modèle (2026-08-13) : Réseaux Sociaux seul -----------------------------------
    private static function templateSocialSeul(contract $contract, agence $agence, client $client, $parCategorie)
    {
        $html = '<h1 style="text-align:center">CONTRAT DE GESTION RESEAUX SOCIAUX</h1>
        <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>
        <p style="margin-top:20px"><b>La société ' . $agence->getRaisonSocial() . '</b>, dont le siège social est sis à ' . $agence->getAdresse() . ', RC : ' . $agence->getRc() . ' - ICE : ' . $agence->getIce() . ', représentée par ' . $agence->getManager() . ', ci-après dénommée <b>« le Prestataire »</b>,</p>
        <p style="margin-top:20px"><b>ET</b></p>
        <p style="margin-top:20px">Le Client : <b>' . self::nomClient($client) . '</b>, dont le siège social est sis à ' . $client->getAdresse() . ', RC : ' . $client->getRc() . ' - ICE : ' . $client->getICE() . ', représentée par ' . self::representantClient($client) . ', ci-après dénommée <b>« le Client »</b>. Il a été convenu ce qui suit :</p>
        <h2 style="margin-top:30px"><u>ARTICLE 1 - OBJET</u></h2>
        <p>Le présent contrat a pour objet de définir les conditions dans lesquelles le Prestataire assure pour le compte du Client des prestations de gestion et d\'animation des réseaux sociaux, dans le but d\'améliorer sa visibilité, son image de marque et son engagement auprès de sa communauté en ligne.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 2 - NATURE DES PRESTATIONS</u></h2>
        <p>Les prestations comprennent notamment, sans que cette liste soit limitative :</p>'
        . self::listeItemsHtml($parCategorie[self::CATEGORIE_SOCIAL])
        . '<p>Toute prestation non expressément mentionnée fera l\'objet d\'un accord complémentaire écrit.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 3 - OBLIGATIONS DU CLIENT</u></h2>
        <p>Le Client s\'engage à :</p>
        <ul style="padding-left:30px">
            <li style="margin-top:5px">Fournir au Prestataire tous les éléments nécessaires à la bonne exécution des prestations (logos, accès aux comptes, informations, visuels, etc.)</li>
            <li style="margin-top:5px">Valider les contenus dans les délais convenus</li>
            <li style="margin-top:5px">Régler les sommes dues conformément aux modalités définies au présent contrat</li>
            <li style="margin-top:5px">Désigner un interlocuteur unique pour le suivi du projet</li>
        </ul>
        <p>Tout retard ou manquement du Client pourra entraîner un retard dans l\'exécution des prestations.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 4 - OBLIGATIONS DU PRESTATAIRE</u></h2>
        <p>Le Prestataire s\'engage à :</p>
        <ul style="padding-left:30px">
            <li style="margin-top:5px">Exécuter les prestations avec professionnalisme et diligence</li>
            <li style="margin-top:5px">Respecter l\'image, la charte graphique et les valeurs du Client</li>
            <li style="margin-top:5px">Informer régulièrement le Client de l\'avancement des actions menées</li>
            <li style="margin-top:5px">Respecter la confidentialité des informations communiquées</li>
        </ul>
        <p>Le Prestataire est tenu à une obligation de moyens, et non de résultats.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 5 - DURÉE</u></h2>
        <p>Le présent contrat est conclu pour une durée de <b>' . $contract->getDuration() . '</b> à compter du ' . normalDate(date('Y-m-d', strtotime($contract->getDate()))) . '. Il est renouvelable par tacite reconduction sauf résiliation écrite.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 6 - CONDITIONS FINANCIÈRES</u></h2>
        <p>Dans le cadre de la présente prestation de service, le CLIENT s\'engage à régler au PRESTATAIRE la somme totale indiquée au devis, correspondant à la totalité de la durée du CONTRAT, selon les modalités suivantes : 100 % à la commande.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 7 - RÉSILIATION</u></h2>
        <p>7.1 Le CLIENT peut résilier le CONTRAT durant les quinze (15) premiers jours, sans indemnité autre que les Prestations déjà exécutées, en cas de manquement du PRESTATAIRE, notamment : retard significatif dans les délais (tolérance 30 %), atteinte à l\'image ou aux intérêts du CLIENT, absence de communication des performances ou défaut de réponse aux sollicitations.</p>
        <p>7.2 En cas de manquement contractuel par l\'une des Parties, le CONTRAT pourra être résilié par la Partie non défaillante après mise en demeure restée sans effet pendant trente (30) jours, sans préjudice des dommages et intérêts.</p>
        <p>7.3 Le CLIENT peut résilier de plein droit, sans préavis ni indemnité, en cas de manquement substantiel du PRESTATAIRE, notamment cessation d\'activité, suspension injustifiée des Prestations, non-conformité ou violation de l\'obligation de confidentialité.</p>
        <p>7.4 Le PRESTATAIRE peut résilier le CONTRAT avec un préavis de trente (30) jours, notamment en cas de non-paiement ou d\'absence de réponse du CLIENT supérieure à sept (07) jours.</p>
        <p>7.5 En cas de résiliation ou d\'expiration du CONTRAT, l\'ensemble des documents et données appartenant au CLIENT devra être restitué sans délai par le PRESTATAIRE.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 8 - CONFIDENTIALITÉ</u></h2>
        <p>Chaque Partie s\'engage à respecter une stricte obligation de confidentialité concernant l\'ensemble des informations, documents, données, études, savoir-faire, bases de données, mots de passe et éléments confidentiels portés à sa connaissance dans le cadre de l\'exécution du CONTRAT.</p>
        <p>Cette obligation ne s\'applique pas aux informations qui étaient légalement connues avant leur divulgation, obtenues licitement auprès d\'un tiers non soumis à une obligation de confidentialité, rendues publiques sans restriction ou divulguées avec l\'accord écrit préalable de la Partie concernée. Les obligations de confidentialité resteront en vigueur pendant la durée du CONTRAT et pendant une période d\'un (1) an après son expiration.</p>
        <p>À la fin du CONTRAT, pour quelque cause que ce soit, le PRESTATAIRE s\'engage à restituer au CLIENT l\'ensemble des documents et supports contenant des informations confidentielles, à détruire toute copie électronique et à ne conserver aucune donnée, cette restitution faisant l\'objet d\'un procès-verbal contradictoire.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 9 - MODIFICATION OU ANNULATION DE LA COMMANDE</u></h2>
        <p>Les prestations sont strictement celles décrites dans le devis accepté par le Client. Toute modification ou annulation ne peut intervenir qu\'avec l\'accord écrit des deux Parties et fera, le cas échéant, l\'objet d\'un nouveau devis. Toute modification validée pourra entraîner des ajustements de délais et de conditions.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 10 - RESPONSABILITÉ</u></h2>
        <p>Le Prestataire ne peut être tenu responsable :</p>
        <ul style="padding-left:30px">
            <li style="margin-top:5px">Des changements d\'algorithmes des moteurs de recherche</li>
            <li style="margin-top:5px">D\'une baisse de position indépendante de ses actions</li>
            <li style="margin-top:5px">Du contenu fourni par le Client</li>
        </ul>
        <h2 style="margin-top:30px"><u>ARTICLE 11 - DROIT APPLICABLE ET JURIDICTION</u></h2>
        <p>Le présent contrat est soumis au droit marocain. Tout litige relève de la compétence exclusive du ' . $contract->getTribunal() . '.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 12 - DATE DE DÉBUT DE CONTRAT</u></h2>
        <p>La société ' . $agence->getRaisonSocial() . ' mettra à disposition de la société ' . self::nomClient($client) . ' l\'ensemble de ses équipes (technique, artistique, marketing...etc.) ainsi que les moyens nécessaires pour le bon déroulement du partenariat à partir du ' . normalDate(date('Y-m-d', strtotime($contract->getDate()))) . ' (date prévisionnelle de début de contrat).</p>'
        . self::blocSignature($contract, $agence, $client);

        if ($contract->getShowSignature()) {
            $html .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px"><img src="../../../images/agences/' . $agence->getSignature() . '" width="250"></div>';
        }
        return $html;
    }

    // ---- Anciens modèles (non remplacés faute de nouvel exemple validé) : liste de prestations
    // rendue dynamique, reste du texte conservé à l'identique. -----------------------------------
    private static function templateDevSeo(contract $contract, agence $agence, client $client, $parCategorie)
    {
        return self::ancienModeleGenerique($contract, $agence, $client, $parCategorie, 'dev_seo');
    }

    private static function templateDevSocial(contract $contract, agence $agence, client $client, $parCategorie)
    {
        return self::ancienModeleGenerique($contract, $agence, $client, $parCategorie, 'dev_social');
    }

    private static function templateSeoSeul(contract $contract, agence $agence, client $client, $parCategorie)
    {
        return self::ancienModeleGenerique($contract, $agence, $client, $parCategorie, 'seo_seul');
    }

    // Les 3 combinaisons ci-dessus n'ont pas reçu de nouveau modèle - le texte legacy est repris
    // à l'identique (voir git history de pdfContract() avant refactor), seule la liste de
    // prestations de l'Article 2 est désormais alimentée par le devis réel.
    private static function ancienModeleGenerique(contract $contract, agence $agence, client $client, $parCategorie, $variante)
    {
        $titres = array(
            'dev_seo' => 'CONTRAT DE PRESTATION DE SERVICE COMBINÉ (Développement Web + SEO)',
            'dev_social' => 'CONTRAT DE PRESTATION DE SERVICE COMBINÉ (Développement Web + Réseaux Sociaux)',
            'seo_seul' => 'CONTRAT DE PRESTATION DE SERVICE (Référencement SEO)',
        );
        $prestations = '';
        if ($variante === 'dev_seo' || $variante === 'dev_social') {
            $prestations .= '<h3 style="margin-top:15px"><u>Développement de Site Internet</u></h3>' . self::listeItemsHtml($parCategorie[self::CATEGORIE_DEV]);
        }
        if ($variante === 'dev_seo' && !empty($parCategorie[self::CATEGORIE_SEO])) {
            $prestations .= '<h3 style="margin-top:15px"><u>Référencement Naturel (SEO)</u></h3>' . self::listeItemsHtml($parCategorie[self::CATEGORIE_SEO]);
        }
        if ($variante === 'dev_social' && !empty($parCategorie[self::CATEGORIE_SOCIAL])) {
            $prestations .= '<h3 style="margin-top:15px"><u>Gestion des Réseaux Sociaux</u></h3>' . self::listeItemsHtml($parCategorie[self::CATEGORIE_SOCIAL]);
        }
        if ($variante === 'seo_seul') {
            $prestations .= self::listeItemsHtml($parCategorie[self::CATEGORIE_SEO]);
        }

        $html = '<h1 style="text-align:center">' . $titres[$variante] . '</h1>
        <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>'
        . self::blocIdentification($agence, $client)
        . '<h2 style="margin-top:30px"><u>ARTICLE 1 - OBJET DU CONTRAT</u></h2>
        <p>Le présent contrat définit les conditions techniques, juridiques et financières dans lesquelles le Prestataire assure pour le compte du Client la réalisation des prestations déterminées et listées dans le devis accepté par le Client.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 2 - DÉTAIL DES PRESTATIONS</u></h2>'
        . $prestations
        . '<h2 style="margin-top:30px"><u>ARTICLE 3 - ENGAGEMENTS ET PROTECTION DU PRESTATAIRE</u></h2>
        <p style="margin-top:15px">Le Prestataire s\'engage à appliquer les meilleures pratiques de son domaine, dans le respect des standards professionnels en vigueur, et n\'est tenu qu\'à une obligation de moyens pour toute prestation dépendant d\'algorithmes ou de plateformes tierces.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 4 - ENGAGEMENTS ET PROTECTION DU CLIENT</u></h2>
        <p style="margin-top:15px">Le Client s\'engage à coopérer avec le Prestataire et à lui fournir, dans un délai de sept (7) jours après acceptation de l\'offre, l\'ensemble des éléments nécessaires à la réalisation des travaux.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 5 - CONDITIONS FINANCIÈRES</u></h2>
        <p style="margin-top:15px">Le prix est fixé selon le devis accepté par le Client, réglé selon les modalités qui y sont définies.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 6 - DURÉE ET RÉSILIATION</u></h2>
        <p style="margin-top:15px">Le contrat est conclu pour une durée de <b>' . $contract->getDuration() . '</b>.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 7 - CONFIDENTIALITÉ</u></h2>
        <p style="margin-top:15px">Les parties s\'engagent à la confidentialité totale durant toute la durée du projet.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 8 - DROIT APPLICABLE ET LITIGES</u></h2>
        <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant le ' . $contract->getTribunal() . '.</p>
        <h2 style="margin-top:30px"><u>ANNEXE 1</u></h2>
        <div>' . $contract->getTexte() . '</div>'
        . self::blocSignature($contract, $agence, $client);

        if ($contract->getShowSignature()) {
            $html .= '<div class="div-signature" style="padding-top: 0;padding-bottom: 50px;text-align:left;margin-left:50px"><img src="../../../images/agences/' . $agence->getSignature() . '" width="250"></div>';
        }
        return $html;
    }

    // Aucune catégorie Dev/SEO/Social détectée (ex: devis Design ou Shooting uniquement) -
    // jamais de PDF vide comme avec l'ancienne version : un contrat générique liste les
    // prestations réelles du devis, tous groupes confondus.
    private static function templateGenerique(contract $contract, agence $agence, client $client, devis $devis)
    {
        $items = $devis->getItems();
        $html = '<h1 style="text-align:center">CONTRAT DE PRESTATION DE SERVICE</h1>
        <h2 style="margin-top:70px"><u>ENTRE LES SOUSSIGNÉS</u></h2>'
        . self::blocIdentification($agence, $client)
        . '<h2 style="margin-top:30px"><u>ARTICLE 1 - OBJET DU CONTRAT</u></h2>
        <p>Le présent contrat définit les conditions dans lesquelles le Prestataire assure pour le compte du Client la réalisation des prestations exclusivement déterminées et listées dans le devis accepté par le Client.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 2 - DÉTAIL DES PRESTATIONS</u></h2>'
        . self::listeItemsHtml($items)
        . '<h2 style="margin-top:30px"><u>ARTICLE 3 - CONDITIONS FINANCIÈRES</u></h2>
        <p style="margin-top:15px">Le prix est fixé selon le devis accepté par le Client, réglé selon les modalités qui y sont définies.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 4 - DURÉE ET RÉSILIATION</u></h2>
        <p style="margin-top:15px">Le contrat est conclu pour une durée de <b>' . $contract->getDuration() . '</b>.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 5 - CONFIDENTIALITÉ</u></h2>
        <p style="margin-top:15px">Les parties s\'engagent à la confidentialité totale durant toute la durée du projet.</p>
        <h2 style="margin-top:30px"><u>ARTICLE 6 - DROIT APPLICABLE ET LITIGES</u></h2>
        <p style="margin-top:15px">Le présent contrat est soumis au droit marocain. Tout litige sera porté devant le ' . $contract->getTribunal() . '.</p>'
        . self::blocSignature($contract, $agence, $client);
        return $html;
    }
}
