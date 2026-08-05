<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Charges</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Charges</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_charge')) :?>
				<div class="col-auto">
					<a href="index.php?option=com_charge&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter charge">
						<i class="fas fa-plus"></i>
					</a>
				</div>
				<?php endif;?>
			</div>
		</div>

		<?php
		// KPI en tête de page, calculés directement à partir de $charges déjà chargé (pas de
		// requête SQL supplémentaire). Limité au DH, comme tous les agrégats financiers de ce
		// CRM (les charges dans d'autres devises existent mais ne sont pas additionnables).
		$totalCharges = 0;
		$totalPayees = 0;
		$totalNonPayees = 0;
		foreach ($charges as $c) {
			if ($c->getDevise() != 'DH') {
				continue;
			}
			$totalCharges += (float) $c->getTotal();
			if ($c->isPaid()) {
				$totalPayees += (float) $c->getTotal();
			} else {
				$totalNonPayees += (float) $c->getTotal();
			}
		}
		$idsChargesAvecBulletin = payslip::findAllIdChargeLies();

		// Ventilation par année/mois/catégorie pour le graphique — calculée elle aussi
		// entièrement à partir de $charges déjà en mémoire, aucune requête SQL de plus.
		$chargesParAnneeMoisType = array();
		$anneesDisponibles = array();
		foreach ($charges as $c) {
			if ($c->getDevise() != 'DH') {
				continue;
			}
			$y = (int) date('Y', strtotime($c->getDateCharge()));
			$m = (int) date('n', strtotime($c->getDateCharge()));
			$t = $c->getType();
			// Garde-fou contre les dates mal saisies historiquement (ex: "0021-07-26" au
			// lieu de "2021-07-26") qui pollueraient sinon le sélecteur d'année du graphique.
			if ($y < 2000 || $y > ((int) date('Y')) + 1) {
				continue;
			}
			if (!isset($chargesParAnneeMoisType[$y])) {
				$chargesParAnneeMoisType[$y] = array();
			}
			if (!isset($chargesParAnneeMoisType[$y][$m])) {
				$chargesParAnneeMoisType[$y][$m] = array('fixe' => 0, 'variable' => 0, 'hors_hw' => 0);
			}
			if (!isset($chargesParAnneeMoisType[$y][$m][$t])) {
				$chargesParAnneeMoisType[$y][$m][$t] = 0;
			}
			$chargesParAnneeMoisType[$y][$m][$t] += (float) $c->getTotal();
			$anneesDisponibles[$y] = true;
		}
		krsort($anneesDisponibles);
		$anneesDisponibles = array_keys($anneesDisponibles);
		if (empty($anneesDisponibles)) {
			$anneesDisponibles = array((int) date('Y'));
		}

		$chartData = array();
		foreach ($anneesDisponibles as $y) {
			$fixe = array();
			$variable = array();
			$horsHw = array();
			for ($m = 1; $m <= 12; $m++) {
				$fixe[] = isset($chargesParAnneeMoisType[$y][$m]['fixe']) ? round($chargesParAnneeMoisType[$y][$m]['fixe'], 2) : 0;
				$variable[] = isset($chargesParAnneeMoisType[$y][$m]['variable']) ? round($chargesParAnneeMoisType[$y][$m]['variable'], 2) : 0;
				$horsHw[] = isset($chargesParAnneeMoisType[$y][$m]['hors_hw']) ? round($chargesParAnneeMoisType[$y][$m]['hors_hw'], 2) : 0;
			}
			$chartData[$y] = array(
				'fixe' => $fixe,
				'variable' => $variable,
				'hors_hw' => $horsHw,
				'total' => round(array_sum($fixe) + array_sum($variable) + array_sum($horsHw), 2)
			);
		}
		?>

		<div class="row">
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-9"><i class="fa fa-dollar-sign"></i></span>
							<div class="dash-count">
								<div class="dash-title">Total des charges (DH)</div>
								<div class="dash-counts"><p><span class="charge-total-counter" data-valeur="<?= $totalCharges ?>">0</span> DH</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-check"></i></span>
							<div class="dash-count">
								<div class="dash-title">Payées (DH)</div>
								<div class="dash-counts"><p><span class="charge-total-counter" data-valeur="<?= $totalPayees ?>">0</span> DH</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1"><i class="fa fa-exclamation-triangle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Non payées (DH)</div>
								<div class="dash-counts"><p><span class="charge-total-counter" data-valeur="<?= $totalNonPayees ?>">0</span> DH</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="card charge-chart-card mb-0">
					<div class="card-body">
						<div class="charge-chart-header">
							<div>
								<h4 class="card-title mb-1">Répartition des charges par catégorie et évolution mensuelle</h4>
								<div class="charge-chart-legend">
									<span><span class="charge-chart-legend-dot" style="background:#10b981;"></span>Charge fixe</span>
									<span><span class="charge-chart-legend-dot" style="background:#f59e0b;"></span>Charge variable</span>
									<span><span class="charge-chart-legend-dot" style="background:#6b7280;"></span>Hors Hello World</span>
								</div>
							</div>
							<div class="d-flex align-items-center flex-wrap" style="gap:1rem;">
								<span id="chargeChartDelta" class="charge-chart-delta flat"><i class="fa fa-minus"></i> —</span>
								<select id="chargeChartAnnee" class="form-control" style="width:auto;">
									<?php foreach ($anneesDisponibles as $y) :?>
									<option value="<?= $y ?>"><?= $y ?></option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
						<div id="chargeCategoryChart"></div>
					</div>
				</div>
			</div>
		</div>

		<script type="application/json" id="chargeChartData"><?= json_encode($chartData) ?></script>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des Charges</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 msgbox"></div>
						<!-- Search Filter -->
                        <div id="filter_inputs_charge" class="card filter-card px-4">
                            <div class="card-body pb-0">
                                <form method="post" action="" id="filterCharges">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-2">
                                            <div class="form-group">
                                                <label>Date début</label>
                                                <div class="cal-icon">
                                                    <input type="text" class="form-control datetimepicker" required name="from" id="from" value="<?php if (isset($_GET['from'])) echo normaldate($_GET['from']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-2">
                                            <div class="form-group">
                                                <label>Date fin</label>
                                                <div class="cal-icon">
                                                    <input type="text" class="form-control datetimepicker" required name="to" id="to" value="<?php if (isset($_GET['to'])) echo normaldate($_GET['to']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <a href="javascript:void(0)" class="btn btn-primary exportCharges" style="margin-top: 32px;"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /Search Filter -->
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable-charges">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Charge</th>
										<th>Montant</th>
										<th>Payée</th>
										<th>Remboursé</th>
										<th>Bulletin</th>
										<th>Date charge</th>
										<th>Date paiement</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($charges as $charge): ?>
									<?php
									$typeLabels = array('fixe' => 'Charge fixe', 'variable' => 'Charge variable', 'hors_hw' => 'Hors Hello World');
									$typeLabel = isset($typeLabels[$charge->getType()]) ? $typeLabels[$charge->getType()] : $charge->getType();
									$payePar = $charge->getPaidBy()->getId() == 0 ? $charge->getAgence()->getNom() : $charge->getPaidBy()->getNom()." ".$charge->getPaidBy()->getPrenom();
									$aBulletin = in_array($charge->getId(), $idsChargesAvecBulletin);
									?>
									<tr>
										<td><?php echo $charge->getId(); ?></td>
										<td>
											<div<?php if ($charge->getDescription() != '') : ?> data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($charge->getDescription()); ?>"<?php endif; ?>>
												<strong><?php echo $charge->getTitre(); ?></strong>
											</div>
											<span class="badge charge-type-badge <?php echo $charge->getType(); ?>"><?php echo $typeLabel; ?></span>
											<small class="text-muted d-block mt-1">Payé par <?php echo $payePar; ?></small>
											<?php $serviceLabelsCharge = array('domaine' => 'Nom de domaine', 'hosting' => 'Hébergement web', 'ssl' => 'Certificat SSL'); ?>
											<?php if ($charge->getServiceConcerne() !== '' && isset($serviceLabelsCharge[$charge->getServiceConcerne()])) : ?>
												<span class="badge bg-info-light d-inline-block mt-1"><i class="fa fa-sync-alt mr-1"></i><?= $serviceLabelsCharge[$charge->getServiceConcerne()] ?><?= $charge->getClient() ? ' — ' . htmlspecialchars(trim($charge->getClient()->getRaisonSocial()) !== '' ? $charge->getClient()->getRaisonSocial() : $charge->getClient()->getNom()) : '' ?></span>
											<?php endif; ?>
											<?php if (!empty($charge->getFournisseurs())) : ?>
												<div class="mt-1">
													<?php foreach ($charge->getFournisseurs() as $fournisseurBadge) : ?>
														<?php $nomFournisseurBadge = trim((string) $fournisseurBadge->getRaisonSocial()) !== '' ? $fournisseurBadge->getRaisonSocial() : trim($fournisseurBadge->getPrenom() . ' ' . $fournisseurBadge->getNom()); ?>
														<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseurBadge->getId() ?>" class="badge bg-default-light mr-1" target="_blank"><i class="fa fa-truck mr-1"></i><?= htmlspecialchars($nomFournisseurBadge) ?></a>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										</td>
										<td><b><?php echo number_format($charge->getTotal(), 2, ',', ' '). ' ' . $charge->getDevise(); ?></b></td>
										<td>
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_charge')) : ?>
												<?php $state = $charge->isPaid() ? 'oui' : 'non'; ?>
												<span class="badge charge-toggle-badge enable <?php echo $charge->isPaid() ? 'badge-success' : 'badge-danger'; ?>" data-id="<?= $charge->getId(); ?>" data-state="<?php echo $state; ?>" data-toggle="tooltip" data-placement="top" data-original-title="Cliquez pour basculer">
													<?php echo $charge->isPaid() ? 'Payée' : 'Non payée'; ?>
												</span>
											<?php else : ?>
												<span class="badge <?php echo $charge->isPaid() ? 'badge-success' : 'badge-danger'; ?>"><?php echo $charge->isPaid() ? 'Payée' : 'Non payée'; ?></span>
											<?php endif; ?>
										</td>
										<td>
										    <?php if($charge->getPaidBy()->getId() != 0): ?>
										    <?php if($charge->isRefunded()) : ?>
										        <span class="badge badge-success">Rembourssé</span>
										    <?php else : ?>
										        <span class="badge badge-danger">Non Rembourssé</span>
										    <?php endif; ?>
										    <?php else : ?>
										        <span class="text-muted">—</span>
										    <?php endif; ?>
										</td>
										<td>
											<?php if ($aBulletin) : ?>
												<span class="badge bg-success-light">Lié</span>
											<?php else : ?>
												<span class="text-muted">—</span>
											<?php endif; ?>
										</td>
										<td data-sort="<?= strtotime($charge->getDateCharge())?>"><?php echo normaldate($charge->getDateCharge()); ?></td>
										<td data-sort="<?= strtotime($charge->getDatePayment())?>"><?php echo normaldate($charge->getDatePayment()); ?></td>
										<td class="text-right">
										    <?php if ($_SESSION['user']->hasDroit('view', 'com_charge')) :?>
    											<?php if($charge->getPhoto() != ''): ?>
    											<a href="images/charges/<?php echo $charge->getPhoto(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Photo" data-fancybox><i class="fa fa-image"></i></a>
    											<?php endif; ?>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_charge')) :?>
												<a href="index.php?option=com_charge&task=edit&id=<?= $charge->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_charge')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $charge->getId(); ?>"><i class="far fa-trash-alt"></i></a>
										    <?php endif;?>
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
<!-- /Page Wrapper -->
<!-- ApexCharts n'est chargé globalement (includes/tpl/bottom.php) que pour le dashboard :
     com_charge en a besoin ici pour son propre graphique, indépendamment de chart-data.js. -->
