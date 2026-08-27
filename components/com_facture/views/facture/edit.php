<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Modifier facture</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_facture">Factures</a></li>
						<li class="breadcrumb-item active">Modifier facture</li>
					</ul>
				</div>
				<div class="col-auto">
					<a href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Paiement">
						<i class="far fa-money-bill-alt"></i>
					</a>
					<?php if($facture->isGlobalPdfAllowed()): ?>
					<a href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank" class="btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" data-original-title="PDF">
						<i class="far fa-file-pdf"></i>
					</a>
					<?php endif; ?>
					<a href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>" target="_blank" class="btn btn-info mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Afficher">
						<i class="far fa-eye"></i>
					</a>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
					    <div class="mb-5">
					        <?php include("wizard_process.php"); ?>
					    </div>
						<?php include("form.php"); ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Montant</th>
										<th>Méthode</th>
										<th>Date réception</th>
										<th>Date validation</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($payments as $payment) : ?>
										<tr>
											<td><?php echo $payment->getId(); ?></td>
											<td><?php echo number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise(); ?></td>
											<td><?php echo $payment->getMethodePayment(); ?></td>
											<td><?php echo normaldate($payment->getDatePayment()); ?></td>
											<td><?php echo normaldate($payment->getDateValidation()); ?></td>
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