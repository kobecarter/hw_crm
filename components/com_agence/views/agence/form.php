<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="agenceForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Nom</label>
				<input type="text" class="form-control" name="nom" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getNom(); ?>" required>
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Email</label>
				<input type="email" class="form-control" name="email" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getEmail(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Raison social</label>
				<input type="text" class="form-control" name="raison_social" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getRaisonSocial(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Gérant</label>
				<input type="text" class="form-control" name="manager" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getManager(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>CIN</label>
				<input type="text" class="form-control" name="cin" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getCin(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Tel</label>
				<input type="tel" class="form-control" name="tel" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getTel(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Tel2</label>
				<input type="tel" class="form-control" name="tel2" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getTel2(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Fax</label>
				<input type="tel" class="form-control" name="fax" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getFax(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Adresse</label>
				<input type="text" class="form-control" name="adresse" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getAdresse(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Ville</label>
				<input type="text" class="form-control" name="ville" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getVille(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Fonction</label>
				<input type="text" class="form-control" name="fonction" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getFonction(); ?>">
			</div>
		</div>
	
		<div class="col-md-3">
			<div class="form-group">
				<label>Numéro d'incrément de facture</label>
				<input type="number" class="form-control" name="numero_increment_facture" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getNumeroIncrementFacture(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Numéro d'incrément de devis</label>
				<input type="number" class="form-control" name="numero_increment_devis" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getNumeroIncrementDevis(); ?>">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Condition de paiement</label>
				<textarea name="condition_de_paiement" id="condition_de_paiement"><?php if (isset($agenceToEdit)) echo $agenceToEdit->getConditionDePaiement(); ?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('condition_de_paiement', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Conditions</label>
				<textarea name="conditions" id="conditions"><?php if (isset($agenceToEdit)) echo $agenceToEdit->getConditions(); ?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('conditions', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Information</label>
				<textarea name="information" id="information"><?php if (isset($agenceToEdit)) echo $agenceToEdit->getInformation(); ?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('information', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>TVA(%)</label>
				<input type="number" class="form-control" name="tva" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getTva(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Site web </label>
				<input type="text" class="form-control" name="website" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getWebsite(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Couleur </label>
				<input type="color" class="form-control" name="color" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getColor(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>IF</label>
				<input type="text" class="form-control" name="if" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getIf(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>TP</label>
				<input type="text" class="form-control" name="tp" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getTp(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>RC</label>
				<input type="text" class="form-control" name="rc" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getRc(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>ICE</label>
				<input type="text" class="form-control" name="ice" value="<?php if(isset($agenceToEdit)) echo $agenceToEdit->getIce(); ?>">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="logo" class="col-sm-3 col-form-label input-label">Logo</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_logo">
							<?php $photoLink = isset($agenceToEdit) && $agenceToEdit->getLogo() != '' ? "images/agences/" . $agenceToEdit->getLogo() : "assets/img/profiles/avatar-02.jpg"; ?>
							<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Logo">
							<input type="file" accept="image/*" name="logo[]" id="edit_logo">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>

							<a href="javascript:void(0)" class="avatar-remove deleteLogo"  data-id="<?=isset($agenceToEdit) ? $agenceToEdit->getId() : "new"?>">
								<i class="far fa-trash-alt"></i>
							</a>
						</label>
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label for="signature" class="col-sm-3 col-form-label input-label">Signature</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl cover-signature m-0" for="edit_signature">
							<?php $photoLink = isset($agenceToEdit) && $agenceToEdit->getSignature() != '' ? "images/agences/" . $agenceToEdit->getSignature() : "assets/img/profiles/avatar-02.jpg"; ?>
							<img id="avatarImgSignature" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Signature">
							<input type="file" accept="image/*" name="signature[]" id="edit_signature">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>

							<a href="javascript:void(0)" class="avatar-remove deleteSignature"  data-id="<?=isset($agenceToEdit) ? $agenceToEdit->getId() : "new"?>">
								<i class="far fa-trash-alt"></i>
							</a>
						</label>
					</div>
				</div>
			</div>
		</div>
				
		<?php if(isset($agenceToEdit)): ?>
		<input type="hidden" name="id" value="<?php echo $agenceToEdit->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#agenceForm').ajaxForm({
            beforeSubmit: function () {
                $("#agenceForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#agenceForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Agence ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Agence modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#agenceForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_agence";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#agenceForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#agenceForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });

		$(".deleteLogo").click(function(event) {
			event.preventDefault();
			if (confirm("Etes-vous sure ?")) {
				var id = $(this).attr("data-id");
				if(id=="new"){
					$("#avatarImg").attr("src", "assets/img/profiles/avatar-02.jpg");
					$("#edit_logo").val(null);
				}else{
					var order = 'id=' + id;
					$.post("components/com_agence/controleurs/router.php?task=deleteLogo", order, function(theResponse) {
						console.log(theResponse)
						if (parseInt(theResponse) == 1) {
							$("#avatarImg").attr("src", "assets/img/profiles/avatar-02.jpg");
							//$("#logo").remove();
						} else {
							$('#configForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
						}
					});
				}
				
			}
		});
		
		$(".deleteSignature").click(function(event) {
			event.preventDefault();
			if (confirm("Etes-vous sure ?")) {
				var id = $(this).attr("data-id");
				if(id=="new"){
					$("#avatarImgSignature").attr("src", "assets/img/profiles/avatar-02.jpg");
					$("#edit_signature").val(null);
				}else{
					var order = 'id=' + id;
					$.post("components/com_agence/controleurs/router.php?task=deleteSignature", order, function(theResponse) {
						console.log(theResponse)
						if (parseInt(theResponse) == 1) {
							$("#avatarImgSignature").attr("src", "assets/img/profiles/avatar-02.jpg");
							//$("#logo").remove();
						} else {
							$('#configForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
						}
					});
				}
				
			}
		});

		$("#edit_logo").on("change",function(){
			let self = $(this)
			let file = self.prop('files')[0]
			var reader = new FileReader();
			reader.onload = function (evt) {
				$("#avatarImg").attr("src",evt.target.result);	
			};
			reader.readAsDataURL(file);
		});
		
		$("#edit_signature").on("change",function(){
			let self = $(this)
			let file = self.prop('files')[0]
			var reader = new FileReader();
			reader.onload = function (evt) {
				$("#avatarImgSignature").attr("src",evt.target.result);	
			};
			reader.readAsDataURL(file);
		});
    })
</script>
