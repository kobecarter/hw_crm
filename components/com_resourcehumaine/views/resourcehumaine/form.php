<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="resourceHumaineForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-12">
			<div class="client-form-top">
				<div class="client-form-avatar-wrap">
					<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
						<?php $photoLink = isset($resourcehumaine) && $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
						<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
						<input type="file" name="photo[]" id="edit_img">
						<span class="avatar-edit">
							<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
						</span>
					</label>
				</div>
				<?php if (!isset($resourcehumaine)): ?>
				<div id="iaDropzoneEmployee" class="ia-dropzone client-form-dropzone">
					<input type="file" accept="application/pdf,image/jpeg,image/png,image/webp" style="display:none;">
					<div class="ia-dropzone-text">
						<i class="fa fa-id-card"></i> Glissez-déposez la CIN ou le CV du candidat (photo ou PDF) ici, ou cliquez pour la sélectionner<br>
						<small>Les informations d'identité seront extraites par l'IA et pré-remplies automatiquement ci-dessous.</small>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-id-badge"></i> Identité</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Profil<span class="text-danger"> * </span></label>
				<select class="select" name="id_profil" required>
					<option value="">Sélectionner</option>
					<?php foreach ($profils as $profil): ?>
						<option value="<?php echo $profil->getId(); ?>"
							<?php
							if (isset($resourcehumaine) && $resourcehumaine->getProfil()) {

								echo $resourcehumaine->getProfil()->getId() == $profil->getId() ?  "selected" : null;
							}
							?>><?php echo $profil->getProfil(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Agence<span class="text-danger"> * </span></label>
				<select class="select" name="id_agency" required>
					<option value="" disabled selected>Sélectionner</option>
					<?php foreach ($agencies as $key => $agency) : ?>
						<option value="<?= $agency->getId() ?>" <?php if (isset($resourcehumaine) && $resourcehumaine->getAgency()->getId() == $agency->getId()) echo "selected"; ?>><?= $agency->getNom() ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Matricule<span class="text-danger d-none"> * </span></label>
				<input type="text" class="form-control" name="reference" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getReference(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Référence pointage<span class="text-danger d-none"> * </span></label>
				<input type="text" class="form-control" name="reference_pointage" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getReferencePointage(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>CIN<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="cin" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getCin(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Prénom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="firstname" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getFirstName(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Nom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="lastname" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getLastName(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Status<span class="text-danger"> * </span></label>
				<select class="select" name="status" required>
					<option value="" disabled selected>Sélectionner</option>
					<option value="Titulaire" <?php if (isset($resourcehumaine) && $resourcehumaine->getStatus() == "Titulaire") echo "selected"; ?>>Titulaire</option>
					<option value="Stagaire" <?php if (isset($resourcehumaine) && $resourcehumaine->getStatus() == "Stagaire") echo "selected"; ?>>Stagaire</option>
					<option value="Periode De test" <?php if (isset($resourcehumaine) && $resourcehumaine->getStatus() == "Periode De test") echo "selected"; ?>>Periode De test</option>
				</select>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-address-book"></i> Coordonnées</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>E-mail<span class="text-danger"> * </span></label>
				<input type="email" class="form-control" name="email" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getEmail(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Password<span class="text-danger d-none"> * </span></label>
				<input type="password" class="form-control" name="password" value="">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Tél<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="phone" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getPhone(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Tél secondaire<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="second_phone" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getSecondPhone(); ?>" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label>Adresse<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="address" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getAddress(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Ville<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="city" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getCity(); ?>" required>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-briefcase"></i> Emploi</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Fonction<span class="text-danger"> * </span></label>
				<select class="select" name="function" required>
					<option value="" disabled selected>Sélectionner</option>
					<option value="Assistante de direction" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Assistante de direction") echo "selected"; ?>>Assistante de direction</option>
					<option value="Responsable administrative" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Responsable administrative") echo "selected"; ?>>Responsable administrative</option>
					<option value="Manager" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Manager") echo "selected"; ?>>Manager</option>
					<option value="Community manager" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Community manager") echo "selected"; ?>>Community manager</option>
					<option value="Graphic designer" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Graphic designer") echo "selected"; ?>>Graphic designer</option>
					<option value="Developpeur web" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Developpeur web") echo "selected"; ?>>Developpeur web</option>
					<option value="Developpeur mobile" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Developpeur mobile") echo "selected"; ?>>Developpeur mobile</option>
					<option value="ADS Manager" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "ADS Manager") echo "selected"; ?>>ADS Manager</option>
					<option value="Chef de projet" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Chef de projet") echo "selected"; ?>>Chef de projet</option>
					<option value="Commercial" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Commercial") echo "selected"; ?>>Commercial</option>
					<option value="Femme de ménage" <?php if (isset($resourcehumaine) && $resourcehumaine->getFunction() == "Femme de ménage") echo "selected"; ?>>Femme de ménage</option>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Source de prospection<span class="text-danger d-none"> * </span></label>
				<input type="text" class="form-control" name="prospecting_source" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getProspectingSource(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>RIB<span class="text-danger d-none"> * </span></label>
				<input type="text" class="form-control" name="rib" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getRib(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Numéro de CNSS<span class="text-danger d-none"> * </span></label>
				<input type="text" class="form-control" name="cnss_number" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getCnssNumber(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Salaire premier jour (DH)<span class="text-danger d-none"> * </span></label>
				<input type="number" step="0.01" min="0" class="form-control" name="salaire_initial" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getSalaireInitial(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Salaire actuel (DH)<span class="text-danger d-none"> * </span></label>
				<input type="number" step="0.01" min="0" class="form-control" name="salaire_actuel" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getSalaireActuel(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Commission de parrainage (MAD)<span class="text-muted"> — par client validé</span></label>
				<input type="number" step="0.01" min="0" class="form-control" name="commission_parrainage" value="<?php if (isset($resourcehumaine)) echo $resourcehumaine->getCommissionParrainage(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date debut<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="start_date" value="<?php if (isset($resourcehumaine)) {
																										echo normaldate($resourcehumaine->getStartDate());
																									} ?>" required>
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date de signature de contrat<span class="text-danger d-none"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="contract_signing_date" value="<?php if (isset($resourcehumaine)) {
																													echo normaldate($resourcehumaine->getContractSigningDate());
																												} ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date fin</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="end_date" value="<?php if (isset($resourcehumaine)) {
																														echo normaldate($resourcehumaine->getEndDate());
																													} ?>">
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<!-- Toggle Switch -->
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Activation</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if (isset($resourcehumaine) && $resourcehumaine->isActive()) echo "checked"; ?>>
					<span class="toggle-switch-label mt-3">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
			<!-- /Toggle Switch -->
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-align-left"></i> Informations complémentaires</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remark"><?php if (isset($resourcehumaine)) {
														echo $resourcehumaine->getRemark();
													} ?></textarea>
				<script type="text/javascript">
					CKEDITOR.replace('remark', {
						allowedContent: true,
						//allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
						filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
					});
				</script>
			</div>
		</div>

		<?php if (isset($resourcehumaine)): ?>
			<input type="hidden" name="id" value="<?php echo $resourcehumaine->getId(); ?>">
		<?php endif; ?>
	</div>

	<div class="form-section-title mt-3"><i class="fa fa-history"></i> Historique des périodes de travail</div>
	<div class="myResponsivTable mb-4">
		<table class="table table-stripped table-center table-hover table-workdates">
			<thead>
				<tr>
					<th>Date debut <span class="text-danger"> * </span></th>
					<th>Date de signature de contrat</th>
					<th>Date fin</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if (isset($resourcehumaine)) :
					if (sizeof($resourcehumaine->getWorkDates()) > 0) :
				?>
						<?php foreach ($resourcehumaine->getWorkDates() as $workdate) : ?>
							<tr>
								<td>
									<input type="date" name="workdate_start_date[]" value="<?php echo $workdate->getStartDate(); ?>" class="form-control" required>
								</td>
								<td>
									<input type="date" name="workdate_contract_signing_date[]" value="<?php echo $workdate->getContractSigningDate(); ?>" class="form-control">
								</td>
								<td>
									<input type="date" name="workdate_end_date[]" value="<?php echo $workdate->getEndDate(); ?>" class="form-control">
								</td>
								<td class="add-remove text-right">
									<input type="hidden" name="workdate_id[]" value="<?php echo $workdate->getId(); ?>" class="id-item-input">
									<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer la ligne" data-id="<?php echo $workdate->getId(); ?>"></i>
								</td>
							</tr>
						<?php endforeach;
					else :
						?>
						<tr>
							<td>
								<input type="date" name="workdate_start_date[]" value="" class="form-control" required>
							</td>
							<td>
								<input type="date" name="workdate_contract_signing_date[]" value="" class="form-control">
							</td>
							<td>
								<input type="date" name="workdate_end_date[]" value="" class="form-control">
							</td>
							<td class="add-remove text-right">
								<input type="hidden" name="workdate_id[]" value="0" class="id-item-input">
								<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer la ligne"></i>
							</td>
						</tr>
			<?php endif;
					else : ?>
						<tr>
							<td>
								<input type="date" name="workdate_start_date[]" value="" class="form-control" required>
							</td>
							<td>
								<input type="date" name="workdate_contract_signing_date[]" value="" class="form-control">
							</td>
							<td>
								<input type="date" name="workdate_end_date[]" value="" class="form-control">
							</td>
							<td class="add-remove text-right">
								<input type="hidden" name="workdate_id[]" value="0" class="id-item-input">
								<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer la ligne"></i>
							</td>
						</tr>
					<?php endif; ?>
			</tbody>
		</table>
		<div class="d-flex justify-content-end mt-4">
			<a href="javascript:void(0)" class="btn btn-success add-row"> <i class="fas fa-plus-circle"></i> Ajouter</a>
		</div>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		<?php if (!isset($resourcehumaine)): ?>
		initEmployeeIaDropzone({
			zoneSelector: '#iaDropzoneEmployee',
			onExtracted: function (c) {
				if (c.prenom) $('[name=firstname]').val(c.prenom);
				if (c.nom) $('[name=lastname]').val(c.nom);
				if (c.cin) $('[name=cin]').val(c.cin);
				if (c.adresse) $('[name=address]').val(c.adresse);
				if (c.ville) $('[name=city]').val(c.ville);
				if (c.email) $('[name=email]').val(c.email);
				if (c.tel) $('[name=phone]').val(c.tel);
				if (c.fonction) {
					var $fonctionSelect = $('select[name=function]');
					$fonctionSelect.find('option').each(function () {
						if ($(this).val().toLowerCase() === c.fonction.toLowerCase()) {
							$fonctionSelect.val($(this).val()).trigger('change');
						}
					});
				}
			}
		});
		<?php endif; ?>

		// envoi du formulaire en ajax
		$('form#resourceHumaineForm').ajaxForm({
			beforeSubmit: function() {
				$("#resourceHumaineForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse)
				$("#resourceHumaineForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Resource humaine ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Resource humaine modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#resourceHumaineForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
					    if ($(".submit").attr("name") === "edit") {
							location.reload();
						}else{
							document.location = "index.php?option=com_resourcehumaine";
						}
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#resourceHumaineForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#resourceHumaineForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});

		$(document).on("click", ".add-row", function() {
			var $btn = $(this);
			var order = '';
			$.post("components/com_resourcehumaine/controleurs/router.php?task=getRowWorkDate", order, function(theResponse) {
				console.log(theResponse)
				$(".table-workdates tbody").append(theResponse);
			})
		})

		$(document).on("click", ".remove-row", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			if (confirm("Etes-vous sure !")) {
				var order = 'id=' + id;
				$.post("components/com_resourcehumaine/controleurs/router.php?task=removeRowWorkDate", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {
						$btn.parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().remove()
						}, 1000);

						$('#factureForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('#factureForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				})
			}
		})
	})
</script>
