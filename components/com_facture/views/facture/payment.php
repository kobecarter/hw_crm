<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Factures</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_facture">Factures</a></li>
						<li class="breadcrumb-item active">Reglement</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<div class="row">
							<div class="col">
								<h4 class="card-title">Liste des reglements facture #<?php echo $facture->getNumero(); ?></h4>
							</div>
							<?php if ($_SESSION['user']->hasDroit('add', 'com_facture')) :?>
    							<div class="col-auto">
    								<a href="javascript:void(0);" class="btn btn-outline-success btn-sm paymentForm" data-id="0">Ajouter payment</a>
    							</div>
							<?php endif;?>
						</div>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Montant</th>
										<th>Méthode</th>
										<th>Date réception</th>
										<th>Date validation</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($payments as $key=> $payment) : ?>
										<tr>
											<td><?php echo $payment->getId(); ?></td>
											<td><?php echo number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise(); ?></td>
											<td><?php echo $payment->getMethodePayment(); ?></td>
											<td data-sort="<?= strtotime($payment->getDatePayment())?>"><?php echo normaldate($payment->getDatePayment()); ?></td>
											<td data-sort="<?= strtotime($payment->getDateValidation())?>"><?php echo normaldate($payment->getDateValidation()); ?></td>
											<td class="text-right">
											    <?php if($payment->getRegImg() != ''): ?>
													<a href="images/reglements/<?php echo $payment->getRegImg(); ?>" data-fancybox class="btn btn-sm btn-white text-success mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Reglement"><i class="fa fa-file-alt"></i></a> 
												<?php endif; ?>
												<?php if ($_SESSION['user']->hasDroit('view', 'com_facture') && !$facture->isGlobalPdfAllowed($payments)) :?>
													<a class="btn btn-sm btn-white text-success mr-2"  href="components/com_facture/controleurs/router.php?task=pdfPayment&id=<?php echo $payment->getId(); ?>&index=<?php echo $key+1; ?>" data-toggle="tooltip" data-placement="top" data-original-title="Facture" target="_blank"><i class="far fa-file-pdf"></i></a>
												<?php endif;?>
											    <?php if ($_SESSION['user']->hasDroit('edit', 'com_facture')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white text-warning mr-2 paymentForm" data-toggle="tooltip" data-placement="top" data-original-title="Modifier" data-id="<?= $payment->getId(); ?>"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif;?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_facture')) :?>
												    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $payment->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

<!-- Add Category Modal -->
<div id="dialog-custom" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
<!-- /Add Category Modal -->

<script type="text/javascript">
	$(function() {

		var msgsucces = "Paiement supprimé avec succès";

		$(document).on("click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_facture/controleurs/router.php?task=deletePayment", order, function(theResponse) {
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

		$(document).on("click", ".paymentForm", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			var title = id != '0' ? 'Modifier paiement' : 'Ajouter paiement';
			var order = 'id=' + id + '&id_facture=<?php echo $facture->getId(); ?>';
			$.post("components/com_facture/controleurs/router.php?task=paymentForm", order, function(theResponse) {
				$(".modal-title").html(title);
				$(".modal-body").html(theResponse);

				$("#dialog-custom").modal('show');
			})
		})

	});
</script>