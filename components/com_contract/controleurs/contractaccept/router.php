<?php
// Point d'entrée AUTONOME (pas de session CRM classique) pour la signature en ligne d'un contrat,
// via le lien envoyé par email au client (cf. envoyerContratEmailSignature()/contratLienSignature()
// dans com_contract/controleurs/contract/controleur.php). Signature électronique "maison" (nom
// tapé + case à cocher + horodatage + IP) - même mécanisme que
// com_resourcehumaine/controleurs/jobofferaccept/router.php pour les offres d'emploi. Un seul
// fichier, volontairement : point d'entrée sensible, plus simple à auditer d'un bloc.
require_once("../../../../config.php");
require_once("../../../../instanceDb.php");
require_once("../../../../includes/functions/functions.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Construit le PDF du contrat avec la mention de signature ajoutée - copie volontairement
// autonome de construireMpdfContrat() (com_contract/controleurs/contract/controleur.php) plutôt
// qu'un require de ce fichier : ses chemins relatifs ('../../../...') supposent un appel depuis
// controleurs/ (profondeur 3), alors que ce point d'entrée public vit un niveau plus bas
// (controleurs/contractaccept/, profondeur 4) - les réutiliser tels quels chargerait le mauvais
// vendor/autoload.php (silencieusement introuvable) et le mauvais logo/dossier de polices. Même
// principe d'autonomie que jobofferaccept/router.php, qui ne réutilise jamais joboffer/controleur.php.
function construireMpdfContratDepuisAccept($contract)
{
    require_once("../../../../vendor/autoload.php");
    global $traduction;
    require_once("../../../../includes/traduction.php");

    $devis = $contract->getDevis();
    $client = $devis->getClient();
    $agence = agence::find($client->getAgence()->getId(), $devis->getLangue());
    $corpsContrat = $contract->getCorpsGenere() ? $contract->getCorpsGenere() : contractGenerator::genererCorpsContrat($contract);

    $htmlInvoice = '<html>
    <head>
    <style>
    body { font-family: montserrat; font-size: 10pt; }
    p { margin: 0pt; }
    </style>
    </head>
    <body>
    <!--mpdf
    <htmlpageheader name="myheader">
        <table width="100%">
        <tr>
            <td><img src="../../../../images/agences/' . $agence->getLogo() . '" width="100"></td>
            <td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
            <p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> ' . $agence->getWebsite() . '</p></td>
        </tr>
        </table>
        <hr>
        </htmlpageheader>
        <htmlpagefooter name="myfooter">
        <div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

    if ($agence->getIce() != '') {
        $htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> ' . $agence->getIf() . ' | <strong>TP</strong> ' . $agence->getTp() . ' | <strong>RC</strong> ' . $agence->getRc() . ' | <strong>ICE</strong> ' . $agence->getIce() . '</p>';
    }

    $htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR']['fr'] . ' {nb}</div>
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
        'fontDir' => array_merge($fontDirs, ['../../../../fonts/']),
        'fontdata' => $fontData + [
            'montserrat' => ['R' => 'Montserrat-Regular.ttf', 'B' => 'Montserrat-Bold.ttf']
        ],
        'default_font' => 'montserrat'
    ]);

    $mpdf->SetTitle("Contract #" . $contract->getId());
    $mpdf->SetAuthor("Hello World");
    $mpdf->SetDisplayMode('fullpage');
    $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
    $mpdf->WriteHTML($htmlInvoice);

    return $mpdf;
}

$task = isset($_POST['task']) ? $_POST['task'] : (isset($_GET['task']) ? $_GET['task'] : 'view');
$token = isset($_GET['token']) ? $_GET['token'] : (isset($_POST['token']) ? $_POST['token'] : '');

// contract::find()/build() filtrent par $_SESSION['agence'] et $_SESSION['user'] (session CRM
// normale), qui n'existent pas ici (page publique, sans authentification). On résout donc d'abord
// l'agence du contrat en SQL brut, puis on simule une session admin - même contournement que les
// endpoints cron (cf. bootstrapSystemSession()/RELANCE_CRON_ACTING_USER_ID, déjà utilisés ailleurs
// pour ce même besoin) - pour pouvoir ensuite réutiliser contract::find() normalement.
global $db;
$contract = new contract();
if ($token) {
    $SQLresolve = sprintf(
        "SELECT A.id as id, D.id_agence as id_agence FROM " . __prefixe_db__ . "contract A INNER JOIN " . __prefixe_db__ . "devis C ON A.id_devis = C.id INNER JOIN " . __prefixe_db__ . "client D ON C.id_client = D.id WHERE A.acceptance_token = %s",
        GetSQLValueString($token, "text")
    );
    $resultResolve = $db->query($SQLresolve);
    if ($db->num_rows($resultResolve) == 1) {
        $ligne = $db->fetch_assoc($resultResolve);
        bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, $ligne['id_agence'], 'fr');
        $contract = contract::find($ligne['id'], $ligne['id_agence'], 'fr');
    }
}

