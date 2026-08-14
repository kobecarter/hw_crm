<?php
/**
 * Absences & congés — espace employé (com_dashboard task=absences). Fork dédié : ne réutilise
 * PAS components/com_resourcehumaine/views/absence/absences.php (partagé avec la vue admin
 * absence/list.php) pour ne jamais impacter le rendu admin. Lecture seule pour l'employé —
 * l'édition/suppression reste une action admin (hasDroit('edit'|'delete','com_resourcehumaine'),
 * toujours false pour une session self-service).
 */
$congesPris = 0;
foreach ($absences as $uneAbsence) {
	if ($uneAbsence->getNatureOfAbsence() == 1) {
		$congesPris += $uneAbsence->getNumberOfDays();
	}
}
$natureLabels = array(1 => array('Congé', 'emp-badge-purple', 'dot-teal'), 2 => array('Justifié', 'emp-badge-blue', 'dot-amber'), 3 => array('Non justifié', 'emp-badge-red', 'dot-red'));
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-umbrella-beach mr-2"></i>Absences & congés</h1>
		<p class="emp-page-subtitle"><?= count($absences) ?> enregistrement(s) — <?= $congesPris ?> jour(s) de congé pris au total</p>
	</div>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-history"></i> Historique</h3>
		</div>

		<?php if (empty($absences)) : ?>
			<div class="emp-empty">
				<i class="fa fa-check-circle"></i>
				Aucune absence enregistrée pour le moment.
			</div>
		<?php else : ?>
			<div class="emp-timeline">
				<?php foreach ($absences as $uneAbsence) :
					$nature = isset($natureLabels[$uneAbsence->getNatureOfAbsence()]) ? $natureLabels[$uneAbsence->getNatureOfAbsence()] : array('—', 'emp-badge-gray', '');
				?>
					<div class="emp-timeline-item">
						<div class="emp-timeline-rail">
							<span class="emp-timeline-dot <?= $nature[2] ?>"></span>
						</div>
						<div class="emp-timeline-content">
							<div class="emp-timeline-top">
								<span class="emp-timeline-title"><?= normaldate($uneAbsence->getStartDate()) ?> → <?= normaldate($uneAbsence->getEndDate()) ?></span>
								<span class="emp-badge <?= $nature[1] ?>"><?= $nature[0] ?></span>
							</div>
							<p class="emp-timeline-meta">
								<i class="fa fa-clock mr-1"></i><b><?= $uneAbsence->getNumberOfDays() ?></b> jour(s)
								<?php if ($uneAbsence->getBackDate()) : ?>
									 · Retour le <?= normaldate($uneAbsence->getBackDate()) ?>
								<?php endif; ?>
								<?php if ($uneAbsence->getNatureOfAbsence() != 1) : ?>
									 · <span class="emp-badge <?= $uneAbsence->getStatus() == 1 ? 'emp-badge-green' : 'emp-badge-red' ?>"><?= $uneAbsence->getStatus() == 1 ? 'Déductible' : 'Non déductible' ?></span>
								<?php endif; ?>
							</p>
							<?php if ($uneAbsence->getRemark()) : ?>
								<p class="emp-timeline-desc"><?= htmlspecialchars(strip_tags($uneAbsence->getRemark())) ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

</main>
