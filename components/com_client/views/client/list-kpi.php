<?php
/* Cartes KPI de la liste des clients : extrait de list-content.php pour pouvoir
   être positionné AVANT la barre d'onglets (Clients/Devis/Contrats/Factures/
   Paiements) sur la page Facturation, comme sur la page Clients autonome où ces
   cartes sont la première chose visible sous l'en-tête de page. Nécessite
   $clients en entrée. */
$totalClientsCount = sizeof($clients);
$activeClientsCount = 0;
$newClientsThisMonth = 0;
foreach ($clients as $c) {
	if ($c->isActive()) {
		$activeClientsCount++;
	}
	if (date('Y-m', strtotime($c->getDateAdd())) == date('Y-m')) {
		$newClientsThisMonth++;
	}
}
$inactiveClientsCount = $totalClientsCount - $activeClientsCount;
?>
<!-- Statistiques rapides -->
<div class="row client-stats-row">
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-2"><i class="fa fa-users"></i></span>
					<div class="dash-count">
						<div class="dash-title">Total clients</div>
						<div class="dash-counts"><p><?php echo $totalClientsCount; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-3"><i class="fa fa-user-check"></i></span>
					<div class="dash-count">
						<div class="dash-title">Actifs</div>
						<div class="dash-counts"><p><?php echo $activeClientsCount; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-1"><i class="fa fa-user-slash"></i></span>
					<div class="dash-count">
						<div class="dash-title">Inactifs</div>
						<div class="dash-counts"><p><?php echo $inactiveClientsCount; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12 d-flex">
		<div class="card flex-fill">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-4"><i class="fa fa-user-plus"></i></span>
					<div class="dash-count">
						<div class="dash-title">Nouveaux ce mois</div>
						<div class="dash-counts"><p><?php echo $newClientsThisMonth; ?></p></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Statistiques rapides -->
