<form method="post" action="<?= isset($payslip) ? $action2 : $action1; ?>" enctype="multipart/form-data" class="validateForm" id="paySlipForm">

    <div class="row">
        <div class="col-sm-12 msgbox"></div>
        <div class="col-md-4">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" class="form-control" id="paySlipTitrePreview" value="<?php if(isset($payslip)) echo $payslip->getTitle(); ?>" readonly>
				<small class="text-muted">Généré automatiquement à partir du nom de l'employé et du mois — non modifiable.</small>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<?php
					// Pré-remplissage possible depuis le bandeau "bulletins manquants" (?mois=YYYY-MM)
					// de la liste, en plus du cas édition normal. normaldate() n'existe pas dans ce
					// projet (seul normaldate2() existe, format texte "mois année" - pas utilisable
					// pour un <input type="month">) : on formate direct depuis la date stockée.
					$dateInitiale = isset($payslip) && $payslip->getDate() ? date('Y-m', strtotime($payslip->getDate())) : (isset($_GET['mois']) ? preg_replace('/[^0-9\-]/', '', $_GET['mois']) : '');
					?>
					<input type="month" class="form-control" id="paySlipDate" name="date" value="<?= $dateInitiale ?>" required>
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
    // Aperçu du titre auto-généré (payslip::titreAuto() côté serveur - même convention reproduite
    // ici en JS pour un retour immédiat, le serveur reste la seule source de vérité à l'enregistrement).
    var paySlipNomEmploye = <?= json_encode(trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName())) ?>;
    function updatePaySlipTitrePreview() {
        var val = $('#paySlipDate').val();
        if (!val) {
            $('#paySlipTitrePreview').val('');
            return;
        }
        var parts = val.split('-');
        $('#paySlipTitrePreview').val('Bulletin de paie ' + paySlipNomEmploye + ' ' + parts[1] + '/' + parts[0]);
    }
    $('#paySlipDate').on('change', updatePaySlipTitrePreview);
    updatePaySlipTitrePreview();

    // Clic sur une pastille "mois manquant" (bandeau d'alerte, en bas de cette même page) :
    // pré-remplit le mois ici et fait défiler jusqu'au formulaire plutôt que de naviguer ailleurs.
    $(document).on('click', '.payslip-missing-pill', function () {
        $('#paySlipDate').val($(this).data('ym')).trigger('change');
        $('html, body').animate({ scrollTop: $('#paySlipForm').offset().top - 100 }, 'slow');
    });

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

                // Pastille "mois manquant" correspondante (si le mois ajouté en faisait partie) :
                // bascule au vert tout de suite, le rechargement ci-dessous la fera disparaître
                // pour de bon (le mois n'est plus manquant, moisManquants recalculé côté serveur).
                var addedYm = $('#paySlipDate').val();
                $('.payslip-missing-pill[data-ym="' + addedYm + '"]').addClass('payslip-missing-pill-done');

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