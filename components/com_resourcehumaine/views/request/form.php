<form method="post" action="<?= isset($request) ? $action2 : $action1; ?>" enctype="multipart/form-data" class="validateForm" id="reQuestForm">

    <div class="row">
        <div class="col-sm-12 msgbox"></div>
        <div class="col-md-12">
			<div class="form-group">
				<label>Titre<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="title" value="<?php if(isset($request)) echo $request->getTitle(); ?>" required>
			</div>
		</div>

        <div class="col-md-12">
			<div class="form-group">
				<label>Description<span class="text-danger"> * </span></label>
                <textarea name="description" class="form-control" rows="6" required><?php if(isset($request)) echo $request->getDescription(); ?></textarea>
			</div>
		</div>

        <input type="hidden" name="id_resourcehumaine" value="<?= $resourcehumaine->getId() ?>" />

        <?php if (isset($request)) { ?>
        <input type="hidden" name="id" value="<?= $request->getId(); ?>" />
        <?php } ?>



    </div>

    <div class="text-right mt-4">
        <!-- <button type="reset" name="<?= $submitName; ?>" class="btn btn-light ">Anuller</button> -->
        <button type="submit" name="" class="btn btn-primary submit"><span
                class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($request) ? 'Modifier' : 'Ajouter'?> </button>
    </div>

</form>

<script>
$(function() {
    // envoi du formulaire en ajax
    $('form#reQuestForm').ajaxForm({
        beforeSubmit: function() {
            $(".loading").fadeIn();
        },
        success: function(theResponse) {
            console.log(theResponse)    
            $(".loading").fadeOut();
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
            var msgvide = "Veuillez remplir Les champs obligatoires !";
            var msgsucces = "Demande ajouté avec succès.";
            var msgfaild = "Erreur lors de l'ajout.";
            if ($(".submit").attr("name") === "edit") {
                msgsucces = "Demande modifié avec succès.";
                msgfaild = "Erreur lors de la modification.";
            }
            if (parseInt(theResponse) === 1) {
                $('#reQuestForm .msgbox').html(
                    '<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succès</strong> ' +
                    msgsucces + '</div>').slideDown();
                setTimeout(function() {
                    document.location = "index.php?task=requests";

                }, 1500);
            } else if (parseInt(theResponse) === 0) {
                $('#reQuestForm .msgbox').html(
                    '<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Attention!</strong> ' +
                    msgvide + '</div>').slideDown();
            } else {
                $('#reQuestForm .msgbox').html(
                    '<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>Erreur!</strong> ' +
                    msgfaild + '</div>').slideDown();
            }
        }
    });
})
</script>