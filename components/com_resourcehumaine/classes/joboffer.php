<?php

// Offre d'emploi / promesse d'embauche d'un candidat (com_resourcehumaine, onglet "Offre d'emploi") :
// brouillon rédigé par l'IA puis retouché en WYSIWYG -> envoyé sur Slack #administration pour
// validation direction -> relance auto tant que non validé -> (SignWell dès que la clé API sera
// configurée) -> document signé attaché à l'espace employé. Cycle de vie porté par `statut`.
class joboffer
{
    static $table = __prefixe_db__ . "joboffer";

    const STATUT_BROUILLON = 'brouillon';
    const STATUT_EN_ATTENTE_SLACK = 'en_attente_validation';
    const STATUT_VALIDEE = 'validee';
    const STATUT_REFUSEE = 'refusee';
    const STATUT_ENVOYEE_SIGNATURE = 'envoyee_signature';
    const STATUT_SIGNEE = 'signee';

    // Gérant à faire figurer sur la lettre, par agence - directement agence::getManager(), déjà
    // correct en base pour les 3 agences (Verse Concept : Zakaria El Haboussi seul ; HW Label et
    // Dubai : Hamid Kennou). Pas de cas particulier ici : un seul gérant par agence.
    private static function gerantsPourAgence($agence)
    {
        return $agence && $agence->getManager() ? $agence->getManager() : '';
    }

    // Assemble la lettre complète : en-tête société + mentions légales + paragraphes fixes
    // (modèle juridique, ne varie jamais) autour des deux seules sections rédigées par l'IA
    // (objectif/brief, spécifiques au poste) et des valeurs saisies dans le formulaire (période
    // d'essai, salaire, conditions de paiement, commissions). Résultat inséré tel quel dans le
    // WYSIWYG - l'utilisateur peut toujours le retoucher avant envoi.
    public static function construireLettreHTML($resourcehumaine, $periodeEssai, $salaire, $conditionsPaiement, $commissions, $objectifPoste, $briefPosteItems, $langue = 'fr')
    {
        $agence = $resourcehumaine->getAgency();
        $nomAgence = $agence ? trim($agence->getNom()) : '';
        $ville = $agence ? trim($agence->getVille()) : '';
        $nomComplet = trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName());
        $poste = $resourcehumaine->getFunction();
        $dateEntree = $resourcehumaine->getStartDate() ? date('d/m/Y', strtotime($resourcehumaine->getStartDate())) : '';
        $gerants = self::gerantsPourAgence($agence);
        $gerantPluriel = strpos($gerants, ' et ') !== false;
        $dateOffre = date('d/m/Y');
        $dateLimite = date('d/m/Y', strtotime('+4 days'));

        $briefHtml = '';
        foreach ($briefPosteItems as $item) {
            $briefHtml .= '<li>' . htmlspecialchars($item) . '</li>';
        }

