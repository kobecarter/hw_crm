<div class="card card-table">
	<div class="card-header">
		<h4 class="card-title">Liste des devis</h4>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3"></div>
		<div class="table-responsive list-box">
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
					<?php if ($devis->getId() == 0) continue; // référence orpheline (devis introuvable) : on ignore la ligne plutôt que planter ?>
					<?php
					switch($devis->getStatu()){
						case '1' : $statu = '<span class="badge bg-success-light">Envoyé</span>'; break;
						case '2' : $statu = '<span class="badge bg-success-light">Accepté</span>'; break;
						case '3' : $statu = '<span class="badge bg-primary-light">Contrat en attente de signature</span>'; break;
						case '4' : $statu = '<span class="badge bg-success text-white">Paiement effectué</span>'; break;
						case '4' : $statu = '<span class="badge bg-danger text-white">Paiement Refusé</span>'; break;
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
							<?php elseif($devis->getFacture()->getId() == 0 && in_array($devis->getStatu(),[1,3])): ?>
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
									<?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) :?>
										<a class="dropdown-item text-danger" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $devis->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
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