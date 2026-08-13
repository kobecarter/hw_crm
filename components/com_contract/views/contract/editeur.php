<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Éditeur de contrat</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_contract">Contrats</a></li>
						<li class="breadcrumb-item active"><?= htmlspecialchars($contract->getTitre()) ?></li>
					</ul>
				</div>
				<div class="col-auto">
					<a href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?= $contract->getId() ?>" target="_blank" class="btn btn-white mr-2"><i class="fa fa-file mr-1"></i> PDF</a>
					<a href="components/com_contract/controleurs/router.php?task=docxContract&id=<?= $contract->getId() ?>" class="btn btn-white mr-2"><i class="fa fa-file-word mr-1"></i> Word</a>
					<a href="index.php?option=com_contract&task=edit&id=<?= $contract->getId() ?>" class="btn btn-white"><i class="fa fa-cog mr-1"></i> Champs du contrat</a>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card glass-page">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<div>
								<span class="text-muted mr-2">Devis N°<?= htmlspecialchars($contract->getDevis()->getNumero()) ?></span>
								<?php
								$statutLabels = array(
									contract::STATUT_PREPARATION => array('En préparation', 'bg-warning-light'),
									contract::STATUT_ATTENTE_SIGNATURE => array('En attente de signature', 'bg-primary-light'),
									contract::STATUT_SIGNE => array('Signé', 'bg-success text-white'),
									contract::STATUT_REFUSE => array('Refusé', 'bg-danger text-white'),
								);
								$statutInfo = isset($statutLabels[$contract->getStatus()]) ? $statutLabels[$contract->getStatus()] : array('—', 'bg-secondary text-white');
								?>
								<span class="badge <?= $statutInfo[1] ?>"><?= $statutInfo[0] ?></span>
							</div>
							<div class="msgbox"></div>
							<button type="button" class="btn btn-primary" id="enregistrerContratBtn"><i class="fa fa-save mr-1"></i> Enregistrer comme brouillon</button>
						</div>

						<textarea id="corpsContratEditeur"><?= $contract->getCorpsGenere() ? $contract->getCorpsGenere() : '<p>Aucun contenu généré pour ce contrat (créé via l\'ancien formulaire) - utilisez le formulaire "Champs du contrat" ci-dessus.</p>' ?></textarea>
						<script type="text/javascript">
							CKEDITOR.replace('corpsContratEditeur', {
								allowedContent: true,
								height: 700,
								filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
							});
						</script>

						<hr class="my-4">

						<h5 class="mb-3">Contrat signé</h5>
						<?php if ($contract->getContratPDF() != '') :?>
							<p class="text-success mb-2"><i class="fa fa-check-circle mr-1"></i> Contrat signé déposé le <?= $contract->getSignedAt() ? normaldate($contract->getSignedAt()) : '—' ?> — <a href="images/contracts/<?= htmlspecialchars($contract->getContratPDF()) ?>" target="_blank">voir le PDF</a></p>
						<?php else :?>
							<p class="text-muted mb-2">Une fois le contrat signé par le client, déposez-en le PDF ici : le devis passera automatiquement au statut "Contrat signé".</p>
						<?php endif;?>
						<form id="contratSigneForm">
							<input type="hidden" name="id" value="<?= $contract->getId() ?>">
							<div class="form-group d-flex align-items-center" style="gap:10px;">
								<input type="file" class="form-control" name="signed_file[]" accept="application/pdf" style="max-width:400px;">
								<button type="submit" class="btn btn-success"><i class="fa fa-upload mr-1"></i> Marquer comme signé</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->

<!-- Fenêtre de message stylée — remplace alert() natif du navigateur (même composant que
     com_devis/views/devis/form.php, dupliqué ici car chaque page charge son propre JS). -->
<div id="messageStyleModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="charge-doublon-icon" id="messageStyleIcone"><i class="fa fa-exclamation-triangle"></i></div>
				<h5 class="modal-title mt-3" id="messageStyleTitre">Action impossible</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-0" id="messageStyleTexte" style="font-size:0.9rem;"></p>
			</div>
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
function afficherMessageStyle(texte, type) {
	type = type || 'error';
	var params = {
		error: { icone: 'fa-exclamation-triangle', classe: 'charge-doublon-icon', titre: 'Action impossible' },
		success: { icone: 'fa-check-circle', classe: 'tva-confirm-icon', titre: 'Succès' },
		info: { icone: 'fa-info-circle', classe: 'tva-confirm-icon', titre: 'Information' }
	};
	var p = params[type] || params.error;
	$('#messageStyleIcone').attr('class', p.classe).html('<i class="fa ' + p.icone + '"></i>');
	$('#messageStyleTitre').text(p.titre);
	$('#messageStyleTexte').text(texte);
	$('#messageStyleModal').modal('show');
}

$(function () {
	$('#enregistrerContratBtn').on('click', function () {
		var $btn = $(this).prop('disabled', true);
		var libelleInitial = $btn.html();
		$btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Enregistrement...');
		$.post('components/com_contract/controleurs/router.php?task=enregistrerCorpsContrat', {
			id: <?= $contract->getId() ?>,
			corps_genere: CKEDITOR.instances.corpsContratEditeur.getData()
		}, function (response) {
			if (response.success) {
				// Retour au formulaire classique (champs du contrat) - un brouillon enregistré
				// invite naturellement à revoir titre/durée/ville/tribunal, pas à rester face au
				// texte qu'on vient de sauvegarder.
				window.location.href = 'index.php?option=com_contract&task=edit&id=<?= $contract->getId() ?>';
				return;
			}
			$btn.prop('disabled', false).html(libelleInitial);
			$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show p-2 mb-0" role="alert">' + (response.message || 'Erreur lors de l\'enregistrement') + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		}, 'json');
	});

	$('#contratSigneForm').on('submit', function (e) {
		e.preventDefault();
		var $btn = $(this).find('button[type=submit]').prop('disabled', true);
		var formData = new FormData(this);
		$.ajax({
			url: 'components/com_contract/controleurs/router.php?task=marquerContratSigne',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function (response) {
				if (response.success) {
					window.location.reload();
				} else {
					$btn.prop('disabled', false);
					afficherMessageStyle(response.message || "Erreur lors de l'envoi.", 'error');
				}
			},
			error: function () {
				$btn.prop('disabled', false);
				afficherMessageStyle("Erreur lors de l'envoi.", 'error');
			}
		});
	});
});
</script>
