
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
								<h1>Login</h1>
								<p class="account-subtitle">Veuillez entrer votre login et mot de passe</p>
								
								<!-- Action relative (jamais préfixée par $siteURL, qui vaut littéralement
								     "http://localhost/hw_crm/" en dur dans config.php) : un téléphone qui
								     accède au site via l'IP LAN du poste de dev (ou tout autre nom d'hôte
								     que "localhost", ex. le domaine réel en prod) chargeait la page
								     correctement mais le formulaire POSTait alors vers "localhost" au sens du
								     TÉLÉPHONE lui-même - une adresse qui ne répond jamais depuis cet appareil,
								     d'où un clic sur "Login" sans aucun effet visible. Une action relative se
								     résout toujours par rapport à l'hôte qui a réellement servi la page. -->
								<form method="post" id="loginForm" action="components/com_login/controleurs/router.php?task=login">
									<div class="msgbox"></div>
									<div class="form-group">
										<label class="form-control-label">Login</label>
										<input type="text" name="login" class="form-control">
									</div>
									<div class="form-group">
										<label class="form-control-label">Mot de passe</label>
										<div class="pass-group">
											<input type="password" name="password" class="form-control pass-input">
											<span class="fas fa-eye toggle-password"></span>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-6">
												<div class="custom-control custom-checkbox">
													<input type="checkbox" name="remember" class="custom-control-input" id="cb1">
													<label class="custom-control-label" for="cb1">Se rappeler de moi</label>
												</div>
											</div>
											<div class="col-6 text-right">
												<a class="forgot-link" href="javascript:void(0);" data-toggle="modal" data-target="#forgotPasswordModal">Mot de passe oublié ?</a>
											</div>
										</div>
									</div>
									<button class="btn btn-lg btn-block btn-primary" type="submit">Login</button>
								</form>

								<?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== '') :?>
								<!-- "Se connecter avec Google" : même vérification serveur (tokeninfo) que
								     client::googleLoginApi() (espace client, déjà en production) - voir
								     verifierIdTokenGoogle() dans com_login/controleurs/login.php. Le bouton
								     lui-même est entièrement rendu par Google Identity Services dans
								     #googleLoginBtn (jamais un bouton HTML fait main : Google impose son
								     propre rendu/texte/logo pour ce bouton). -->
								<div class="login-or">
									<span class="or-line"></span>
									<span class="span-or">Autre moyen de se connecter</span>
								</div>
								<div id="googleLoginBtn" class="d-flex justify-content-center mb-3"></div>
								<div id="googleLoginMsgbox" class="msgbox"></div>
								<?php endif;?>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Main Wrapper -->

		<!-- Popup "Mot de passe oublié" - même habillage que les autres fenêtres modales de l'app
		     (bootstrap.min.js déjà chargé sur cette page, includes/tpl/bottom-login.php). -->
		<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Mot de passe oublié ?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<div class="msgbox"></div>
						<form id="forgotPasswordForm">
							<div class="form-group">
								<label class="form-control-label">Votre email</label>
								<input type="email" name="email" class="form-control" required>
							</div>
							<button class="btn btn-lg btn-block btn-primary" type="submit">Envoyer le lien de réinitialisation</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== '') :?>
		<script src="https://accounts.google.com/gsi/client" async defer></script>
		<?php endif;?>
		<script type="text/javascript">
			// window.addEventListener('load', ...), jamais $(function(){...}) : ce bloc est imprimé
			// dans le <body> par la VUE (components/com_login/views/login/login.php), incluse par
			// index.php AVANT includes/tpl/bottom-login.php - c'est CE fichier-là qui charge jQuery
			// (assets/js/jquery-3.5.1.min.js), en toute fin de page. Au moment où ce <script> inline
			// s'exécute (au fil du parsing HTML, donc bien avant d'atteindre le <script> de jQuery
			// plus bas), $ n'existe donc pas encore - $(function(){...}) plantait immédiatement
			// (« $ is not defined »), empêchant tout le bloc de s'enregistrer : ni la fenêtre "mot de
			// passe oublié" ni le bouton Google ne fonctionnaient. "load" se déclenche après TOUS les
			// scripts de la page (peu importe leur ordre dans le HTML), $ est alors garanti défini.
			window.addEventListener('load', function () {
				$('#forgotPasswordForm').on('submit', function (e) {
					e.preventDefault();
					var $form = $(this);
					var $msgbox = $('#forgotPasswordModal .modal-body > .msgbox');
					var $btn = $form.find('button[type="submit"]').prop('disabled', true);
					$.post('components/com_login/controleurs/router.php?task=resetPasswordRequest', $form.serialize(), function (response) {
						$msgbox.html('<div class="alert alert-success" role="alert">' + response.message + '</div>');
						$form[0].reset();
					}, 'json').fail(function () {
						$msgbox.html('<div class="alert alert-danger" role="alert">Impossible de contacter le serveur.</div>');
					}).always(function () {
						$btn.prop('disabled', false);
					});
				});

				<?php if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== '') :?>
				function initGoogleLoginButton() {
					if (typeof google === 'undefined' || !google.accounts || !document.getElementById('googleLoginBtn')) {
						setTimeout(initGoogleLoginButton, 200);
						return;
					}
					google.accounts.id.initialize({
						client_id: <?= json_encode(GOOGLE_CLIENT_ID) ?>,
						callback: function (response) {
							$('#googleLoginMsgbox').html('');
							$.post('components/com_login/controleurs/router.php?task=googleLogin', { credential: response.credential }, function (data) {
								if (data.success) {
									document.location = 'index.php?option=com_dashboard';
								} else {
									$('#googleLoginMsgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> ' + (data.message || 'Connexion Google impossible.') + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
								}
							}, 'json').fail(function () {
								$('#googleLoginMsgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Impossible de contacter le serveur.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
							});
						}
					});
					google.accounts.id.renderButton(document.getElementById('googleLoginBtn'), { theme: 'outline', size: 'large', width: 300, text: 'signin_with' });
				}
				initGoogleLoginButton();
				<?php endif;?>
			});
		</script>