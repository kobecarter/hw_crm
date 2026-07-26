<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Paramètres générales</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Paramètres générales</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<!--<h4 class="card-title">Basic Info</h4>-->
						<form method="post" action="<?php echo $action; ?>" id="configForm" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-12 msgbox"></div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Nom</label>
										<input type="text" class="form-control" name="nom" value="<?php if (isset($config)) echo $config->getNom(); ?>">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Titre</label>
										<input type="text" class="form-control" name="titre" value="<?php if (isset($config)) echo $config->getTitre(); ?>">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label>Description</label>
										<input type="text" class="form-control" name="description" value="<?php if (isset($config)) echo $config->getDescription(); ?>">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>E-mail</label>
										<input type="email" class="form-control" name="email" value="<?php if (isset($config)) echo $config->getEmail(); ?>">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Téléphone</label>
										<input type="text" class="form-control" name="tel" value="<?php if (isset($config)) echo $config->getTel(); ?>">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Téléphone 2</label>
										<input type="text" class="form-control" name="tel2" value="<?php if (isset($config)) echo $config->getTel2(); ?>">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Fax</label>
										<input type="text" class="form-control" name="fax" value="<?php if (isset($config)) echo $config->getFax(); ?>">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label>Adresse</label>
										<input type="text" class="form-control" name="adresse" value="<?php if (isset($config)) echo $config->getAdresse(); ?>">
									</div>
								</div>
								<?php
								$social = array("facebook", "twitter", "youtube", "instagram", "pinterest", "linkedin", "snapshat", "tumblr", "viadeo");
								foreach ($social as $item) {
									$s = ucfirst($item);
									$get = "get" . $s;
									$v = $config->$get();
								?>
									<div class="col-md-3">
										<div class="form-group">
											<label>URL <?= $item; ?></label>
											<input type="text" class="form-control" name="<?= $item; ?>" value="<?= $v; ?>">
										</div>
									</div>
								<?php
								}
								?>

								<div class="col-md-3">
									<div class="form-group">
										<label>Analytics</label>
										<input type="text" class="form-control" name="analytic" value="<?php if (isset($config)) echo $config->getAnalytic(); ?>">
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label>Condition de paiment</label>
										<textarea name="condition_paiment" rows="2" class="form-control"><?php if (isset($config)) echo $config->getConditionPaiment(); ?></textarea>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="photo" class="col-sm-3 col-form-label input-label">Logo</label>
										<div class="col-sm-9">
											<div class="d-flex align-items-center">
												<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
													<?php $photoLink = isset($config) && $config->getLogo() != '' ? "images/config/" . $config->getLogo() : "assets/img/profiles/avatar-02.jpg"; ?>
													<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Logo">
													<input type="file" name="logo[]" id="edit_img">
													<span class="avatar-edit">
														<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
													</span>

													<a href="javascript:void(0)" class="avatar-remove deleteLogo">
														<i class="far fa-trash-alt"></i>
													</a>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="text-right mt-4">
								<button type="submit" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
							</div>
						</form>

						<script>
							$(function() {

								// envoi du formulaire en ajax
								$('form#configForm').ajaxForm({
									beforeSubmit: function() {
										$("#configForm .loading").css('display', 'inline-block');
									},
									success: function(theResponse) {
										$("#configForm .loading").fadeOut();
										$("html, body").animate({
											scrollTop: 0
										}, "slow");

										var msgsucces = "Mise à jour avec succès";
										if (parseInt(theResponse) === 1) {
											$('#configForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

											setTimeout(function() {
												document.location.reload();
											}, 1500)

										} else if (parseInt(theResponse) === 0) {
											$('#configForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
										} else {
											$('#configForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
										}
									}
								});

								$(".deleteLogo").click(function(event) {
									event.preventDefault();
									if (confirm("Etes-vous sure ?")) {
										var order = '';
										$.post("components/com_config/controleurs/router.php?task=deleteLogo", order, function(theResponse) {
											if (parseInt(theResponse) == 1) {
												$("#avatarImg").attr("src", "assets/img/profiles/avatar-02.jpg");
												//$("#logo").remove();
											} else {
												$('#configForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
											}
										});
									}
								});
							})
						</script>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>