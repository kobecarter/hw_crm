<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Journal d'activité</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Journal d'activité</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Créations, modifications et suppressions</h4>
					</div>
					<div class="card-body">
						<div class="table-responsive">
						<table id="journal-table" class="table table-stripped table-center table-hover datatable">
							<thead class="thead-light">
								<tr>
									<th>Date</th>
									<th>Agence</th>
									<th>Utilisateur</th>
									<th>Entité</th>
									<th>Action</th>
									<th>Description</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($logs as $log) : ?>
									<?php
									$logAgence = $log->getIdAgence() ? agence::find($log->getIdAgence(), $_SESSION['langue']) : null;
									$logUser = $log->getIdUser() ? user::find($log->getIdUser()) : null;
									$actionLabels = array('add' => 'Création', 'edit' => 'Modification', 'delete' => 'Suppression', 'login' => 'Connexion');
									$actionBadges = array('add' => 'badge-success', 'edit' => 'badge-warning', 'delete' => 'badge-danger', 'login' => 'badge-info');
									?>
									<tr>
										<td data-sort="<?= strtotime($log->getDateAdd()) ?>"><?= date('d/m/Y H:i:s', strtotime($log->getDateAdd())) ?></td>
										<td><?= $logAgence ? htmlspecialchars($logAgence->getNom()) : '—' ?></td>
										<td><?= $logUser ? htmlspecialchars($logUser->getPrenom() . ' ' . $logUser->getNom()) : '—' ?></td>
										<td><?= htmlspecialchars($log->getEntite()) ?></td>
										<td><span class="badge <?= isset($actionBadges[$log->getAction()]) ? $actionBadges[$log->getAction()] : 'badge-secondary' ?>"><?= isset($actionLabels[$log->getAction()]) ? $actionLabels[$log->getAction()] : $log->getAction() ?></span></td>
										<td><?= htmlspecialchars($log->getDescription()) ?></td>
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
