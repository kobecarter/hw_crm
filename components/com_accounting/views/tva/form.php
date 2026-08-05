<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=isset($tva) ? $action2 : $action1?>" id="tvaForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<?php if (!isset($tva)) : ?>
		<div class="col-md-12">
			<div id="iaDropzoneTva" class="ia-dropzone mb-3">
				<input type="file" accept="application/pdf,image/jpeg,image/png,image/webp" style="display:none;">
				<div class="ia-dropzone-text">
					<i class="fa fa-file-invoice-dollar"></i> Glissez-déposez la déclaration de TVA envoyée par le comptable (PDF ou photo) ici, ou cliquez pour la sélectionner<br>
					<small>Montant, majoration et mois seront pré-remplis automatiquement ci-dessous, et le document sera joint à l'enregistrement.</small>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php if (!isset($tva)) : ?>
		<div class="col-md-12">
			<div class="form-group mb-2">
				<label>Périodicité de la déclaration</label>
				<div class="form-checks">
					<div class="form-check form-check-inline">
						<input type="radio" name="periodicite" id="periodicite-mensuel" class="form-check-input" value="mensuel" checked>
						<label class="form-check-label" for="periodicite-mensuel">Mensuelle</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" name="periodicite" id="periodicite-trimestriel" class="form-check-input" value="trimestriel">
						<label class="form-check-label" for="periodicite-trimestriel">Trimestrielle</label>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div class="col-md-4" id="tva-bloc-mensuel">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="month" id="month-input" class="form-control" onclick="document.getElementById('month-input').click();" name="date" value="<?php if (isset($tva)){ echo date('Y-m',strtotime($tva->getDate()));} ?>" required>
				</div>
			</div>
		</div>

		<?php if (!isset($tva)) : ?>
		<div class="col-md-2 d-none" id="tva-bloc-trimestre-select">
			<div class="form-group">
				<label>Trimestre<span class="text-danger"> * </span></label>
				<select class="form-control" name="trimestre" id="trimestre-input">
					<option value="1">T1 (Janvier-Mars)</option>
					<option value="2">T2 (Avril-Juin)</option>
					<option value="3">T3 (Juillet-Septembre)</option>
					<option value="4">T4 (Octobre-Décembre)</option>
				</select>
			</div>
		</div>
		<div class="col-md-2 d-none" id="tva-bloc-trimestre-annee">
			<div class="form-group">
				<label>Année<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="annee" id="annee-input" value="<?= date('Y') ?>">
			</div>
		</div>
		<div class="col-md-12 d-none" id="tva-bloc-trimestre-confirmation">
			<p class="text-muted mb-2" style="font-size:0.85rem;" id="tva-trimestre-confirmation-texte"></p>
		</div>
		<?php endif; ?>

		<div class="col-md-4">
			<div class="form-group">
				<label>Montant<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="amount" min="0" step="any" value="<?php if(isset($tva)) echo $tva->getAmount(); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Majoration<span class="text-danger d-none"> * </span></label>
				<input type="number" class="form-control" name="increasion" min="0" step="any" value="<?php if(isset($tva)) echo $tva->getIncreasion(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Document</label>
				<input type="file" class="form-control" name="doc[]">
			</div>
		</div>

		<?php if(isset($tva) && $tva->getDoc() != ''): ?>
		<div class="col-md-2">
		    <a href="images/tva/<?php echo $tva->getDoc(); ?>" class="btn btn-success" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox="" style="margin-top: 32px;">
                <i class="fas fa-file"></i>
            </a>
            <a href="" class="btn btn-danger deleteDoc" data-id="<?php echo $tva->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer document" style="margin-top: 32px;">
                <i class="fas fa-trash"></i>
            </a>
		</div>
		<?php endif; ?>

		<?php if (isset($tva) && !empty($relevesLiesTva)) : ?>
		<div class="col-md-12">
			<div class="alert alert-info mb-2" style="font-size:0.9rem;">
				<i class="fa fa-university mr-1"></i> <?= count($relevesLiesTva) ?> relevé(s) bancaire(s) couvrant la période de cette TVA (traçabilité — pour voir avec quel relevé le comptable a calculé ce montant) :
				<div class="table-responsive mt-2">
					<table class="table table-sm table-striped mb-0">
						<thead><tr><th>Compte</th><th>Période du relevé</th><th>Fichier</th></tr></thead>
						<tbody>
							<?php foreach ($relevesLiesTva as $lotTva) :
								$bankTva = $lotTva->getBank();
								$nomCompteTva = $bankTva ? ($bankTva->getLabel() !== null && $bankTva->getLabel() !== '' ? $bankTva->getLabel() : ($bankTva->getRaisonSociale() !== null && $bankTva->getRaisonSociale() !== '' ? $bankTva->getRaisonSociale() : $bankTva->getBanque())) : '—';
							?>
							<tr>
								<td><?= htmlspecialchars($nomCompteTva) ?></td>
								<td><?= $lotTva->getDateDebut() ? date('d/m/Y', strtotime($lotTva->getDateDebut())) : '—' ?> → <?= $lotTva->getDateFin() ? date('d/m/Y', strtotime($lotTva->getDateFin())) : '—' ?></td>
								<td><?= htmlspecialchars($lotTva->getFichierSource()) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<a href="index.php?option=com_rapprochement" class="d-inline-block mt-2" style="font-size:0.85rem;">Voir dans BANK STATEMENT <i class="fa fa-arrow-right ml-1"></i></a>
			</div>
		</div>
		<?php endif; ?>

		<?php if (isset($tva) && $tva->getIdCharge() != '' && $tva->getIdCharge() !== null) : ?>
		<div class="col-md-12">
			<div class="alert alert-success mb-2" style="font-size:0.9rem;">
				<i class="fa fa-check-circle mr-1"></i> Cette TVA a déjà été poussée vers les charges —
				<a href="index.php?option=com_charge&task=edit&id=<?= $tva->getIdCharge() ?>" target="_blank">voir/modifier la charge</a>.
			</div>
		</div>
		<?php else : ?>
		<div class="col-md-4">
			<div class="form-group">
				<label>Preuve de paiement</label>
				<input type="file" class="form-control" name="preuve_paiement[]">
				<small class="text-muted">Si fournie, une charge fixe "TVA" est créée automatiquement (voir date ci-contre).</small>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Date de paiement</label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="date_payment" value="<?php if(isset($tva) && $tva->getDatePayment()) echo normalDate($tva->getDatePayment()); else echo date('d/m/Y'); ?>">
				</div>
			</div>
		</div>
		<?php endif; ?>

        <div class="col-md-4">
			<div class="form-group">
				<label>Statut<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" id="status1" value="1"  <?= isset($tva) ? ($tva->getStatus() == 1 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="status1">Poussé</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" value="0" id="status2" <?= isset($tva) ? ($tva->getStatus() == 0 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="status2">Non poussé</label>
                    </div>
                </div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remark"><?php if (isset($tva)){ echo $tva->getRemark(); }?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('remark', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>	
		
		<!-- Toggle Switch -->

        <input type="hidden" name="id_agence" value="<?= isset($tva) ? $tva->getAgence()->getId() : $_SESSION['agence'] ?>">
				
		<?php if(isset($tva)): ?>
			<input type="hidden" name="id_tva" value="<?php echo $tva->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?=isset($tva) ? 'Modifier' : 'Ajouter'?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($tva) ? 'Modifier' : 'Ajouter'?> </button>
	</div>

	<?php if (!isset($tva)) : ?>
	<script>
		$(function () {
			var nomsTrimestre = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

			function majConfirmationTrimestre() {
				var t = parseInt($('#trimestre-input').val(), 10);
				var annee = $('#annee-input').val();
				var debut = (t - 1) * 3;
				var mois = [nomsTrimestre[debut], nomsTrimestre[debut + 1], nomsTrimestre[debut + 2]];
				$('#tva-trimestre-confirmation-texte').html('<i class="fa fa-info-circle mr-1"></i> Ce trimestre couvre : <strong>' + mois.join(', ') + ' ' + annee + '</strong> — le montant et la majoration saisis seront divisés également sur ces 3 mois (une ligne par mois).');
			}

			function togglePeriodicite() {
				var trimestriel = $('#periodicite-trimestriel').is(':checked');
				$('#tva-bloc-mensuel').toggleClass('d-none', trimestriel);
				$('#tva-bloc-trimestre-select, #tva-bloc-trimestre-annee, #tva-bloc-trimestre-confirmation').toggleClass('d-none', !trimestriel);
				$('#month-input').prop('required', !trimestriel);
				if (trimestriel) {
					majConfirmationTrimestre();
				}
			}

			$('input[name=periodicite]').on('change', togglePeriodicite);
			$('#trimestre-input, #annee-input').on('change input', majConfirmationTrimestre);

			initTvaIaDropzone({
				zoneSelector: '#iaDropzoneTva',
				onExtracted: function (extracted, file) {
					if (extracted.montant) $('#tvaForm input[name=amount]').val(extracted.montant);
					if (extracted.majoration) $('#tvaForm input[name=increasion]').val(extracted.majoration);

					if (extracted.periodicite === 'trimestriel' && extracted.trimestre && extracted.annee) {
						$('#periodicite-trimestriel').prop('checked', true);
						$('#trimestre-input').val(String(extracted.trimestre));
						$('#annee-input').val(extracted.annee);
						togglePeriodicite();
					} else if (extracted.mois && extracted.annee) {
						$('#periodicite-mensuel').prop('checked', true);
						togglePeriodicite();
						var moisPadded = ('0' + extracted.mois).slice(-2);
						$('#tvaForm input[name=date]').val(extracted.annee + '-' + moisPadded);
					}

					// Le fichier déjà déposé devient directement la pièce jointe du formulaire :
					// pas besoin de le sélectionner une seconde fois dans le champ "Document".
					try {
						var dt = new DataTransfer();
						dt.items.add(file);
						document.querySelector('#tvaForm input[name="doc[]"]').files = dt.files;
					} catch (e) {
						// Navigateur ne supportant pas DataTransfer en écriture : l'utilisateur
						// devra simplement rejoindre le document manuellement, le reste marche.
					}
				}
			});
		});
	</script>
	<?php endif; ?>
</form>
