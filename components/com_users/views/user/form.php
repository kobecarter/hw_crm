<form method="post" action="<?php echo $action; ?>" id="userForm" enctype="multipart/form-data">
	
	<div class="msgbox"></div>
	
	<div class="row form-group">
		<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
		<div class="col-sm-9">
			<div class="d-flex align-items-center">
				<label class="avatar avatar-xxl profile-cover-avatar m-0" for="edit_img">
					<?php $photoLink = isset($user) && $user->getPhoto() != '' ? "images/users/" . $user->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
					<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
					<input type="file" name="photo[]" id="edit_img">
					<span class="avatar-edit">
						<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
					</span>
				</label>
			</div>
		</div>
	</div>
	<?php if (($task == "edit" && $_SESSION['user']->hasDroit('edit', 'com_users')) || ($task == "add" && $_SESSION['user']->hasDroit('add', 'com_users'))) :?>
    	<div class="row form-group">
    		<label for="nom" class="col-sm-3 col-form-label input-label">Profil</label>
    		<div class="col-sm-9">
    			<select class="select" name="profil">
    				<?php foreach($profils as $profil): ?>
    				<option value="<?php echo $profil->getId(); ?>" <?php if(isset($user) && $user->getProfil()->getId() == $profil->getId()){ echo "selected";}?>><?php echo $profil->getProfil(); ?></option>
    				<?php endforeach; ?>
    			</select>
    		</div>
    	</div>
	<?php endif;?>
	<div class="row form-group">
		<label for="nom" class="col-sm-3 col-form-label input-label">Nom</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="nom" placeholder="Nom" value="<?php if(isset($user)) echo $user->getNom(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="prenom" class="col-sm-3 col-form-label input-label">Prénom</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="prenom" placeholder="Prénom" value="<?php if(isset($user)) echo $user->getPrenom(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="email" class="col-sm-3 col-form-label input-label">Email</label>
		<div class="col-sm-9">
			<input type="email" class="form-control" name="email" placeholder="Email" value="<?php if(isset($user)) echo $user->getEmail(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="tel" class="col-sm-3 col-form-label input-label">Tél <span class="text-muted">(Optional)</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="tel" placeholder="+x(xxx)xxx-xx-xx" value="<?php if(isset($user)) echo $user->getTel(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="adresse" class="col-sm-3 col-form-label input-label">Adresse</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="adresse" placeholder="Adresse" value="<?php if(isset($user)) echo $user->getAdresse(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="login" class="col-sm-3 col-form-label input-label">Login</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="login" placeholder="Login" value="<?php if(isset($user)) echo $user->getLogin(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="login" class="col-sm-3 col-form-label input-label">Password</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" name="password" placeholder="Password" value="">
		</div>
	</div>
	<div class="row form-group">
		<label for="zipcode" class="col-sm-3 col-form-label input-label">Langue système</label>
		<div class="col-sm-9">
			<select class="select" name="langue">
				<option value="fr" <?php if(isset($user) && $user->getLangue() == 'fr') echo "selected"; ?>>Français</option>
				<option value="en" <?php if(isset($user) && $user->getLangue() == 'en') echo "selected"; ?>>Anglais</option>
			</select>
		</div>
	</div>
	
	<!-- Toggle Switch -->
	<label class="row form-group toggle-switch">
		<span class="col-8 col-sm-3 toggle-switch-content ml-0">
			<span class="d-block text-dark">Utilisateur actif</span>
		</span>
		<span class="col-4 col-sm-1">
			<input type="checkbox" name="actif" class="toggle-switch-input" <?php if(isset($user) && $user->isActif()) echo "checked"; ?>>
			<span class="toggle-switch-label ml-auto">
				<span class="toggle-switch-indicator"></span>
			</span>
		</span>
	</label>
	<!-- /Toggle Switch -->
	
	<!-- Toggle Switch -->
	<label class="row form-group toggle-switch">
		<span class="col-8 col-sm-3 toggle-switch-content ml-0">
			<span class="d-block text-dark">Super administrateur</span>
		</span>
		<span class="col-4 col-sm-1">
			<input type="checkbox" name="su" class="toggle-switch-input" <?php if(isset($user) && $user->isSuperUser()) echo "checked"; ?>>
			<span class="toggle-switch-label ml-auto">
				<span class="toggle-switch-indicator"></span>
			</span>
		</span>
	</label>
	<!-- /Toggle Switch -->
	
	<?php if(isset($user)) { ?>
		<input type="hidden" name="id" value="<?= $user->getId() ;?>" />
	<?php } ?>
	
	<div class="text-right">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#userForm').ajaxForm({
            beforeSubmit: function () {
                $(".loading").fadeIn();
            },
            success: function (theResponse) {
                console.log(theResponse)
                $(".loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Utilisateur ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Utilisateur modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#userForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>').slideDown();
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_users";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#userForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#userForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
