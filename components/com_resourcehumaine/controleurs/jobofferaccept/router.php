<?php
// Point d'entrée AUTONOME (pas de $_SESSION['user'], jamais de session CRM classique) pour
// l'acceptation en ligne d'une offre d'emploi validée, via le lien envoyé par email au candidat
// (voir envoyerEmailOffreValideeAuCandidat() dans com_resourcehumaine/controleurs/joboffer/
// controleur.php). Signature électronique "maison" (nom tapé + tracé dessiné au doigt/à la souris
// + case à cocher + horodatage + IP) tant que SignWell n'est pas configuré - voir
// SIGNWELL_API_KEY dans config.secrets.php. Un seul
// fichier, volontairement, même principe que components/com_client/controleurs/socialaccess/
// router.php : point d'entrée sensible, plus simple à auditer d'un bloc.
require_once("../../../../config.php");
require_once("../../../../instanceDb.php");
require_once("../../../../includes/functions/functions.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['langue']) || empty($_SESSION['langue'])) {
    $_SESSION['langue'] = 'fr';
}

$task = isset($_POST['task']) ? $_POST['task'] : (isset($_GET['task']) ? $_GET['task'] : 'view');
$token = isset($_GET['token']) ? $_GET['token'] : (isset($_POST['token']) ? $_POST['token'] : '');
$offer = joboffer::findByAcceptanceToken($token);
$estValide = $offer->getId() != 0;
$dejaSignee = $estValide && $offer->getStatut() === joboffer::STATUT_SIGNEE;

// Acceptation, en AJAX depuis le formulaire ci-dessous.
if ($task === 'accepterOffre') {
    header('Content-Type: application/json');
    if (!$estValide || $offer->getStatut() !== joboffer::STATUT_VALIDEE) {
        echo json_encode(array('ok' => false, 'error' => 'Ce lien n\'est plus valide ou cette offre a déjà été traitée.'));
        exit;
    }
    $nomTape = isset($_POST['nom_signature']) ? trim($_POST['nom_signature']) : '';
    $signatureData = isset($_POST['signature_data']) ? trim($_POST['signature_data']) : '';
    if ($nomTape === '' || !isset($_POST['certifie']) || $signatureData === '' || strpos($signatureData, 'data:image/png;base64,') !== 0) {
        echo json_encode(array('ok' => false, 'error' => 'Merci de saisir votre nom complet, de signer dans le cadre prévu et de cocher la case de certification.'));
        exit;
    }

    require_once("../../../../vendor/autoload.php");
    $resourcehumaine = $offer->getResourcehumaine();
    $dateSignature = date('d/m/Y à H:i');
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue';

    // Même en-tête/pied de page répété sur chaque page que le PDF de prévisualisation admin
    // (joboffer::mpdfHeaderFooterBlock(), task=telechargerOffrePDF) - le document final signé doit
    // avoir le même rendu que ce que le candidat a consulté avant d'accepter. Le tracé de signature
    // (canvas ci-dessous) est intégré tel quel en base64 - mPDF le rend nativement, pas besoin de
    // l'écrire sur disque séparément du PDF final.
    $htmlSigne = '<html><head><style>body{font-family: Calibri, Arial, sans-serif;}</style></head><body>'
        . joboffer::mpdfHeaderFooterBlock($resourcehumaine->getAgency())
        . $offer->getContenu()
        . '<div style="margin-top:30px; padding-top:15px; border-top:1px solid #ccc; font-size:9pt;">'
        . '<img src="' . $signatureData . '" style="max-width:260px; max-height:90px; display:block; margin-bottom:6px;">'
        . '<p><strong>' . htmlspecialchars($nomTape) . '</strong><br>Accepté et signé électroniquement le ' . $dateSignature . ' (adresse IP : ' . htmlspecialchars($ip) . ').</p>'
        . '</div></body></html>';

    try {
        $mpdf = new \Mpdf\Mpdf(['margin_top' => 35, 'margin_header' => 8, 'margin_footer' => 12, 'margin_bottom' => 20]);
        $mpdf->WriteHTML($htmlSigne);
        $nomBase = 'offre-signee-' . $resourcehumaine->getId() . '-' . $offer->getId();
        $nomBase = str_replace(array('/', '\\'), '-', $nomBase);
        $dossierDestination = '../../../../images/resourceshumaines/offres';
        if (!is_dir($dossierDestination)) {
            mkdir($dossierDestination, 0755, true);
        }
        $nomFichier = $nomBase . '.pdf';
        $n = '';
        while (file_exists("$dossierDestination/$nomBase$n.pdf")) {
            $n++;
            $nomFichier = $nomBase . $n . '.pdf';
        }
        $mpdf->Output("$dossierDestination/$nomFichier", \Mpdf\Output\Destination::FILE);
    } catch (\Exception $e) {
        echo json_encode(array('ok' => false, 'error' => 'Erreur lors de la génération du document. Merci de réessayer ou de contacter l\'administration.'));
        exit;
    }

    $offer->setStatut(joboffer::STATUT_SIGNEE);
    $offer->setSignedFile($nomFichier);
    $offer->setSignedAt(date('Y-m-d H:i:s'));
    $offer->edit();

    echo json_encode(array('ok' => true));
    exit;
}

