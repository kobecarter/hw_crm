<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Factures</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Factures</li>
					</ul>
				</div>
				<div class="col-auto">
				    <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
    					<a href="index.php?option=com_facture&task=archive" class="btn btn-warning text-white mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Archive">
    						<i class="fas fa-archive"></i>
    					</a>
    					<a href="index.php?option=com_facture&task=unpaid" class="btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Factures impayées">
    						<i class="fa fa-file-invoice"></i>
    					</a>
					<?php endif?>
					<?php if ($_SESSION['user']->hasDroit('add', 'com_facture')) :?>
    					<!--<a href="index.php?option=com_facture&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter facture">
    						<i class="fas fa-plus"></i>
    					</a>-->
					<?php endif;?>
					<?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
    					<a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
    						<i class="fas fa-filter"></i>
    					</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="nav nav-tabs pill-tabs" role="tablist">
				<a class="nav-link" href="index.php?option=com_facture&task=client" role="tab">Clients <span class="badge badge-pill ml-1"><?php echo sizeof($clients); ?></span></a>
				<a class="nav-link" href="index.php?option=com_facture&task=devis" role="tab">Devis <span class="badge badge-pill ml-1"><?php echo sizeof($deviss); ?></span></a>
				<a class="nav-link" href="index.php?option=com_facture&task=contract" role="tab">Contrats <span class="badge badge-pill ml-1"><?php echo sizeof($contracts); ?></span></a>
				<a class="nav-link active" href="index.php?option=com_facture&task=facture" role="tab">Factures <span class="badge badge-pill ml-1"><?php echo sizeof($allFactures); ?></span></a>
				<a class="nav-link" href="index.php?option=com_facture&task=paiement" role="tab">Paiements <span class="badge badge-pill ml-1"><?php echo sizeof($payments); ?></span></a>
			</div>
		</div>

		<!-- Search Filter -->
		<div id="filter_inputs" class="card filter-card">
			<div class="card-body pb-0">
				<form method="post" action="components/com_facture/controleurs/router.php?task=filterFacture" id="filterFacture">
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label>Date début</label>
								<div class="cal-icon">
									<input type="text" class="form-control datetimepicker" required name="from" id="from" value="<?php if (isset($_GET['from'])) echo normaldate($_GET['from']); ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label>Date fin</label>
								<div class="cal-icon">
									<input type="text" class="form-control datetimepicker" required name="to" id="to" value="<?php if (isset($_GET['to'])) echo normaldate($_GET['to']); ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">
							<button type="submit" name="" class="btn btn-primary submit" style="margin-top: 32px;"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>
							<a href="#0" class="btn btn-primary export" style="margin-top: 32px;"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /Search Filter -->
		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des factures <?php if($client) echo ' - ' . $client->getRaisonSocial(); ?></h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable datatable-invoice" data-order="[]">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Ajouté par</th>
										<th>Édité par</th>
										<th>Numéro</th>
										<th>Client</th>
										<th></th>
										<th>Date</th>
										<th>Montant</th>
										<th>Reste</th>
										<th>Status</th>
										<th>Send Mail</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($factures as $facture) : ?>
										<?php
										$payments = payment::findAll($facture->getId());
										$nbrPayment = sizeof($payments);
										if($facture->getStatu() == 3){
										    $statu = '<span class="badge bg-primary-light">Litige ('.$nbrPayment.')</span>';
										}else {
										    if($facture->getTotal() == $facture->getReste()){
    											$statu = '<span class="badge bg-danger-light">Impayée ('.$nbrPayment.')</span>';
    										}elseif($facture->getTotal() > $facture->getReste() && $facture->getReste() > 0){	
    											$statu = '<span class="badge bg-warning-light">Payée partialement ('.$nbrPayment.')</span>';
    										}elseif($facture->getReste() <= 0){
    											$statu = '<span class="badge bg-success-light">Payée ('.$nbrPayment.')</span>';
    										}
										}

										$nom = $facture->getClient()->getRaisonSocial() != '' ? $facture->getClient()->getRaisonSocial() : $facture->getClient()->getNom() . ' ' . $facture->getClient()->getPrenom();	
										?>
										<tr>
											<td><?php echo $facture->getId(); ?></td>
											<td><?php echo $facture->getUserAdded()->getNom(); ?></td>
											<td><?php echo $facture->getUserEdited()->getNom(); ?></td>
											<td><a href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>">#<?php echo $facture->getNumero(); ?></a></td>
											<td>
												<?php $photoLink = $facture->getClient()->getPhoto() != '' ? "images/clients/" . $facture->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
												<h2 class="table-avatar">
													<a href="<?php echo $photoLink; ?>"data-fancybox><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $nom; ?></a>
												</h2>
											</td>
											<td><?php echo $facture->isAvoir() ? '<span class="badge bg-info-light">avoir</span>' : ''; ?></td>
											<td data-sort="<?= strtotime($facture->getDateFacture())?>"><?php echo normaldate($facture->getDateFacture()); ?></td>
											<td><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
											<td><?php echo number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
											<td><?php echo $statu; ?></td>
											<td><a onclick="return confirm('Êtes-vous sûr de vouloir envoyer ce fichier?')" href="components/com_facture/controleurs/router.php?task=sendViaMailFacture&id=<?php echo $facture->getId(); ?>" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi de facture via Mail" data-id="<?= $facture->getId(); ?>" target="_blank"><i class="far fa-paper-plane"></i></td>
											<td class="text-right">
												<div class="dropdown dropdown-action">
													<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
													<div class="dropdown-menu dropdown-menu-right">
													    <?php if ($_SESSION['user']->hasDroit('edit', 'com_facture')) :?>
														    <a class="dropdown-item text-warning" href="index.php?option=com_facture&task=edit&id=<?php echo $facture->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
														<?php endif;?>
														<?php if ($_SESSION['user']->hasDroit('add', 'com_facture') || $_SESSION['user']->hasDroit('edit', 'com_facture')) :?>
														    <a class="dropdown-item text-secondary" href="index.php?option=com_facture&task=avoir&id=<?php echo $facture->getId(); ?>"><i class="fa fa-file-invoice-dollar mr-3"></i>Avoir</a>
														<?php endif;?>
														<?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
														    <a class="dropdown-item text-info" href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
														<?php endif;?>
														<?php if ($_SESSION['user']->hasDroit('delete', 'com_facture')) :?>
														    <a class="dropdown-item text-danger delete" href="javascript:void(0);" data-id="<?php echo $facture->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
                                                        <?php endif;?>
                                                        <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
														    <a class="dropdown-item text-success" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>"><i class="far fa-money-bill-alt mr-2"></i>Reglement</a>
															<?php if($facture->isGlobalPdfAllowed($payments)): ?>
														    <a class="dropdown-item text-danger"  href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
															<?php endif; ?>
														<?php endif;?>
														<?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) :?>
    														<?php if($facture->getDevis()->getId() != 0):?>
    														    <a class="dropdown-item text-info" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $facture->getDevis()->getId();?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>Devis</a>
    														<?php endif;?>
														<?php endif;?>
													</div>
												</div>
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
		<div class="page-footer">
		    <div class="card card-table">
				<div class="card-header">
			        <div class="row align-items-center">
        				<div class="col-12">
        					<h3 class="page-title">Informations</h3>
        				</div>
			        </div>
			    </div>
			    <div class="card-body">
			        <div class="row align-items-center">
        				<div class="col-12 p-4">
        				    <?= $agence->getInformation();?>
        				</div>
			        </div>
			    </div>
			</div>
			<hr>
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