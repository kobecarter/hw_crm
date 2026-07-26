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
					<h3 class="page-title">Relances de client</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_relance">Relances</a></li>
						<li class="breadcrumb-item active">Relances de client</li>
					</ul>
				</div>
				<div class="col-auto">
				    
					<?php if($_SESSION['user']->hasDroit('add', 'com_relance')) :?>
    					<a href="index.php?option=com_relance&task=add&id_client=<?php echo $client->getId();?>" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter relance">
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
						<h4 class="card-title">Liste des relances du client <?php echo $client->getRaisonSocial(); ?> N°<?php echo $client->getId();?></h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Facture</th>
										<th>Type</th>
										<th>Remarque</th>
										<th>Date relance</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($relances as $relance) : ?>
										<?php
										if($relance->getFacture()->getId() != 0)
											$nom = $relance->getFacture()->getNumero() . " - " . normaldate($relance->getFacture()->getDateFacture()) . ' ('. number_format($relance->getFacture()->getTotal(), 2, ',', ' ') . ' ' . $relance->getFacture()->getDevise() .')';
										else
											$nom = '--';
										
											$rowClass = '';
										if ($relance->getDaysLeft() < 3) {
											$rowClass = 'table-warning';
										}
										if ($relance->getDaysLeft() < 0) {
											$rowClass = 'table-danger';
										}
										?>
										<tr class="<?php echo $rowClass; ?>">
											<td><?php echo $relance->getId(); ?></td>
											<td><?php echo $nom; ?></td>
											<td><?php echo $relance->getType(); ?></td>
											<td><?php

												$remark = $relance->getRemarque();
												if(strlen($remark)>50){
													$remark = substr($remark, 0, 50);
													echo $remark . ' ...';
												}else{
													echo $remark;
												}
												
												?>
											</td>
											<td><?php
												$date=date_create($relance->getDate());
												echo date_format($date,"d/m/Y H:i"); 
												
											 ?></td>
											<td class="text-right">
												<?php if ($relance->getDaysLeft() <= 0): ?>
												<a href="javascript:void(0);" data-id="<?php echo $relance->getId(); ?>" class="btn btn-sm btn-white text-success mr-2 prolonge" data-toggle="tooltip" data-placement="top" data-original-title="Prolongée"><i class="fa fa-clock"></i></a> 
												<?php endif; ?>
												<?php if($relance->isTraite()): ?>
													<a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Relance traitée"><i class="fa fa-check"></i></a> 
												<?php endif; ?>

												<?php if($relance->getPhoto() != ''): ?>
													<a href="images/relances/<?php echo $relance->getPhoto(); ?>" data-fancybox class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Justificatif"><i class="fa fa-file-alt"></i></a> 
												<?php endif; ?>

                                                <?php if($_SESSION['user']->hasDroit('edit', 'com_relance')) :?>
												    <a href="index.php?option=com_relance&task=edit&id=<?= $relance->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif;?>
                                                <?php if($_SESSION['user']->hasDroit('delete', 'com_relance')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $relance->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

		var msgsucces = "Relance supprimé avec succès";
		$(document).on("click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_relance/controleurs/router.php?task=deleteRelance", order, function(theResponse) {
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