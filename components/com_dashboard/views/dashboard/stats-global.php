<?php
// Libellés/couleurs des 5 KPI, + l'URL de la page où "aller regarder le détail" quand on clique
// dessus (Marge n'a pas de page dédiée - c'est un indicateur calculé, pas une liste de fiches).
$kpiDefs = array(
	'ca' => array('label' => "Chiffre d'affaires", 'icon' => 'fa-file-invoice-dollar', 'classe' => 'kpi-blue', 'url' => 'index.php?option=com_facture&task=facture'),
	'encaissements' => array('label' => 'Encaissements reçus', 'icon' => 'fa-hand-holding-usd', 'classe' => 'kpi-green', 'url' => 'index.php?option=com_facture&task=paiement'),
	'charges' => array('label' => 'Charges', 'icon' => 'fa-shopping-basket', 'classe' => 'kpi-red', 'url' => 'index.php?option=com_charge'),
	'creances' => array('label' => 'Créances (impayé)', 'icon' => 'fa-exclamation-triangle', 'classe' => 'kpi-orange', 'url' => 'index.php?option=com_facture&task=unpaid'),
	'marge' => array('label' => 'Marge nette', 'icon' => 'fa-chart-line', 'classe' => 'kpi-purple', 'url' => null),
);
$presets = array(
	'mois' => array('label' => 'Ce mois', 'from' => date('Y-m-01'), 'to' => date('Y-m-d')),
	'trimestre' => array('label' => 'Ce trimestre', 'from' => date('Y-m-d', strtotime('-3 months')), 'to' => date('Y-m-d')),
	'annee' => array('label' => 'Cette année', 'from' => date('Y-01-01'), 'to' => date('Y-m-d')),
	'annee_precedente' => array('label' => 'Année dernière', 'from' => date('Y-01-01', strtotime('-1 year')), 'to' => date('Y-12-31', strtotime('-1 year'))),
);
// Preset actif pour les puces de filtre : celui de l'URL, sinon "année" par défaut UNIQUEMENT si
// aucune date n'a été soumise (une plage personnalisée ne doit jamais réafficher un preset actif).
$presetActifPourChips = isset($_GET['preset']) ? $_GET['preset'] : (isset($_GET['from']) ? null : 'annee');
?>
<!-- Page Wrapper -->
<div class="page-wrapper glass-page globalstats-page">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Statistiques globales</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Statistiques globales</li>
					</ul>
					<p class="text-muted mb-0" style="font-size:0.85rem;">
						Du <?= normaldate($from) ?> au <?= normaldate($to) ?>
						— comparé <?= $modeComparaison === 'an_dernier' ? 'à la même période l\'an dernier' : 'à la période précédente' ?> (<?= normaldate($precFrom) ?> → <?= normaldate($precTo) ?>)
					</p>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<!-- Filtres : presets rapides + plage personnalisée, tout en liens/formulaire GET normaux
		     (pas d'AJAX partiel) - une seule source de vérité (l'URL), jamais d'état à moitié
		     rafraîchi sur une page censée aider à décider. -->
		<div class="row">
			<div class="col-md-12">
				<div class="card globalstats-filter-card">
					<div class="card-body">
						<div class="d-flex align-items-center flex-wrap" style="gap:0.6rem;">
							<div class="quick-filter-chips mb-0">
								<?php foreach ($presets as $cle => $p) : ?>
									<a href="index.php?option=com_dashboard&task=globalStats&preset=<?= $cle ?>&from=<?= $p['from'] ?>&to=<?= $p['to'] ?>&comparaison=<?= $modeComparaison ?>" class="<?= $presetActifPourChips === $cle ? 'active' : '' ?>"><?= $p['label'] ?></a>
								<?php endforeach; ?>
							</div>
							<form method="get" action="index.php" class="d-flex align-items-center flex-wrap ml-auto" style="gap:0.5rem;">
								<input type="hidden" name="option" value="com_dashboard">
								<input type="hidden" name="task" value="globalStats">
								<input type="hidden" name="comparaison" value="<?= $modeComparaison ?>">
								<input type="date" class="form-control form-control-sm" name="from" value="<?= $from ?>" style="width:150px;">
								<span class="text-muted">→</span>
								<input type="date" class="form-control form-control-sm" name="to" value="<?= $to ?>" style="width:150px;">
								<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter mr-1"></i>Filtrer</button>
							</form>
						</div>
						<div class="d-flex align-items-center flex-wrap mt-2" style="gap:0.6rem;">
							<span class="text-muted small">Comparer à :</span>
							<div class="quick-filter-chips mb-0">
								<a href="index.php?option=com_dashboard&task=globalStats&from=<?= $from ?>&to=<?= $to ?>&comparaison=precedente" class="<?= $modeComparaison === 'precedente' ? 'active' : '' ?>">Période précédente (même durée)</a>
								<a href="index.php?option=com_dashboard&task=globalStats&from=<?= $from ?>&to=<?= $to ?>&comparaison=an_dernier" class="<?= $modeComparaison === 'an_dernier' ? 'active' : '' ?>">Même période l'an dernier</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- KPI cliquables : chaque carte mène à la page correspondante (sauf Marge, purement
		     calculée) - + une puce de tendance vs la période précédente, pour que le chiffre dise
		     tout de suite s'il s'agit d'une bonne ou d'une mauvaise nouvelle. -->
		<div class="row globalstats-kpi-row">
			<?php foreach ($kpiDefs as $cle => $def) : ?>
				<?php
				$valeur = $statsActuelles[$cle];
				$valeurPrecedente = $statsPrecedentes[$cle];
				$variation = variationPourcent($valeur, $valeurPrecedente);
				// Pour les Charges et les Créances, une hausse est une MAUVAISE nouvelle (sens inversé).
				$sensInverse = in_array($cle, array('charges', 'creances'));
				$positif = $variation === null ? null : ($sensInverse ? $variation <= 0 : $variation >= 0);
				?>
				<div class="col-xl-<?= $cle === 'marge' ? '12' : '3' ?> col-sm-6 col-12">
					<?php if ($def['url']) : ?><a href="<?= $def['url'] ?>" class="globalstats-kpi-link"><?php endif; ?>
					<div class="card globalstats-kpi-card <?= $def['classe'] ?> <?= $cle === 'marge' ? 'globalstats-kpi-marge' : '' ?>" data-toggle="tooltip" title="<?= $def['url'] ? 'Voir le détail' : 'Chiffre d\'affaires moins charges, sur la période' ?>">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon"><i class="fas <?= $def['icon'] ?>"></i></span>
								<div class="dash-count">
									<div class="dash-title"><?= $def['label'] ?></div>
									<div class="dash-counts money-sensitive">
										<p><?= number_format($valeur, 0, ',', ' ') ?> <span class="globalstats-devise">DH</span></p>
									</div>
								</div>
								<?php if ($variation !== null) : ?>
									<span class="globalstats-trend <?= $positif ? 'globalstats-trend-up' : 'globalstats-trend-down' ?>">
										<i class="fas fa-arrow-<?= $variation >= 0 ? 'up' : 'down' ?>"></i>
										<?= number_format(abs($variation), 1, ',', ' ') ?>%
									</span>
								<?php endif; ?>
							</div>
							<?php if ($cle === 'marge') : ?>
								<div class="globalstats-marge-sub">
									Taux de marge : <strong><?= number_format($statsActuelles['tauxMarge'], 1, ',', ' ') ?>%</strong>
									&nbsp;·&nbsp;
									Taux de recouvrement (encaissé / facturé) : <strong><?= number_format($statsActuelles['tauxRecouvrement'], 1, ',', ' ') ?>%</strong>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php if ($def['url']) : ?></a><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Tendance : la vraie réponse à "je veux voir des évolutions qui signifient quelque
		     chose" - CA / Encaissé / Charges mois par mois sur la période, pas un simple total figé. -->
		<div class="row">
			<div class="col-xl-8 d-flex">
				<div class="card flex-fill globalstats-chart-card">
					<div class="card-header">
						<h5 class="card-title">Évolution mensuelle (DH)</h5>
					</div>
					<div class="card-body money-sensitive">
						<div id="globalstats-trend-chart"></div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 d-flex">
				<div class="card flex-fill globalstats-chart-card">
					<div class="card-header">
						<h5 class="card-title">Répartition du CA par agence</h5>
					</div>
					<div class="card-body">
						<div id="globalstats-agence-chart"></div>
						<div class="globalstats-agence-legend">
							<?php foreach ($repartitionAgences as $ra) : ?>
								<div class="globalstats-agence-legend-item">
									<span class="globalstats-agence-dot" style="background:<?= htmlspecialchars($ra['couleur']) ?>;"></span>
									<span class="globalstats-agence-nom"><?= htmlspecialchars($ra['nom']) ?></span>
									<span class="globalstats-agence-valeur money-sensitive"><?= number_format($ra['ca'], 0, ',', ' ') ?> DH</span>
								</div>
							<?php endforeach; ?>
							<?php if (empty($repartitionAgences)) : ?>
								<div class="text-muted small">Aucune donnée sur cette période.</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Marge par agence : la question "qui est rentable" plutôt que juste "qui facture le
		     plus" - un CA élevé avec des charges élevées peut valoir moins qu'un petit CA propre.
		     mt-2 en plus de la marge par défaut de .card (30px) : les deux rangées de cartes se
		     touchaient visuellement d'une carte à l'autre. -->
		<div class="row mt-2">
			<div class="col-xl-12 d-flex">
				<div class="card flex-fill globalstats-chart-card">
					<div class="card-header">
						<h5 class="card-title">Marge par agence (CA − Charges)</h5>
					</div>
					<div class="card-body money-sensitive">
						<div id="globalstats-marge-chart"></div>
					</div>
				</div>
			</div>
		</div>

		<!-- Top 10 services vendus / top 10 charges : "qu'est-ce qui rapporte" et "où part
		     l'argent", sur la même période que le reste de la page (item_facture::topServices() /
		     charge::topCharges(), includes/functions/functions.php pour tauxConversionDH()). -->
		<div class="row mt-2">
			<div class="col-xl-6 d-flex">
				<div class="card flex-fill globalstats-chart-card">
					<div class="card-header">
						<h5 class="card-title">Top 10 services vendus</h5>
					</div>
					<div class="card-body p-0 money-sensitive">
						<?php if (empty($topServices)) : ?>
							<div class="text-muted small text-center py-4">Aucune vente sur cette période.</div>
						<?php else : ?>
							<ul class="globalstats-top-list">
								<?php $maxService = $topServices[0]['total']; ?>
								<?php foreach ($topServices as $i => $s) : ?>
									<li class="globalstats-top-item">
										<span class="globalstats-top-rank"><?= $i + 1 ?></span>
										<div class="globalstats-top-body">
											<div class="globalstats-top-row">
												<span class="globalstats-top-titre"><?= htmlspecialchars($s['titre']) ?></span>
												<span class="globalstats-top-valeur"><?= number_format($s['total'], 0, ',', ' ') ?> DH</span>
											</div>
											<div class="globalstats-top-bar-track">
												<div class="globalstats-top-bar" style="width:<?= $maxService > 0 ? round(($s['total'] / $maxService) * 100) : 0 ?>%;"></div>
											</div>
											<span class="globalstats-top-sub"><?= number_format($s['qte'], 0, ',', ' ') ?> vendu(s)</span>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="col-xl-6 d-flex">
				<div class="card flex-fill globalstats-chart-card">
					<div class="card-header">
						<h5 class="card-title">Top 10 charges</h5>
					</div>
					<div class="card-body p-0 money-sensitive">
						<?php if (empty($topCharges)) : ?>
							<div class="text-muted small text-center py-4">Aucune charge payée sur cette période.</div>
						<?php else : ?>
							<ul class="globalstats-top-list">
								<?php $maxCharge = $topCharges[0]['total']; ?>
								<?php foreach ($topCharges as $i => $c) : ?>
									<li class="globalstats-top-item">
										<span class="globalstats-top-rank globalstats-top-rank-charge"><?= $i + 1 ?></span>
										<div class="globalstats-top-body">
											<div class="globalstats-top-row">
												<span class="globalstats-top-titre"><?= htmlspecialchars($c['titre']) ?></span>
												<span class="globalstats-top-valeur"><?= number_format($c['total'], 0, ',', ' ') ?> DH</span>
											</div>
											<div class="globalstats-top-bar-track">
												<div class="globalstats-top-bar globalstats-top-bar-charge" style="width:<?= $maxCharge > 0 ? round(($c['total'] / $maxCharge) * 100) : 0 ?>%;"></div>
											</div>
											<?php if (!empty($c['type'])) : ?><span class="globalstats-top-sub"><?= htmlspecialchars($c['type']) ?></span><?php endif; ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<!-- /Page Wrapper -->

