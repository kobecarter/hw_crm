<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="langueForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Nom</label>
				<input type="text" class="form-control" name="nom" value="<?php if(isset($lg)) echo $lg->getNom(); ?>" required>
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Code</label>
				<input type="text" class="form-control" name="code" value="<?php if(isset($lg)) echo $lg->getCode(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label for="photo" class="col-sm-6 col-form-label input-label">Drapeau</label>
				<div class="col-sm-6">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($lg) && $lg->getFlag() != '' ? "../images/langues/" . $lg->getFlag() : "assets/img/profiles/avatar-02.jpg"; ?>
							<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Drapeau">
							<input type="file" name="flag[]" id="edit_img">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>
						</label>
					</div>
				</div>
			</div>
		</div>	

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Langue active</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if(isset($lg) && $lg->isActif()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
		
		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Langue par défaut</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="defaut" class="toggle-switch-input" <?php if(isset($lg) && $lg->isDefault()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
				
		<?php if(isset($lg)): ?>
		<input type="hidden" name="id" value="<?php echo $lg->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#langueForm').ajaxForm({
            beforeSubmit: function () {
                $("#langueForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#langueForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Langue ajoutée avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Langue modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#langueForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_lang";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#langueForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#langueForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
