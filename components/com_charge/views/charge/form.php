<style>
	.chosen-container{
		width: 100% !important;
	}
</style>
<form method="post" action="<?php echo $action; ?>" id="chargeForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<?php if (!isset($charge)) : ?>
		<div class="col-md-12">
			<div class="charge-section-title"><i class="fa fa-file-invoice-dollar mr-2"></i>Document (optionnel)</div>
			<p class="text-muted mb-2" style="font-size:0.9rem;">
				Glissez-déposez ou sélectionnez n'importe quel justificatif — bulletin de paie,
				déclaration CNSS, déclaration TVA, bilan, taxe professionnelle, facture ou quittance —
				le système détecte automatiquement de quoi il s'agit et pré-remplit les champs
				ci-dessous. Pour un bulletin de paie, le document est en plus ajouté à l'espace de
				l'employé concerné. Pour CNSS/TVA/Bilan/Taxe pro, une vérification anti-doublon est
				faite avant de vous laisser continuer.
			</p>
			<div id="iaDropzonePayslip" class="ia-dropzone mb-2">
				<input type="file" accept="application/pdf,image/jpeg,image/png,image/webp" style="display:none;">
				<div class="ia-dropzone-text">
					<i class="fa fa-file-invoice-dollar"></i> Glissez-déposez un document (PDF ou photo) ici, ou cliquez pour le sélectionner<br>
					<small>Formats acceptés : PDF, JPG, PNG, WEBP.</small>
				</div>
			</div>
			<div id="chargePayslipEmployeBlock" class="d-none mb-3">
				<div class="form-group mb-0">
					<label>Employé concerné<span class="text-danger"> * </span></label>
					<select class="form-control" name="id_resourcehumaine_payslip" id="chargePayslipEmploye">
						<option value="">Sélectionner</option>
						<?php foreach ($employes as $employe) : ?>
							<option value="<?= $employe->getId() ?>"><?= $employe->getFirstName() . ' ' . $employe->getLastName() ?></option>
						<?php endforeach; ?>
					</select>
					<small id="chargePayslipConfianceNote" class="text-muted d-block mt-1"></small>
				</div>
			</div>
			<div id="chargeDoublonBanner" class="alert alert-danger d-none mb-3" style="font-size:0.9rem;"></div>
			<input type="hidden" name="is_payslip" id="chargeIsPayslip" value="0">
			<input type="hidden" name="payslip_mois" id="chargePayslipMois" value="">
			<input type="hidden" name="payslip_annee" id="chargePayslipAnnee" value="">
		</div>
		<?php endif; ?>

		<?php if (isset($charge) && isset($bulletinLie) && $bulletinLie->getId() > 0) : ?>
		<div class="col-md-12">
			<div class="alert alert-success mb-3" style="font-size:0.9rem;">
				<i class="fa fa-check-circle mr-1"></i> Cette charge est liée au bulletin de paie de
				<strong><?= $bulletinLie->getResourcehumaine()->getFirstName() . ' ' . $bulletinLie->getResourcehumaine()->getLastName() ?></strong> —
				<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $bulletinLie->getResourcehumaine()->getId() ?>" target="_blank">voir son espace employé</a>.
			</div>
		</div>
		<?php endif; ?>

		<div class="col-md-12">
			<div class="charge-section-title"><i class="fa fa-info-circle mr-2"></i>Informations générales</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Payé par</label>
				<select class="select" name="paid_by">
					<option value="">Sélectionner</option>
					<?php foreach($users as $key=>$value) :?>
					    <option value="<?=$value->getId()?>" <?= (isset($charge) && $charge->getPaidBy()->getId() == $value->getId()) ? 'selected' : '' ?>><?=$value->getNom()." ".$value->getPrenom()?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" autocomplete="off" list="data-list-titles" class="form-control" name="titre" value="<?php if(isset($charge)) echo $charge->getTitre(); ?>">
				<datalist id="data-list-titles">
					<?php foreach(chargesTitles() as $key=>$value) :?>
						<option value="<?=$value?>"><?=$value?></option>
					<?php endforeach;?>
				</datalist>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date charge</label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date_charge" value="<?php if(isset($charge)) echo normaldate($charge->getDateCharge()); else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Type</label>
				<select class="chosen-select" name="type" id="chargeType">
					<option value="fixe" <?php if(isset($charge) && $charge->getType() == 'fixe') echo "selected"; ?>>Charge fixe</option>
					<option value="variable" <?php if(isset($charge) && $charge->getType() == 'variable') echo "selected"; ?>>Charge variable</option>
					<option value="hors_hw" <?php if(isset($charge) && $charge->getType() == 'hors_hw') echo "selected"; ?>>Charge Hors Hello World</option>
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="charge-section-title"><i class="fa fa-money-bill-wave mr-2"></i>Montant &amp; TVA</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Montant</label>
				<input type="number" step="any" class="form-control" name="total" id="chargeTotal" value="<?php if(isset($charge)) echo $charge->getTotal(); ?>">
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Devise</label>
				<select class="select" name="devise" id="chargeDevise">
					<option value="DH" <?php if (isset($charge) && $charge->getDevise() == 'DH') echo "selected"; ?>>MAD (DH)</option>
					<option value="€" <?php if (isset($charge) && $charge->getDevise() == '€') echo "selected"; ?>>Euro (€)</option>
					<option value="$" <?php if (isset($charge) && $charge->getDevise() == '$') echo "selected"; ?>>Dollar ($)</option>
					<option value="£" <?php if (isset($charge) && $charge->getDevise() == '£') echo "selected"; ?>>Pound (£)</option>
					<option value="AED" <?php if (isset($charge) && $charge->getDevise() == 'AED') echo "selected"; ?>>AED (DH)</option>
				</select>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Taux TVA</label>
				<select class="select" name="tva_taux" id="chargeTvaTaux">
					<option value="">N/A</option>
					<option value="0" <?php if (isset($charge) && $charge->getTvaTaux() !== null && (float) $charge->getTvaTaux() === 0.0) echo "selected"; ?>>0%</option>
					<option value="7" <?php if (isset($charge) && $charge->getTvaTaux() == '7') echo "selected"; ?>>7%</option>
					<option value="10" <?php if (isset($charge) && $charge->getTvaTaux() == '10') echo "selected"; ?>>10%</option>
					<option value="14" <?php if (isset($charge) && $charge->getTvaTaux() == '14') echo "selected"; ?>>14%</option>
					<option value="20" <?php if (isset($charge) && $charge->getTvaTaux() == '20') echo "selected"; ?>>20%</option>
				</select>
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-2">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">TVA déductible</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="tva_deductible" id="chargeTvaDeductible" class="toggle-switch-input" <?php if (!isset($charge) || $charge->getTvaDeductible() == 1) echo "checked"; ?>>
					<span class="toggle-switch-label mt-3">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->

		<div class="col-md-12">
			<div class="charge-section-title"><i class="fa fa-hand-holding-usd mr-2"></i>Paiement</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date paiement</label>
				<div class="cal-icon">
				<input type="text" class="form-control datetimepicker" name="date_payment" id="chargeDatePayment" value="<?php if(isset($charge)) echo normaldate($charge->getDatePayment()); else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Mode paiement</label>
				<select class="chosen-select" name="mode_payment" id="chargeModePayment">
					<option value="espece" <?php if(isset($charge) && $charge->getModePayment() == 'espece') echo "selected"; ?>>Espèce</option>
					<option value="cheque" <?php if(isset($charge) && $charge->getModePayment() == 'cheque') echo "selected"; ?>>Chèque</option>
					<option value="virement" <?php if(isset($charge) && $charge->getModePayment() == 'virement') echo "selected"; ?>>Virement</option>
					<option value="paiement_en_ligne" <?php if(isset($charge) && $charge->getModePayment() == 'paiement_en_ligne') echo "selected"; ?>>Paiement en ligne</option>
				</select>
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Charge payée</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="paid" id="chargePaid" class="toggle-switch-input" <?php if(isset($charge) && $charge->isPaid()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->

		<div class="col-md-12">
			<div class="charge-section-title"><i class="fa fa-ellipsis-h mr-2"></i>Autres</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Facturation</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="facture" class="toggle-switch-input" <?php if(isset($charge) && $charge->hasFacture()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->

			<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Rembourssé</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="refunded" class="toggle-switch-input" <?php if(isset($charge) && $charge->isRefunded()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>
		<!-- /Toggle Switch -->

		<div class="col-md-12">
			<div class="charge-section-title"><i class="fa fa-paperclip mr-2"></i>Description &amp; justificatif</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label>Description</label>
				<textarea name="description" class="form-control"><?php if(isset($charge)) echo $charge->getDescription(); ?></textarea>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($charge) && $charge->getPhoto() != '' ? "images/charges/" . $charge->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
							<?php // test document pdf
							if(isset($charge) && fileExtension($charge->getPhoto()) == 'pdf') $photoLink = "assets/img/pdf.png";
							?>
							<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
							<input type="file" name="photo[]" id="edit_img">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>
							<?php if(isset($charge) && $charge->getPhoto() != ''): ?>
							<a href="images/charges/<?php echo $charge->getPhoto(); ?>" class="avatar-edit" style="bottom: 40px;" data-fancybox>
								<i class="fa fa-file"></i>
							</a>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="id_agence" value="<?= isset($charge) ? $charge->getAgence()->getId() : $_SESSION['agence']?>">
		<input type="hidden" name="id_user" value="<?= isset($charge) ? $charge->getUser()->getId() : $_SESSION['user']->getId() ?>">
		<?php if(isset($charge)): ?>
			<input type="hidden" name="id" value="<?php echo $charge->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<?php if (!isset($charge)) : ?>
<!-- Popup "Doublon détecté" (CNSS/TVA/Bilan/Taxe pro déjà enregistrés pour cette période) -->
<div id="chargeDoublonModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="charge-doublon-icon"><i class="fa fa-exclamation-triangle"></i></div>
				<h5 class="modal-title mt-3">Cette déclaration existe déjà</h5>
			</div>
			<div class="modal-body text-center">
				<p class="mb-1" id="chargeDoublonModalTexte"></p>
				<p class="text-muted mb-0" style="font-size:0.85rem;">
					Pour éviter les doublons, les champs n'ont pas été pré-remplis. Vous pouvez
					consulter la déclaration existante, ou fermer cette fenêtre et saisir la charge
					manuellement si vous êtes certain(e) qu'il ne s'agit pas d'un doublon.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
				<a href="#" id="chargeDoublonModalLien" target="_blank" class="btn btn-primary"><i class="fa fa-external-link-alt mr-1"></i> Voir la déclaration existante</a>
			</div>
		</div>
	</div>
</div>

<script>
	$(function () {
		var moisNomsCharge = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

		function fmtMontant(n) {
			return (parseFloat(n) || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
		}

		initChargeDocumentDropzone({
			zoneSelector: '#iaDropzonePayslip',
			onExtracted: function (extracted, file, employeMatch, doublon) {

				if (doublon) {
					$('#chargeDoublonBanner').removeClass('d-none').html(
						'<i class="fa fa-exclamation-triangle mr-2"></i><strong>Doublon détecté (' + doublon.module + ')</strong><br>' +
						doublon.libelle + ' (montant déjà enregistré : ' + fmtMontant(doublon.montant_existant) + ' DH). ' +
						'<a href="' + doublon.lien + '" target="_blank">Voir la déclaration existante</a>'
					);
					$('#chargeDoublonModalTexte').text(doublon.libelle + ' Montant déjà enregistré : ' + fmtMontant(doublon.montant_existant) + ' DH.');
					$('#chargeDoublonModalLien').attr('href', doublon.lien);
					$('#chargeDoublonModal').modal('show');

					var reset = $('#iaDropzonePayslip').data('resetDropzone');
					if (reset) reset();
					return;
				}

				$('#chargeDoublonBanner').addClass('d-none').html('');

				var typeDoc = extracted.type_document || 'autre';
				var moisNom = extracted.mois ? moisNomsCharge[parseInt(extracted.mois, 10) - 1] : '';

				if (typeDoc === 'bulletin_paie') {
					var nomComplet = ((extracted.prenom || '') + ' ' + (extracted.nom || '')).trim();
					if (nomComplet !== '') {
						$('#chargeForm input[name=titre]').val('Salaire ' + nomComplet + (moisNom ? (' — ' + moisNom + ' ' + extracted.annee) : ''));
					}
					$('#chargePayslipEmployeBlock').removeClass('d-none');
					if (employeMatch && employeMatch.id) {
						$('#chargePayslipEmploye').val(String(employeMatch.id)).trigger('change');
						$('#chargePayslipConfianceNote').html(employeMatch.confiance === 'haute'
							? '<i class="fa fa-check-circle text-success mr-1"></i>Employé identifié automatiquement (CIN) — vérifiez avant de valider.'
							: '<i class="fa fa-exclamation-triangle text-warning mr-1"></i>Correspondance probable trouvée — vérifiez qu\'il s\'agit bien du bon employé.');
					} else {
						$('#chargePayslipConfianceNote').html('<i class="fa fa-info-circle mr-1"></i>Aucune correspondance automatique : sélectionnez l\'employé concerné.');
					}
					$('#chargeIsPayslip').val('1');
					$('#chargeType').val('fixe').trigger('change');
					$('#chargeTvaDeductible').prop('checked', false);
					$('#chargePaid').prop('checked', true);
					$('#chargeModePayment').val('virement').trigger('change');
					if (extracted.montant) $('#chargeTotal').val(extracted.montant);
					$('#chargeDevise').val('DH').trigger('change');
					setDatesFromPeriode(extracted.mois, extracted.annee);
				} else if (typeDoc === 'cnss' || typeDoc === 'tva' || typeDoc === 'bilan' || typeDoc === 'taxe_professionnelle') {
					$('#chargePayslipEmployeBlock').addClass('d-none');
					$('#chargeIsPayslip').val('0');
					var titresParType = {
						cnss: 'CNSS — ' + moisNom + ' ' + extracted.annee,
						tva: 'TVA — ' + moisNom + ' ' + extracted.annee,
						bilan: 'Bilan comptable ' + extracted.annee,
						taxe_professionnelle: 'Taxe professionnelle ' + extracted.annee
					};
					$('#chargeForm input[name=titre]').val(titresParType[typeDoc]);
					$('#chargeType').val('fixe').trigger('change');
					$('#chargeTvaDeductible').prop('checked', false);
					$('#chargePaid').prop('checked', true);
					$('#chargeModePayment').val('virement').trigger('change');
					if (extracted.montant) $('#chargeTotal').val(extracted.montant);
					$('#chargeDevise').val('DH').trigger('change');
					setDatesFromPeriode(extracted.mois, extracted.annee);
				} else {
					// 'autre' (facture, quittance...) : uniquement titre + montant, le reste du
					// formulaire garde ses valeurs par défaut (une facture peut être réellement
					// déductible, contrairement aux 4 types ci-dessus).
					$('#chargePayslipEmployeBlock').addClass('d-none');
					$('#chargeIsPayslip').val('0');
					if (extracted.titre_suggere) {
						$('#chargeForm input[name=titre]').val(extracted.titre_suggere);
					}
					if (extracted.montant) {
						$('#chargeTotal').val(extracted.montant);
					}
				}

				$('#chargePayslipMois').val(extracted.mois || '');
				$('#chargePayslipAnnee').val(extracted.annee || '');

				// Le fichier déjà déposé devient directement la pièce jointe du formulaire,
				// quel que soit le type détecté : pas besoin de le rejoindre une seconde fois.
				try {
					var dt = new DataTransfer();
					dt.items.add(file);
					document.querySelector('#chargeForm input[name="photo[]"]').files = dt.files;
				} catch (e) {
					// Navigateur ne supportant pas DataTransfer en écriture : l'utilisateur
					// devra simplement rejoindre le document manuellement, le reste marche.
				}
			}
		});

		function setDatesFromPeriode(mois, annee) {
			if (!annee) return;
			var dateStr = '';
			if (mois) {
				var dernierJour = new Date(parseInt(annee, 10), parseInt(mois, 10), 0).getDate();
				dateStr = ('0' + dernierJour).slice(-2) + '/' + ('0' + mois).slice(-2) + '/' + annee;
			} else {
				dateStr = '31/12/' + annee;
			}
			$('#chargeForm input[name=date_charge]').val(dateStr);
			$('#chargeDatePayment').val(dateStr);
		}
	});
</script>
<?php endif; ?>

<script>
    $(function () {

        // envoi du formulaire en ajax
        $('form#chargeForm').ajaxForm({
            beforeSubmit: function () {
                $("#chargeForm .loading").css('display','inline-block');
            },
            success: function (theResponse) {
                $("#chargeForm .loading").fadeOut();
                $("html, body").animate({ scrollTop: 0 }, "slow");

                var msgsucces = "Charge ajoutée avec succès";
                if($(".submit").attr("name") === "edit"){
                    msgsucces = "Charge modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#chargeForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    <?php if (isset($charge)) : ?>
						setTimeout(function() {
							document.location.reload();
						}, 1500)
					<?php else : ?>
						setTimeout(function() {
							document.location = "index.php?option=com_charge";
						}, 1500)
					<?php endif; ?>

                } else if(parseInt(theResponse) === 0) {
                    $('#chargeForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#chargeForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });

		var msgsucces = "Charge supprimée avec succès";

		$(document).on( "click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_charge/controleurs/router.php?task=deleteCharge", order, function (theResponse) {
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
