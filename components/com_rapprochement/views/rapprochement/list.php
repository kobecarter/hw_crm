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

		<div class="row mb-4">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="far fa-file-excel"></i></span>
							<div class="dash-count">
								<div class="dash-title">Relevés bancaires importés</div>
								<div class="dash-counts"><p><?= sizeof($lotsData) ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
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
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
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
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
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
				<div class="card-header pl-0 mb-4" style="background-color: transparent;">
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
					// Replié même s'il vient d'être importé si l'utilisateur a répondu "Non" à la
					// question "faire le rapprochement maintenant ?" (collapse_dernier=1 posé par
					// le rechargement de afficherGatePeriode()) - dans ce cas on ne veut pas
					// dérouler automatiquement l'écran de résolution ligne par ligne.
					$expanded = ($index === 0) && !isset($_GET['collapse_dernier']);
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
										<tr data-id="<?= $l->getId() ?>" data-statut="<?= htmlspecialchars($l->getStatut()) ?>" data-libelle="<?= htmlspecialchars($l->getLibelle(), ENT_QUOTES, 'UTF-8') ?>" data-debit="<?= $l->getDebit() ?>" data-match-type="<?= isset($infos['type']) ? htmlspecialchars($infos['type']) : '' ?>" data-employe-suggere='<?= isset($infos['employe_suggere']) && $infos['employe_suggere'] ? htmlspecialchars(json_encode($infos['employe_suggere']), ENT_QUOTES, "UTF-8") : '' ?>' data-fournisseur-suggere='<?= isset($infos['fournisseur_suggere']) && $infos['fournisseur_suggere'] ? htmlspecialchars(json_encode($infos['fournisseur_suggere']), ENT_QUOTES, "UTF-8") : '' ?>'>
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
															<?php
															$idsCandidats = array_column($infos['candidats'], 'id_facture');
															// Mise en avant : parmi TOUTES les factures (payées comprises, cf. objectif "lier un
															// paiement à une facture"), celles dont le montant TOTAL correspond exactement au
															// crédit de cette ligne sortent dans un groupe séparé en tête de liste - même une
															// facture déjà soldée reste un candidat plausible (double règlement, etc.).
															$montantCredit = round((float) $l->getCredit(), 2);
															$facturesMontantCorrespondant = array();
															foreach ($facturesOuvertes as $fCandidatMontant) {
																if (in_array($fCandidatMontant->getId(), $idsCandidats)) {
																	continue;
																}
																if (abs(round((float) $fCandidatMontant->getTotal(), 2) - $montantCredit) < 0.01) {
																	$facturesMontantCorrespondant[] = $fCandidatMontant;
																}
															}
															$idsMontantCorrespondant = array_map(function ($fMap) { return $fMap->getId(); }, $facturesMontantCorrespondant);
															?>
															<!-- Simple porteur de valeur - la sélection se fait dans la fenêtre #affecterModal (même
															     habillage que les autres fenêtres du module), jamais dans ce <select> caché. -->
															<select class="rapprochement-facture-select d-none">
																<option value="">Choisir la facture...</option>
																<?php if (!empty($infos['candidats'])) :?>
																<optgroup label="Candidat(s) détecté(s) (même montant)">
																	<?php foreach ($infos['candidats'] as $c) :?>
																	<option value="<?= $c['id_facture'] ?>" data-client="<?= isset($c['id_client']) && $c['id_client'] ? $c['id_client'] : '' ?>">N°<?= htmlspecialchars($c['numero']) ?> — <?= htmlspecialchars($c['client']) ?> (<?= number_format($c['montant'], 2, ',', ' ') ?> DH)</option>
																	<?php endforeach;?>
																</optgroup>
																<?php endif;?>
																<?php if (!empty($facturesMontantCorrespondant)) :?>
																<optgroup label="⭐ Montant correspondant (<?= number_format($montantCredit, 2, ',', ' ') ?> DH)" data-groupe="montant-correspondant">
																	<?php foreach ($facturesMontantCorrespondant as $f) :?>
																		<?php $client = $f->getClient(); $nomClient = $client ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : '';?>
																	<option value="<?= $f->getId() ?>" data-client="<?= $client ? $client->getId() : '' ?>">⭐ N°<?= htmlspecialchars($f->getNumero()) ?> — <?= htmlspecialchars($nomClient) ?> (Total : <?= number_format($f->getTotal(), 2, ',', ' ') ?> DH<?= $f->getReste() <= 0 ? ', payée' : '' ?>)</option>
																	<?php endforeach;?>
																</optgroup>
																<?php endif;?>
																<optgroup label="Toutes les factures (payées comprises)">
																	<?php foreach ($facturesOuvertes as $f) :?>
																		<?php if (in_array($f->getId(), $idsCandidats) || in_array($f->getId(), $idsMontantCorrespondant)) { continue; }?>
																		<?php $client = $f->getClient(); $nomClient = $client ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : '';?>
																		<?php $resteAffiche = $f->getReste() > 0 ? 'reste ' . number_format($f->getReste(), 2, ',', ' ') . ' DH' : 'payée';?>
																	<option value="<?= $f->getId() ?>" data-client="<?= $client ? $client->getId() : '' ?>">N°<?= htmlspecialchars($f->getNumero()) ?> — <?= htmlspecialchars($nomClient) ?> (Total : <?= number_format($f->getTotal(), 2, ',', ' ') ?> DH, <?= $resteAffiche ?>)</option>
																	<?php endforeach;?>
																</optgroup>
															</select>
															<!-- Porteur de valeur pour "Associer ce règlement" (panneau "Règlements déjà enregistrés"
															     de la fenêtre #affecterModal) - alternative à .rapprochement-facture-select : lie ce
															     crédit à un règlement DÉJÀ existant du client au lieu d'en créer un nouveau. -->
															<input type="hidden" class="rapprochement-payment-existant d-none" value="">
															<button type="button" class="btn btn-white btn-sm rapprochement-choisir" data-toggle="tooltip" title="Choisir la facture" data-affecter-type="facture" data-candidats-facture="<?= count($infos['candidats']) ?>"><i class="fa fa-search mr-1"></i>Choisir</button>
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
																<?php if (!empty($chargesDejaAffectees)) :?>
																<optgroup label="⚠ Charges déjà affectées (réaffectation possible)">
																	<?php foreach ($chargesDejaAffectees as $c) :?>
																	<option value="<?= $c->getId() ?>">⚠ <?= htmlspecialchars($c->getTitre()) ?> — <?= $c->getDateCharge() ? date('d/m/Y', strtotime($c->getDateCharge())) : '' ?> (<?= number_format($c->getTotal(), 2, ',', ' ') ?> <?= htmlspecialchars($c->getDevise()) ?>)</option>
																	<?php endforeach;?>
																</optgroup>
																<?php endif;?>
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
																<?php if (!empty($chargesDejaAffectees)) :?>
																<optgroup label="⚠ Charges déjà affectées (réaffectation possible)">
																	<?php foreach ($chargesDejaAffectees as $c) :?>
																	<option value="<?= $c->getId() ?>">⚠ <?= htmlspecialchars($c->getTitre()) ?> — <?= $c->getDateCharge() ? date('d/m/Y', strtotime($c->getDateCharge())) : '' ?> (<?= number_format($c->getTotal(), 2, ',', ' ') ?> <?= htmlspecialchars($c->getDevise()) ?>)</option>
																	<?php endforeach;?>
																</optgroup>
																<?php endif;?>
															</select>
															<button type="button" class="btn btn-white btn-sm rapprochement-choisir" data-toggle="tooltip" title="Choisir une charge déjà existante" data-affecter-type="charge"><i class="fa fa-search mr-1"></i>Choisir</button>
														<?php endif;?>
														<button type="button" class="btn btn-success btn-sm rapprochement-valider" data-toggle="tooltip" title="Valider"><i class="fa fa-check"></i></button>
														<button type="button" class="btn btn-white btn-sm rapprochement-ignorer" data-toggle="tooltip" title="Ignorer"><i class="fa fa-times"></i></button>
													<?php endif;?>
												<?php elseif ($l->getStatut() === 'sans_justificatif') :?>
													<button type="button" class="btn btn-danger btn-sm rapprochement-justificatif-manuel" data-toggle="tooltip" title="Insérer le justificatif"><i class="fa fa-paperclip"></i></button>
													<button type="button" class="btn btn-white btn-sm rapprochement-ignorer" data-toggle="tooltip" title="Ignorer"><i class="fa fa-times"></i></button>
												<?php elseif ($l->getStatut() === 'matched_facture') :?>
													<!-- Retour à "à valider" (voir annulerRapprochementFacture() côté contrôleur) - le
													     règlement n'est supprimé que s'il a été CRÉÉ par ce rapprochement, jamais s'il
													     s'agissait d'un règlement du client déjà existant simplement associé. -->
													<button type="button" class="btn btn-white btn-sm rapprochement-annuler-facture" data-toggle="tooltip" title="Annuler ce rapprochement"><i class="fa fa-undo text-danger"></i></button>
												<?php elseif ($l->getStatut() === 'matched_charge' && $l->getIdCharge()) :?>
													<!-- La charge (créée ou liée depuis ce relevé) reste modifiable sans quitter la page -
													     titre/montant/type/remarque erronés ou à compléter après coup, sans repasser par la
													     liste complète des charges pour la retrouver. -->
													<a href="index.php?option=com_charge&task=edit&id=<?= $l->getIdCharge() ?>" target="_blank" class="btn btn-white btn-sm" data-toggle="tooltip" title="Modifier la charge affectée"><i class="fa fa-edit"></i></a>
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
				<div class="form-group">
					<label>Justificatif (optionnel — sinon le relevé bancaire du lot est utilisé)</label>
					<input type="file" class="form-control" id="justificatifFichier" accept=".jpg,.jpeg,.png,.gif,.pdf">
				</div>
				<!-- Commune aux 3 modes (charge simple, bulletin de paie, fournisseur) - pas de zone
				     dédiée à un mode en particulier. Exportée telle quelle dans le dossier comptable
				     Excel (onglet "Tous les achats-charges", cf. exportTvaComptable()). -->
				<div class="form-group mb-0">
					<label>Remarque (optionnel)</label>
					<textarea class="form-control" id="justificatifRemarque" rows="2"></textarea>
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
     détectés automatiquement ET l'ensemble des factures (payées comprises) / charges disponibles de
     l'agence, pour ne jamais bloquer l'utilisateur au seul auto-matching. -->
<div id="affecterModal" class="modal custom-modal fade" role="dialog" data-client-infos='<?= htmlspecialchars(json_encode(array("factures" => $facturesParClient, "reglements" => $reglementsParClient)), ENT_QUOTES, "UTF-8") ?>'>
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title" id="affecterModalTitre">Choisir la facture correspondante</h5>
			</div>
			<div class="modal-body">
				<!-- Ne s'affiche que pour une facture quand AUCUN client n'a été détecté automatiquement
				     dans le libellé du crédit (0 candidat) : plutôt que de noyer l'utilisateur dans la
				     liste complète de toutes les factures de l'agence, on cherche d'abord le
				     client par nom, puis seules SES factures (payées comprises) alimentent le select ci-dessous. -->
				<div id="affecterClientZone" class="form-group d-none">
					<label>Rechercher le client</label>
					<select id="affecterClientSelect" class="form-control" style="width:100%;">
						<option value="">Tapez le nom du client...</option>
						<?php foreach ($clientsPourRapprochement as $cli) :?>
							<?php $nomCli = trim((string) $cli->getRaisonSocial()) !== '' ? $cli->getRaisonSocial() : trim($cli->getPrenom() . ' ' . $cli->getNom());?>
						<option value="<?= $cli->getId() ?>"><?= htmlspecialchars($nomCli) ?></option>
						<?php endforeach;?>
					</select>
					<p class="text-muted mb-0 mt-2" id="affecterClientVide" style="font-size:0.8rem;">Aucun client détecté automatiquement pour ce libellé — cherchez-le ci-dessus pour afficher toutes ses factures.</p>
				</div>

				<div class="form-group mb-0" id="affecterSelectZone">
					<select id="affecterSelect" class="form-control" style="width:100%;"></select>
				</div>

				<!-- Vue d'ensemble du client de la facture actuellement sélectionnée : ses AUTRES
				     factures et ses règlements déjà enregistrés - jamais une affectation à l'aveugle,
				     l'admin voit tout l'historique avant de confirmer. Alimentée en JS depuis
				     data-client-infos (posé sur #affecterModal, chargé une seule fois avec la page),
				     mise à jour à chaque changement de facture sélectionnée ou de client recherché. -->
				<div id="affecterClientInfos" class="d-none mt-3 pt-3" style="border-top:1px dashed #e2e8f0;">
					<div class="row">
						<div class="col-6">
							<label class="text-muted mb-1" style="font-size:0.75rem;"><i class="fa fa-file-invoice mr-1"></i>Factures du client</label>
							<div class="list-group" style="max-height:180px; overflow-y:auto;" id="affecterClientFactures"></div>
						</div>
						<div class="col-6">
							<label class="text-muted mb-1" style="font-size:0.75rem;"><i class="fa fa-money-check-alt mr-1"></i>Règlements déjà enregistrés</label>
							<div class="list-group" style="max-height:180px; overflow-y:auto;" id="affecterClientReglements"></div>
						</div>
					</div>
				</div>

				<!-- Filet de secours (facture uniquement) : le client recherché n'a AUCUNE facture -
				     plutôt que de bloquer l'utilisateur, un petit encart lui propose d'aller créer/lier
				     le règlement directement depuis la page Facturation (nouvel onglet, la fenêtre de
				     rapprochement reste ouverte ici) - à rafraîchir ensuite pour le retrouver dans le
				     panneau "Règlements déjà enregistrés" et cliquer "Associer". -->
				<div id="affecterAucuneFactureZone" class="d-none mt-3">
					<div class="d-flex align-items-center" style="gap:0.75rem; background:rgba(99, 102, 241, 0.06); border-radius:12px; padding:0.7rem 0.9rem;">
						<div style="width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg, #6366f1, #4f46e5); display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 10px rgba(79, 70, 229, 0.3);">
							<i class="fa fa-lightbulb" style="color:#fff; font-size:0.85rem;"></i>
						</div>
						<p class="mb-0" style="flex:1; min-width:0; font-size:0.82rem; color:var(--ink, #181a2a);">Introuvable ? Ajoutez le règlement directement depuis la facturation.</p>
						<a href="index.php?option=com_facture" target="_blank" class="btn btn-sm btn-primary" style="border-radius:999px; white-space:nowrap; flex-shrink:0;"><i class="fa fa-plus mr-1"></i>Nouveau règlement</a>
					</div>
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

<!-- Popup "Confirmer le rapprochement" — dernier verrou avant d'écrire en base (créer un nouveau
     règlement, ou lier un règlement déjà existant du client) : jamais un clic direct sans relecture,
     même habillage que les autres popups de confirmation du module (.tva-confirm-modal). Le texte
     du corps est rempli dynamiquement (demanderConfirmationRapprochement()) selon le crédit/la
     facture/le règlement concernés - le popup lui-même reste générique. -->
<div id="confirmerRapprochementModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="tva-confirm-icon" style="background:linear-gradient(135deg, #34d399, #059669); box-shadow:0 8px 20px rgba(5, 150, 105, 0.35);"><i class="fa fa-link"></i></div>
				<h5 class="modal-title mt-3">Confirmer le rapprochement ?</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-0" id="confirmerRapprochementTexte" style="font-size:0.9rem;">—</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-success" id="confirmerRapprochementBtn"><i class="fa fa-check mr-1"></i> Confirmer</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup "Annuler ce rapprochement" — retour d'une ligne "Facture rapprochée" à "à valider" (voir
     annulerRapprochementFacture()) : action destructrice potentielle (peut supprimer un règlement),
     jamais un clic direct sans confirmation, même habillage que les autres popups d'avertissement. -->
<div id="annulerRapprochementModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="tva-confirm-icon" style="background:linear-gradient(135deg, #f87171, #b91c1c); box-shadow:0 8px 20px rgba(185, 28, 28, 0.35);"><i class="fa fa-undo"></i></div>
				<h5 class="modal-title mt-3">Annuler ce rapprochement ?</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-0" id="annulerRapprochementTexte" style="font-size:0.9rem;">—</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
				<button type="button" class="btn btn-danger" id="annulerRapprochementBtn"><i class="fa fa-undo mr-1"></i> Annuler le rapprochement</button>
			</div>
		</div>
	</div>
</div>

<!-- Popup "Réaffectation" (sécurité anti-doublon) — la charge (ou le bulletin de paie) choisi est
     déjà rattaché à une AUTRE ligne de relevé bancaire : bloque la validation immédiate et montre
     l'ancienne affectation avant de laisser l'utilisateur écraser le lien ou annuler. Même
     habillage que les autres popups d'avertissement (.tva-confirm-modal + .charge-doublon-icon,
     déjà utilisée par la dropzone Charges pour "Doublon détecté"). -->
<div id="reaffectationModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="charge-doublon-icon"><i class="fa fa-exclamation-triangle"></i></div>
				<h5 class="modal-title mt-3">Cette charge est déjà affectée</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-3" style="font-size:0.9rem;">
					Êtes-vous sûr de vouloir la réaffecter ? L'ancienne liaison ci-dessous sera écrasée.
				</p>
				<div id="reaffectationAncienneInfo" class="rapprochement-suppression-resume"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Ignorer</button>
				<button type="button" class="btn btn-danger" id="reaffectationConfirmerBtn"><i class="fa fa-exchange-alt mr-1"></i> Valider la réaffectation</button>
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

<!-- Question posée juste après confirmation d'un import : les relevés de la période TVA (mois ou
     trimestre selon l'agence) sont regroupés ici, avant de proposer de lancer la résolution ligne
     par ligne - voir afficherGatePeriode() dans le script ci-dessous. Contenu injecté en JS. -->
<div id="dialog-gate-periode" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content"></div>
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

	// Dernier verrou avant d'écrire en base pour un rapprochement crédit -> facture (nouveau
	// règlement créé OU règlement existant associé) - jamais un clic direct sans relecture. Le
	// popup lui-même (#confirmerRapprochementModal) est générique, seul le texte change.
	var rapprochementConfirmerCallback = null;
	function demanderConfirmationRapprochement(messageHtml, callback) {
		$('#confirmerRapprochementTexte').html(messageHtml);
		rapprochementConfirmerCallback = callback;
		$('#confirmerRapprochementModal').modal('show');
	}
	$('#confirmerRapprochementBtn').on('click', function () {
		$('#confirmerRapprochementModal').modal('hide');
		if (rapprochementConfirmerCallback) {
			var callback = rapprochementConfirmerCallback;
			rapprochementConfirmerCallback = null;
			callback();
		}
	});

	// Même principe pour "Annuler ce rapprochement" (#annulerRapprochementModal) - action
	// destructrice potentielle (peut supprimer un règlement), jamais un clic direct.
	var rapprochementAnnulerCallback = null;
	function demanderAnnulationRapprochement(messageHtml, callback) {
		$('#annulerRapprochementTexte').html(messageHtml);
		rapprochementAnnulerCallback = callback;
		$('#annulerRapprochementModal').modal('show');
	}
	$('#annulerRapprochementBtn').on('click', function () {
		$('#annulerRapprochementModal').modal('hide');
		if (rapprochementAnnulerCallback) {
			var callback = rapprochementAnnulerCallback;
			rapprochementAnnulerCallback = null;
			callback();
		}
	});

	// ---- Sécurité anti-doublon (réaffectation) : la charge/le bulletin choisi est déjà rattaché
	// à une AUTRE ligne de relevé - le serveur bloque et renvoie needs_confirmation=1 au lieu
	// d'agir. Cette popup montre l'ancienne affectation puis rejoue l'appel avec force=1 si
	// l'utilisateur confirme vouloir écraser le lien existant. Retourne true si la réponse a été
	// interceptée (l'appelant ne doit alors PAS afficher son propre message d'erreur générique).
	var reaffectationRenvoyer = null;
	function gererBesoinConfirmation(response, renvoyerAvecForce) {
		if (!response || !response.needs_confirmation) {
			return false;
		}
		var a = response.ancienne_affectation || {};
		var html = '<ul class="mb-0">';
		html += '<li><strong>Compte :</strong> ' + escHtml(a.compte || '—') + '</li>';
		html += '<li><strong>Date :</strong> ' + escHtml(a.date_operation || '—') + '</li>';
		html += '<li><strong>Libellé :</strong> ' + escHtml(a.libelle || '—') + '</li>';
		html += '<li><strong>Montant :</strong> ' + fmtMontant(a.montant) + ' DH</li>';
		html += '</ul>';
		$('#reaffectationAncienneInfo').html(html);
		reaffectationRenvoyer = renvoyerAvecForce;
		$('#reaffectationModal').modal('show');
		return true;
	}
	$('#reaffectationConfirmerBtn').on('click', function () {
		$('#reaffectationModal').modal('hide');
		if (reaffectationRenvoyer) {
			var callback = reaffectationRenvoyer;
			reaffectationRenvoyer = null;
			callback();
		}
	});

	// Filtres rapides (Tous / À valider / Sans justificatif / À jour) : filtre les LIGNES à
	// l'intérieur de chaque tableau de lot (pas seulement les cartes) - un lot ne reste visible
	// que s'il lui reste au moins une ligne correspondant au filtre choisi. "À jour" affiche les
	// lignes déjà traitées (ni à valider, ni sans justificatif).
	$(document).on('click', '.quick-filter-chips button', function () {
		var filtre = $(this).data('filter');
		$('.quick-filter-chips button').removeClass('active');
		$(this).addClass('active');
		$('.rapprochement-lot-carte').each(function () {
			var $carte = $(this);
			var nbLignesVisibles = 0;
			$carte.find('tbody tr').each(function () {
				var $ligne = $(this);
				var statut = $ligne.attr('data-statut');
				var visible = true;
				if (filtre === 'a_valider') { visible = statut === 'a_valider'; }
				else if (filtre === 'sans_justificatif') { visible = statut === 'sans_justificatif'; }
				else if (filtre === 'a_jour') { visible = statut !== 'a_valider' && statut !== 'sans_justificatif'; }
				$ligne.toggle(visible);
				if (visible) { nbLignesVisibles++; }
			});
			$carte.toggle(filtre === 'all' || nbLignesVisibles > 0);
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

	// Conservé pour permettre de relancer l'analyse avec un compte choisi manuellement sans
	// redemander le fichier à l'utilisateur (afficherChoixCompteManuel() ci-dessous).
	var dernierFichierImporte = null;

	function importerFichier(fichier, idBankForce) {
		dernierFichierImporte = fichier;
		$('#rapprochementMsgBox').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin mr-2"></i>Analyse du relevé en cours (détection du compte, lecture des lignes)...</div>');

		var formData = new FormData();
		formData.append('document[]', fichier);
		if (idBankForce) {
			formData.append('id_bank_force', idBankForce);
		}

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
				} else if (response.bank_non_detecte) {
					afficherChoixCompteManuel(response);
				} else {
					$('#rapprochementMsgBox').html('<div class="alert alert-danger">' + escHtml(response.message || "Erreur lors de l'analyse") + '</div>');
				}
			},
			error: function () {
				$('#rapprochementMsgBox').html('<div class="alert alert-danger">Erreur lors de l\'analyse.</div>');
			}
		});
	}

	// ---- Compte bancaire non reconnu automatiquement (RIB/IBAN non lus, format inhabituel...) :
	// plutôt que de bloquer l'import, on propose de choisir le compte manuellement dans la liste
	// des comptes de l'agence, puis on relance l'analyse avec ce choix (id_bank_force) sur le
	// MÊME fichier déjà en mémoire côté navigateur - pas besoin de le redéposer. -----------------
	function afficherChoixCompteManuel(r) {
		var comptes = r.comptes_disponibles || [];
		var options = comptes.map(function (c) {
			return '<option value="' + c.id + '">' + escHtml(c.nom) + '</option>';
		}).join('');

		var html = '<div class="alert alert-warning">'
			+ '<div class="d-flex align-items-center flex-wrap">'
			+ '<div class="mr-3 mb-2"><i class="fa fa-university mr-2"></i>Compte bancaire non reconnu automatiquement dans ce document. Choisissez-le manuellement :</div>'
			+ '</div>'
			+ '<div class="d-flex align-items-center flex-wrap mt-2">'
			+ '<select id="compteManuelSelect" class="form-control mr-2 mb-2" style="max-width:320px;"><option value="">Sélectionner un compte…</option>' + options + '</select>'
			+ '<button type="button" class="btn btn-primary mb-2" id="compteManuelConfirmerBtn"><i class="fa fa-check mr-1"></i> Continuer avec ce compte</button>'
			+ '</div>'
			+ '</div>';
		$('#rapprochementMsgBox').html(html);
	}

	$(document).on('click', '#compteManuelConfirmerBtn', function () {
		var idChoisi = $('#compteManuelSelect').val();
		if (!idChoisi) {
			alert('Veuillez sélectionner un compte.');
			return;
		}
		if (!dernierFichierImporte) {
			$('#rapprochementMsgBox').html('<div class="alert alert-danger">Fichier perdu, veuillez le redéposer.</div>');
			return;
		}
		importerFichier(dernierFichierImporte, idChoisi);
	});

	// ---- Aperçu avant validation ------------------------------------------------------------
	var apercuCourant = null;

	function badgeStatutLigne(l) {
		var infos = l.donnees_matching || {};
		if (l.statut === 'sans_justificatif') {
			return '<span class="badge bg-danger-light"><i class="fa fa-exclamation-triangle mr-1"></i>Sans justificatif</span>';
		}
		if (infos.type === 'ligne_dupliquee') {
			return '<span class="badge badge-light text-muted" data-toggle="tooltip" data-title="' + (infos.date_import_existant ? 'Déjà importée le ' + formatDateFr(infos.date_import_existant) : '') + '"><i class="fa fa-copy mr-1"></i>Déjà importée</span>';
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
		var bloque = r.compteurs && r.compteurs.doublon > 0;

		html += '<div class="alert ' + (bloque ? 'alert-warning' : 'alert-success') + ' mb-3">Compte détecté : <strong>' + escHtml(r.banque) + '</strong>'
			+ (r.periode_libelle ? ' — période <strong>' + escHtml(r.periode_libelle) + '</strong>' : '')
			+ (r.agence_basculee ? '<br><i class="fa fa-exchange-alt mr-1"></i>Ce compte appartient à <strong>' + escHtml(r.nouvelle_agence) + '</strong> — la session basculera sur cette agence à la validation.' : '')
			+ '</div>';

		// Doublon détecté (partiel ou total) : on bloque simplement la validation plutôt que de
		// proposer un choix garder/écraser - l'utilisateur doit supprimer l'ancien relevé (bouton
		// 🗑 sur sa carte dans "Relevés importés" ci-dessous) avant de pouvoir réimporter celui-ci.
		if (bloque) {
			var lotExistant = r.lot_existant_info || null;
			html += '<div class="alert alert-danger mb-3">'
				+ '<i class="fa fa-ban mr-2"></i><strong>' + (r.releve_entierement_deja_importe ? 'Ce relevé bancaire a déjà été importé' : (r.compteurs.doublon + ' ligne(s) de ce relevé ont déjà été importées')) + '</strong>'
				+ (lotExistant && lotExistant.date_debut ? ' (période du ' + formatDateFr(lotExistant.date_debut) + ' au ' + formatDateFr(lotExistant.date_fin) + ', importé le ' + formatDateFr(lotExistant.date_add) + ')' : '')
				+ '.<br>Supprimez l\'ancien relevé dans la liste "Relevés importés" ci-dessous (icône <i class="fa fa-trash"></i>), puis réimportez ce fichier.'
				+ '</div>';
		}

		html += '<div class="table-responsive mb-3"><table class="table table-sm table-striped mb-0"><thead><tr><th>Date</th><th>Libellé</th><th>Débit</th><th>Crédit</th><th>Statut proposé</th></tr></thead><tbody>';
		(r.lignes || []).forEach(function (l) {
			html += '<tr><td>' + formatDateFr(l.date_operation) + '</td><td>' + escHtml(l.libelle) + '</td>'
				+ '<td>' + (l.debit ? fmtMontant(l.debit) + ' DH' : '') + '</td>'
				+ '<td>' + (l.credit ? fmtMontant(l.credit) + ' DH' : '') + '</td>'
				+ '<td>' + badgeStatutLigne(l) + '</td></tr>';
		});
		html += '</tbody></table></div>';

		if (r.commissions && !bloque) {
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
			+ '<button type="button" class="btn btn-primary" id="apercuValiderBtn"' + (bloque ? ' disabled title="Supprimez l\'ancien relevé avant de continuer"' : '') + '><i class="fa fa-check mr-1"></i> Valider la lecture</button>'
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

	// Simple : si l'aperçu a détecté des lignes déjà importées, le bouton est désactivé (voir
	// afficherApercu()) et ce clic ne peut normalement jamais arriver dans ce cas - garde
	// défensive quand même, plutôt que de compter uniquement sur l'attribut disabled.
	$(document).on('click', '#apercuValiderBtn', function () {
		if (!apercuCourant) { return; }
		if (apercuCourant.compteurs && apercuCourant.compteurs.doublon > 0) { return; }
		posterConfirmerReleve();
	});

	function posterConfirmerReleve() {
		if (!apercuCourant) { return; }
		var $btn = $('#apercuValiderBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Validation...');
		$.post('components/com_rapprochement/controleurs/router.php?task=confirmerReleve', { payload: JSON.stringify(apercuCourant) }, function (response) {
			if (response.success) {
				afficherGatePeriode(response);
			} else {
				alert(response.message || 'Erreur lors de la validation');
				$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Valider la lecture');
			}
		});
	}

	// ---- Question posée après insertion : le/les relevé(s) de la période sont réunis, veut-on
	// faire le rapprochement bancaire maintenant ou plus tard ? L'insertion elle-même a déjà eu
	// lieu (confirmerReleve() a écrit le lot et ses lignes juste avant) - cette étape ne fait que
	// décider comment recharger la page : lot déplié prêt à résoudre (Oui), ou replié (Non). -----
	function afficherGatePeriode(r) {
		var groupe = r.groupe_periode || [];
		var html = '<div class="modal-header">'
			+ '<h5 class="modal-title"><i class="fa fa-layer-group mr-2"></i>Relevé inséré' + (r.periode_libelle ? ' — période ' + escHtml(r.periode_libelle) : '') + '</h5>'
			+ '</div>';
		html += '<div class="modal-body">';

		if (groupe.length > 1) {
			html += '<p>Cette période regroupe déjà <strong>' + groupe.length + ' relevé(s)</strong> importé(s) pour ce compte :</p>';
			html += '<ul class="rapprochement-gate-liste mb-3">';
			groupe.forEach(function (g) {
				html += '<li>'
					+ (g.date_debut ? formatDateFr(g.date_debut) + ' → ' + formatDateFr(g.date_fin) : escHtml(g.fichier_source || ''))
					+ (g.est_nouveau ? ' <span class="badge bg-primary-light ml-1">celui-ci</span>' : '')
					+ (g.a_valider > 0 ? ' <span class="badge bg-warning-light ml-1">' + g.a_valider + ' à valider</span>' : ' <span class="badge bg-success-light ml-1">à jour</span>')
					+ '</li>';
			});
			html += '</ul>';
		} else {
			html += '<p>1 relevé importé pour cette période.</p>';
		}

		html += '<p class="mb-0">Voulez-vous faire le rapprochement bancaire maintenant ?</p>';
		html += '</div>';
		html += '<div class="modal-footer">'
			+ '<button type="button" class="btn btn-white" id="gatePeriodeNonBtn">Non, plus tard</button>'
			+ '<button type="button" class="btn btn-primary" id="gatePeriodeOuiBtn"><i class="fa fa-check mr-1"></i> Oui, faire le rapprochement</button>'
			+ '</div>';

		$('#dialog-gate-periode .modal-content').html(html);
		$('#dialog-gate-periode').modal({ backdrop: 'static', keyboard: false }).modal('show');
	}

	$(document).on('click', '#gatePeriodeOuiBtn', function () {
		window.location.href = 'index.php?option=com_rapprochement';
	});
	$(document).on('click', '#gatePeriodeNonBtn', function () {
		window.location.href = 'index.php?option=com_rapprochement&collapse_dernier=1';
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
	var affecterFactureHtmlComplet = '';
	// Toutes les factures + tous les règlements de l'agence, regroupés par id_client (posé une
	// seule fois par index.php dans data-client-infos, voir #affecterModal) - jamais un nouvel
	// aller-retour AJAX pour afficher l'historique du client au survol/à la sélection.
	var affecterClientInfos = (function () {
		var brut = $('#affecterModal').attr('data-client-infos');
		try { return brut ? JSON.parse(brut) : { factures: {}, reglements: {} }; } catch (e) { return { factures: {}, reglements: {} }; }
	})();

	// Rafraîchit le panneau "Factures du client / Règlements déjà enregistrés" du #affecterModal
	// pour le client actuellement identifié (facture sélectionnée dans #affecterSelect OU client
	// choisi manuellement dans #affecterClientSelect) - jamais une affectation à l'aveugle.
	function affecterAfficherInfosClient(idClient) {
		var $zone = $('#affecterClientInfos');
		var $factures = $('#affecterClientFactures');
		var $reglements = $('#affecterClientReglements');

		// Filet de secours vers la facturation : reste visible tant qu'un client est identifié, MÊME
		// s'il a des factures/règlements affichés - rien ne garantit que l'un d'eux est le bon pour
		// CE crédit précis (montant différent, etc.), c'est à l'utilisateur d'en juger, pas au code.
		$('#affecterAucuneFactureZone').toggleClass('d-none', !idClient);

		var factures = idClient ? (affecterClientInfos.factures[idClient] || []) : [];
		var reglements = idClient ? (affecterClientInfos.reglements[idClient] || []) : [];

		if (!idClient || (factures.length === 0 && reglements.length === 0)) {
			$zone.addClass('d-none');
			return;
		}

		if (factures.length === 0) {
			$factures.html('<p class="text-muted mb-0 px-2 py-1" style="font-size:0.8rem;">Aucune facture.</p>');
		} else {
			var htmlF = '';
			factures.forEach(function (f) {
				var estPayee = f.reste <= 0;
				htmlF += '<div class="list-group-item d-flex justify-content-between align-items-center py-1 px-2" style="font-size:0.78rem;">'
					+ '<span>N°' + f.numero + ' <small class="text-muted">' + formatDateFr(f.date) + '</small></span>'
					+ '<span class="badge ' + (estPayee ? 'badge-success' : 'badge-warning') + '">' + fmtMontant(f.total) + ' ' + f.devise + (estPayee ? '' : ' · reste ' + fmtMontant(f.reste) + ' ' + f.devise) + '</span>'
					+ '</div>';
			});
			$factures.html(htmlF);
		}

		if (reglements.length === 0) {
			$reglements.html('<p class="text-muted mb-0 px-2 py-1" style="font-size:0.8rem;">Aucun règlement enregistré.</p>');
		} else {
			var htmlR = '';
			reglements.forEach(function (r) {
				htmlR += '<div class="list-group-item d-flex justify-content-between align-items-center py-1 px-2" style="font-size:0.78rem;">'
					+ '<span>' + (r.deja_lie ? '⚠ ' : '') + 'N°' + r.facture_numero + ' <small class="text-muted">' + formatDateFr(r.date) + (r.methode ? ' · ' + r.methode : '') + '</small><br><span class="badge badge-light">' + fmtMontant(r.montant) + ' ' + r.devise + '</span></span>'
					+ '<button type="button" class="btn btn-white btn-xs rapprochement-associer-reglement" data-toggle="tooltip" title="' + (r.deja_lie ? 'Déjà rapproché avec une autre opération - cliquer pour réaffecter' : 'Associer ce crédit à ce règlement plutôt que de créer un nouveau paiement') + '" data-payment-id="' + r.id + '">Associer</button>'
					+ '</div>';
			});
			$reglements.html(htmlR);
		}

		$zone.removeClass('d-none');
	}

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
		if ($('#affecterClientSelect').hasClass('select2-hidden-accessible')) {
			$('#affecterClientSelect').select2('destroy');
		}
		var html = $selectSource.html();
		// Rien de la liste ne correspond forcément à un débit fourre-tout (ex: virement à un
		// employé pour tout autre chose que la charge suggérée par montant/date) - jamais bloquant.
		if (type === 'charge') {
			html = '<option value="__nouvelle__">+ Créer une nouvelle charge</option>' + html;
		}
		affecterFactureHtmlComplet = html;
		$('#affecterAucuneFactureZone').addClass('d-none');

		// Aucun client détecté automatiquement dans le libellé de ce crédit (0 candidat) :
		// recherche du client d'abord (fenêtre dédiée), la liste des factures ne s'affiche qu'une
		// fois le client choisi, plutôt que de noyer l'utilisateur dans toutes les factures
		// ouvertes de l'agence dès l'ouverture.
		var candidatsFacture = type === 'facture'
			? (parseInt($tr.find('.rapprochement-choisir[data-affecter-type="facture"]').data('candidats-facture'), 10) || 0)
			: 0;
		var rechercheClientActive = type === 'facture' && candidatsFacture === 0;

		$('#affecterClientZone').toggleClass('d-none', !rechercheClientActive);
		if (rechercheClientActive) {
			$('#affecterClientSelect').val(null);
			$affecterSelect.html('<option value="">Choisir la facture...</option>');
			$('#affecterSelectZone').addClass('d-none');
			$('#affecterClientInfos').addClass('d-none');
		} else {
			$affecterSelect.html(html).val($selectSource.val());
			$('#affecterSelectZone').removeClass('d-none');
			// Par défaut, le panneau client montre le MEILLEUR candidat détecté (premier de son
			// groupe) - l'admin voit tout de suite l'historique du client le plus probable, et le
			// panneau se met à jour dès qu'il choisit une autre facture dans la liste.
			if (type === 'facture') {
				var idClientDefaut = $('<div>').html(html).find('optgroup[label^="Candidat"] option[data-client]').first().data('client');
				affecterAfficherInfosClient(idClientDefaut);
			} else {
				$('#affecterClientInfos').addClass('d-none');
			}
		}
		affecterBasculerZoneNouvelleCharge(false);

		if (type === 'charge') {
			$('#affecterNouvelleChargeTitre').val($tr.data('libelle'));
			$('#affecterNouvelleChargeMontant').val($tr.data('debit'));
			$('#affecterNouvelleChargeType').val('variable');
		}

		$('#affecterModal').modal('show');
		// select2 doit s'initialiser une fois la modale visible (sinon calcul de largeur à 0px,
		// piège Select2 classique sur un élément encore display:none) - d'où l'appel APRÈS
		// modal('show') aussi bien ici que dans affecterAppliquerFiltreClient() ci-dessous.
		if (rechercheClientActive) {
			affecterAppliquerFiltreClient('');
			$('#affecterClientSelect').select2({ dropdownParent: $('#affecterModal'), width: '100%', placeholder: 'Tapez le nom du client...' });
		} else {
			$affecterSelect.select2({ dropdownParent: $('#affecterModal'), width: '100%' });
		}
	}

	// Filtre la liste des factures par client choisi (attribut data-client déjà posé sur chaque
	// <option>, aucun nouvel aller-retour AJAX - la liste complète est déjà chargée dans
	// affecterFactureHtmlComplet). Sans client choisi (idClient vide), seul le raccourci "⭐ Montant
	// correspondant" reste visible (toutes agences confondues) - un moyen rapide de retrouver la
	// bonne facture sans même avoir à chercher le client, si son montant total suffit à l'identifier.
	function affecterAppliquerFiltreClient(idClient) {
		var $affecterSelect = $('#affecterSelect');
		if ($affecterSelect.hasClass('select2-hidden-accessible')) {
			$affecterSelect.select2('destroy');
		}
		var $toutes = $('<div>').html(affecterFactureHtmlComplet);

		if (!idClient) {
			var $groupeMontant = $toutes.find('optgroup[data-groupe="montant-correspondant"]').clone();
			if ($groupeMontant.length) {
				$affecterSelect.html('<option value="">Choisir la facture...</option>' + $('<div>').append($groupeMontant).html());
				$('#affecterSelectZone').removeClass('d-none');
			} else {
				$affecterSelect.html('<option value="">Choisir la facture...</option>');
				$('#affecterSelectZone').addClass('d-none');
			}
			$affecterSelect.select2({ dropdownParent: $('#affecterModal'), width: '100%' });
			affecterAfficherInfosClient('');
			return;
		}

		var $optionsClient = $toutes.find('option[data-client="' + idClient + '"]');
		if ($optionsClient.length === 0) {
			$affecterSelect.html('<option value="">Aucune facture pour ce client</option>');
		} else {
			var $selectNew = $('<select><option value="">Choisir la facture...</option></select>');
			$optionsClient.each(function () {
				$selectNew.append($(this).clone());
			});
			$affecterSelect.html($selectNew.html());
		}
		$('#affecterSelectZone').removeClass('d-none');
		$affecterSelect.select2({ dropdownParent: $('#affecterModal'), width: '100%' });
		affecterAfficherInfosClient(idClient);
	}

	$(document).on('change', '#affecterClientSelect', function () {
		affecterAppliquerFiltreClient($(this).val());
	});

	$(document).on('click', '.rapprochement-choisir', function () {
		var $tr = $(this).closest('tr');
		ouvrirAffecterModal($tr, $(this).data('affecter-type'));
	});

	$(document).on('change', '#affecterSelect', function () {
		affecterBasculerZoneNouvelleCharge(affecterTypeCourant === 'charge' && $(this).val() === '__nouvelle__');
		if (affecterTypeCourant === 'facture') {
			affecterAfficherInfosClient($(this).find(':selected').data('client'));
		}
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

		if (affecterTypeCourant === 'facture') {
			var libelleFacture = $('#affecterSelect option:selected').text();
			var creditLigne = $.trim(affecterLigneCourante.find('td:nth-child(4)').text());
			$('#affecterModal').modal('hide');
			demanderConfirmationRapprochement(
				'Le crédit de <strong>' + escHtml(creditLigne) + '</strong> sera lié à :<br><strong>' + escHtml(libelleFacture) + '</strong><br><span class="text-muted" style="font-size:0.8rem;">Un nouveau règlement sera créé pour cette facture.</span>',
				function () {
					$selectSource.val(valeur);
					affecterLigneCourante.find('.rapprochement-valider').trigger('click');
				}
			);
			return;
		}

		$selectSource.val(valeur);
		$('#affecterModal').modal('hide');
		affecterLigneCourante.find('.rapprochement-valider').trigger('click');
	});

	// "Associer ce règlement" : le crédit correspond à un paiement déjà saisi manuellement pour ce
	// client (panneau "Règlements déjà enregistrés") - on lie directement, sans passer par le
	// <select> de factures ni créer un nouveau règlement en doublon.
	$(document).on('click', '.rapprochement-associer-reglement', function () {
		var idPayment = $(this).data('payment-id');
		var texteReglement = $(this).closest('.list-group-item').find('span').first().text().trim();
		var $ligneCourante = affecterLigneCourante;
		$('#affecterModal').modal('hide');
		demanderConfirmationRapprochement(
			'Ce crédit sera associé au règlement déjà enregistré :<br><strong>' + escHtml(texteReglement) + '</strong><br><span class="text-muted" style="font-size:0.8rem;">Aucun nouveau paiement ne sera créé.</span>',
			function () {
				$ligneCourante.find('.rapprochement-facture-select').val('');
				$ligneCourante.find('.rapprochement-payment-existant').val(idPayment);
				$ligneCourante.find('.rapprochement-valider').trigger('click');
			}
		);
	});

	// "Annuler ce rapprochement" (ligne "Facture rapprochée") : retour à "à valider" - voir
	// annulerRapprochementFacture() côté contrôleur pour la logique de suppression conditionnelle
	// du règlement (jamais s'il s'agissait d'un règlement du client déjà existant).
	$(document).on('click', '.rapprochement-annuler-facture', function () {
		var $tr = $(this).closest('tr');
		var id = $tr.data('id');
		var libelle = $tr.data('libelle');
		demanderAnnulationRapprochement(
			'La ligne <strong>' + escHtml(libelle) + '</strong> redeviendra "à valider".<br><span class="text-muted" style="font-size:0.8rem;">Si un nouveau règlement avait été créé pour ce rapprochement, il sera supprimé et la facture recalculée. Un règlement déjà existant du client, lui, ne sera jamais supprimé — seulement délié.</span>',
			function () {
				$.post('components/com_rapprochement/controleurs/router.php?task=annulerRapprochementFacture', { id: id }, function (response) {
					if (response.success) {
						window.location.reload();
					} else {
						alert(response.message || "Erreur lors de l'annulation");
					}
				});
			}
		);
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
		if (resume && resume.nb_paiements_lies > 0) {
			html += '<li>' + resume.nb_paiements_lies + ' règlement(s) existant(s) du client simplement délié(s) (déjà enregistrés avant cet import, jamais supprimés)</li>';
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
		var idPaymentExistant = $tr.find('.rapprochement-payment-existant').val();

		if (matchType === 'debit_charge_existante' && !idChargeExistante) {
			ouvrirAffecterModal($tr, 'charge');
			return;
		}
		if (matchType === 'credit_ambigu' && !idFacture && !idPaymentExistant) {
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

		function envoyerValiderDirect(force) {
			var payload = { id: id, id_facture: idFacture, id_charge_existante: idChargeExistante };
			if (idPaymentExistant) {
				payload.id_payment_existant = idPaymentExistant;
			}
			if (force) {
				payload.force_reaffectation = 1;
			}
			$.post('components/com_rapprochement/controleurs/router.php?task=validerLigne', payload, function (response) {
				if (response.success) {
					window.location.reload();
					return;
				}
				if (gererBesoinConfirmation(response, function () { envoyerValiderDirect(true); })) {
					return;
				}
				alert(response.message || 'Erreur lors de la validation');
				// Le choix de règlement échoué (introuvable/refusé) ne doit pas rester collé à la
				// ligne pour un prochain essai - la prochaine ouverture du modal doit repartir propre.
				$tr.find('.rapprochement-payment-existant').val('');
			});
		}
		envoyerValiderDirect(false);
	});

	function envoyerValiderJustificatif(force) {
		var $btn = $('#validerJustificatifConfirmerBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Confirmation...');
		var formData = new FormData();
		formData.append('id', validerLigneCourante);
		if (validerIdChargeExistanteCourant) {
			formData.append('id_charge_existante', validerIdChargeExistanteCourant);
		}
		if (force) {
			formData.append('force_reaffectation', '1');
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
					return;
				}
				if (gererBesoinConfirmation(response, function () { envoyerValiderJustificatif(true); })) {
					$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmer');
					return;
				}
				alert(response.message || 'Erreur lors de la validation');
				$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmer');
			},
			error: function () {
				alert('Erreur lors de la validation');
				$btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirmer');
			}
		});
	}

	$('#validerJustificatifConfirmerBtn').on('click', function () {
		envoyerValiderJustificatif(false);
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
			if (!response.success) {
				return;
			}
			(response.bulletins || []).forEach(function (b) {
				$select.append('<option value="' + b.id_charge + '">' + escHtml(b.title) + '</option>');
			});
			// Règle 4 (listes distinctes) : les bulletins déjà rattachés à une AUTRE ligne de relevé
			// restent proposés (pas cachés) dans un second groupe visuellement marqué - les
			// sélectionner déclenche la fenêtre de réaffectation (voir gererBesoinConfirmation()).
			if (response.bulletins_deja_affectes && response.bulletins_deja_affectes.length) {
				var $optgroup = $('<optgroup label="⚠ Bulletins déjà affectés (réaffectation possible)"></optgroup>');
				response.bulletins_deja_affectes.forEach(function (b) {
					$optgroup.append('<option value="' + b.id_charge + '">⚠ ' + escHtml(b.title) + '</option>');
				});
				$select.append($optgroup);
			}
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
		$('#justificatifRemarque').val('');
		$('#justificatifSuggestionEmploye').addClass('d-none').empty();

		// Pré-remplit mois/année du bulletin depuis la date de l'opération (JJ/MM/AAAA affichée).
		var parties = (dateOperation || '').split('/');
		$('#justificatifPayslipMois').val(parties.length === 3 ? parseInt(parties[1], 10) : (new Date()).getMonth() + 1);
		$('#justificatifPayslipAnnee').val(parties.length === 3 ? parties[2] : (new Date()).getFullYear());

		var employeSuggereRaw = $tr.attr('data-employe-suggere');
		var employeSuggere = null;
		try { employeSuggere = employeSuggereRaw ? JSON.parse(employeSuggereRaw) : null; } catch (e) { employeSuggere = null; }

		var fournisseurSuggereRaw = $tr.attr('data-fournisseur-suggere');
		var fournisseurSuggere = null;
		try { fournisseurSuggere = fournisseurSuggereRaw ? JSON.parse(fournisseurSuggereRaw) : null; } catch (e) { fournisseurSuggere = null; }

		// L'employé (salaire) est toujours prioritaire sur un fournisseur détecté - un même
		// virement ne correspond jamais aux deux à la fois en pratique (voir matcherDebit() côté
		// serveur, qui ne cherche même un fournisseur que si aucun employé n'a matché).
		if (employeSuggere && employeSuggere.id) {
			$('#justificatifResourcehumaine').val(employeSuggere.id);
			$('#justificatifSuggestionEmploye').removeClass('d-none').html(
				'<i class="fa fa-lightbulb mr-1"></i>Ce libellé ressemble à un virement vers <strong>' + escHtml(employeSuggere.nom_complet) + '</strong> — bulletin de paie pré-rempli.'
			);
			$('input[name=justificatifMode][value=payslip]').prop('checked', true);
			justificatifAfficherMode('payslip');
			chargerBulletinsExistants(employeSuggere.id);
		} else if (fournisseurSuggere && fournisseurSuggere.id) {
			$('#justificatifFournisseur').val(fournisseurSuggere.id);
			$('#justificatifSuggestionEmploye').removeClass('d-none').html(
				'<i class="fa fa-lightbulb mr-1"></i>Ce libellé ressemble à un virement vers <strong>' + escHtml(fournisseurSuggere.nom_complet) + '</strong> — fournisseur pré-rempli.'
			);
			$('input[name=justificatifMode][value=fournisseur]').prop('checked', true);
			justificatifAfficherMode('fournisseur');
			chargerBulletinsExistants(null);
		} else {
			$('input[name=justificatifMode][value=charge]').prop('checked', true);
			justificatifAfficherMode('charge');
			chargerBulletinsExistants(null);
		}

		$('#justificatifManuelModal').modal('show');
	});

	var justificatifLibelleBoutonCourant = '<i class="fa fa-check mr-1"></i> Créer la charge';

	function envoyerJustificatifManuel(force) {
		var $btn = $('#justificatifConfirmerBtn');
		if (!force) {
			justificatifLibelleBoutonCourant = $btn.html();
		}
		var libelleBoutonInitial = justificatifLibelleBoutonCourant;
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Enregistrement...');
		var mode = $('input[name=justificatifMode]:checked').val();
		var formData = new FormData();
		formData.append('id', justificatifLigneCourante);
		formData.append('mode', mode);
		formData.append('montant', $('#justificatifMontant').val());
		formData.append('remarque', $('#justificatifRemarque').val());

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

		if (force) {
			formData.append('force_reaffectation', '1');
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
					return;
				}
				if (gererBesoinConfirmation(response, function () { envoyerJustificatifManuel(true); })) {
					$btn.prop('disabled', false).html(libelleBoutonInitial);
					return;
				}
				alert(response.message || "Erreur lors de l'enregistrement");
				$btn.prop('disabled', false).html(libelleBoutonInitial);
			},
			error: function () {
				alert("Erreur lors de l'enregistrement");
				$btn.prop('disabled', false).html(libelleBoutonInitial);
			}
		});
	}

	$('#justificatifConfirmerBtn').on('click', function () {
		envoyerJustificatifManuel(false);
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
