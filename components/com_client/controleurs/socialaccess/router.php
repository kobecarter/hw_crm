<?php
// Point d'entrée AUTONOME (pas de $_SESSION['user'], jamais de session CRM classique) pour l'accès
// temporaire de 24h à la page "Réseaux sociaux" d'UN client, via un lien token+code généré par un
// admin (voir components/com_client/views/client/social.php, bouton "Générer un accès temporaire").
// Volontairement un seul fichier, lisible de bout en bout - c'est le point d'entrée le plus sensible
// de cette fonctionnalité, mieux vaut ne pas l'éclater en plusieurs inclusions.
require_once("../../../../config.php");
require_once("../../../../instanceDb.php");
require_once("../../../../includes/functions/functions.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['langue']) || empty($_SESSION['langue'])) {
    $_SESSION['langue'] = 'fr';
}
$config = new config($db);

$task = isset($_POST['task']) ? $_POST['task'] : (isset($_GET['task']) ? $_GET['task'] : 'view');

// Vérification du code, en AJAX depuis le formulaire ci-dessous.
if ($task === 'verifyAccessCode') {
    header('Content-Type: application/json');
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    $code = isset($_POST['code']) ? $_POST['code'] : '';
    $t = clientsocialtoken::verify($token, $code);
    if ($t) {
        $_SESSION['socialAccessTokenId'] = $t->getId();
        $t->markUsed();
        echo json_encode(array('ok' => true));
    } else {
        // Message volontairement générique (ne dit jamais si c'est le token ou le code qui cloche).
        echo json_encode(array('ok' => false, 'error' => 'Lien ou code invalide, expiré, ou révoqué.'));
    }
    exit;
}

$token = isset($_GET['token']) ? $_GET['token'] : '';
$tokenObj = clientsocialtoken::findByToken($token);
$estValide = $tokenObj->getId() != 0 && !$tokenObj->isRevoked() && !$tokenObj->isExpired();
$sessionValide = $estValide && isset($_SESSION['socialAccessTokenId']) && $_SESSION['socialAccessTokenId'] == $tokenObj->getId();

if ($task === 'logout') {
    unset($_SESSION['socialAccessTokenId']);
    header('Location: ' . $siteURL . 'components/com_client/controleurs/socialaccess/router.php?token=' . urlencode($token));
    exit;
}

