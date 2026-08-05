<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">BANK STATEMENT</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">BANK STATEMENT</li>
					</ul>
				</div>
			</div>
		</div>

		<?php
		$compteursGlobal = array('matched_facture' => 0, 'matched_charge' => 0, 'matched_tva' => 0, 'a_valider' => 0, 'sans_justificatif' => 0, 'ignore' => 0);
		$nbLotsAJour = 0;
		foreach ($lotsData as $ld) {
			foreach ($ld['compteurs'] as $statut => $nb) {
				$compteursGlobal[$statut] = isset($compteursGlobal[$statut]) ? $compteursGlobal[$statut] + $nb : $nb;
			}
			if ($ld['compteurs']['a_valider'] == 0 && $ld['compteurs']['sans_justificatif'] == 0) {
				$nbLotsAJour++;
			}
		}
		$nbRapprocheesGlobal = $compteursGlobal['matched_facture'] + $compteursGlobal['matched_charge'] + $compteursGlobal['matched_tva'];
		?>

		<div class="row">
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-check-circle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Rapprochées automatiquement</div>
								<div class="dash-counts"><p><?= $nbRapprocheesGlobal ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1"><i class="fa fa-clock"></i></span>
							<div class="dash-count">
								<div class="dash-title">À valider</div>
								<div class="dash-counts"><p><?= $compteursGlobal['a_valider'] ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0 <?= $compteursGlobal['sans_justificatif'] > 0 ? 'kpi-blink' : '' ?>">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-9"><i class="fa fa-exclamation-triangle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Sans justificatif</div>
								<div class="dash-counts"><p><?= $compteursGlobal['sans_justificatif'] ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><i class="far fa-file-excel mr-2 text-success"></i>Importer un relevé bancaire</h4>
					</div>
					<div class="card-body">
						<div id="rapprochementImportZone">
							<p class="text-muted mb-3" style="font-size:0.85rem;">
								Déposez un relevé bancaire (CSV ou PDF) : le CRM identifie automatiquement le compte concerné
								(RIB/IBAN/nom de banque lus dans le document, pas besoin de le sélectionner), lit chaque ligne,
								associe automatiquement les crédits aux factures clients, regroupe les commissions bancaires,
								reconnaît les débits récurrents, et signale en rouge tout débit sans justificatif. Rien n'est
								enregistré tant que vous n'avez pas validé l'aperçu ci-dessous.
							</p>
							<div id="rapprochementDropzone" class="ia-dropzone">
								<input type="file" id="rapprochementFileInput" accept=".csv,.pdf" style="display:none;">
								<div class="ia-dropzone-text">
									<i class="fas fa-cloud-upload-alt mb-2" style="font-size:1.8rem;"></i><br>
									Glissez-déposez le relevé (CSV ou PDF) ici, ou cliquez pour sélectionner
								</div>
							</div>
							<div id="rapprochementMsgBox" class="mt-3"></div>

							<div class="rapprochement-comptes-zone">
								<strong style="font-size:0.85rem;">Comptes bancaires</strong>
								<div class="rapprochement-comptes-grille mt-2">
								<?php if (empty($banks)) :?>
									<p class="text-muted mb-0" style="font-size:0.85rem;">
										Aucun compte configuré pour cette agence — ajoutez-en un dans
										<a href="index.php?option=com_bank">Gestion des banques</a>.
									</p>
								<?php else :?>
									<?php foreach ($banks as $b) :?>
										<?php
										$importe = releveLigne::existePourBank($b->getId());
										$nomCompte = $b->getLabel() !== null && $b->getLabel() !== '' ? $b->getLabel() : ($b->getRaisonSociale() !== null && $b->getRaisonSociale() !== '' ? $b->getRaisonSociale() : $b->getBanque());
										?>
										<div class="rapprochement-compte-carte <?= $importe ? 'ok' : 'manquant' ?>">
											<span class="rapprochement-compte-icone"><i class="fa <?= $importe ? 'fa-check' : 'fa-times' ?>"></i></span>
											<span class="rapprochement-compte-nom"><?= htmlspecialchars($nomCompte) ?></span>
										</div>
									<?php endforeach;?>
								<?php endif;?>
								</div>
							</div>
						</div>

						<!-- Aperçu avant écriture en base - rempli par previewReleve() en AJAX, rien n'est
						     enregistré tant que "Valider la lecture" n'a pas été cliqué explicitement. -->
						<div id="rapprochementApercuZone" style="display:none;"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card-header pl-0">
					<h4 class="card-title">Relevés importés<?= empty($lotsData) ? ' — aucun pour le moment' : '' ?></h4>
				</div>

				<?php if (!empty($lotsData)) : ?>
				<!-- Filtres rapides : masque/affiche les lots (pas les lignes individuelles) selon
				     ce qu'il reste à traiter dedans - mêmes compteurs déjà affichés en badge sur
				     chaque en-tête de lot. -->
				<div class="quick-filter-chips mb-3">
					<button type="button" class="active" data-filter="all">Tous les relevés <span class="badge badge-pill ml-1"><?= sizeof($lotsData) ?></span></button>
					<button type="button" data-filter="a_valider">À valider <span class="badge badge-pill ml-1"><?= $compteursGlobal['a_valider'] ?></span></button>
					<button type="button" data-filter="sans_justificatif">Sans justificatif <span class="badge badge-pill ml-1"><?= $compteursGlobal['sans_justificatif'] ?></span></button>
					<button type="button" data-filter="a_jour">À jour <span class="badge badge-pill ml-1"><?= $nbLotsAJour ?></span></button>
				</div>
				<?php endif; ?>

				<?php foreach ($lotsData as $index => $ld) :
					$lot = $ld['lot'];
					$compteurs = $ld['compteurs'];
					$lignesLot = $ld['lignes'];
					$nbRapprocheesLot = $compteurs['matched_facture'] + $compteurs['matched_charge'] + $compteurs['matched_tva'];
					$bankLot = $lot->getBank();
					$nomCompteLot = $bankLot ? ($bankLot->getLabel() !== null && $bankLot->getLabel() !== '' ? $bankLot->getLabel() : ($bankLot->getRaisonSociale() !== null && $bankLot->getRaisonSociale() !== '' ? $bankLot->getRaisonSociale() : $bankLot->getBanque())) : '—';
					$expanded = ($index === 0);
					$fromExport = $lot->getDateDebut() ? date('d/m/Y', strtotime($lot->getDateDebut())) : '';
					$toExport = $lot->getDateFin() ? date('d/m/Y', strtotime($lot->getDateFin())) : '';
				?>
				<div class="card rapprochement-lot-carte mb-3" data-nb-a-valider="<?= $compteurs['a_valider'] ?>" data-nb-sans-justificatif="<?= $compteurs['sans_justificatif'] ?>">
					<div class="card-header rapprochement-lot-header" data-toggle="collapse" data-target="#lot-<?= $lot->getId() ?>" aria-expanded="<?= $expanded ? 'true' : 'false' ?>">
						<div class="d-flex justify-content-between align-items-center flex-wrap">
							<div class="rapprochement-lot-titre">
								<strong><?= htmlspecialchars($nomCompteLot) ?></strong>
								<span class="rapprochement-lot-periode"><?= htmlspecialchars($lot->getPeriodeLibelle()) ?></span>
								<span class="rapprochement-lot-dates"><?= $fromExport ?> → <?= $toExport ?></span>
							</div>
							<div class="d-flex align-items-center flex-wrap">
								<span class="badge bg-success-light mr-1"><?= $nbRapprocheesLot ?> rapprochée(s)</span>
								<?php if ($compteurs['a_valider'] > 0) :?><span class="badge bg-warning-light mr-1"><?= $compteurs['a_valider'] ?> à valider</span><?php endif;?>
								<?php if ($compteurs['sans_justificatif'] > 0) :?><span class="badge bg-danger-light mr-1"><?= $compteurs['sans_justificatif'] ?> sans justificatif</span><?php endif;?>
								<button type="button" class="btn btn-white btn-sm ml-2 rapprochement-exporter-lot" data-toggle="tooltip" title="Dossier comptable de ce lot"
									data-export-href="components/com_accounting/controleurs/router.php?task=exportTvaComptable&from=<?= urlencode($fromExport) ?>&to=<?= urlencode($toExport) ?>"
									data-lot-compte="<?= htmlspecialchars($nomCompteLot) ?>" data-lot-periode="<?= htmlspecialchars($lot->getPeriodeLibelle()) ?>"
									data-lot-dates="<?= htmlspecialchars($fromExport . ' → ' . $toExport) ?>"
									data-nb-rapprochees="<?= $nbRapprocheesLot ?>" data-nb-a-valider="<?= $compteurs['a_valider'] ?>" data-nb-sans-justificatif="<?= $compteurs['sans_justificatif'] ?>"
								><i class="far fa-file-excel text-success"></i></button>
								<button type="button" class="btn btn-white btn-sm ml-2 rapprochement-supprimer-lot" data-toggle="tooltip" title="Supprimer cet import" data-lot-import="<?= htmlspecialchars($lot->getLotImport()) ?>" data-lot-compte="<?= htmlspecialchars($nomCompteLot) ?>" data-lot-periode="<?= htmlspecialchars($lot->getPeriodeLibelle()) ?>" data-lot-resume='<?= htmlspecialchars(json_encode($ld['resume_suppression']), ENT_QUOTES, "UTF-8") ?>'><i class="fa fa-trash text-danger"></i></button>
								<span class="rapprochement-lot-toggle ml-2" aria-expanded="<?= $expanded ? 'true' : 'false' ?>"><i class="fa fa-chevron-down"></i></span>
							</div>
						</div>
					</div>
					<div class="collapse<?= $expanded ? ' show' : '' ?>" id="lot-<?= $lot->getId() ?>">
						<div class="card-body">
							<div class="table-responsive rapprochement-lot-table-wrap">
								<table class="table table-border table-striped custom-table mb-0">
									<thead>
										<tr>
											<th>Date</th>
											<th>Libellé</th>
											<th>Débit</th>
											<th>Crédit</th>
											<th>Statut</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($lignesLot as $l) :?>
										<?php $infos = $l->getDonneesMatchingArray(); ?>
										<tr data-id="<?= $l->getId() ?>" data-libelle="<?= htmlspecialchars($l->getLibelle(), ENT_QUOTES, 'UTF-8') ?>" data-debit="<?= $l->getDebit() ?>" data-match-type="<?= isset($infos['type']) ? htmlspecialchars($infos['type']) : '' ?>" data-employe-suggere='<?= isset($infos['employe_suggere']) && $infos['employe_suggere'] ? htmlspecialchars(json_encode($infos['employe_suggere']), ENT_QUOTES, "UTF-8") : '' ?>'>
											<td><?= date('d/m/Y', strtotime($l->getDateOperation())) ?></td>
											<td><?= htmlspecialchars($l->getLibelle()) ?></td>
											<td><?= $l->getDebit() ? number_format($l->getDebit(), 2, ',', ' ') . ' DH' : '' ?></td>
											<td><?= $l->getCredit() ? number_format($l->getCredit(), 2, ',', ' ') . ' DH' : '' ?></td>
											<td>
												<?php
												switch ($l->getStatut()) {
													case 'matched_facture':
														echo '<span class="badge bg-success-light"><i class="fa fa-check mr-1"></i>Facture rapprochée</span>';
														break;
													case 'matched_charge':
														echo isset($infos['type']) && $infos['type'] === 'debit_commission'
															? '<span class="badge bg-success-light"><i class="fa fa-check mr-1"></i>Commission (agrégée)</span>'
															: '<span class="badge bg-success-light"><i class="fa fa-check mr-1"></i>Charge créée</span>';
														break;
													case 'matched_tva':
														echo '<span class="badge bg-success-light"><i class="fa fa-check mr-1"></i>TVA rapprochée</span>';
														break;
													case 'sans_justificatif':
														echo '<span class="badge bg-danger-light rapprochement-statut-clickable" data-toggle="tooltip" title="Cliquer pour insérer le justificatif"><i class="fa fa-exclamation-triangle mr-1"></i>Débit de ' . number_format($l->getDebit(), 2, ',', ' ') . ' DH — Facture manquante</span>';
														break;
													case 'ignore':
														echo '<span class="badge badge-secondary">Ignorée</span>';
														break;
													default:
														if (isset($infos['type']) && $infos['type'] === 'debit_reconnu') {
															echo '<span class="badge bg-warning-light rapprochement-statut-clickable" data-toggle="tooltip" title="Cliquer pour valider">À valider : ' . htmlspecialchars($infos['titre']) . '</span>';
														} elseif (isset($infos['type']) && $infos['type'] === 'debit_tva') {
															echo '<span class="badge bg-warning-light rapprochement-statut-clickable" data-toggle="tooltip" title="Cliquer pour confirmer la TVA">Paiement TVA détecté (' . htmlspecialchars($infos['periode_detectee']) . ') — à confirmer</span>';
														} elseif (isset($infos['type']) && $infos['type'] === 'credit_ambigu') {
															echo '<span class="badge bg-warning-light rapprochement-statut-clickable" data-toggle="tooltip" title="Cliquer pour choisir la facture">À valider (' . count($infos['candidats']) . ' facture(s) candidate(s))</span>';
														} elseif (isset($infos['type']) && $infos['type'] === 'debit_charge_existante') {
															echo '<span class="badge bg-warning-light rapprochement-statut-clickable" data-toggle="tooltip" title="Cliquer pour choisir la charge">Charge existante trouvée — à confirmer</span>';
														} else {
															echo '<span class="badge bg-warning-light rapprochement-statut-clickable" data-toggle="tooltip" title="Cliquer pour traiter">À valider</span>';
														}
												}
												?>
											</td>
											<td>
												<?php if ($l->getStatut() === 'a_valider') :?>
													<?php if (isset($infos['type']) && $infos['type'] === 'debit_tva') :?>
														<button type="button" class="btn btn-primary btn-sm rapprochement-tva-confirmer" data-toggle="tooltip" title="Confirmer la déclaration TVA" data-tva-info='<?= htmlspecialchars(json_encode($infos), ENT_QUOTES, "UTF-8") ?>'><i class="fa fa-file-invoice-dollar"></i></button>
														<button type="button" class="btn btn-white btn-sm rapprochement-ignorer" data-toggle="tooltip" title="Ignorer"><i class="fa fa-times"></i></button>
													<?php else :?>
														<?php if (isset($infos['type']) && $infos['type'] === 'credit_ambigu') :?>
															<?php $idsCandidats = array_column($infos['candidats'], 'id_facture');?>
															<!-- Simple porteur de valeur - la sélection se fait dans la fenêtre #affecterModal (même
															     habillage que les autres fenêtres du module), jamais dans ce <select> caché. -->
															<select class="rapprochement-facture-select d-none">
																<option value="">Choisir la facture...</option>
																<?php if (!empty($infos['candidats'])) :?>
																<optgroup label="Candidat(s) détecté(s) (même montant)">
																	<?php foreach ($infos['candidats'] as $c) :?>
																	<option value="<?= $c['id_facture'] ?>">N°<?= htmlspecialchars($c['numero']) ?> — <?= htmlspecialchars($c['client']) ?> (<?= number_format($c['montant'], 2, ',', ' ') ?> DH)</option>
																	<?php endforeach;?>
																</optgroup>
																<?php endif;?>
																<optgroup label="Toutes les factures ouvertes">
																	<?php foreach ($facturesOuvertes as $f) :?>
																		<?php if (in_array($f->getId(), $idsCandidats)) { continue; }?>
																		<?php $client = $f->getClient(); $nomClient = $client ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : '';?>
																	<option value="<?= $f->getId() ?>">N°<?= htmlspecialchars($f->getNumero()) ?> — <?= htmlspecialchars($nomClient) ?> (reste <?= number_format($f->getReste(), 2, ',', ' ') ?> DH)</option>
																	<?php endforeach;?>
																</optgroup>
															</select>
															<button type="button" class="btn btn-white btn-sm rapprochement-choisir" data-toggle="tooltip" title="Choisir la facture" data-affecter-type="facture"><i class="fa fa-search mr-1"></i>Choisir</button>
														<?php elseif (isset($infos['type']) && $infos['type'] === 'debit_charge_existante') :?>
															<?php $idsCandidatsCharge = array_column($infos['candidats'], 'id');?>
															<select class="rapprochement-charge-select d-none">
																<option value="">Choisir la charge...</option>
																<optgroup label="Candidat(s) détecté(s) (même montant, date proche)">
																	<?php foreach ($infos['candidats'] as $c) :?>
																	<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['titre']) ?> — <?= date('d/m/Y', strtotime($c['date'])) ?> (<?= number_format($c['montant'], 2, ',', ' ') ?> DH)</option>
																	<?php endforeach;?>
																</optgroup>
																<optgroup label="Toutes les charges disponibles">
																	<?php foreach ($chargesDisponibles as $c) :?>
																		<?php if (in_array($c->getId(), $idsCandidatsCharge)) { continue; }?>
																	<option value="<?= $c->getId() ?>"><?= htmlspecialchars($c->getTitre()) ?> — <?= $c->getDateCharge() ? date('d/m/Y', strtotime($c->getDateCharge())) : '' ?> (<?= number_format($c->getTotal(), 2, ',', ' ') ?> <?= htmlspecialchars($c->getDevise()) ?>)</option>
																	<?php endforeach;?>
																</optgroup>
															</select>
															<button type="button" class="btn btn-white btn-sm rapprochement-choisir" data-toggle="tooltip" title="Choisir la charge" data-affecter-type="charge"><i class="fa fa-search mr-1"></i>Choisir</button>
														<?php elseif (isset($infos['type']) && $infos['type'] === 'debit_reconnu') :?>
															<!-- Un achat comme celui-ci (fournisseur récurrent reconnu) peut déjà avoir été saisi
															     manuellement ce mois-ci - "Choisir" permet de lier cette charge existante plutôt
															     que d'en créer systématiquement une nouvelle. -->
															<select class="rapprochement-charge-select d-none">
																<option value="">Choisir la charge...</option>
																<optgroup label="Toutes les charges disponibles">
																	<?php foreach ($chargesDisponibles as $c) :?>
																	<option value="<?= $c->getId() ?>"><?= htmlspecialchars($c->getTitre()) ?> — <?= $c->getDateCharge() ? date('d/m/Y', strtotime($c->getDateCharge())) : '' ?> (<?= number_format($c->getTotal(), 2, ',', ' ') ?> <?= htmlspecialchars($c->getDevise()) ?>)</option>
																	<?php endforeach;?>
																</optgroup>
															</select>
															<button type="button" class="btn btn-white btn-sm rapprochement-choisir" data-toggle="tooltip" title="Choisir une charge déjà existante" data-affecter-type="charge"><i class="fa fa-search mr-1"></i>Choisir</button>
														<?php endif;?>
														<button type="button" class="btn btn-success btn-sm rapprochement-valider" data-toggle="tooltip" title="Valider"><i class="fa fa-check"></i></button>
														<button type="button" class="btn btn-white btn-sm rapprochement-ignorer" data-toggle="tooltip" title="Ignorer"><i class="fa fa-times"></i></button>
													<?php endif;?>
												<?php elseif ($l->getStatut() === 'sans_justificatif') :?>
													<button type="button" class="btn btn-danger btn-sm rapprochement-justificatif-manuel" data-toggle="tooltip" title="Insérer le justificatif"><i class="fa fa-paperclip"></i></button>
													<button type="button" class="btn btn-white btn-sm rapprochement-ignorer" data-toggle="tooltip" title="Ignorer"><i class="fa fa-times"></i></button>
												<?php else :?>
													—
												<?php endif;?>
											</td>
										</tr>
										<?php endforeach;?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach;?>
			</div>
		</div>

	</div>
