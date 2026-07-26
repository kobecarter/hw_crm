<!--<h4 class="card-title">Basic Info</h4>-->
<style>
	.discount_val {
		display: none;
	}

	.myResponsivTable {
		display: block;
		width: 100%;
	}

	@media (max-width: 768px) {
		.myResponsivTable {
			overflow-x: auto !important;
		}
	}
</style>
<form method="post" action="<?php echo $action; ?>" id="factureForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-6">
			<div class="form-group">
				<label>Client</label>
				<select class="chosen-select form-select form-control" name="client" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($clients as $client) : ?>
						<?php $sl = isset($facture) && $facture->getClient()->getId() == $client->getId() ? "selected" : ""; ?>
						<option value="<?php echo $client->getId() ?>" <?php echo $sl; ?>><?php echo $client->getNom() . ' ' . $client->getPrenom() . ' - ' . $client->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label>Banque</label>
				<select class="chosen-select form-select form-control" name="bank" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($banks as $bank) : ?>
						<?php $sl = isset($facture) && $facture->getBank() && $facture->getBank()->getId() == $bank->getId() ? "selected" : ""; ?>
						<option value="<?php echo $bank->getId() ?>" <?php echo $sl; ?>><?php echo $bank->getRaisonSociale() . ' ' . $bank->getRib(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Numéro</label>
				<input type="text" readonly class="form-control" name="numero" value="<?php if (isset($facture)) echo $facture->getNumero();?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date facture</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_facture" value="<?php if (isset($facture)) echo normaldate($facture->getDateFacture());
																										else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date debut</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_debut" value="<?php if (isset($facture)) echo normaldate($facture->getDateDebut());
																										else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date fin</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_fin" value="<?php if (isset($facture)) echo normaldate($facture->getDateFin());
																										else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Réduction</label>
				<select class="select discount" name="discount">
					<option value="">Aucune</option>
					<option value="percentage" <?php if (isset($facture) && $facture->getDiscount() == 'percentage') echo "selected"; ?>>Pourcentage</option>
					<option value="amount" <?php if (isset($facture) && $facture->getDiscount() == 'amount') echo "selected"; ?>>Montant</option>
				</select>
			</div>
		</div>

		<div class="col-md-4 discount_val" <?php if (isset($facture) && $facture->getDiscount() != '') echo 'style="display:block"'; ?>>
			<div class="form-group">
				<label>Valeur réduction</label>
				<input type="number" step="any" class="form-control" name="discount_val" value="<?php if (isset($facture)) echo $facture->getDiscountVal(); ?>">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Statu</label>
				<select class="select" name="statu">
					<option value="0" <?php if (isset($facture) && $facture->getStatu() == 0) echo "selected"; ?>>Impayé</option>
					<option value="1" <?php if (isset($facture) && $facture->getStatu() == 1) echo "selected"; ?>>Payé</option>
					<option value="2" <?php if (isset($facture) && $facture->getStatu() == 2) echo "selected"; ?>>Payé partialement</option>
					<option value="3" <?php if (isset($facture) && $facture->getStatu() == 3) echo "selected"; ?>>Litige</option>
				</select>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Langue</label>
				<select class="select" name="langue">
					<option value="fr" <?php if (isset($facture) && $facture->getLangue() == 'fr') echo "selected"; ?>>Français</option>
					<option value="en" <?php if (isset($facture) && $facture->getLangue() == 'en') echo "selected"; ?>>Anglais</option>
				</select>
			</div>
		</div>

	

	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label>Condition de paiement</label>
				<textarea name="condition_paiment" id="condition_paiment"><?php if (isset($facture)) echo $facture->getConditionPaiment(); else  echo $agence->getConditionDePaiement();?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('condition_paiment', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remarque" class="form-control" rows="2"><?php if (isset($facture)) echo $facture->getRemarque(); ?></textarea>
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque pour moi</label>
				<textarea name="remarque_moi" id="remarque_moi" class="form-control"><?php if (isset($facture)) echo $facture->getRemarqueMoi(); ?></textarea>
				<script type="text/javascript">
                    CKEDITOR.replace('remarque_moi', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Informations Complémentaires</label>
				<textarea name="additional_information" class="form-control" rows="2"><?php if (isset($facture)) echo $facture->getAdditionalInformation(); ?></textarea>
			</div>
		</div>


		<div class="myResponsivTable mt-4 mb-4">
			<table class="table table-stripped table-center table-hover">
				<thead>
					<tr>
						<th width="100">Ordre</th>
						<th>Service</th>
						<th>Quantité</th>
						<th>Réduction</th>
						<th>Prix</th>
						<th>Unité</th>
						<th>Total</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (isset($facture) || isset($factureavoir)) : ?>
						<?php foreach ($facture->getItems() as $item_facture) : ?>
							<tr>
								<td>
									<input type="number" name="ordre[]" value="<?php echo $item_facture->getOrdre(); ?>" class="form-control">
								</td>
								<td>
									<select class="chosen-select service-select" name="id_service[]" style="width:500px;" required>
									    <option value="" selected>Sélectionner</option>
										<?php foreach ($services as $service) : ?>
											<?php $sl = $item_facture->getService()->getId() == $service->getId() ? "selected" : ""; ?>
											<?php $selectedValue = $item_facture->getService()->getId() == $service->getId() ? $item_facture->getTitre() : $service->getTitre(); ?>
											<option data-title="<?=$item_facture->getService()->getId() == $service->getId() ? $item_facture->getTitre() : $service->getTitre()?>"  data-description="<?=$item_facture->getService()->getId() == $service->getId() ? $item_facture->getDescription() : $service->getDescription()?>" value="<?php echo $service->getId() ?>" <?php echo $sl; ?>><?= $selectedValue ?></option>
										<?php endforeach; ?>
									</select>
									<input type="hidden" name="item_facture_service_title[]" value="<?=$item_facture->getTitre()?>">
									<input type="hidden" name="item_facture_service_description[]" value="<?=$item_facture->getDescription()?>">
								</td>
								<td>
									<input type="number" name="qte[]" value="<?php echo $item_facture->getQte(); ?>" class="form-control qte-input">
								</td>
								<td>
									<input type="number" min="0" max="100" name="rms[]"  value="<?php echo $item_facture->getDiscount() > 0 ? $item_facture->getDiscount() : 0; ?>" class="form-control discount-input">
								</td>
								<td>
									<input type="number" step="any" name="prix[]" value="<?php echo $item_facture->getPrix(); ?>" class="form-control price-input" style="width:100px;">
								</td>
								<td>
								    <select class="chosen-select" name="unite[]" style="width:300px;" required>
									    <option value="" selected>Sélectionner</option>
										<?php
										    $unities = getUnities()[isset($facture) ? $facture->getLangue() : 'fr'];
										    foreach ($unities as $key=> $value) : ?>
											<option value="<?php echo $key ?>" <?=$key == $item_facture->getUnite() ? 'selected' : null?>><?= $value ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input type="number" step="any" name="soustotal[]" value="<?php echo $item_facture->getTotal(); ?>" class="form-control total-input" style="width:100px;" disabled>
								</td>
								<td class="add-remove text-right">
									<input type="hidden" name="item_id[]" value="<?php echo $item_facture->getId(); ?>" class="id-item-input">
									<?php if (!isset($factureavoir)) : ?><i class="fas fa-brush custom-row" data-toggle="tooltip" data-placement="top" data-original-title="Personnaliser" data-id="<?php echo $item_facture->getId(); ?>"></i><?php endif; ?>
									<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une ligne"></i>
									<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne" <?php if (!isset($factureavoir)) : ?>data-id="<?php echo $item_facture->getId(); ?>" <?php endif; ?>></i>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td></td>
							<td>
								<select class="chosen-select service-select" name="id_service[]" style="width:500px;">
								    <option value="" selected>Sélectionner</option>
									<?php foreach ($services as $service) : ?>
										<option  data-title="<?=$service->getTitre()?>"  data-description="<?=$service->getDescription()?>" value="<?php echo $service->getId() ?>"><?php echo $service->getTitre(); ?></option>
									<?php endforeach; ?>
								</select>
								<input type="hidden" name="item_facture_service_title[]" value="">
								<input type="hidden" name="item_facture_service_description[]" value="">
							</td>
							<td>
								<input type="number" name="qte[]" value="1" class="form-control qte-input">
							</td>
							<td>
								<input type="number" min="0" max="100" name="rms[]" value="0" class="form-control discount-input">
							</td>
							<td>
								<input type="number" step="any" name="prix[]" value="<?php echo $services[0]->getPrix(); ?>" class="form-control price-input" style="width:100px;">
							</td>
							<td>
							    <select class="chosen-select" name="unite[]" style="width:300px;" required>
								    <option value="" selected>Sélectionner</option>
									<?php
									    $unities = getUnities()[isset($facture) ? $facture->getLangue() : 'fr'];
									    foreach ($unities as $key=>  $value) : ?>
										<option value="<?php echo $key ?>" ><?= $value ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td>
								<input type="number" step="any" name="soustotal[]" value="<?php echo $services[0]->getPrix(); ?>" class="form-control total-input" style="width:100px;" disabled>
							</td>
							<td class="add-remove text-right">
								<input type="hidden" name="item_id[]" value="0" class="id-item-input">
								<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
								<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne"></i>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<div class="col-md-2">
			<div class="form-group">
				<label>Total</label>
				<input type="number" step="any" class="form-control" disabled name="total" value="<?php if (isset($facture)) echo $facture->getTotal(); ?>">
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Devise</label>
				<select class="select" name="devise">
					<option value="DH" <?php if (isset($facture) && $facture->getDevise() == 'DH') echo "selected"; ?>>MAD (DH)</option>
					<option value="€" <?php if (isset($facture) && $facture->getDevise() == '€') echo "selected"; ?>>Euro (€)</option>
					<option value="$" <?php if (isset($facture) && $facture->getDevise() == '$') echo "selected"; ?>>Dollar ($)</option>
					<option value="£" <?php if (isset($facture) && $facture->getDevise() == '£') echo "selected"; ?>>Pound (£)</option>
					<option value="AED" <?php if (isset($facture) && $facture->getDevise() == 'AED') echo "selected"; ?>>AED (DH)</option>
				</select>
			</div>
		</div>
		
			<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Proforma</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="proforma" class="toggle-switch-input" <?php if (isset($facture) && $facture->isProforma()) echo "checked"; ?>>
					<span class="toggle-switch-label mr-auto mt-2">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->
		
		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Afficher la signature</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="show_signature" value="1" class="toggle-switch-input" <?php if (isset($facture) && $facture->isShowSignature()) echo "checked"; ?>>
					<span class="toggle-switch-label mr-auto mt-2">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->
		
		<?php if (isset($facture) && !isset($factureavoir)) : ?>
			<input type="hidden" name="id" value="<?php echo $facture->getId(); ?>">
		<?php endif; ?>

		<?php if (isset($factureavoir)) : ?>
			<input type="hidden" name="avoir" value="1">
			<input type="hidden" name="id_facture" value="<?php echo $facture->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<!-- Add Category Modal -->
<div id="dialog-custom" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Personnaliser</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>
<!-- /Add Category Modal -->

<script>
	$(function() {

	    $(document).on("change", ".service-select", function() {
			var select = $(this);
			var id = select.val();
			var title = select.find(':selected').attr('data-title');
			var description = select.find(':selected').attr('data-description');
			var qte = select.parent().parent().find(".qte-input").val();
			var discount = select.parent().parent().find(".discount-input").val();
			var id_item = select.parent().parent().find(".id-item-input").val();
			var order = 'id=' + id + '&id_item=' + id_item;
			$.post("components/com_facture/controleurs/router.php?task=getServicePrice", order, function(theResponse) {
				var total = parseFloat(theResponse) * parseInt(qte)
				select.parent().parent().find(".price-input").val(theResponse);
				select.parent().parent().find(".total-input").val(total);
				select.parent().parent().find('input[name="item_facture_service_title[]"]').val(title)
				select.parent().parent().find('input[name="item_facture_service_description[]"]').val(description)
			});
			$.post("components/com_facture/controleurs/router.php?task=getServiceUnite", order, function(theResponse2) {
				select.parent().parent().find(".unite-input").val(theResponse2);
			});
		})

		$(document).on("keyup change", ".qte-input", function() {
			var input = $(this);
			var discount = input.parent().parent().find(".discount-input").val();
			var qte = input.val();
			var prix = input.parent().parent().find(".price-input").val();
			var subtotal = prix * qte
			var discountTotal = (parseFloat(discount) * subtotal)  / 100;
			var total = subtotal - discountTotal;
			input.parent().parent().find(".total-input").val(total);
		})
		
		$(document).on("keyup change", ".discount-input", function() {
			var input = $(this);
			var discount = input.val();
			var qte = input.parent().parent().find(".qte-input").val();
			var prix = input.parent().parent().find(".price-input").val();
			var subtotal = prix * parseInt(qte)
			var discountTotal = (parseFloat(discount) * subtotal)  / 100;
			var total = subtotal - discountTotal;
			input.parent().parent().find(".total-input").val(total);
		})

		$(document).on("keyup", ".price-input", function() {
		    var input = $(this);
			var discount = input.parent().parent().find(".discount-input").val();
			var qte = input.parent().parent().find(".qte-input").val();
			var prix = input.val();
			var subtotal = discount * prix
			var discountTotal = (parseFloat(discount) * subtotal)  / 100;
			var total = subtotal - discountTotal;
			input.parent().parent().find(".total-input").val(total);
		})

		$('.discount').change(function() {
			var select = $(this);
			var val = select.val();
			if (val != '') {
				$('.discount_val').fadeIn();
			} else {
				$('.discount_val').fadeOut();
			}
		})


		// envoi du formulaire en ajax
		$('form#factureForm').ajaxForm({
			beforeSubmit: function() {
				$("#factureForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse)
				// console.log(theResponse);
				$("#factureForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Facture ajoutée avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "facture modifiée avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#factureForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					<?php if (isset($facture)) : ?>
						setTimeout(function() {
							document.location.reload();
						}, 1500)
					<?php else : ?>
						setTimeout(function() {
							document.location = "index.php?option=com_facture";
						}, 1500)
					<?php endif; ?>

				} else if (parseInt(theResponse) === 0) {
					$('#factureForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#factureForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});

		$(document).on("click", ".add-row", function() {
			var $btn = $(this);
			let lang = $("select[name='langue']").val()
			var order = '';
			$.post("components/com_facture/controleurs/router.php?task=getRowFacture", order, function(theResponse) {
				$btn.parent().parent().after(theResponse);
				switchUnitiesByLangauge(lang)
			})
		})

		$(document).on("click", ".remove-row", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			let lang = $("select[name='langue']").val()
			if (confirm("Etes-vous sure !")) {
				var order = 'id=' + id;
				$.post("components/com_facture/controleurs/router.php?task=removeItemFacture", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().addClass("table-danger");
						switchUnitiesByLangauge(lang)
						setTimeout(function() {
							$btn.parent().parent().remove()
						}, 1000);

						$('#factureForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('#factureForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				})
			}
		})

		$(document).on("click", ".custom-row", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_facture/controleurs/router.php?task=customItemFacture", order, function(theResponse) {

				$(".modal-body").html(theResponse);
				$("#dialog-custom").modal('show');
			})
		})
	})
</script>