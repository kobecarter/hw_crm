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
		<?php if (!isset($facture)): ?>
		<div class="col-md-12">
			<div class="form-group">
				<div id="iaDropzoneFacture" class="ia-dropzone">
					<input type="file" accept="application/pdf" style="display:none;">
					<div class="ia-dropzone-text">
						<i class="fas fa-file-pdf"></i> Glissez-déposez une présentation (PDF) ici, ou cliquez pour la sélectionner<br>
						<small>Les prestations détectées seront ajoutées automatiquement aux lignes de la facture.</small>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="col-md-6">
			<div class="form-group">
				<label>Client</label>
				<select class="chosen-select form-select form-control client-select" name="client" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($clients as $client) : ?>
						<?php $sl = isset($facture) ? ($facture->getClient()->getId() == $client->getId() ? "selected" : "") : (isset($preselectClientId) && $preselectClientId == $client->getId() ? "selected" : ""); ?>
						<option value="<?php echo $client->getId() ?>" data-agence="<?php echo $client->getAgence()->getId(); ?>" <?php echo $sl; ?>><?php echo $client->getNom() . ' ' . $client->getPrenom() . ' - ' . $client->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label>Banque</label>
				<select class="chosen-select form-select form-control bank-select" name="bank" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($banks as $bank) : ?>
						<?php $sl = isset($facture) && $facture->getBank() && $facture->getBank()->getId() == $bank->getId() ? "selected" : ""; ?>
						<?php $estPersoOption = stripos($bank->getRaisonSociale(), 'PERSO') !== false; ?>
						<option value="<?php echo $bank->getId() ?>" <?php echo $sl; ?><?php echo $estPersoOption ? ' data-perso="1"' : ''; ?>><?php echo $bank->getRaisonSociale() . ' ' . $bank->getRib(); ?></option>
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


		<div class="col-md-12">
			<label>Prestations</label>
			<a href="#0" class="text-success addServiceManual" style="float:right;">+ service</a>
		</div>

		<div class="myResponsivTable mt-4 mb-4">
			<table class="table table-stripped table-center table-hover facture-items-table">
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
								    <select class="chosen-select unite-input" name="unite[]" style="width:300px;" required>
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
									<i class="fas fa-magic ask-ai-item-row" data-toggle="tooltip" data-placement="top" data-original-title="Assistant IA"></i>
									<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une ligne"></i>
									<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne" <?php if (!isset($factureavoir)) : ?>data-id="<?php echo $item_facture->getId(); ?>" <?php endif; ?>></i>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td><input type="number" name="ordre[]" value="1" class="form-control"></td>
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
							    <select class="chosen-select unite-input" name="unite[]" style="width:300px;" required>
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
								<i class="fas fa-magic ask-ai-item-row" data-toggle="tooltip" data-placement="top" data-original-title="Assistant IA"></i>
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
				<?php
				// L'agence 2 (Dubai) ne facture qu'en AED : on ne propose que cette devise
				// dans ce cas. Si une facture existante a une autre devise (créée avant ce
				// changement, ou déplacée d'agence), on la garde visible pour ne pas la
				// perdre silencieusement à l'édition.
				$deviseLabels = array('DH' => 'MAD (DH)', '€' => 'Euro (€)', '$' => 'Dollar ($)', '£' => 'Pound (£)', 'AED' => 'AED (DH)');
				$currentDevise = isset($facture) ? $facture->getDevise() : '';
				$isDubaiAgence = $_SESSION['agence'] == 2;
				$deviseOptions = $isDubaiAgence ? array('AED') : array('DH', '€', '$', '£');
				if ($currentDevise !== '' && !in_array($currentDevise, $deviseOptions)) {
					$deviseOptions[] = $currentDevise;
				}
				?>
				<label>Devise</label>
				<select class="select" name="devise">
					<?php foreach ($deviseOptions as $val) : ?>
						<?php $sl = $currentDevise !== '' ? ($currentDevise == $val ? "selected" : "") : (!isset($facture) && $isDubaiAgence && $val == 'AED' ? "selected" : ""); ?>
						<option value="<?php echo $val; ?>" <?php echo $sl; ?>><?php echo isset($deviseLabels[$val]) ? $deviseLabels[$val] : $val; ?></option>
					<?php endforeach; ?>
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

		<?php if (!isset($facture)) : ?>
			<!-- Rempli par le JS si l'utilisateur choisit de réutiliser un dossier Drive existant
			     plutôt que d'en créer un nouveau (étape de validation "dossier similaire trouvé"). -->
			<input type="hidden" name="drive_folder_override" id="driveFolderOverride" value="">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<?php if (!isset($facture)) : ?>
