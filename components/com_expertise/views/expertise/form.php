<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="expertiseForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Parent</label>
				<select class="chosen-select col-12 p-0 m-0" name="id_parent">
					<option value="0">Selectionner une option</option>
					<?php foreach ($parents as $parent) : ?>
						<?php $sl = (isset($expertise) && $expertise->getParent()->getId() == $parent->getId()) ? "selected" : ""; ?>
						<option value="<?php echo $parent->getId() ?>" <?php echo $sl; ?>><?php echo $parent->getTitre(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" class="form-control" name="titre" value="<?php if (isset($expertise)) echo $expertise->getTitre(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Sous Titre</label>
				<input type="text" class="form-control" name="sous_titre" value="<?php if (isset($expertise)) echo $expertise->getSousTitre(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Ordre</label>
				<input type="number" class="form-control" name="ordre" value="<?php if (isset($expertise)) echo $expertise->getOrdre(); ?>">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Extrait</label>
				<textarea class="form-control" name="extrait" value=""><?php if (isset($expertise)) echo $expertise->getExtrait(); ?></textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="row form-group mx-auto">
				<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
				<div class="col-sm-9 h-50">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xxl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($expertise) && $expertise->getPhoto() != '' ? "images/expertises/" . $expertise->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
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



		<div class="col-md-12">
			<div class="form-group">
				<label>Texte</label>
				<textarea name="texte" id="description"><?php if (isset($expertise)) echo $expertise->getTexte(); ?></textarea>
				<script type="text/javascript">
					CKEDITOR.replace('description', {
						allowedContent: true,
						//allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
						filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
					});
				</script>
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-12">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-3 toggle-switch-content ml-0">
					<span class="d-block text-dark">Expertise actif</span>
				</span>
				<span class="col-4 col-sm-1">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if (isset($expertise) && $expertise->isActive()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->

		<?php if (isset($expertise)) : ?>
			<input type="hidden" name="id" value="<?php echo $expertise->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		// envoi du formulaire en ajax
		$('form#expertiseForm').ajaxForm({
			beforeSubmit: function() {
				$("#expertiseForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse);
				$("#expertiseForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Expertise ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Expertise modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#expertiseForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						document.location = "index.php?option=com_expertise";
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#expertiseForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#expertiseForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
	})
</script>