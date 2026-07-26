<?php
$action = "components/com_module/controleur/module.php?task=addModule";
$bt = $trad_com_module['AJOUTER_MODULE'][$_SESSION['user']->getLangue()];

?>
<form method="post" action="<?php echo $action ?>" enctype="multipart/form-data" class="validateForm" id="moduleForm" >
    <div class="row">
        <div class="col-md-4 form-group">
            <label>Zip</label>
            <input type="file" name="module[]" multiple />
        </div>
    </div>
    <input type="reset" class="btn btn-default" value="<?= $trad_com_module['ANNULER'][$_SESSION['user']->getLangue()]; ?>"/>
    <input type="submit" value="<?php echo $bt ?>" class="btn btn-primary submit"/>
    <span class="loading"><img src="../images/loading.gif" /></span>
</form>
<script>
    $(function () {
        var succes = "<?= $trad_com_module['SUCCES'][$_SESSION['user']->getLangue()];?>";
        var error = "<?= $trad_com_module['ERREUR'][$_SESSION['user']->getLangue()];?>";

        // envoi du formulaire en ajax
        $('form#moduleForm').ajaxForm({
            beforeSubmit: function () {
                $(".loading").fadeIn();
            },
            success: function (theResponse) {
                $(".loading").fadeOut();
                // messages
                var msgsucces = "<?= $trad_com_module['SUCCES_ADD'][$_SESSION['user']->getLangue()];?>";
                var msgfaild = "<?= $trad_com_module['ERREUR_ADD'][$_SESSION['user']->getLangue()];?>";

                if (parseInt(theResponse) === 1) {
                    $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + msgsucces + '</div>').slideDown();
                    setTimeout(function () {
                        document.location = "index.php?option=com_module";
                    }, 1500)
                }
                else {
                    $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + msgfaild + '</div>').slideDown();
                }
            }
        });
    })
</script>