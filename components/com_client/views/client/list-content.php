<?php
/* Contenu partagé de la liste des clients (hors cartes KPI, voir list-kpi.php) :
   inclus tel quel par components/com_client/views/client/list.php (page autonome,
   option=com_client) ET par components/com_facture/views/facture/facturation.php
   (onglet Clients, option=com_facture&task=client). Une seule version à
   maintenir pour les deux points d'entrée. Nécessite $clients en entrée. */
?>
<div class="card card-table">
	<div class="card-header d-flex align-items-center">
		<h4 class="card-title">Liste des clients</h4>
		<div class="client-live-search ml-3 mr-auto">
			<i class="fa fa-search"></i>
			<input type="text" id="client-live-search" class="form-control form-control-sm" placeholder="Rechercher un client...">
		</div>
		<div class="w-fit">
			<?php if ($_SESSION['user']->hasDroit('add', 'com_client')) : ?>
				<a href="index.php?option=com_client&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter client">
					<i class="fas fa-plus"></i>
				</a>
			<?php endif; ?>
			<?php if ($_SESSION['user']->hasDroit('view', 'com_client')) : ?>
				<a href="index.php?option=com_client&task=archive" class="btn btn-warning text-white mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Clients archivés">
					<i class="fas fa-archive"></i>
				</a>
				<a class="btn btn-primary filter-btn mr-1" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
					<i class="fas fa-filter"></i>
				</a>
				<div class="dropdown d-inline-block export-dropdown">
					<a href="javascript:void(0);" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="fas fa-download mr-1"></i> Exporter
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item exportClient" href="#0"><i class="far fa-file-excel mr-2 text-success"></i>Liste détaillée (par année)</a>
						<a class="dropdown-item exportClientWithFilter" href="#0"><i class="far fa-file-excel mr-2 text-success"></i>Liste des clients</a>
						<a class="dropdown-item exportEmail" href="#0"><i class="far fa-envelope mr-2 text-info"></i>Emails des clients</a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3"></div>

		<!-- Search Filter -->
		<div id="filter_inputs" class="card filter-card">
			<div class="card-body">
				<form method="get" action="index.php" id="filterClient">
					<input type="hidden" name="option" value="com_client">
					<div class="row align-items-end">
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label><i class="fa fa-calendar-alt mr-1"></i> Date début</label>
								<div class="cal-icon">
									<input type="text" class="form-control datetimepicker" required name="from" id="filter_from" value="<?php echo isset($_GET['from']) ? $_GET['from'] : '01/01/' . date('Y'); ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label><i class="fa fa-calendar-alt mr-1"></i> Date fin</label>
								<div class="cal-icon">
									<input type="text" class="form-control datetimepicker" required name="to" id="filter_to" value="<?php echo isset($_GET['to']) ? $_GET['to'] : date('d/m/Y'); ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label><i class="fa fa-toggle-on mr-1"></i> Statut</label>
								<select class="form-control" id="client-status-filter">
									<option value="all">Tous les statuts</option>
									<option value="active">Actifs uniquement</option>
									<option value="inactive">Inactifs uniquement</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label><i class="fa fa-inbox mr-1"></i> Activité</label>
								<select class="form-control" id="client-activity-filter">
									<option value="all">Tous les clients</option>
									<option value="no_activity">Sans activité (0 devis, 0 facture, 0 relance, 0 paiement)</option>
								</select>
							</div>
						</div>
						<div class="col-sm-12 col-md-12 mb-3">
							<button type="submit" name="" class="btn btn-primary submit">Filtrer par période</button>
							<a href="index.php?option=com_client" id="reset-status-filter" class="btn btn-white text-secondary ml-1"><i class="fa fa-undo mr-1"></i> Réinitialiser</a>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /Search Filter -->

		<div class="table-responsive list-box">
			<table id="clients-table" class="table table-stripped table-center table-hover datatable">
				<thead class="thead-light">
					<tr>
						<th>ID</th>
						<th>Client</th>
						<th>Téléphone</th>
						<th>Email</th>
						<th>Raison sociale</th>
						<th>Statut</th>
						<th class="text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($clients as $client): ?>
					<?php $activity = "Ajouté par " . $client->getUserAdded()->getNom() . " | Modifié par " . $client->getUserEdited()->getNom(); ?>
					<?php $state = $client->isActive() ? 'oui' : 'non'; ?>
					<?php $title = $client->isActive() ? 'Actif' : 'Inactif'; ?>
					<?php $color = $client->isActive() ? 'text-success' : 'text-danger'; ?>
					<?php $ico = $client->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
					<?php $badgeClass = $client->isActive() ? 'bg-success-light' : 'bg-danger-light'; ?>
					<?php $sansActivite = isset($sansActiviteMap[$client->getId()]) && $sansActiviteMap[$client->getId()]; ?>
					<tr data-noactivity="<?= $sansActivite ? '1' : '0' ?>">
						<td><?php echo $client->getId(); ?></td>
						<td>
							<?php $photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
							<h2 class="table-avatar">
								<a href="index.php?option=com_client&task=showDetails&id=<?= $client->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>">
									<span class="avatar-with-status mr-2">
										<img class="avatar avatar-sm avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image">
										<?php if ($sansActivite) : ?><span class="avatar-sleep-badge" title="Sans activité : 0 devis, 0 facture, 0 relance, 0 paiement">😴</span><?php endif; ?>
									</span>
									<?php echo $client->getPrenom() . " " . $client->getNom(); ?>
								</a>
							</h2>
						</td>
						<td><?php echo $client->getTel(); ?></td>
						<td><?php echo $client->getEmail(); ?></td>
						<td><?php echo $client->getRaisonSocial(); ?></td>
						<td><span class="badge <?php echo $badgeClass; ?>"><?php echo $title; ?></span></td>
						<td class="text-right">
							<?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) :?>
							    <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enableClient" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $client->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
							<?php endif;?>
							<div class="dropdown dropdown-action d-inline-block">
								<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
								<div class="dropdown-menu dropdown-menu-right">
									<a class="dropdown-item text-info" href="index.php?option=com_client&task=showDetails&id=<?= $client->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
									<?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) :?>
									    <a class="dropdown-item text-warning" href="index.php?option=com_client&task=edit&id=<?= $client->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
									    <a class="dropdown-item text-secondary archiveClient" href="javascript:void(0);" data-id="<?= $client->getId(); ?>"><i class="fas fa-archive mr-2"></i>Archiver</a>
									<?php endif;?>
									<?php if ($_SESSION['user']->hasDroit('delete', 'com_client')) :?>
									    <a class="dropdown-item text-danger deleteClient" href="javascript:void(0);" data-id="<?= $client->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
									<?php endif;?>
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

