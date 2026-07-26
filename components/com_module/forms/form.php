<?php
$action = "components/com_module/controleur/module.php?task=editModule";
$bt = $trad_com_module['MODIFIER_MODULE'][$_SESSION['user']->getLangue()];
?>
<form method="post" action="<?php echo $action ?>" enctype="multipart/form-data" class="validateForm" id="moduleForm">
    <div class="row">
        <div class="col-md-4 form-group has-iconed">
            <label><?= $trad_com_module['NOM'][$_SESSION['user']->getLangue()]; ?></label>
            <div class="iconed-input"><input name="nom" type="text" value="<?php echo $m->getNom() ?>"
                                             class="form-control"/></div>
        </div>
        <div class="col-md-4 form-group has-iconed">
            <label><?= $trad_com_module['CLASSE'][$_SESSION['user']->getLangue()]; ?></label>
            <div class="iconed-input"><input name="classe" type="text" value="<?php echo $m->getClasse() ?>"
                                             class="form-control"/></div>
        </div>
        <div class="col-md-4 form-group has-iconed">
            <label><?= $trad_com_module['TABLE'][$_SESSION['user']->getLangue()]; ?></label>
            <div class="iconed-input"><input name="table" type="text" value="<?php echo $m->getNomTable() ?>"
                                             class="form-control"/></div>
        </div>
        <div class="col-md-4 form-group has-iconed">
            <label><?= $trad_com_module['NOM_ICONE'][$_SESSION['user']->getLangue()]; ?></label>
            <div class="iconed-input"><input name="icone" type="text" value="<?php echo $m->getIcon() ?>"
                                             class="form-control"/></div>
        </div>
        <div class="col-md-4 form-group has-iconed">
            <label><?= $trad_com_module['ICONE'][$_SESSION['user']->getLangue()]; ?></label>
            <div class="iconed-input">
                <div class="fa fa-<?= $m->getIcon(); ?> fa-2x"></div>
            </div>
        </div>
        <div class="col-md-4 form-group">
            <label><?= $trad_com_module['POSITION'][$_SESSION['user']->getLangue()]; ?></label>
            <select name="position" class="form-control chosen-select">
                <option value=""></option>
                <?php
                $positions = array("side", "param", "center");
                foreach ($positions as $position) {
                    $sl = isset($m) && $m->getPosition() == $position ? "selected" : "";
                    ?>
                    <option value="<?= $position; ?>" <?= $sl; ?>><?= $position; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <input type="hidden" name="id" value="<?= $m->getIdModule(); ?>"/>
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
                var msgsucces = "<?= $trad_com_module['SUCCES_MODIF'][$_SESSION['user']->getLangue()];?>";
                var msgfaild = "<?= $trad_com_module['ERREUR_MODIF'][$_SESSION['user']->getLangue()];?>";

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