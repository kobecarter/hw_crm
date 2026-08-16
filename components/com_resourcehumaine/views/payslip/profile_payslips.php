<?php
/**
 * Bulletins de paie — espace employé (com_dashboard task=payslips). Fork dédié : ne réutilise
 * PAS .../payslip/payslips.php (partagé avec la vue admin list.php, dont les pilules "mois
 * manquant" ouvrent un formulaire d'ajout admin-only — hasDroit('add','com_resourcehumaine'),
 * toujours false ici). Côté employé la bannière reste donc purement informative (pas
 * d'ajout possible depuis cet espace), et il n'y a ni édition ni suppression (admin-only).
 */
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-file-invoice-dollar mr-2"></i>Bulletins de paie</h1>
		<p class="emp-page-subtitle"><?= count($payslips) ?> bulletin(s) disponible(s)</p>
	</div>

	<?php if (!empty($moisManquants)) : ?>
		<div class="emp-card emp-missing-banner">
			<div class="emp-missing-banner-title">
				<i class="fa fa-exclamation-triangle"></i>
				<?= count($moisManquants) ?> bulletin(s) manquant(s) depuis la signature de votre contrat
			</div>
			<div class="emp-missing-pills">
				<?php foreach ($moisManquants as $m) : ?>
					<span class="emp-missing-pill" style="cursor:default;"><i class="fa fa-clock mr-1"></i><?= htmlspecialchars($m['label']) ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-money-check-alt"></i> Vos bulletins</h3>
		</div>

		<?php if (empty($payslips)) : ?>
			<div class="emp-empty">
				<i class="fa fa-file-invoice-dollar"></i>
				Aucun bulletin de paie disponible pour le moment.
			</div>
		<?php else : ?>
			<div class="emp-grid emp-file-grid">
				<?php foreach ($payslips as $unBulletin) : ?>
					<div class="emp-card emp-card-tilt emp-file-card">
						<div class="emp-file-top">
							<div class="emp-file-icon"><i class="far fa-file-pdf"></i></div>
							<div class="emp-file-title"><?= htmlspecialchars($unBulletin->getTitle()) ?></div>
						</div>
						<span class="emp-badge emp-badge-purple" style="align-self:flex-start;"><?= date("m/Y", strtotime($unBulletin->getDate())) ?></span>
						<div class="emp-file-actions">
							<a href="./images/resourceshumaines/payslips/<?= htmlspecialchars($unBulletin->getFile()) ?>" target="_blank" class="emp-btn-mini" title="Ouvrir"><i class="fa fa-eye"></i></a>
							<a href="./images/resourceshumaines/payslips/<?= htmlspecialchars($unBulletin->getFile()) ?>" download class="emp-btn-mini" title="Télécharger"><i class="fa fa-download"></i></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

</main>
