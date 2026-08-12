<form method="post" action="<?php echo $action; ?>" id="userSelfForm" enctype="multipart/form-data">

	<div class="msgbox"></div>

	<div class="row form-group">
		<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
		<div class="col-sm-9">
			<div class="d-flex align-items-center">
				<label class="avatar avatar-xxl profile-cover-avatar m-0" for="edit_img">
					<?php $photoLink = $user->getPhoto() != '' ? "images/users/" . $user->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
					<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
					<input type="file" name="photo[]" id="edit_img">
					<span class="avatar-edit">
						<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
					</span>
				</label>
			</div>
		</div>
	</div>
	<div class="row form-group">
		<label class="col-sm-3 col-form-label input-label">Profil</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" value="<?php echo $user->getProfil()->getProfil(); ?>" disabled>
			<small class="text-muted">Modifiable uniquement par un administrateur.</small>
		</div>
	</div>
	<div class="row form-group">
		<label for="nom" class="col-sm-3 col-form-label input-label">Nom</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="nom" placeholder="Nom" value="<?php echo $user->getNom(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="prenom" class="col-sm-3 col-form-label input-label">Prénom</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="prenom" placeholder="Prénom" value="<?php echo $user->getPrenom(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="email" class="col-sm-3 col-form-label input-label">Email</label>
		<div class="col-sm-9">
			<input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo $user->getEmail(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="tel" class="col-sm-3 col-form-label input-label">Tél <span class="text-muted">(Optional)</span></label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="tel" placeholder="+x(xxx)xxx-xx-xx" value="<?php echo $user->getTel(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label for="adresse" class="col-sm-3 col-form-label input-label">Adresse</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" name="adresse" placeholder="Adresse" value="<?php echo $user->getAdresse(); ?>">
		</div>
	</div>
	<div class="row form-group">
		<label class="col-sm-3 col-form-label input-label">Login</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" value="<?php echo $user->getLogin(); ?>" disabled>
			<small class="text-muted">Modifiable uniquement par un administrateur.</small>
		</div>
	</div>
	<div class="row form-group">
		<label for="password" class="col-sm-3 col-form-label input-label">Nouveau mot de passe <span class="text-muted">(Optional)</span></label>
		<div class="col-sm-9">
			<input type="password" class="form-control" name="password" placeholder="Laisser vide pour ne pas changer" value="">
		</div>
	</div>
	<div class="row form-group">
		<label for="langue" class="col-sm-3 col-form-label input-label">Langue système</label>
		<div class="col-sm-9">
			<select class="select" name="langue">
				<option value="fr" <?php if($user->getLangue() == 'fr') echo "selected"; ?>>Français</option>
				<option value="en" <?php if($user->getLangue() == 'en') echo "selected"; ?>>Anglais</option>
			</select>
		</div>
	</div>

	<input type="hidden" name="id" value="<?= $user->getId(); ?>" />

	<div class="text-right">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {
        $('form#userSelfForm').ajaxForm({
            beforeSubmit: function () {
                $(".loading").fadeIn();
            },
            success: function (theResponse) {
                $(".loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");

                if (parseInt(theResponse) === 1) {
                    $('#userSelfForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Profil mis à jour avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>').slideDown();
                    setTimeout(function () {
                        document.location = "index.php?option=com_users&task=myProfile";
                    }, 1200)
                } else if (parseInt(theResponse) === 0) {
                    $('#userSelfForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#userSelfForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
