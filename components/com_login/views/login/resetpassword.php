<?php
// Page atterrissage du lien "mot de passe oublié" (voir controleurs/login.php::resetPasswordRequest()
// pour l'envoi de l'email, ::resetPasswordSubmit() pour le traitement AJAX de ce formulaire).
// Vérifié ICI aussi (en plus du contrôle serveur au submit) pour ne jamais laisser un lien mort
// afficher un formulaire qui échouera de toute façon - mais le contrôle qui compte réellement
// reste côté serveur au moment du submit, jamais celui-ci seul.
$resetValide = trim((string) $tokenReset) !== '' && userpasswordreset::findValidByToken($tokenReset)->isUsable();
?>
		<!-- Main Wrapper -->
		<div class="main-wrapper login-body">
			<video class="login-body-video" autoplay muted loop playsinline poster="images/config/loginbackground.jpg">
				<source src="images/config/backgorundlogin.mp4" type="video/mp4">
			</video>
			<div class="login-wrapper">
				<div class="container">

					<img class="img-fluid logo-dark mb-2" src="images/config/<?php echo $config->getLogo(); ?>" alt="<?php echo $config->getNom(); ?>">
					<div class="loginbox">

						<div class="login-right">
							<div class="login-right-wrap">
								<?php if (!$resetValide) :?>
									<h1>Lien invalide</h1>
									<p class="account-subtitle">Ce lien de réinitialisation est invalide ou a expiré (valable 1 heure). Refaites une demande depuis la page de connexion.</p>
									<a class="btn btn-lg btn-block btn-primary" href="index.php?option=com_login">Retour à la connexion</a>
								<?php else :?>
									<h1>Nouveau mot de passe</h1>
									<p class="account-subtitle">Choisissez un nouveau mot de passe (8 caractères minimum).</p>

									<form method="post" id="resetPasswordForm">
										<div class="msgbox"></div>
										<input type="hidden" name="token" value="<?= htmlspecialchars($tokenReset) ?>">
										<div class="form-group">
											<label class="form-control-label">Nouveau mot de passe</label>
											<div class="pass-group">
												<input type="password" name="password" class="form-control pass-input" minlength="8" required>
												<span class="fas fa-eye toggle-password"></span>
											</div>
										</div>
										<div class="form-group">
											<label class="form-control-label">Confirmer le mot de passe</label>
											<div class="pass-group">
												<input type="password" name="password_confirm" class="form-control pass-input" minlength="8" required>
												<span class="fas fa-eye toggle-password"></span>
											</div>
										</div>
										<button class="btn btn-lg btn-block btn-primary" type="submit">Réinitialiser mon mot de passe</button>
									</form>
								<?php endif;?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Main Wrapper -->

		<?php if ($resetValide) :?>
		<script type="text/javascript">
			// window.addEventListener('load', ...), jamais $(function(){...}) : ce script inline est
			// imprimé avant includes/tpl/bottom-login.php, qui charge jQuery en fin de page - $ n'existe
			// pas encore au moment du parsing (voir même bug/fix dans login.php ci-dessus).
			window.addEventListener('load', function () {
				$('#resetPasswordForm').on('submit', function (e) {
					e.preventDefault();
					var $form = $(this);
					var $msgbox = $form.find('.msgbox');
					var password = $form.find('input[name="password"]').val();
					var passwordConfirm = $form.find('input[name="password_confirm"]').val();

					if (password !== passwordConfirm) {
						$msgbox.html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Les mots de passe ne correspondent pas.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						return;
					}

					var $btn = $form.find('button[type="submit"]').prop('disabled', true);
					$.post('components/com_login/controleurs/router.php?task=resetPasswordSubmit', {
						token: $form.find('input[name="token"]').val(),
						password: password
					}, function (response) {
						if (response.success) {
							$msgbox.html('<div class="alert alert-success" role="alert"><strong>Succès!</strong> ' + response.message + '</div>');
							$form.find('input,button').prop('disabled', true);
							setTimeout(function () {
								document.location = 'index.php?option=com_login';
							}, 2000);
						} else {
							$btn.prop('disabled', false);
							$msgbox.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> ' + (response.message || 'Une erreur est survenue.') + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						}
					}, 'json').fail(function () {
						$btn.prop('disabled', false);
						$msgbox.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Impossible de contacter le serveur.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					});
				});
			});
		</script>
		<?php endif;?>
