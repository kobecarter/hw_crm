<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Resources humaines</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Resources humaines</li>
					</ul>
				</div>
				<div class="col-auto">
					<?php if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) : ?>
						<a href="javascript:void(0)" data-target="#div-export-humanresource" data-toggle="modal" class="btn btn-primary mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Exporter la fiche d'absence par mois">
							<i class="fa fa-file-excel"></i> Exporter
						</a>
						<a href="index.php?option=com_resourcehumaine&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter resourcehumaine">
							<i class="fas fa-plus"></i>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Personnel</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<button class="nav-link active" id="nav-home-tab" data-toggle="tab" data-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Titulaire</button>
								<button class="nav-link" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Stagaire</button>
								<button class="nav-link" id="nav-contact-tab" data-toggle="tab" data-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Periode de test</button>
							</div>
						</div>
						<div class="tab-content" id="nav-tabContent">
							<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
								<div class="table-responsive list-box">
									<table class="table table-stripped table-center table-hover datatable">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Matricule</th>
												<th>Référence pointage</th>
												<th>Nom complet</th>
												<th>Téléphone</th>
												<th>Téléphone secondaire</th>
												<th>Date début</th>
												<th>Date fin</th>
												<th>Fonction</th>
												<th>Statut</th>
												<th class="text-right">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($resources_humaines_titulaire as $resourcehumaine): ?>
												<tr class="<?=$resourcehumaine->getEndDate() ? 'bg-danger-light' : null?>">
													<td><?php echo $resourcehumaine->getId(); ?></td>
													<td><?php echo $resourcehumaine->getReference(); ?></td>
													<td><?php echo $resourcehumaine->getReferencePointage(); ?></td>
													<td>
														<?php $photoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
														<h2 class="table-avatar">
															<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Fournisseur Image"> <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getlastName(); ?></a>
														</h2>
													</td>
													<td><?php echo $resourcehumaine->getPhone(); ?></td>
													<td><?php echo $resourcehumaine->getSecondPhone(); ?></td>
													<td><?php echo normaldate($resourcehumaine->getStartDate()); ?></td>
													<td><?php echo normaldate($resourcehumaine->getEndDate()); ?></td>
													<td><?php echo $resourcehumaine->getFunction(); ?></td>
													<td>
														<?php
															$status_value = "Inactif";
															$status_class = "bg-danger-light";
															if($resourcehumaine->isActive()){
																$status_value = "Actif";
																$status_class = "bg-success-light";
															}
														?>
														<span class="badge badge-pill <?=$status_class?>"><?=$status_value?></span>
													</td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i> Afficher</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-warning" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i> Modifier</a>
																	<a href="index.php?option=com_resourcehumaine&task=duplicate&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Dupliquer"><i class="fa fa-file"></i> Dupliquer</a>
																	<?php $fileresourcehumaine = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());?>
																	<a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-info" data-toggle="tooltip" data-placement="top" data-original-title="Fichiers"><i class="fa fa-file"></i> Fichiers <?php if(sizeof($fileresourcehumaine)<=0): ?> <span class="point text-danger"></span><?php endif;?></a>
																	<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Bulletin de paie"><i class="fa fa-file"></i> Bulletin de paie </a>
																	<a href="index.php?option=com_resourcehumaine&task=request&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Demandes"><i class="fa fa-envelope"></i>  Demandes</a>
																	<a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Absences"><i class="fa fa-file-alt"></i> Absences</a>
																	<a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-brown" data-toggle="tooltip" data-placement="top" data-original-title="Bonus"><i class="fa fa-money-bill"></i> Bonus</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
																	<a href="javascript:void(0);" class="dropdown-item text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $resourcehumaine->getId(); ?>"><i class="far fa-trash-alt"></i> Supprimer</a>
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
							<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
								<div class="table-responsive list-box">
									<table class="table table-stripped table-center table-hover datatable">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Matricule</th>
												<th>Référence pointage</th>
												<th>Nom complet</th>
												<th>Téléphone</th>
												<th>Date début</th>
												<th>Date fin</th>
												<th>Fonction</th>
												<th class="text-right">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($resources_humaines_stagaire as $resourcehumaine): ?>
												<tr class="<?=$resourcehumaine->getEndDate() ? 'bg-danger-light' : null?>">
													<td><?php echo $resourcehumaine->getId(); ?></td>
													<td><?php echo $resourcehumaine->getReference(); ?></td>
													<td><?php echo $resourcehumaine->getReferencePointage(); ?></td>
													<td>
														<?php $photoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
														<h2 class="table-avatar">
															<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Fournisseur Image"> <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getlastName(); ?></a>
														</h2>
													</td>
													<td><?php echo $resourcehumaine->getPhone(); ?></td>
													<td><?php echo normaldate($resourcehumaine->getStartDate()); ?></td>
													<td><?php echo normaldate($resourcehumaine->getEndDate()); ?></td>
													<td><?php echo $resourcehumaine->getFunction(); ?></td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i> Afficher</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-warning" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i> Modifier</a>
																	<a href="index.php?option=com_resourcehumaine&task=duplicate&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Dupliquer"><i class="fa fa-file"></i> Dupliquer</a>
																	<?php $fileresourcehumaine = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());?>
																	<a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-info" data-toggle="tooltip" data-placement="top" data-original-title="Fichiers"><i class="fa fa-file"></i> Fichiers <?php if(sizeof($fileresourcehumaine)<=0): ?> <span class="point text-danger"></span><?php endif;?></a>
																	<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Bulletin de paie"><i class="fa fa-file"></i> Bulletin de paie </a>
																	<a href="index.php?option=com_resourcehumaine&task=request&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Demandes"><i class="fa fa-envelope"></i>  Demandes</a>
																	<a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Absences"><i class="fa fa-file-alt"></i> Absences</a>
																	<a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-brown" data-toggle="tooltip" data-placement="top" data-original-title="Bonus"><i class="fa fa-money-bill"></i> Bonus</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
																	<a href="javascript:void(0);" class="dropdown-item text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $resourcehumaine->getId(); ?>"><i class="far fa-trash-alt"></i> Supprimer</a>
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
							<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
								<div class="table-responsive list-box">
									<table class="table table-stripped table-center table-hover datatable">
										<thead class="thead-light">
											<tr>
												<th>ID</th>
												<th>Matricule</th>
												<th>Référence pointage</th>
												<th>Nom complet</th>
												<th>Téléphone</th>
												<th>Date début</th>
												<th>Date fin</th>
												<th>Fonction</th>
												<th class="text-right">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($resources_humaines_periode_de_test as $resourcehumaine): ?>
												<tr class="<?=$resourcehumaine->getEndDate() ? 'bg-danger-light' : null?>">
													<td><?php echo $resourcehumaine->getId(); ?></td>
													<td><?php echo $resourcehumaine->getReference(); ?></td>
													<td><?php echo $resourcehumaine->getReferencePointage(); ?></td>
													<td>
														<?php $photoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
														<h2 class="table-avatar">
															<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Fournisseur Image"> <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getlastName(); ?></a>
														</h2>
													</td>
													<td><?php echo $resourcehumaine->getPhone(); ?></td>
													<td><?php echo normaldate($resourcehumaine->getStartDate()); ?></td>
													<td><?php echo normaldate($resourcehumaine->getEndDate()); ?></td>
													<td><?php echo $resourcehumaine->getFunction(); ?></td>
													<td class="text-right">
														<div class="dropdown dropdown-action">
															<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
															<div class="dropdown-menu dropdown-menu-right">
																<?php if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=show&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Afficher"><i class="fa fa-eye"></i> Afficher</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
																	<a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-warning" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i> Modifier</a>
																	<a href="index.php?option=com_resourcehumaine&task=duplicate&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-black" data-toggle="tooltip" data-placement="top" data-original-title="Dupliquer"><i class="fa fa-file"></i> Dupliquer</a>
																	<?php $fileresourcehumaine = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());?>
																	<a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-info" data-toggle="tooltip" data-placement="top" data-original-title="Fichiers"><i class="fa fa-file"></i> Fichiers <?php if(sizeof($fileresourcehumaine)<=0): ?> <span class="point text-danger"></span><?php endif;?></a>
																	<a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Bulletin de paie"><i class="fa fa-file"></i> Bulletin de paie </a>
																	<a href="index.php?option=com_resourcehumaine&task=request&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Demandes"><i class="fa fa-envelope"></i>  Demandes</a>
																	<a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Absences"><i class="fa fa-file-alt"></i> Absences</a>
																	<a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="dropdown-item text-brown" data-toggle="tooltip" data-placement="top" data-original-title="Bonus"><i class="fa fa-money-bill"></i> Bonus</a>
																<?php endif; ?>
																<?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
																	<a href="javascript:void(0);" class="dropdown-item text-danger delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $resourcehumaine->getId(); ?>"><i class="far fa-trash-alt"></i> Supprimer</a>
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
	</div>
</div>
<!-- /Page Wrapper -->

<!-- Add Category Modal -->
<div id="div-export-humanresource" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title mr-auto">Exporter par mois</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<form method="post" action="components/com_resourcehumaine/controleurs/router.php?task=exportAbsenceFile">
							<div class="row">
								<div class="col-12">
									<label class="form-label">Mois<span class="text-danger"> * </span></label>
									<div class="form-group">
										<input type="month" name="month" class="form-control" required>
									</div>								
								</div>
								<div class="col-12">
									<div class="form-group">
										<button type="submit" class="btn btn-primary">Valider</button>
									</div>								
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Add Category Modal -->

<script type="text/javascript">
	$(function() {

		var msgsucces = "Resource humaine supprimé avec succès";

		$(document).on("click", ".delete", function() {
			event.preventDefault();
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_resourcehumaine/controleurs/router.php?task=deleteResourceHumaine", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().parent().parent().remove()
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