        // Pas d'en-tête/pied de page société ici : sur le vrai document (impression/téléchargement,
        // voir telechargerOffrePDF()) ils sont générés par mPDF comme en-tête/pied RÉPÉTÉ SUR CHAQUE
        // PAGE (mpdfHeaderFooterBlock() ci-dessous), exactement comme le modèle de référence fourni
        // par l'utilisateur - les inclure ici en HTML statique les aurait rendus non répétés et
        // mélangés avec le corps modifiable dans le WYSIWYG.
        //
        // Modèle dupliqué (FR/EN) plutôt que templaté avec des labels traduits séparément : pour un
        // document destiné à être signé, chaque version doit rester lisible et auditable telle
        // quelle d'un bloc, sans risque qu'un fragment mal raccordé change le sens d'une clause.
        if ($langue === 'en') {
            $remuneration = '<p>Your probationary and training period will last <strong>' . htmlspecialchars($periodeEssai) . '</strong>.</p>';
            if ($salaire !== '' && $salaire !== null) {
                $remuneration .= '<p>During this period, your compensation will be <strong>' . number_format((float) $salaire, 2, '.', ',') . ' MAD</strong>'
                    . ($conditionsPaiement !== '' ? ', ' . htmlspecialchars($conditionsPaiement) : '') . '.</p>';
            }
            if ($commissions !== '' && $commissions !== null) {
                $remuneration .= '<p>You will also benefit from the following commissions: <strong>' . htmlspecialchars($commissions) . '</strong>.</p>';
            }
            $remuneration .= '<p>Once the probationary and training period has been successfully completed, your compensation will be reviewed based on your performance.</p>';

            $html = '<div style="font-family: Calibri, Arial, sans-serif; font-size: 11pt; line-height: 1.5;">';
            $html .= '<h2 style="text-align:center;">EMPLOYMENT OFFER LETTER</h2>';
            $html .= '<p><strong>PERSONAL AND CONFIDENTIAL</strong><br><strong>' . htmlspecialchars($nomComplet) . '</strong></p>';
            $html .= '<p style="text-align:right;"><strong>' . htmlspecialchars($ville !== '' ? $ville . ', ' : '') . $dateOffre . '</strong></p>';
            $html .= '<p>Dear ' . htmlspecialchars($nomComplet) . ',</p>';
            $html .= '<p>We are pleased to offer you employment at <strong>' . htmlspecialchars($nomAgence) . '</strong> as <strong>' . htmlspecialchars($poste) . '</strong>'
                . ($ville !== '' ? ' in ' . htmlspecialchars($ville) : '') . '. You will begin your position'
                . ($dateEntree !== '' ? ' on <strong>' . $dateEntree . '</strong>' : '') . ', or on another date to be mutually agreed upon'
                . ($gerants !== '' ? ', and you will report to <strong>' . htmlspecialchars($gerants) . '</strong>' : '') . '. '
                . 'As part of our hiring practices, we conduct employment reference and educational background checks. '
                . 'It is understood that this offer remains conditional upon obtaining positive results from these checks and upon your acceptance regarding bonding, licensing and/or insurance requirements.</p>';

            $html .= '<h3><u>Salary and Other Compensation</u></h3>' . $remuneration;
            $html .= '<h3><u>Position Objective:</u></h3><p>' . nl2br(htmlspecialchars($objectifPoste)) . '</p>';
            $html .= '<h3><u>Position Brief:</u></h3><ul>' . $briefHtml . '</ul>';

            $html .= '<h3><u>Working Hours</u></h3>'
                . '<p>Your working hours will be Monday to Friday: 9:00 AM to 1:00 PM // 2:00 PM to 6:00 PM, equivalent to 40 hours per week (+ 4 hours as needed).<br>'
                . 'N.B.: our company generally grants two days off per week, Saturday and Sunday. However, you may be required to work certain Saturdays '
                . '(half-day) based on the company\'s exceptional needs, as stipulated by the Labor Code.</p>';

            $html .= '<h3><u>Company Policies</u></h3>'
                . '<p>You will be required to comply with the company\'s current policies relevant to your position, as well as any additional policies that may be introduced during your employment. '
                . 'These policies will be communicated to you during your probationary period.</p>';

            $html .= '<h3><u>Confidentiality and Intellectual Property</u></h3>'
                . '<p>The employer undertakes to take all necessary measures to preserve the confidentiality of the information covered by this agreement, in particular with regard to its employees, '
                . 'representatives, agents, successors, heirs, or assigns.</p>';

            $html .= '<p>We hope you will find your role at <strong>' . htmlspecialchars($nomAgence) . '</strong> to be a stimulating and rewarding challenge, and we wish you great success in your new position.</p>';
            $html .= '<p>If you accept the terms above, please sign, date, and return this letter by email to the Human Resources department by <strong>' . $dateLimite . '</strong>.</p>';
            $html .= '<p>Please do not hesitate to contact us if you have any questions or need assistance before starting your position.</p>';
            $html .= '<p>Sincerely,</p>';
            $html .= '<p><strong>' . htmlspecialchars($nomAgence) . '</strong><br>Represented by its ' . ($gerantPluriel ? 'Managers' : 'Manager') . '<br>' . htmlspecialchars($gerants) . '</p>';
            $html .= '<p style="margin-top:40px;">&nbsp;</p><p>' . htmlspecialchars($nomComplet) . '</p>';
            $html .= '</div>';

            return $html;
        }

