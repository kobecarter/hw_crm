<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="profilForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Profil</label>
				<input type="text" class="form-control" name="profil" value="<?php if(isset($profil)) echo $profil->getProfil(); ?>">
			</div>
		</div>
		
		<?php if(isset($profil)): ?>
		<input type="hidden" name="id" value="<?php echo $profil->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#profilForm').ajaxForm({
            beforeSubmit: function () {
                $(".loading").fadeIn();
            },
            success: function (theResponse) {
                $(".loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
				
                var msgsucces = "Profil ajouté avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Profil modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#profilForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					
                    setTimeout(function () {
                        document.location = "index.php?option=com_users&task=profil";
                    }, 1500)
					
                } else if(parseInt(theResponse) === 0) {
                    $('#profilForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#profilForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>
