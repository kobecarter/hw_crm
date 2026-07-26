<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="realisationForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>


		<div class="col-md-6 p-0">
			<div class="col-12">
				<div class="form-group">
					<label>Titre</label>
					<input type="text" class="form-control" name="titre" value="<?php if (isset($realisation)) echo $realisation->getTitre(); ?>">
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label>Ordre</label>
					<input type="number" class="form-control" name="ordre" value="<?php if (isset($realisation)) echo $realisation->getOrdre(); ?>">
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label>URL du projet</label>
					<input type="text" class="form-control" name="url_project" value="<?php if (isset($realisation)) echo $realisation->getUrlProject(); ?>">
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="" for="edit_img">
							<?php $photoLink = isset($realisation) && $realisation->getPhoto() != '' ? "images/realisation/" . $realisation->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
							<div id="avatarImg" class="avatar-img rounded" style="height:10.5rem;background-image:url('<?php echo $photoLink; ?>');background-size:cover;background-position:center;"></div>
							<input type="file" name="photo[]" id="edit_img" style="opacity: 0;">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12">
			<div class="form-group">
				<label>Extrait</label>
				<textarea class="form-control" name="extrait" rows="3"><?php if (isset($realisation)) echo $realisation->getExtrait(); ?></textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Texte</label>
				<textarea name="texte" id="description"><?php if (isset($realisation)) echo $realisation->getTexte(); ?></textarea>
				<script type="text/javascript">
					CKEDITOR.replace('description', {
						allowedContent: true,
						//allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
						filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
					});
				</script>
			</div>
		</div>


		<?php if (isset($realisation)) : ?>
			<input type="hidden" name="id" value="<?php echo $realisation->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		// envoi du formulaire en ajax
		$('form#realisationForm').ajaxForm({
			beforeSubmit: function() {
				$("#realisationForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(JSON.stringify(theResponse));
				$("#realisationForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Realisation ajoutée avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Realisation modifiée avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#realisationForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						document.location = "index.php?option=com_realisation";
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#realisationForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#realisationForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
	})
</script>