<script>
	// false (pas true) : assets/plugins/apexchart/chart-data.js a son propre bloc "if (STATS_GLOBAL)"
	// legacy qui référence ca_verse/ca_hwlabel/... (les anciennes variables de cette page, remplacées
	// ici par les graphiques ApexCharts ci-dessous) - le mettre à true ferait planter ce bloc externe
	// en cherchant des variables qui n'existent plus. false = comportement neutre, comme sur le
	// dashboard normal (components/com_dashboard/views/dashboard/list.php).
	var STATS_GLOBAL = false;
	$(function () {
		if (typeof ApexCharts === 'undefined') {
			return;
		}

		var moisLabels = <?= json_encode(array_column($tendanceMensuelle, 'label')) ?>;
		var moisCA = <?= json_encode(array_map(function ($m) { return round($m['ca'], 2); }, $tendanceMensuelle)) ?>;
		var moisEncaissements = <?= json_encode(array_map(function ($m) { return round($m['encaissements'], 2); }, $tendanceMensuelle)) ?>;
		var moisCharges = <?= json_encode(array_map(function ($m) { return round($m['charges'], 2); }, $tendanceMensuelle)) ?>;

		new ApexCharts(document.querySelector('#globalstats-trend-chart'), {
			chart: { type: 'area', height: 340, toolbar: { show: false }, fontFamily: 'inherit' },
			series: [
				{ name: "Chiffre d'affaires", data: moisCA },
				{ name: 'Encaissé', data: moisEncaissements },
				{ name: 'Charges', data: moisCharges }
			],
			xaxis: { categories: moisLabels },
			colors: ['#6366f1', '#22c55e', '#ef4444'],
			stroke: { curve: 'smooth', width: 2.5 },
			fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
			dataLabels: { enabled: false },
			legend: { position: 'top' },
			yaxis: { labels: { formatter: function (v) { return Math.round(v).toLocaleString('fr-FR') + ' DH'; } } },
			tooltip: { y: { formatter: function (v) { return Number(v).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH'; } } }
		}).render();

		var agenceNoms = <?= json_encode(array_column($repartitionAgences, 'nom')) ?>;
		var agenceCA = <?= json_encode(array_map(function ($a) { return round($a['ca'], 2); }, $repartitionAgences)) ?>;
		var agenceCouleurs = <?= json_encode(array_column($repartitionAgences, 'couleur')) ?>;
		var agenceCharges = <?= json_encode(array_map(function ($a) { return round($a['charges'], 2); }, $repartitionAgences)) ?>;
		var agenceMarges = <?= json_encode(array_map(function ($a) { return round($a['ca'] - $a['charges'], 2); }, $repartitionAgences)) ?>;

		if (agenceNoms.length) {
			new ApexCharts(document.querySelector('#globalstats-agence-chart'), {
				chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
				series: agenceCA,
				labels: agenceNoms,
				colors: agenceCouleurs,
				legend: { show: false },
				dataLabels: { enabled: true, formatter: function (v) { return v.toFixed(0) + '%'; } },
				tooltip: { y: { formatter: function (v) { return Number(v).toLocaleString('fr-FR') + ' DH'; } } }
			}).render();

			new ApexCharts(document.querySelector('#globalstats-marge-chart'), {
				chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'inherit' },
				series: [{ name: 'Marge (CA - Charges)', data: agenceMarges }],
				xaxis: { categories: agenceNoms },
				plotOptions: { bar: { borderRadius: 6, distributed: true, columnWidth: '45%' } },
				colors: agenceMarges.map(function (v) { return v >= 0 ? '#22c55e' : '#ef4444'; }),
				legend: { show: false },
				dataLabels: { enabled: true, formatter: function (v) { return Math.round(v).toLocaleString('fr-FR') + ' DH'; } },
				yaxis: { labels: { formatter: function (v) { return Math.round(v).toLocaleString('fr-FR') + ' DH'; } } }
			}).render();
		} else {
			document.querySelector('#globalstats-agence-chart').innerHTML = '<div class="text-muted small text-center py-4">Aucune donnée</div>';
			document.querySelector('#globalstats-marge-chart').innerHTML = '<div class="text-muted small text-center py-4">Aucune donnée</div>';
		}
	});
</script>
