<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Resources humaines</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Resources humaines</li>
					</ul>
				</div>
				<div class="col-auto">
					<?php if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) : ?>
						<a href="javascript:void(0)" data-target="#div-export-humanresource" data-toggle="modal" class="btn btn-primary mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Exporter la fiche d'absence par mois">
							<i class="fa fa-file-excel"></i> Exporter
						</a>
						<a href="index.php?option=com_resourcehumaine&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter resourcehumaine">
							<i class="fas fa-plus"></i>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php
		// --- KPI : uniquement les employés/stagiaires/périodes de test ACTIFS ------------
		// (un employé inactif ne compte ni dans les effectifs, ni dans l'alerte dossiers
		// incomplets : son dossier RH n'a plus besoin d'être finalisé).
		$kpiTitulairesActifs = count(array_filter($resources_humaines_titulaire, function ($e) { return $e->isActive(); }));
		$kpiStagiairesActifs = count(array_filter($resources_humaines_stagaire, function ($e) { return $e->isActive(); }));
		$kpiPeriodeTestActifs = count(array_filter($resources_humaines_periode_de_test, function ($e) { return $e->isActive(); }));
		$kpiDossiersIncomplets = 0;
		foreach (array_merge($resources_humaines_titulaire, $resources_humaines_stagaire, $resources_humaines_periode_de_test) as $employeKpi) {
			if (!$employeKpi->isActive()) {
				continue;
			}
			$filesEmployeKpi = fileresourcehumaine::findAllByResourcehumaine($employeKpi->getId());
			$manquantsEmployeKpi = fileresourcehumaine::documentsManquants($employeKpi->getStatus(), $filesEmployeKpi);
			if (!empty($manquantsEmployeKpi)) {
				$kpiDossiersIncomplets++;
			}
		}
		?>

		<div class="row">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-user-check"></i></span>
							<div class="dash-count">
								<div class="dash-title">Titulaires actifs</div>
								<div class="dash-counts"><p><?= $kpiTitulairesActifs ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-4"><i class="fa fa-user-graduate"></i></span>
							<div class="dash-count">
								<div class="dash-title">Stagiaires actifs</div>
								<div class="dash-counts"><p><?= $kpiStagiairesActifs ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="fa fa-user-clock"></i></span>
							<div class="dash-count">
								<div class="dash-title">Période de test actifs</div>
								<div class="dash-counts"><p><?= $kpiPeriodeTestActifs ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill <?= $kpiDossiersIncomplets > 0 ? 'kpi-blink' : '' ?>">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-9"><i class="fa fa-exclamation-triangle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Dossiers incomplets (actifs)</div>
								<div class="dash-counts"><p><?= $kpiDossiersIncomplets ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		// --- Planning des stagiaires par pôle (aide à la décision de recrutement) ---------
		// 4 pôles fixes, 2 postes nominaux chacun. Les stages actifs sans date de fin définie
		// se voient estimer une date de fin prévisionnelle à partir de la durée moyenne
		// historique constatée pour ce pôle (marqué "estimé" dans l'interface, sinon "confirmé").
		$polesGantt = array('Community manager', 'Developpeur web', 'Graphic designer', 'Commercial');
		$sommeParPoste = array();
		$nbParPoste = array();
		foreach ($resources_humaines_stagaire as $s) {
			if (!$s->getEndDate() || !$s->getStartDate()) {
				continue;
			}
			$duree = (strtotime($s->getEndDate()) - strtotime($s->getStartDate())) / 86400;
			if ($duree <= 0) {
				continue;
			}
			$poste = $s->getFunction();
			if (!isset($sommeParPoste[$poste])) {
				$sommeParPoste[$poste] = 0;
				$nbParPoste[$poste] = 0;
			}
			$sommeParPoste[$poste] += $duree;
			$nbParPoste[$poste]++;
		}
		$dureeMoyenneParPoste = array();
		foreach ($sommeParPoste as $poste => $somme) {
			$dureeMoyenneParPoste[$poste] = round($somme / $nbParPoste[$poste]);
		}
		$dureeMoyenneDefaut = 60;

		$today = date('Y-m-d');
		$minDateGantt = $today;
		$maxDateGantt = date('Y-m-d', strtotime('+30 days'));
		$ganttParPole = array();
		foreach ($polesGantt as $pole) {
			$ganttParPole[$pole] = array();
			foreach ($resources_humaines_stagaire as $s) {
				if (!$s->isActive() || $s->getFunction() !== $pole) {
					continue;
				}
				$debut = $s->getStartDate();
				$fin = $s->getEndDate();
				$estime = false;
				if (!$fin) {
					$duree = isset($dureeMoyenneParPoste[$pole]) ? $dureeMoyenneParPoste[$pole] : $dureeMoyenneDefaut;
					$fin = date('Y-m-d', strtotime($debut . ' +' . $duree . ' days'));
					$estime = true;
				}
				$joursRestants = (int) ((strtotime($fin) - strtotime($today)) / 86400);
				$ganttParPole[$pole][] = array(
					'id' => $s->getId(),
					'nom' => trim($s->getFirstName() . ' ' . $s->getLastName()),
					'debut' => $debut,
					'fin' => $fin,
					'estime' => $estime,
					'jours_restants' => $joursRestants,
					'bientot_fini' => $joursRestants <= 30,
				);
				if (strtotime($debut) < strtotime($minDateGantt)) {
					$minDateGantt = $debut;
				}
				if (strtotime($fin) > strtotime($maxDateGantt)) {
					$maxDateGantt = $fin;
				}
			}
		}
		$minDateGantt = date('Y-m-d', strtotime($minDateGantt . ' -5 days'));
		$maxDateGantt = date('Y-m-d', strtotime($maxDateGantt . ' +10 days'));
		$totalJoursGantt = max(1, (strtotime($maxDateGantt) - strtotime($minDateGantt)) / 86400);

		$moisGraduationGantt = array();
		$curseurMois = strtotime(date('Y-m-01', strtotime($minDateGantt)));
		while ($curseurMois <= strtotime($maxDateGantt)) {
			$moisGraduationGantt[] = date('Y-m-d', $curseurMois);
			$curseurMois = strtotime('+1 month', $curseurMois);
		}

		function ganttPct($date, $minDateGantt, $totalJoursGantt)
		{
			return max(0, min(100, ((strtotime($date) - strtotime($minDateGantt)) / 86400 / $totalJoursGantt) * 100));
		}

		$noms_mois_fr = array('Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc');
		$couleurPole = array(
			'Community manager' => 'blue',
			'Developpeur web' => 'brand',
			'Graphic designer' => 'pink',
			'Commercial' => 'teal',
		);
		?>

		<?php ob_start(); // Planning capturé ici, affiché après le bloc "Personnel" plus bas ?>
		<div class="row">
			<div class="col-sm-12">
				<div class="card intern-gantt-card">
					<div class="card-header">
						<h4 class="card-title">Planning des stagiaires par pôle</h4>
						<p class="text-muted mb-0 gantt-subtitle">2 postes nominaux par pôle — une case pointillée signale un poste vacant, une ligne pulsante signale une fin de stage dans moins de 30 jours.</p>
					</div>
					<div class="card-body">
						<div class="gantt-wrapper">
							<div class="gantt-scale">
								<?php foreach ($moisGraduationGantt as $m) : ?>
									<div class="gantt-scale-mark" style="left: <?= ganttPct($m, $minDateGantt, $totalJoursGantt) ?>%;"><?= $noms_mois_fr[(int) date('n', strtotime($m)) - 1] ?></div>
								<?php endforeach; ?>
							</div>
							<div class="gantt-today-line" style="left: <?= ganttPct($today, $minDateGantt, $totalJoursGantt) ?>%;"><span>Aujourd'hui</span></div>

							<?php foreach ($polesGantt as $pole) : $lignesGantt = $ganttParPole[$pole]; $nbLignesGantt = max(2, count($lignesGantt)); ?>
								<div class="gantt-department">
									<div class="gantt-department-label">
										<span><?= htmlspecialchars($pole) ?></span>
										<span class="badge badge-pill <?= count($lignesGantt) >= 2 ? 'bg-success-light' : (count($lignesGantt) == 1 ? 'bg-warning-light' : 'bg-danger-light') ?>"><?= count($lignesGantt) ?>/2</span>
									</div>
									<div class="gantt-department-rows">
										<?php for ($i = 0; $i < $nbLignesGantt; $i++) : ?>
											<?php if (isset($lignesGantt[$i])) : $ligne = $lignesGantt[$i]; ?>
												<div class="gantt-row">
													<div class="gantt-bar gantt-bar-<?= $couleurPole[$pole] ?> <?= $ligne['bientot_fini'] ? 'gantt-bar-ending' : '' ?> <?= $ligne['estime'] ? 'gantt-bar-estimated' : '' ?>"
														style="left: <?= ganttPct($ligne['debut'], $minDateGantt, $totalJoursGantt) ?>%; width: <?= max(2, ganttPct($ligne['fin'], $minDateGantt, $totalJoursGantt) - ganttPct($ligne['debut'], $minDateGantt, $totalJoursGantt)) ?>%;"
														title="<?= htmlspecialchars($ligne['nom'] . ' — du ' . normaldate($ligne['debut']) . ' au ' . normaldate($ligne['fin']) . ($ligne['estime'] ? ' (estimé)' : ' (confirmé)')) ?>">
														<span class="gantt-bar-label"><?= htmlspecialchars($ligne['nom']) ?><?php if ($ligne['bientot_fini']) : ?> <i class="fa fa-exclamation-circle ml-1"></i><?php endif; ?></span>
													</div>
												</div>
											<?php else : ?>
												<div class="gantt-row">
													<div class="gantt-bar gantt-bar-vacant" style="left: 0%; width: 100%;">
														<span class="gantt-bar-label"><i class="fa fa-plus-circle mr-1"></i> Poste vacant — disponible pour recrutement</span>
													</div>
												</div>
											<?php endif; ?>
										<?php endfor; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="gantt-legend">
							<span><i class="gantt-legend-swatch gantt-legend-confirmed"></i> Confirmé</span>
							<span><i class="gantt-legend-swatch gantt-legend-estimated"></i> Fin estimée</span>
							<span><i class="gantt-legend-swatch gantt-legend-ending"></i> Fin &lt; 30 jours</span>
							<span><i class="gantt-legend-swatch gantt-legend-vacant"></i> Poste vacant</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php $ganttMarkupHtml = ob_get_clean(); ?>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header d-flex align-items-center flex-wrap">
						<h4 class="card-title mr-3">Personnel</h4>
						<div class="rh-live-search ml-0 mr-auto">
							<i class="fa fa-search"></i>
							<input type="text" id="rh-live-search" class="form-control form-control-sm" placeholder="Rechercher un employé...">
						</div>
						<div class="form-group mb-0">
							<select class="form-control form-control-sm" id="rh-status-filter">
								<option value="all">Tous les statuts</option>
								<option value="active" selected>Actifs uniquement</option>
								<option value="inactive">Inactifs uniquement</option>
							</select>
						</div>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<button class="nav-link active" id="nav-home-tab" data-toggle="tab" data-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Titulaire <span class="badge badge-pill ml-1"><?= sizeof($resources_humaines_titulaire) ?></span></button>
								<button class="nav-link" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Stagaire <span class="badge badge-pill ml-1"><?= sizeof($resources_humaines_stagaire) ?></span></button>
								<button class="nav-link" id="nav-contact-tab" data-toggle="tab" data-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Periode de test <span class="badge badge-pill ml-1"><?= sizeof($resources_humaines_periode_de_test) ?></span></button>
							</div>
						</div>
						<div class="tab-content" id="nav-tabContent">
							<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
								<div class="table-responsive list-box">
									<table id="titulaire-table" class="table table-stripped table-center table-hover datatable">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Employé</th>
												<th>Contact</th>
												<th>Fonction</th>
												<th>Période</th>
												<th>Ancienneté</th>
												<th>Statut</th>
												<th class="text-right">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($resources_humaines_titulaire as $resourcehumaine): ?>
												<?php
												$filesRowRh = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
												$manquantsRowRh = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $filesRowRh);
												$dossierIncompletRowRh = !empty($manquantsRowRh);
												?>
												<tr class="<?=$resourcehumaine->getEndDate() ? 'bg-danger-light' : null?> <?= $dossierIncompletRowRh ? 'rh-row-incomplete' : '' ?>" data-active="<?= $resourcehumaine->isActive() ? '1' : '0' ?>" <?php if ($dossierIncompletRowRh) : ?>title="Dossier incomplet : <?= htmlspecialchars(implode(', ', $manquantsRowRh)) ?>"<?php endif; ?>>
													<td><?php echo $resourcehumaine->getId(); ?></td>
													<td>
														<?php $photoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
														<h2 class="table-avatar">
															<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Photo employé"> <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getlastName(); ?></a>
														</h2>
														<?php if ($resourcehumaine->getReference() != '') : ?><small class="text-muted">Matricule <?= htmlspecialchars($resourcehumaine->getReference()) ?></small><?php endif; ?>
													</td>
													<td><?php echo htmlspecialchars(trim($resourcehumaine->getPhone() . ' / ' . $resourcehumaine->getSecondPhone(), ' /')) ?: '—'; ?></td>
													<td><?php echo $resourcehumaine->getFunction(); ?></td>
													<td><?php echo normaldate($resourcehumaine->getStartDate()); ?> <?php if ($resourcehumaine->getEndDate()) : ?>→ <?php echo normaldate($resourcehumaine->getEndDate()); ?><?php endif; ?></td>
													<td><?php echo $resourcehumaine->calculerPeriodeAgent($resourcehumaine->getStartDate(), $resourcehumaine->getEndDate()); ?></td>
													<td>
														<span class="badge badge-pill <?= $resourcehumaine->isActive() ? 'bg-success-light' : 'bg-danger-light' ?>"><?= $resourcehumaine->isActive() ? 'Actif' : 'Inactif' ?></span>
													</td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i> Afficher</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-warning" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i> Modifier</a>
																	<a href="index.php?option=com_resourcehumaine&task=duplicate&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Dupliquer"><i class="fa fa-file"></i> Dupliquer</a>
																	<?php $fileresourcehumaine = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());?>
																	<a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-info" data-toggle="tooltip" data-placement="top" data-original-title="Fichiers"><i class="fa fa-file"></i> Fichiers <?php if(sizeof($fileresourcehumaine)<=0): ?> <span class="point text-danger"></span><?php endif;?></a>
																	<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Bulletin de paie"><i class="fa fa-file"></i> Bulletin de paie </a>
																	<a href="index.php?option=com_resourcehumaine&task=request&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Demandes"><i class="fa fa-envelope"></i>  Demandes</a>
																	<a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Absences"><i class="fa fa-file-alt"></i> Absences</a>
																	<a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-brown" data-toggle="tooltip" data-placement="top" data-original-title="Bonus"><i class="fa fa-money-bill"></i> Bonus</a>
																	<a href="index.php?option=com_resourcehumaine&task=joboffer&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-purple" data-toggle="tooltip" data-placement="top" data-original-title="Offre d'emploi"><i class="fa fa-file-signature"></i> Offre d'emploi</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
																	<a href="javascript:void(0);" class="dropdown-item text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $resourcehumaine->getId(); ?>"><i class="far fa-trash-alt"></i> Supprimer</a>
																<?php endif; ?>
															</div>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
								<div class="table-responsive list-box">
									<table id="stagiaire-table" class="table table-stripped table-center table-hover datatable">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Employé</th>
												<th>Contact</th>
												<th>Fonction</th>
												<th>Période</th>
												<th>Durée du stage</th>
												<th>Statut</th>
												<th class="text-right">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($resources_humaines_stagaire as $resourcehumaine): ?>
												<?php
												$filesRowRh = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
												$manquantsRowRh = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $filesRowRh);
												$dossierIncompletRowRh = !empty($manquantsRowRh);
												?>
												<tr class="<?=$resourcehumaine->getEndDate() ? 'bg-danger-light' : null?> <?= $dossierIncompletRowRh ? 'rh-row-incomplete' : '' ?>" data-active="<?= $resourcehumaine->isActive() ? '1' : '0' ?>" <?php if ($dossierIncompletRowRh) : ?>title="Dossier incomplet : <?= htmlspecialchars(implode(', ', $manquantsRowRh)) ?>"<?php endif; ?>>
													<td><?php echo $resourcehumaine->getId(); ?></td>
													<td>
														<?php $photoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
														<h2 class="table-avatar">
															<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Photo employé"> <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getlastName(); ?></a>
														</h2>
														<?php if ($resourcehumaine->getReference() != '') : ?><small class="text-muted">Matricule <?= htmlspecialchars($resourcehumaine->getReference()) ?></small><?php endif; ?>
													</td>
													<td><?php echo htmlspecialchars(trim($resourcehumaine->getPhone())) ?: '—'; ?></td>
													<td><?php echo $resourcehumaine->getFunction(); ?></td>
													<td><?php echo normaldate($resourcehumaine->getStartDate()); ?> <?php if ($resourcehumaine->getEndDate()) : ?>→ <?php echo normaldate($resourcehumaine->getEndDate()); ?><?php endif; ?></td>
													<td><?php echo $resourcehumaine->calculerPeriodeStagiaire($resourcehumaine->getStartDate(), $resourcehumaine->getEndDate()); ?></td>
													<td>
														<span class="badge badge-pill <?= $resourcehumaine->isActive() ? 'bg-success-light' : 'bg-danger-light' ?>"><?= $resourcehumaine->isActive() ? 'Actif' : 'Inactif' ?></span>
													</td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i> Afficher</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-warning" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i> Modifier</a>
																	<a href="index.php?option=com_resourcehumaine&task=duplicate&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Dupliquer"><i class="fa fa-file"></i> Dupliquer</a>
																	<?php $fileresourcehumaine = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());?>
																	<a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-info" data-toggle="tooltip" data-placement="top" data-original-title="Fichiers"><i class="fa fa-file"></i> Fichiers <?php if(sizeof($fileresourcehumaine)<=0): ?> <span class="point text-danger"></span><?php endif;?></a>
																	<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Bulletin de paie"><i class="fa fa-file"></i> Bulletin de paie </a>
																	<a href="index.php?option=com_resourcehumaine&task=request&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Demandes"><i class="fa fa-envelope"></i>  Demandes</a>
																	<a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Absences"><i class="fa fa-file-alt"></i> Absences</a>
																	<a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-brown" data-toggle="tooltip" data-placement="top" data-original-title="Bonus"><i class="fa fa-money-bill"></i> Bonus</a>
																	<a href="index.php?option=com_resourcehumaine&task=joboffer&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-purple" data-toggle="tooltip" data-placement="top" data-original-title="Offre d'emploi"><i class="fa fa-file-signature"></i> Offre d'emploi</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
																	<a href="javascript:void(0);" class="dropdown-item text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $resourcehumaine->getId(); ?>"><i class="far fa-trash-alt"></i> Supprimer</a>
																<?php endif; ?>
															</div>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
								<div class="table-responsive list-box">
									<table id="test-table" class="table table-stripped table-center table-hover datatable">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Employé</th>
												<th>Contact</th>
												<th>Fonction</th>
												<th>Période</th>
												<th>Ancienneté</th>
												<th>Statut</th>
												<th class="text-right">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($resources_humaines_periode_de_test as $resourcehumaine): ?>
												<?php
												$filesRowRh = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
												$manquantsRowRh = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $filesRowRh);
												$dossierIncompletRowRh = !empty($manquantsRowRh);
												?>
												<tr class="<?=$resourcehumaine->getEndDate() ? 'bg-danger-light' : null?> <?= $dossierIncompletRowRh ? 'rh-row-incomplete' : '' ?>" data-active="<?= $resourcehumaine->isActive() ? '1' : '0' ?>" <?php if ($dossierIncompletRowRh) : ?>title="Dossier incomplet : <?= htmlspecialchars(implode(', ', $manquantsRowRh)) ?>"<?php endif; ?>>
													<td><?php echo $resourcehumaine->getId(); ?></td>
													<td>
														<?php $photoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
														<h2 class="table-avatar">
															<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Photo employé"> <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getlastName(); ?></a>
														</h2>
														<?php if ($resourcehumaine->getReference() != '') : ?><small class="text-muted">Matricule <?= htmlspecialchars($resourcehumaine->getReference()) ?></small><?php endif; ?>
													</td>
													<td><?php echo htmlspecialchars(trim($resourcehumaine->getPhone())) ?: '—'; ?></td>
													<td><?php echo $resourcehumaine->getFunction(); ?></td>
													<td><?php echo normaldate($resourcehumaine->getStartDate()); ?> <?php if ($resourcehumaine->getEndDate()) : ?>→ <?php echo normaldate($resourcehumaine->getEndDate()); ?><?php endif; ?></td>
													<td><?php echo $resourcehumaine->calculerPeriodeAgent($resourcehumaine->getStartDate(), $resourcehumaine->getEndDate()); ?></td>
													<td>
														<span class="badge badge-pill <?= $resourcehumaine->isActive() ? 'bg-success-light' : 'bg-danger-light' ?>"><?= $resourcehumaine->isActive() ? 'Actif' : 'Inactif' ?></span>
													</td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i> Afficher</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-warning" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i> Modifier</a>
																	<a href="index.php?option=com_resourcehumaine&task=duplicate&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Dupliquer"><i class="fa fa-file"></i> Dupliquer</a>
																	<?php $fileresourcehumaine = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());?>
																	<a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-info" data-toggle="tooltip" data-placement="top" data-original-title="Fichiers"><i class="fa fa-file"></i> Fichiers <?php if(sizeof($fileresourcehumaine)<=0): ?> <span class="point text-danger"></span><?php endif;?></a>
																	<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Bulletin de paie"><i class="fa fa-file"></i> Bulletin de paie </a>
																	<a href="index.php?option=com_resourcehumaine&task=request&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Demandes"><i class="fa fa-envelope"></i>  Demandes</a>
																	<a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Absences"><i class="fa fa-file-alt"></i> Absences</a>
																	<a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-brown" data-toggle="tooltip" data-placement="top" data-original-title="Bonus"><i class="fa fa-money-bill"></i> Bonus</a>
																	<a href="index.php?option=com_resourcehumaine&task=joboffer&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-purple" data-toggle="tooltip" data-placement="top" data-original-title="Offre d'emploi"><i class="fa fa-file-signature"></i> Offre d'emploi</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
																	<a href="javascript:void(0);" class="dropdown-item text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $resourcehumaine->getId(); ?>"><i class="far fa-trash-alt"></i> Supprimer</a>
																<?php endif; ?>
															</div>
														</div>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<?php echo $ganttMarkupHtml; ?>
	</div>
