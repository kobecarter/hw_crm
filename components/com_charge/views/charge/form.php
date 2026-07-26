<style>
	.chosen-container{
		width: 100% !important;
	}
</style>
<form method="post" action="<?php echo $action; ?>" id="chargeForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Payé par</label>
				<select class="select" name="paid_by">
					<option value="">Sélectionner</option>
					<?php foreach($users as $key=>$value) :?>
					    <option value="<?=$value->getId()?>" <?= (isset($charge) && $charge->getPaidBy()->getId() == $value->getId()) ? 'selected' : '' ?>><?=$value->getNom()." ".$value->getPrenom()?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" autocomplete="off" list="data-list-titles" class="form-control" name="titre" value="<?php if(isset($charge)) echo $charge->getTitre(); ?>">
				<datalist id="data-list-titles">
					<?php foreach(chargesTitles() as $key=>$value) :?>
						<option value="<?=$value?>"><?=$value?></option>
					<?php endforeach;?>
				</datalist>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Date charge</label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date_charge" value="<?php if(isset($charge)) echo normaldate($charge->getDateCharge()); else echo date('d/m/Y'); ?>">
				</div>	
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Type</label>
				<select class="chosen-select" name="type">
					<option value="fixe" <?php if(isset($charge) && $charge->getType() == 'fixe') echo "selected"; ?>>Charge fixe</option>
					<option value="variable" <?php if(isset($charge) && $charge->getType() == 'variable') echo "selected"; ?>>Charge variable</option>
					<option value="hors_hw" <?php if(isset($charge) && $charge->getType() == 'hors_hw') echo "selected"; ?>>Charge Hors Hello World</option>
				</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Montant</label>
				<input type="number" step="any" class="form-control" name="total" value="<?php if(isset($charge)) echo $charge->getTotal(); ?>">
			</div>
		</div>
		
		<div class="col-md-2">
			<div class="form-group">
				<label>Devise</label>
				<select class="select" name="devise">
					<option value="DH" <?php if (isset($charge) && $charge->getDevise() == 'DH') echo "selected"; ?>>MAD (DH)</option>
					<option value="€" <?php if (isset($charge) && $charge->getDevise() == '€') echo "selected"; ?>>Euro (€)</option>
					<option value="$" <?php if (isset($charge) && $charge->getDevise() == '$') echo "selected"; ?>>Dollar ($)</option>
					<option value="£" <?php if (isset($charge) && $charge->getDevise() == '£') echo "selected"; ?>>Pound (£)</option>
					<option value="AED" <?php if (isset($charge) && $charge->getDevise() == 'AED') echo "selected"; ?>>AED (DH)</option>
				</select>
			</div>
		</div>
		
		<div class="col-md-2">
			<div class="form-group">
				<label>Date paiement</label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date_payment" value="<?php if(isset($charge)) echo normaldate($charge->getDatePayment()); else echo date('d/m/Y'); ?>">
				</div>	
			</div>
		</div>
		
		<div class="col-md-2">
			<div class="form-group">
				<label>Mode paiement</label>
				<select class="chosen-select" name="mode_payment">
					<option value="espece" <?php if(isset($charge) && $charge->getModePayment() == 'espece') echo "selected"; ?>>Espèce</option>
					<option value="cheque" <?php if(isset($charge) && $charge->getModePayment() == 'cheque') echo "selected"; ?>>Chèque</option>
					<option value="virement" <?php if(isset($charge) && $charge->getModePayment() == 'virement') echo "selected"; ?>>Virement</option>
					<option value="paiement_en_ligne" <?php if(isset($charge) && $charge->getModePayment() == 'paiement_en_ligne') echo "selected"; ?>>Paiement en ligne</option>
				</select>
			</div>
		</div>
		
		
		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Charge payée</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="paid" class="toggle-switch-input" <?php if(isset($charge) && $charge->isPaid()) echo "checked"; ?>>
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
					<span class="d-block text-dark">Facturation</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="facture" class="toggle-switch-input" <?php if(isset($charge) && $charge->hasFacture()) echo "checked"; ?>>
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
					<span class="d-block text-dark">Rembourssé</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="refunded" class="toggle-switch-input" <?php if(isset($charge) && $charge->isRefunded()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
		
		<div class="col-md-6">
			<div class="form-group">
				<label>Description</label>
				<textarea name="description" class="form-control"><?php if(isset($charge)) echo $charge->getDescription(); ?></textarea>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($charge) && $charge->getPhoto() != '' ? "images/charges/" . $charge->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
							<?php // test document pdf 
							if(isset($charge) && fileExtension($charge->getPhoto()) == 'pdf') $photoLink = "assets/img/pdf.png";
							?>
							<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
							<input type="file" name="photo[]" id="edit_img">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>
							<?php if(isset($charge) && $charge->getPhoto() != ''): ?>
							<a href="images/charges/<?php echo $charge->getPhoto(); ?>" class="avatar-edit" style="bottom: 40px;" data-fancybox>
								<i class="fa fa-file"></i>
							</a>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="id_agence" value="<?= isset($charge) ? $charge->getAgence()->getId() : $_SESSION['agence']?>">
		<input type="hidden" name="id_user" value="<?= isset($charge) ? $charge->getUser()->getId() : $_SESSION['user']->getId() ?>">
		<?php if(isset($charge)): ?>
			<input type="hidden" name="id" value="<?php echo $charge->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#chargeForm').ajaxForm({
            beforeSubmit: function () {
                $("#chargeForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
                $("#chargeForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Charge ajoutée avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Charge modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#chargeForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				
                    <?php if (isset($charge)) : ?>
						setTimeout(function() {
							document.location.reload();
						}, 1500)
					<?php else : ?>
						setTimeout(function() {
							document.location = "index.php?option=com_charge";
						}, 1500)
					<?php endif; ?>
					
                } else if(parseInt(theResponse) === 0) {
                    $('#chargeForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#chargeForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
		
		var msgsucces = "Charge supprimée avec succès";
	
		$(document).on( "click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_charge/controleurs/router.php?task=deleteCharge", order, function (theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().addClass("table-danger");
						setTimeout(function () {
							$btn.parent().parent().remove()
						}, 1000);

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
					else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})	
    })
</script>


