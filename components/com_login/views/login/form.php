<form method="post" action="<?= $action; ?>" enctype="multipart/form-data" class="validateForm" id="operationForm">

    <div class="row">

        <div class="col-md-3 form-group">
            <label>Titre</label>
            <input name="titre" type="text" value="<?= isset($operation) ? $operation->getTitre() : "" ;?>" class="form-control" />
        </div>

        <div class="col-md-3 form-group">
            <label>Ordre</label>
            <input name="ordre" type="number" value="<?= isset($operation) ? $operation->getOrdre() : "" ;?>" class="form-control" />
        </div>

        <div class="col-md-3 form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="active"
                           value="1" <?= isset($operation) && $operation->isActive() ? "checked" : "" ;?> /> Active
                </label>
            </div>
        </div>

        <?php if(isset($operation)) { ?>
            <input type="hidden" name="id" value="<?= $operation->getId() ;?>" />
        <?php } ?>

    </div>

    <input type="reset" class="btn btn-default" value="Annuler"/>
    <input type="submit" name="<?= $submitName; ?>" value="<?= $submitValue; ?>" class="btn btn-primary submit"/>
    <span class="loading"><img src="../images/loading.gif" /></span>

</form>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#operationForm').ajaxForm({
            beforeSubmit: function () {
                $(".loading").fadeIn();
            },
            success: function (theResponse) {
                $(".loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");
                var msgvide = "Veuillez remplir Les champs obligatoires !";
                var msgsucces = "Operation ajouté avec succès.";
                var msgfaild = "Erreur lors de l'ajout.";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Operation modifié avec succès.";
                    msgfaild = "Erreur lors de la modification.";
                }
                if (parseInt(theResponse) === 1) {
                    $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succès</strong> ' + msgsucces + '</div>').slideDown();
                    setTimeout(function () {
                        document.location = "index.php?option=com_operation";
                    }, 1500)
                } else if(parseInt(theResponse) === 0) {
                    $('.msgbox').html('<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Attention!</strong> ' + msgvide + '</div>').slideDown();
                } else {
                    $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>Erreur!</strong> ' + msgfaild + '</div>').slideDown();
                }
            }
        });
    })
</script>