</div>
<!-- /Page Wrapper -->

<!-- Add Category Modal -->
<div id="div-export-humanresource" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title mr-auto">Exporter par mois</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<form method="post" action="components/com_resourcehumaine/controleurs/router.php?task=exportAbsenceFile">
							<div class="row">
								<div class="col-12">
									<label class="form-label">Mois<span class="text-danger"> * </span></label>
									<div class="form-group">
										<input type="month" name="month" class="form-control" required>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<button type="submit" class="btn btn-primary">Valider</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Add Category Modal -->

<script type="text/javascript">
	$(function() {

		// Diagramme des stagiaires : les barres poussent depuis la gauche à l'ouverture
		// (largeur déjà fixée en inline-style par PHP, GSAP anime "depuis" 0).
		if (typeof gsap !== 'undefined') {
			gsap.from('.gantt-bar:not(.gantt-bar-vacant)', {
				scaleX: 0,
				transformOrigin: 'left center',
				duration: 0.9,
				stagger: 0.06,
				ease: 'power2.out',
				clearProps: 'transform'
			});
			gsap.from('.gantt-bar-vacant', {
				opacity: 0,
				duration: 0.6,
				stagger: 0.06,
				delay: 0.3,
				ease: 'power1.out'
			});
			gsap.from('.gantt-today-line', { opacity: 0, duration: 0.8, delay: 0.4 });
		}
		$('[data-toggle="tooltip"], .gantt-bar[title]').tooltip();

		var msgsucces = "Resource humaine supprimé avec succès";

		$(document).on("click", ".delete", function() {
			event.preventDefault();
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_resourcehumaine/controleurs/router.php?task=deleteResourceHumaine", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().parent().parent().remove()
						}, 1000);

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})

		// Recherche instantanée : cible la table du seul onglet actuellement visible
		// (Titulaire/Stagaire/Periode de test), chacun ayant sa propre instance DataTables.
		function rhActiveTableId() {
			return $('.tab-pane.active table.datatable').attr('id');
		}
		$(document).on("keyup", "#rh-live-search", function() {
			var value = $(this).val();
			var tableId = rhActiveTableId();
			if (tableId && $.fn.DataTable && $.fn.DataTable.isDataTable('#' + tableId)) {
				$('#' + tableId).DataTable().search(value).draw();
			}
		});

		// Filtre Actif/Inactif : basé sur data-active posé sur chaque <tr>, appliqué aux 3
		// tableaux (peu importe l'onglet affiché) pour rester cohérent en changeant d'onglet.
		var rhStatusFilter = 'active';
		var rhTableIds = ['titulaire-table', 'stagiaire-table', 'test-table'];
		if ($.fn.DataTable) {
			$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
				if (!settings.nTable || rhTableIds.indexOf(settings.nTable.id) === -1) return true;
				if (rhStatusFilter === 'all') return true;
				var $row = $(settings.aoData[dataIndex].nTr);
				var actif = $row.attr('data-active');
				if (rhStatusFilter === 'active') return actif === '1';
				if (rhStatusFilter === 'inactive') return actif === '0';
				return true;
			});
			// script.js initialise déjà les .datatable avant que ce filtre soit enregistré
			// (leur premier draw() interne se fait donc sans lui) : un redraw explicite ici
			// applique "Actifs uniquement" (valeur par défaut du select) dès le chargement.
			rhTableIds.forEach(function(id) {
				if ($.fn.DataTable.isDataTable('#' + id)) {
					$('#' + id).DataTable().draw();
				}
			});
		}
		$(document).on("change", "#rh-status-filter", function() {
			rhStatusFilter = $(this).val();
			rhTableIds.forEach(function(id) {
				if ($.fn.DataTable && $.fn.DataTable.isDataTable('#' + id)) {
					$('#' + id).DataTable().draw();
				}
			});
		});
	});
</script>
