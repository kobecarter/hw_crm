<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Règlements</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_facture">Factures</a></li>
						<li class="breadcrumb-item active">Règlements</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Facture</th>
										<th>Client</th>
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
											<td><?php echo $payment->getFacture()->getNumero(); ?></td>
											<td><?php
												$nom = $payment->getFacture()->getClient()->getRaisonSocial() != '' ? $payment->getFacture()->getClient()->getRaisonSocial() : $payment->getFacture()->getClient()->getNom() . ' ' . $payment->getFacture()->getClient()->getPrenom();
												echo $nom; 
											?></td>
											<td><?php echo number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise(); ?></td>
											<td><?php echo $payment->getMethodePayment(); ?></td>
											<td data-sort="<?= strtotime($payment->getDatePayment())?>"><?php echo normaldate($payment->getDatePayment()); ?></td>
											<td data-sort="<?= strtotime($payment->getDateValidation())?>"><?php echo normaldate($payment->getDateValidation()); ?></td>
											<td class="text-right">
											    <?php if($payment->getRegImg() != ''): ?>
													<a href="images/reglements/<?php echo $payment->getRegImg(); ?>" data-fancybox class="btn btn-sm btn-white text-success mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Reglement"><i class="fa fa-file-alt"></i></a> 
												<?php endif; ?>
												<?php if ($_SESSION['user']->hasDroit('view', 'com_facture') && !$payment->getFacture()->isGlobalPdfAllowed()) :?>
													<a class="btn btn-sm btn-white text-success mr-2"  href="components/com_facture/controleurs/router.php?task=pdfPayment&id=<?php echo $payment->getId(); ?>&index=<?php echo $key+1; ?>" target="_blank"><i class="far fa-file-pdf"></i></a>
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
	});
</script>