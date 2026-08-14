<?php
/**
 * Demandes — espace employé (com_dashboard task=requests). Fork dédié : ne réutilise PAS
 * .../request/requests.php (partagé avec la vue admin list.php, dont les actions
 * approuver/refuser/répondre sont admin-only). Le formulaire, auparavant inclus depuis
 * l'ancien request/form.php (supprimé, exclusif à cette vue), est désormais inline
 * ci-dessous. Contrôle serveur inchangé : editRequest/deleteRequest vérifient déjà côté
 * routeur que l'employé n'agit que sur ses propres demandes.
 */
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-comments mr-2"></i>Mes demandes</h1>
		<p class="emp-page-subtitle">Adressez une demande aux ressources humaines et suivez son statut</p>
	</div>

	<div class="emp-card" style="margin-bottom:20px;">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="<?= isset($request) ? 'fa fa-pen' : 'fa fa-plus' ?>"></i> <?= isset($request) ? 'Modifier la demande' : 'Nouvelle demande' ?></h3>
		</div>

		<form method="post" action="<?= isset($request) ? $action2 : $action1; ?>" enctype="multipart/form-data" id="reQuestForm">
			<div class="emp-msgbox msgbox"></div>

			<div class="emp-form-group">
				<label class="emp-form-label">Type de demande <span style="color:#ef4444;">*</span></label>
				<select name="type" class="emp-input" required>
					<?php foreach (request::$typesLabels as $cle => $libelle) : ?>
						<option value="<?= $cle ?>" <?= isset($request) && $request->getType() == $cle ? 'selected' : '' ?>><?= $libelle ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Titre <span style="color:#ef4444;">*</span></label>
				<input type="text" class="emp-input" name="title" value="<?= isset($request) ? htmlspecialchars($request->getTitle()) : '' ?>" required>
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Description <span style="color:#ef4444;">*</span></label>
				<textarea name="description" class="emp-textarea" required><?= isset($request) ? htmlspecialchars($request->getDescription()) : '' ?></textarea>
			</div>

			<input type="hidden" name="id_resourcehumaine" value="<?= $resourcehumaine->getId() ?>">
			<?php if (isset($request)) : ?>
				<input type="hidden" name="id" value="<?= $request->getId(); ?>">
			<?php endif; ?>

			<button type="submit" class="emp-submit-btn">
				<span class="spinner-border spinner-border-sm loading" style="display:none;"></span>
				<span><?= isset($request) ? 'Modifier' : 'Envoyer la demande' ?></span>
				<i class="fa fa-paper-plane"></i>
			</button>
			<?php if (isset($request)) : ?>
				<a href="index.php?task=requests" class="emp-btn-mini" style="width:auto;padding:0 16px;height:44px;margin-left:8px;">Annuler</a>
			<?php endif; ?>
		</form>
	</div>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-list"></i> Historique</h3>
		</div>

		<?php if (empty($requests)) : ?>
			<div class="emp-empty">
				<i class="fa fa-comments"></i>
				Vous n'avez encore envoyé aucune demande.
			</div>
		<?php else : ?>
			<div class="emp-timeline">
				<?php foreach ($requests as $uneDemande) :
					$statusMap = array(0 => array('En attente', 'emp-badge-amber', 'dot-amber'), 1 => array('Approuvé', 'emp-badge-green', 'dot-green'), 2 => array('Refusé', 'emp-badge-red', 'dot-red'));
					$st = isset($statusMap[$uneDemande->getStatus()]) ? $statusMap[$uneDemande->getStatus()] : array('—', 'emp-badge-gray', '');
					$typeBadgeMap = array('conge' => 'emp-badge-purple', 'absence' => 'emp-badge-blue', 'formation' => 'emp-badge-teal', 'autre' => 'emp-badge-gray');
				?>
					<div class="emp-timeline-item">
						<div class="emp-timeline-rail">
							<span class="emp-timeline-dot <?= $st[2] ?>"></span>
						</div>
						<div class="emp-timeline-content">
							<div class="emp-timeline-top">
								<span class="emp-timeline-title"><?= htmlspecialchars($uneDemande->getTitle()) ?></span>
								<span>
									<span class="emp-badge <?= isset($typeBadgeMap[$uneDemande->getType()]) ? $typeBadgeMap[$uneDemande->getType()] : 'emp-badge-gray' ?>"><?= htmlspecialchars($uneDemande->getTypeLabel()) ?></span>
									<span class="emp-badge <?= $st[1] ?>"><?= $st[0] ?></span>
								</span>
							</div>
							<p class="emp-timeline-desc"><?= htmlspecialchars($uneDemande->getDescription()) ?></p>
							<?php if ($uneDemande->getResponse()) : ?>
								<p class="emp-timeline-desc" style="margin-top:8px;padding-top:8px;border-top:1px dashed var(--emp-glass-border);">
									<i class="fa fa-reply mr-1"></i><b>Réponse RH :</b> <?= htmlspecialchars($uneDemande->getResponse()) ?>
								</p>
							<?php endif; ?>
							<?php if ($uneDemande->getStatus() == 0) : ?>
								<div class="emp-timeline-actions">
									<a href="index.php?task=requests&id_request=<?= $uneDemande->getId(); ?>" class="emp-btn-mini" title="Modifier"><i class="fa fa-pencil-alt"></i></a>
									<a href="javascript:void(0);" class="emp-btn-mini danger delete-request" data-id="<?= $uneDemande->getId(); ?>" title="Supprimer"><i class="fa fa-trash"></i></a>
								</div>
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
		$(document).on("click", ".delete-request", function(event) {
			event.preventDefault();
			var $item = $(this).closest('.emp-timeline-item');
			if (confirm("Supprimer cette demande ?")) {
				var id = $(this).attr("data-id");
				$.post("components/com_resourcehumaine/controleurs/router.php?task=deleteRequest", { id: id }, function(theResponse) {
					if (parseInt(theResponse) === 1) {
						$item.css('opacity', '0.3');
						setTimeout(function() { $item.remove(); }, 400);
					} else {
						alert("Erreur lors de la suppression");
					}
				});
			}
		});

		$('form#reQuestForm').ajaxForm({
			beforeSubmit: function() {
				$("#reQuestForm .loading").show();
			},
			success: function(theResponse) {
				$("#reQuestForm .loading").hide();
				var isEdit = <?= isset($request) ? 'true' : 'false' ?>;
				if (parseInt(theResponse) === 1) {
					$('#reQuestForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès !</strong> Demande ' + (isEdit ? 'modifiée' : 'envoyée') + ' avec succès.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					setTimeout(function() { document.location = "index.php?task=requests"; }, 1200);
				} else if (parseInt(theResponse) === 0) {
					$('#reQuestForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention !</strong> Veuillez remplir les champs obligatoires.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				} else {
					$('#reQuestForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> Une erreur est survenue.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				}
			}
		});
	});
</script>
