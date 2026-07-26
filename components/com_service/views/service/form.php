<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="serviceForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Catégorie</label>
				<select class="chosen-select" name="id_categorie">
				<?php foreach($categories as $categorie): ?>
					<?php $sl = (isset($service) && $service->getCategorie()->getId() == $categorie->getId()) ? "selected" : ""; ?>
					<option value="<?php echo $categorie->getId() ?>" <?php echo $sl; ?>><?php echo $categorie->getTitre(); ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" class="form-control" name="titre" value="<?php if(isset($service)) echo $service->getTitre(); ?>">
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Unité</label>
				<select class="chosen-select" name="unite">
				    <option value="">Sélectionner</option>
					<?php
				    $unities = getUnities()[isset($devis) ? $devis->getLangue() : $_SESSION['langue']];
				    foreach ($unities as $key=>  $value) : ?>
				    <?php $sl = isset($service) && $service->getUnite() == $key ? "selected" : ""; ?>
					<option value="<?php echo $key ?>" <?php echo $sl; ?>><?= $value ?></option>
					<?php endforeach; ?>
				</select>
				
				<!-- <input type="text" class="form-control" name="unite" value="<?php if(isset($service)) echo $service->getUnite(); ?>"> -->
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Prix</label>
				<input type="number" step="any" class="form-control" name="prix" value="<?php if(isset($service)) echo $service->getPrix(); ?>">
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Intervenants</label>
				<input type="text" class="form-control" name="intervenant" value="<?php if(isset($service)) echo $service->getIntervenant(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Facturation</label>
				<select class="chosen-select" name="facturation">
				    <option value="unique" <?php if(isset($service) && $service->getFacturation() == 'unique') echo "selected"; ?>>Unique</option>
				    <option value="periodique" <?php if(isset($service) && $service->getFacturation() == 'periodique') echo "selected"; ?>>Périodique</option>
				</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Ordre</label>
				<input type="number" class="form-control" name="ordre" value="<?php if(isset($service)) echo $service->getOrdre(); ?>">
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Description</label>
				<textarea name="description" id="description"><?php if (isset($service)) echo $service->getDescription(); ?></textarea>
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
					<span class="d-block text-dark">Service actif</span>
				</span>
				<span class="col-4 col-sm-1">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if(isset($service) && $service->isActive()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
				
		<?php if(isset($service)): ?>
		<input type="hidden" name="id" value="<?php echo $service->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#serviceForm').ajaxForm({
            beforeSubmit: function () {
                $("#serviceForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
                $("#serviceForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Service ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Service modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#serviceForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_service";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#serviceForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#serviceForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
