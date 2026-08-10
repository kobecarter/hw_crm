<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Clients archivés</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_client">Clients</a></li>
						<li class="breadcrumb-item active">Archive</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Clients archivés</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable">
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
									<?php foreach ($clients as $client) : ?>
										<?php $title = $client->isActive() ? 'Actif' : 'Inactif'; ?>
										<?php $badgeClass = $client->isActive() ? 'bg-success-light' : 'bg-danger-light'; ?>
										<tr>
											<td><?php echo $client->getId(); ?></td>
											<td>
												<?php $photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
												<h2 class="table-avatar">
													<a href="index.php?option=com_client&task=showDetails&id=<?= $client->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $client->getPrenom() . " " . $client->getNom(); ?></a>
												</h2>
											</td>
											<td><?php echo $client->getTel(); ?></td>
											<td><?php echo $client->getEmail(); ?></td>
											<td><?php echo $client->getRaisonSocial(); ?></td>
											<td><span class="badge <?php echo $badgeClass; ?>"><?php echo $title; ?></span></td>
											<td class="text-right">
												<div class="dropdown dropdown-action d-inline-block">
													<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item text-info" href="index.php?option=com_client&task=showDetails&id=<?= $client->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
														<?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) : ?>
															<a class="dropdown-item text-success retablirClient" href="javascript:void(0);" data-id="<?= $client->getId(); ?>"><i class="fa fa-undo mr-2"></i>Rétablir</a>
														<?php endif; ?>
														<?php if ($_SESSION['user']->hasDroit('delete', 'com_client')) : ?>
															<a class="dropdown-item text-danger deleteClient" href="javascript:void(0);" data-id="<?= $client->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
														<?php endif; ?>
													</div>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
									<?php if (sizeof($clients) <= 0) : ?>
										<tr><td colspan="7" class="text-center">Aucun client archivé</td></tr>
									<?php endif; ?>
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

		var msgsucces = "Client supprimé avec succès";

		$(document).on("click", ".deleteClient", function() {
			event.preventDefault();
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $btn.attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_client/controleurs/router.php?task=deleteClient", order, function (theResponse) {
					if (parseInt(theResponse) == 1) {
						$btn.closest("tr").addClass("table-danger");
						setTimeout(function () {
							$btn.closest("tr").remove()
						}, 1000);
						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})

		$(document).on("click", ".retablirClient", function() {
			event.preventDefault();
			var $btn = $(this);
			if (confirm("Rétablir ce client dans la liste principale ?")) {
				var id = $btn.attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_client/controleurs/router.php?task=retablirClient", order, function (theResponse) {
					if (parseInt(theResponse) == 1) {
						$btn.closest("tr").addClass("table-success");
						setTimeout(function () {
							$btn.closest("tr").remove()
						}, 1000);
						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Client rétabli avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors du rétablissement<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})
	});
</script>
