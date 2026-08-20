<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="contractForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Devis <span class="text-danger"> * </span></label>
				<select class="chosen-select form-select form-control" name="id_devis" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($devises as $devis) : ?>
						<?php $sl = isset($contract) && $contract->getDevis() && $contract->getDevis()->getId() == $devis->getId() ? "selected" : ""; ?>
						<?php
						 $contract_exists = contract::findByDevis($devis->getId(),$_SESSION['agence'],$_SESSION['langue']);
						 $ds = "";
						 if(isset($contract)){
							if(isset($contract_exists) && $contract_exists->getDevis()){
								if($contract_exists->getDevis()->getId() == $devis->getId() && $contract->getId() != $contract_exists->getId()){
									$ds = "disabled";
								}
							 }
						 }else if(isset($contract_exists) && $contract_exists->getDevis()){
							
							if($contract_exists->getDevis()->getId() == $devis->getId()){
								$ds = "disabled";
							}
						 }
						 ?>
						<option value="<?php echo $devis->getId() ?>" <?= $sl ?> <?=$ds?>><?php echo "#".$devis->getNumero()." (".$devis->getClient()->getNom().' '.$devis->getClient()->getPrenom().")"; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Titre <span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="titre" value="<?php if (isset($contract)) echo $contract->getTitre();?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Duration <span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="duration" value="<?php if (isset($contract)) echo $contract->getDuration();?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Garantie <span class="text-danger d-none"> * </span></label>
				<input type="text" class="form-control" name="garantie" value="<?php if (isset($contract)) echo $contract->getGarantie();?>">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Ville <span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="ville" value="<?php if (isset($contract)) echo $contract->getVille();?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Date <span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date" value="<?php if (isset($contract)) echo normaldate($contract->getDate());
																										else echo date('d/m/Y'); ?>" required>
				</div>
			</div>
		</div>
	
		<div class="col-md-4">
			<div class="form-group">
				<label>Date fin</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_fin" value="<?php if (isset($contract)) echo normaldate($contract->getDateFin()); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Tribunal <span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="tribunal" value="<?php if (isset($contract)) echo $contract->getTribunal();?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Nombre de paiement <span class="text-danger"> * </span></label>
				<input type="number" min="0" placeholder="0" class="form-control" id="inputNombreDePaiement" name="nombre_de_paiement" value="<?php if (isset($contract)) echo $contract->getNombreDePaiement();?>" required>
				<?php if (isset($contract) && $contract->getDevis()) :
					$nombreDetecte = contractGenerator::estimerNombreDePaiement($contract->getDevis()->getConditionPaiment());
				?>
				<small class="form-text text-muted">
					Détecté depuis les conditions de paiement du devis #<?= htmlspecialchars($contract->getDevis()->getNumero()) ?> : <strong><?= $nombreDetecte ?></strong>
					<?php if ($nombreDetecte != $contract->getNombreDePaiement()) : ?>
						— <a href="#" id="utiliserNombreDetecteBtn" data-valeur="<?= $nombreDetecte ?>">utiliser cette valeur</a>
					<?php endif; ?>
				</small>
				<?php endif; ?>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Status <span class="text-danger"> * </span></label>
				<select class="chosen-select form-select form-control" name="status" required>
				    <option value="1" <?php if (isset($contract) && $contract->getStatus() == 1) echo "selected"?>>En préparation</option>
					<option value="2" <?php if (isset($contract) && $contract->getStatus() == 2) echo "selected"?>>En attente de signature</option>
					<option value="3" <?php if (isset($contract) && $contract->getStatus() == 3) echo "selected"?>>Signé</option>
					<option value="4" <?php if (isset($contract) && $contract->getStatus() == 4) echo "selected"?>>Refusé</option>
				</select>
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Texte <span class="text-danger d-none"> * </span></label>
				<?php if (isset($contract) && $contract->getId()) : ?>
				<div class="d-flex align-items-center mb-2" style="gap:10px;flex-wrap:wrap;">
					<button type="button" class="btn btn-white btn-sm" id="regenererDepuisDevisBtn"><i class="fa fa-sync mr-1"></i> Régénérer depuis le devis</button>
					<span class="text-muted">ou</span>
					<input type="file" class="form-control form-control-sm d-inline-block" id="inputImportFichierTexte" accept=".doc,.docx" style="max-width:240px;">
					<button type="button" class="btn btn-white btn-sm" id="importerFichierTexteBtn"><i class="fa fa-file-import mr-1"></i> Importer un fichier Word</button>
					<span class="text-muted" style="font-size:0.8rem;">Remplace le texte ci-dessous - à valider avec "Enregistrer" en bas.</span>
				</div>
				<?php endif; ?>
				<textarea name="texte" id="texte"><?php if (isset($contract)) echo $contract->getTexte(); ?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('texte', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>	
		
		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Afficher la signature</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="show_signature" value="1" class="toggle-switch-input" <?php if (isset($contract) && $contract->isShowSignature()) echo "checked"; ?>>
					<span class="toggle-switch-label mr-auto mt-2">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->
		<div class="col-md-6">
			<div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Contrat (fichier)</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($contract) && $contract->getContratPDF() != '' ? "images/contracts/" . $contract->getContratPDF() : "assets/img/profiles/avatar-02.jpg"; ?>
							<?php $filename = isset($contract) ? explode('.',$contract->getContratPDF()) : '';?>
							<?php // doc/docx traités comme le pdf (icône générique) - ce ne sont pas des images, les
							// afficher via <img src="$photoLink"> comme le fait la branche "else" casserait l'aperçu. ?>
							<?php if(isset($filename[1]) && in_array(strtolower($filename[1]), array('pdf', 'doc', 'docx'))): ?>
								<a href="<?php echo $photoLink;?>" target="_blank"><img id="avatarImg" class="avatar-img" src="assets/img/pdf.png" alt="Profile Image"></a>
							<?php else: ?>
								<a href="<?php echo $photoLink;?>"data-fancybox><img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image"></a>
							<?php endif; ?>
							<input type="file" name="contrat_pdf[]" id="edit_img" accept=".pdf,.doc,.docx,image/*">
							
							<span class="avatar-edit" style="bottom:0;color:green;">
								<i data-feather="edit-2" class="fa fa-upload shadow-soft"></i>
							</span>
						</label>

						<?php if (isset($contract) && $contract->getContratPDF() != '') : ?>
						<a class="avatar-remove" style="top:0;color:red;left: 72px;" data-id="<?php echo $contract->getId(); ?>">
							<i class="fa fa-trash shadow-soft"></i>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
				
		<?php if(isset($contract)): ?>
		<input type="hidden" name="id" value="<?php echo $contract->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

		$(document).on("click", "#utiliserNombreDetecteBtn", function(e) {
			e.preventDefault();
			$('#inputNombreDePaiement').val($(this).data('valeur'));
		});

		$(document).on("click", "#regenererDepuisDevisBtn", function() {
			if (!confirm("Régénérer le texte depuis le devis ? Le contenu actuel de l'éditeur ci-dessous sera remplacé (rien n'est perdu tant que vous n'enregistrez pas le formulaire).")) {
				return;
			}
			var $btn = $(this).prop('disabled', true);
			var libelleInitial = $btn.html();
			$btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Régénération...');
			$.post('components/com_contract/controleurs/router.php?task=regenererCorpsDepuisDevis', {
				id: $('input[name=id]').val()
			}, function (response) {
				$btn.prop('disabled', false).html(libelleInitial);
				if (response.success) {
					CKEDITOR.instances.texte.setData(response.html);
				} else {
					alert(response.message || "Erreur lors de la régénération.");
				}
			}, 'json').fail(function () {
				$btn.prop('disabled', false).html(libelleInitial);
				alert("Erreur lors de la régénération.");
			});
		});

		$(document).on("click", "#importerFichierTexteBtn", function() {
			var fichier = document.getElementById('inputImportFichierTexte').files[0];
			if (!fichier) {
				alert("Choisissez d'abord un fichier Word (.doc ou .docx).");
				return;
			}
			var $btn = $(this).prop('disabled', true);
			var libelleInitial = $btn.html();
			$btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Import...');
			var formData = new FormData();
			formData.append('id', $('input[name=id]').val());
			formData.append('fichier_import', fichier);
			$.ajax({
				url: 'components/com_contract/controleurs/router.php?task=importerContratDepuisFichier',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function (response) {
					$btn.prop('disabled', false).html(libelleInitial);
					if (response.success) {
						CKEDITOR.instances.texte.setData(response.html);
						document.getElementById('inputImportFichierTexte').value = '';
					} else {
						alert(response.message || "Erreur lors de l'import.");
					}
				},
				error: function () {
					$btn.prop('disabled', false).html(libelleInitial);
					alert("Erreur lors de l'import.");
				}
			});
		});

		$(document).on("click", ".avatar-remove", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			if (confirm("Etes-vous sure !")) {
				var order = 'id=' + id;
				$.post("components/com_contract/controleurs/router.php?task=removeContratPDF", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {
						$('.avatar-img').attr('src','assets/img/profiles/avatar-02.jpg');
					} else {
						$('#contractForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				})
			}
		})
        // envoi du formulaire en ajax
        $('form#contractForm').ajaxForm({
            beforeSubmit: function () {
                $("#contractForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#contractForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Contrat ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Contrat modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#contractForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_contract";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#contractForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else if(parseInt(theResponse) === 3) {
                    $('#contractForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez uploader le contrat signé PDF<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#contractForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