        $remuneration = '<p>Votre période d\'essai et de formation aura une durée de <strong>' . htmlspecialchars($periodeEssai) . '</strong>.</p>';
        if ($salaire !== '' && $salaire !== null) {
            $remuneration .= '<p>Durant cette période, votre rémunération sera de <strong>' . number_format((float) $salaire, 2, ',', ' ') . ' DH</strong>'
                . ($conditionsPaiement !== '' ? ', ' . htmlspecialchars($conditionsPaiement) : '') . '.</p>';
        }
        if ($commissions !== '' && $commissions !== null) {
            $remuneration .= '<p>Vous bénéficierez également des commissions suivantes : <strong>' . htmlspecialchars($commissions) . '</strong>.</p>';
        }
        $remuneration .= '<p>Une fois la période d\'essai et de formation validée, votre situation salariale sera réévaluée en fonction de votre rendement.</p>';

        $html = '<div style="font-family: Calibri, Arial, sans-serif; font-size: 11pt; line-height: 1.5;">';
        $html .= '<h2 style="text-align:center;">LETTRE D\'OFFRE D\'EMPLOI</h2>';
        $html .= '<p><strong>PERSONNEL ET CONFIDENTIEL</strong><br><strong>' . htmlspecialchars($nomComplet) . '</strong></p>';
        $html .= '<p style="text-align:right;"><strong>' . htmlspecialchars($ville !== '' ? $ville . ' le ' : '') . $dateOffre . '</strong></p>';
        $html .= '<p>Bonjour,</p>';
        $html .= '<p>Nous sommes heureux de vous offrir un emploi chez <strong>' . htmlspecialchars($nomAgence) . '</strong> à titre de <strong>' . htmlspecialchars($poste) . '</strong>'
            . ($ville !== '' ? ' à ' . htmlspecialchars($ville) : '') . '. Vous entrerez en fonction'
            . ($dateEntree !== '' ? ' le <strong>' . $dateEntree . '</strong>' : '') . ', ou à une autre date déterminée d\'un commun accord'
            . ($gerants !== '' ? ', et vous relèverez de <strong>' . htmlspecialchars($gerants) . '</strong>' : '') . '. '
            . 'Dans le cadre de nos pratiques d\'embauche, nous effectuons des vérifications des références d\'emploi et du dossier de scolarité. '
            . 'Il est entendu que cette offre demeure conditionnelle à l\'obtention de résultats positifs lors de ces vérifications et à votre acceptation en matière de cautionnement, de permis et/ou d\'assurance.</p>';

        $html .= '<h3><u>Salaire et autre rémunération</u></h3>' . $remuneration;
        $html .= '<h3><u>Objectif du Poste :</u></h3><p>' . nl2br(htmlspecialchars($objectifPoste)) . '</p>';
        $html .= '<h3><u>Brief du Poste :</u></h3><ul>' . $briefHtml . '</ul>';

        $html .= '<h3><u>Horaire de travail</u></h3>'
            . '<p>Votre horaire de travail sera du lundi au vendredi : 09h00 à 13h00 // 14h00 à 18h00, équivalent à 40 heures par semaine (+ 4 heures en fonction du besoin).<br>'
            . 'N.B : notre société accorde généralement deux jours de repos par semaine, le samedi et le dimanche. Il est toutefois possible que vous soyez amené(e) à travailler certains samedis '
            . '(demi-journée) en fonction des besoins exceptionnels de l\'entreprise, comme le stipule le code du travail.</p>';

        $html .= '<h3><u>Politiques de l\'entreprise</u></h3>'
            . '<p>Vous devrez respecter les politiques en vigueur de l\'entreprise qui sont pertinentes à votre poste ainsi que les autres politiques qui pourraient s\'ajouter durant votre emploi. '
            . 'Ces politiques vous seront communiquées par la suite pendant votre période de test.</p>';

        $html .= '<h3><u>Confidentialité et propriété intellectuelle</u></h3>'
            . '<p>L\'employeur s\'engage à prendre toutes les mesures nécessaires pour préserver la confidentialité des informations visées au présent accord, notamment auprès de ses employés, '
            . 'représentants, mandataires, successeurs, héritiers ou ayants droit.</p>';