<!-- Validation "dossier Drive similaire trouvé" à la création d'une facture : ne s'affiche QUE
     si un dossier au nom proche existe déjà sous le même pays/ville - sinon la facture se crée
     normalement, sans interruption (voir verifierDossierDriveClient() côté serveur). -->
<div id="dialog-drive-similaire" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fab fa-google-drive mr-1"></i> Dossier Drive similaire trouvé</h5>
			</div>
			<div class="modal-body">
				<p class="mb-2">Un ou plusieurs dossiers déjà présents dans Drive ressemblent au nom du dossier qui serait créé pour ce client (<strong id="driveNomPropose"></strong>) :</p>
				<div id="driveSimilairesListe" class="mb-3"></div>
				<p class="text-muted mb-0" style="font-size:0.85rem;">Choisissez le dossier existant s'il s'agit bien du même client, ou créez quand même un nouveau dossier s'il s'agit d'un client différent.</p>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-white" id="driveSimilaireAnnuler">Annuler</button>
				<button type="button" class="btn btn-outline-primary" id="driveSimilaireCreerQuandMeme">Créer un nouveau dossier quand même</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

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

<!-- Add Service Modal -->
<div id="dialog-service" class="modal service-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Ajouter service</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>
<!-- /Add Service Modal -->

<!-- IA Review Modal -->
<div id="dialog-ia-review" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="max-width: 900px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Vérifier les données détectées avant de les ajouter à la facture</h5>
			</div>
			<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
				<div class="form-group">
					<label>Langue de la facture</label>
					<select class="form-control" id="iaReviewLangue">
						<option value="fr">Français</option>
						<option value="en">Anglais</option>
					</select>
				</div>

				<label class="mt-3">Prestations détectées</label>
				<table class="table table-sm table-bordered" id="iaReviewServicesTable">
					<thead>
						<tr>
							<th>Détecté</th>
							<th>Qté</th>
							<th>Prix</th>
							<th>Unité</th>
							<th>Service à utiliser</th>
							<th>Assistant IA</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<p class="text-muted"><small>Aucune de ces données n'est ajoutée à la facture tant que vous n'avez pas cliqué sur "Valider et ajouter".</small></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id="iaReviewCancel">Ignorer</button>
				<button type="button" class="btn btn-primary" id="iaReviewConfirm">Valider et ajouter à la facture</button>
			</div>
		</div>
	</div>
