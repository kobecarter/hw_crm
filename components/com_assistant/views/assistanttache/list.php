<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Assistant — Tâches, rendez-vous &amp; suivis</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Assistant</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_assistant')) : ?>
				<div class="col-auto">
					<a href="index.php?option=com_assistant&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter">
						<i class="fas fa-plus"></i>
					</a>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
		// Compteurs des puces de filtre rapide - même logique que com_rappel/views/rappel/list.php.
		$tacheTotal = sizeof($taches);
		$tacheAFaire = 0;
		$tacheTerminees = 0;
		$tacheEnRetard = 0;
		foreach ($taches as $tacheCompte) {
			if ($tacheCompte->isTermine()) {
				$tacheTerminees++;
			} else {
				$tacheAFaire++;
				$j = $tacheCompte->getDaysLeft();
				if ($j !== null && $j < 0) {
					$tacheEnRetard++;
				}
			}
		}
		$typeLabels = array('tache' => 'Tâche', 'rendez_vous' => 'Rendez-vous', 'suivi_client' => 'Suivi client');
		?>

		<div class="row">
			<div class="col-sm-12">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>

						<div class="quick-filter-chips mb-3">
							<button type="button" class="active" data-filter="all">Toutes <span class="badge badge-pill ml-1"><?= $tacheTotal ?></span></button>
							<button type="button" data-filter="todo">À faire <span class="badge badge-pill ml-1"><?= $tacheAFaire ?></span></button>
							<button type="button" data-filter="late">En retard <span class="badge badge-pill ml-1"><?= $tacheEnRetard ?></span></button>
							<button type="button" data-filter="done">Terminées <span class="badge badge-pill ml-1"><?= $tacheTerminees ?></span></button>
						</div>

						<div class="table-responsive">
							<table id="assistant-tache-table" class="table table-stripped table-center table-hover datatable" data-order="[]">
								<thead class="thead-light">
									<tr>
										<th>Type</th>
										<th>Titre</th>
										<th>Lié à</th>
										<th>Date</th>
										<th>Statut</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($taches as $tache) : ?>
										<?php
										$joursRestants = $tache->getDaysLeft();
										$enRetard = !$tache->isTermine() && $joursRestants !== null && $joursRestants < 0;
										?>
										<tr data-highlight-id="<?= $tache->getId(); ?>" data-termine="<?= $tache->isTermine() ? '1' : '0' ?>" data-late="<?= $enRetard ? '1' : '0' ?>" class="<?= $enRetard ? 'table-danger' : '' ?>">
											<td><span class="badge bg-primary-light"><?= $typeLabels[$tache->getType()] ?? $tache->getType() ?></span></td>
											<td><?= $tache->isTermine() ? '<s class="text-muted">' . htmlspecialchars($tache->getTitre()) . '</s>' : htmlspecialchars($tache->getTitre()) ?></td>
											<td>
												<?php $relationLabel = $tache->getRelationLabel(); $relationUrl = $tache->getRelationUrl(); $relationIcon = $tache->getRelationIcon(); ?>
												<?php if ($relationLabel) : ?>
													<?php if ($relationUrl) : ?>
														<a href="<?= $relationUrl ?>"><i class="fa <?= $relationIcon ?> mr-1 text-muted"></i><?= htmlspecialchars($relationLabel) ?></a>
													<?php else : ?>
														<i class="fa <?= $relationIcon ?> mr-1 text-muted"></i><?= htmlspecialchars($relationLabel) ?>
													<?php endif; ?>
												<?php else : ?>
													<span class="text-muted">—</span>
												<?php endif; ?>
											</td>
											<td data-sort="<?= $tache->getDateTache() ? strtotime($tache->getDateTache()) : 0 ?>">
												<?= $tache->getDateTache() ? date('d/m/Y H:i', strtotime($tache->getDateTache())) : '<span class="text-muted">—</span>' ?>
											</td>
											<td>
												<label class="toggle-switch mb-0">
													<input type="checkbox" class="toggle-switch-input toggle-termine" data-id="<?= $tache->getId() ?>" <?= $tache->isTermine() ? 'checked' : '' ?>>
													<span class="toggle-switch-label">
														<span class="toggle-switch-indicator"></span>
													</span>
												</label>
											</td>
											<td class="text-right">
												<?php if ($_SESSION['user']->hasDroit('edit', 'com_assistant')) : ?>
													<a href="index.php?option=com_assistant&task=edit&id=<?= $tache->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
												<?php endif; ?>
												<?php if ($_SESSION['user']->hasDroit('delete', 'com_assistant')) : ?>
													<a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete-assistant-tache" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $tache->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
<!-- /Page Wrapper -->

<script type="text/javascript">
	$(function() {

		// Filtres rapides - même mécanique que com_rappel (extension de recherche DataTables,
		// scopée par id de table pour ne pas affecter les autres listes .datatable du site).
		var tacheFilter = 'all';
		if ($.fn.DataTable) {
			$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
				if (!settings.nTable || settings.nTable.id !== 'assistant-tache-table') return true;
				if (tacheFilter === 'all') return true;
				var $row = $(settings.aoData[dataIndex].nTr);
				var termine = $row.attr('data-termine') === '1';
				var late = $row.attr('data-late') === '1';
				if (tacheFilter === 'todo') return !termine;
				if (tacheFilter === 'late') return late;
				if (tacheFilter === 'done') return termine;
				return true;
			});
		}
		$(document).on('click', '.quick-filter-chips button', function () {
			$('.quick-filter-chips button').removeClass('active');
			$(this).addClass('active');
			tacheFilter = $(this).data('filter');
			if ($.fn.DataTable && $.fn.DataTable.isDataTable('#assistant-tache-table')) {
				$('#assistant-tache-table').DataTable().draw();
			}
		});

		$(document).on('change', '.toggle-termine', function () {
			var $checkbox = $(this);
			var id = $checkbox.data('id');
			$.post('components/com_assistant/controleurs/router.php?task=toggleTermineAssistantTache', { id: id }, function (theResponse) {
				if (parseInt(theResponse) === 1) {
					setTimeout(function () { document.location.reload(); }, 400);
				} else {
					$checkbox.prop('checked', !$checkbox.prop('checked'));
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la mise à jour<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		});

		$(document).on('click', '.delete-assistant-tache', function () {
			var $btn = $(this);
			if (confirm('Etes-vous sure !')) {
				var id = $btn.attr('data-id');
				$.post('components/com_assistant/controleurs/router.php?task=deleteAssistantTache', { id: id }, function (theResponse) {
					if (parseInt(theResponse) === 1) {
						$btn.closest('tr').addClass('table-danger');
						setTimeout(function () { $btn.closest('tr').remove(); }, 1000);
						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Élément supprimé avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		});
	});
</script>
