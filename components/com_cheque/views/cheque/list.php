<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Cheques</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Cheques</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_cheque')) :?>
				<div class="col-auto">
					<a href="index.php?option=com_cheque&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter un cheque">
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
						<h4 class="card-title">Liste des Cheques</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable" data-order="[]">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Sens</th>
										<th>Tiers</th>
										<th>N° Chèque</th>
										<th>Montant</th>
										<th>Statut</th>
										<th>Date encaissement</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($cheques as $cheque): ?>
									<?php
									$trClass = "";
									switch($cheque->getStatus()){
										case 'Encaissé' :
											$trClass = "table-success";
											break;
										case 'Pas encore' :
											$trClass = "table-warning";
											break;
										case 'Impayé' :
											$trClass = "table-danger";
											break;
										default :
											$trClass = "";
											break;
									}
									$joursRestants = $cheque->getDaysLeft();
									?>
									<tr class="<?php echo $trClass; ?>">
										<td><?php echo $cheque->getId(); ?></td>
										<td>
											<?php if ($cheque->getType() === 'entrant') :?>
											<span class="badge bg-success-light"><i class="fa fa-arrow-down mr-1"></i>Entrant</span>
											<?php elseif ($cheque->getType() === 'sortant') :?>
											<span class="badge bg-danger-light"><i class="fa fa-arrow-up mr-1"></i>Sortant</span>
											<?php else :?>
											—
											<?php endif;?>
										</td>
										<td>
											<?php echo htmlspecialchars($cheque->getNomTiers()); ?>
											<?php if ($cheque->getTiersType() === 'fournisseur') :?>
											<span class="text-muted" style="font-size:0.78rem;">(fournisseur)</span>
											<?php elseif ($cheque->getTiersType() === 'client') :?>
											<span class="text-muted" style="font-size:0.78rem;">(client)</span>
											<?php endif;?>
										</td>
										<td><?php echo $cheque->getCheckNumber();?></td>
										<td><b><?php echo number_format($cheque->getAmount(), 2, ',', ' ').' '. $cheque->getCurrency() ?></b></td>
										<td><?php echo $cheque->getStatus(); ?></td>
										<td data-sort="<?= strtotime($cheque->getDateEncaissement())?>">
											<?php echo $cheque->getDateEncaissement() ? normaldate($cheque->getDateEncaissement()) : '—'; ?>
											<?php if ($cheque->estEnAttente() && $joursRestants !== null) :?>
												<?php if ($joursRestants < 0) :?>
												<br><span class="badge bg-danger-light" style="font-size:0.7rem;">En retard de <?= abs($joursRestants) ?> j</span>
												<?php elseif ($joursRestants <= 30) :?>
												<br><span class="badge bg-warning-light" style="font-size:0.7rem;">Dans <?= $joursRestants ?> j</span>
												<?php endif;?>
											<?php endif;?>
										</td>
										<td class="text-right">
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_cheque')) :?>
    											<a href="index.php?option=com_cheque&task=edit&id=<?= $cheque->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_cheque')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $cheque->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
$(function () {

	var msgsucces = "Cheque supprimée avec succès";

	$(document).on( "click", ".delete", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_cheque/controleurs/router.php?task=deleteCheque", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {

					$btn.parent().parent().addClass("table-danger");
					setTimeout(function () {
						$btn.parent().parent().remove()
					}, 1000);

					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})
});
</script>