<script src="assets/plugins/apexchart/apexcharts.min.js"></script>
<script type="text/javascript">
$(function () {

	// Graphique "Répartition des charges par catégorie et évolution mensuelle" — les données
	// (une matrice [année] -> {fixe, variable, hors_hw, total} sur 12 mois) sont déjà calculées
	// et injectées en PHP dans #chargeChartData, aucun appel AJAX nécessaire.
	(function () {
		var dataParAnnee = JSON.parse(document.getElementById('chargeChartData').textContent);
		var moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

		function seriesPourAnnee(annee) {
			var d = dataParAnnee[annee] || { fixe: Array(12).fill(0), variable: Array(12).fill(0), hors_hw: Array(12).fill(0) };
			return [
				{ name: 'Charge fixe', data: d.fixe },
				{ name: 'Charge variable', data: d.variable },
				{ name: 'Hors Hello World', data: d.hors_hw }
			];
		}

		function formatMontant(valeur) {
			return valeur.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';
		}

		function majBadgeEvolution(annee) {
			var $delta = $('#chargeChartDelta');
			var precedente = dataParAnnee[annee - 1];
			if (!precedente) {
				$delta.attr('class', 'charge-chart-delta flat').html('<i class="fa fa-minus"></i> Pas de données ' + (annee - 1) + ' pour comparer');
				return;
			}
			var actuel = (dataParAnnee[annee] || { total: 0 }).total;
			var totalPrecedent = precedente.total;
			var diff = actuel - totalPrecedent;
			var pourcentage = totalPrecedent !== 0 ? (diff / totalPrecedent * 100) : (actuel > 0 ? 100 : 0);
			var classe = diff > 0 ? 'up' : (diff < 0 ? 'down' : 'flat');
			var icone = diff > 0 ? 'fa-arrow-up' : (diff < 0 ? 'fa-arrow-down' : 'fa-minus');
			$delta.attr('class', 'charge-chart-delta ' + classe).html('<i class="fa ' + icone + '"></i> ' + (diff >= 0 ? '+' : '') + pourcentage.toFixed(1) + '% vs ' + (annee - 1));
		}

		var anneeInitiale = parseInt($('#chargeChartAnnee').val(), 10);

		var chart = new ApexCharts(document.querySelector('#chargeCategoryChart'), {
			chart: { type: 'bar', height: 320, stacked: true, toolbar: { show: false }, fontFamily: 'inherit', animations: { speed: 400 } },
			plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
			colors: ['#10b981', '#f59e0b', '#6b7280'],
			series: seriesPourAnnee(anneeInitiale),
			xaxis: { categories: moisLabels },
			yaxis: { labels: { formatter: function (v) { return v.toLocaleString('fr-FR') + ' DH'; } } },
			legend: { show: false },
			dataLabels: { enabled: false },
			grid: { borderColor: 'rgba(0,0,0,0.06)' },
			tooltip: { y: { formatter: formatMontant } }
		});
		chart.render();
		majBadgeEvolution(anneeInitiale);

		$('#chargeChartAnnee').on('change', function () {
			var annee = parseInt($(this).val(), 10);
			chart.updateSeries(seriesPourAnnee(annee));
			majBadgeEvolution(annee);
		});

		if (typeof gsap !== 'undefined') {
			gsap.from('.charge-chart-card', { opacity: 0, y: 20, duration: 0.6, ease: 'power2.out' });
		}
	})();

	// Table dédiée (classe distincte de ".datatable" utilisée globalement ailleurs dans
	// l'app) : la colonne 8 (Actions) n'est pas triable, tri initial par ID décroissant.
	$('.datatable-charges').DataTable({
		order: [[0, 'desc']],
		columnDefs: [{ orderable: false, targets: [8] }]
	});

	// Compteurs animés des cartes KPI (GSAP si disponible, sinon affichage statique immédiat).
	$('.charge-total-counter').each(function () {
		var $el = $(this);
		var valeurFinale = parseFloat($el.attr('data-valeur')) || 0;
		if (typeof gsap === 'undefined') {
			$el.text(valeurFinale.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
			return;
		}
		var compteur = { valeur: 0 };
		gsap.to(compteur, {
			valeur: valeurFinale,
			duration: 1.2,
			ease: 'power1.out',
			onUpdate: function () {
				$el.text(compteur.valeur.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
			}
		});
	});

	var msgsucces = "Charge supprimée avec succès";

	$(document).on( "click", ".delete", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_charge/controleurs/router.php?task=deleteCharge", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {

					$btn.closest("tr").addClass("table-danger");
					setTimeout(function () {
						$btn.closest("tr").remove()
					}, 1000);

					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})

	$(document).on( "click", ".enable", function() {
		var btn = $(this);
		var id = btn.attr("data-id");
		var state = btn.attr("data-state");
		var order = 'id=' + id + "&state=" + state;
		$.post("components/com_charge/controleurs/router.php?task=enableCharge", order, function (theResponse) {
			var error_msg = "Erreur lors de l'activation.";
			if (state === "oui") {
				error_msg = "Erreur lors de la désactivation.";
			}
			if (parseInt(theResponse) === 1) {
				if (state === "oui") {
					btn.attr("data-state", "non").removeClass("badge-success").addClass("badge-danger").attr("data-original-title", "Cliquez pour basculer").text("Non payée");
				} else {
					btn.attr("data-state", "oui").removeClass("badge-danger").addClass("badge-success").attr("data-original-title", "Cliquez pour basculer").text("Payée");
				}
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> ' + error_msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});

	// Export résultat filtre
    $(document).on("click", ".exportCharges", function() {
        event.preventDefault();
        window.open('components/com_charge/controleurs/router.php?task=exportCharges&from=' + $("#from").val() + '&to=' + $("#to").val(), '_blank');
    })
});
</script>
