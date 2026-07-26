<form method="post" action="<?= $action; ?>" enctype="multipart/form-data" class="validateForm" id="temoignageForm">

    <div class="row">
        <div class="col-md-12 msgbox"></div>
        <div class="col-md-3 form-group">
            <label>
                <?php
                if (isset($trad_com_temoignage['NOM'][$_SESSION['user']->getLangue()]))
                    echo $trad_com_temoignage['NOM'][$_SESSION['user']->getLangue()];
                else
                    echo "Nom";
                ?>
            </label>
            <input name="nom" type="text" value="<?= isset($temoignage) ? $temoignage->getNom() : ""; ?>" class="form-control" />
        </div>

        <div class="col-md-3 form-group">
            <label>
                <?php
                if (isset($trad_com_temoignage['FONCTION'][$_SESSION['user']->getLangue()]))
                    echo $trad_com_temoignage['FONCTION'][$_SESSION['user']->getLangue()];
                else
                    echo "Fonction";
                ?>
            </label>
            <input name="fonction" type="text" value="<?= isset($temoignage) ? $temoignage->getFonction() : ""; ?>" class="form-control" />
        </div>

        <div class="col-md-3 form-group">
            <label>
                <?php
                if (isset($trad_com_temoignage['EMAIL'][$_SESSION['user']->getLangue()]))
                    echo $trad_com_temoignage['EMAIL'][$_SESSION['user']->getLangue()];
                else
                    echo "E-mail";
                ?>
            </label>
            <input name="email" type="email" value="<?= isset($temoignage) ? $temoignage->getEmail() : ""; ?>" class="form-control" />
        </div>

        <div class="col-md-3 form-group">
            <label>
                <?php
                if (isset($trad_com_temoignage['SUJET'][$_SESSION['user']->getLangue()]))
                    echo $trad_com_temoignage['SUJET'][$_SESSION['user']->getLangue()];
                else
                    echo "Sujet";
                ?>
            </label>
            <input name="sujet" type="text" value="<?= isset($temoignage) ? $temoignage->getSujet() : ""; ?>" class="form-control" />
        </div>



        <div class="col-md-6 form-group">
            <label>
                <?php
                if (isset($trad_com_temoignage['TEMOIGNAGE'][$_SESSION['user']->getLangue()]))
                    echo $trad_com_temoignage['TEMOIGNAGE'][$_SESSION['user']->getLangue()];
                else
                    echo "Temoignage";
                ?>
            </label>
            <textarea class="form-control" id="temoignage" name="temoignage"><?php if (isset($temoignage)) echo $temoignage->getTemoignage(); ?></textarea>
        </div>
        <div class="col-md-3 form-group">
            <label>Photo</label>
            <input type="file" name="photo[]" class="form-control" />
        </div>

        <?php if (isset($temoignage) && $temoignage->getPhoto() ) { ?>
            <div class="col-md-2 mt-4">
                <?php global $siteURL;
                $imageLink = $siteURL . "images/temoignages/" . $temoignage->getPhoto(); ?>
                <img src="<?= $imageLink ?>" class="img-thumbnail" width="150" height="100" />
            </div>
        <?php } ?>

        <div class="col-md-2 form-group">
            <label>
                <?php
                if (isset($trad_com_temoignage['ORDRE'][$_SESSION['user']->getLangue()]))
                    echo $trad_com_temoignage['ORDRE'][$_SESSION['user']->getLangue()];
                else
                    echo "Ordre";
                ?>
            </label>
            <input name="ordre" type="number" value="<?= isset($temoignage) ? $temoignage->getOrdre() : ""; ?>" class="form-control" />
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label class="mb-3">Active</label>
                <label class="row toggle-switch">

                    <span class="col-4 col-sm-1">
                        <input type="checkbox" name="active" class="toggle-switch-input" <?php if (isset($temoignage) && $temoignage->isActive()) echo "checked"; ?>>
                        <span class="toggle-switch-label ml-auto">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </span>
                </label>
            </div>

        </div>


        <?php if (isset($temoignage)) { ?>
            <input type="hidden" name="id" value="<?= $temoignage->getId(); ?>" />
        <?php } ?>

    </div>



    <div class="text-right mt-4">
        <button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
    </div>

</form>

<script>
    $(function() {

        // envoi du formulaire en ajax
        $('form#temoignageForm').ajaxForm({
            beforeSubmit: function() {
                $("#temoignageForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse);
                $("#temoignageForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");
                var msgsucces = "Temoignage ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Temoignage modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#temoignageForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    setTimeout(function() {
                        document.location = "index.php?option=com_temoignage";
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#temoignageForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#temoignageForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    })
</script>