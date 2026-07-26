<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="categorieForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" class="form-control" name="titre" value="<?php if(isset($categorie)) echo $categorie->getTitre(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Ordre</label>
				<input type="number" class="form-control" name="ordre" value="<?php if(isset($categorie)) echo $categorie->getOrdre(); ?>">
			</div>
		</div>
		
		<!-- Toggle Switch -->
		<div class="col-md-12">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-3 toggle-switch-content ml-0">
					<span class="d-block text-dark">Catégorie active</span>
				</span>
				<span class="col-4 col-sm-1">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if(isset($categorie) && $categorie->isActive()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
				
		<?php if(isset($categorie)): ?>
		<input type="hidden" name="id" value="<?php echo $categorie->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#categorieForm').ajaxForm({
            beforeSubmit: function () {
                $("#categorieForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
                $("#categorieForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Catégorie ajoutée avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Catégorie modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#categorieForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_categorie";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#categorieForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#categorieForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
