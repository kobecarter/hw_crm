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
												<?php if ($_SESSION['user']->hasDroit('add', 'com_relance') && $facture->getReste() > 0 && $payment->getRegImg() == '') :?>
													<a href="javascript:void(0);" class="btn btn-sm btn-white text-primary mr-2 relance-payment-btn" data-id="<?= $payment->getId(); ?>" data-montant="<?= $payment->getMontant(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Envoyer une relance de paiement"><i class="fa fa-paper-plane"></i></a>
												<?php endif;?>
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

<!-- Confirmation avant l'envoi d'une relance de paiement manuelle (bouton "enveloppe" sur chaque
     ligne de règlement ci-dessus) - un email réel part vers le client, donc jamais un envoi
     silencieux au premier clic (même patron .tva-confirm-modal que les autres confirmations de
     l'app, pas de confirm() natif). -->
<?php if ($_SESSION['user']->hasDroit('add', 'com_relance') && $facture->getReste() > 0) :
	$clientRelance = $facture->getClient();
	$nomClientRelance = $clientRelance ? trim($clientRelance->getPrenom() . ' ' . $clientRelance->getNom()) : '';
?>
<div id="relancePaymentModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="tva-confirm-icon"><i class="fa fa-paper-plane"></i></div>
				<h5 class="modal-title mt-3">Envoyer une relance de paiement ?</h5>
			</div>
			<div class="modal-body text-center">
				<p class="mb-0">
					Un email sera envoyé à <strong><?= htmlspecialchars($nomClientRelance) ?></strong> pour lui rappeler
					le règlement de <strong id="relancePaymentMontantTexte">—</strong>
					sur la facture <strong>#<?= $facture->getNumero() ?></strong>.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-primary" id="relancePaymentConfirmerBtn"><span class="spinner-border spinner-border-sm mr-2 relance-payment-loading" style="display:none;"></span><i class="fa fa-paper-plane mr-1"></i> Envoyer</button>
			</div>
		</div>
	</div>
</div>
<?php endif;?>

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
				// Scopé à #dialog-custom : ".modal-title"/".modal-body" non scopés matchaient AUSSI
				// le Centre d'alertes global (#alertCenterModal, includes/tpl/bottom.php, présent sur
				// toute page) - le formulaire de paiement (avec son input#edit_img) s'y dupliquait en
				// plus de la popup visible, créant deux id="edit_img" dans le DOM. Le navigateur
				// résolvait alors le clic sur l'icône crayon vers l'input caché du Centre d'alertes au
				// lieu de celui de la popup visible : le fichier ne partait jamais avec le formulaire.
				$("#dialog-custom .modal-title").html(title);
				$("#dialog-custom .modal-body").html(theResponse);

				$("#dialog-custom").modal('show');
			})
		})

		// Relance de paiement manuelle - ouvre la confirmation (montant DE CE RÈGLEMENT précis,
		// pas le reste dû global de la facture), puis envoie au clic sur "Envoyer" (voir
		// relance::envoyerRelanceManuelle()). Un seul modal partagé par toutes les lignes, mais son
		// contenu et l'id_payment envoyé changent selon le bouton cliqué (data-id/data-montant).
		var relancePaymentIdCourant = null;
		$(document).on("click", ".relance-payment-btn", function() {
			var $btn = $(this);
			relancePaymentIdCourant = $btn.data('id');
			var montant = parseFloat($btn.data('montant')) || 0;
			$('#relancePaymentMontantTexte').text(montant.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' <?php echo $facture->getDevise(); ?>');
			$('#relancePaymentModal').modal('show');
		});

		$(document).on("click", "#relancePaymentConfirmerBtn", function() {
			var $btn = $(this);
			$btn.prop('disabled', true);
			$btn.find('.relance-payment-loading').show();
			$.post("components/com_relance/controleurs/router.php?task=envoyerRelanceManuelle", { id_payment: relancePaymentIdCourant }, function(response) {
				$('#relancePaymentModal').modal('hide');
				$btn.prop('disabled', false);
				$btn.find('.relance-payment-loading').hide();
				if (response && response.success) {
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès!</strong> Relance envoyée avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> ' + ((response && response.message) || "Erreur lors de l'envoi de la relance") + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}, 'json').fail(function() {
				$('#relancePaymentModal').modal('hide');
				$btn.prop('disabled', false);
				$btn.find('.relance-payment-loading').hide();
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur!</strong> Impossible de contacter le serveur.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			});
		});

	});
</script>