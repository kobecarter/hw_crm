<div class="card card-table">
	<div class="card-header">
		<h4 class="card-title">Nouvelle demande d'attestation</h4>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3 attestation-add-msgbox"></div>
		<form id="attestationAddForm">
			<input type="hidden" name="id_client" value="<?php echo $client->getId(); ?>">
			<div class="form-group">
				<label>Titre</label>
				<input type="text" class="form-control" name="titre" placeholder="Ex : Attestation de référence 2026" required>
			</div>
			<div class="form-group">
				<label>Document (PDF ou Word)</label>
				<input type="file" class="form-control" name="fichier" accept=".pdf,.doc,.docx" required>
				<small class="form-text text-muted">Le document officiel (avec en-tête, cachet, signature agence) que le client va recevoir puis signer en ligne.</small>
			</div>
			<div class="form-group">
				<label>Message pour le client (optionnel)</label>
				<textarea class="form-control" name="message" rows="3" placeholder="Ex : Merci de bien vouloir signer l'attestation ci-jointe."></textarea>
			</div>
			<button type="submit" class="btn btn-primary">Envoyer la demande au client</button>
		</form>
	</div>
</div>

<div class="card card-table mt-3">
	<div class="card-header">
		<h4 class="card-title">Attestations de ce client</h4>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3 attestation-list-msgbox"></div>
		<?php if (empty($attestations)) : ?>
			<p class="text-muted mb-0">Aucune attestation pour l'instant.</p>
		<?php else : ?>
		<div class="table-responsive list-box">
			<table class="table table-stripped table-center table-hover">
				<thead class="thead-light">
					<tr>
						<th>Titre</th>
						<th>Document</th>
						<th>Statut</th>
						<th>Comment le client a répondu</th>
						<th>Demandée le</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($attestations as $att) : ?>
						<tr data-attestation-row="<?php echo (int) $att->getId(); ?>">
							<td><?php echo htmlspecialchars($att->getTitre()); ?></td>
							<td>
								<?php if ($att->getFichier()) : ?>
									<a href="components/com_client/controleurs/router.php?task=downloadAttestationAdmin&id=<?php echo (int) $att->getId(); ?>" target="_blank"><i class="fa fa-file-text-o"></i> Voir le document</a>
								<?php else : ?>
									<span class="text-muted">Aucun fichier</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($att->getStatu() == 1) : ?>
									<span class="badge bg-success-light">Signée</span>
								<?php else : ?>
									<span class="badge bg-warning-light">En attente</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($att->getStatu() == 1) : ?>
									<span class="badge bg-success-light"><i class="fa fa-pencil-square-o"></i> Signée en ligne</span><br>
									<small class="text-muted"><?php echo htmlspecialchars($att->getSignatureNom()) . ' - ' . date('d/m/Y H:i', strtotime($att->getSignatureDate())); ?></small>
								<?php elseif ($att->getDownloadDate()) : ?>
									<span class="badge bg-info-light"><i class="fa fa-download"></i> Téléchargée (non signée)</span><br>
									<small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($att->getDownloadDate())); ?></small>
								<?php else : ?>
									<span class="text-muted">Pas encore consultée</span>
								<?php endif; ?>
							</td>
							<td><?php echo date('d/m/Y', strtotime($att->getDateAdd())); ?></td>
							<td>
								<button type="button" class="btn btn-sm btn-outline-danger attestation-delete-btn" data-id="<?php echo (int) $att->getId(); ?>" title="Supprimer cette demande"><i class="fa fa-trash"></i></button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
(function () {
	var form = document.getElementById('attestationAddForm');
	if (!form) return;
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var box = document.querySelector('.attestation-add-msgbox');
		var fileInput = form.querySelector('[name="fichier"]');
		if (!fileInput.files || !fileInput.files.length) {
			box.innerHTML = '<div class="alert alert-warning">Merci de sélectionner un document PDF ou Word.</div>';
			return;
		}
		var body = new FormData(form);
		var btn = form.querySelector('button[type="submit"]');
		btn.disabled = true;
		fetch('components/com_client/controleurs/router.php?task=addAttestation', { method: 'POST', body: body })
			.then(function (r) { return r.text(); })
			.then(function (t) {
				t = t.trim();
				if (t === '1') {
					box.innerHTML = '<div class="alert alert-success">Demande envoyée. Le client la verra dans son espace.</div>';
					setTimeout(function () { document.location.reload(); }, 1200);
				} else if (t === '3') {
					box.innerHTML = '<div class="alert alert-warning">Le fichier n\'a pas pu être enregistré : format non accepté (PDF, DOC ou DOCX uniquement) ou envoi trop volumineux.</div>';
					btn.disabled = false;
				} else {
					box.innerHTML = '<div class="alert alert-warning">Merci de remplir le titre et de joindre le document.</div>';
					btn.disabled = false;
				}
			})
			.catch(function () { box.innerHTML = '<div class="alert alert-danger">Une erreur est survenue.</div>'; btn.disabled = false; });
	});

	var listBox = document.querySelector('.attestation-list-msgbox');
	document.querySelectorAll('.attestation-delete-btn').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (!window.confirm('Supprimer définitivement cette demande d\'attestation et le document associé ?')) return;
			var id = btn.getAttribute('data-id');
			btn.disabled = true;
			var body = new URLSearchParams({ id: id }).toString();
			fetch('components/com_client/controleurs/router.php?task=deleteAttestation', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
				.then(function (r) { return r.text(); })
				.then(function (t) {
					if (t.trim() === '1') {
						var row = document.querySelector('[data-attestation-row="' + id + '"]');
						if (row) row.remove();
					} else {
						btn.disabled = false;
						if (listBox) listBox.innerHTML = '<div class="alert alert-danger">La suppression a échoué.</div>';
					}
				})
				.catch(function () { btn.disabled = false; if (listBox) listBox.innerHTML = '<div class="alert alert-danger">Une erreur est survenue.</div>'; });
		});
	});
})();
</script>
