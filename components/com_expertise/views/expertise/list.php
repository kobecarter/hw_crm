<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Expertises</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Expertises</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_expertise')) :?>
    				<div class="col-auto">
    					<a href="index.php?option=com_expertise&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter expertise">
    						<i class="fas fa-plus"></i>
    					</a>
    				</div>
				<?php endif;?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des expertises</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Photo</th>
										<th>Titre</th>
										<th>Extrait</th>
										<th>Ordre</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php
									// echo var_dump($expertises);
									global $siteURL;
									foreach ($expertises as $expertise) : ?>
										<tr>
											<td><?php echo $expertise->getId(); ?></td>
											<td>
												<?php $photoLink = $expertise->getPhoto() ? "images/expertises/" . $expertise->getPhoto() : ""; ?>
												<h2 class="table-avatar">
													<img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="">
												</h2>
											</td>
											<td><?php echo $expertise->getTitre(); ?></td>
											<td><?php

												$extrait = $expertise->getExtrait();
												$extrait = substr($extrait, 0, 50);
												echo $extrait . '..';
												?></td>
											<td><?php echo $expertise->getOrdre(); ?></td>
											<td class="text-right">
												<?php $state = $expertise->isActive() ? 'oui' : 'non'; ?>
												<?php $title = $expertise->isActive() ? 'Actif' : 'Inactif'; ?>
												<?php $color = $expertise->isActive() ? 'text-success' : 'text-danger'; ?>
												<?php $ico = $expertise->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
												<?php if ($_SESSION['user']->hasDroit('edit', 'com_expertise')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $expertise->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
												    <a href="index.php?option=com_expertise&task=edit&id=<?= $expertise->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif;?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_expertise')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $expertise->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

		var msgsucces = "Expertise supprimé avec succès";

		$(document).on("click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_expertise/controleurs/router.php?task=deleteExpertise", order, function(theResponse) {
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

		$(".enable").click(function() {
			var btn = $(this);
			var id = btn.attr("data-id");
			var state = btn.attr("data-state");
			var order = 'id=' + id + "&state=" + state;
			$.post("components/com_expertise/controleurs/router.php?task=enableExpertise", order, function(theResponse) {
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