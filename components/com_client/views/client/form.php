<!--<h4 class="card-title">Basic Info</h4>-->
<?php $pf = isset($prefillClient) && is_array($prefillClient) ? $prefillClient : array(); ?>
<form method="post" action="<?php echo $action; ?>" id="clientForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-12">
			<div class="client-form-top">
				<div class="client-form-avatar-wrap">
					<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
						<?php $photoLink = isset($client) && $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
						<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
						<input type="file" name="photo[]" id="edit_img">
						<span class="avatar-edit">
							<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
						</span>
					</label>
				</div>
				<?php if (!isset($client) && $submitName !== 'addclientrapide'): ?>
				<div id="iaDropzoneClient" class="ia-dropzone client-form-dropzone">
					<input type="file" accept="application/pdf" style="display:none;">
					<div class="ia-dropzone-text">
						<i class="fas fa-file-pdf"></i> Glissez-déposez une présentation (PDF) ici, ou cliquez pour la sélectionner<br>
						<small>Les informations du client et des prestations seront extraites automatiquement.</small>
					</div>
				</div>
				<input type="hidden" name="presentation_file" id="presentation_file" value="">
				<input type="hidden" name="presentation_extracted" id="presentation_extracted" value="">
				<?php endif; ?>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-id-card"></i> Informations générales</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Agence<span class="text-danger"> * </span></label>
				<select class="select" name="agence" required>
				    <option value="" selected disabled>Sélectionner</option>
				    <?php foreach($agences as $agence): ?>
				    <option value="<?php echo $agence->getId(); ?>" <?php if(isset($client) && $client->getAgence()->getId() == $agence->getId()) echo "selected"; ?>><?php echo $agence->getNom(); ?></option>
				    <?php endforeach; ?>
			</select>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Source</label>
				<?php
					$sourcesConnues = array('Social Media', 'HubSpot', 'Ads', 'Direct', 'Referral');
					$sourceActuelle = isset($client) ? $client->getSource() : '';
					$sourceHistorique = $sourceActuelle != '' && !in_array($sourceActuelle, $sourcesConnues);
				?>
				<select class="select" name="source">
				<option value="Social Media" <?php if($sourceActuelle == 'Social Media') echo "selected"; ?>>Social Media</option>
				<option value="HubSpot" <?php if($sourceActuelle == 'HubSpot') echo "selected"; ?>>HubSpot</option>
				<option value="Ads" <?php if($sourceActuelle == 'Ads') echo "selected"; ?>>Ads</option>
				<option value="Direct" <?php if($sourceActuelle == 'Direct') echo "selected"; ?>>Direct</option>
				<option value="Referral" <?php if($sourceActuelle == 'Referral') echo "selected"; ?>>Referral</option>
				<?php if ($sourceHistorique) : ?>
				<option value="<?php echo htmlspecialchars($sourceActuelle); ?>" selected><?php echo htmlspecialchars($sourceActuelle); ?> (historique)</option>
				<?php endif; ?>
			</select>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>titre<span class="text-danger"> * </span></label>
				<select class="select" name="titre" required>
				<option value="Mr" <?php if(isset($client) && $client->getTitre() == 'Mr') echo "selected"; ?>>Mr</option>
				<option value="Mme" <?php if(isset($client) && $client->getTitre() == 'Mme') echo "selected"; ?>>Mme</option>
				<option value="Mlle" <?php if(isset($client) && $client->getTitre() == 'Mlle') echo "selected"; ?>>Mlle</option>	
			</select>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Nom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="nom" value="<?php if(isset($client)) echo $client->getNom(); elseif(isset($pf['nom'])) echo htmlspecialchars($pf['nom']); ?>" required>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Prénom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="prenom" value="<?php if(isset($client)) echo $client->getPrenom(); elseif(isset($pf['prenom'])) echo htmlspecialchars($pf['prenom']); ?>" required>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Raison sociale<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="raison_social" value="<?php if(isset($client)) echo $client->getRaisonSocial(); elseif(isset($pf['raison_social'])) echo htmlspecialchars($pf['raison_social']); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Fonction<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="fonction" value="<?php if(isset($client)) echo $client->getFonction(); elseif(isset($pf['fonction'])) echo htmlspecialchars($pf['fonction']); ?>" required>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>ICE</label>
				<input type="text" class="form-control" name="ice" value="<?php if(isset($client)) echo $client->getICE(); ?>">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>RC</label>
				<input type="text" class="form-control" name="rc" value="<?php if(isset($client)) echo $client->getRc(); ?>">
			</div>
		</div>
		
		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-address-book"></i> Coordonnées</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Tél<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="tel" value="<?php if(isset($client)) echo $client->getTel(); elseif(isset($pf['tel'])) echo htmlspecialchars($pf['tel']); ?>" required>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Tél 2</label>
				<input type="text" class="form-control" name="tel2" value="<?php if(isset($client)) echo $client->getTel2(); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Tél 3</label>
				<input type="text" class="form-control" name="tel3" value="<?php if(isset($client)) echo $client->getTel3(); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>E-mail<span class="text-danger"> * </span></label>
				<input type="email" class="form-control" name="email_client" value="<?php if(isset($client)) echo $client->getEmail(); elseif(isset($pf['email'])) echo htmlspecialchars($pf['email']); ?>" required>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Mot de passe</label>
				<input type="password" class="form-control" name="password" value="">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Site internet</label>
				<div class="d-flex align-items-center" style="gap:10px;">
					<input type="url" class="form-control" id="site_web" name="site_web" placeholder="https://exemple.com" value="<?php if(isset($client)) echo htmlspecialchars($client->getSiteWeb()); ?>">
					<img id="siteLogoPreview" src="" alt="" style="height:38px;width:38px;object-fit:contain;border-radius:6px;display:<?php echo (isset($client) && $client->getSiteWeb()) ? 'block' : 'none'; ?>;">
				</div>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-map-marker-alt"></i> Adresse</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Adresse</label>
				<input type="text" class="form-control" name="adresse" value="<?php if(isset($client)) echo $client->getAdresse(); elseif(isset($pf['adresse'])) echo htmlspecialchars($pf['adresse']); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Adresse 2</label>
				<input type="text" class="form-control" name="adresse2" value="<?php if(isset($client)) echo $client->getAdresse2(); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Pays</label>
				<input type="text" class="form-control" name="pays" value="<?php if(isset($client)) echo $client->getPays(); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Région</label>
				<input type="text" class="form-control" name="region" value="<?php if(isset($client)) echo $client->getRegion(); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Ville</label>
				<input type="text" class="form-control" name="ville" value="<?php if(isset($client)) echo $client->getVille(); elseif(isset($pf['ville'])) echo htmlspecialchars($pf['ville']); ?>">
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Code postal</label>
				<input type="text" class="form-control" name="cp" value="<?php if(isset($client)) echo $client->getCP(); ?>">
			</div>
		</div>
		
	
		
		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-sliders-h"></i> Paramètres</div>
		</div>
		<!-- Toggle Switch -->
		<div class="col-md-6">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-3 toggle-switch-content ml-0">
					<span class="d-block text-dark">Client actif</span>
				</span>
				<span class="col-4 col-sm-1">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if(isset($client) ? $client->isActive() : true) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
				
		<?php if(isset($client)): ?>
			<input type="hidden" name="id" value="<?php echo $client->getId(); ?>">
		<?php endif; ?>
		<input type="hidden" name="id_agence" value="<?= isset($client) ? $client->getAgence()->getId() : $_SESSION['agence'] ?>">
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<?php if (!isset($client) && $submitName !== 'addclientrapide'): ?>
<!-- Confirm Create Devis Modal -->
<div id="dialog-confirm-devis" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Client créé avec succès</h5>
			</div>
			<div class="modal-body">
				<p>Voulez-vous créer un devis avec les prestations détectées dans le PDF ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id="confirmDevisIgnorer">Ignorer pour le moment</button>
				<button type="button" class="btn btn-primary" id="confirmDevisOui">Oui</button>
			</div>
		</div>
	</div>
