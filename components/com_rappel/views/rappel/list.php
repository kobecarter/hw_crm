<!-- Page Wrapper -->
<style>
	.table-expired-recently {
		background-color: rgba(220, 53, 69, 0.5);
	}

	.table-expired-recently:hover {
		background-color: rgba(220, 53, 69, 0.6) !important;
	}

	.table-expired {
		background-color: rgba(220, 53, 69, 0.7);
	}

	.table-expired:hover {
		background-color: rgba(220, 53, 69, 0.8) !important;
	}
</style>
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Rappels</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Rappels</li>
					</ul>
				</div>
				<div class="col-auto">
				    <?php if ($_SESSION['user']->hasDroit('view', 'com_rappel')) :?>
    					<a class="btn btn-warning filter-btn text-white" href="index.php?option=com_rappel&task=relances" data-toggle="tooltip" data-placement="top" data-original-title="Relance">
    						<i class="fa fa-envelope"></i>
    					</a>
    					<a class="btn btn-primary filter-btn" href="index.php?option=com_rappel&task=archive" data-toggle="tooltip" data-placement="top" data-original-title="Archive">
    						<i class="fas fa-archive"></i>
    					</a>
					<?php endif;?>
					<?php if ($_SESSION['user']->hasDroit('add', 'com_rappel')) :?>
    					<a href="index.php?option=com_rappel&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter rappel">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<?php
		// Compteurs des puces de filtre rapide - mêmes seuils que le code couleur des lignes déjà
		// en place ci-dessous (30j "bientôt", 10j "urgent", 0j "expiré").
		$rappelTotal = sizeof($rappels);
		$rappelBientot = 0;
		$rappelUrgent = 0;
		$rappelExpire = 0;
		foreach ($rappels as $rappelCompte) {
			$j = $rappelCompte->getDaysLeft();
			if ($j < 30) { $rappelBientot++; }
			if ($j < 10) { $rappelUrgent++; }
			if ($j < 0) { $rappelExpire++; }
		}
		?>
		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Hosting / Domaines / Renouvellements</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>

						<!-- Filtres rapides : mêmes seuils que le code couleur des lignes (30j/10j/0j). -->
						<div class="quick-filter-chips mb-3">
							<button type="button" class="active" data-filter="all">Tous <span class="badge badge-pill ml-1"><?= $rappelTotal ?></span></button>
							<button type="button" data-filter="soon">Bientôt (30j) <span class="badge badge-pill ml-1"><?= $rappelBientot ?></span></button>
							<button type="button" data-filter="urgent">Urgent (10j) <span class="badge badge-pill ml-1"><?= $rappelUrgent ?></span></button>
							<button type="button" data-filter="expired">Expirés <span class="badge badge-pill ml-1"><?= $rappelExpire ?></span></button>
						</div>

						<div class="table-responsive">
							<table id="rappels-table" class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Type</th>
										<th>Domaine</th>
										<th>Date expiration</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($rappels as $rappel) : ?>
										<?php
										$rowClass = '';
										if ($rappel->getDaysLeft() < 30) {
											$rowClass = 'table-warning';
										}
										if ($rappel->getDaysLeft() < 10) {
											$rowClass = 'table-danger';
										}
										if ($rappel->getDaysLeft() < 0) {
											$rowClass = 'table-expired-recently';
										}
										if ($rappel->getDaysLeft() < -30) {
											$rowClass = 'table-expired';
										}
										?>
										<tr class="<?php echo $rowClass; ?>" data-highlight-id="<?= $rappel->getId(); ?>" data-days-left="<?= $rappel->getDaysLeft(); ?>">
											<td><?php echo $rappel->getId(); ?></td>
											<td><?php echo $rappel->getType(); ?></td>
											<td><?php echo $rappel->getDomaine(); ?></td>
											<td data-sort="<?= strtotime($rappel->getDateExpir())?>"><?php echo normaldate($rappel->getDateExpir()); ?></td>
											<td class="text-right">
											    <?php if ($_SESSION['user']->hasDroit('edit', 'com_rappel') || $_SESSION['user']->hasDroit('add', 'com_rappel')) :?>
    												<?php if ($rappel->getDaysLeft() < -30) : ?>
    													<a href="javascript:void(0);" class="btn btn-sm btn-white text-dark mr-2 archive" data-toggle="tooltip" data-placement="top" data-original-title="Archiver" data-id="<?= $rappel->getId(); ?>"><i class="fa fa-archive"></i></a>
    												<?php endif; ?>
    												<?php if ($rappel->getDaysLeft() < 30) : ?>
    													<a href="javascript:void(0);" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi mail de rappel" data-id="<?= $rappel->getId(); ?>"><i class="far fa-paper-plane"></i></a>
    
    													<a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2 renew" data-toggle="tooltip" data-placement="top" data-original-title="Renouveller" data-id="<?= $rappel->getId(); ?>"><i class="fa fa-sync"></i></a>
    												<?php endif; ?>
    											<?php endif; ?>
    											<?php if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) :?>
    												<a href="index.php?option=com_rappel&task=edit&id=<?= $rappel->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif;?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_rappel')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $rappel->getId(); ?>"><i class="far fa-trash-alt"></i></a>
												<?php endif;?>
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

		// Filtres rapides (Tous / Bientôt / Urgent / Expirés) - même mécanique que la page
		// Relances (data-* sur les <tr> + extension de recherche DataTables). Scopé par l'id de la
		// table (rappels-table) : .datatable est une classe générique réutilisée sur tout le site,
		// sans ce garde-fou le filtre s'appliquerait à toutes les autres listes de l'app.
		var rappelFilter = 'all';
		if ($.fn.DataTable) {
			$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
				if (!settings.nTable || settings.nTable.id !== 'rappels-table') return true;
				if (rappelFilter === 'all') return true;
				var joursRestants = parseInt($(settings.aoData[dataIndex].nTr).attr('data-days-left'), 10);
				if (rappelFilter === 'soon') return joursRestants < 30;
				if (rappelFilter === 'urgent') return joursRestants < 10;
				if (rappelFilter === 'expired') return joursRestants < 0;
				return true;
			});
		}
		$(document).on('click', '.quick-filter-chips button', function () {
			$('.quick-filter-chips button').removeClass('active');
			$(this).addClass('active');
			rappelFilter = $(this).data('filter');
			if ($.fn.DataTable && $.fn.DataTable.isDataTable('#rappels-table')) {
				$('#rappels-table').DataTable().draw();
			}
		});

		var msgsucces = "Rappel supprimé avec succès";
		$(document).on("click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_rappel/controleurs/router.php?task=deleteRappel", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().remove()
						}, 1000);

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})

		var msgsucces = "Rappel archivé avec succès";
		$(document).on("click", ".archive", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_rappel/controleurs/router.php?task=archiveRappel", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().remove()
						}, 1000);

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'archivation<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})


		$(document).on("click", ".renew", function() {
			var msgsuccesrenew = "Rappel renouvellé avec succès";
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_rappel/controleurs/router.php?task=renewRappel", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsuccesrenew + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

						setTimeout(function() {
							document.location.reload();
						}, 3000);

					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors du renouvellement<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})

		$(document).on("click", ".sendMail", function() {
			var msgsuccesrenew = "Email de relance envoyé avec succès";
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_rappel/controleurs/router.php?task=sendMailRappel", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsuccesrenew + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else if (parseInt(theResponse) == 2) {
						$('.msgbox').html('<div class="alert alert-info alert-dismissible fade show" role="alert"><strong>Oups!</strong> Service renouvellé<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la relance<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})
	});
</script>