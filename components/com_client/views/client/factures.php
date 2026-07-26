<div class="card card-table">
	<div class="card-header">
		<h4 class="card-title">Liste des factures</h4>
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
					<th></th>
					<th>Date</th>
					<th>Montant</th>
					<th>Reste</th>
					<th>Statut</th>
					<th>Send Mail</th>
					<th></th>
					<th class="text-right">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($factures as $facture) : ?>
					<?php
					$payments_by_invoice = payment::findAll($facture->getId());
					$nbrPayment = sizeof($payments_by_invoice);

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
					$activity = "Ajouté par " . $facture->getUserAdded()->getNom() . " | Modifié par " . $facture->getUserEdited()->getNom();
					?>
					<tr>
						<td><?php echo $facture->getId(); ?></td>
						<td><a href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>">#<?php echo $facture->getNumero(); ?></a></td>
						<td>
							<?php $photoLink = $facture->getClient()->getPhoto() != '' ? "images/clients/" . $facture->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
							<h2 class="table-avatar">
								<a href="<?php echo $photoLink; ?>" data-fancybox><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $nom; ?></a>
							</h2>
						</td>
						<td><?php echo $facture->isAvoir() ? '<span class="badge bg-info-light">avoir</span>' : ''; ?></td>
						<td data-sort="<?= strtotime($facture->getDateFacture()) ?>"><?php echo normaldate($facture->getDateFacture()); ?></td>
						<td><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
						<td><?php echo number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
						<td><?php echo $statu; ?></td>
						<td><a onclick="return confirm('Êtes-vous sûr de vouloir envoyer ce fichier?')" href="components/com_facture/controleurs/router.php?task=sendViaMailFacture&id=<?php echo $facture->getId(); ?>" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi de facture via Mail" data-id="<?= $facture->getId(); ?>" target="_blank"><i class="far fa-paper-plane"></i></td>
						<td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
						<td class="text-right">
							<div class="dropdown dropdown-action">
								<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
								<div class="dropdown-menu dropdown-menu-right">
									<?php if ($_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
										<a class="dropdown-item text-warning" href="index.php?option=com_facture&task=edit&id=<?php echo $facture->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('add', 'com_facture') || $_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
										<a class="dropdown-item text-secondary" href="index.php?option=com_facture&task=avoir&id=<?php echo $facture->getId(); ?>"><i class="fa fa-file-invoice-dollar mr-3"></i>Avoir</a>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
										<a class="dropdown-item text-info" href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
									<?php endif; ?>
									
									<?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
										<a class="dropdown-item text-success" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>"><i class="far fa-money-bill-alt mr-2"></i>Reglement</a>
										<?php if($facture->getReste() <= 0): ?>
											<a class="dropdown-item text-danger"  href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) : ?>
										<?php if ($facture->getDevis()->getId() != 0): ?>
											<a class="dropdown-item text-info" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $facture->getDevis()->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>Devis</a>
										<?php endif; ?>
									<?php endif; ?>
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