</div>
<!-- /Confirm Create Devis Modal -->
<?php endif; ?>

<script>
    $(function () {

        // Aperçu logo à partir du domaine saisi dans "Site internet" : Clearbit d'abord (marques
        // connues), puis repli sur le favicon du site (Google) si Clearbit ne couvre pas ce domaine.
        function updateSiteLogoPreview() {
            var url = $('#site_web').val().trim();
            var domaine = url.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('/')[0];
            var $img = $('#siteLogoPreview');
            if (!domaine) {
                $img.hide().attr('src', '').removeData('fallbackTried');
                return;
            }
            $img.removeData('fallbackTried')
                .attr('data-fallback', 'https://www.google.com/s2/favicons?domain=' + domaine + '&sz=128')
                .attr('src', 'https://logo.clearbit.com/' + domaine)
                .show();
        }
        $('#site_web').on('blur', updateSiteLogoPreview);
        $('#siteLogoPreview').on('error', function () {
            var $img = $(this);
            if (!$img.data('fallbackTried')) {
                $img.data('fallbackTried', true);
                $img.attr('src', $img.attr('data-fallback'));
            } else {
                $img.hide();
            }
        });
        if ($('#site_web').val()) { updateSiteLogoPreview(); }

        <?php if (!isset($client) && $submitName !== 'addclientrapide'): ?>
        initIaDropzone({
            zoneSelector: '#iaDropzoneClient',
            context: 'client',
            onExtracted: function (response) {
                $('#presentation_file').val(response.fichier);
                $('#presentation_extracted').val(JSON.stringify(response.extracted));
                var c = response.extracted.client || {};
                if (c.prenom) $('[name=prenom]').val(c.prenom);
                if (c.nom) $('[name=nom]').val(c.nom);
                if (c.raison_social) $('[name=raison_social]').val(c.raison_social);
                if (c.fonction) $('[name=fonction]').val(c.fonction);
                if (c.email) $('[name=email_client]').val(c.email);
                if (c.tel) $('[name=tel]').val(c.tel);
                if (c.adresse) $('[name=adresse]').val(c.adresse);
                if (c.ville) $('[name=ville]').val(c.ville);
                if (c.pays) $('[name=pays]').val(c.pays);

                if (response.extracted.services && response.extracted.services.length) {
                    sessionStorage.setItem('ia_services', JSON.stringify(response.extracted.services));
                } else {
                    sessionStorage.removeItem('ia_services');
                }
                sessionStorage.setItem('ia_client_pays', c.pays || '');

                if (response.extracted.conditions && response.extracted.conditions.length) {
                    sessionStorage.setItem('ia_conditions', JSON.stringify(response.extracted.conditions));
                } else {
                    sessionStorage.removeItem('ia_conditions');
                }
            }
        });
        <?php endif; ?>

        // envoi du formulaire en ajax
        $('form#clientForm').ajaxForm({
            beforeSubmit: function () {
                $("#clientForm .submit").prop("disabled", true);
                $("#clientForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#clientForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Client ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Client modifié avec succès";
                }

                if (parseInt(theResponse) === 1) {

                    // test du formulaire ajout rapide sur devis
				    if ($("#clientForm .submit").attr("name") === "addclientrapide") {

				        var newClientId = String(theResponse).split('|')[1];
				        var newClientAgence = $("#clientForm [name=agence]").val();

				        // mettre à jour la liste des clients
				        var order = '';
				        $.post("components/com_client/controleurs/router.php?task=getClientSelect", order, function(theResponse2) {
				            $(".client-box").html(theResponse2);
				            $(document).trigger("iaClientCreated", [newClientId, newClientAgence]);
            			});

            			$("#dialog-client").modal('hide');

				    }else{
                    $('#clientForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                        var newClientId = String(theResponse).split('|')[1];
                        var newClientAgence = $("#clientForm [name=agence]").val();
                        var iaServices = sessionStorage.getItem('ia_services');

                        setTimeout(function () {
                            <?php if($_GET['task'] == 'edit') :?>
                                location.reload();
                            <?php else:?>
                                if (iaServices && newClientId) {
                                    $("#dialog-confirm-devis").modal('show');
                                    $("#confirmDevisOui").off('click').on('click', function () {
                                        $("#dialog-confirm-devis").modal('hide');
                                        ensureSessionAgence(newClientAgence, function () {
                                            document.location = "index.php?option=com_devis&task=add&id_client=" + newClientId;
                                        });
                                    });
                                    $("#confirmDevisIgnorer").off('click').on('click', function () {
                                        $("#dialog-confirm-devis").modal('hide');
                                        sessionStorage.removeItem('ia_services');
                                        document.location = "index.php?option=com_client";
                                    });
                                } else {
                                    document.location = "index.php?option=com_client";
                                }
                            <?php endif;?>
                        }, 1500)
				    }

                } else if(parseInt(theResponse) === 0) {
                    $('#clientForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    $('#clientForm .submit').prop('disabled', false);
                } else {
                    $('#clientForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    $('#clientForm .submit').prop('disabled', false);
                }
            }
        });
    })
</script>
