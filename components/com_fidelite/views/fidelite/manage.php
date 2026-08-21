<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Fidélité · <a href="index.php?option=com_client&task=showDetails&id=<?php echo $client->getId(); ?>"><?php echo $client->getNom() . " " . $client->getPrenom(); ?></a></h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_fidelite">Espace Fidélité</a></li>
						<li class="breadcrumb-item active"><?php echo $client->getNom() . " " . $client->getPrenom(); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="card">
					<div class="card-body text-center">
						<h1 class="mb-0"><?php echo (int) $total; ?></h1>
						<p class="text-muted mb-4">points actuels · <a href="index.php?option=com_client&task=showDetails&id=<?php echo $client->getId(); ?>">voir la fiche client</a></p>

						<form method="post" id="fideliteForm">
							<div class="msgbox text-left"></div>
							<input type="hidden" name="id_client" value="<?php echo $client->getId(); ?>">
							<div class="form-group text-left">
								<label>Points (positif = crédit, négatif = retrait)</label>
								<input type="number" class="form-control" name="points" placeholder="ex: 5 ou -5" required>
							</div>
							<div class="form-group text-left">
								<label>Motif <small class="text-muted">(visible dans l'historique)</small></label>
								<input type="text" class="form-control" name="libelle" placeholder="ex: Geste commercial">
							</div>
							<button type="submit" class="btn btn-primary btn-block"><span class="spinner-border spinner-border-sm mr-2 loading" style="display:none"></span> Valider</button>
						</form>
					</div>
				</div>

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Récompenses débloquées</h4>
					</div>
					<div class="card-body">
						<div class="msgbox mb-3"></div>
						<?php if (count($rewards) === 0) : ?>
						<p class="text-muted mb-0">Aucun palier atteint pour l'instant (10 / 20 / 50 / 100 points).</p>
						<?php else : ?>
						<ul class="list-group" id="fideliteRewardsList">
							<?php foreach ($rewards as $r) : ?>
							<li class="list-group-item d-flex justify-content-between align-items-center px-0">
								<div>
									<b><?php echo (int) $r['seuil']; ?> pts</b> — <?php echo htmlspecialchars($r['libelle']); ?>
									<?php if ((int) $r['statut'] === 1) : ?>
									<br><small class="text-success">Donnée le <?php echo date("d/m/Y", strtotime($r['date_affecte'])); ?><?php if (!empty($r['affecte_par'])) : ?> par <?php echo htmlspecialchars($r['affecte_par']); ?><?php endif; ?></small>
									<?php else : ?>
									<br><small class="text-warning">En attente</small>
									<?php endif; ?>
								</div>
								<?php if ((int) $r['statut'] === 0) : ?>
								<button type="button" class="btn btn-sm btn-success mark-reward-given" data-id="<?php echo (int) $r['id']; ?>">Marquer comme donnée</button>
								<?php endif; ?>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Historique des points</h4>
					</div>
					<div class="card-body">
						<div class="msgbox mb-3"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center">
								<thead class="thead-light">
									<tr>
										<th>Date</th>
										<th>Type</th>
										<th>Motif</th>
										<th class="text-right">Points</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody id="fideliteHistoryBody">
									<?php if (count($history) === 0) : ?>
									<tr><td colspan="5" class="text-center text-muted">Aucun point pour l'instant.</td></tr>
									<?php else : foreach ($history as $h) : ?>
									<tr>
										<td><?php echo date("d/m/Y H:i", strtotime($h['date_add'])); ?></td>
										<td><?php echo htmlspecialchars($h['type']); ?></td>
										<td><?php echo htmlspecialchars($h['libelle']); ?></td>
										<td class="text-right"><b class="<?php echo ((int) $h['points'] < 0) ? 'text-danger' : 'text-success'; ?>"><?php echo ((int) $h['points'] > 0 ? '+' : '') . (int) $h['points']; ?></b></td>
										<td class="text-right">
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_fidelite')) : ?>
											<a href="javascript:void(0);" class="btn btn-sm btn-white text-danger delete-point" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?php echo (int) $h['id']; ?>"><i class="far fa-trash-alt"></i></a>
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Filleuls recommandés</h4>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-stripped table-center">
								<thead class="thead-light">
									<tr>
										<th>Filleul</th>
										<th>Entreprise</th>
										<th>Email</th>
										<th>Statut</th>
										<th>Date</th>
									</tr>
								</thead>
								<tbody>
									<?php if (count($parrainages) === 0) : ?>
									<tr><td colspan="5" class="text-center text-muted">Aucun filleul recommandé pour l'instant.</td></tr>
									<?php else :
										$statutLabels = array(0 => 'En attente', 1 => 'Contacté', 2 => 'Converti', 3 => 'Clôturé');
										foreach ($parrainages as $p) : ?>
									<tr>
										<td><?php echo htmlspecialchars($p['filleul_nom']); ?></td>
										<td><?php echo htmlspecialchars($p['filleul_entreprise']); ?></td>
										<td><?php echo htmlspecialchars($p['filleul_email']); ?></td>
										<td><?php echo isset($statutLabels[(int) $p['statut']]) ? $statutLabels[(int) $p['statut']] : $p['statut']; ?></td>
										<td><?php echo date("d/m/Y", strtotime($p['date_add'])); ?></td>
									</tr>
									<?php endforeach; endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->

<script>
$(function () {
	$('#fideliteForm').on('submit', function (e) {
		e.preventDefault();
		var $form = $(this);
		var $box = $form.find('.msgbox');
		$form.find('.loading').show();
		$.post('components/com_fidelite/controleurs/router.php?task=addManualPoints', $form.serialize(), function (theResponse) {
			$form.find('.loading').hide();
			if (parseInt(theResponse) === 1) {
				$box.html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès!</strong> Points enregistrés.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				setTimeout(function () { document.location.reload(); }, 1200);
			} else if (parseInt(theResponse) === 0) {
				$box.html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			} else {
				$box.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Une erreur est survenue.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			}
		});
	});

	$(document).on('click', '.mark-reward-given', function () {
		var $btn = $(this);
		var $box = $btn.closest('.card-body').find('.msgbox');
		var id = $btn.attr('data-id');
		$btn.prop('disabled', true);
		$.post('components/com_fidelite/controleurs/router.php?task=markRewardGiven', { id: id }, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				$box.html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès!</strong> Récompense marquée comme donnée — les points correspondants ont été déduits du solde du client, qui recevra une notification.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				setTimeout(function () { document.location.reload(); }, 1400);
			} else {
				$btn.prop('disabled', false);
				$box.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> La mise à jour a échoué.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			}
		});
	});

	$(document).on('click', '.delete-point', function () {
		var $btn = $(this);
		var $box = $btn.closest('.card-body').find('.msgbox');
		if (!confirm("Supprimer cette ligne de l'historique des points ?")) return;
		var id = $btn.attr('data-id');
		$.post('components/com_fidelite/controleurs/router.php?task=deletePoint', { id: id }, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				$btn.closest('tr').addClass('table-danger');
				setTimeout(function () { document.location.reload(); }, 700);
			} else {
				$box.html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> La suppression a échoué.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			}
		});
	});
});
</script>
