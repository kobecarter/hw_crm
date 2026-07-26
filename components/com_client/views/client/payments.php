<div class="card card-table">
	<div class="card-header">
		<h4 class="card-title">Liste des paiements</h4>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3"></div>
		<div class="table-responsive list-box">
			<table class="table table-stripped table-center table-hover datatable datatable-invoice" data-order="[]">
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
					<?php foreach ($factures as $facture) : ?>
						<?php $payments = payment::findAll($facture->getId()); ?>
						<?php foreach ($payments as $payment) : ?>
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
									
									<?php if ($_SESSION['user']->hasDroit('edit', 'com_facture')) :?>
										<a href="javascript:void(0);" class="btn btn-sm btn-white text-warning mr-2 paymentForm" data-toggle="tooltip" data-placement="top" data-original-title="Modifier" data-id="<?= $payment->getId(); ?>"><i class="fa fa-pencil-alt"></i></a>
									<?php endif;?>
									<?php if ($_SESSION['user']->hasDroit('delete', 'com_facture')) :?>
										<a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $payment->getId(); ?>"><i class="far fa-trash-alt"></i></a>
									<?php endif;?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Add Category Modal -->
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

		$(document).on("click", ".paymentForm", function() {
			var $btn = $(this);
			var id = $btn.attr("data-id");
			var title = id != '0' ? 'Modifier paiement' : 'Ajouter paiement';
			var order = 'id=' + id + '&id_facture=<?php echo isset($facture) ? $facture->getId() : NULL; ?>';
			$.post("components/com_facture/controleurs/router.php?task=paymentForm", order, function(theResponse) {
				$(".modal-title").html(title);
				$(".modal-body").html(theResponse);

				$("#dialog-custom").modal('show');
			})
		})

	});
</script>