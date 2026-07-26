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
<div class="page-wrapper">
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
				<div class="col-auto">
				    <a href="index.php?option=com_relance&task=list" class="btn btn-warning mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Relances non traitées">
						<i class="fas fa-list"></i>
					</a>
					
					<?php if($_SESSION['user']->hasDroit('add', 'com_relance')) :?>
    					<a href="index.php?option=com_relance&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter relance">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

			<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des relances</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Nom complet</th>
										<th>Téléphone</th>
										<th>Email</th>
										<th>Raison social</th>
										<th>Relances</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($clients as $client): ?>
									<tr>
										<td><?php echo $client->getId(); ?></td>
										<td>
											<?php $photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
											<h2 class="table-avatar">
												<a href="index.php?option=com_client&task=edit&id=<?= $client->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $client->getPrenom() . " " . $client->getNom(); ?></a>
											</h2>
										</td>
										<td><?php echo $client->getTel(); ?></td>
										<td><?php echo $client->getEmail(); ?></td>
										<td><?php echo $client->getRaisonSocial(); ?></td>
										<td class="text-center">
											<?php
											 	$count_relances = relance::countByClient($client->getId());
												echo $count_relances;
											 ?>
										</td>
										<td class="text-right">
											<?php $state = $client->isActive() ? 'oui' : 'non'; ?>
											<?php $title = $client->isActive() ? 'Actif' : 'Inactif'; ?>
											<?php $color = $client->isActive() ? 'text-success' : 'text-danger'; ?>
											<?php $ico = $client->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
											<?php if ($_SESSION['user']->hasDroit('view', 'com_relance')) :?>
											    <a href="index.php?option=com_relance&task=client&id=<?= $client->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i></a> 
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
	
</script>