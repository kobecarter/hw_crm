<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="reclamationForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Client</label>
				<select class="select" name="id_client">
					<?php foreach($clients as $client):?>
						<option value="<?php echo $client->getId() ?>" <?php if(isset($reclamation) && $reclamation->getClient()->getId() == $client->getId()) echo "selected"; ?>><?php echo $client->getNom().' '.$client->getPrenom() ?></option>
					<?php endforeach; ?>
			</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label>Departement</label>
				<select class="select" name="department">
					<option value="">Sélectionner</option>
					<option value="Support" <?php if(isset($reclamation) && $reclamation->getDepartment() == "Support") echo "selected"; ?>>Support</option>
					<option value="Billing" <?php if(isset($reclamation) && $reclamation->getDepartment() == "Billing") echo "selected"; ?>>Billing</option>
					<option value="Sales" <?php if(isset($reclamation) && $reclamation->getDepartment() == "Sales") echo "selected"; ?>>Sales</option>
					<option value="Abuse" <?php if(isset($reclamation) && $reclamation->getDepartment() == "Abuse") echo "selected"; ?>>Abuse</option>
			</select>
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Sujet</label>
				<input type="text" class="form-control" name="sujet" value="<?php if(isset($reclamation)) echo $reclamation->getSujet(); ?>">
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Message</label>
				<textarea name="message" class="form-control"><?php if(isset($reclamation)) echo $reclamation->getMessage(); ?></textarea>
			</div>
		</div>
		
		<!-- Toggle Switch -->
		<div class="col-md-6">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-3 toggle-switch-content ml-0">
					<span class="d-block text-dark">Traité</span>
				</span>
				<span class="col-4 col-sm-1">
					<input type="checkbox" name="etat" class="toggle-switch-input" <?php if(isset($reclamation) && $reclamation->isProcess()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
				
		<?php if(isset($reclamation)): ?>
			<input type="hidden" name="id" value="<?php echo $reclamation->getId(); ?>">
		<?php endif; ?>

	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#reclamationForm').ajaxForm({
            beforeSubmit: function () {
                $("#reclamationForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#reclamationForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Reclamation ajoutée avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Reclamation modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#reclamationForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_reclamation";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#reclamationForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#reclamationForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
