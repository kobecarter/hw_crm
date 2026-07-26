<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Realisations</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Realisations</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_realisation')) :?>
    				<div class="col-auto">
    					<a href="index.php?option=com_realisation&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter realisation">
    						<i class="fas fa-plus"></i>
    					</a>
    				</div>
				<?php endif;?>
			</div>
		</div>

		<!-- Search Filter -->

		<!-- /Search Filter -->

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des realisations</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Photo</th>
										<th>Titre</th>
										<th>Extrait</th>
										<th>Date Ajout</th> 
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($realisations as $realisation) : ?>
										<tr>
											<td><?php echo $realisation->getId(); ?></td>
											<td>
												<?php $photoLink = $realisation->getPhoto() != '' ? "images/realisation/" . $realisation->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
												<h2 class="table-avatar">
													<img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Realisation Image">
												</h2>
											</td>
											<td><?php echo $realisation->getTitre(); ?></td>
											<td><?php echo substr($realisation->getExtrait(), 0, 50) . '..'; ?></td>
											<td data-sort="<?= strtotime($realisation->getDateAdd())?>"><?php echo normaldate($realisation->getDateAdd()); ?></td>
											<td class="text-right">
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_realisation')) :?>
												    <a href="index.php?option=com_realisation&task=edit&id=<?= $realisation->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif;?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_realisation')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $realisation->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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


		var msgsucces = "Realisation supprimée avec succès";

		$(document).on("click", ".delete", function() {
			event.preventDefault();
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_realisation/controleurs/router.php?task=deleteRealisation", order, function(theResponse) {
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
	});
</script>