<?php
/**
 * Fichiers — espace employé (com_dashboard task=files). Fork dédié : ne réutilise PAS
 * .../fileresourcehumaine/filesresourcehumaine.php (partagé avec la vue admin list.php).
 * L'employé peut déposer lui-même les documents manquants (task=addFileResourceHumaineSelf,
 * gated isResourceHumaine() dans le routeur) mais pas éditer/supprimer un document existant
 * (reste admin-only, hasDroit(...) toujours false ici). Un document déposé par l'employé
 * arrive "en attente de validation" (validated=0) et ne compte pas encore dans la checklist -
 * voir fileresourcehumaine::documentTypesPresents().
 */
$documentsRequisFiles = fileresourcehumaine::documentsRequis($resourcehumaine->getStatus());
$documentsManquantsFiles = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $files);
$documentsPresentsFiles = fileresourcehumaine::documentTypesPresents($files);
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-folder-open mr-2"></i>Mes fichiers</h1>
		<p class="emp-page-subtitle"><?= count($files) ?> document(s) dans votre dossier</p>
	</div>

	<div class="emp-card" style="margin-bottom:20px;">
		<div class="emp-compliance-banner <?= empty($documentsManquantsFiles) ? 'is-ok' : 'is-missing' ?>">
			<i class="fa <?= empty($documentsManquantsFiles) ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
			<span>
				<?php if (empty($documentsManquantsFiles)) : ?>
					Dossier complet — tous les documents requis (statut « <?= htmlspecialchars($resourcehumaine->getStatus()) ?> ») sont fournis.
				<?php else : ?>
					Il manque <?= count($documentsManquantsFiles) ?> document(s) obligatoire(s) : <?= htmlspecialchars(implode(', ', $documentsManquantsFiles)) ?>.
				<?php endif; ?>
			</span>
		</div>
		<div class="emp-doc-checklist">
			<?php foreach ($documentsRequisFiles as $cle => $libelle) :
				$dejaEnAttente = false;
				foreach ($files as $f) {
					if ($f->getDocumentType() === $cle && !$f->getValidated()) {
						$dejaEnAttente = true;
					}
				}
			?>
				<div class="emp-doc-item <?= isset($documentsPresentsFiles[$cle]) ? 'ok' : 'missing' ?>" style="justify-content:space-between;">
					<span style="display:flex;align-items:center;gap:8px;">
						<i class="fa <?= isset($documentsPresentsFiles[$cle]) ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
						<span><?= htmlspecialchars($libelle) ?></span>
					</span>
					<?php if (!isset($documentsPresentsFiles[$cle])) : ?>
						<?php if ($dejaEnAttente) : ?>
							<span class="emp-badge emp-badge-amber">En attente</span>
						<?php else : ?>
							<button type="button" class="emp-btn-mini upload-doc-toggle" data-doctype="<?= htmlspecialchars($cle) ?>" data-doclabel="<?= htmlspecialchars($libelle) ?>" title="Ajouter ce document"><i class="fa fa-plus"></i></button>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<form method="post" action="components/com_resourcehumaine/controleurs/router.php?task=addFileResourceHumaineSelf" enctype="multipart/form-data" id="uploadDocForm" style="display:none;margin-top:16px;padding-top:16px;border-top:1px dashed var(--emp-glass-border);">
			<div class="emp-msgbox msgbox"></div>
			<p style="font-size:.85rem;font-weight:700;color:var(--emp-ink);margin-bottom:10px;">Ajouter : <span id="uploadDocLabel"></span></p>
			<input type="hidden" name="document_type" id="uploadDocType">
			<div class="emp-form-group">
				<input type="file" name="file[]" class="emp-input" required accept=".pdf,.jpg,.jpeg,.png,.webp">
			</div>
			<button type="submit" class="emp-submit-btn">
				<span class="spinner-border spinner-border-sm loading" style="display:none;"></span>
				<span>Envoyer</span>
			</button>
			<button type="button" class="emp-btn-mini" id="uploadDocCancel" style="width:auto;padding:0 16px;height:44px;margin-left:8px;">Annuler</button>
		</form>
	</div>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-file-alt"></i> Documents</h3>
		</div>

		<?php if (empty($files)) : ?>
			<div class="emp-empty">
				<i class="fa fa-folder-open"></i>
				Aucun fichier n'a encore été ajouté à votre dossier.
			</div>
		<?php else : ?>
			<div class="emp-grid emp-file-grid">
				<?php foreach ($files as $unFichier) : ?>
					<div class="emp-card emp-card-tilt emp-file-card">
						<div class="emp-file-top">
							<div class="emp-file-icon"><i class="far fa-file-pdf"></i></div>
							<div class="emp-file-title"><?= htmlspecialchars($unFichier->getTitle()) ?></div>
						</div>
						<?php if ($unFichier->getDocumentType() && isset($documentsRequisFiles[$unFichier->getDocumentType()])) : ?>
							<span class="emp-badge emp-badge-teal" style="align-self:flex-start;"><?= htmlspecialchars($documentsRequisFiles[$unFichier->getDocumentType()]) ?></span>
						<?php endif; ?>
						<?php if (!$unFichier->getValidated()) : ?>
							<span class="emp-badge emp-badge-amber" style="align-self:flex-start;"><i class="fa fa-clock mr-1"></i>En attente de validation</span>
						<?php endif; ?>
						<div class="emp-file-actions">
							<a href="./images/resourceshumaines/files/<?= htmlspecialchars($unFichier->getFile()) ?>" target="_blank" class="emp-btn-mini" title="Ouvrir"><i class="fa fa-eye"></i></a>
							<a href="./images/resourceshumaines/files/<?= htmlspecialchars($unFichier->getFile()) ?>" download class="emp-btn-mini" title="Télécharger"><i class="fa fa-download"></i></a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

</main>

<script type="text/javascript">
	$(function() {
		$(document).on('click', '.upload-doc-toggle', function() {
			var type = $(this).data('doctype');
			var label = $(this).data('doclabel');
			$('#uploadDocType').val(type);
			$('#uploadDocLabel').text(label);
			$('#uploadDocForm').slideDown(200);
			$('html, body').animate({ scrollTop: $('#uploadDocForm').offset().top - 100 }, 300);
		});
		$('#uploadDocCancel').on('click', function() {
			$('#uploadDocForm').slideUp(200);
		});

		$('form#uploadDocForm').ajaxForm({
			beforeSubmit: function() {
				$('#uploadDocForm .loading').show();
			},
			success: function(theResponse) {
				$('#uploadDocForm .loading').hide();
				if (parseInt(theResponse) === 1) {
					$('#uploadDocForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Envoyé !</strong> Document en attente de validation.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					setTimeout(function() { document.location = 'index.php?task=files'; }, 1200);
				} else {
					$('#uploadDocForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> Veuillez sélectionner un fichier.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				}
			}
		});
	});
</script>
