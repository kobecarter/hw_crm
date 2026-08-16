<?php
/**
 * Parrainage client — espace employé (com_dashboard task=parrainage). Exclusif à ce contexte.
 * La détection de correspondance (client::search, toutes agences) est purement informative :
 * le statut reste "en attente" jusqu'à validation admin (voir parrainage/controleur.php),
 * seul moment où la commission est due (montant figé, défini par l'admin sur la fiche RH).
 */
$totalCommissionValidee = parrainage::totalCommissionValidee($resourcehumaine->getId());
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-handshake mr-2"></i>Parrainage client</h1>
		<p class="emp-page-subtitle">Recommandez un client HelloWorld et cumulez une commission une fois validée par l'administration</p>
	</div>

	<div class="emp-grid emp-kpi-grid" style="margin-bottom:20px;">
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-kpi-icon kpi-2"><i class="fa fa-coins"></i></div>
			<div>
				<div class="emp-kpi-value"><?= number_format($totalCommissionValidee, 0, ',', ' ') ?> MAD</div>
				<div class="emp-kpi-label">Commission cumulée (validée)</div>
			</div>
		</div>
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-kpi-icon kpi-3"><i class="fa fa-user-friends"></i></div>
			<div>
				<div class="emp-kpi-value"><?= count($parrainages) ?></div>
				<div class="emp-kpi-label">Parrainage(s) soumis</div>
			</div>
		</div>
	</div>

	<div class="emp-card" style="margin-bottom:20px;">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-plus"></i> Recommander un client</h3>
		</div>

		<form method="post" action="components/com_resourcehumaine/controleurs/router.php?task=addParrainage" id="parrainageForm">
			<div class="emp-msgbox msgbox"></div>

			<div class="row">
				<div class="col-md-6">
					<div class="emp-form-group">
						<label class="emp-form-label">Nom <span style="color:#ef4444;">*</span></label>
						<input type="text" class="emp-input" name="nom" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="emp-form-group">
						<label class="emp-form-label">Prénom <span style="color:#ef4444;">*</span></label>
						<input type="text" class="emp-input" name="prenom" required>
					</div>
				</div>
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Email</label>
				<input type="email" class="emp-input" name="email">
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Raison sociale de l'entreprise <span style="color:#ef4444;">*</span></label>
				<input type="text" class="emp-input" name="raison_social" required>
			</div>

			<button type="submit" class="emp-submit-btn">
				<span class="spinner-border spinner-border-sm loading" style="display:none;"></span>
				<span>Envoyer la recommandation</span>
				<i class="fa fa-paper-plane"></i>
			</button>
		</form>
	</div>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-list"></i> Mes parrainages</h3>
		</div>

		<?php if (empty($parrainages)) : ?>
			<div class="emp-empty">
				<i class="fa fa-handshake"></i>
				Vous n'avez encore recommandé aucun client.
			</div>
		<?php else : ?>
			<div class="emp-timeline">
				<?php foreach ($parrainages as $unParrainage) :
					$statutMap = array(
						parrainage::STATUT_EN_ATTENTE => array('En attente', 'emp-badge-amber', 'dot-amber'),
						parrainage::STATUT_VALIDE => array('Validé', 'emp-badge-green', 'dot-green'),
						parrainage::STATUT_REFUSE => array('Refusé', 'emp-badge-red', 'dot-red'),
					);
					$st = $statutMap[$unParrainage->getStatut()];
				?>
					<div class="emp-timeline-item">
						<div class="emp-timeline-rail">
							<span class="emp-timeline-dot <?= $st[2] ?>"></span>
						</div>
						<div class="emp-timeline-content">
							<div class="emp-timeline-top">
								<span class="emp-timeline-title"><?= htmlspecialchars($unParrainage->getPrenom() . ' ' . $unParrainage->getNom()) ?></span>
								<span>
									<?php if ($unParrainage->getStatut() == parrainage::STATUT_VALIDE) : ?>
										<span class="emp-badge emp-badge-purple"><?= number_format($unParrainage->getMontantCommission(), 0, ',', ' ') ?> MAD</span>
									<?php endif; ?>
									<span class="emp-badge <?= $st[1] ?>"><?= $st[0] ?></span>
								</span>
							</div>
							<p class="emp-timeline-meta"><i class="fa fa-building mr-1"></i><?= htmlspecialchars($unParrainage->getRaisonSocial()) ?></p>
							<?php if ($unParrainage->getClient()) : ?>
								<p class="emp-timeline-desc"><i class="fa fa-check-circle mr-1" style="color:#22c55e;"></i>Correspondance trouvée avec un client existant du CRM.</p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

</main>

<script type="text/javascript">
	$(function() {
		$('form#parrainageForm').ajaxForm({
			beforeSubmit: function() {
				$('#parrainageForm .loading').show();
			},
			success: function(theResponse) {
				$('#parrainageForm .loading').hide();
				if (parseInt(theResponse) === 1) {
					$('#parrainageForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès !</strong> Recommandation envoyée.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					setTimeout(function() { document.location = 'index.php?task=parrainage'; }, 1200);
				} else if (parseInt(theResponse) === 0) {
					$('#parrainageForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention !</strong> Veuillez remplir les champs obligatoires.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				} else {
					$('#parrainageForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> Une erreur est survenue.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				}
			}
		});
	});
</script>
