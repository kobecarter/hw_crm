<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Caisse noire</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Caisse noire</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_caissenoire')) : ?>
				<div class="col-auto">
					<a href="index.php?option=com_caissenoire&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter">
						<i class="fas fa-plus"></i>
					</a>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
		// KPI en tête de page, calculés directement à partir de $entrees déjà chargé (pas de
		// requête SQL supplémentaire) - même approche que com_charge/views/charge/list.php.
		$totalAvance = 0;
		$totalRembourse = 0;
		foreach ($entrees as $e) {
			$totalAvance += (float) $e->getMontant();
			if ($e->isRefunded()) {
				$totalRembourse += (float) $e->getMontant();
			}
		}
		$totalResteARembourser = $totalAvance - $totalRembourse;
		?>

		<div class="row mb-4">
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-9"><i class="fa fa-wallet"></i></span>
							<div class="dash-count">
								<div class="dash-title">Total avancé (DH)</div>
								<div class="dash-counts"><p><span id="kpiCaisseTotal" class="caisse-total-counter" data-valeur="<?= $totalAvance ?>">0</span> DH</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-check"></i></span>
							<div class="dash-count">
								<div class="dash-title">Remboursé (DH)</div>
								<div class="dash-counts"><p><span id="kpiCaisseRembourse" class="caisse-total-counter" data-valeur="<?= $totalRembourse ?>">0</span> DH</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1"><i class="fa fa-exclamation-triangle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Reste à rembourser (DH)</div>
								<div class="dash-counts"><p><span id="kpiCaisseReste" class="caisse-total-counter" data-valeur="<?= $totalResteARembourser ?>">0</span> DH</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des entrées</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 msgbox"></div>

						<div id="filter_inputs_caissenoire" class="card filter-card px-4 m-4">
							<div class="card-body pb-0">
								<form method="post" action="" id="filterCaisseNoire">
									<div class="row">
										<div class="col-sm-6 col-md-2">
											<div class="form-group">
												<label>Date début</label>
												<div class="cal-icon">
													<input type="text" class="form-control datetimepicker" name="from" id="caisseFrom">
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-2">
											<div class="form-group">
												<label>Date fin</label>
												<div class="cal-icon">
													<input type="text" class="form-control datetimepicker" name="to" id="caisseTo">
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-3">
											<div class="form-group">
												<label>Payé par</label>
												<select class="form-control" id="caisseUtilisateur">
													<option value="">Tous</option>
													<?php foreach (CAISSENOIRE_USERS_AUTORISES as $idUtilisateurAutorise) : $utilisateurFiltre = user::find($idUtilisateurAutorise); ?>
													<option value="<?= $utilisateurFiltre->getId() ?>"><?= $utilisateurFiltre->getPrenom() . ' ' . $utilisateurFiltre->getNom() ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
										<div class="col-sm-6 col-md-3">
											<div class="form-group">
												<label>Statut</label>
												<select class="form-control" id="caisseStatut">
													<option value="">Tous</option>
													<option value="1">Remboursé</option>
													<option value="0">Non remboursé</option>
												</select>
											</div>
										</div>
										<div class="col-sm-6 col-md-2" style="margin-top: 32px;">
											<a href="javascript:void(0)" class="btn btn-white filterCaisseNoire"><span class="fa fa-filter mr-2"></span> Filtrer</a>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable-caissenoire">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Payé par</th>
										<th>Titre</th>
										<th>Description</th>
										<th>Montant</th>
										<th>Date</th>
										<th>Statut</th>
										<th>Justificatif</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($entrees as $entree) : ?>
									<tr data-utilisateur="<?= $entree->getUtilisateur()->getId(); ?>" data-refunded="<?= $entree->isRefunded() ? 1 : 0; ?>">
										<td><?= $entree->getId(); ?></td>
										<td><?= htmlspecialchars($entree->getUtilisateur()->getPrenom() . ' ' . $entree->getUtilisateur()->getNom()); ?></td>
										<td><?= htmlspecialchars($entree->getTitre()); ?></td>
										<td><?= htmlspecialchars($entree->getDescription()); ?></td>
										<td><b><?= number_format($entree->getMontant(), 2, ',', ' '); ?> DH</b></td>
										<td data-sort="<?= strtotime($entree->getDateCharge()); ?>"><?= normaldate($entree->getDateCharge()); ?></td>
										<td>
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_caissenoire')) : ?>
												<?php $state = $entree->isRefunded() ? 'oui' : 'non'; ?>
												<span class="badge caisse-toggle-badge enable-rembourse <?= $entree->isRefunded() ? 'badge-success' : 'badge-danger'; ?>" data-id="<?= $entree->getId(); ?>" data-state="<?= $state; ?>" data-toggle="tooltip" data-placement="top" data-original-title="Cliquez pour basculer">
													<?= $entree->isRefunded() ? 'Remboursé' : 'Non remboursé'; ?>
												</span>
											<?php else : ?>
												<span class="badge <?= $entree->isRefunded() ? 'badge-success' : 'badge-danger'; ?>"><?= $entree->isRefunded() ? 'Remboursé' : 'Non remboursé'; ?></span>
											<?php endif; ?>
										</td>
										<td>
											<?php if ($entree->getJustificatif()) : ?>
											<a href="images/caissenoire/<?= $entree->getJustificatif(); ?>" class="btn btn-sm btn-white text-info" data-toggle="tooltip" data-placement="top" data-original-title="Justificatif" data-fancybox><i class="fa fa-paperclip"></i></a>
											<?php else : ?>
											<span class="text-muted">—</span>
											<?php endif; ?>
										</td>
										<td class="text-right">
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_caissenoire')) : ?>
											<a href="index.php?option=com_caissenoire&task=edit&id=<?= $entree->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
											<?php endif; ?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_caissenoire')) : ?>
											<a href="javascript:void(0);" class="btn btn-sm btn-white text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $entree->getId(); ?>"><i class="far fa-trash-alt"></i></a>
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function () {

	function animerCompteurVers($el, valeurFinale) {
		if (typeof gsap === 'undefined') {
			$el.text(valeurFinale.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
			return;
		}
		var compteur = { valeur: 0 };
		gsap.to(compteur, {
			valeur: valeurFinale,
			duration: 1.2,
			ease: 'power1.out',
			onUpdate: function () {
				$el.text(compteur.valeur.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
			}
		});
	}

	$('.caisse-total-counter').each(function () {
		var $el = $(this);
		animerCompteurVers($el, parseFloat($el.attr('data-valeur')) || 0);
	});

	var caisseTable = $('.datatable-caissenoire').DataTable({
		order: [[5, 'desc']],
		columnDefs: [{ orderable: false, targets: [8] }]
	});

	$.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
		if (settings.nTable !== caisseTable.table().node()) {
			return true;
		}
		var $row = $(caisseTable.row(dataIndex).node());

		var from = $("#caisseFrom").val();
		var to = $("#caisseTo").val();
		if (from || to) {
			var dateCharge = caisseTable.cell(dataIndex, 5).render('sort');
			if (from) {
				var fromTs = new Date(from.split('/').reverse().join('-')).getTime() / 1000;
				if (dateCharge < fromTs) return false;
			}
			if (to) {
				var toTs = new Date(to.split('/').reverse().join('-')).getTime() / 1000 + 86399;
				if (dateCharge > toTs) return false;
			}
		}

		var utilisateur = $("#caisseUtilisateur").val();
		if (utilisateur && $row.attr('data-utilisateur') !== utilisateur) {
			return false;
		}

		var statut = $("#caisseStatut").val();
		if (statut !== "" && statut !== undefined && $row.attr('data-refunded') !== statut) {
			return false;
		}

		return true;
	});

	$(document).on("click", ".filterCaisseNoire", function (event) {
		event.preventDefault();
		caisseTable.draw();
	});

	var msgsucces = "Entrée supprimée avec succès";

	$(document).on("click", ".delete", function () {
		var $btn = $(this);
		if (confirm("Etes-vous sûr ?")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_caissenoire/controleurs/router.php?task=deleteCaisseNoire", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {
					$btn.closest("tr").addClass("table-danger");
					setTimeout(function () {
						$btn.closest("tr").remove();
					}, 1000);
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				} else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				}
			});
		}
	});

	$(document).on("click", ".enable-rembourse", function () {
		var btn = $(this);
		var id = btn.attr("data-id");
		var state = btn.attr("data-state");
		var order = 'id=' + id + "&state=" + state;
		$.post("components/com_caissenoire/controleurs/router.php?task=toggleRembourse", order, function (theResponse) {
			var error_msg = state === "oui" ? "Erreur lors du passage à non remboursé." : "Erreur lors du passage à remboursé.";
			if (parseInt(theResponse) === 1) {
				if (state === "oui") {
					btn.attr("data-state", "non").removeClass("badge-success").addClass("badge-danger").attr("data-original-title", "Cliquez pour basculer").text("Non remboursé");
					btn.closest('tr').attr('data-refunded', '0');
				} else {
					btn.attr("data-state", "oui").removeClass("badge-danger").addClass("badge-success").attr("data-original-title", "Cliquez pour basculer").text("Remboursé");
					btn.closest('tr').attr('data-refunded', '1');
				}
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> ' + error_msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			}
		});
	});
});
</script>
