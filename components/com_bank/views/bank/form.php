<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="bankForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Raison sociale</label>
				<input type="text" class="form-control" name="raison_sociale" value="<?php if(isset($bank)) echo $bank->getRaisonSociale(); ?>" required>
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Siège social</label>
				<input type="text" class="form-control" name="siege_social" value="<?php if(isset($bank)) echo $bank->getSiegeSocial(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Numero registre du commerce</label>
				<input type="text" class="form-control" name="numero_registre_commerce" value="<?php if(isset($bank)) echo $bank->getNumeroRegistreCommerce(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>ICE</label>
				<input type="text" class="form-control" name="ice" value="<?php if(isset($bank)) echo $bank->getIce(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Banque</label>
				<input type="text" class="form-control" name="banque" value="<?php if(isset($bank)) echo $bank->getBanque(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Libellé du compte <small class="text-muted">(pour BANK STATEMENT)</small></label>
				<input type="text" class="form-control" name="label" placeholder="Ex: BCP Verse Concept, BMCE Convertible DH" value="<?php if(isset($bank)) echo $bank->getLabel(); ?>">
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Compte personnel <small class="text-muted">(exclu de BANK STATEMENT)</small></span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="exclu_rapprochement" class="toggle-switch-input" value="1" <?php if(isset($bank) && $bank->getExcluRapprochement()) echo "checked"; ?>>
					<span class="toggle-switch-label mt-3">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->

		<div class="col-md-3">
			<div class="form-group">
				<label>RIB</label>
				<input type="text" class="form-control" name="rib" value="<?php if(isset($bank)) echo $bank->getrib(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Code SWIFT Banque populaire</label>
				<input type="text" class="form-control" name="code_swift" value="<?php if(isset($bank)) echo $bank->getCodeSwift(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Numéro IBAN</label>
				<input type="text" class="form-control" name="iban_number" value="<?php if(isset($bank)) echo $bank->getIbanNumber(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Devise</label>
				<input type="text" class="form-control" name="currency" value="<?php if(isset($bank)) echo $bank->getCurrency(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Agence</label>
				<select class="select" name="agence">
					<option value="" selected disabled>Sélectionner</option>
					<?php foreach ($agences as $agence): ?>
					<option value="<?php echo $agence->getId(); ?>" <?php if(isset($bank) && $bank->getAgence() && $bank->getAgence()->getId() == $agence->getId()) echo "selected"; ?>><?php echo $agence->getNom(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<?php if(isset($bank)): ?>
		<input type="hidden" name="id" value="<?php echo $bank->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#bankForm').ajaxForm({
            beforeSubmit: function () {
                $("#bankForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#bankForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Banque ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Bank modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#bankForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_bank";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#bankForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#bankForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
