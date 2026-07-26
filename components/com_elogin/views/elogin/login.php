
		<!-- Main Wrapper -->
		<div class="main-wrapper login-body">
			<div class="login-wrapper">
				<div class="container">
				
					<img class="img-fluid logo-dark mb-2" src="images/config/<?php echo $config->getLogo(); ?>" alt="<?php echo $config->getNom(); ?>">
					<div class="loginbox">
						
						<div class="login-right">
							<div class="login-right-wrap">
								<h1>Se connecter</h1>
								<p class="account-subtitle">Veuillez entrer votre e-mail et mot de passe</p>
								
								<form method="post" id="eloginForm" action="<?php echo $siteURL; ?>components/com_elogin/controleurs/router.php?task=login">
									<div class="msgbox"></div>
									<div class="form-group">
										<label class="form-control-label">E-mail</label>
										<input type="text" name="email" class="form-control">
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
													<input type="checkbox" class="custom-control-input" id="cb1">
													<label class="custom-control-label" for="cb1">Se rappeler de moi</label>
												</div>
											</div>
										</div>
									</div>
									<button class="btn btn-lg btn-block btn-primary" type="submit">Se connecter</button>
								</form>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Main Wrapper -->