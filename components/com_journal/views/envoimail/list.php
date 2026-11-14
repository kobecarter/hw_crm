<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Envois par email</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Envois par email</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<?php if ($_SESSION['user']->isSuperUser()) : ?>
				<ul class="nav nav-tabs mb-3">
					<li class="nav-item"><a class="nav-link" href="index.php?option=com_journal">Créations/modifications</a></li>
					<li class="nav-item"><a class="nav-link active" href="index.php?option=com_journal&task=envois">Envois par email</a></li>
				</ul>
				<?php endif; ?>
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Devis et factures envoyés par email</h4>
					</div>
					<div class="card-body">
						<div class="table-responsive">
						<table id="envoimail-table" class="table table-stripped table-center table-hover datatable">
							<thead class="thead-light">
								<tr>
									<th>Date</th>
									<th>Document</th>
									<th>Client</th>
									<th>Destinataire</th>
									<th>Statut</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$typeLabels = array(
									'devis' => 'Devis',
									'devis_accepte' => 'Devis (accepté)',
									'facture' => 'Facture',
									'facture_avoir' => "Facture d'avoir",
									'facture_acompte' => "Facture d'acompte",
								);
								?>
								<?php foreach ($envois as $envoi) : ?>
									<?php
									$envoiClient = $envoi->getIdClient() ? client::find($envoi->getIdClient(), $_SESSION['agence']) : null;
									$lienDocument = ($envoi->getEntite() == 'devis')
										? "index.php?option=com_devis&task=show&id=" . $envoi->getIdEntite()
										: "index.php?option=com_facture&task=show&id=" . $envoi->getIdEntite();
									$labelType = isset($typeLabels[$envoi->getTypeEnvoi()]) ? $typeLabels[$envoi->getTypeEnvoi()] : $envoi->getTypeEnvoi();
									?>
									<tr>
										<td data-sort="<?= strtotime($envoi->getDateEnvoi()) ?>"><?= date('d/m/Y H:i:s', strtotime($envoi->getDateEnvoi())) ?></td>
										<td><a href="<?= $lienDocument ?>"><?= htmlspecialchars($labelType) ?> #<?= htmlspecialchars($envoi->getNumero()) ?></a></td>
										<td><?= $envoiClient ? htmlspecialchars($envoiClient->getRaisonSocial() != '' ? $envoiClient->getRaisonSocial() : trim($envoiClient->getNom() . ' ' . $envoiClient->getPrenom())) : '—' ?></td>
										<td><?= htmlspecialchars($envoi->getDestinataire()) ?></td>
										<td>
											<?php if ($envoi->getStatut() == 'succes') : ?>
												<span class="badge badge-success">Envoyé</span>
											<?php else : ?>
												<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="<?= htmlspecialchars($envoi->getErreur()) ?>">Échec</span>
											<?php endif; ?>
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
