<form method="post" action="<?= isset($payslip) ? $action2 : $action1; ?>" enctype="multipart/form-data" class="validateForm" id="paySlipForm">

    <div class="row">
        <div class="col-sm-12 msgbox"></div>
        <div class="col-md-3">
			<div class="form-group">
				<label>Titre<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="title" value="<?php if(isset($payslip)) echo $payslip->getTitle(); ?>" required>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="month" class="form-control" name="date" value="<?php if (isset($payslip)){ echo normaldate($payslip->getDate());}?>" required>
				</div>
			</div>
		</div>
        <div class="col-md-3 form-group">
            <label>Fichier
            </label>
            <input type="file" name="file[]" class="form-control" multiple />
        </div>

        <?php if (isset($payslip) && $payslip->getFile()) { ?>
            <div class="col-md-3 form-group mt-4">
                <div class="mt-3">
                    <a href="./images/resourceshumaines/payslips/<?php echo $payslip->getFile(); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-file"></i> Click pour voir le fichier</a>
                </div>
            </div>
        <?php } ?>

        <input type="hidden" name="id_resourcehumaine" value="<?= $resourcehumaine->getId() ?>" />

        <?php if (isset($payslip)) { ?>
        <input type="hidden" name="id" value="<?= $payslip->getId(); ?>" />
        <?php } ?>



    </div>

    <div class="text-right mt-4">
        <!-- <button type="reset" name="<?= $submitName; ?>" class="btn btn-light ">Anuller</button> -->
        <button type="submit" name="" class="btn btn-primary submit"><span
                class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($payslip) ? 'Modifier' : 'Ajouter'?> </button>
    </div>

</form>

<script>
$(function() {
    // envoi du formulaire en ajax
    $('form#paySlipForm').ajaxForm({
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
            var msgsucces = "Bulletin de paie ajouté avec succès.";
            var msgfaild = "Erreur lors de l'ajout.";
            if ($(".submit").attr("name") === "edit") {
                msgsucces = "Bulletin de paie modifié avec succès.";
                msgfaild = "Erreur lors de la modification.";
            }
            if (parseInt(theResponse) === 1) {
                $('#paySlipForm .msgbox').html(
                    '<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succès</strong> ' +
                    msgsucces + '</div>').slideDown();
                setTimeout(function() {

                    <?php $loc = "index.php?option=com_resourcehumaine&task=payslip&id=" . $_GET['id']; ?>
                    document.location = "<?= $loc ?>";

                }, 1500);
            } else if (parseInt(theResponse) === 0) {
                $('#paySlipForm .msgbox').html(
                    '<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Attention!</strong> ' +
                    msgvide + '</div>').slideDown();
            } else {
                $('#paySlipForm .msgbox').html(
                    '<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>Erreur!</strong> ' +
                    msgfaild + '</div>').slideDown();
            }
        }
    });
})
</script>