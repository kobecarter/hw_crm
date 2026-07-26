<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="clientForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-12">
			<div class="form-group">
				<div class="col-sm-12 mb-5">
					<div class="d-flex align-items-center justify-content-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($client) && $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
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
		
		<?php if(isset($client)): ?>
		<div class="col-md-3">
			<div class="form-group">
				<label>Agence<span class="text-danger"> * </span></label>
				<select class="select" name="agence" required>
				    <?php foreach($agences as $agence): ?>
				    <option value="<?php echo $agence->getId(); ?>" <?php if(isset($client) && $client->getAgence()->getId() == $agence->getId()) echo "selected"; ?>><?php echo $agence->getNom(); ?></option>
				    <?php endforeach; ?>
			</select>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Source</label>
				<select class="select" name="source">
				<option value="Référencement" <?php if(isset($client) && $client->getSource() == 'Référencement') echo "selected"; ?>>Référencement</option>
				<option value="Réseaux sociaux" <?php if(isset($client) && $client->getSource() == 'Réseaux sociaux') echo "selected"; ?>>Réseaux sociaux</option>
				<option value="Compagne Ads" <?php if(isset($client) && $client->getSource() == 'Compagne Ads') echo "selected"; ?>>Compagne Ads</option>
				<option value="Recommandation" <?php if(isset($client) && $client->getSource() == 'Recommandation') echo "selected"; ?>>Recommandation</option>
				<option value="Autres" <?php if(isset($client) && $client->getSource() == 'Autres') echo "selected"; ?>>Autres</option>
			</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>titre<span class="text-danger"> * </span></label>
				<select class="select" name="titre" required>
				<option value="Mr" <?php if(isset($client) && $client->getTitre() == 'Mr') echo "selected"; ?>>Mr</option>
				<option value="Mme" <?php if(isset($client) && $client->getTitre() == 'Mme') echo "selected"; ?>>Mme</option>
				<option value="Mlle" <?php if(isset($client) && $client->getTitre() == 'Mlle') echo "selected"; ?>>Mlle</option>	
			</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Nom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="nom" value="<?php if(isset($client)) echo $client->getNom(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Prénom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="prenom" value="<?php if(isset($client)) echo $client->getPrenom(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Raison sociale<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="raison_social" value="<?php if(isset($client)) echo $client->getRaisonSocial(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Fonction<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="fonction" value="<?php if(isset($client)) echo $client->getFonction(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>ICE</label>
				<input type="text" class="form-control" name="ice" value="<?php if(isset($client)) echo $client->getICE(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>RC</label>
				<input type="text" class="form-control" name="rc" value="<?php if(isset($client)) echo $client->getRc(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Tél<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="tel" value="<?php if(isset($client)) echo $client->getTel(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Tél 2</label>
				<input type="text" class="form-control" name="tel2" value="<?php if(isset($client)) echo $client->getTel2(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Tél 3</label>
				<input type="text" class="form-control" name="tel3" value="<?php if(isset($client)) echo $client->getTel3(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>E-mail<span class="text-danger"> * </span></label>
				<input type="email" class="form-control" name="email_client" value="<?php if(isset($client)) echo $client->getEmail(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Mot de passe</label>
				<input type="password" class="form-control" name="password" value="">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Adresse</label>
				<input type="text" class="form-control" name="adresse" value="<?php if(isset($client)) echo $client->getAdresse(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Adresse 2</label>
				<input type="text" class="form-control" name="adresse2" value="<?php if(isset($client)) echo $client->getAdresse2(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Pays</label>
				<input type="text" class="form-control" name="pays" value="<?php if(isset($client)) echo $client->getPays(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Région</label>
				<input type="text" class="form-control" name="region" value="<?php if(isset($client)) echo $client->getRegion(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Ville</label>
				<input type="text" class="form-control" name="ville" value="<?php if(isset($client)) echo $client->getVille(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Code postal</label>
				<input type="text" class="form-control" name="cp" value="<?php if(isset($client)) echo $client->getCP(); ?>">
			</div>
		</div>
		
	
		
		<!-- Toggle Switch -->
		<div class="col-md-6">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-3 toggle-switch-content ml-0">
					<span class="d-block text-dark">Client actif</span>
				</span>
				<span class="col-4 col-sm-1">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if(isset($client) && $client->isActive()) echo "checked"; ?>>
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

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#clientForm').ajaxForm({
            beforeSubmit: function () {
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
				        
				        // mettre à jour la liste des clients
				        var order = '';
				        $.post("components/com_client/controleurs/router.php?task=getClientSelect", order, function(theResponse2) {
				            $(".client-box").html(theResponse2);
            			});
            			
            			$("#dialog-client").modal('hide');
            			
				    }else{
                    $('#clientForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                        setTimeout(function () {
                            <?php if($_GET['task'] == 'edit') :?>
                                location.reload();
                            <?php else:?>
                                document.location = "index.php?option=com_client";
                            <?php endif;?>
                        }, 1500)
				    }
					
                } else if(parseInt(theResponse) === 0) {
                    $('#clientForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#clientForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
