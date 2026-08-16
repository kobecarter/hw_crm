<?php
/**
 * Bonus — espace employé (com_dashboard task=bonuses). Fork dédié : ne réutilise PAS
 * .../bonus/bonuses.php (partagé avec la vue admin list.php). Lecture seule — l'ajout/
 * édition/suppression reste une action admin (hasDroit('add'|'edit'|'delete',
 * 'com_resourcehumaine'), toujours false ici).
 */
$totalPrimes = 0;
$primesPrises = 0;
foreach ($bonuses as $unePrime) {
	$totalPrimes += $unePrime->getAmount();
	if ($unePrime->getStatus() == 1) {
		$primesPrises += $unePrime->getAmount();
	}
}
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-gift mr-2"></i>Mes bonus</h1>
		<p class="emp-page-subtitle"><?= count($bonuses) ?> bonus — <?= number_format($totalPrimes, 0, ',', ' ') ?> MAD au total</p>
	</div>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-coins"></i> Historique des bonus</h3>
		</div>

		<?php if (empty($bonuses)) : ?>
			<div class="emp-empty">
				<i class="fa fa-gift"></i>
				Aucun bonus enregistré pour le moment.
			</div>
		<?php else : ?>
			<div class="emp-timeline">
				<?php foreach ($bonuses as $unePrime) : ?>
					<div class="emp-timeline-item">
						<div class="emp-timeline-rail">
							<span class="emp-timeline-dot <?= $unePrime->getStatus() == 1 ? 'dot-green' : '' ?>"></span>
						</div>
						<div class="emp-timeline-content">
							<div class="emp-timeline-top">
								<span class="emp-timeline-title"><?= number_format($unePrime->getAmount(), 0, ',', ' ') ?> MAD</span>
								<span class="emp-badge <?= $unePrime->getStatus() == 1 ? 'emp-badge-green' : 'emp-badge-red' ?>"><?= $unePrime->getStatus() == 1 ? 'Pris' : 'Pas pris' ?></span>
							</div>
							<p class="emp-timeline-meta"><i class="fa fa-calendar mr-1"></i><?= date("m/Y", strtotime($unePrime->getDate())) ?></p>
							<?php if ($unePrime->getRemark()) : ?>
								<p class="emp-timeline-desc"><?= htmlspecialchars(strip_tags($unePrime->getRemark())) ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

</main>
