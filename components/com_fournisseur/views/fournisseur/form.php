<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="fournisseurForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Source<span class="text-danger"> * </span></label>
				<select class="chosen-select form-select form-control" name="source" required>
				<option value="Site" <?php if(isset($fournisseur) && $fournisseur->getSource() == 'Site') echo "selected"; ?>>Inbound Société / Site Web</option>
				<option value="Social" <?php if(isset($fournisseur) && $fournisseur->getSource() == 'Social') echo "selected"; ?>>Réseaux Sociaux</option>
				<option value="Souaida" <?php if(isset($fournisseur) && $fournisseur->getSource() == 'Souaida') echo "selected"; ?>>Souaida</option>
				<option value="Event" <?php if(isset($fournisseur) && $fournisseur->getSource() == 'Event') echo "selected"; ?>>Événements & Sommets professionnels</option>
			</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Titre<span class="text-danger"> * </span></label>
				<select class="chosen-select form-select form-control" name="titre" required>
				<option value="Mr" <?php if(isset($fournisseur) && $fournisseur->getTitre() == 'Mr') echo "selected"; ?>>Mr</option>
				<option value="Mme" <?php if(isset($fournisseur) && $fournisseur->getTitre() == 'Mme') echo "selected"; ?>>Mme</option>
				<option value="Mlle" <?php if(isset($fournisseur) && $fournisseur->getTitre() == 'Mlle') echo "selected"; ?>>Mlle</option>	
			</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Nom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="nom" value="<?php if(isset($fournisseur)) echo $fournisseur->getNom(); ?>" >
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Prénom<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="prenom" value="<?php if(isset($fournisseur)) echo $fournisseur->getPrenom(); ?>" >
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Raison sociale<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="raison_social" value="<?php if(isset($fournisseur)) echo $fournisseur->getRaisonSocial(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>ICE</label>
				<input type="text" class="form-control" name="ice" value="<?php if(isset($fournisseur)) echo $fournisseur->getICE(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Tél<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="tel" value="<?php if(isset($fournisseur)) echo $fournisseur->getTel(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Tél 2</label>
				<input type="text" class="form-control" name="tel2" value="<?php if(isset($fournisseur)) echo $fournisseur->getTel2(); ?>">
			</div>
		</div>
		
		<div class="col-md-3 d-none">
			<div class="form-group">
				<label>Tél 3</label>
				<input type="text" class="form-control" name="tel3" value="<?php if(isset($fournisseur)) echo $fournisseur->getTel3(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>E-mail<span class="text-danger"> * </span></label>
				<input type="email" class="form-control" name="email" value="<?php if(isset($fournisseur)) echo $fournisseur->getEmail(); ?>" required>
			</div>
		</div>
		
		<!--<div class="col-md-3">
			<div class="form-group">
				<label>Mot de passe</label>
				<input type="password" class="form-control" name="password" value="">
			</div>
		</div>-->
		<div class="col-md-3">
			<div class="form-group">
				<label>Pays</label>
				<input type="text" class="form-control" name="pays" value="<?php if(isset($fournisseur)) echo $fournisseur->getPays(); ?>">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Adresse</label>
				<input type="text" class="form-control" name="adresse" value="<?php if(isset($fournisseur)) echo $fournisseur->getAdresse(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Adresse 2</label>
				<input type="text" class="form-control" name="adresse2" value="<?php if(isset($fournisseur)) echo $fournisseur->getAdresse2(); ?>">
			</div>
		</div>
		
		
		
		<div class="col-md-3 d-none">
			<div class="form-group">
				<label>Région</label>
				<input type="text" class="form-control" name="region" value="<?php if(isset($fournisseur)) echo $fournisseur->getRegion(); ?>">
			</div>
		</div>
		<div class="col-md-3">
    <div class="form-group">
        <label>Catégorie<span class="text-danger"> * </span></label>
        
        <select class="chosen-select form-select form-control" name="categorie" required>
            <option value="">-- Sélectionner --</option>
            <?php $categories = getCategorieFournisseur(); ?>
            <?php foreach($categories as $key => $categorie): ?>
            <option value="<?php echo $key; ?>" <?php if(isset($fournisseur) && $fournisseur->getCategorie() == $key) echo "selected"; ?>><?php echo $categorie; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Lien</label>
				<input type="text" class="form-control" name="lien" value="<?php if(isset($fournisseur)) echo $fournisseur->getLien(); ?>">
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Ville</label>
				<input type="text" class="form-control" name="ville" value="<?php if(isset($fournisseur)) echo $fournisseur->getVille(); ?>">
			</div>
		</div>
		
		<div class="col-md-3 d-none">
			<div class="form-group">
				<label>Code postal</label>
				<input type="text" class="form-control" name="cp" value="<?php if(isset($fournisseur)) echo $fournisseur->getCP(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label for="photo" class="col-form-label input-label">Photo</label>
				<div>
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($fournisseur) && $fournisseur->getPhoto() != '' ? "images/fournisseurs/" . $fournisseur->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
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
		
		<div class="col-md-3">
    		<div class="form-group">
    			<label for="doc" class="col-form-label input-label">Document</label>
    			<div>
    				<div class="d-flex align-items-center">
    					<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_doc">
    						<?php $photoLink = isset($fournisseur) && $fournisseur->getDoc() != '' ? "images/fournisseurs/" . $fournisseur->getDoc() : "assets/img/profiles/avatar-02.jpg"; ?>
                            <?php $filename = isset($fournisseur) ? explode('.',$fournisseur->getDoc()) : '';?>
                            <?php if(isset($filename[1]) && strtolower($filename[1]) == 'pdf'): ?>
    						    <a href="<?php echo $photoLink;?>" target="_blank"><img id="avatarImg" class="avatar-img" src="assets/img/pdf.png" alt="Profile Image"></a>
                            <?php else: ?>
                                <a href="<?php echo $photoLink;?>" data-fancybox><img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image"></a>
    						<?php endif; ?>
                            <input type="file" name="doc[]" id="edit_doc">
    						
                            <span class="avatar-edit">
    							<i data-feather="edit-2" class="fa fa-upload shadow-soft"></i>
    						</span>
    					</label>
    
                        <?php if (isset($fournisseur)) : ?>
                        <a class="doc-remove avatar-edit" style="bottom:80px;color:red;left: 72px;" data-id="<?php echo $fournisseur->getId(); ?>">
                            <i class="fa fa-trash shadow-soft"></i>
                        </a>
                        <?php endif; ?>
    				</div>
    			</div>
    		</div>
		</div>
		
		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Fournisseur actif</span>
				</span>
				<span class="col-4">
					<input type="checkbox" name="active" class="toggle-switch-input" <?php if(isset($fournisseur) && $fournisseur->isActive()) echo "checked"; ?>>
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
				<span class="col-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Fournisseur valide</span>
				</span>
				<span class="col-4">
					<input type="checkbox" name="valide" class="toggle-switch-input" <?php if(isset($fournisseur) && $fournisseur->isValide()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->
				
		<?php if(isset($fournisseur)): ?>
			<input type="hidden" name="id" value="<?php echo $fournisseur->getId(); ?>">
		<?php endif; ?>
		<input type="hidden" name="id_agence" value="<?= isset($fournisseur) ? $fournisseur->getAgence()->getId() : $_SESSION['agence'] ?>">
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {
        $(document).on("click", ".doc-remove", function() {
            var $btn = $(this);
            var id = $btn.attr("data-id");
            if (confirm("Etes-vous sure !")) {
                var order = 'id=' + id;
                $.post("components/com_fournisseur/controleurs/router.php?task=removeDoc", order, function(theResponse) {
                    if (parseInt(theResponse) === 1) {
                        $('.avatar-img').attr('src','assets/img/profiles/avatar-02.jpg');
                    } else {
                        $('#fournisseurForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                })
            }
        })
        
        // envoi du formulaire en ajax
        $('form#fournisseurForm').ajaxForm({
            beforeSubmit: function () {
                $("#fournisseurForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
                $("#fournisseurForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Fournisseur ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Fournisseur modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#fournisseurForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_fournisseur";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#fournisseurForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#fournisseurForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
