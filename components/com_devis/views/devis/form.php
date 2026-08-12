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

<?php if(isset($devis) && $devis->getFacture()->getId() != 0 && $devis->getStatu() == 4): ?>
<div class="alert alert-warning"><i class="fa fa-info"></i> <strong>Attention !!</strong> vous ne pouvez pas modifier ce devis car il est déjà lié à une facture</div>
<?php endif; ?>

<?php
$devisFactureLiee = isset($devis) ? facture::findByDevis($devis->getId(), $_SESSION['agence']) : false;
$devisResteAPayer = ($devisFactureLiee && $devisFactureLiee->getId()) ? $devisFactureLiee->getReste() : (isset($devis) ? $devis->getTotal() : 0);
?>

<form method="post" action="<?php echo $action; ?>" id="devisForm" enctype="multipart/form-data">
    <fieldset <?php if(isset($devis) && $devis->getFacture()->getId() != 0 && $devis->getStatu() == 4): ?>disabled="disabled"<?php endif; ?>>
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		<?php if (!isset($devis)): ?>
		<div class="col-md-12">
			<div class="form-group">
				<div id="iaDropzoneDevis" class="ia-dropzone">
					<input type="file" accept="application/pdf" style="display:none;">
					<div class="ia-dropzone-text">
						<i class="fas fa-file-pdf"></i> Glissez-déposez une présentation (PDF) ici, ou cliquez pour la sélectionner<br>
						<small>Les prestations détectées seront ajoutées automatiquement aux lignes du devis.</small>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="col-md-4">
			<div class="form-group">
				<label>Client</label>
				<a href="#0" class="text-success addClient" style="float:right;">+ client</a>
				<div class="client-box">
				<select class="chosen-select form-select form-control client-select" name="client" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($clients as $client) : ?>
						<?php $sl = isset($devis) ? ($devis->getClient()->getId() == $client->getId() ? "selected" : "") : (isset($preselectClientId) && $preselectClientId == $client->getId() ? "selected" : ""); ?>
						<option value="<?php echo $client->getId() ?>" data-agence="<?php echo $client->getAgence()->getId(); ?>" <?php echo $sl; ?>><?php echo $client->getNom() . ' ' . $client->getPrenom() . ' - ' . $client->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
				</div>
			</div>
		</div>
		
	    <div class="col-md-4">
			<div class="form-group">
				<label>Banque</label>
				<select class="chosen-select form-select form-control bank-select" name="bank" required>
				    <option value="" selected disabled>Sélectionner</option>
					<?php foreach ($banks as $bank) : ?>
						<?php $sl = isset($devis) && $devis->getBank() && $devis->getBank()->getId() == $bank->getId() ? "selected" : ""; ?>
						<?php $estPersoOption = stripos($bank->getRaisonSociale(), 'PERSO') !== false; ?>
						<option value="<?php echo $bank->getId() ?>" <?php echo $sl; ?><?php echo $estPersoOption ? ' data-perso="1"' : ''; ?>><?php echo $bank->getRaisonSociale() . ' ' . $bank->getRib(); ?></option>
					<?php endforeach; ?>
				</select>
				<small class="text-muted">La liste se filtre selon l'agence du client sélectionné.</small>
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
				<label>Statu <span id="devis-status-badge" class="badge"></span></label>
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
		<div class="col-md-12 devis-payment-box" id="devis-payment-box" style="background:#E9E9E9;padding-top: 10px;border-radius: 5px;margin-bottom: 10px;display:none;">
			<div class="form-group">
				<label><i class="fa fa-money-bill-alt"></i> Paiement</label>
				<div class="alert alert-info mb-3">Ce paiement sera enregistré sur la facture de ce devis (elle sera créée automatiquement si elle n'existe pas encore).</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label>Montant réglé <span class="text-danger">*</span></label>
							<input class="form-control" type="number" step="any" name="payment_montant" id="payment_montant" <?php if (isset($devis)) echo 'data-reste="' . $devisResteAPayer . '"'; ?> value="">
							<?php if (isset($devis)): ?>
							<small class="text-muted">Reste à payer sur la facture : <strong><?php echo number_format($devisResteAPayer, 2, ',', ' '); ?> <?php echo $devis->getDevise(); ?></strong></small>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>Date réception <span class="text-danger">*</span></label>
							<div class="cal-icon">
								<input class="form-control datetimepicker" type="text" name="payment_date_payment" value="<?php echo date('d/m/Y'); ?>">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>Méthode de paiement <span class="text-danger">*</span></label>
							<select class="select" name="payment_methode_payment">
								<option value="Chèque">Chèque</option>
								<option value="Traite">Traite</option>
								<option value="Espèce">Espèce</option>
								<option value="Virement">Virement</option>
								<option value="Paiement enline">Paiement enline</option>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label>Date validation <span class="text-danger">*</span></label>
							<div class="cal-icon">
								<input class="form-control datetimepicker" type="text" name="payment_date_validation" value="<?php echo date('d/m/Y'); ?>">
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Détail</label>
							<textarea name="payment_detail" class="form-control"></textarea>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Règlement (justificatif)</label>
							<input type="file" name="payment_photo[]" class="form-control">
						</div>
					</div>
					<div class="col-sm-12">
						<div class="alert alert-warning payment-relance-alert" style="display:none;">
							<i class="fa fa-info-circle"></i> Le montant saisi est inférieur au reste à payer sur la facture : une relance sera automatiquement créée pour le solde restant, selon les conditions de paiement du devis.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">

		<div class="col-md-12" style="background:#E9E9E9;padding-top: 10px;border-radius: 5px;margin-bottom: 10px;">
			<div class="form-group">
				<label>Conditions de paiement</label>

				<div class="myResponsivTable mb-4">
					<table class="table table-stripped table-center table-hover devis-conditions-table">
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

		<div class="col-md-12">
			<label>Prestations</label>
			<a href="#0" class="text-success addServiceManual" style="float:right;">+ service</a>
		</div>

		<div class="myResponsivTable mt-4 mb-4">
			<table class="table table-stripped table-center table-hover devis-items-table">
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
									<i class="fas fa-magic ask-ai-item-row" data-toggle="tooltip" data-placement="top" data-original-title="Assistant IA"></i>
									<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une ligne"></i>
									<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne" data-id="<?php echo $item_devis->getId(); ?>"></i>
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
				<input type="number" step="any" class="form-control" disabled name="total" value="<?php if (isset($devis)) echo $devis->getTotal(); ?>">
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<?php
				// L'agence 2 (Dubai) ne facture qu'en AED : on ne propose que cette devise
				// dans ce cas (au lieu de l'afficher au milieu des devises marocaines/
				// internationales). Si un devis existant a une autre devise (créé avant ce
				// changement, ou déplacé d'agence), on la garde visible pour ne pas la
				// perdre silencieusement à l'édition.
				$deviseLabels = array('DH' => 'MAD (DH)', '€' => 'Euro (€)', '$' => 'Dollar ($)', '£' => 'Pound (£)', 'AED' => 'AED (DH)');
				$currentDevise = isset($devis) ? $devis->getDevise() : '';
				$isDubaiAgence = $_SESSION['agence'] == 2;
				$deviseOptions = $isDubaiAgence ? array('AED') : array('DH', '€', '$', '£');
				if ($currentDevise !== '' && !in_array($currentDevise, $deviseOptions)) {
					$deviseOptions[] = $currentDevise;
				}
				?>
				<label>Devise</label>
				<select class="select" name="devise">
					<?php foreach ($deviseOptions as $val) : ?>
						<?php $sl = $currentDevise !== '' ? ($currentDevise == $val ? "selected" : "") : (!isset($devis) && $isDubaiAgence && $val == 'AED' ? "selected" : ""); ?>
						<option value="<?php echo $val; ?>" <?php echo $sl; ?>><?php echo isset($deviseLabels[$val]) ? $deviseLabels[$val] : $val; ?></option>
					<?php endforeach; ?>
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
	
	<?php if(!isset($devis) || $devis->getFacture()->getId() == 0 || $devis->getStatu() != 4): ?>
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
	<div class="modal-dialog modal-dialog-scrollable" role="document" style="max-width: 800px;">
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

<!-- Add Service Modal -->
<div id="dialog-service" class="modal service-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-scrollable" role="document" style="max-width: 800px;">
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
				<h5 class="modal-title">Vérifier les données détectées avant de les ajouter au devis</h5>
			</div>
			<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
				<div class="form-group">
					<label>Langue du devis</label>
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

				<div class="d-flex justify-content-between align-items-center mt-3">
					<label class="mb-0">Conditions de paiement <span id="iaReviewConditionsHint" class="text-muted"></span></label>
					<a href="#0" class="text-success" id="iaReviewAddCondition">+ ajouter une ligne</a>
				</div>
				<table class="table table-sm table-bordered" id="iaReviewConditionsTable">
					<thead>
						<tr>
							<th>Montant/Pourcentage</th>
							<th>Condition</th>
							<th>Date rappel</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<p class="text-muted"><small>Aucune de ces données n'est ajoutée au devis tant que vous n'avez pas cliqué sur "Valider et ajouter".</small></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id="iaReviewCancel">Ignorer</button>
				<button type="button" class="btn btn-primary" id="iaReviewConfirm">Valider et ajouter au devis</button>
			</div>
		</div>
	</div>
</div>
<!-- /IA Review Modal -->

<!-- Confirm Create Invoice Modal -->
<div id="dialog-confirm-facture" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Devis créé avec succès</h5>
			</div>
			<div class="modal-body">
				<p>Voulez-vous créer directement une facture à partir de ce devis ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id="confirmFactureIgnorer">Ignorer pour le moment</button>
				<button type="button" class="btn btn-primary" id="confirmFactureOui">Oui</button>
			</div>
		</div>
	</div>
</div>
<!-- /Confirm Create Invoice Modal -->

<!-- Acompte Popup (Devis Accepté) -->
<div id="devis-acompte-modal" class="modal custom-modal tva-confirm-modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<div class="tva-confirm-icon"><i class="fa fa-money-bill-alt"></i></div>
				<h5 class="modal-title mt-3">Devis accepté</h5>
			</div>
			<div class="modal-body text-center">
				<p>Envoyer une facture de paiement au client pour l'acompte ?</p>
				<div class="form-group text-left">
					<label>Acompte</label>
					<div class="input-group">
						<input type="number" step="any" class="form-control" id="devis-acompte-montant">
						<div class="input-group-append"><span class="input-group-text"><?php if (isset($devis)) echo $devis->getDevise(); ?></span></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
				<button type="button" id="devis-acompte-send" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2" style="display:none;"></span>Envoyer la facture de paiement</button>
			</div>
		</div>
	</div>
</div>
<!-- /Acompte Popup -->

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

		// Couleur du statut (même code couleur que la liste des devis), mise à jour à chaque
		// changement manuel du select et exposée globalement pour être rafraîchie après un envoi
		// email/Slack réussi (bascule automatique Brouillon -> Envoyé) depuis edit.php.
		var devisStatusMap = {
			'0': { label: 'Brouillon', cls: 'bg-warning-light' },
			'1': { label: 'Envoyé', cls: 'bg-success-light' },
			'2': { label: 'Accepté', cls: 'bg-success-light' },
			'3': { label: 'Contrat en attente de signature', cls: 'bg-primary-light' },
			'4': { label: 'Paiement effectué', cls: 'bg-success text-white' },
			'5': { label: 'Devis Refusé', cls: 'bg-danger text-white' }
		};
		window.updateDevisStatusBadge = function(statu) {
			var info = devisStatusMap[String(statu)] || devisStatusMap['0'];
			$("#devis-status-badge").attr('class', 'badge ' + info.cls).text(info.label);
		};
		updateDevisStatusBadge($("#status-select").val());
		$(document).on("change", "#status-select", function() {
			updateDevisStatusBadge($(this).val());
		});

		// Statut "Paiement effectué" : affiche/masque le bloc paiement (facture + relance si partiel).
		// Ne se déclenche qu'au vrai changement de statut (transition), pas si le devis est déjà payé.
		var devisInitialStatu = "<?php echo isset($devis) ? intval($devis->getStatu()) : -1; ?>";
		var $devisPaymentBox = $("#devis-payment-box");
		var $devisPaymentRequiredFields = $devisPaymentBox.find('[name="payment_montant"], [name="payment_date_payment"], [name="payment_methode_payment"], [name="payment_date_validation"]');

		function toggleDevisPaymentBox() {
			var status = $("#status-select").val();
			if (status == '4' && devisInitialStatu != '4') {
				$devisPaymentBox.slideDown();
				$devisPaymentRequiredFields.prop('required', true);
			} else {
				$devisPaymentBox.slideUp();
				$devisPaymentRequiredFields.prop('required', false);
				$(".payment-relance-alert").hide();
			}
		}
		$(document).on("change", "#status-select", toggleDevisPaymentBox);
		toggleDevisPaymentBox();

		$(document).on("keyup change", "#payment_montant", function() {
			var montant = parseFloat($(this).val());
			var reste = parseFloat($(this).data('reste'));
			if (!isNaN(montant) && !isNaN(reste) && montant < reste) {
				$(".payment-relance-alert").slideDown();
			} else {
				$(".payment-relance-alert").slideUp();
			}
		});

		// Envoi de la facture de paiement (acompte) depuis la popup "Devis accepté"
		$(document).on("click", "#devis-acompte-send", function() {
			var $btn = $(this);
			var factureId = $btn.attr('data-facture-id');
			var montant = $('#devis-acompte-montant').val();
			if (!montant || parseFloat(montant) <= 0) {
				alert("Veuillez saisir un montant valide");
				return;
			}
			$btn.prop('disabled', true);
			$btn.find('.spinner-border').show();
			var order = 'id_facture=' + factureId + '&montant=' + montant;
			$.post("components/com_facture/controleurs/router.php?task=sendPaymentRequestPdf", order, function(theResponse) {
				$btn.prop('disabled', false);
				$btn.find('.spinner-border').hide();
				if (parseInt(theResponse) === 1) {
					alert("Facture de paiement envoyée au client");
					$("#devis-acompte-modal").modal('hide');
				} else {
					alert("Erreur lors de l'envoi de la facture de paiement");
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
				$("#devisForm .submit").prop("disabled", true);
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
					var respParts = String(theResponse).split('|');
					var becameAccepted = (devisInitialStatu != '2' && $("#status-select").val() == '2');
					if (becameAccepted && respParts.length > 1) {
						var newFactureId = respParts[1];
						var firstAcompte = $('.amount-input').first().val();
						$('#devis-acompte-montant').val(firstAcompte);
						$('#devis-acompte-send').attr('data-facture-id', newFactureId);
						$("#devis-acompte-modal").off('hidden.bs.modal').on('hidden.bs.modal', function() {
							document.location.reload();
						});
						setTimeout(function() {
							$("#devis-acompte-modal").modal('show');
						}, 500)
					} else {
	                    setTimeout(function() {
							document.location.reload();
						}, 1500)
					}
                    <?php else: ?>
					var newDevisId = String(theResponse).split('|')[1];
					setTimeout(function() {
						if (newDevisId) {
							$("#dialog-confirm-facture").modal('show');
							$("#confirmFactureOui").off('click').on('click', function () {
								$("#dialog-confirm-facture").modal('hide');
								$.post("components/com_devis/controleurs/router.php?task=createInvoice", 'id=' + newDevisId, function (invoiceResponse) {
									var newFactureId = String(invoiceResponse).split('|')[1];
									if (parseInt(invoiceResponse) === 1 && newFactureId) {
										document.location = "index.php?option=com_facture&task=edit&id=" + newFactureId;
									} else {
										document.location = "index.php?option=com_devis&task=edit&id=" + newDevisId;
									}
								});
							});
							$("#confirmFactureIgnorer").off('click').on('click', function () {
								$("#dialog-confirm-facture").modal('hide');
								document.location = "index.php?option=com_devis&task=edit&id=" + newDevisId;
							});
						} else {
							document.location = "index.php?option=com_devis";
						}
					}, 1500)
					<?php endif; ?>

				} else if (parseInt(theResponse) === 0) {
					$('#devisForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    $('#devisForm .submit').prop('disabled', false);
				}else if (parseInt(theResponse) === 2) {
					$('#devisForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez signer le contrat<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}else if (parseInt(theResponse) === 3) {
					$('#devisForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Ce devis est associé à un contrat signé, vous ne pouvez pas changer son statut<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#devisForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    $('#devisForm .submit').prop('disabled', false);
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
				$("#dialog-custom").modal('show');
				$("#dialog-custom").one('shown.bs.modal', function() {
					$("#dialog-custom .modal-body").html(theResponse);
				});
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
		
		// Point de passage commun pour toute création de client depuis cette page (bouton "+ client"
		// ou modale pré-remplie par l'IA) : si l'agence choisie dans la modale diffère de l'agence de
		// session en cours, ni la liste clients ni la liste banques ne la reflèteraient (toutes deux
		// filtrées côté serveur par $_SESSION['agence']) - on bascule donc la session puis on recharge
		// la page. Sinon on sélectionne simplement le nouveau client, ce qui déclenche le "change" sur
		// .client-select et donc le refiltrage de .bank-select (assets/js/ia-bank-filter.js).
		function handleClientCreatedOnDevis(newClientId, newClientAgence, onDifferentAgence, onSameAgence) {
			if (newClientAgence && parseInt(newClientAgence) !== parseInt(currentSessionAgence)) {
				if (typeof onDifferentAgence === 'function') onDifferentAgence();
				ensureSessionAgence(newClientAgence, function () {
					document.location = "index.php?option=com_devis&task=add&id_client=" + newClientId;
				});
			} else {
				$(".client-select").val(newClientId).trigger('change');
				if (typeof onSameAgence === 'function') onSameAgence();
			}
		}

		$(document).on("click", ".addClient", function() {
			openIaClientModal({
				modalSelector: '#dialog-client',
				onCreated: function (newClientId, newClientAgence) {
					handleClientCreatedOnDevis(newClientId, newClientAgence);
				}
			});
		})

		$(document).on("click", ".addServiceManual", function() {
			openIaServiceModal({
				modalSelector: '#dialog-service',
				targetRow: $(".devis-items-table tbody tr").last()
			});
		});

		<?php if (!isset($devis)): ?>
		initIaDropzone({
			zoneSelector: '#iaDropzoneDevis',
			context: 'devis',
			onExtracted: function (response) {
				var clientData = response.extracted.client;
				var clientSelected = $(".client-select").val();
				var hasClientData = clientData && (clientData.nom || clientData.raison_social || clientData.email);

				if (!clientSelected && hasClientData) {
					openIaClientModal({
						modalSelector: '#dialog-client',
						prefillClient: clientData,
						onCreated: function (newClientId, newClientAgence) {
							handleClientCreatedOnDevis(newClientId, newClientAgence, function () {
								// le client appartient à une autre agence : la page va être rechargée
								// (voir handleClientCreatedOnDevis), on garde les données extraites
								// par l'IA de côté pour les réappliquer une fois la page revenue.
								sessionStorage.setItem('ia_services', JSON.stringify(response.extracted.services || []));
								sessionStorage.setItem('ia_conditions', JSON.stringify(response.extracted.conditions || []));
								sessionStorage.setItem('ia_client_pays', (clientData && clientData.pays) || '');
							}, function () {
								showIaReviewPanel(response.extracted);
							});
						}
					});
				} else {
					showIaReviewPanel(response.extracted);
				}
			}
		});

		// reprise : la révision se fait aussi si on arrive depuis la création du client (sans re-déposer le fichier)
		var iaServicesCarriedOver = sessionStorage.getItem('ia_services');
		var iaPaysCarriedOver = sessionStorage.getItem('ia_client_pays');
		var iaConditionsCarriedOver = sessionStorage.getItem('ia_conditions');
		if (iaServicesCarriedOver || iaConditionsCarriedOver) {
			sessionStorage.removeItem('ia_services');
			sessionStorage.removeItem('ia_client_pays');
			sessionStorage.removeItem('ia_conditions');
			try {
				showIaReviewPanel({
					client: { pays: iaPaysCarriedOver || '' },
					services: iaServicesCarriedOver ? JSON.parse(iaServicesCarriedOver) : [],
					conditions: iaConditionsCarriedOver ? JSON.parse(iaConditionsCarriedOver) : []
				});
			} catch (e) {}
		}

		function showIaReviewPanel(extracted) {
			var services = extracted.services || [];
			var conditions = extracted.conditions || [];
			if (!services.length && !conditions.length) {
				return;
			}

			var suggestedLangue = suggestLangueFromPays(extracted.client && extracted.client.pays) || $("select[name='langue']").val() || 'fr';
			$("#iaReviewLangue").val(suggestedLangue);

			var serviceOptionsHtml = $(".devis-items-table .service-select").first().html() || '';
			serviceOptionsHtml = serviceOptionsHtml.replace('<option value="" selected>Sélectionner</option>', '');

			var uniteOptionsHtml = $(".devis-items-table .unite-input").first().html() || '';

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

			// Une même prestation (ex: "Site Web", "Réseaux sociaux") apparaît parfois plusieurs
			// fois dans une présentation (une page qui la détaille, une autre qui la récapitule) -
			// signalée ici pour que l'utilisateur puisse fusionner en une seule ligne de devis
			// plutôt que de se retrouver avec 2 lignes identiques.
			detecterEtSignalerDoublonsIaReview();

			$("#iaReviewConditionsTable tbody").empty();
			$("#iaReviewConditionsHint").text(conditions.length ? '' : '(aucune détectée automatiquement — vous pouvez en saisir manuellement ci-dessous)');
			if (conditions.length) {
				conditions.forEach(function (cond) {
					addIaReviewConditionRow(cond.montant, cond.condition);
				});
			} else {
				// aucune condition détectée : on affiche tout de suite une ligne vide et modifiable
				// plutôt qu'un tableau qui semble vide/non éditable
				addIaReviewConditionRow('', '');
			}

			$("#dialog-ia-review").modal('show');
		}

		// Lettres/chiffres uniquement, minuscules, sans accents - pour repérer 2 titres de
		// prestation détectés comme "le même service" malgré une casse ou un accent différent.
		function normaliserTitreServiceIa(texte) {
			return (texte || '').toString().toLowerCase()
				.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
				.replace(/[^a-z0-9]+/g, ' ')
				.trim();
		}

		// Regroupe les lignes de #iaReviewServicesTable par service détecté identique (même
		// service du catalogue choisi, ou même titre normalisé tant qu'aucun service catalogue
		// n'est encore choisi) et affiche un bandeau "Combiner" au-dessus de chaque groupe de 2+.
		// Idempotent : peut être rappelée après une correction manuelle du "service à utiliser".
		function detecterEtSignalerDoublonsIaReview() {
			$('#iaReviewServicesTable .ia-review-doublon-banner').remove();
			$('#iaReviewServicesTable tbody tr').removeAttr('data-doublon-groupe');

			var groupes = {};
			$('#iaReviewServicesTable tbody tr').has('.ia-review-service-select').each(function () {
				var $row = $(this);
				var serviceChoisi = $row.find('.ia-review-service-select').val();
				var cle = (serviceChoisi && serviceChoisi !== '__CREATE_NEW__')
					? 'service:' + serviceChoisi
					: 'titre:' + normaliserTitreServiceIa($row.data('svc') ? $row.data('svc').titre : '');
				if (cle === 'titre:') {
					return;
				}
				(groupes[cle] = groupes[cle] || []).push($row);
			});

			var compteurGroupe = 0;
			$.each(groupes, function (cle, lignes) {
				if (lignes.length < 2) {
					return;
				}
				compteurGroupe++;
				var idGroupe = 'doublon-' + compteurGroupe;
				var titreAffiche = (lignes[0].data('svc') || {}).titre || '';
				lignes.forEach(function ($row) {
					$row.attr('data-doublon-groupe', idGroupe);
				});

				var banniere = $(
					'<tr class="ia-review-doublon-banner" data-doublon-groupe="' + idGroupe + '"><td colspan="6">' +
					'<div class="alert alert-warning d-flex justify-content-between align-items-center mb-0 py-2">' +
					'<span><i class="fa fa-clone mr-1"></i>' + lignes.length + ' prestations similaires détectées (« ' +
					$('<div>').text(titreAffiche).html() + ' ») — les combiner en une seule ligne ?</span>' +
					'<button type="button" class="btn btn-sm btn-warning ml-2 ia-review-combiner-btn" data-doublon-groupe="' + idGroupe + '">' +
					'<i class="fa fa-compress-arrows-alt mr-1"></i>Combiner</button></div></td></tr>'
				);
				lignes[0].before(banniere);
			});
		}

		// Une correction manuelle du service choisi peut faire apparaître (ou disparaître) un
		// doublon qui n'était pas détecté au premier passage (ex: 2 titres différents finalement
		// rattachés au même service du catalogue).
		$(document).on('change', '.ia-review-service-select', function () {
			detecterEtSignalerDoublonsIaReview();
		});

		$(document).on('click', '.ia-review-combiner-btn', function () {
			var idGroupe = $(this).data('doublon-groupe');
			var $lignes = $('#iaReviewServicesTable tbody tr[data-doublon-groupe="' + idGroupe + '"]').not('.ia-review-doublon-banner');
			if ($lignes.length < 2) {
				return;
			}

			var $premiere = $lignes.first();
			var qteTotal = 0;
			var descriptions = [];
			$lignes.each(function () {
				var q = parseFloat($(this).find('.ia-review-qte').val());
				qteTotal += isNaN(q) ? 0 : q;
				var d = $(this).data('aiDescription');
				if (d && descriptions.indexOf(d) === -1) {
					descriptions.push(d);
				}
			});
			$premiere.find('.ia-review-qte').val(qteTotal || $lignes.length);
			if (descriptions.length > 1) {
				$premiere.data('aiDescription', descriptions.join('\n\n'));
			}
			$premiere.find('td').first().append(' <span class="badge badge-secondary ml-1">' + $lignes.length + ' fusionnées</span>');

			$lignes.slice(1).each(function () {
				$(this).next('.ia-review-ai-row').remove();
				$(this).remove();
			});
			$('#iaReviewServicesTable tbody tr.ia-review-doublon-banner[data-doublon-groupe="' + idGroupe + '"]').remove();
		});

		function addIaReviewConditionRow(montant, condition, afterRow) {
			var row = $(
				'<tr>' +
				'<td><input type="text" class="form-control form-control-sm ia-review-montant" value="' + (montant || '').replace(/"/g, '&quot;') + '"></td>' +
				'<td><input type="text" class="form-control form-control-sm ia-review-condition" value="' + (condition || '').replace(/"/g, '&quot;') + '"></td>' +
				'<td><input type="date" class="form-control form-control-sm ia-review-date-rappel"></td>' +
				'<td class="text-right">' +
				'<i class="fas fa-plus-circle text-success ia-review-add-condition-row" style="cursor:pointer;" title="Ajouter une ligne"></i> ' +
				'<i class="fas fa-minus-circle text-danger ia-review-remove-condition" style="cursor:pointer;" title="Supprimer"></i>' +
				'</td>' +
				'</tr>'
			);
			if (afterRow && afterRow.length) {
				afterRow.after(row);
			} else {
				$("#iaReviewConditionsTable tbody").append(row);
			}
			return row;
		}

		$(document).on('click', '#iaReviewAddCondition', function (e) {
			e.preventDefault();
			addIaReviewConditionRow('', '');
		});

		$(document).on('click', '.ia-review-add-condition-row', function () {
			addIaReviewConditionRow('', '', $(this).closest('tr'));
		});

		$(document).on('click', '.ia-review-remove-condition', function () {
			if ($("#iaReviewConditionsTable tbody tr").length > 1) {
				$(this).closest('tr').remove();
			} else {
				$(this).closest('tr').find('input').val('');
			}
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
					// uniquement les titres, en une ligne compacte, pas de liens ; fusionné avec
					// la description déjà présente sur cette ligne, jamais écrasée.
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

		$("#iaReviewCancel").on('click', function () {
			$("#dialog-ia-review").modal('hide');
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
					prix: row.find('.ia-review-prix').val(),
					id_service: (chosen && chosen !== '__CREATE_NEW__') ? chosen : null,
					description: row.data('aiDescription') || ''
				});
			});

			var validatedConditions = [];
			$("#iaReviewConditionsTable tbody tr").each(function () {
				var row = $(this);
				var montant = row.find('.ia-review-montant').val();
				var condition = row.find('.ia-review-condition').val();
				var dateRappel = row.find('.ia-review-date-rappel').val();
				if (montant || condition) {
					validatedConditions.push({ montant: montant, condition: condition, date_rappel: dateRappel });
				}
			});

			$("#dialog-ia-review").modal('hide');

			if (validatedServices.length) {
				processIaDevisServices(validatedServices, 0);
			}
			if (validatedConditions.length) {
				processIaDevisConditions(validatedConditions, 0);
			}
		});

		function processIaDevisConditions(conditions, index) {
			if (index >= conditions.length) {
				return;
			}
			var cond = conditions[index];

			function applyToConditionRow(row) {
				if (cond.montant) { row.find(".amount-input").val(cond.montant); }
				if (cond.condition) { row.find(".condition-input").val(cond.condition); }
				if (cond.date_rappel) {
					row.find(".date-input").val(cond.date_rappel);
				} else if (!row.find(".date-input").val()) {
					row.find(".date-input").val(new Date().toISOString().slice(0, 10));
				}
				processIaDevisConditions(conditions, index + 1);
			}

			if (index === 0) {
				applyToConditionRow($(".devis-conditions-table tbody tr").first());
			} else {
				var lastRow = $(".devis-conditions-table tbody tr").last();
				$.post("components/com_devis/controleurs/router.php?task=getRowCondition", '', function (theResponse) {
					lastRow.after(theResponse);
					applyToConditionRow(lastRow.next());
				});
			}
		}

		function processIaDevisServices(services, index) {
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
						// description rédigée par l'IA : ne s'applique qu'à cette ligne de ce devis
						// (le service enregistré, partagé, n'est pas modifié)
						if (svc.description) { row.find('input[name="item_devis_service_description[]"]').val(svc.description); }
						row.find(".qte-input").trigger('change');
						processIaDevisServices(services, index + 1);
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
								processIaDevisServices(services, index + 1);
							}, 700);
						}
					});
				}
			}

			if (index === 0) {
				applyToRow($(".devis-items-table tbody tr").first());
			} else {
				var lang = $("select[name='langue']").val();
				var lastRow = $(".devis-items-table tbody tr").last();
				$.post("components/com_devis/controleurs/router.php?task=getRowDevis", 'lang=' + lang, function (theResponse) {
					lastRow.after(theResponse);
					applyToRow(lastRow.next());
				});
			}
		}
		<?php endif; ?>

	})
</script>