$client = $sessionValide ? $tokenObj->getClient() : null;
$clientNomAffiche = $client ? ($client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getTitre() . ' ' . $client->getNom() . ' ' . $client->getPrenom())) : '';
$entries = array();
if ($sessionValide) {
    $entries = clientsocial::findAllByClient($client->getId());
    if ($tokenObj->getScopeType() === 'specific') {
        $entries = array_values(array_filter($entries, function ($cs) use ($tokenObj) {
            return $cs->getPlateforme() === $tokenObj->getScopePlateforme();
        }));
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="robots" content="noindex, nofollow">
	<title>Accès temporaire — Réseaux sociaux</title>
	<link rel="shortcut icon" href="<?= $siteURL ?>assets/img/favicon.png">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/style.css">
	<link rel="stylesheet" href="<?= $siteURL ?>assets/css/modern-theme.css">
	<style>
		body { background: #f7f8f9; min-height: 100vh; }
		.social-access-wrap { max-width: 720px; margin: 0 auto; padding: 3rem 1.25rem; }
		.social-access-header { text-align: center; margin-bottom: 2rem; }
		.social-access-expiry { font-size: 0.85rem; color: var(--ink-soft); text-align: center; margin-bottom: 1.5rem; }
		.social-access-code-card { max-width: 420px; margin: 2rem auto 0; }
	</style>
</head>
<body>
<div class="social-access-wrap">
	<div class="social-access-header">
		<img class="img-fluid mb-2" src="<?= $siteURL ?>images/config/<?php echo $config->getLogo(); ?>" alt="" style="max-height:60px;">
		<h3 class="mt-2">Accès temporaire — Réseaux sociaux</h3>
	</div>

	<?php if (!$estValide) : ?>
		<div class="card social-access-code-card">
			<div class="card-body text-center">
				<i class="fa fa-exclamation-triangle text-warning mb-2" style="font-size:2rem;"></i>
				<p class="mb-0">Ce lien n'est plus valide : il a expiré, a été révoqué, ou n'existe pas.</p>
				<p class="text-muted small mt-2">Demandez un nouveau lien à votre contact chez Hello World.</p>
			</div>
		</div>

	<?php elseif (!$sessionValide) : ?>
		<div class="card social-access-code-card">
			<div class="card-body">
				<h5 class="mb-3 text-center">Entrez le code de sécurité</h5>
				<p class="text-muted small text-center">Ce code vous a été communiqué séparément par la personne qui vous a envoyé ce lien.</p>
				<form id="socialAccessCodeForm">
					<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
					<div class="form-group">
						<input type="text" inputmode="numeric" maxlength="6" class="form-control text-center" style="font-size:1.4rem; letter-spacing:0.3rem;" name="code" id="socialAccessCode" placeholder="000000" autofocus>
					</div>
					<div class="msgbox"></div>
					<button type="submit" class="btn btn-primary btn-block">Valider</button>
				</form>
			</div>
		</div>
		<script src="<?= $siteURL ?>assets/js/jquery-3.5.1.min.js" onerror="this.onerror=null;this.src='https://code.jquery.com/jquery-3.6.0.min.js';"></script>
		<script>
			document.getElementById('socialAccessCodeForm').addEventListener('submit', function (e) {
				e.preventDefault();
				var form = e.target;
				var data = new FormData(form);
				data.append('task', 'verifyAccessCode');
				fetch('', { method: 'POST', body: data })
					.then(function (r) { return r.json(); })
					.then(function (resp) {
						if (resp.ok) {
							window.location.reload();
						} else {
							document.querySelector('.msgbox').innerHTML = '<div class="alert alert-danger p-2 small">' + resp.error + '</div>';
						}
					});
			});
		</script>

	<?php else : ?>
		<p class="social-access-expiry">
			<i class="fa fa-clock mr-1"></i> Accès valable jusqu'au <?php echo date('d/m/Y à H:i', strtotime($tokenObj->getExpiresAt())); ?>
			— <a href="?task=logout&token=<?= htmlspecialchars($token) ?>">se déconnecter de cet accès</a>
		</p>
		<div class="alert alert-warning">
			<i class="fa fa-shield-alt mr-2"></i>
			Client : <strong><?php echo htmlspecialchars($clientNomAffiche); ?></strong> —
			<?php echo $tokenObj->getScopeType() === 'specific' ? 'accès restreint à ' . htmlspecialchars($tokenObj->getScopePlateforme()) : 'tous les accès réseaux sociaux'; ?>.
			Vérifiez que chaque identifiant fonctionne encore ; mettez à jour ceux qui ont changé.
		</div>

		<div class="row social-accounts-row">
			<?php if (empty($entries)) : ?>
				<div class="col-12"><p class="text-muted text-center">Aucun identifiant enregistré pour l'instant.</p></div>
			<?php endif; ?>
			<?php foreach ($entries as $entry) : ?>
				<?php $def = isset(clientsocial::$plateformes[$entry->getPlateforme()]) ? clientsocial::$plateformes[$entry->getPlateforme()] : array('label' => $entry->getPlateforme(), 'icon' => 'fa-key', 'couleur' => '#64748b'); ?>
				<div class="col-md-6 col-12 d-flex">
					<div class="card flex-fill social-account-card">
						<div class="card-body">
							<div class="social-account-head">
								<span class="social-account-icon" style="background:<?php echo htmlspecialchars($def['couleur']); ?>;"><i class="fa <?php echo htmlspecialchars($def['icon']); ?>"></i></span>
								<h5 class="social-account-title"><?php echo htmlspecialchars($def['label']); ?></h5>
							</div>
							<ul class="social-account-fields">
								<li><span class="social-account-label">Identifiant</span><span class="social-account-value"><?php echo $entry->getLogin() != '' ? htmlspecialchars($entry->getLogin()) : '—'; ?></span></li>
								<li>
									<span class="social-account-label">Mot de passe</span>
									<span class="social-account-value social-account-password" data-id="<?php echo $entry->getId(); ?>">
										<span class="social-password-masked">••••••••</span>
										<span class="social-password-plain" style="display:none;"></span>
										<a href="javascript:void(0);" class="social-password-reveal-btn" title="Révéler"><i class="fa fa-eye"></i></a>
									</span>
								</li>
								<li><span class="social-account-label">Lien</span><span class="social-account-value"><?php if ($entry->getLien() != '') : ?><a href="<?php echo htmlspecialchars($entry->getLien()); ?>" target="_blank" rel="noopener">Ouvrir</a><?php else : ?>—<?php endif; ?></span></li>
								<li><span class="social-account-label">Email récup.</span><span class="social-account-value"><?php echo $entry->getEmailRecuperation() != '' ? htmlspecialchars($entry->getEmailRecuperation()) : '—'; ?></span></li>
								<li><span class="social-account-label">Tél. récup.</span><span class="social-account-value"><?php echo $entry->getTelephoneRecuperation() != '' ? htmlspecialchars($entry->getTelephoneRecuperation()) : '—'; ?></span></li>
							</ul>
							<a href="javascript:void(0);" class="btn btn-white btn-sm social-account-edit-btn"
								data-id="<?php echo $entry->getId(); ?>"
								data-plateforme="<?php echo htmlspecialchars($entry->getPlateforme()); ?>"
								data-login="<?php echo htmlspecialchars($entry->getLogin()); ?>"
								data-lien="<?php echo htmlspecialchars($entry->getLien()); ?>"
								data-email="<?php echo htmlspecialchars($entry->getEmailRecuperation()); ?>"
								data-tel="<?php echo htmlspecialchars($entry->getTelephoneRecuperation()); ?>"
								data-remarque="<?php echo htmlspecialchars($entry->getRemarque()); ?>"
							><i class="fa fa-pencil-alt mr-1"></i> Mettre à jour</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Modale de mise à jour (mêmes champs que la page admin) -->
		<div id="socialAccountModal" class="modal custom-modal fade" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="socialAccountModalTitle">Mettre à jour</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					<form id="socialAccountForm">
						<div class="modal-body">
							<input type="hidden" name="id" id="socialFieldId" value="">
							<input type="hidden" name="id_client" value="<?php echo $client->getId(); ?>">
							<input type="hidden" name="plateforme" id="socialFieldPlateforme" value="">
							<div class="form-group"><label>Identifiant / nom d'utilisateur</label><input type="text" class="form-control" name="login" id="socialFieldLogin"></div>
							<div class="form-group"><label>Mot de passe</label><input type="password" class="form-control" name="password" id="socialFieldPassword" autocomplete="new-password" placeholder="Laisser vide pour ne pas modifier"></div>
							<div class="form-group"><label>Lien</label><input type="text" class="form-control" name="lien" id="socialFieldLien"></div>
							<div class="form-group"><label>Email de récupération</label><input type="text" class="form-control" name="email_recuperation" id="socialFieldEmail"></div>
							<div class="form-group"><label>Téléphone de récupération</label><input type="text" class="form-control" name="telephone_recuperation" id="socialFieldTel"></div>
							<div class="form-group"><label>Remarque</label><textarea class="form-control" name="remarque" id="socialFieldRemarque" rows="2"></textarea></div>
							<div class="msgbox"></div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
							<button type="submit" class="btn btn-primary">Enregistrer</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script src="<?= $siteURL ?>assets/js/jquery-3.5.1.min.js" onerror="this.onerror=null;this.src='https://code.jquery.com/jquery-3.6.0.min.js';"></script>
		<script src="<?= $siteURL ?>assets/js/bootstrap.min.js" onerror="this.onerror=null;this.src='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js';"></script>
		<script>
			var SOCIAL_ROUTER = '<?= $siteURL ?>components/com_client/controleurs/router.php';
			$(function () {
				$('.social-account-edit-btn').on('click', function () {
					var $btn = $(this);
					$('#socialAccountForm')[0].reset();
					$('#socialFieldId').val($btn.data('id'));
					$('#socialFieldPlateforme').val($btn.data('plateforme'));
					$('#socialFieldLogin').val($btn.data('login'));
					$('#socialFieldLien').val($btn.data('lien'));
					$('#socialFieldEmail').val($btn.data('email'));
					$('#socialFieldTel').val($btn.data('tel'));
					$('#socialFieldRemarque').val($btn.data('remarque'));
					$('#socialAccountModalTitle').text('Mettre à jour — ' + $btn.data('plateforme'));
					$('#socialAccountModal').modal('show');
				});
				$('#socialAccountForm').on('submit', function (e) {
					e.preventDefault();
					$.post(SOCIAL_ROUTER + '?task=editClientSocial', $(this).serialize(), function (resp) {
						if (parseInt(resp) === 1) {
							document.location.reload();
						} else {
							$('#socialAccountForm .msgbox').html('<div class="alert alert-danger p-2 small">Erreur lors de l\'enregistrement.</div>');
						}
					});
				});
				$('.social-password-reveal-btn').on('click', function () {
					var $btn = $(this);
					var $wrap = $btn.closest('.social-account-password');
					var $masked = $wrap.find('.social-password-masked');
					var $plain = $wrap.find('.social-password-plain');
					if ($plain.is(':visible')) {
						$plain.hide(); $masked.show();
						$btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
						return;
					}
					$.post(SOCIAL_ROUTER + '?task=revealClientSocialPassword', { id: $wrap.data('id') }, function (resp) {
						var data = typeof resp === 'string' ? JSON.parse(resp) : resp;
						if (data.error) { alert('Accès refusé ou identifiant introuvable.'); return; }
						$plain.text(data.password !== '' ? data.password : '(vide)').show();
						$masked.hide();
						$btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
					}, 'json');
				});
			});
		</script>
	<?php endif; ?>
</div>
</body>
</html>
