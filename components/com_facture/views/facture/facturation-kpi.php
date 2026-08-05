<?php
/* Cartes KPI pour les onglets Devis/Contrats/Factures/Paiements de la page
   Facturation (option=com_facture&task=devis|contract|facture|paiement) :
   total clients, total factures, chiffre d'affaires (MAD pour les agences du
   Maroc, AED pour Dubai) et nouvelles factures ce mois. Nécessite $clients,
   $factures, $agence et $_SESSION['agence'] en entrée. */
$facturationDevise = (stripos($agence->getNom(), 'dubai') !== false) ? 'AED' : 'DH';
$facturationDeviseLabel = $facturationDevise === 'AED' ? 'AED' : 'MAD';
$totalClientsKpi = sizeof($clients);
$totalFacturesKpi = sizeof($factures);
$chiffreAffairesKpi = payment::total(date('Y'), false, $facturationDevise, $_SESSION['agence']);
$newFacturesThisMonth = 0;
foreach ($factures as $f) {
	if (date('Y-m', strtotime($f->getDateFacture())) == date('Y-m')) {
		$newFacturesThisMonth++;
	}
}
?>
<!-- Statistiques rapides (facturation) -->
<div class="row client-stats-row">
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-2"><i class="fa fa-users"></i></span>
					<div class="dash-count">
						<div class="dash-title">Total clients</div>
						<div class="dash-counts"><p><?php echo $totalClientsKpi; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-3"><i class="fa fa-file-invoice"></i></span>
					<div class="dash-count">
						<div class="dash-title">Total factures</div>
						<div class="dash-counts"><p><?php echo $totalFacturesKpi; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-4"><i class="fa fa-money-bill-wave"></i></span>
					<div class="dash-count">
						<div class="dash-title">Chiffre d'affaires <?php echo date('Y'); ?></div>
						<div class="dash-counts"><p><?php echo number_format($chiffreAffairesKpi, 2, ',', ' ') . ' ' . $facturationDeviseLabel; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-1"><i class="fa fa-file-invoice-dollar"></i></span>
					<div class="dash-count">
						<div class="dash-title">Nouvelles factures ce mois</div>
						<div class="dash-counts"><p><?php echo $newFacturesThisMonth; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Statistiques rapides (facturation) -->
