<?php
if(isset($l)){
    $action = "components/com_lang/controleur/langue.php?task=editLangue";
    $task = "edit";
    $bt = $trad_com_lang['MODIFIER_LANGUE'][$_SESSION['user']->getLangue()];
}
else{
    $action = "components/com_lang/controleur/langue.php?task=addLangue";
    $task = "add";
    $bt = $trad_com_lang['AJOUTER_LANGUE'][$_SESSION['user']->getLangue()];
}
?>
<form method="post" action="<?php echo $action?>" enctype="multipart/form-data" class="validateForm" id="langueForm" >
    <div class="row">

        <div class="col-md-3 form-group">
            <label><?= $trad_com_lang['NOM'][$_SESSION['user']->getLangue()]; ?></label>
            <input name="nom" type="text" value="<?php if(isset($l)) echo $l->getNom()?>" required class="form-control" />
        </div>

        <div class="col-md-3 form-group">
            <label><?= $trad_com_lang['CODE'][$_SESSION['user']->getLangue()]; ?></label>
            <input name="code" type="text" value="<?php if(isset($l)) echo $l->getCode()?>" required class="form-control" />
        </div>

        <div class="col-md-2 form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="defaut" value="1" <?php if(isset($l) && $l->isDefault()) echo "checked";?> /> <?= $trad_com_lang['LANGUE_PAR_DEFAUT'][$_SESSION['user']->getLangue()]; ?>
                </label>
            </div>
        </div>

        <div class="col-md-3 form-group">
            <label><?= $trad_com_lang['DRAPEAU'][$_SESSION['user']->getLangue()]; ?></label>
            <input type="file" name="image[]" />
        </div>
        <?php
        if(isset($l) && $l->getFlag() != ''){
            ?>
            <div class="col-md-2 form-group"><img src="../images/langues/<?php echo $l->getFlag(); ?>" height="40" /></div>
            <?php
        }
        ?>


    </div>
    <?php if(isset($l)){ ?>
        <input type="hidden" name="id" value="<?=$l->getId()?>" />
    <?php } ?>

    <input type="reset" class="btn btn-default" value="<?= $trad_com_lang['ANNULER'][$_SESSION['user']->getLangue()]; ?>" />
    <input type="submit" value="<?php echo $bt?>" name="<?php echo $task; ?>" class="btn btn-primary submit" />
    <span class="loading"></span>
</form>
<script>
    $(function(){
        var succes = "<?= $trad_com_lang['SUCCES'][$_SESSION['user']->getLangue()];?>";
        var error = "<?= $trad_com_lang['ERREUR'][$_SESSION['user']->getLangue()];?>";

        // envoi du formulaire en ajax
        $('form#langueForm').ajaxForm({
            beforeSubmit: function() {
                $(".loading").fadeIn();
            },
            success: function(theResponse) {
                $(".loading").fadeOut();
                // messages
                if($(".submit").attr("name") == 'edit'){
                    var succes_msg = "<?= $trad_com_lang['SUCCES_MODIF'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_lang['ERREUR_MODIF'][$_SESSION['user']->getLangue()];?>";
                }
                else{
                    var succes_msg = "<?= $trad_com_lang['SUCCES_ADD'][$_SESSION['user']->getLangue()];?>";
                    var error_msg = "<?= $trad_com_lang['ERREUR_ADD'][$_SESSION['user']->getLangue()];?>";
                }
                if(parseInt(theResponse) == 1){
                    $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>');
                    setTimeout(function(){ document.location = "index.php?option=com_lang"; },2000)
                }
                else {
                    $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>');
                    $('.msgbox').slideDown();
                }
            }
        });
    })
</script>