<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Devis</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Devis</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_devis')) :?>
    				<div class="col-auto">
    					<a href="index.php?option=com_devis&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter devis">
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
						<h4 class="card-title">Liste des devis</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable" data-order="[]">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Numéro</th>
										<th>Client</th>
										<th>Date</th>
										<th>Montant</th>
										<th>Contrat</th>
										<th>Facture</th>
										<th>Status</th>
										<th>Envoyer Mail</th>
										<th></th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($deviss as $devis): ?>
									<?php
									switch($devis->getStatu()){
										case '1' : $statu = '<span class="badge bg-success-light">Envoyé</span>'; break;
										case '2' : $statu = '<span class="badge bg-success-light">Accepté</span>'; break;
										case '3' : $statu = '<span class="badge bg-primary-light">Contrat en attente de signature</span>'; break;
										case '4' : $statu = '<span class="badge bg-success text-white">Paiement effectué</span>'; break;
										case '5' : $statu = '<span class="badge bg-danger text-white">Devis Refusé</span>'; break;
										default : $statu = '<span class="badge bg-warning-light">Brouillon</span>'; break;	
									}

									$activity = "Ajouté par " . $devis->getUserAdded()->getNom() . " | Modifié par " . $devis->getUserEdited()->getNom();
									?>
									<tr>
										<td><?php echo $devis->getId(); ?></td>
										<td><a href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>">#<?php echo $devis->getNumero(); ?></a></td>
										<td>
											<?php $photoLink = $devis->getClient()->getPhoto() != '' ? "images/clients/" . $devis->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
											<h2 class="table-avatar">
												<a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $devis->getClient()->getRaisonSocial(); ?></a>
											</h2>
										</td>
										<td data-sort="<?= strtotime($devis->getDateDevis())?>"><?php echo normaldate($devis->getDateDevis()); ?></td>
										<td><?php echo number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise(); ?></td>
										<td>
											<?php
											$contract = contract::findByDevis($devis->getId(),$_SESSION['agence'],$devis->getLangue());
											if($contract->getId() != 0): ?>
											    <a href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?php echo $contract->getId(); ?>" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Contract" target="_blank"><i class="fa fa-file"></i></a> 
											<?php endif; ?>
										</td>
										<td>
											<?php if($devis->getFacture()->getId() != 0): ?>
											    <a href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $devis->getFacture()->getId(); ?>" class="btn btn-sm btn-white text-info" data-toggle="tooltip" data-placement="top" data-original-title="Facture" target="_blank"><i class="fa fa-file-alt"></i></a> 
											<?php elseif($devis->getFacture()->getId() == 0 && in_array($devis->getStatu(),[4])): ?>
											    <a class="dropdown-item text-success create-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-file-invoice mr-2"></i>+</a>
											<?php endif; ?>
										</td>
										<td><?php echo $statu; ?></td>
										<td><a onclick="return confirm('Êtes-vous sûr de vouloir envoyer ce fichier?')" href="components/com_devis/controleurs/router.php?task=sendViaMailDevis&id=<?php echo $devis->getId(); ?>" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi de devis via Mail" data-id="<?= $devis->getId(); ?>" target="_blank"><i class="far fa-paper-plane"></i></td>
										<td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
										<td class="text-right">
											<div class="dropdown dropdown-action">
												<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
												<div class="dropdown-menu dropdown-menu-right">
												    <?php if ($_SESSION['user']->hasDroit('edit', 'com_devis')) :?>
													    <a class="dropdown-item text-warning" href="index.php?option=com_devis&task=edit&id=<?php echo $devis->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
													<?php endif;?>
													<?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) :?>
													    <a class="dropdown-item text-info" href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
													<?php endif;?>
													<?php if ($_SESSION['user']->hasDroit('delete', 'com_devis')) :?>
													    <a class="dropdown-item text-danger delete" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
													<?php endif;?>
													<?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) :?>
													    <!-- <a class="dropdown-item text-danger" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $devis->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a> -->
													    <a class="dropdown-item text-danger" href="components/com_devis/controleurs/router.php?task=pdfDevisTexte&id=<?php echo $devis->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
													<?php endif;?>
													<?php if ($_SESSION['user']->hasDroit('add', 'com_facture') || $_SESSION['user']->hasDroit('edit', 'com_facture')) :?>
													    <?php
													    $facture = facture::findByDevis($devis->getId(),$_SESSION['agence']);
													    if($devis->getStatu() == 4 && !$facture):
													    ?>
													    <a class="dropdown-item text-success create-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-file-invoice mr-2"></i>+ Facture</a>
													    <?php endif;?>
													    <a class="dropdown-item text-info duplicate-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-copy mr-2"></i>Dupliquer</a>
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
	</div>			
</div>
<!-- /Page Wrapper -->

<script type="text/javascript">
$(function () {
	
	var msgsucces = "";
	
	$(document).on( "click", ".delete", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_devis/controleurs/router.php?task=deleteDevis", order, function (theResponse) {
			    console.log(theResponse)
			    msgsucces = "Devis supprimé avec succès"
				if (parseInt(theResponse) == 1) {
					
					$btn.parent().parent().parent().parent().addClass("table-danger");
					setTimeout(function () {
						$btn.parent().parent().parent().parent().remove()
					}, 1000);
					
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})
	

	$(document).on( "click", ".create-invoice", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_devis/controleurs/router.php?task=createInvoice", order, function (theResponse) {
			    msgsucces = "Facture créée avec succès";
				if (parseInt(theResponse) == 1) {
					
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					setTimeout(function () {
					    location.href="index.php?option=com_facture"
					},1000)
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})

	$(document).on( "click", ".duplicate-invoice", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_devis/controleurs/router.php?task=duplicateDevis", order, function (theResponse) {
			    msgsucces = "Devis dupliqué avec succès";
				if (parseInt(theResponse) == 1) {
					
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					$(document).reload();
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})
});
</script>