</div>
<!-- /IA Review Modal -->

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


		<?php if (!isset($facture)) : ?>
		// Étape de validation "dossier Drive similaire" - uniquement à la CRÉATION d'une facture
		// (jamais en modification, le dossier a déjà été résolu à la création). driveCheckOk passe
		// à true une fois la vérification faite pour ce client (ou l'utilisateur ayant tranché),
		// pour ne relancer la vérification qu'une fois par client réellement sélectionné.
		var driveCheckOk = false;
		var driveCheckClientId = null;
		var driveSimilairesEnCours = [];

		function escHtmlDrive(s) {
			return $('<div>').text(s === undefined || s === null ? '' : s).html();
		}

		function afficherSimilairesDrive(nomPropose, similaires) {
			driveSimilairesEnCours = similaires;
			$('#driveNomPropose').text(nomPropose);
			var $zone = $('#driveSimilairesListe').empty();
			similaires.forEach(function (s, index) {
				var $carte = $(
					'<div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center py-2 flex-wrap">' +
					'<div class="mr-2"><a href="' + escHtmlDrive(s.lien) + '" target="_blank">' + escHtmlDrive(s.nom) + '</a>' +
					'<span class="badge badge-light ml-1">' + s.score + '% similaire</span></div>' +
					'<button type="button" class="btn btn-sm btn-success drive-utiliser-dossier" data-index="' + index + '">Utiliser ce dossier</button>' +
					'</div></div>'
				);
				$zone.append($carte);
			});
			$('#dialog-drive-similaire').modal('show');
		}

		$(document).on('click', '.drive-utiliser-dossier', function () {
			var index = $(this).data('index');
			var choisi = driveSimilairesEnCours[index];
			if (choisi) {
				$('#driveFolderOverride').val(choisi.id);
			}
			driveCheckOk = true;
			$('#dialog-drive-similaire').modal('hide');
			$('form#factureForm').submit();
		});

		$('#driveSimilaireCreerQuandMeme').on('click', function () {
			$('#driveFolderOverride').val('');
			driveCheckOk = true;
			$('#dialog-drive-similaire').modal('hide');
			$('form#factureForm').submit();
		});

		$('#driveSimilaireAnnuler').on('click', function () {
			$('#dialog-drive-similaire').modal('hide');
			$("#factureForm .submit").prop("disabled", false);
		});
		<?php endif; ?>

		// envoi du formulaire en ajax
		$('form#factureForm').ajaxForm({
			beforeSubmit: function() {
				<?php if (!isset($facture)) : ?>
				var idClientActuel = $('.client-select').val();
				if (!driveCheckOk || driveCheckClientId !== idClientActuel) {
					driveCheckOk = false;
					driveCheckClientId = idClientActuel;
					$("#factureForm .submit").prop("disabled", true);
					$("#factureForm .loading").css('display', 'inline-block');
					$.post('components/com_facture/controleurs/router.php?task=verifierDossierDriveClient', { client: idClientActuel }, function (response) {
						$("#factureForm .loading").fadeOut();
						if (response && response.configure && response.similaires && response.similaires.length) {
							$("#factureForm .submit").prop("disabled", false);
							afficherSimilairesDrive(response.nom_propose, response.similaires);
						} else {
							driveCheckOk = true;
							$('form#factureForm').submit();
						}
					}, 'json').fail(function () {
						// La vérification Drive ne doit jamais bloquer la création de la facture :
						// en cas d'erreur réseau, on laisse simplement passer sans validation.
						driveCheckOk = true;
						$('form#factureForm').submit();
					});
					return false;
				}
				<?php endif; ?>
				$("#factureForm .submit").prop("disabled", true);
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
                    $('#factureForm .submit').prop('disabled', false);
				} else {
					$('#factureForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    $('#factureForm .submit').prop('disabled', false);
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

				$("#dialog-custom").modal('show');
				$("#dialog-custom").one('shown.bs.modal', function() {
					$("#dialog-custom .modal-body").html(theResponse);
				});
			})
		})

		$(document).on("click", ".addServiceManual", function() {
			openIaServiceModal({
				modalSelector: '#dialog-service',
				targetRow: $(".facture-items-table tbody tr").last()
			});
		});

		<?php if (!isset($facture)): ?>
		initIaDropzone({
			zoneSelector: '#iaDropzoneFacture',
			context: 'facture',
			onExtracted: function (response) {
				showIaReviewPanel(response.extracted);
			}
		});

		function showIaReviewPanel(extracted) {
			var services = extracted.services || [];
			if (!services.length) {
				return;
			}

			var suggestedLangue = suggestLangueFromPays(extracted.client && extracted.client.pays) || $("select[name='langue']").val() || 'fr';
			$("#iaReviewLangue").val(suggestedLangue);

			var serviceOptionsHtml = $(".facture-items-table .service-select").first().html() || '';
			serviceOptionsHtml = serviceOptionsHtml.replace('<option value="" selected>Sélectionner</option>', '');

			var uniteOptionsHtml = $(".facture-items-table .unite-input").first().html() || '';

			var servicesBody = $("#iaReviewServicesTable tbody").empty();
			services.forEach(function (svc) {
				var preselect = svc.id_service || (svc.suggested_service ? svc.suggested_service.id_service : '');
				var uniteInitiale = svc.unite || (svc.suggested_service ? svc.suggested_service.unite : '') || '';
				var row = $(
					'<tr>' +
					'<td>' + $('<div>').text(svc.titre || '(sans titre)').html() + '</td>' +
					'<td><input type="number" class="form-control form-control-sm ia-review-qte" value="' + (svc.qte || 1) + '"></td>' +
					'<td><input type="number" step="any" class="form-control form-control-sm ia-review-prix" value="' + (svc.prix || 0) + '"></td>' +
					'<td><select class="form-control form-control-sm ia-review-unite">' + uniteOptionsHtml + '</select></td>' +
					'<td><select class="form-control form-control-sm ia-review-service-select"><option value="">— Sélectionner —</option>' + serviceOptionsHtml + '<option value="__CREATE_NEW__">➕ Créer un nouveau service</option></select></td>' +
					'<td><button type="button" class="btn btn-sm btn-outline-primary ia-review-ask-ai">✨ IA</button></td>' +
					'</tr>' +
					'<tr class="ia-review-ai-row" style="display:none;"><td colspan="6">' +
					'<div class="form-group mb-1"><input type="text" class="form-control form-control-sm ia-review-ai-question" placeholder="Demandez par ex. : rédige une description pour ce service"></div>' +
					'<button type="button" class="btn btn-sm btn-primary ia-review-ai-send">Envoyer</button>' +
					'<div class="ia-review-ai-result mt-2"></div>' +
					'</td></tr>'
				);
				row.data('svc', svc);
				row.find('.ia-review-service-select').val(preselect || '__CREATE_NEW__');
				if (uniteInitiale) {
					row.find('.ia-review-unite').val(uniteInitiale);
				}
				servicesBody.append(row);
			});

			$("#dialog-ia-review").modal('show');
		}

		$("#iaReviewCancel").on('click', function () {
			$("#dialog-ia-review").modal('hide');
		});

		$(document).on('click', '.ia-review-ask-ai', function () {
			$(this).closest('tr').next('.ia-review-ai-row').toggle();
		});

		$(document).on('click', '.ia-review-ai-send', function () {
			var $btn = $(this);
			var aiRow = $btn.closest('tr');
			var mainRow = aiRow.prev('tr');
			var question = aiRow.find('.ia-review-ai-question').val().trim();
			if (!question) {
				return;
			}
			var svc = mainRow.data('svc');
			var chosenServiceId = mainRow.find('.ia-review-service-select').val();
			var idServiceForChat = (chosenServiceId && chosenServiceId !== '__CREATE_NEW__') ? chosenServiceId : 0;
			$btn.prop('disabled', true).text('Analyse en cours...');

			$.post("components/com_ia/controleurs/router.php?task=chatServiceAssistant", {
				id_service: idServiceForChat,
				titre: svc.titre || '',
				description: mainRow.data('aiDescription') || '',
				message: question
			}, function (response) {
				$btn.prop('disabled', false).text('Envoyer');
				if (!response.success) {
					aiRow.find('.ia-review-ai-result').html('<div class="alert alert-danger">' + response.message + '</div>');
					return;
				}
				if (response.intent === 'update_description') {
					mainRow.data('aiDescription', response.proposed_description);
					aiRow.find('.ia-review-ai-result').html(
						'<div class="alert alert-success">Description enregistrée pour ce service :</div>' +
						'<textarea class="form-control ia-review-ai-description" rows="4">' + response.proposed_description + '</textarea>'
					);
				} else if (response.intent === 'scan_website') {
					var pages = response.pages || [];
					if (!pages.length) {
						aiRow.find('.ia-review-ai-result').html('<div class="alert alert-warning">Aucune page détectée sur ' + response.url + '.</div>');
						return;
					}
					var pagesLine = buildPagesLine(pages);
					var pagesText = mergePagesLine(mainRow.data('aiDescription') || '', pagesLine);
					mainRow.data('aiDescription', pagesText);
					aiRow.find('.ia-review-ai-result').html(
						'<div class="alert alert-success">Texte proposé pour la description de cette ligne (pages détectées sur ' + response.url + ') :</div>' +
						'<textarea class="form-control ia-review-ai-description" rows="6">' + pagesText + '</textarea>'
					);
				} else if (response.intent === 'need_url') {
					aiRow.find('.ia-review-ai-result').html('<div class="alert alert-warning">Merci de préciser l\'URL du site à scanner dans votre message.</div>');
				} else {
					aiRow.find('.ia-review-ai-result').html('<div class="alert alert-warning">' + (response.message || "Je n'ai pas compris la demande.") + '</div>');
				}
			}).fail(function () {
				$btn.prop('disabled', false).text('Envoyer');
				aiRow.find('.ia-review-ai-result').html('<div class="alert alert-danger">Erreur réseau.</div>');
			});
		});

		$(document).on('change', '.ia-review-ai-description', function () {
			$(this).closest('.ia-review-ai-row').prev('tr').data('aiDescription', $(this).val());
		});

		$("#iaReviewConfirm").on('click', function () {
			var langue = $("#iaReviewLangue").val();
			$("select[name='langue']").val(langue).trigger('change');

			var validatedServices = [];
			$("#iaReviewServicesTable tbody tr").has('.ia-review-service-select').each(function () {
				var row = $(this);
				var svc = row.data('svc');
				var chosen = row.find('.ia-review-service-select').val();
				validatedServices.push({
					titre: svc.titre,
					unite: row.find('.ia-review-unite').val() || svc.unite,
					qte: row.find('.ia-review-qte').val(),
					description: row.data('aiDescription') || '',
					prix: row.find('.ia-review-prix').val(),
					id_service: (chosen && chosen !== '__CREATE_NEW__') ? chosen : null
				});
			});

			$("#dialog-ia-review").modal('hide');

			if (validatedServices.length) {
				processIaFactureServices(validatedServices, 0);
			}
		});

		function processIaFactureServices(services, index) {
			if (index >= services.length) {
				return;
			}
			var svc = services[index];

			function applyToRow(row) {
				row.find('input[name="ordre[]"]').val(index + 1);
				if (svc.id_service) {
					row.find(".service-select").val(svc.id_service).trigger('change');
					setTimeout(function () {
						if (svc.qte) { row.find(".qte-input").val(svc.qte); }
						if (svc.prix) { row.find(".price-input").val(svc.prix); }
						if (svc.unite) { row.find(".unite-input").val(svc.unite).trigger('change'); }
						if (svc.description) { row.find('input[name="item_facture_service_description[]"]').val(svc.description); }
						row.find(".qte-input").trigger('change');
						processIaFactureServices(services, index + 1);
					}, 700);
				} else {
					openIaServiceModal({
						modalSelector: '#dialog-service',
						targetRow: row,
						prefillTitre: svc.titre,
						prefillPrix: svc.prix,
						prefillUnite: svc.unite,
						prefillDescription: svc.description,
						onCreated: function () {
							setTimeout(function () {
								if (svc.qte) { row.find(".qte-input").val(svc.qte); }
								if (svc.prix) { row.find(".price-input").val(svc.prix); }
								row.find(".qte-input").trigger('change');
								processIaFactureServices(services, index + 1);
							}, 700);
						}
					});
				}
			}

			if (index === 0) {
				applyToRow($(".facture-items-table tbody tr").first());
			} else {
				var lastRow = $(".facture-items-table tbody tr").last();
				$.post("components/com_facture/controleurs/router.php?task=getRowFacture", '', function (theResponse) {
					lastRow.after(theResponse);
					applyToRow(lastRow.next());
				});
			}
		}
		<?php endif; ?>
	})
</script>