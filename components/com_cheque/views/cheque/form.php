<style>
	.chosen-container{
		width: 100% !important;
	}
</style>
<form method="post" action="<?php echo $action; ?>" id="chequeForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Sens <span class="text-danger">*</span></label>
				<select class="select" name="type" id="chequeType" required>
					<option value="">Séléctionner</option>
					<option value="entrant" <?php if (isset($cheque) && $cheque->getType() == 'entrant') echo "selected"; ?>>Entrant (reçu)</option>
					<option value="sortant" <?php if (isset($cheque) && $cheque->getType() == 'sortant') echo "selected"; ?>>Sortant (émis)</option>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Tiers <span class="text-danger">*</span></label>
				<select class="select" name="tiers_type" id="chequeTiersType" required>
					<option value="">Séléctionner</option>
					<option value="client" <?php if (isset($cheque) && $cheque->getTiersType() == 'client') echo "selected"; ?>>Client</option>
					<option value="fournisseur" <?php if (isset($cheque) && $cheque->getTiersType() == 'fournisseur') echo "selected"; ?>>Fournisseur</option>
				</select>
			</div>
		</div>

		<div class="col-md-6" id="chequeZoneClient" style="<?= isset($cheque) && $cheque->getTiersType() == 'fournisseur' ? 'display:none;' : '' ?>">
			<div class="form-group">
				<label>Client</label>
				<select class="chosen-select" name="client">
					<option value="">Choisir le client...</option>
					<?php foreach ($clients as $client) : ?>
						<?php $sl = isset($cheque) && $cheque->getTiersType() == 'client' && $cheque->getClient()->getId() == $client->getId() ? "selected" : ""; ?>
						<option value="<?php echo $client->getId() ?>" <?php echo $sl; ?>><?php echo trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom()); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-6" id="chequeZoneFournisseur" style="<?= !isset($cheque) || $cheque->getTiersType() != 'fournisseur' ? 'display:none;' : '' ?>">
			<div class="form-group">
				<label>Fournisseur</label>
				<select class="chosen-select" name="fournisseur">
					<option value="">Choisir le fournisseur...</option>
					<?php foreach ($fournisseurs as $fournisseurOption) : ?>
						<?php $nomFournisseurCheque = trim((string) $fournisseurOption->getRaisonSocial()) !== '' ? $fournisseurOption->getRaisonSocial() : trim($fournisseurOption->getPrenom() . ' ' . $fournisseurOption->getNom()); ?>
						<?php $sl = isset($cheque) && $cheque->getTiersType() == 'fournisseur' && $cheque->getFournisseur()->getId() == $fournisseurOption->getId() ? "selected" : ""; ?>
						<option value="<?php echo $fournisseurOption->getId() ?>" <?php echo $sl; ?>><?php echo htmlspecialchars($nomFournisseurCheque); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Numéro de Chèque <span class="text-danger">*</span></label>
				<input type="text" class="form-control" name="check_number" value="<?php if(isset($cheque)) echo $cheque->getCheckNumber(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date d'émission <span class="text-danger">*</span></label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date" value="<?php if(isset($cheque)) echo normaldate($cheque->getDate()); else echo date('d/m/Y'); ?>" required>
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date d'encaissement <span class="text-danger">*</span></label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date_encaissement" value="<?php if(isset($cheque) && $cheque->getDateEncaissement()) echo normaldate($cheque->getDateEncaissement()); ?>" required>
				</div>
				<small class="text-muted">Un rappel apparaît dans le bandeau d'alertes tant que le chèque reste "Pas encore".</small>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Montant <span class="text-danger">*</span></label>
				<input type="number" step="any" class="form-control" name="amount" value="<?php if(isset($cheque)) echo $cheque->getAmount(); ?>" required>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Devise <span class="text-danger">*</span></label>
				<select class="select" name="currency" required>
					<option value="">Séléctionner</option>
					<option value="DH" <?php if (isset($cheque) && $cheque->getCurrency() == 'DH') echo "selected"; ?>>MAD (DH)</option>
					<option value="€" <?php if (isset($cheque) && $cheque->getCurrency() == '€') echo "selected"; ?>>Euro (€)</option>
					<option value="$" <?php if (isset($cheque) && $cheque->getCurrency() == '$') echo "selected"; ?>>Dollar ($)</option>
					<option value="£" <?php if (isset($cheque) && $cheque->getCurrency() == '£') echo "selected"; ?>>Pound (£)</option>
					<option value="AED" <?php if (isset($cheque) && $cheque->getCurrency() == 'AED') echo "selected"; ?>>AED (DH)</option>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Status <span class="text-danger">*</span></label>
				<select class="chosen-select" name="status" required>
					<option value="">Séléctionner</option>
					<option value="Pas encore" <?php if(isset($cheque) && $cheque->getStatus() == 'Pas encore'){ echo "selected"; }?>>Pas encore</option>
					<option value="Encaissé" <?php if(isset($cheque) && $cheque->getStatus() == 'Encaissé'){ echo "selected"; }?>>Encaissé</option>
					<option value="Impayé" <?php if(isset($cheque) && $cheque->getStatus() == 'Impayé'){ echo "selected"; }?>>Impayé</option>
				</select>
			</div>
		</div>

		<div class="col-md-4 form-group">
            <label>Fichier <?php if(!isset($cheque)) :?> <span class="text-danger">*</span> <?php endif;?></label>
            <input type="file" name="file_check[]" class="form-control" multiple <?php if(!isset($cheque)) {echo 'required';}?>/>
        </div>

        <?php if (isset($cheque) && $cheque->getFile()) { ?>
            <div class="col-md-3 form-group mt-4">
                <div class="mt-3">
                    <a href="./images/cheques/<?php echo $cheque->getFile(); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-file"></i> Clicker pour voir le fichier</a>
                </div>
            </div>
        <?php } ?>

		<div class="col-md-12">
			<div class="form-group">
				<label>Motif</label>
				<textarea name="reason" id="reason"><?php if (isset($cheque)){ echo $cheque->getReason(); }?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('reason', {
                        allowedContent: true,
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Commentaire</label>
				<textarea name="comment" id="comment"><?php if (isset($cheque)){ echo $cheque->getComment(); }?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('comment', {
                        allowedContent: true,
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>

		<input type="hidden" name="id_agence" value="<?= isset($cheque) ? $cheque->getAgence()->getId() : $_SESSION['agence']?>">
		<?php if(isset($cheque)): ?>
			<input type="hidden" name="id" value="<?php echo $cheque->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
    $(function () {

        function chequeBasculerTiers() {
            var tiersType = $('#chequeTiersType').val();
            $('#chequeZoneClient').toggle(tiersType === 'client');
            $('#chequeZoneFournisseur').toggle(tiersType === 'fournisseur');
        }
        chequeBasculerTiers();
        $(document).on('change', '#chequeTiersType', chequeBasculerTiers);

        // envoi du formulaire en ajax
        $('form#chequeForm').ajaxForm({
            beforeSubmit: function () {
                $("#chequeForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
				console.log(theResponse)
                $("#chequeForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");

                var msgsucces = "Cheque ajoutée avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Cheque modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#chequeForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    <?php if (isset($cheque)) : ?>
						setTimeout(function() {
							document.location.reload();
						}, 1500)
					<?php else : ?>
						setTimeout(function() {
							document.location = "index.php?option=com_cheque";
						}, 1500)
					<?php endif; ?>

                } else if(parseInt(theResponse) === 0) {
                    $('#chequeForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires (dont le client/fournisseur choisi)<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#chequeForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });

		var msgsuccesDelete = "Cheque supprimée avec succès";

		$(document).on( "click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_cheque/controleurs/router.php?task=deleteCheque", order, function (theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().addClass("table-danger");
						setTimeout(function () {
							$btn.parent().parent().remove()
						}, 1000);

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsuccesDelete + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
					else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})
    })
</script>
