<?php
/* Vue unique et intuitive du module Relances : remplace les anciennes list.php
   (clients groupés) / list2.php (relances non traitées) / view.php (relances
   d'un client) par un seul tableau de relances, avec KPI + recherche + filtres
   rapides (Toutes / Non traitées / En retard / Traitées) gérés côté client,
   sans recharger la page. */
$totalRelances = sizeof($relances);
$nonTraitees = 0;
$enRetard = 0;
$clientsConcernes = array();
foreach ($relances as $r) {
	if (!$r->isTraite()) {
		$nonTraitees++;
	}
	if (!$r->isTraite() && $r->getDaysLeft() < 0) {
		$enRetard++;
	}
	$clientsConcernes[$r->getClient()->getId()] = true;
}
$nbClientsConcernes = sizeof($clientsConcernes);
?>
<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Relances</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Relances</li>
					</ul>
				</div>
			</div>
		</div>

		<!-- Statistiques rapides -->
		<div class="row client-stats-row">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="fa fa-bell"></i></span>
							<div class="dash-count">
								<div class="dash-title">Total relances</div>
								<div class="dash-counts"><p><?php echo $totalRelances; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-4"><i class="fa fa-hourglass-half"></i></span>
							<div class="dash-count">
								<div class="dash-title">Non traitées</div>
								<div class="dash-counts"><p><?php echo $nonTraitees; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1"><i class="fa fa-exclamation-triangle"></i></span>
							<div class="dash-count">
								<div class="dash-title">En retard</div>
								<div class="dash-counts"><p><?php echo $enRetard; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-users"></i></span>
							<div class="dash-count">
								<div class="dash-title">Clients concernés</div>
								<div class="dash-counts"><p><?php echo $nbClientsConcernes; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Statistiques rapides -->

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header d-flex align-items-center">
						<h4 class="card-title">Liste des relances</h4>
						<div class="client-live-search ml-3 mr-auto">
							<i class="fa fa-search"></i>
							<input type="text" id="relance-live-search" class="form-control form-control-sm" placeholder="Rechercher un client, une facture...">
						</div>
						<div class="w-fit">
							<?php if($_SESSION['user']->hasDroit('add', 'com_relance')) :?>
								<a href="index.php?option=com_relance&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter relance">
									<i class="fas fa-plus"></i>
								</a>
							<?php endif;?>
						</div>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>

						<div class="quick-filter-chips">
							<button type="button" class="active" data-filter="all">Toutes <span class="badge badge-pill ml-1"><?php echo $totalRelances; ?></span></button>
							<button type="button" data-filter="untreated">Non traitées <span class="badge badge-pill ml-1"><?php echo $nonTraitees; ?></span></button>
							<button type="button" data-filter="late">En retard <span class="badge badge-pill ml-1"><?php echo $enRetard; ?></span></button>
							<button type="button" data-filter="treated">Traitées <span class="badge badge-pill ml-1"><?php echo $totalRelances - $nonTraitees; ?></span></button>
						</div>

						<div class="table-responsive list-box">
							<table id="relances-table" class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>Client</th>
										<th>Facture</th>
										<th>Type</th>
										<th>Remarque</th>
										<th>Date relance</th>
										<th>Statut</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($relances as $relance) : ?>
										<?php
										if ($relance->getFacture()->getId() != 0) {
											$factureLabel = $relance->getFacture()->getNumero() . ' - ' . number_format($relance->getFacture()->getTotal(), 2, ',', ' ') . ' ' . $relance->getFacture()->getDevise();
										} else {
											$factureLabel = '--';
										}

										$isLate = !$relance->isTraite() && $relance->getDaysLeft() < 0;
										if ($relance->isTraite()) {
											$statutBadge = '<span class="badge bg-success-light">Traitée</span>';
										} elseif ($isLate) {
											$statutBadge = '<span class="badge bg-danger-light">En retard</span>';
										} elseif ($relance->getDaysLeft() < 3) {
											$statutBadge = '<span class="badge bg-warning-light">Bientôt due</span>';
										} else {
											$statutBadge = '<span class="badge bg-primary-light">À venir</span>';
										}

										$remark = $relance->getRemarque();
										if (strlen($remark) > 60) {
											$remark = substr($remark, 0, 60) . '...';
										}

										$photoLink = $relance->getClient()->getPhoto() != '' ? "images/clients/" . $relance->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg";
										$clientNom = $relance->getClient()->getRaisonSocial() != '' ? $relance->getClient()->getRaisonSocial() : $relance->getClient()->getNom() . ' ' . $relance->getClient()->getPrenom();
										$date = date_create($relance->getDate());
										?>
										<tr data-traite="<?php echo $relance->isTraite() ? '1' : '0'; ?>" data-late="<?php echo $isLate ? '1' : '0'; ?>" data-highlight-id="<?= $relance->getId(); ?>">
											<td>
												<h2 class="table-avatar">
													<a href="index.php?option=com_client&task=showDetails&id=<?php echo $relance->getClient()->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $clientNom; ?></a>
												</h2>
											</td>
											<td><?php echo $factureLabel; ?></td>
											<td><?php echo $relance->getType(); ?></td>
											<td><?php echo htmlspecialchars($remark); ?></td>
											<td data-sort="<?php echo strtotime($relance->getDate()); ?>"><?php echo date_format($date, 'd/m/Y H:i'); ?></td>
											<td><?php echo $statutBadge; ?></td>
											<td class="text-right">
												<?php if ($isLate) : ?>
													<a href="javascript:void(0);" data-id="<?php echo $relance->getId(); ?>" class="btn btn-sm btn-white text-success mr-2 prolonge" data-toggle="tooltip" data-placement="top" data-original-title="Prolonger"><i class="fa fa-clock"></i></a>
												<?php endif; ?>
												<?php if ($relance->getPhoto() != '') : ?>
													<a href="images/relances/<?php echo $relance->getPhoto(); ?>" data-fancybox class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Justificatif"><i class="fa fa-file-alt"></i></a>
												<?php endif; ?>
												<div class="dropdown dropdown-action d-inline-block">
													<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
														<?php if ($_SESSION['user']->hasDroit('edit', 'com_relance')) : ?>
															<a class="dropdown-item text-warning" href="index.php?option=com_relance&task=edit&id=<?= $relance->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
														<?php endif; ?>
														<?php if ($_SESSION['user']->hasDroit('delete', 'com_relance')) : ?>
															<a class="dropdown-item text-danger deleteRelance" href="javascript:void(0);" data-id="<?= $relance->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
														<?php endif; ?>
													</div>
												</div>
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

