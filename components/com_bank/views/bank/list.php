<?php
$totalBanksCount = sizeof($banks);
$madCount = 0;
$aedCount = 0;
foreach ($banks as $b) {
	$dev = mb_strtoupper(trim($b->getCurrency()), 'UTF-8');
	if ($dev === 'AED') {
		$aedCount++;
	} elseif (strpos($dev, 'MAD') !== false || $dev === '') {
		$madCount++;
	}
}
?>
<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Banques</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Banques</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_bank')) :?>
					<div class="col-auto">
						<a href="index.php?option=com_bank&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter bank">
							<i class="fas fa-plus"></i>
						</a>
					</div>
				<?php endif;?>
			</div>
		</div>

		<!-- Statistiques rapides -->
		<div class="row client-stats-row">
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="fa fa-university"></i></span>
							<div class="dash-count">
								<div class="dash-title">Total comptes</div>
								<div class="dash-counts"><p><?php echo $totalBanksCount; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-coins"></i></span>
							<div class="dash-count">
								<div class="dash-title">Comptes MAD (Maroc)</div>
								<div class="dash-counts"><p><?php echo $madCount; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-4 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-4"><i class="fa fa-money-bill-wave"></i></span>
							<div class="dash-count">
								<div class="dash-title">Comptes AED (Dubai)</div>
								<div class="dash-counts"><p><?php echo $aedCount; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Statistiques rapides -->

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des banques</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>Banque</th>
										<th>Raison sociale</th>
										<th>Agence</th>
										<th>RIB / IBAN</th>
										<th>Devise</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($banks as $bank): ?>
									<?php
									$logo = bank::getLogoInfo($bank->getBanque());
									$devise = trim($bank->getCurrency());
									if ($devise === '') {
										$deviseBadge = '<span class="badge bg-warning-light">Non renseignée</span>';
									} elseif (mb_strtoupper($devise, 'UTF-8') === 'AED') {
										$deviseBadge = '<span class="badge bg-primary-light">AED</span>';
									} elseif (strpos($devise, ',') !== false) {
										$deviseBadge = '<span class="badge bg-info-light" data-toggle="tooltip" data-original-title="Compte multi-devises (virements internationaux)">Devise (' . htmlspecialchars($devise) . ')</span>';
									} else {
										$deviseBadge = '<span class="badge bg-success-light">' . htmlspecialchars($devise) . '</span>';
									}
									?>
									<tr>
										<td>
											<h2 class="table-avatar">
												<span class="avatar avatar-sm mr-2" style="background:<?php echo $logo['bg']; ?>;color:#fff;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;font-size:0.7rem;font-weight:700;"><?php echo htmlspecialchars($logo['initials']); ?></span>
												<?php echo htmlspecialchars($bank->getBanque()); ?>
											</h2>
										</td>
										<td><?php echo htmlspecialchars($bank->getRaisonSociale()); ?></td>
										<td><?php echo $bank->getAgence() ? htmlspecialchars($bank->getAgence()->getNom()) : '<span class="text-muted">—</span>'; ?></td>
										<td><small><?php echo htmlspecialchars($bank->getIbanNumber() ?: $bank->getRib()); ?></small></td>
										<td><?php echo $deviseBadge; ?></td>
										<td class="text-right">
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_bank')) :?>
											    <a href="index.php?option=com_bank&task=edit&id=<?= $bank->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_bank')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $bank->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
<script type="text/javascript">
$(function () {

	var msgsucces = "Banque supprimé avec succès";

	$(document).on( "click", ".delete", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_bank/controleurs/router.php?task=deleteBank", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {

					$btn.closest("tr").addClass("table-danger");
					setTimeout(function () {
						$btn.closest("tr").remove()
					}, 1000);

					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})
});
</script>