$estValide = $contract->getId() != 0 && $token !== '' && hash_equals((string) $contract->getAcceptanceToken(), $token);
$dejaSignee = $estValide && $contract->getStatus() == contract::STATUT_SIGNE;

// Signature, en AJAX depuis le formulaire ci-dessous.
if ($task === 'signerContrat') {
    header('Content-Type: application/json');
    if (!$estValide || $contract->getStatus() != contract::STATUT_ATTENTE_SIGNATURE) {
        echo json_encode(array('ok' => false, 'error' => "Ce lien n'est plus valide ou ce contrat a déjà été traité."));
        exit;
    }
    $nomTape = isset($_POST['nom_signature']) ? trim($_POST['nom_signature']) : '';
    if ($nomTape === '' || !isset($_POST['certifie'])) {
        echo json_encode(array('ok' => false, 'error' => 'Merci de saisir votre nom complet et de cocher la case de certification.'));
        exit;
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';

    // Si aucun document n'a été déposé (cas le plus courant : contrat généré par le système), on
    // produit un PDF signé avec la mention d'acceptation - même principe que jobofferaccept. Si un
    // document a été déposé (Word/PDF, écrase la génération système, cf. remplacerContratGenere()),
    // on ne le modifie pas : c'est le document exact que le client a examiné avant de signer. La
    // preuve (nom tapé + IP + horodatage) reste alors dans les colonnes dédiées ci-dessous,
    // indépendamment du fichier.
    if (!$contract->getContratPDF()) {
        require_once("../../../../vendor/autoload.php");
        $dateSignature = date('d/m/Y à H:i');
        $attestation = '<div style="margin-top:30px;padding-top:15px;border-top:1px solid #ccc;font-size:9pt;">'
            . '<p><strong>Accepté électroniquement par ' . htmlspecialchars($nomTape) . ' le ' . $dateSignature . ' (adresse IP : ' . htmlspecialchars($ip) . ').</strong></p>'
            . '</div>';
        try {
            $mpdf = construireMpdfContratDepuisAccept($contract);
            $mpdf->WriteHTML($attestation);
            $devis = $contract->getDevis();
            $nomBase = 'contrat-signe-' . $contract->getId() . '-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) $devis->getNumero());
            $dossierDestination = '../../../../images/contracts';
            $nomFichier = $nomBase . '.pdf';
            $n = 0;
            while (file_exists("$dossierDestination/$nomFichier")) {
                $n++;
                $nomFichier = $nomBase . '-' . $n . '.pdf';
            }
            $mpdf->Output("$dossierDestination/$nomFichier", \Mpdf\Output\Destination::FILE);
            $contract->setContratPDF($nomFichier);
        } catch (\Exception $e) {
            echo json_encode(array('ok' => false, 'error' => "Erreur lors de la génération du document. Merci de réessayer ou de contacter l'agence."));
            exit;
        }
    }

    $contract->setStatus(contract::STATUT_SIGNE);
    $contract->setSignedAt(date('Y-m-d H:i:s'));
    $contract->setSignedByName($nomTape);
    $contract->setSignedIp($ip);
    $contract->setLastEdit(date('Y-m-d H:i:s'));
    $contract->edit();

    $devis = $contract->getDevis();
    $devis->setStatu(devis::STATU_CONTRAT_SIGNE);
    $devis->edit();

    echo json_encode(array('ok' => true));
    exit;
}

$devis = $estValide ? $contract->getDevis() : null;
$client = $devis ? $devis->getClient() : null;
$fichierDepose = $estValide ? $contract->getContratPDF() : '';
$extensionDeposee = $fichierDepose ? strtolower(pathinfo($fichierDepose, PATHINFO_EXTENSION)) : '';
$corpsAffiche = ($estValide && !$fichierDepose) ? ($contract->getCorpsGenere() ? $contract->getCorpsGenere() : contractGenerator::genererCorpsContrat($contract)) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="robots" content="noindex, nofollow">
	<title>Votre contrat</title>
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/style.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/modern-theme.css">
	<style>
		body { background: #f7f8f9; min-height: 100vh; }
		.contract-accept-wrap { max-width: 820px; margin: 0 auto; padding: 3rem 1.25rem; }
		.contract-accept-letter { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
		.contract-accept-file { background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; text-align: center; }
	</style>
</head>
<body>
<div class="contract-accept-wrap">

	<?php if (!$estValide) : ?>
		<div class="card"><div class="card-body text-center">
			<i class="fa fa-exclamation-triangle text-warning mb-2" style="font-size:2rem;"></i>
			<p class="mb-0">Ce lien n'est plus valide : il est incorrect, expiré, ou le contrat a été retiré.</p>
			<p class="text-muted small mt-2">Contactez l'agence pour plus d'informations.</p>
		</div></div>

	<?php elseif ($dejaSignee) : ?>
		<div class="card"><div class="card-body text-center">
			<i class="fa fa-check-circle text-success mb-2" style="font-size:2rem;"></i>
			<h5>Contrat déjà signé</h5>
			<p class="mb-0">Vous avez signé ce contrat le <?= date('d/m/Y à H:i', strtotime($contract->getSignedAt())) ?>. Merci !</p>
			<p class="text-muted small mt-2">L'équipe reviendra vers vous très prochainement.</p>
		</div></div>

	<?php elseif ($contract->getStatus() != contract::STATUT_ATTENTE_SIGNATURE) : ?>
		<div class="card"><div class="card-body text-center">
			<i class="fa fa-info-circle text-info mb-2" style="font-size:2rem;"></i>
			<p class="mb-0">Ce contrat n'est plus disponible à la signature.</p>
		</div></div>

	<?php else : ?>
		<h3 class="text-center mb-4">Bonjour <?= htmlspecialchars($client->getPrenom()) ?>, voici votre contrat pour le devis N°<?= htmlspecialchars($devis->getNumero()) ?></h3>

		<?php if ($fichierDepose) : ?>
			<div class="contract-accept-file">
				<?php if ($extensionDeposee === 'pdf') : ?>
					<iframe src="<?= $siteURL ?>images/contracts/<?= rawurlencode($fichierDepose) ?>" style="width:100%;height:70vh;border:1px solid #eee;border-radius:6px;"></iframe>
				<?php else : ?>
					<i class="fa fa-file-word mb-2" style="font-size:2.5rem;color:#2b579a;"></i>
					<p>Votre contrat vous a été envoyé en pièce jointe de l'email. Vous pouvez aussi le télécharger ici :</p>
				<?php endif; ?>
				<p class="mt-3"><a class="btn btn-white" href="<?= $siteURL ?>images/contracts/<?= rawurlencode($fichierDepose) ?>" target="_blank"><i class="fa fa-download mr-1"></i> Télécharger le contrat</a></p>
			</div>
		<?php else : ?>
			<div class="contract-accept-letter"><?= $corpsAffiche ?></div>
		<?php endif; ?>

		<div class="card">
			<div class="card-body">
				<h5 class="mb-3">Signer ce contrat</h5>
				<form id="contractAcceptForm">
					<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
					<div class="form-group">
						<label>Tapez votre nom complet pour signer</label>
						<input type="text" class="form-control" name="nom_signature" placeholder="<?= htmlspecialchars(trim($client->getPrenom() . ' ' . $client->getNom())) ?>" required>
					</div>
					<div class="form-group form-check">
						<input type="checkbox" class="form-check-input" id="certifie" name="certifie" required>
						<label class="form-check-label" for="certifie">Je certifie avoir lu et accepté les termes de ce contrat.</label>
					</div>
					<div class="msgbox"></div>
					<button type="submit" class="btn btn-success btn-block"><i class="fa fa-signature mr-1"></i> J'accepte et je signe</button>
				</form>
			</div>
		</div>
	<?php endif; ?>

</div>
<script src="<?= $siteURL ?>assets/js/jquery-3.5.1.min.js" onerror="this.onerror=null;this.src='https://code.jquery.com/jquery-3.6.0.min.js';"></script>
<script>
	document.getElementById('contractAcceptForm') && document.getElementById('contractAcceptForm').addEventListener('submit', function (e) {
		e.preventDefault();
		var form = e.target;
		var btn = form.querySelector('button[type=submit]');
		btn.disabled = true;
		var data = new FormData(form);
		data.append('task', 'signerContrat');
		fetch('', { method: 'POST', body: data })
			.then(function (r) { return r.json(); })
			.then(function (resp) {
				if (resp.ok) {
					window.location.reload();
				} else {
					btn.disabled = false;
					document.querySelector('.msgbox').innerHTML = '<div class="alert alert-danger p-2 small">' + resp.error + '</div>';
				}
			})
			.catch(function () {
				btn.disabled = false;
				document.querySelector('.msgbox').innerHTML = '<div class="alert alert-danger p-2 small">Erreur de connexion, merci de réessayer.</div>';
			});
	});
</script>
</body>
</html>
