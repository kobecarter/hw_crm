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

<?php if(isset($devis) && $devis->getFacture()->getId() != 0): ?>
<div class="alert alert-warning"><i class="fa fa-info"></i> <strong>Attention !!</strong> vous ne pouvez pas modifier ce devis car il est déjà lié à une facture</div>
<?php endif; ?>

<form method="post" action="<?php echo $action; ?>" id="devisForm" enctype="multipart/form-data">
    <fieldset <?php if(isset($devis) && $devis->getFacture()->getId() != 0): ?>disabled="disabled"<?php endif; ?>>
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<div class="col-md-4">
			<div class="form-group">
				<label>Client</label>
				<a href="#0" class="text-success addClient" style="float:right;">+ client</a>
				<div class="client-box">
				<select class="chosen-select form-select form-control client-select" name="client" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($clients as $client) : ?>
						<?php $sl = isset($devis) && $devis->getClient()->getId() == $client->getId() ? "selected" : ""; ?>
						<option value="<?php echo $client->getId() ?>" <?php echo $sl; ?>><?php echo $client->getNom() . ' ' . $client->getPrenom() . ' - ' . $client->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
				</div>
			</div>
		</div>
		
	    <div class="col-md-4">
			<div class="form-group">
				<label>Banque</label>
				<select class="chosen-select form-select form-control" name="bank" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($banks as $bank) : ?>
						<?php $sl = isset($devis) && $devis->getBank() && $devis->getBank()->getId() == $bank->getId() ? "selected" : ""; ?>
						<option value="<?php echo $bank->getId() ?>" <?php echo $sl; ?>><?php echo $bank->getRaisonSociale() . ' ' . $bank->getRib(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Numéro</label>
				<input type="text" class="form-control" readonly name="numero" value="<?php if (isset($devis)) echo $devis->getNumero(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date devis</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_devis" value="<?php if (isset($devis)) echo normaldate($devis->getDateDevis());
																									else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Réduction</label>
				<select class="select discount" name="discount">
					<option value="">Aucune</option>
					<option value="percentage" <?php if (isset($devis) && $devis->getDiscount() == 'percentage') echo "selected"; ?>>Pourcentage</option>
					<option value="amount" <?php if (isset($devis) && $devis->getDiscount() == 'amount') echo "selected"; ?>>Montant</option>
				</select>
			</div>
		</div>

		<div class="col-md-3 discount_val" <?php if (isset($devis) && $devis->getDiscount() != '') echo 'style="display:block"'; ?>>
			<div class="form-group">
				<label>Valeur réduction</label>
				<input type="number" step="any" class="form-control" name="discount_val" value="<?php if (isset($devis)) echo $devis->getDiscountVal(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Statu</label>
				<select class="select" name="statu" id="status-select">
					<option value="0" <?php if (isset($devis) && $devis->getStatu() == 0) echo "selected"; ?>>Brouillon</option>
					<option value="1" <?php if (isset($devis) && $devis->getStatu() == 1) echo "selected"; ?>>Envoyé</option>
					<option value="2" <?php if (isset($devis) && $devis->getStatu() == 2) echo "selected"; ?>>Accepté</option>
					<option value="3" <?php if (isset($devis) && $devis->getStatu() == 3) echo "selected"; ?>>Contrat en attente de signature</option>
					<option value="4" <?php if (isset($devis) && $devis->getStatu() == 4) echo "selected"; ?>>Paiement effectué</option>
					<option value="5" <?php if (isset($devis) && $devis->getStatu() == 5) echo "selected"; ?>>Devis Refusé</option>
				</select>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Langue</label>
				<select class="select" name="langue">
					<option value="fr" <?php if (isset($devis) && $devis->getLangue() == 'fr') echo "selected"; ?>>Français</option>
					<option value="en" <?php if (isset($devis) && $devis->getLangue() == 'en') echo "selected"; elseif($_SESSION['langue'] == 'en') echo "selected"; ?>>Anglais</option>
				</select>
			</div>
		</div>

	</div>
	<div class="row">

		<div class="col-md-12" style="background:#E9E9E9;padding-top: 10px;border-radius: 5px;margin-bottom: 10px;">
			<div class="form-group">
				<label>Conditions de paiement</label>

				<div class="myResponsivTable mb-4">
					<table class="table table-stripped table-center table-hover">
						<thead>
							<tr>
								<th>Montant/Pourcentage</th>
								<th>Condition</th>
								<th>Date rappel</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (isset($devis) && sizeof($devis->getConditions()) > 0) : ?>
								<?php foreach ($devis->getConditions() as $condition) : ?>	
								<tr>
									<td><input type="text" name="amount[]" value="<?php echo $condition->getMontant(); ?>" class="form-control amount-input" required></td>
									<td><input type="text" name="condition[]" value="<?php echo $condition->getCondition(); ?>" class="form-control condition-input" required></td>
									<td><input type="date" name="date_rappel[]" value="<?php echo $condition->getDate(); ?>" class="form-control date-input" required></td>
									<td>
										<i class="fas fa-plus-circle add-row-condition" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
										<i class="fas fa-minus-circle remove-row-condition" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne" data-id="<?php echo $condition->getId(); ?>"></i>
									</td>
								</tr>
								<input type="hidden" name="condition_id[]" value="<?php echo $condition->getId(); ?>" class="id-condition-input">
								<?php endforeach; ?>
							<?php else: ?>	
							<tr>
								<td><input type="text" name="amount[]" value="" class="form-control amount-input" required></td>
								<td><input type="text" name="condition[]" value="" class="form-control condition-input" required></td>
								<td><input type="date" name="date_rappel[]" value="" class="form-control date-input" required></td>
								<td>
									<i class="fas fa-plus-circle add-row-condition" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
									<i class="fas fa-minus-circle remove-row-condition" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne"></i>
								</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<textarea name="condition_paiment" id="condition_paiment"><?php if (isset($devis)) echo $devis->getConditionPaiment(); ?></textarea>
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
				<textarea name="remarque" class="form-control" rows="2"><?php if (isset($devis)) echo $devis->getRemarque(); ?></textarea>
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
					<?php if (isset($devis)) : ?>
						<?php foreach ($devis->getItems() as $item_devis) : ?>
						 <?php $discount = $item_devis->getDiscount()>0 ? ($item_devis->getDiscount() * $item_devis->getTotal()) / 100 : 0;?>
							<tr>
								<td>
									<input type="number" name="ordre[]" value="<?php echo $item_devis->getOrdre(); ?>" class="form-control">
								</td>
								<td>
									<select class="chosen-select service-select" name="id_service[]" style="width:500px;" required>
									    <option value="" selected>Sélectionner</option>
										<?php foreach ($services as $service) : ?>
											<?php $sl = $item_devis->getService()->getId() == $service->getId() ? "selected" : ""; ?>
											<?php $selectedValue = $item_devis->getService()->getId() == $service->getId() ? $item_devis->getTitre() : $service->getTitre(); ?>
											<option data-title="<?=$item_devis->getService()->getId() == $service->getId() ? $item_devis->getTitre() : $service->getTitre()?>"  data-description="<?=$item_devis->getService()->getId() == $service->getId() ? addslashes($item_devis->getDescription()) : addslashes($service->getDescription())?>" value="<?php echo $service->getId() ?>" <?php echo $sl; ?>><?= $selectedValue ?></option>
										<?php endforeach; ?>
									</select>
									<input type="hidden" name="item_devis_service_title[]" value="<?=$item_devis->getTitre()?>">
									<input type="hidden" name="item_devis_service_description[]" value="<?=$item_devis->getDescription()?>">
								</td>
								<td>
									<input type="number" name="qte[]" value="<?php echo $item_devis->getQte(); ?>" class="form-control qte-input">
								</td>
								<td>
									<input type="number" min="0" max="100" name="rms[]"  value="<?php echo $item_devis->getDiscount() > 0 ? $item_devis->getDiscount() : 0; ?>" class="form-control discount-input">
								</td>
								<td>
									<input type="number" step="any" name="prix[]" value="<?php echo $item_devis->getPrix(); ?>" class="form-control price-input" style="width:100px;">
								</td>
								<td>
								    <select class="chosen-select unite-input" name="unite[]" style="width:300px;" required>
									    <option value="" selected>Sélectionner</option>
										<?php
										    $unities = getUnities()[isset($devis) ? $devis->getLangue() : $_SESSION['langue']];
										    foreach ($unities as $key=> $value) : ?>
											<option value="<?php echo $key ?>" <?=$key == $item_devis->getUnite() ? 'selected' : null?>><?= $value ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input type="number" step="any" name="soustotal[]" value="<?php echo $item_devis->getTotal() - $discount; ?>" class="form-control total-input" style="width:100px;" disabled>
								</td>
								<td class="add-remove text-right">
									<input type="hidden" name="item_id[]" value="<?php echo $item_devis->getId(); ?>" class="id-item-input">
									<i class="fas fa-brush custom-row" data-toggle="tooltip" data-placement="top" data-original-title="Personnaliser" data-id="<?php echo $item_devis->getId(); ?>"></i>
									<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une ligne"></i>
									<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne" data-id="<?php echo $item_devis->getId(); ?>"></i>
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
								<input type="hidden" name="item_devis_service_title[]" value="">
								<input type="hidden" name="item_devis_service_description[]" value="">
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
							    <select class="chosen-select unite-input" name="unite[]" style="width:300px;" required>
								    <option value="" selected>Sélectionner</option>
									<?php
									    $unities = getUnities()[isset($devis) ? $devis->getLangue() : $_SESSION['langue']];
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
				<input type="number" step="any" class="form-control" disabled name="total" value="<?php if (isset($devis)) echo $devis->getTotal(); ?>">
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Devise</label>
				<select class="select" name="devise">
					<option value="DH" <?php if (isset($devis) && $devis->getDevise() == 'DH') echo "selected"; ?>>MAD (DH)</option>
					<option value="€" <?php if (isset($devis) && $devis->getDevise() == '€') echo "selected"; ?>>Euro (€)</option>
					<option value="$" <?php if (isset($devis) && $devis->getDevise() == '$') echo "selected"; ?>>Dollar ($)</option>
					<option value="£" <?php if (isset($devis) && $devis->getDevise() == '£') echo "selected"; ?>>Pound (£)</option>
					<option value="AED" <?php if (isset($devis) && $devis->getDevise() == 'AED') echo "selected"; elseif($_SESSION['agence'] == 2) echo "selected"; ?>>AED (DH)</option>
				</select>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Taux TVA</label>
				<input type="number" step="any" class="form-control" name="tva" value="<?php if (isset($devis)) echo $devis->getTauxTVA(); ?>">
			</div>
		</div>
		
		<!-- Toggle Switch -->
		<div class="col-md-1">
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Proforma</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="proforma" class="toggle-switch-input" <?php if (isset($devis) && $devis->isProforma()) echo "checked"; ?>>
					<span class="toggle-switch-label mr-auto mt-2">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->
		
		<!-- Toggle Switch -->
		<div class="col-md-1">
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Package</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="pack" class="toggle-switch-input" <?php if (isset($devis) && $devis->isPack()) echo "checked"; ?>>
					<span class="toggle-switch-label mr-auto mt-2">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->
		<?php 
		$contract = new contract();
		if(isset($devis)){
			$contract = contract::findByDevis($devis->getId(),$_SESSION['agence'],$devis->getLangue());
		}
	if((isset($devis) && !$contract->getId()) || !isset($devis)) :?>
		<div class="col-12">
			<hr/>
		</div>
		<!-- Toggle Switch -->
		<div class="col-md-12">
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-dark">Etes-vous sûr de vouloir générer un contrat pour ce devis ?</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="generate_contract" class="toggle-switch-input" value="1">
					<span class="toggle-switch-label mr-auto mt-2">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->
		<div class="selection-contract hidden px-3">
			<div class="row">
				<div class="col-12">
					<h2 >Contract</h2>
				</div>
				<div class="col-12">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Titre <span class="text-danger titre_contract d-none"> * </span></label>
									<input type="text" class="form-control" name="titre_contract" value="">
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label>Duration <span class="text-danger duration_contract d-none"> * </span></label>
									<input type="text" class="form-control" name="duration_contract" value="">
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label>Garantie <span class="text-danger garantie_contract d-none"> * </span></label>
									<input type="text" class="form-control" name="garantie_contract" value="">
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label>Ville <span class="text-danger ville_contract d-none"> * </span></label>
									<input type="text" class="form-control" name="ville_contract" value="">
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label>Date <span class="text-danger date_contract d-none"> * </span></label>
									<div class="cal-icon">
										<input type="text" class="form-control datetimepicker" name="date_contract" value="<?php echo date('d/m/Y'); ?>">
									</div>
								</div>
							</div>
						
							<div class="col-md-4">
								<div class="form-group">
									<label>Tribunal <span class="text-danger tribunal_contract d-none"> * </span></label>
									<input type="text" class="form-control" name="tribunal_contract" value="">
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label>Nombre de paiement <span class="text-danger nombre_de_paiement_contract d-none"> * </span></label>
									<input type="number" min="0" placeholder="0" class="form-control" name="nombre_de_paiement_contract" value="" >
								</div>
							</div>
							
							<!-- Toggle Switch -->
                    		<div class="col-md-3">
                    			<label class="row form-group toggle-switch">
                    				<span class="col-12 toggle-switch-content ml-0">
                    					<span class="d-block text-dark">Afficher la signature</span>
                    				</span>
                    				<span class="col-12">
                    					<input type="checkbox" name="show_signature_contract" value="1" class="toggle-switch-input">
                    					<span class="toggle-switch-label mr-auto mt-2">
                    						<span class="toggle-switch-indicator"></span>
                    					</span>
                    				</span>
                    			</label>
                    		</div>
                    		<!-- /Toggle Switch -->
						</div>
				</div>
			</div>
		</div>
		<?php endif;?>
		<?php if($contract->getId()) :?>
			<div class="col-12">
				<span class="text-success">Ce devis est déjà associé à un contrat. Veuillez essayer d'y accéder en cliquant sur ce bouton.</span> <a target="_blank" href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?= $contract->getId(); ?>" class="btn btn-primary btn-white mr-2" data-toggle="tooltip" data-placement="top" data-original-title="PDF"><i class="fa fa-file"></i> Contrat</a> 
			</div>
		<?php endif;?>
		<?php if (isset($devis)) : ?>
			<input type="hidden" name="id" value="<?php echo $devis->getId(); ?>">
		<?php endif; ?>
	</div>
	
	<?php if(!isset($devis) || $devis->getFacture()->getId() == 0): ?>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
	<?php endif; ?>
	</fieldset>
</form>

<!-- Add Prestation Modal -->
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
<!-- /Add Prestation Modal -->

<!-- Add Client Modal -->
<div id="dialog-client" class="modal client-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Ajouter client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>
<!-- /Add Client Modal -->

<script>
	$(function() {
		
		// Check is contrat signed
		$(document).on("change", "#status-select", function(e) {
			var select = $(this);
			var status = select.val();
			var order = 'status=' + status + '&id=<?php if (isset($devis)) echo $devis->getId(); ?>';
			$.post("components/com_devis/controleurs/router.php?task=checkContrat", order, function(theResponse) {
				if (parseInt(theResponse) == 1) {
					e.preventDefault();
					alert("Attention! Veuillez signer le contrat");
				}
				if (parseInt(theResponse) == 2) {
					e.preventDefault();
					alert("Ce devis est associé à un contrat signé, vous ne pouvez pas changer son statut");
				}
			});
		})

		// Création factures à partir du devis
		$(document).on( "click", ".create-invoice", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_devis/controleurs/router.php?task=createInvoice", order, function (theResponse) {
					msgsucces = "Facture créée avec succès";
					if (parseInt(theResponse) == 1) {
						
						$('#devisForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
						setTimeout(function () {
							//location.href="index.php?option=com_facture"
							$btn.remove();
						},1000)
					}
					else {
						$('#devisForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})

		$(document).on("change", ".service-select", function() {
			var select = $(this);
			var id = select.val();
			var title = select.find(':selected').attr('data-title');
			var description = select.find(':selected').attr('data-description');
			var qte = select.parent().parent().find(".qte-input").val();
			var discount = select.parent().parent().find(".discount-input").val();
			var id_item = select.parent().parent().find(".id-item-input").val();
			var order = 'id=' + id + '&id_item=' + id_item;
			$.post("components/com_devis/controleurs/router.php?task=getServicePrice", order, function(theResponse) {
				var discountTotal = (parseFloat(discount) * parseFloat(theResponse)) / 100; 
				var total = (parseFloat(theResponse) * parseInt(qte)) - discountTotal;
				select.parent().parent().find(".price-input").val(theResponse);
				select.parent().parent().find(".total-input").val(total);
				select.parent().parent().find('input[name="item_devis_service_title[]"]').val(title);
				select.parent().parent().find('input[name="item_devis_service_description[]"]').val(description);
				//select.parent().parent().find('select[name="unite[]"]').val('day_man');
			});
			$.post("components/com_devis/controleurs/router.php?task=getServiceUnite", order, function(theResponse2) {
				select.parent().parent().find(".unite-input").val(theResponse2).change();
				select.parent().parent().find(".unite-input").select2();
				select.parent().parent().find(".unite-input").trigger("chosen:updated");
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
		$('form#devisForm').ajaxForm({
			beforeSubmit: function() {
				$("#devisForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
			    console.log(theResponse)
				$("#devisForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Devis ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "devis modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#devisForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					<?php if(isset($devis)): ?>
                    setTimeout(function() {
						document.location.reload();
					}, 1500)
                    <?php else: ?>
					setTimeout(function() {
						document.location = "index.php?option=com_devis";
					}, 1500)
					<?php endif; ?>

				} else if (parseInt(theResponse) === 0) {
					$('#devisForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}else if (parseInt(theResponse) === 2) {
					$('#devisForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez signer le contrat<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}else if (parseInt(theResponse) === 3) {
					$('#devisForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Ce devis est associé à un contrat signé, vous ne pouvez pas changer son statut<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#devisForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});

		$(document).on("click", ".add-row", function() {
			var $btn = $(this);
			let lang = $("select[name='langue']").val()
			var order = 'lang='+lang;
			$.post("components/com_devis/controleurs/router.php?task=getRowDevis", order, function(theResponse) {
				$btn.parent().parent().after(theResponse);
				switchUnitiesByLangauge(lang)
			})
		})

		$(document).on("click", ".remove-row", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			let lang = $("select[name='langue']").val();
			var nbr = $( ".remove-row" ).length;
			
			if(nbr > 1){
    			if (confirm("Etes-vous sure !")) {
    				var order = 'id=' + id;
    				$.post("components/com_devis/controleurs/router.php?task=removeItemDevis", order, function(theResponse) {
    					if (parseInt(theResponse) == 1) {
    
    						$btn.parent().parent().addClass("table-danger");
    						switchUnitiesByLangauge(lang)
    						setTimeout(function() {
    							$btn.parent().parent().remove()
    						}, 1000);
    
    						$('#devisForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
    					} else {
    						$('#devisForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
    					}
    				})
    			}
			}else{
			    alert("Impossible de supprimer le dernier élément");
			}
		})

		$(document).on("click", ".remove-row-condition", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			if (confirm("Etes-vous sure !")) {
				var order = 'id=' + id;
				$.post("components/com_devis/controleurs/router.php?task=removeCondition", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().remove()
						}, 1000);

						$('#devisForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('#devisForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				})
			}
		})

		$(document).on("click", ".custom-row", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_devis/controleurs/router.php?task=customItemDevis", order, function(theResponse) {
				$(".modal-body").html(theResponse);
				$("#dialog-custom").modal('show');
			})
		})

		$(document).on("change","input[name=generate_contract]",function(){
			let self = $(this)
			if(self.is(":checked")){
				$(".selection-contract").removeClass('hidden')
				$(".selection-contract").find('input[name="titre_contract"]').prop('required',true)
				$(".selection-contract").find('input[name="duration_contract"]').prop('required',true)
				$(".selection-contract").find('input[name="ville_contract"]').prop('required',true)
				$(".selection-contract").find('input[name="date_contract"]').prop('required',true)
				$(".selection-contract").find('input[name="tribunal_contract"]').prop('required',true)
				$(".selection-contract").find('input[name="nombre_de_paiement_contract"]').prop('required',true)
				$(".selection-contract").find('label span').removeClass('d-none')
				$(".selection-contract").find('label span.garantie_contract').addClass('d-none')
			}else{
				$(".selection-contract").addClass('hidden')
				$(".selection-contract").find('input[name="titre_contract"]').removeAttr('required')
				$(".selection-contract").find('input[name="duration_contract"]').removeAttr('required')
				$(".selection-contract").find('input[name="garantie_contract"]').removeAttr('required')
				$(".selection-contract").find('input[name="ville_contract"]').removeAttr('required')
				$(".selection-contract").find('input[name="date_contract"]').removeAttr('required')
				$(".selection-contract").find('input[name="tribunal_contract"]').removeAttr('required')
				$(".selection-contract").find('input[name="nombre_de_paiement_contract"]').removeAttr('required')
				$(".selection-contract").find('label span').addClass('d-none')
			}
		})

		$(document).on("click", ".add-row-condition", function() {
			var $btn = $(this);
			var order = '';
			$.post("components/com_devis/controleurs/router.php?task=getRowCondition", order, function(theResponse) {
				$btn.parent().parent().after(theResponse);
			})
		})
		
		$(document).on("click", ".addClient", function() {
			var order = '';
			$.post("components/com_client/controleurs/router.php?task=addClientForm", order, function(theResponse) {
				$("#dialog-client .modal-body").html(theResponse);
				$("#dialog-client").modal('show');
				
				$("#dialog-client select").select2();
				$("#dialog-client select").trigger("chosen:updated");
			})
		})
		
	})
</script>