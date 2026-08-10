<form method="post" action="<?= isset($file) ? $action2 : $action1; ?>" enctype="multipart/form-data" class="validateForm" id="fileResourceHumaineForm">

    <div class="row">
        <div class="col-sm-12 msgbox"></div>
        <input type="hidden" name="id_realisation" value="<?= $_GET['id']; ?>">
        <div class="col-md-3">
			<div class="form-group">
				<label>Titre<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="title" value="<?php if(isset($file)) echo $file->getTitle(); ?>" required>
			</div>
		</div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Type de document</label>
                <select class="form-control" name="document_type">
                    <option value="">Autre / non catégorisé</option>
                    <?php foreach (fileresourcehumaine::documentsRequis($resourcehumaine->getStatus()) as $cle => $libelle) : ?>
                        <option value="<?= $cle ?>" <?= (isset($file) && $file->getDocumentType() == $cle) ? 'selected' : '' ?>><?= htmlspecialchars($libelle) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-3 form-group">
            <label>Fichier
            </label>
            <input type="file" name="file[]" class="form-control" multiple />
        </div>

        <?php if (isset($file) && $file->getFile()) { ?>
            <div class="col-md-3 form-group mt-4">
                <div class="mt-3">
                    <a href="./images/resourceshumaines/files/<?php echo $file->getFile(); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-file"></i> Click pour voir le fichier</a>
                </div>
            </div>
        <?php } ?>

        <input type="hidden" name="id_resourcehumaine" value="<?= $resourcehumaine->getId() ?>" />

        <?php if (isset($file)) { ?>
        <input type="hidden" name="id" value="<?= $file->getId(); ?>" />
        <?php } ?>



    </div>

    <div class="text-right mt-4">
        <!-- <button type="reset" name="<?= $submitName; ?>" class="btn btn-light ">Anuller</button> -->
        <button type="submit" name="" class="btn btn-primary submit"><span
                class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($file) ? 'Modifier' : 'Ajouter'?> </button>
    </div>

</form>

<script>
$(function() {
    // envoi du formulaire en ajax
    $('form#fileResourceHumaineForm').ajaxForm({
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
            var msgsucces = "Fichier ajouté avec succès.";
            var msgfaild = "Erreur lors de l'ajout.";
            if ($(".submit").attr("name") === "edit") {
                msgsucces = "Fichier modifié avec succès.";
                msgfaild = "Erreur lors de la modification.";
            }
            if (parseInt(theResponse) === 1) {
                $('#fileResourceHumaineForm .msgbox').html(
                    '<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succès</strong> ' +
                    msgsucces + '</div>').slideDown();
                setTimeout(function() {

                    <?php $loc = "index.php?option=com_resourcehumaine&task=file&id=" . $_GET['id']; ?>
                    document.location = "<?= $loc ?>";

                }, 1500);
            } else if (parseInt(theResponse) === 0) {
                $('#fileResourceHumaineForm .msgbox').html(
                    '<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Attention!</strong> ' +
                    msgvide + '</div>').slideDown();
            } else {
                $('#fileResourceHumaineForm .msgbox').html(
                    '<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>Erreur!</strong> ' +
                    msgfaild + '</div>').slideDown();
            }
        }
    });
})
</script>