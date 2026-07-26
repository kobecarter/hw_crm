<form method="post" action="<?php echo $action; ?>" id="userForm" enctype="multipart/form-data">
	
	<div class="msgbox"></div>

	<div class="row form-group">
		<label for="password" class="col-sm-3 col-form-label input-label">Mot de passe</label>
		<div class="col-sm-9">
			<input type="password" class="form-control" name="password" placeholder="mot de passe">
		</div>
	</div>

	<!-- /Toggle Switch -->
	
	<?php if(isset($user)) { ?>
		<input type="hidden" name="id" value="<?= $user->getId() ;?>" />
	<?php } ?>
	<?php if(isset($user)) { ?>
	
		<input type="hidden" class="form-control" name="login" placeholder="Login" value="<?php if(isset($user)) echo $user->getLogin(); ?>">
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