<!-- Form Modal -->
<div id="dialog-custom" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>
<!-- /Form Modal -->

<script type="text/javascript">
	$(function() {

		// Recherche instantanée
		$(document).on("keyup", "#relance-live-search", function () {
			var value = $(this).val();
			if ($.fn.DataTable && $.fn.DataTable.isDataTable('#relances-table')) {
				$('#relances-table').DataTable().search(value).draw();
			}
		});

		// Filtres rapides (Toutes / Non traitées / En retard / Traitées)
		var relanceFilter = 'all';
		if ($.fn.DataTable) {
			$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
				if (!settings.nTable || settings.nTable.id !== 'relances-table') return true;
				if (relanceFilter === 'all') return true;
				var $row = $(settings.aoData[dataIndex].nTr);
				var traite = $row.attr('data-traite') === '1';
				var late = $row.attr('data-late') === '1';
				if (relanceFilter === 'untreated') return !traite;
				if (relanceFilter === 'late') return late;
				if (relanceFilter === 'treated') return traite;
				return true;
			});
		}
		$(document).on("click", ".quick-filter-chips button", function () {
			$(".quick-filter-chips button").removeClass("active");
			$(this).addClass("active");
			relanceFilter = $(this).data('filter');
			if ($.fn.DataTable && $.fn.DataTable.isDataTable('#relances-table')) {
				$('#relances-table').DataTable().draw();
			}
		});

		$(document).on("click", ".prolonge", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			var title = 'Prolonger la relance';
			var order = 'id=' + id;
			$.post("components/com_relance/controleurs/router.php?task=prolongerRelance", order, function(theResponse) {
				$(".modal-title").html(title);
				$(".modal-body").html(theResponse);
				$("#dialog-custom").modal('show');
			})
		})

		var msgsucces = "Relance supprimée avec succès";
		$(document).on("click", ".deleteRelance", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_relance/controleurs/router.php?task=deleteRelance", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {
						$btn.closest("tr").addClass("table-danger");
						setTimeout(function() {
							$btn.closest("tr").remove()
						}, 1000);
						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})
	});
</script>
