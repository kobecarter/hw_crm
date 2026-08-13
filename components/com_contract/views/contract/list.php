<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Contrats</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Contrats</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_contract')) :?>
    				<div class="col-auto">
    					<a href="index.php?option=com_contract&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter contrat">
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
						<h4 class="card-title">Liste des contrats</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Devis</th>
										<th>Client</th>
										<th>Titre</th>
										<th>Duration</th>
										<th>Garantie</th>
										<th>Ville</th>
										<th>Nombre de paiement</th>
										<th>Date</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($contracts as $contract): ?>
									<tr>
										<td><?php echo $contract->getId(); ?></td>
										<td><?php echo $contract->getDevis()->getNumero(); ?></td>
										<td>
                                            <?php $photoLink = $contract->getDevis()->getClient()->getPhoto() != '' ? "images/clients/" . $contract->getDevis()->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                            <h2 class="table-avatar">
                                                <a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $contract->getDevis()->getClient()->getRaisonSocial(); ?></a>
                                            </h2>
                                        </td>
										<td><?php echo $contract->getTitre(); ?></td>
										<td><?php echo $contract->getDuration(); ?></td>
										<td><?php echo $contract->getGarantie(); ?></td>
										<td><?php echo $contract->getVille(); ?></td>
										<td><b><?php echo $contract->getNombreDePaiement(); ?></b></td>
										<td data-sort="<?= strtotime($contract->getDate())?>"><?php echo normaldate($contract->getDate()); ?></td>
										<td class="text-right">
											<?php
											switch($contract->getStatus()){
												case 1 : $status = "En préparation"; break;
												case 2 : $status = "En attente de signature"; break;
												case 3 : $status = "Signé"; break;
												case 4 : $status = "Refusé"; break;
												default : $status = ""; break;
											}
											?>
											<a href="javascript:void(0);" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $status; ?>"><i class="fa fa-file-signature"></i></a>

											<?php if ($_SESSION['user']->hasDroit('edit', 'com_contract')) :?>
											    <a href="index.php?option=com_contract&task=edit&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a> 
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('view', 'com_contract')) :?>
											    <a href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-primary mr-2" data-toggle="tooltip" data-placement="top" data-original-title="PDF"><i class="fa fa-file"></i></a>
											    <a href="components/com_contract/controleurs/router.php?task=docxContract&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Télécharger en Word"><i class="fa fa-file-word"></i></a>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_contract')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $contract->getId(); ?>"><i class="far fa-trash-alt"></i></a>
											<?php endif?>
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

<!-- Popup "Supprimer ce contrat" — même habillage que les autres popups de confirmation
     destructive de l'app (.tva-confirm-modal + variante rouge "charge-doublon-icon"), remplace le
     confirm() natif du navigateur qui s'affichait ici auparavant. -->
<div id="supprimerContratListeModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="charge-doublon-icon"><i class="fa fa-trash"></i></div>
				<h5 class="modal-title mt-3">Supprimer ce contrat ?</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-0" style="font-size:0.9rem;">Cette action est irréversible.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-danger" id="supprimerContratListeConfirmerBtn"><i class="fa fa-trash mr-1"></i> Supprimer</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function () {

	var msgsucces = "Contrat supprimé avec succès";
	var $ligneASupprimer = null;

	$(document).on( "click", ".delete", function() {
		$ligneASupprimer = $(this);
		$('#supprimerContratListeModal').modal('show');
	});

	$('#supprimerContratListeConfirmerBtn').on('click', function () {
		var $confirmerBtn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Suppression...');
		var $btn = $ligneASupprimer;
		var id = $btn.attr("data-id");
		var order = 'id=' + id;
		$.post("components/com_contract/controleurs/router.php?task=deleteContract", order, function (theResponse) {
			$confirmerBtn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Supprimer');
			$('#supprimerContratListeModal').modal('hide');
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
	});
});
</script>