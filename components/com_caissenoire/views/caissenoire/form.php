<form method="post" action="<?php echo $action; ?>" id="caissenoireForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Payé par<span class="text-danger"> * </span></label>
				<select class="form-control" name="id_utilisateur" required>
					<option value="">Sélectionner</option>
					<?php foreach (CAISSENOIRE_USERS_AUTORISES as $idUtilisateurAutorise) : $utilisateurOption = user::find($idUtilisateurAutorise); ?>
					<option value="<?= $utilisateurOption->getId() ?>" <?php if (isset($caissenoire) && $caissenoire->getUtilisateur()->getId() == $utilisateurOption->getId()) echo 'selected'; ?>><?= $utilisateurOption->getPrenom() . ' ' . $utilisateurOption->getNom() ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Titre<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="titre" value="<?php if (isset($caissenoire)) echo htmlspecialchars($caissenoire->getTitre()); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Montant (DH)<span class="text-danger"> * </span></label>
				<input type="number" step="0.01" class="form-control" name="montant" value="<?php if (isset($caissenoire)) echo $caissenoire->getMontant(); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<input type="text" class="form-control datetimepicker" name="date_charge" value="<?php if (isset($caissenoire)) echo normaldate($caissenoire->getDateCharge()); else echo date('d/m/Y'); ?>" required>
			</div>
		</div>

		<div class="col-md-8">
			<div class="form-group">
				<label>Description</label>
				<input type="text" class="form-control" name="description" value="<?php if (isset($caissenoire)) echo htmlspecialchars($caissenoire->getDescription()); ?>">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Justificatif</label>
				<input type="file" name="justificatif[]" class="form-control">
				<?php if (isset($caissenoire) && $caissenoire->getJustificatif()) : ?>
				<small class="text-muted d-block mt-1">Fichier actuel : <a href="images/caissenoire/<?= $caissenoire->getJustificatif() ?>" target="_blank"><?= $caissenoire->getJustificatif() ?></a></small>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<div class="custom-control custom-checkbox mt-4">
					<input type="checkbox" class="custom-control-input" id="caissenoireRefunded" name="refunded" value="1" <?php if (isset($caissenoire) && $caissenoire->isRefunded()) echo 'checked'; ?>>
					<label class="custom-control-label" for="caissenoireRefunded">Remboursé</label>
				</div>
			</div>
		</div>

		<div class="col-md-4" id="caissenoireDateRemboursementBloc" style="<?php if (!isset($caissenoire) || !$caissenoire->isRefunded()) echo 'display:none;'; ?>">
			<div class="form-group">
				<label>Date de remboursement</label>
				<input type="text" class="form-control datetimepicker" name="date_remboursement" value="<?php if (isset($caissenoire) && $caissenoire->getDateRemboursement()) echo normaldate($caissenoire->getDateRemboursement()); ?>">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea class="form-control" name="remarque" rows="2"><?php if (isset($caissenoire)) echo htmlspecialchars($caissenoire->getRemarque()); ?></textarea>
			</div>
		</div>

		<?php if (isset($caissenoire)) : ?>
		<input type="hidden" name="id" value="<?= $caissenoire->getId() ?>">
		<?php endif; ?>

		<div class="col-md-12 mt-3">
			<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading" style="display:none;"></span> <?php echo $submitValue; ?></button>
			<a href="index.php?option=com_caissenoire" class="btn btn-white">Annuler</a>
		</div>
	</div>
</form>

<script>
$(function () {
	$('#caissenoireRefunded').on('change', function () {
		$('#caissenoireDateRemboursementBloc').toggle($(this).is(':checked'));
	});

	$('form#caissenoireForm').ajaxForm({
		beforeSubmit: function () {
			$("#caissenoireForm .submit").prop("disabled", true);
			$("#caissenoireForm .loading").css('display', 'inline-block');
		},
		success: function (theResponse) {
			$("#caissenoireForm .loading").fadeOut();
			$("html, body").animate({ scrollTop: 0 }, "slow");
			if (parseInt(theResponse) === 1) {
				$('#caissenoireForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès!</strong> Entrée enregistrée avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				setTimeout(function () {
					document.location = "index.php?option=com_caissenoire";
				}, 1200);
			} else if (parseInt(theResponse) === 0) {
				$('#caissenoireForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				$('#caissenoireForm .submit').prop('disabled', false);
			} else {
				$('#caissenoireForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Erreur lors de l\'exécution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				$('#caissenoireForm .submit').prop('disabled', false);
			}
		}
	});
});
</script>
