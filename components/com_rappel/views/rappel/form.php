<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="rappelForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Client</label>
				<select class="chosen-select" name="client">
					<?php foreach ($clients as $client) : ?>
						<?php $sl = isset($rappel) && $rappel->getClient()->getId() == $client->getId() ? "selected" : ""; ?>
						<option value="<?php echo $client->getId() ?>" <?php echo $sl; ?>><?php echo $client->getNom() . ' ' . $client->getPrenom() . ' - ' . $client->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

<div class="col-md-3">
	<div class="form-group">
		<label>Type</label>
		<select class="select" name="type">
			<option value="domaine" <?= (isset($rappel) && $rappel->getType() == 'domaine') ? 'selected' : '' ?>>Nom de domaine</option>
			<option value="hosting" <?= (isset($rappel) && $rappel->getType() == 'hosting') ? 'selected' : '' ?>>Hébergement</option>
			<option value="ssl" <?= (isset($rappel) && $rappel->getType() == 'ssl') ? 'selected' : '' ?>>Certificat SSL</option>
		</select>
	</div>
</div>


		<div class="col-md-3">
			<div class="form-group">
				<label>Domaine</label>
				<input type="text" class="form-control" name="domaine" value="<?php if (isset($rappel)) echo $rappel->getDomaine(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date expiration</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_expir" value="<?php if (isset($rappel)) echo normaldate($rappel->getDateExpir()); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label>Fournisseur(s)</label>
				<select class="chosen-select" name="fournisseurs[]" multiple="multiple" style="width:100%;">
					<?php $fournisseursActuelsRappel = isset($rappel) ? array_map(function ($f) { return $f->getId(); }, $rappel->getFournisseurs()) : array(); ?>
					<?php foreach ($fournisseurs as $fournisseurOption) : ?>
						<?php $nomFournisseurRappel = trim((string) $fournisseurOption->getRaisonSocial()) !== '' ? $fournisseurOption->getRaisonSocial() : trim($fournisseurOption->getPrenom() . ' ' . $fournisseurOption->getNom()); ?>
						<option value="<?php echo $fournisseurOption->getId() ?>" <?php echo in_array($fournisseurOption->getId(), $fournisseursActuelsRappel) ? 'selected' : ''; ?>><?php echo htmlspecialchars($nomFournisseurRappel); ?></option>
					<?php endforeach; ?>
				</select>
				<small class="text-muted">Ex: domaine chez un fournisseur, hébergement chez un autre.</small>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remarque" class="form-control"><?php if (isset($rappel)) echo $rappel->getRemarque(); ?></textarea>
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Archiver</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="archived" class="toggle-switch-input" <?php if (isset($rappel) && $rappel->isArchived()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->


		<?php if (isset($rappel)) : ?>
			<input type="hidden" name="id" value="<?php echo $rappel->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		// envoi du formulaire en ajax
		$('form#rappelForm').ajaxForm({
			beforeSubmit: function() {
				$("#rappelForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				$("#rappelForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Rappel ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Rappel modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#rappelForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						document.location = "index.php?option=com_rappel";
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#rappelForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#rappelForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
	})
</script>