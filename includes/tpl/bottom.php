		</div>
		<!-- /Main Wrapper -->

		<?php if (isset($GLOBALS['alertesUrgentes'])) : ?>
		<!-- Centre d'alertes unifié ("Rappels(Urgent)") - contenu calculé dans
		     includes/tpl/notification.php (getAlertesUrgentes), rendu ici (hors du <ul> du
		     bandeau haut, une modale ne pouvant pas être un enfant direct de <ul>). -->
		<div class="modal fade alert-center-modal" id="alertCenterModal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fa fa-bell mr-2"></i>Centre d'alertes</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<?php if (empty($GLOBALS['alertesUrgentes'])) : ?>
						<div class="alert-center-vide">
							<i class="fa fa-check-circle"></i>
							<p>Aucune alerte pour le moment — tout est à jour.</p>
						</div>
						<?php else : ?>
							<?php foreach ($GLOBALS['alertesUrgentes'] as $groupe) : ?>
							<div class="alert-center-groupe">
								<h6 class="alert-center-groupe-titre">
									<i class="fa <?= $groupe['icon'] ?>"></i>
									<span class="alert-center-groupe-label"><?= htmlspecialchars($groupe['label']) ?></span>
									<span class="alert-center-groupe-count"><?= count($groupe['items']) ?></span>
								</h6>
								<div class="alert-center-liste">
									<?php foreach ($groupe['items'] as $item) : ?>
									<a href="<?= htmlspecialchars($item['url']) ?>" class="alert-center-item alert-center-item-<?= $item['urgence'] ?>">
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
		<script src="assets/js/script.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/ia-dropzone.js"></script>
		<script src="assets/js/ia-service-modal.js"></script>
		<script src="assets/js/ia-client-modal.js"></script>
		<script src="assets/js/ia-bank-filter.js"></script>
		<script src="assets/js/global-search.js"></script>
		<script src="assets/js/row-highlight.js"></script>
		<script src="assets/js/theme-toggle.js"></script>

		<!-- Modern Theme (GSAP micro-interactions, surcouche additive) -->
		<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
		<script src="assets/js/modern-theme.js"></script>

	</body>
</html>