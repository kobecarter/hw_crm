<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="holidayForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Nom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="name" value="<?php if (isset($holiday)) echo $holiday->getName(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date début<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="start_date" value="<?php if (isset($holiday)) echo normaldate($holiday->getStartDate()); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date fin<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="end_date" value="<?php if (isset($holiday)) echo normaldate($holiday->getEndDate()); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remarque" class="form-control"><?php if (isset($holiday)) echo $holiday->getRemarque(); ?></textarea>
			</div>
		</div>


		<?php if (isset($holiday)) : ?>
			<input type="hidden" name="id" value="<?php echo $holiday->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		// envoi du formulaire en ajax
		$('form#holidayForm').ajaxForm({
			beforeSubmit: function() {
				$("#holidayForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse)
				$("#holidayForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Jour férié ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Jour férié modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#holidayForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						document.location = "index.php?option=com_holiday";
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#holidayForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#holidayForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
	})
</script>