</div>
<!-- /Page Wrapper -->

<!-- Popup "Confirmer le paiement TVA" — un débit dont le libellé mentionne la TVA est détecté
     (période calculée à partir de la date de l'opération), mais jamais rapproché automatiquement :
     l'utilisateur doit toujours confirmer explicitement à quelle déclaration ce relevé correspond. -->
<div id="tvaRapprochementModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="tva-confirm-icon"><i class="fa fa-file-invoice-dollar"></i></div>
				<h5 class="modal-title mt-3">Confirmer le paiement TVA</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-3" style="font-size:0.9rem;">
					Période détectée à partir de la date de l'opération : <strong id="tvaRapprochementPeriode">—</strong>
				</p>
				<div id="tvaRapprochementListe"></div>
				<p class="text-muted text-center mb-0 d-none" id="tvaRapprochementVide" style="font-size:0.85rem;">
					Aucune déclaration TVA trouvée à proximité de cette période —
					<a href="index.php?option=com_accounting&task=tva" target="_blank">ajoutez-la d'abord</a>.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" id="tvaRapprochementConfirmerBtn"><i class="fa fa-check mr-1"></i> Confirmer</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup "Insérer le justificatif" — pour un débit sans charge existante ni fournisseur reconnu,
     3 façons de le résoudre sans quitter la page : charge simple, bulletin de paie (le virement
     ressemble à un salaire - employé suggéré si détecté), ou achat lié à un fournisseur existant.
     Le relevé bancaire du lot sert de justificatif par défaut, sauf si un fichier différent est
     déposé ici. -->
<div id="justificatifManuelModal" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">Insérer le justificatif</h5>
			</div>
			<div class="modal-body">
				<div class="justificatif-mode-switch mb-3">
					<label class="justificatif-mode-item">
						<input type="radio" name="justificatifMode" value="charge" checked>
						<span><i class="fa fa-receipt mr-1"></i>Charge simple</span>
					</label>
					<label class="justificatif-mode-item">
						<input type="radio" name="justificatifMode" value="payslip">
						<span><i class="fa fa-user-tie mr-1"></i>Bulletin de paie</span>
					</label>
					<label class="justificatif-mode-item">
						<input type="radio" name="justificatifMode" value="fournisseur">
						<span><i class="fa fa-truck mr-1"></i>Fournisseur</span>
					</label>
				</div>

				<div id="justificatifSuggestionEmploye" class="alert alert-info d-none" style="font-size:0.82rem;"></div>

				<div id="justificatifZoneCharge">
					<div class="form-group">
						<label>Titre de la charge</label>
						<input type="text" class="form-control" id="justificatifTitre">
					</div>
				</div>

				<div id="justificatifZonePayslip" class="d-none">
					<div class="form-group">
						<label>Employé</label>
						<select class="form-control" id="justificatifResourcehumaine">
							<option value="">Choisir l'employé...</option>
							<?php foreach ($employesActifs as $e) :?>
							<option value="<?= $e->getId() ?>"><?= htmlspecialchars($e->getFullName()) ?></option>
							<?php endforeach;?>
						</select>
					</div>
					<div id="justificatifBulletinsExistants" class="justificatif-bulletins-existants mb-3 d-none">
						<div class="form-group mb-0">
							<label>Bulletin pour cet employé</label>
							<select class="form-control" id="justificatifPayslipExistant">
								<option value="">— Créer un nouveau bulletin —</option>
							</select>
						</div>
					</div>
					<div id="justificatifPayslipNouveauZone" class="row">
						<div class="col-6 form-group">
							<label>Mois</label>
							<select class="form-control" id="justificatifPayslipMois">
								<?php $moisNoms = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');?>
								<?php foreach ($moisNoms as $num => $nom) :?>
								<option value="<?= $num ?>"><?= $nom ?></option>
								<?php endforeach;?>
							</select>
						</div>
						<div class="col-6 form-group">
							<label>Année</label>
							<input type="number" class="form-control" id="justificatifPayslipAnnee">
						</div>
					</div>
				</div>

				<div id="justificatifZoneFournisseur" class="d-none">
					<div class="form-group">
						<label>Fournisseur</label>
						<select class="form-control" id="justificatifFournisseur">
							<option value="">Choisir le fournisseur...</option>
							<?php foreach ($fournisseursActifs as $f) :?>
							<?php $nomF = trim((string) $f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getPrenom() . ' ' . $f->getNom());?>
							<option value="<?= $f->getId() ?>"><?= htmlspecialchars($nomF) ?></option>
							<?php endforeach;?>
						</select>
					</div>
					<div class="form-group">
						<label>Titre de la charge (optionnel)</label>
						<input type="text" class="form-control" id="justificatifTitreFournisseur">
					</div>
				</div>

				<div class="form-group">
					<label>Montant (DH)</label>
					<input type="text" class="form-control" id="justificatifMontant">
				</div>
				<div class="form-group mb-0">
					<label>Justificatif (optionnel — sinon le relevé bancaire du lot est utilisé)</label>
					<input type="file" class="form-control" id="justificatifFichier" accept=".jpg,.jpeg,.png,.gif,.pdf">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" id="justificatifConfirmerBtn"><i class="fa fa-check mr-1"></i> Créer la charge</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup optionnel "Valider avec justificatif" — pour un débit reconnu (ex: Achat Genious) ou
     une charge existante trouvée : la même action (créer/lier la charge) mais avec la possibilité
     d'attacher directement la facture d'achat, sans jamais bloquer la validation si le document
     n'est pas encore sous la main. -->
<div id="validerJustificatifModal" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">Confirmer la validation</h5>
			</div>
			<div class="modal-body">
				<p id="validerJustificatifTexte" class="mb-3" style="font-size:0.9rem;"></p>
				<div class="form-group mb-0">
					<label>Facture d'achat / justificatif (optionnel)</label>
					<input type="file" class="form-control" id="validerJustificatifFichier" accept=".jpg,.jpeg,.png,.gif,.pdf">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" id="validerJustificatifConfirmerBtn"><i class="fa fa-check mr-1"></i> Confirmer</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup "Choisir la facture / la charge" — même habillage que les autres fenêtres du module
     (au lieu d'un <select> cramponné dans la cellule du tableau) : recherche parmi les candidats
     détectés automatiquement ET l'ensemble des factures ouvertes / charges disponibles de
     l'agence, pour ne jamais bloquer l'utilisateur au seul auto-matching. -->
<div id="affecterModal" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="affecterModalTitre">Choisir la facture correspondante</h5>
			</div>
			<div class="modal-body">
				<div class="form-group mb-0">
					<select id="affecterSelect" class="form-control" style="width:100%;"></select>
				</div>

				<!-- Ne s'affiche que pour une charge (jamais pour une facture) : si aucune charge de la
				     liste ne correspond réellement, l'utilisateur crée la charge manquante ici même,
				     plutôt que d'être bloqué au seul choix parmi les charges existantes. -->
				<div id="affecterNouvelleChargeZone" class="d-none mt-3 pt-3" style="border-top:1px dashed #e2e8f0;">
					<div class="form-group">
						<label>Titre de la charge</label>
						<input type="text" class="form-control" id="affecterNouvelleChargeTitre">
					</div>
					<div class="row">
						<div class="col-7 form-group">
							<label>Montant (DH)</label>
							<input type="text" class="form-control" id="affecterNouvelleChargeMontant">
						</div>
						<div class="col-5 form-group">
							<label>Type</label>
							<select class="form-control" id="affecterNouvelleChargeType">
								<option value="variable">Variable</option>
								<option value="fixe">Fixe</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" id="affecterConfirmerBtn"><i class="fa fa-check mr-1"></i> Confirmer</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup "Supprimer cet import" — annulation complète d'un lot déjà confirmé : jamais silencieux,
     la fenêtre détaille explicitement ce qui sera défait (paiements, charges, bulletins de paie,
     déclarations TVA) avant que l'utilisateur ne confirme. -->
<div id="supprimerLotModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="tva-confirm-icon" style="background:linear-gradient(135deg, #f87171, #b91c1c); box-shadow:0 8px 20px rgba(185, 28, 28, 0.35);"><i class="fa fa-trash"></i></div>
				<h5 class="modal-title mt-3">Supprimer cet import ?</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-3" style="font-size:0.9rem;">
					<strong id="supprimerLotTitre">—</strong>
				</p>
				<div id="supprimerLotResume" class="rapprochement-suppression-resume"></div>
				<p class="text-muted mt-3 mb-0 text-center" style="font-size:0.78rem;">
					Cette action est irréversible.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-danger" id="supprimerLotConfirmerBtn"><i class="fa fa-trash mr-1"></i> Supprimer définitivement</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup "Pousser ce relevé dans le dossier comptable" — étape de vérification avant d'exporter :
     rappelle où en est le lot (rapprochées / à valider / sans justificatif) pour que l'utilisateur
     confirme en connaissance de cause plutôt que de pousser des données encore incomplètes. -->
<div id="exportLotModal" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">Pousser ce relevé dans le dossier comptable ?</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-3" style="font-size:0.9rem;">
					<strong id="exportLotTitre">—</strong>
				</p>
				<div id="exportLotResume" class="rapprochement-suppression-resume"></div>
				<p class="text-muted mt-3 mb-0" style="font-size:0.8rem;">
					Vérifiez ces chiffres avant de continuer — l'export du dossier comptable inclut toutes
					les charges/paiements de la période, y compris ceux issus des lignes encore à traiter.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" id="exportLotConfirmerBtn"><i class="far fa-file-excel mr-1"></i> Confirmer et exporter</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function () {

	function escHtml(s) {
		return $('<div>').text(s === undefined || s === null ? '' : s).html();
	}
	function fmtMontant(n) {
		return parseFloat(n || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
	}
	function formatDateFr(iso) {
		if (!iso) { return ''; }
		var p = iso.split('-');
		return p.length === 3 ? (p[2] + '/' + p[1] + '/' + p[0]) : iso;
	}

	// Filtres rapides (Tous / À valider / Sans justificatif / À jour) : masque/affiche les cartes
	// de lot directement (pas de DataTables ici, chaque lot est une carte indépendante avec sa
	// propre table interne), à partir des compteurs déjà posés en data-* sur .rapprochement-lot-carte.
	$(document).on('click', '.quick-filter-chips button', function () {
		var filtre = $(this).data('filter');
		$('.quick-filter-chips button').removeClass('active');
		$(this).addClass('active');
		$('.rapprochement-lot-carte').each(function () {
			var $carte = $(this);
			var aValider = parseInt($carte.attr('data-nb-a-valider'), 10) || 0;
			var sansJustificatif = parseInt($carte.attr('data-nb-sans-justificatif'), 10) || 0;
			var visible = true;
			if (filtre === 'a_valider') { visible = aValider > 0; }
			else if (filtre === 'sans_justificatif') { visible = sansJustificatif > 0; }
			else if (filtre === 'a_jour') { visible = aValider === 0 && sansJustificatif === 0; }
			$carte.toggle(visible);
		});
	});

	var dropzone = $('#rapprochementDropzone');
	var fileInput = $('#rapprochementFileInput');

	dropzone.on('click', function (e) {
		// Même garde que assets/js/ia-dropzone.js : évite la boucle infinie quand le clic bubblé
		// vient de fileInput.trigger('click') lui-même (ex: bouton .ia-dropzone-browse-btn).
		if (e.target === fileInput[0]) {
			return;
		}
		fileInput.trigger('click');
	});
	dropzone.on('dragover', function (e) {
		e.preventDefault();
		dropzone.addClass('ia-dropzone-hover');
	});
	dropzone.on('dragleave', function () {
		dropzone.removeClass('ia-dropzone-hover');
	});
	dropzone.on('drop', function (e) {
		e.preventDefault();
		dropzone.removeClass('ia-dropzone-hover');
		var f = e.originalEvent.dataTransfer.files[0];
		if (f) {
			importerFichier(f);
		}
	});
	fileInput.on('change', function () {
		if (this.files[0]) {
			importerFichier(this.files[0]);
		}
	});

	function importerFichier(fichier) {
		$('#rapprochementMsgBox').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin mr-2"></i>Analyse du relevé en cours (détection du compte, lecture des lignes)...</div>');

		var formData = new FormData();
		formData.append('document[]', fichier);

		$.ajax({
			url: 'components/com_rapprochement/controleurs/router.php?task=previewReleve',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				$('#rapprochementMsgBox').empty();
				if (response.success) {
					afficherApercu(response);
				} else {
					$('#rapprochementMsgBox').html('<div class="alert alert-danger">' + escHtml(response.message || "Erreur lors de l'analyse") + '</div>');
				}
			},
			error: function () {
				$('#rapprochementMsgBox').html('<div class="alert alert-danger">Erreur lors de l\'analyse.</div>');
			}
		});
	}

	// ---- Aperçu avant validation ------------------------------------------------------------
	var apercuCourant = null;

	function badgeStatutLigne(l) {
		var infos = l.donnees_matching || {};
		if (l.statut === 'sans_justificatif') {
			return '<span class="badge bg-danger-light"><i class="fa fa-exclamation-triangle mr-1"></i>Sans justificatif</span>';
		}
		if (infos.type === 'debit_commission') {
			return '<span class="badge bg-info-light">Commission bancaire (agrégée)</span>';
		}
		if (infos.type === 'credit_rapproche') {
			return '<span class="badge bg-success-light"><i class="fa fa-check mr-1"></i>Facture N°' + escHtml(infos.facture ? infos.facture.numero : '') + ' (auto à la validation)</span>';
		}
		if (infos.type === 'debit_reconnu') {
			return '<span class="badge bg-warning-light">À valider : ' + escHtml(infos.titre) + '</span>';
		}
		if (infos.type === 'debit_tva') {
			return '<span class="badge bg-warning-light">Paiement TVA détecté (' + escHtml(infos.periode_detectee) + ') — à confirmer</span>';
		}
		if (infos.type === 'credit_ambigu') {
			return '<span class="badge bg-warning-light">À valider (' + ((infos.candidats || []).length) + ' facture(s) candidate(s))</span>';
		}
		if (infos.type === 'debit_charge_existante') {
			return '<span class="badge bg-warning-light">Charge existante trouvée — à confirmer</span>';
		}
		return '<span class="badge bg-warning-light">À valider</span>';
	}

	function recalculerCommission() {
		if (!apercuCourant || !apercuCourant.commissions) { return; }
		var taux = parseFloat($('#apercuCommissionTaux').val());
		if (isNaN(taux) || taux < 0) { return; }
		var total = apercuCourant.commissions.total;
		var ht = Math.round((total / (1 + taux / 100)) * 100) / 100;
		var tva = Math.round((total - ht) * 100) / 100;
		apercuCourant.commissions.taux = taux;
		apercuCourant.commissions.ht = ht;
		apercuCourant.commissions.tva = tva;
		$('#apercuCommissionHt').val(fmtMontant(ht) + ' DH');
		$('#apercuCommissionTva').val(fmtMontant(tva) + ' DH');
	}

	function afficherApercu(r) {
		apercuCourant = r;
		var html = '';
		html += '<div class="alert alert-success mb-3">Compte détecté : <strong>' + escHtml(r.banque) + '</strong>'
			+ (r.periode_libelle ? ' — période <strong>' + escHtml(r.periode_libelle) + '</strong>' : '')
			+ (r.agence_basculee ? '<br><i class="fa fa-exchange-alt mr-1"></i>Ce compte appartient à <strong>' + escHtml(r.nouvelle_agence) + '</strong> — la session basculera sur cette agence à la validation.' : '')
			+ '</div>';

		html += '<div class="table-responsive mb-3"><table class="table table-sm table-striped mb-0"><thead><tr><th>Date</th><th>Libellé</th><th>Débit</th><th>Crédit</th><th>Statut proposé</th></tr></thead><tbody>';
		(r.lignes || []).forEach(function (l) {
			html += '<tr><td>' + formatDateFr(l.date_operation) + '</td><td>' + escHtml(l.libelle) + '</td>'
				+ '<td>' + (l.debit ? fmtMontant(l.debit) + ' DH' : '') + '</td>'
				+ '<td>' + (l.credit ? fmtMontant(l.credit) + ' DH' : '') + '</td>'
				+ '<td>' + badgeStatutLigne(l) + '</td></tr>';
		});
		html += '</tbody></table></div>';

		if (r.commissions) {
			html += '<div class="rapprochement-commissions-bloc mb-3">'
				+ '<h5><i class="fa fa-receipt mr-2"></i>Commissions bancaires détectées (' + r.commissions.nb_lignes + ' ligne(s))</h5>'
				+ '<p class="text-muted mb-3" style="font-size:0.82rem;">Regroupées en une seule charge, avec ce relevé bancaire comme justificatif. Le taux de TVA récupérable est modifiable avant validation.</p>'
				+ '<div class="row">'
				+ '<div class="col-md-3 form-group"><label style="font-size:0.78rem;">Total (TTC)</label><input type="text" class="form-control" value="' + fmtMontant(r.commissions.total) + ' DH" disabled></div>'
				+ '<div class="col-md-3 form-group"><label style="font-size:0.78rem;">Taux TVA (%)</label><input type="number" step="0.01" min="0" class="form-control" id="apercuCommissionTaux" value="' + r.commissions.taux + '"></div>'
				+ '<div class="col-md-3 form-group"><label style="font-size:0.78rem;">Montant HT</label><input type="text" class="form-control" id="apercuCommissionHt" value="' + fmtMontant(r.commissions.ht) + ' DH" disabled></div>'
				+ '<div class="col-md-3 form-group"><label style="font-size:0.78rem;">TVA récupérable</label><input type="text" class="form-control" id="apercuCommissionTva" value="' + fmtMontant(r.commissions.tva) + ' DH" disabled></div>'
				+ '</div></div>';
		}

		html += '<div class="d-flex justify-content-end">'
			+ '<button type="button" class="btn btn-white mr-2" id="apercuAnnulerBtn">Annuler</button>'
			+ '<button type="button" class="btn btn-primary" id="apercuValiderBtn"><i class="fa fa-check mr-1"></i> Valider la lecture</button>'
			+ '</div>';

		$('#rapprochementImportZone').hide();
		$('#rapprochementApercuZone').html(html).show();

		if (typeof gsap !== 'undefined') {
			gsap.from('#rapprochementApercuZone', { y: 12, opacity: 0, duration: 0.4, ease: 'power2.out', clearProps: 'all' });
		}
	}

	$(document).on('input', '#apercuCommissionTaux', recalculerCommission);

	$(document).on('click', '#apercuAnnulerBtn', function () {
		apercuCourant = null;
		$('#rapprochementApercuZone').hide().empty();
		$('#rapprochementImportZone').show();
		fileInput.val('');
	});

	$(document).on('click', '#apercuValiderBtn', function () {
		if (!apercuCourant) { return; }
		var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Validation...');
		$.post('components/com_rapprochement/controleurs/router.php?task=confirmerReleve', { payload: JSON.stringify(apercuCourant) }, function (response) {
			if (response.success) {
				window.location.reload();
			} else {
				alert(response.message || 'Erreur lors de la validation');
				$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Valider la lecture');
			}
		});
	});

	// ---- Actions par ligne ------------------------------------------------------------------
	// Pour un débit reconnu (ex: Achat Genious) ou une charge existante trouvée, "Valider" ouvre
	// désormais une petite fenêtre pour attacher optionnellement la facture d'achat/justificatif
	// avant de créer/lier la charge - jamais bloquant si le document n'est pas encore disponible.
	// Les autres cas (crédit ambigu, etc.) gardent le comportement immédiat d'avant.
	var validerLigneCourante = null;
	var validerIdChargeExistanteCourant = null;

	// ---- Fenêtre "Choisir la facture / la charge" - même habillage que les autres fenêtres du
	// module, plutôt qu'un <select> cramponné dans la cellule du tableau. ----------------------
	var affecterLigneCourante = null;
	var affecterTypeCourant = null;

	function affecterBasculerZoneNouvelleCharge(estNouvelleCharge) {
		$('#affecterNouvelleChargeZone').toggleClass('d-none', !estNouvelleCharge);
		$('#affecterConfirmerBtn').html(estNouvelleCharge
			? '<i class="fa fa-plus mr-1"></i> Créer et lier'
			: '<i class="fa fa-check mr-1"></i> Confirmer');
	}

	function ouvrirAffecterModal($tr, type) {
		affecterLigneCourante = $tr;
		affecterTypeCourant = type;
		var $selectSource = $tr.find(type === 'facture' ? '.rapprochement-facture-select' : '.rapprochement-charge-select');
		$('#affecterModalTitre').text(type === 'facture' ? 'Choisir la facture correspondante' : 'Choisir la charge correspondante');

		var $affecterSelect = $('#affecterSelect');
		if ($affecterSelect.hasClass('select2-hidden-accessible')) {
			$affecterSelect.select2('destroy');
		}
		var html = $selectSource.html();
		// Rien de la liste ne correspond forcément à un débit fourre-tout (ex: virement à un
		// employé pour tout autre chose que la charge suggérée par montant/date) - jamais bloquant.
		if (type === 'charge') {
			html = '<option value="__nouvelle__">+ Créer une nouvelle charge</option>' + html;
		}
		$affecterSelect.html(html).val($selectSource.val());
		affecterBasculerZoneNouvelleCharge(false);

		if (type === 'charge') {
			$('#affecterNouvelleChargeTitre').val($tr.data('libelle'));
			$('#affecterNouvelleChargeMontant').val($tr.data('debit'));
			$('#affecterNouvelleChargeType').val('variable');
		}

		$('#affecterModal').modal('show');
		$affecterSelect.select2({ dropdownParent: $('#affecterModal'), width: '100%' });
	}

	$(document).on('click', '.rapprochement-choisir', function () {
		var $tr = $(this).closest('tr');
		ouvrirAffecterModal($tr, $(this).data('affecter-type'));
	});

	$(document).on('change', '#affecterSelect', function () {
		affecterBasculerZoneNouvelleCharge(affecterTypeCourant === 'charge' && $(this).val() === '__nouvelle__');
	});

	$('#affecterConfirmerBtn').on('click', function () {
		var valeur = $('#affecterSelect').val();
		if (!valeur) {
			alert(affecterTypeCourant === 'facture' ? 'Choisissez une facture.' : 'Choisissez une charge.');
			return;
		}

		if (affecterTypeCourant === 'charge' && valeur === '__nouvelle__') {
			var titre = $('#affecterNouvelleChargeTitre').val();
			var montant = $('#affecterNouvelleChargeMontant').val();
			if (!montant || !parseFloat(montant.toString().replace(',', '.'))) {
				alert('Indiquez le montant de la charge.');
				return;
			}
			var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Création...');
			$.post('components/com_rapprochement/controleurs/router.php?task=creerChargeEtLier', {
				id: affecterLigneCourante.data('id'),
				titre: titre,
				montant: montant,
				type_charge: $('#affecterNouvelleChargeType').val()
			}, function (response) {
				if (response.success) {
					window.location.reload();
				} else {
					alert(response.message || 'Erreur lors de la création de la charge');
					$btn.prop('disabled', false).html('<i class="fa fa-plus mr-1"></i> Créer et lier');
				}
			});
			return;
		}

		var $selectSource = affecterLigneCourante.find(affecterTypeCourant === 'facture' ? '.rapprochement-facture-select' : '.rapprochement-charge-select');
		$selectSource.val(valeur);
		$('#affecterModal').modal('hide');
		affecterLigneCourante.find('.rapprochement-valider').trigger('click');
	});

	// ---- Supprimer un import entier (annulation complète du lot) ----------------------------
	// Liaison directe (pas de délégation document) : ces boutons vivent dans l'en-tête cliquable
	// du lot (data-toggle="collapse") - stopPropagation() ici les empêche aussi de déplier/replier
	// la carte, ce qu'un stopPropagation() posé APRÈS la délégation document ne pourrait plus faire.
	var lotImportASupprimer = null;
	$('.rapprochement-supprimer-lot').on('click', function (e) {
		e.stopPropagation();
		lotImportASupprimer = $(this).data('lot-import');
		$('#supprimerLotTitre').text($(this).data('lot-compte') + ' — ' + $(this).data('lot-periode'));

		var resume = null;
		try { resume = JSON.parse($(this).attr('data-lot-resume') || 'null'); } catch (e2) { resume = null; }
		var html = '<ul class="mb-0">';
		html += '<li>' + (resume ? resume.nb_lignes : 0) + ' ligne(s) de relevé seront supprimées</li>';
		if (resume && resume.nb_paiements > 0) {
			html += '<li><strong>' + resume.nb_paiements + '</strong> paiement(s) seront annulés (les factures liées redeviennent impayées/partiellement payées)</li>';
		}
		if (resume && resume.nb_charges > 0) {
			html += '<li><strong>' + resume.nb_charges + '</strong> charge(s) créée(s) par cet import (dont l\'agrégat des commissions bancaires, bulletins de paie compris) seront supprimées</li>';
		}
		if (resume && resume.nb_tva > 0) {
			html += '<li><strong>' + resume.nb_tva + '</strong> déclaration(s) TVA marquée(s) payée(s) par ce lot redeviendront non payées</li>';
		}
		if (resume && resume.nb_charges_liees > 0) {
			html += '<li>' + resume.nb_charges_liees + ' charge(s) existante(s) simplement déliée(s) (créées avant cet import, jamais supprimées)</li>';
		}
		html += '</ul>';
		$('#supprimerLotResume').html(html);

		$('#supprimerLotModal').modal('show');
	});

	$('#supprimerLotConfirmerBtn').on('click', function () {
		var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Suppression...');
		$.post('components/com_rapprochement/controleurs/router.php?task=supprimerLot', { lot_import: lotImportASupprimer }, function (response) {
			if (response.success) {
				window.location.reload();
			} else {
				alert(response.message || "Erreur lors de la suppression de l'import");
				$btn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Supprimer définitivement');
			}
		});
	});

	// ---- Vérifier avant de pousser le relevé dans le dossier comptable ----------------------
	var exportHrefCourant = null;
	$('.rapprochement-exporter-lot').on('click', function (e) {
		e.stopPropagation();
		exportHrefCourant = $(this).data('export-href');
		$('#exportLotTitre').text($(this).data('lot-compte') + ' — ' + $(this).data('lot-periode') + ' (' + $(this).data('lot-dates') + ')');

		var nbRapprochees = $(this).data('nb-rapprochees') || 0;
		var nbAValider = $(this).data('nb-a-valider') || 0;
		var nbSansJustificatif = $(this).data('nb-sans-justificatif') || 0;
		var html = '<ul class="mb-0">';
		html += '<li><strong>' + nbRapprochees + '</strong> ligne(s) rapprochée(s)</li>';
		html += '<li' + (nbAValider > 0 ? ' style="color:#b45309;"' : '') + '>' + nbAValider + ' ligne(s) encore à valider</li>';
		html += '<li' + (nbSansJustificatif > 0 ? ' style="color:#b91c1c;"' : '') + '>' + nbSansJustificatif + ' ligne(s) sans justificatif</li>';
		html += '</ul>';
		$('#exportLotResume').html(html);

		$('#exportLotModal').modal('show');
	});

	$('#exportLotConfirmerBtn').on('click', function () {
		if (exportHrefCourant) {
			window.open(exportHrefCourant, '_blank');
		}
		$('#exportLotModal').modal('hide');
	});

	$(document).on('click', '.rapprochement-valider', function () {
		var $tr = $(this).closest('tr');
		var id = $tr.data('id');
		var matchType = $tr.data('match-type');
		var idFacture = $tr.find('.rapprochement-facture-select').val();
		var idChargeExistante = $tr.find('.rapprochement-charge-select').val();

		if (matchType === 'debit_charge_existante' && !idChargeExistante) {
			ouvrirAffecterModal($tr, 'charge');
			return;
		}
		if (matchType === 'credit_ambigu' && !idFacture) {
			ouvrirAffecterModal($tr, 'facture');
			return;
		}

		if (matchType === 'debit_reconnu' || matchType === 'debit_charge_existante') {
			validerLigneCourante = id;
			validerIdChargeExistanteCourant = idChargeExistante;
			$('#validerJustificatifFichier').val('');
			$('#validerJustificatifTexte').text(matchType === 'debit_reconnu'
				? "Confirmer la création de la charge et attacher, si vous l'avez, la facture d'achat correspondante."
				: "Confirmer la liaison à la charge existante et attacher, si vous l'avez, la facture d'achat correspondante.");
			$('#validerJustificatifModal').modal('show');
			return;
		}

		$.post('components/com_rapprochement/controleurs/router.php?task=validerLigne', { id: id, id_facture: idFacture, id_charge_existante: idChargeExistante }, function (response) {
			if (response.success) {
				window.location.reload();
			} else {
				alert(response.message || 'Erreur lors de la validation');
			}
		});
	});

	$('#validerJustificatifConfirmerBtn').on('click', function () {
		var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Confirmation...');
		var formData = new FormData();
		formData.append('id', validerLigneCourante);
		if (validerIdChargeExistanteCourant) {
			formData.append('id_charge_existante', validerIdChargeExistanteCourant);
		}
		var fichier = $('#validerJustificatifFichier')[0].files[0];
		if (fichier) {
			formData.append('justificatif_valider[]', fichier);
		}
		$.ajax({
			url: 'components/com_rapprochement/controleurs/router.php?task=validerLigne',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					window.location.reload();
				} else {
					alert(response.message || 'Erreur lors de la validation');
					$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmer');
				}
			},
			error: function () {
				alert('Erreur lors de la validation');
				$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmer');
			}
		});
	});

	// Le badge de statut lui-même est cliquable : il déclenche exactement la même action que le
	// bouton principal de la ligne (Valider/Confirmer TVA/Insérer le justificatif/Choisir), pas
	// besoin de viser la petite icône dans la colonne Action.
	$(document).on('click', '.rapprochement-statut-clickable', function () {
		var $tr = $(this).closest('tr');
		var $boutonChoisir = $tr.find('.rapprochement-choisir').first();
		var $select = $tr.find('.rapprochement-facture-select, .rapprochement-charge-select').first();
		if ($boutonChoisir.length && $select.length && !$select.val()) {
			$boutonChoisir.trigger('click');
			return;
		}
		var $boutonPrincipal = $tr.find('.rapprochement-tva-confirmer, .rapprochement-justificatif-manuel, .rapprochement-valider').first();
		if ($boutonPrincipal.length) {
			$boutonPrincipal.trigger('click');
		}
	});

	$(document).on('click', '.rapprochement-ignorer', function () {
		var id = $(this).closest('tr').data('id');
		$.post('components/com_rapprochement/controleurs/router.php?task=ignorerLigne', { id: id }, function (response) {
			if (response.success) {
				window.location.reload();
			}
		});
	});

	var tvaLigneCourante = null;
	$(document).on('click', '.rapprochement-tva-confirmer', function () {
		var $tr = $(this).closest('tr');
		tvaLigneCourante = $tr.data('id');
		var infos = JSON.parse($(this).attr('data-tva-info'));

		$('#tvaRapprochementPeriode').text(infos.periode_detectee || '—');

		var $liste = $('#tvaRapprochementListe').empty();
		var candidats = infos.candidats || [];
		if (candidats.length === 0) {
			$('#tvaRapprochementVide').removeClass('d-none');
		} else {
			$('#tvaRapprochementVide').addClass('d-none');
			candidats.forEach(function (c) {
				var montant = parseFloat(c.montant).toLocaleString('fr-FR', { minimumFractionDigits: 2 });
				var $item = $(
					'<label class="rapprochement-tva-candidat' + (c.detecte ? ' detecte' : '') + '">' +
						'<input type="radio" name="tvaCandidatChoix" value="' + c.id + '"' + (c.detecte || candidats.length === 1 ? ' checked' : '') + '>' +
						'<span class="rapprochement-tva-candidat-periode">TVA ' + c.periode_libelle + '</span>' +
						'<span class="rapprochement-tva-candidat-montant">' + montant + ' DH</span>' +
						(c.deja_depose ? '<span class="rapprochement-tva-candidat-tag">déjà déposée</span>' : '') +
					'</label>'
				);
				$liste.append($item);
			});
		}

		$('#tvaRapprochementModal').modal('show');
	});

	$('#tvaRapprochementConfirmerBtn').on('click', function () {
		var idTva = $('#tvaRapprochementListe input[name=tvaCandidatChoix]:checked').val();
		if (!idTva) {
			alert('Choisissez la déclaration TVA correspondante.');
			return;
		}
		$.post('components/com_rapprochement/controleurs/router.php?task=validerLigne', { id: tvaLigneCourante, id_tva: idTva }, function (response) {
			if (response.success) {
				window.location.reload();
			} else {
				alert(response.message || 'Erreur lors de la confirmation');
			}
		});
	});

	// ---- Justificatif manuel : charge simple / bulletin de paie / fournisseur -------------
	var justificatifLigneCourante = null;

	function justificatifAfficherMode(mode) {
		$('#justificatifZoneCharge').toggleClass('d-none', mode !== 'charge');
		$('#justificatifZonePayslip').toggleClass('d-none', mode !== 'payslip');
		$('#justificatifZoneFournisseur').toggleClass('d-none', mode !== 'fournisseur');
		$('.justificatif-mode-item').each(function () {
			$(this).toggleClass('active', $(this).find('input').val() === mode);
		});
	}

	$(document).on('change', 'input[name=justificatifMode]', function () {
		justificatifAfficherMode($(this).val());
	});

	// Bulletins déjà enregistrés pour l'employé choisi : sélectionnable directement (lier plutôt
	// que recréer un doublon) - "Créer un nouveau bulletin" reste le choix par défaut.
	function justificatifBasculerVersNouveauBulletin(estNouveau) {
		$('#justificatifPayslipNouveauZone').toggleClass('d-none', !estNouveau);
		$('#justificatifMontant').closest('.form-group').toggleClass('d-none', !estNouveau);
		$('#justificatifFichier').closest('.form-group').toggleClass('d-none', !estNouveau);
		$('#justificatifConfirmerBtn').html(estNouveau ? '<i class="fa fa-check mr-1"></i> Créer la charge' : '<i class="fa fa-check mr-1"></i> Lier ce bulletin');
	}

	function chargerBulletinsExistants(idResourcehumaine) {
		var $zone = $('#justificatifBulletinsExistants');
		var $select = $('#justificatifPayslipExistant');
		$select.html('<option value="">— Créer un nouveau bulletin —</option>');
		justificatifBasculerVersNouveauBulletin(true);
		if (!idResourcehumaine) {
			$zone.addClass('d-none');
			return;
		}
		$zone.removeClass('d-none');
		$.post('components/com_rapprochement/controleurs/router.php?task=listerBulletinsPaie', { id_resourcehumaine: idResourcehumaine }, function (response) {
			if (!response.success || !response.bulletins.length) {
				return;
			}
			response.bulletins.forEach(function (b) {
				$select.append('<option value="' + b.id_charge + '">' + escHtml(b.title) + '</option>');
			});
		});
	}

	$(document).on('change', '#justificatifResourcehumaine', function () {
		chargerBulletinsExistants($(this).val());
	});

	$(document).on('change', '#justificatifPayslipExistant', function () {
		justificatifBasculerVersNouveauBulletin($(this).val() === '');
	});

	$(document).on('click', '.rapprochement-justificatif-manuel', function () {
		var $tr = $(this).closest('tr');
		justificatifLigneCourante = $tr.data('id');
		var libelle = $tr.data('libelle');
		var debit = $tr.data('debit');
		var dateOperation = $tr.find('td').first().text();

		$('#justificatifTitre').val(libelle);
		$('#justificatifTitreFournisseur').val('');
		$('#justificatifMontant').val(debit);
		$('#justificatifFichier').val('');
		$('#justificatifResourcehumaine').val('');
		$('#justificatifFournisseur').val('');
		$('#justificatifSuggestionEmploye').addClass('d-none').empty();

		// Pré-remplit mois/année du bulletin depuis la date de l'opération (JJ/MM/AAAA affichée).
		var parties = (dateOperation || '').split('/');
		$('#justificatifPayslipMois').val(parties.length === 3 ? parseInt(parties[1], 10) : (new Date()).getMonth() + 1);
		$('#justificatifPayslipAnnee').val(parties.length === 3 ? parties[2] : (new Date()).getFullYear());

		var employeSuggereRaw = $tr.attr('data-employe-suggere');
		var employeSuggere = null;
		try { employeSuggere = employeSuggereRaw ? JSON.parse(employeSuggereRaw) : null; } catch (e) { employeSuggere = null; }

		if (employeSuggere && employeSuggere.id) {
			$('#justificatifResourcehumaine').val(employeSuggere.id);
			$('#justificatifSuggestionEmploye').removeClass('d-none').html(
				'<i class="fa fa-lightbulb mr-1"></i>Ce libellé ressemble à un virement vers <strong>' + escHtml(employeSuggere.nom_complet) + '</strong> — bulletin de paie pré-rempli.'
			);
			$('input[name=justificatifMode][value=payslip]').prop('checked', true);
			justificatifAfficherMode('payslip');
			chargerBulletinsExistants(employeSuggere.id);
		} else {
			$('input[name=justificatifMode][value=charge]').prop('checked', true);
			justificatifAfficherMode('charge');
			chargerBulletinsExistants(null);
		}

		$('#justificatifManuelModal').modal('show');
	});

	$('#justificatifConfirmerBtn').on('click', function () {
		var $btn = $(this);
		var libelleBoutonInitial = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Enregistrement...');
		var mode = $('input[name=justificatifMode]:checked').val();
		var formData = new FormData();
		formData.append('id', justificatifLigneCourante);
		formData.append('mode', mode);
		formData.append('montant', $('#justificatifMontant').val());

		if (mode === 'payslip') {
			formData.append('id_resourcehumaine', $('#justificatifResourcehumaine').val());
			var idBulletinExistant = $('#justificatifPayslipExistant').val();
			if (idBulletinExistant) {
				formData.append('id_charge_bulletin_existant', idBulletinExistant);
			} else {
				formData.append('payslip_mois', $('#justificatifPayslipMois').val());
				formData.append('payslip_annee', $('#justificatifPayslipAnnee').val());
			}
		} else if (mode === 'fournisseur') {
			formData.append('id_fournisseur', $('#justificatifFournisseur').val());
			formData.append('titre', $('#justificatifTitreFournisseur').val());
		} else {
			formData.append('titre', $('#justificatifTitre').val());
		}

		var fichier = $('#justificatifFichier')[0].files[0];
		if (fichier) {
			formData.append('justificatif[]', fichier);
		}
		$.ajax({
			url: 'components/com_rapprochement/controleurs/router.php?task=creerJustificatifManuel',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				if (response.success) {
					window.location.reload();
				} else {
					alert(response.message || "Erreur lors de l'enregistrement");
					$btn.prop('disabled', false).html(libelleBoutonInitial);
				}
			},
			error: function () {
				alert("Erreur lors de l'enregistrement");
				$btn.prop('disabled', false).html(libelleBoutonInitial);
			}
		});
	});

	// Redesign checklist "Comptes bancaires" + entrée en cascade des cartes de lot au chargement
	// (clearProps après coup pour ne laisser aucun style inline traîner).
	if (typeof gsap !== 'undefined') {
		gsap.from('.rapprochement-compte-carte', {
			y: 14,
			opacity: 0,
			duration: 0.45,
			stagger: 0.08,
			ease: 'power2.out',
			clearProps: 'all'
		});
		gsap.from('.rapprochement-lot-carte', {
			y: 16,
			opacity: 0,
			duration: 0.4,
			stagger: 0.1,
			ease: 'power2.out',
			clearProps: 'all'
		});
	}
});
</script>