<script type="text/javascript">
$(function () {

	// Recherche instantanée : s'appuie sur le moteur de recherche déjà initialisé par
	// DataTables sur #clients-table (script.js), donc reste compatible avec la
	// pagination et le tri au lieu de cacher des lignes "à la main".
	$(document).on("keyup", "#client-live-search", function () {
		var value = $(this).val();
		if ($.fn.DataTable && $.fn.DataTable.isDataTable('#clients-table')) {
			$('#clients-table').DataTable().search(value).draw();
		}
	});

	// Filtre rapide Actif/Inactif : s'appuie sur le bouton .enableClient (data-state)
	// déjà présent dans chaque ligne. Ciblé sur #clients-table par id (et non une
	// classe générique) car cette même page peut contenir d'autres DataTables
	// (onglets Devis/Contrats/Factures/Paiements dans la page Facturation).
	// Filtre "Sans activité" : combiné au même filtre (data-noactivity posé sur le <tr>,
	// calculé côté serveur via client::activityMap() pour éviter une requête par ligne).
	var clientStatusFilter = 'all';
	var clientActivityFilter = 'all';
	if ($.fn.DataTable) {
		$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
			if (!settings.nTable || settings.nTable.id !== 'clients-table') return true;
			var $row = $(settings.aoData[dataIndex].nTr);
			if (clientStatusFilter !== 'all') {
				var state = $row.find('.enableClient').attr('data-state');
				if (clientStatusFilter === 'active' && state !== 'oui') return false;
				if (clientStatusFilter === 'inactive' && state !== 'non') return false;
			}
			if (clientActivityFilter === 'no_activity' && $row.attr('data-noactivity') !== '1') return false;
			return true;
		});
	}
	$(document).on("change", "#client-status-filter", function () {
		clientStatusFilter = $(this).val();
		if ($.fn.DataTable && $.fn.DataTable.isDataTable('#clients-table')) {
			$('#clients-table').DataTable().draw();
		}
	});
	$(document).on("change", "#client-activity-filter", function () {
		clientActivityFilter = $(this).val();
		if ($.fn.DataTable && $.fn.DataTable.isDataTable('#clients-table')) {
			$('#clients-table').DataTable().draw();
		}
	});
	$(document).on("click", ".exportClient", function () {
		event.preventDefault();
		// Ce rapport ("Liste détaillée par année") reste structuré par année (colonne "Année",
		// détail des factures de l'année) : on en déduit l'année depuis "Date début" de la période.
		var anneeDeduite = ($("#filter_from").val() || '').split('/')[2] || new Date().getFullYear();
		window.open('components/com_client/controleurs/router.php?task=exportClient&year=' + anneeDeduite, '_blank');
	})

	$(document).on("click", ".exportClientWithFilter", function () {
		event.preventDefault();
		window.open('components/com_client/controleurs/router.php?task=exportClientWithFilter&from=' + encodeURIComponent($("#filter_from").val()) + '&to=' + encodeURIComponent($("#filter_to").val()), '_blank');
	})

	$(document).on("click", ".exportEmail", function () {
		event.preventDefault();
		window.open('components/com_client/controleurs/router.php?task=exportEmail', '_blank');
	})

	// Le filtre "Date début/Date fin" est un formulaire GET classique (navigation réelle vers
	// index.php?option=com_client&from=...&to=...) : le tableau rendu reste alors exactement le
	// même que celui de la liste normale (mêmes colonnes, mêmes badges), au lieu d'un tableau
	// réduit renvoyé en AJAX qui faisait disparaître l'indicateur "sans activité".

	var msgsucces = "Client supprimé avec succès";

	$(document).on("click", ".deleteClient", function() {
		event.preventDefault();
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_client/controleurs/router.php?task=deleteClient", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {

					$btn.closest("tr").addClass("table-danger");
					setTimeout(function () {
						$btn.closest("tr").remove()
					}, 1000);

					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})

	$(document).on("click", ".archiveClient", function() {
		event.preventDefault();
		var $btn = $(this);
		if (confirm("Archiver ce client ? Il ne sera plus visible dans la liste principale, mais reste accessible depuis \"Clients archivés\".")) {
			var id = $btn.attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_client/controleurs/router.php?task=archiveClient", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {
					$btn.closest("tr").addClass("table-warning");
					setTimeout(function () {
						$btn.closest("tr").remove()
					}, 1000);
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Client archivé avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'archivage<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})

	$(document).on("click", ".enableClient", function() {
		var btn = $(this);
		var id = btn.attr("data-id");
		var state = btn.attr("data-state");
		var order = 'id=' + id + "&state=" + state;
		$.post("components/com_client/controleurs/router.php?task=enableClient", order, function (theResponse) {
			var error_msg = "Erreur lors de l'activation.";
			if (state === "oui") {
				error_msg = "Erreur lors de la désactivation.";
			}
			if (parseInt(theResponse) === 1) {
				if (state === "oui") {
					btn.attr("data-state", "non").removeClass("text-success").addClass("text-danger").attr("data-original-title", "Inactif").html("<i class='fa fa-toggle-off'>");
				} else {
					btn.attr("data-state", "oui").removeClass("text-danger").addClass("text-success").attr("data-original-title", "Actif").html("<i class='fa fa-toggle-on'>");
				}
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> ' + error_msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});
});
</script>