        $html .= '<p>Nous espérons que vous trouverez chez <strong>' . htmlspecialchars($nomAgence) . '</strong> un défi stimulant et passionnant et nous vous souhaitons beaucoup de succès dans vos nouvelles fonctions.</p>';
        $html .= '<p>Si vous acceptez les conditions ci-dessus, veuillez signer, dater et retourner cette lettre par email au service des ressources humaines d\'ici le <strong>' . $dateLimite . '</strong>.</p>';
        $html .= '<p>N\'hésitez pas à communiquer avec nous si vous avez des questions ou si vous avez besoin d\'aide avant votre entrée en fonctions.</p>';
        $html .= '<p>Cordialement,</p>';
        $html .= '<p><strong>' . htmlspecialchars($nomAgence) . '</strong><br>Représenté' . ($gerantPluriel ? 's' : '') . ' par ' . ($gerantPluriel ? 'ses Gérants' : 'son Gérant') . '<br>' . htmlspecialchars($gerants) . '</p>';
        $html .= '<p style="margin-top:40px;">&nbsp;</p><p>' . htmlspecialchars($nomComplet) . '</p>';
        $html .= '</div>';

        return $html;
    }

    // En-tête/pied de page mPDF (logo+adresse en haut, tel/email/site sous le logo, IF/TP/RC/ICE en
    // bas), RÉPÉTÉS SUR CHAQUE PAGE générée - même syntaxe <!--mpdf ... mpdf--> que
    // com_devis/controleurs/devis/controleur.php, seule façon d'obtenir un vrai en-tête/pied de
    // page qui persiste sur plusieurs pages (du HTML statique en début/fin de contenu ne le ferait
    // qu'une fois). Utilisé par telechargerOffrePDF() (aperçu/téléchargement) ET par la génération
    // du PDF signé (jobofferaccept/router.php) pour un rendu identique dans les deux cas.
    public static function mpdfHeaderFooterBlock($agence)
    {
        if (!$agence) {
            return '';
        }
        $nomAgence = htmlspecialchars(trim($agence->getNom()));
        $adresse = htmlspecialchars($agence->getAdresse());
        // Chemin ABSOLU (pas relatif au cwd) : cette méthode est appelée depuis deux contextes à
        // des profondeurs différentes (joboffer/controleur.php via router.php vs
        // jobofferaccept/router.php qui est lui-même le script requêté) - un chemin relatif du
        // style "../../../images/..." serait juste selon l'appelant, jamais les deux (déjà vu ce
        // piège plusieurs fois sur ce projet). __DIR__ ici = components/com_resourcehumaine/classes,
        // 3 niveaux au-dessus = racine du site.
        $racineAbsolue = dirname(__DIR__, 3);
        $bloc = '<!--mpdf
<htmlpageheader name="offreheader">
<table width="100%"><tr>
<td width="20%">' . ($agence->getlogo() ? '<img src="' . $racineAbsolue . '/images/agences/' . htmlspecialchars($agence->getlogo()) . '" width="90">' : '') . '</td>
<td width="80%" style="text-align:center; font-size:8pt; font-family: Calibri, Arial, sans-serif;">
<strong>' . $nomAgence . ($adresse !== '' ? ', ' . $adresse : '') . '</strong><br>
tel: ' . htmlspecialchars($agence->getTel()) . ' | e: ' . htmlspecialchars($agence->getEmail()) . ' | w: ' . htmlspecialchars($agence->getWebsite()) . '
</td>
</tr></table>
</htmlpageheader>
<htmlpagefooter name="offrefooter">
<div style="text-align:center; font-size:8pt; font-family: Calibri, Arial, sans-serif; border-top:1px solid #ccc; padding-top:3mm;">';
        if (trim((string) $agence->getIce()) !== '') {
            $bloc .= 'IF ' . htmlspecialchars($agence->getIf()) . ' | TP ' . htmlspecialchars($agence->getTp()) . ' | RC ' . htmlspecialchars($agence->getRc()) . ' | ICE ' . htmlspecialchars($agence->getIce());
        }
        $bloc .= '</div>
</htmlpagefooter>
<sethtmlpageheader name="offreheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="offrefooter" value="on" />
mpdf-->';
        return $bloc;
    }

    private $id;
    private $resourcehumaine;
    private $periodeEssai;
    private $salaire;
    private $conditionsPaiement;
    private $commissions;
    private $contenu;
    private $statut;
    private $slackTs;
    private $slackLastReminder;
    private $slackValidatedBy;
    private $slackValidatedAt;
    private $signwellDocumentId;
    private $signedFile;
    private $signedAt;
    private $acceptanceToken;
    private $userAdded;
    private $dateAdd;
    private $lastEdit;

    public function __construct()
    {
        $this->id = 0;
        $this->statut = self::STATUT_BROUILLON;
    }

    public function getId() { return $this->id; }
    public function getResourcehumaine() { return $this->resourcehumaine; }
    public function getPeriodeEssai() { return $this->periodeEssai; }
    public function getSalaire() { return $this->salaire; }
    public function getConditionsPaiement() { return $this->conditionsPaiement; }
    public function getCommissions() { return $this->commissions; }
    public function getContenu() { return $this->contenu; }
    public function getStatut() { return $this->statut; }
    public function getSlackTs() { return $this->slackTs; }
    public function getSlackLastReminder() { return $this->slackLastReminder; }
    public function getSlackValidatedBy() { return $this->slackValidatedBy; }
    public function getSlackValidatedAt() { return $this->slackValidatedAt; }
    public function getSignwellDocumentId() { return $this->signwellDocumentId; }
    public function getSignedFile() { return $this->signedFile; }
    public function getSignedAt() { return $this->signedAt; }
    public function getAcceptanceToken() { return $this->acceptanceToken; }
    public function getUserAdded() { return $this->userAdded; }
    public function getDateAdd() { return $this->dateAdd; }
    public function getLastEdit() { return $this->lastEdit; }

    public function setId($id) { $this->id = $id; }
    public function setResourcehumaine($resourcehumaine) { $this->resourcehumaine = $resourcehumaine; }
    public function setPeriodeEssai($v) { $this->periodeEssai = $v; }
    public function setSalaire($v) { $this->salaire = $v; }
    public function setConditionsPaiement($v) { $this->conditionsPaiement = $v; }
    public function setCommissions($v) { $this->commissions = $v; }
    public function setContenu($v) { $this->contenu = $v; }
    public function setStatut($v) { $this->statut = $v; }
    public function setSlackTs($v) { $this->slackTs = $v; }
    public function setSlackLastReminder($v) { $this->slackLastReminder = $v; }
    public function setSlackValidatedBy($v) { $this->slackValidatedBy = $v; }
    public function setSlackValidatedAt($v) { $this->slackValidatedAt = $v; }
    public function setSignwellDocumentId($v) { $this->signwellDocumentId = $v; }
    public function setSignedFile($v) { $this->signedFile = $v; }
    public function setSignedAt($v) { $this->signedAt = $v; }
    public function setAcceptanceToken($v) { $this->acceptanceToken = $v; }
    public function setUserAdded($v) { $this->userAdded = $v; }
    public function setDateAdd($v) { $this->dateAdd = $v; }
    public function setLastEdit($v) { $this->lastEdit = $v; }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_resourcehumaine, periode_essai, salaire, conditions_paiement, commissions, contenu, statut, id_user_added, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->periodeEssai, "text"),
            GetSQLValueString($this->salaire, "double"),
            GetSQLValueString($this->conditionsPaiement, "text"),
            GetSQLValueString($this->commissions, "text"),
            GetSQLValueString($this->contenu, "text"),
            GetSQLValueString($this->statut, "text"),
            GetSQLValueString($this->userAdded ? $this->userAdded->getId() : null, "int"),
            GetSQLValueString(date("Y-m-d H:i:s"), "text"),
            GetSQLValueString(date("Y-m-d H:i:s"), "text")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET id_resourcehumaine=%s, periode_essai=%s, salaire=%s, conditions_paiement=%s, commissions=%s, contenu=%s, statut=%s, slack_ts=%s, slack_last_reminder=%s, slack_validated_by=%s, slack_validated_at=%s, signwell_document_id=%s, signed_file=%s, signed_at=%s, acceptance_token=%s, last_edit=%s WHERE id=%s",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->periodeEssai, "text"),
            GetSQLValueString($this->salaire, "double"),
            GetSQLValueString($this->conditionsPaiement, "text"),
            GetSQLValueString($this->commissions, "text"),
            GetSQLValueString($this->contenu, "text"),
            GetSQLValueString($this->statut, "text"),
            GetSQLValueString($this->slackTs, "text"),
            GetSQLValueString($this->slackLastReminder, "text"),
            GetSQLValueString($this->slackValidatedBy, "text"),
            GetSQLValueString($this->slackValidatedAt, "text"),
            GetSQLValueString($this->signwellDocumentId, "text"),
            GetSQLValueString($this->signedFile, "text"),
            GetSQLValueString($this->signedAt, "text"),
            GetSQLValueString($this->acceptanceToken, "text"),
            GetSQLValueString(date("Y-m-d H:i:s"), "text"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s", GetSQLValueString($this->getId(), "int"));
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function find($id)
    {
        global $db;
        $offer = new joboffer();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s", GetSQLValueString($id, "int"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $offer = static::build($db->fetch_assoc($result));
        }
        return $offer;
    }

    public static function findByAcceptanceToken($token)
    {
        global $db;
        $offer = new joboffer();
        if (!$token) {
            return $offer;
        }
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE acceptance_token = %s", GetSQLValueString($token, "text"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $offer = static::build($db->fetch_assoc($result));
        }
        return $offer;
    }

    public static function findAllByResourcehumaine($id_resourcehumaine)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s ORDER BY id DESC", GetSQLValueString($id_resourcehumaine, "int"));
        foreach ($db->queryS($SQLselect) as $data) {
            $items[] = static::build($data);
        }
        return $items;
    }

    // Offres en attente de validation Slack depuis plus de $heures et pas encore relancées depuis
    // autant de temps - utilisé par le cron de relance (task=cronRelanceOffreEmploi).
    public static function findAllEnAttenteARelancer($heures = 4)
    {
        global $db;
        $items = array();
        $seuil = date("Y-m-d H:i:s", strtotime("-$heures hours"));
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE statut = %s AND slack_ts IS NOT NULL AND (slack_last_reminder IS NULL OR slack_last_reminder <= %s)",
            GetSQLValueString(self::STATUT_EN_ATTENTE_SLACK, "text"),
            GetSQLValueString($seuil, "text")
        );
        foreach ($db->queryS($SQLselect) as $data) {
            $items[] = static::build($data);
        }
        return $items;
    }

    public static function findAllEnAttenteValidation()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE statut = %s", GetSQLValueString(self::STATUT_EN_ATTENTE_SLACK, "text"));
        foreach ($db->queryS($SQLselect) as $data) {
            $items[] = static::build($data);
        }
        return $items;
    }

    public static function build($data)
    {
        $offer = new joboffer();
        $offer->setId($data['id']);
        $offer->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
        $offer->setPeriodeEssai($data['periode_essai']);
        $offer->setSalaire($data['salaire']);
        $offer->setConditionsPaiement($data['conditions_paiement']);
        $offer->setCommissions($data['commissions']);
        $offer->setContenu($data['contenu']);
        $offer->setStatut($data['statut']);
        $offer->setSlackTs($data['slack_ts']);
        $offer->setSlackLastReminder($data['slack_last_reminder']);
        $offer->setSlackValidatedBy($data['slack_validated_by']);
        $offer->setSlackValidatedAt($data['slack_validated_at']);
        $offer->setSignwellDocumentId($data['signwell_document_id']);
        $offer->setSignedFile($data['signed_file']);
        $offer->setSignedAt($data['signed_at']);
        $offer->setAcceptanceToken(isset($data['acceptance_token']) ? $data['acceptance_token'] : null);
        $offer->setUserAdded(isset($data['id_user_added']) && $data['id_user_added'] ? user::find($data['id_user_added']) : new user());
        $offer->setDateAdd($data['date_add']);
        $offer->setLastEdit($data['last_edit']);
        return $offer;
    }
}
