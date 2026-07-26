<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="relanceForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Client<span class="text-danger"> * </span></label>
				<select class="chosen-select" name="client" id="client_select" required>
					<option value="">Sélectionner</option>
					<?php foreach ($clients as $value) : ?>
						<?php 
							$sl = null;
							if((isset($relance) && $relance->getClient()->getId() == $value->getId()) || (isset($client) && $client->getId() == $value->getId()))
							{
								$sl = "selected";
							} ?>
						<option value="<?php echo $value->getId() ?>" <?php echo $sl; ?>><?php echo $value->getNom() . ' ' . $value->getPrenom() . ' - ' . $value->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Facture</label>
				<div class="facture_select">
				<select class="chosen-select" name="facture">
					<option value="">Sélectionner</option>
					<?php foreach ($factures as $facture) : ?>
						<?php if($facture->getReste() > 0): ?>
						<?php $sl = isset($relance) && $relance->getFacture()->getId() == $facture->getId() ? "selected" : ""; ?>
						<option value="<?php echo $facture->getId() ?>" <?php echo $sl; ?>><?php echo $facture->getNumero() . ' - ' . normaldate($facture->getDateFacture()) . ' ('. number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() .')'; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Type<span class="text-danger"> * </span></label>
				<?php
					$selected1 = null;
					if(isset($relance) && $relance->getType() == "Téléphone"){
						$selected1 = "selected";
					}
					$selected2 = null;
					if(isset($relance) && $relance->getType() == "Email"){
						$selected2 = "selected";
					}
					$selected3 = null;
					if(isset($relance) && $relance->getType() == "directement"){
						$selected3 = "selected";
					}
				?>
				<select class="select" name="type" required>
					<option value="">Sélectionner</option>
					<option value="Téléphone" <?php echo $selected1;?>>Téléphone</option>
					<option value="Email" <?php echo $selected2;?>>Email</option>
					<option value="directement" <?php echo $selected3;?>>directement</option>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
					<input type="date" class="form-control" name="date" value="<?php if (isset($relance)) echo $relance->getDate(); ?>" required>
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-4 col-sm-4 toggle-switch-content ml-0">
					<span class="d-block text-dark">Traitée</span>
				</span>
				<span class="col-8 col-sm-8">
					<input type="checkbox" name="traite" class="toggle-switch-input" <?php if(isset($relance) && $relance->isTraite()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto" style="float: left;">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->

		

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque<span class="text-danger"> * </span></label>
				<textarea name="remarque" class="form-control" required><?php if (isset($relance)) echo $relance->getRemarque(); ?></textarea>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($relance) && $relance->getPhoto() != '' ? "images/relances/" . $relance->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
							<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
							<input type="file" name="photo[]" id="edit_img">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<?php if (isset($relance)) : ?>
			<input type="hidden" name="id" value="<?php echo $relance->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		$(document).on("change", "#client_select", function() {
			var select = $(this);
			var id = select.val();
			var order = 'id=' + id;
			$.post("components/com_relance/controleurs/router.php?task=getFactureByClient", order, function(theResponse) {
				$(".facture_select").html(theResponse);
				$(".chosen-select").select2();
			});
		})

		// envoi du formulaire en ajax
		$('form#relanceForm').ajaxForm({
			beforeSubmit: function() {
				$("#relanceForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse)
				$("#relanceForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Rappel ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Rappel modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#relanceForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						let client_id = $("select[name='client']").val()
						document.location = "index.php?option=com_relance&task=client&id="+client_id;
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#relanceForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#relanceForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
	})
</script>