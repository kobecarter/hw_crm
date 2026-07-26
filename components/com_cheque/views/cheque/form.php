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
				<label>Numéro de Chèque <spaan class="text-danger">*</span></label>
				<input type="text" class="form-control" name="check_number" value="<?php if(isset($cheque)) echo $cheque->getCheckNumber(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Date <spaan class="text-danger">*</span></label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date" value="<?php if(isset($cheque)) echo normaldate($cheque->getDate()); else echo date('d/m/Y'); ?>" required>
				</div>	
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Bénéficiaire <spaan class="text-danger">*</span></label>
				<input type="text" class="form-control" name="beneficiary" value="<?php if(isset($cheque)) echo $cheque->getBeneficiary(); ?>" required>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Montant <spaan class="text-danger">*</span></label>
				<input type="number" step="any" class="form-control" name="amount" value="<?php if(isset($cheque)) echo $cheque->getAmount(); ?>" required>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Devise <spaan class="text-danger">*</span></label>
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
				<label>Status <spaan class="text-danger">*</span></label>
				<select class="chosen-select" name="status" required>
					<option value="">Séléctionner</option>
					<option value="Encaissé" <?php if(isset($cheque) && $cheque->getStatus() == 'Encaissé'){ echo "selected"; }?>>Encaissé</option>
					<option value="Pas encore" <?php if(isset($cheque) && $cheque->getStatus() == 'Pas encore'){ echo "selected"; }?>>Pas encore</option>
					<option value="Impayé" <?php if(isset($cheque) && $cheque->getStatus() == 'Impayé'){ echo "selected"; }?>>Impayé</option>
				</select>
			</div>
		</div>

		<div class="col-md-3 form-group">
            <label>Fichier <?php if(!isset($cheque)) :?> <spaan class="text-danger">*</span> <?php endif;?></label>
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
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
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
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
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
                    $('#chequeForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#chequeForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
		
		var msgsucces = "Cheque supprimée avec succès";
	
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

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
					else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})	
    })
</script>


