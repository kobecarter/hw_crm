<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Encaissements</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Encaissements</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des encaissements</h4>
						<div class="text-center mt-4">
							<?php if(isset($_GET['currency_name']) && isset($_GET['color'])) :?><h4 class="text-<?=$_GET['color']?>"><?=number_format($total, 2, ',', ' ').' '.$_GET['currency_name']?></h4> <?php endif;?>
						</div>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable datatable-invoice" data-order="[]">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Numéro</th>
										<th>Client</th>
										<th>Reçue</th>
										<th>Date</th>
										
									</tr>
								</thead>
								<tbody>
									<?php foreach ($payments as $payment) : ?>
										<?php
										$nom = $payment->getFacture()->getClient()->getRaisonSocial() != '' ? $payment->getFacture()->getClient()->getRaisonSocial() : $payment->getFacture()->getClient()->getNom() . ' ' . $payment->getFacture()->getClient()->getPrenom();	
										?>
										<tr>
											<td><?php echo $payment->getId(); ?></td>
											<td><a href="index.php?option=com_facture&task=show&id=<?php echo $payment->getFacture()->getId(); ?>">#<?php echo $payment->getFacture()->getNumero(); ?></a></td>
											<td>
												<?php $photoLink = $payment->getFacture()->getClient()->getPhoto() != '' ? "images/clients/" . $payment->getFacture()->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
												<h2 class="table-avatar">
													<a href="<?php echo $photoLink; ?>"data-fancybox><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $nom; ?></a>
												</h2>
											</td>
											<td><b class="text-success"><?php echo number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise(); ?></b></td>
											<td data-sort="<?= strtotime($payment->getDatePayment())?>"><?php echo normaldate($payment->getDatePayment()); ?></td>
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

        // Export résultat filtre
		$(document).on( "click", ".export", function() {	
			event.preventDefault();
			window.open('components/com_facture/controleurs/router.php?task=exportFacture&from=' + $("#from").val() + '&to=' + $("#to").val(), '_blank'); 
		})
		
		// envoi du filtre en ajax
		$('form#filterFacture').ajaxForm({
			beforeSubmit: function() {
				$("#filterFacture .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				$("#filterFacture .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				$(".list-box").html(theResponse)
			}
		});

		var msgsucces = "Facture supprimée avec succès";

		$(document).on("click", ".delete", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
				$.post("components/com_facture/controleurs/router.php?task=deleteFacture", order, function(theResponse) {
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

		$(".enable").click(function() {
			var btn = $(this);
			var id = btn.attr("data-id");
			var state = btn.attr("data-state");
			var order = 'id=' + id + "&state=" + state;
			$.post("components/com_facture/controleurs/router.php?task=enableClient", order, function(theResponse) {
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