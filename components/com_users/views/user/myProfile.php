<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h3 class="page-title">Mon profil</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Mon profil</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-9 col-md-8">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Mes informations</h5>
					</div>
					<div class="card-body">

						<!-- Form -->
						<?php include('formSelf.php'); ?>
						<!-- /Form -->

					</div>
				</div>

				<?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== '') :?>
				<!-- Lier/délier un compte Google : action volontaire depuis un compte déjà authentifié
				     (jamais utilisée pour se connecter la première fois) - voir googleLink()/
				     googleUnlink() dans com_login/controleurs/login.php. Une fois lié, "Se connecter
				     avec Google" devient possible depuis la page de login avec ce même compte Google. -->
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Connexion avec Google</h5>
					</div>
					<div class="card-body">
						<div id="googleLinkMsgbox" class="msgbox"></div>
						<?php if ($user->getGoogleEmail()) :?>
							<p class="mb-2">Compte Google lié : <strong><?= htmlspecialchars($user->getGoogleEmail()) ?></strong></p>
							<button type="button" class="btn btn-white text-danger" id="googleUnlinkBtn"><i class="fa fa-unlink mr-1"></i>Délier ce compte Google</button>
						<?php else :?>
							<p class="text-muted mb-2">Liez votre compte Google pour pouvoir vous connecter au CRM sans mot de passe.</p>
							<div id="googleLinkBtn"></div>
						<?php endif;?>
					</div>
				</div>
				<script src="https://accounts.google.com/gsi/client" async defer></script>
				<script type="text/javascript">
					$(function () {
						$('#googleUnlinkBtn').on('click', function () {
							var $btn = $(this).prop('disabled', true);
							$.post('components/com_login/controleurs/router.php?task=googleUnlink', {}, function (data) {
								if (data.success) {
									location.reload();
								} else {
									$btn.prop('disabled', false);
									$('#googleLinkMsgbox').html('<div class="alert alert-danger" role="alert">' + (data.message || 'Erreur.') + '</div>');
								}
							}, 'json');
						});

						function initGoogleLinkButton() {
							if (typeof google === 'undefined' || !google.accounts || !document.getElementById('googleLinkBtn')) {
								if (document.getElementById('googleLinkBtn')) {
									setTimeout(initGoogleLinkButton, 200);
								}
								return;
							}
							google.accounts.id.initialize({
								client_id: <?= json_encode(GOOGLE_CLIENT_ID) ?>,
								callback: function (response) {
									$.post('components/com_login/controleurs/router.php?task=googleLink', { credential: response.credential }, function (data) {
										if (data.success) {
											location.reload();
										} else {
											$('#googleLinkMsgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> ' + (data.message || 'Liaison impossible.') + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
										}
									}, 'json');
								}
							});
							google.accounts.id.renderButton(document.getElementById('googleLinkBtn'), { theme: 'outline', size: 'large', text: 'signin_with' });
						}
						initGoogleLinkButton();
					});
				</script>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
<!--  /Page Wrapper-->
