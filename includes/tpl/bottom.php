		</div>
		<!-- /Main Wrapper -->

		<?php if (isset($GLOBALS['alertesUrgentes'])) :
			// Contenu calculé dans includes/tpl/notification.php (getAlertesUrgentes), rendu ici (hors
			// du <ul> du bandeau haut, une modale ne pouvant pas être un enfant direct de <ul>).
			//
			// Repasse sur les groupes une seule fois ici pour : (1) trier les items de chaque groupe
			// "danger" d'abord, puis - à urgence égale - la plus RÉCEMMENT ajoutée en premier (jamais
			// une urgence enterrée sous des warnings, et une alerte toute neuve - nouvelle demande RH,
			// nouvelle relance... - ressort juste après les "danger" plutôt que perdue sous des
			// warnings plus anciens), (2) trier les groupes eux-mêmes "contient une urgence" en premier
			// (mêmes raisons, à l'échelle du panneau entier - l'utilisateur ne doit jamais avoir à
			// faire défiler pour tomber sur un groupe critique), (3) calculer les totaux globaux
			// (résumé + puce "Tout") une seule fois, réutilisés à la fois par la barre de filtres et
			// par le corps.
			$totalAlertes = 0;
			$totalDanger = 0;
			$groupesAlertes = $GLOBALS['alertesUrgentes'];
			foreach ($groupesAlertes as $cle => &$groupeRef) {
				usort($groupeRef['items'], function ($a, $b) {
					$compareUrgence = ($a['urgence'] === 'danger' ? 0 : 1) <=> ($b['urgence'] === 'danger' ? 0 : 1);
					if ($compareUrgence !== 0) {
						return $compareUrgence;
					}
					// Date manquante/invalide (strtotime() renvoie false) traitée comme "la plus
					// ancienne possible" (0) - jamais prioritaire par accident sur une vraie date.
					$dateA = !empty($a['date_add']) ? strtotime($a['date_add']) : 0;
					$dateB = !empty($b['date_add']) ? strtotime($b['date_add']) : 0;
					return $dateB <=> $dateA;
				});
				$groupeRef['a_danger'] = false;
				foreach ($groupeRef['items'] as $item) {
					$totalAlertes++;
					if ($item['urgence'] === 'danger') {
						$totalDanger++;
						$groupeRef['a_danger'] = true;
					}
				}
			}
			unset($groupeRef);
			uasort($groupesAlertes, function ($a, $b) {
				return ($a['a_danger'] ? 0 : 1) <=> ($b['a_danger'] ? 0 : 1);
			});
		?>
		<!-- Centre d'alertes unifié ("Rappels(Urgent)") : recherche + filtres par catégorie (barre de
		     puces horizontale, glass) au-dessus d'une liste de groupes repliables - pensé pour ne
		     jamais perdre une alerte dans la masse quand plusieurs catégories sont actives à la fois
		     (recherche/puces filtrent en JS pur, aucune donnée n'est jamais retirée du DOM). -->
		<div class="modal fade alert-center-modal" id="alertCenterModal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header alert-center-header">
						<div class="alert-center-header-top">
							<h5 class="modal-title"><i class="fa fa-bell mr-2"></i>Centre d'alertes</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<?php if (!empty($groupesAlertes)) : ?>
						<p class="alert-center-summary">
							<strong><?= $totalAlertes ?></strong> alerte<?= $totalAlertes > 1 ? 's' : '' ?>
							<?php if ($totalDanger > 0) : ?>
								<span class="alert-center-summary-sep">·</span>
								<span class="alert-center-summary-danger"><strong><?= $totalDanger ?></strong> urgente<?= $totalDanger > 1 ? 's' : '' ?></span>
							<?php else : ?>
								<span class="alert-center-summary-sep">·</span> rien d'urgent pour l'instant
							<?php endif; ?>
						</p>
						<div class="alert-center-search">
							<i class="fa fa-search"></i>
							<input type="text" id="alertCenterSearch" placeholder="Rechercher une alerte..." autocomplete="off">
						</div>
						<div class="alert-center-chips" id="alertCenterChips">
							<button type="button" class="alert-center-chip active" data-filtre="tout">
								<span>Tout</span><span class="alert-center-chip-count"><?= $totalAlertes ?></span>
							</button>
							<?php if ($totalDanger > 0) : ?>
							<button type="button" class="alert-center-chip alert-center-chip-danger" data-filtre="urgent">
								<span>🔴 Urgentes</span><span class="alert-center-chip-count"><?= $totalDanger ?></span>
							</button>
							<?php endif; ?>
							<?php foreach ($groupesAlertes as $cle => $groupe) : ?>
							<button type="button" class="alert-center-chip" data-filtre="<?= htmlspecialchars($cle) ?>" title="<?= htmlspecialchars($groupe['label']) ?>">
								<i class="fa <?= $groupe['icon'] ?>"></i><span><?= htmlspecialchars($groupe['label']) ?></span><span class="alert-center-chip-count"><?= count($groupe['items']) ?></span>
							</button>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
					<div class="modal-body alert-center-body">
						<?php if (empty($groupesAlertes)) : ?>
						<div class="alert-center-vide">
							<i class="fa fa-check-circle"></i>
							<p>Aucune alerte pour le moment — tout est à jour.</p>
						</div>
						<?php else : ?>
							<div class="alert-center-vide alert-center-vide-filtre d-none" id="alertCenterEmptyFiltre">
								<i class="fa fa-filter"></i>
								<p>Tout est calme ici pour ce filtre.</p>
							</div>
							<?php foreach ($groupesAlertes as $cle => $groupe) : ?>
							<div class="alert-center-groupe<?= $groupe['a_danger'] ? ' alert-center-groupe-danger' : '' ?>" data-groupe="<?= htmlspecialchars($cle) ?>">
								<button type="button" class="alert-center-groupe-titre" data-toggle-groupe>
									<span class="alert-center-groupe-icon"><i class="fa <?= $groupe['icon'] ?>"></i></span>
									<span class="alert-center-groupe-label"><?= htmlspecialchars($groupe['label']) ?></span>
									<span class="alert-center-groupe-count"><?= count($groupe['items']) ?></span>
									<i class="fa fa-chevron-down alert-center-groupe-chevron"></i>
								</button>
								<div class="alert-center-liste">
									<?php foreach ($groupe['items'] as $item) : ?>
									<a href="<?= htmlspecialchars($item['url']) ?>" class="alert-center-item alert-center-item-<?= $item['urgence'] ?>" data-texte="<?= htmlspecialchars(mb_strtolower($item['titre'] . ' ' . $item['sous_titre'], 'UTF-8')) ?>">
										<span class="alert-center-item-puce"></span>
										<span class="alert-center-item-texte">
											<span class="alert-center-item-titre"><?= htmlspecialchars($item['titre']) ?></span>
											<span class="alert-center-item-sous-titre"><?= htmlspecialchars($item['sous_titre']) ?></span>
										</span>
										<i class="fa fa-chevron-right alert-center-item-fleche"></i>
									</a>
									<?php endforeach; ?>
								</div>
							</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php include("includes/tpl/floating_widgets.php"); ?>

		<!-- Bootstrap Core JS -->
		<script src="assets/js/popper.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Feather Icon JS -->
		<script src="assets/js/feather.min.js"></script>
		
		<!-- Slimscroll JS -->
		<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		
		<?php if((isset($_GET['option']) && $_GET['option'] == "com_dashboard" && !$_SESSION['user']->isResourceHumaine()) || !isset($_GET['option']) && !$_SESSION['user']->isResourceHumaine()): ?>
		<!-- Chart JS -->
		<script src="assets/plugins/apexchart/apexcharts.min.js"></script>
		<script src="assets/plugins/apexchart/chart-data.js"></script>
		<?php endif; ?>
		
		<!-- Select2 JS -->
		<script src="assets/plugins/select2/js/select2.min.js"></script>

		<!-- Chosen -->
		<script src="assets/plugins/chosen/chosen.jquery.js"></script>

		<!-- Datatables JS -->
		<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
		<script src="assets/plugins/datatables/datatables.min.js"></script>

		<!-- Datepicker Core JS -->
		<script src="assets/plugins/moment/moment.min.js"></script>
		<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

		<?php if (!isset($_GET['option']) || in_array($_GET['option'], array('com_dashboard', 'com_holiday'))): ?>
		<!-- FullCalendar (calendrier des jours fériés) -->
		<script src="assets/plugins/fullcalendar/fullcalendar.min.js"></script>
		<?php endif; ?>

		<!-- fancyBox-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

		<!-- Custom JS -->
		<script src="<?= assetVersion('assets/js/script.js') ?>"></script>
		<script src="<?= assetVersion('assets/js/custom.js') ?>"></script>
		<script src="assets/js/ia-dropzone.js"></script>
		<script src="assets/js/ia-service-modal.js"></script>
		<script src="assets/js/ia-client-modal.js"></script>
		<script src="assets/js/ia-bank-filter.js"></script>
		<script src="<?= assetVersion('assets/js/global-search.js') ?>"></script>
		<script src="assets/js/row-highlight.js"></script>
		<script src="assets/js/theme-toggle.js"></script>

		<!-- Modern Theme (GSAP micro-interactions, surcouche additive) -->
		<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
		<script src="<?= assetVersion('assets/js/modern-theme.js') ?>"></script>

	</body>
</html>