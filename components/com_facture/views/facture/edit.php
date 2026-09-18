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
					<button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target="#envoyerFactureEmailModal" data-original-title="Envoyer par email" title="Envoyer par email">
						<i class="far fa-paper-plane"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<!-- Popup "Envoyer par email" - même habillage que les autres popups de l'app
		     (.tva-confirm-modal, cf. #devis-acompte-modal dans com_devis/views/devis/form.php). -->
		<div id="envoyerFactureEmailModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<div class="tva-confirm-icon"><i class="far fa-paper-plane"></i></div>
						<h5 class="modal-title mt-3">Envoyer la facture par email</h5>
					</div>
					<div class="modal-body text-center">
						<p>Envoyer la facture N°<?php echo $facture->getNumero(); ?> à <?php echo htmlspecialchars($facture->getClient()->getEmail()); ?> ?</p>
						<div class="form-group text-left">
							<label>Ajouter une adresse en copie (CC) - facultatif</label>
							<input type="email" class="form-control" id="envoyerFactureEmailCc" placeholder="exemple@domaine.com">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
						<button type="button" id="envoyerFactureEmailConfirmer" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2" style="display:none;"></span>Envoyer</button>
					</div>
				</div>
			</div>
		</div>
		<!-- /Popup "Envoyer par email" -->

		<script>
		$(function () {
			$('#envoyerFactureEmailConfirmer').on('click', function () {
				var $btn = $(this);
				var cc = $('#envoyerFactureEmailCc').val().trim();
				$btn.prop('disabled', true).find('.spinner-border').show();
				$.post('components/com_facture/controleurs/router.php?task=envoyerFactureEmailAvecCc', {
					id: <?php echo $facture->getId(); ?>,
					cc: cc
				}, function (response) {
					$btn.prop('disabled', false).find('.spinner-border').hide();
					$('#envoyerFactureEmailModal').modal('hide');
					if (response && response.success) {
						afficherMessageStyle('Facture envoyée par email avec succès.', 'success');
						$('#envoyerFactureEmailCc').val('');
					} else {
						afficherMessageStyle((response && response.message) || "Erreur lors de l'envoi", 'error');
					}
				}, 'json').fail(function () {
					$btn.prop('disabled', false).find('.spinner-border').hide();
					$('#envoyerFactureEmailModal').modal('hide');
					afficherMessageStyle("Erreur réseau lors de l'envoi", 'error');
				});
			});
		});
		</script>

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