$resourcehumaine = $estValide ? $offer->getResourcehumaine() : null;
$nomComplet = $resourcehumaine ? trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName()) : '';
$documents = ($estValide && $resourcehumaine) ? fileresourcehumaine::documentsRequis($resourcehumaine->getStatus()) : array();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="robots" content="noindex, nofollow">
	<title>Votre offre d'emploi</title>
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/style.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/modern-theme.css">
	<style>
		body { background: #f7f8f9; min-height: 100vh; }
		.offer-accept-wrap { max-width: 820px; margin: 0 auto; padding: 3rem 1.25rem; }
		.offer-accept-letter { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
		.offer-accept-docs { background: #fff7e6; border: 1px solid #e8b23d; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; }
	</style>
</head>
<body>
<div class="offer-accept-wrap">

	<?php if (!$estValide) : ?>
		<div class="card"><div class="card-body text-center">
			<i class="fa fa-exclamation-triangle text-warning mb-2" style="font-size:2rem;"></i>
			<p class="mb-0">Ce lien n'est plus valide : il est incorrect, expiré, ou l'offre a été retirée.</p>
			<p class="text-muted small mt-2">Contactez l'administration pour plus d'informations.</p>
		</div></div>

	<?php elseif ($dejaSignee) : ?>
		<div class="card"><div class="card-body text-center">
			<i class="fa fa-check-circle text-success mb-2" style="font-size:2rem;"></i>
			<h5>Offre déjà acceptée</h5>
			<p class="mb-0">Vous avez accepté cette offre le <?= date('d/m/Y à H:i', strtotime($offer->getSignedAt())) ?>. Merci !</p>
			<p class="text-muted small mt-2">L'équipe des ressources humaines reviendra vers vous très prochainement.</p>
		</div></div>

	<?php elseif ($offer->getStatut() !== joboffer::STATUT_VALIDEE) : ?>
		<div class="card"><div class="card-body text-center">
			<i class="fa fa-info-circle text-info mb-2" style="font-size:2rem;"></i>
			<p class="mb-0">Cette offre n'est plus disponible à l'acceptation.</p>
		</div></div>

	<?php else : ?>
		<h3 class="text-center mb-4">Bonjour <?= htmlspecialchars($resourcehumaine->getFirstName()) ?>, voici votre offre d'emploi</h3>

		<div class="offer-accept-letter"><?= $offer->getContenu() ?></div>

		<?php if (!empty($documents)) : ?>
		<div class="offer-accept-docs">
			<strong><i class="fa fa-folder-open mr-1"></i> Documents à nous transmettre dès que possible :</strong>
			<ul class="mb-0 mt-2">
				<?php foreach ($documents as $libelle) : ?>
					<li><?= htmlspecialchars($libelle) ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<div class="card">
			<div class="card-body">
				<h5 class="mb-3">Accepter cette offre</h5>
				<form id="offerAcceptForm">
					<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
					<div class="form-group">
						<label>Tapez votre nom complet pour signer</label>
						<input type="text" class="form-control" name="nom_signature" placeholder="<?= htmlspecialchars($nomComplet) ?>" required>
					</div>
					<div class="form-group">
						<label>Signez ci-dessous avec la souris ou le doigt</label>
						<div style="border:1px solid #ced4da; border-radius:4px; background:#fff;">
							<canvas id="signaturePad" style="width:100%; height:180px; touch-action:none; cursor:crosshair; display:block;"></canvas>
						</div>
						<input type="hidden" name="signature_data" id="signatureData">
						<button type="button" id="clearSignature" class="btn btn-white btn-sm mt-2"><i class="fa fa-eraser mr-1"></i> Effacer la signature</button>
					</div>
					<div class="form-group form-check">
						<input type="checkbox" class="form-check-input" id="certifie" name="certifie" required>
						<label class="form-check-label" for="certifie">Je certifie avoir lu et je vous ai lues, comprises et j'accepte les termes de cette offre d'emploi.</label>
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
	// Pavé de signature manuscrite (canvas natif, pas de librairie externe) - le tracé est renvoyé
	// en PNG base64 dans #signatureData et intégré tel quel dans le PDF signé côté serveur (mPDF
	// accepte les data URI directement en <img src>).
	(function () {
		var canvas = document.getElementById('signaturePad');
		if (!canvas) return;
		var ctx = canvas.getContext('2d');
		var hiddenField = document.getElementById('signatureData');
		var hasSignature = false;
		var drawing = false;
		var lastX = 0;
		var lastY = 0;

		function resizeCanvas() {
			// Redimensionne le canvas à la résolution d'écran réelle (retina) tout en gardant les
			// coordonnées de dessin dans le repère CSS - sinon le trait rendu est flou/pixellisé.
			var ratio = window.devicePixelRatio || 1;
			var rect = canvas.getBoundingClientRect();
			var dessinExistant = hasSignature ? canvas.toDataURL('image/png') : null;
			canvas.width = rect.width * ratio;
			canvas.height = rect.height * ratio;
			ctx.scale(ratio, ratio);
			ctx.lineWidth = 2;
			ctx.lineCap = 'round';
			ctx.lineJoin = 'round';
			ctx.strokeStyle = '#1a1a1a';
			if (dessinExistant) {
				var img = new Image();
				img.onload = function () { ctx.drawImage(img, 0, 0, rect.width, rect.height); };
				img.src = dessinExistant;
			}
		}
		resizeCanvas();
		window.addEventListener('resize', resizeCanvas);

		function pos(e) {
			var rect = canvas.getBoundingClientRect();
			var t = e.touches && e.touches.length ? e.touches[0] : e;
			return { x: t.clientX - rect.left, y: t.clientY - rect.top };
		}
		function start(e) {
			e.preventDefault();
			drawing = true;
			var p = pos(e);
			lastX = p.x;
			lastY = p.y;
		}
		function move(e) {
			if (!drawing) return;
			e.preventDefault();
			var p = pos(e);
			ctx.beginPath();
			ctx.moveTo(lastX, lastY);
			ctx.lineTo(p.x, p.y);
			ctx.stroke();
			lastX = p.x;
			lastY = p.y;
			hasSignature = true;
			hiddenField.value = canvas.toDataURL('image/png');
		}
		function end() { drawing = false; }

		canvas.addEventListener('mousedown', start);
		canvas.addEventListener('mousemove', move);
		window.addEventListener('mouseup', end);
		canvas.addEventListener('touchstart', start, { passive: false });
		canvas.addEventListener('touchmove', move, { passive: false });
		canvas.addEventListener('touchend', end);

		document.getElementById('clearSignature').addEventListener('click', function () {
			ctx.clearRect(0, 0, canvas.width, canvas.height);
			hasSignature = false;
			hiddenField.value = '';
		});
	})();

	document.getElementById('offerAcceptForm') && document.getElementById('offerAcceptForm').addEventListener('submit', function (e) {
		e.preventDefault();
		var form = e.target;
		if (!document.getElementById('signatureData').value) {
			document.querySelector('.msgbox').innerHTML = '<div class="alert alert-danger p-2 small">Merci de signer dans le cadre prévu avant de valider.</div>';
			return;
		}
		var btn = form.querySelector('button[type=submit]');
		btn.disabled = true;
		var data = new FormData(form);
		data.append('task', 'accepterOffre');
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
