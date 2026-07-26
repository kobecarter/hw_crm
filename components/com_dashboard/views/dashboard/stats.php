<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Statistiques</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Statistiques</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<form method="post" action="components/com_dashboard/controleurs/router.php?task=stats" id="statsForm">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Date début</label>
									<div class="cal-icon">
										<input type="text" class="form-control datetimepicker" name="from" value="<?php echo date('01/01/Y'); ?>">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Date fin</label>
									<div class="cal-icon">
										<input type="text" class="form-control datetimepicker" name="to" value="<?php echo date('d/m/Y'); ?>">
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<button type="submit" class="btn btn-primary submit" style="margin-top:30px"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row stats-result">

			<div class="<?=$_SESSION['user']->isSuperUser() != false ? 'col-xl-3' : 'col-xl-4'?> col-sm-6 col-12">
				<div class="card">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1">
								<i class="fas fa-dollar-sign"></i>
							</span>
							<div class="dash-count">
								<div class="dash-title">Créances</div>
								<div class="dash-counts">
									<p class="text-warning">
										<?php echo number_format($creances, 2, ',', ' '); ?> DH<br>
										<?php echo number_format($creancesEuro, 2, ',', ' '); ?> €<br>
										<?php echo number_format($creancesPound, 2, ',', ' '); ?> £<br>
										<?php echo number_format($creancesDollar, 2, ',', ' '); ?> $<br>
										<?php echo number_format($creancesAed, 2, ',', ' '); ?> AED
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="<?=$_SESSION['user']->isSuperUser() != false ? 'col-xl-3' : 'col-xl-4'?> col-sm-6 col-12">
				<div class="card">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2">
								<i class="fas fa-dollar-sign"></i>
							</span>
							<div class="dash-count">
								<div class="dash-title">Chiffre d'affaire</div>
								<div class="dash-counts">
									<p class="text-info">
										<?php echo number_format($factureTotal, 2, ',', ' '); ?> DH<br>
										<?php echo number_format($factureTotalEuro, 2, ',', ' '); ?> €<br>
										<?php echo number_format($factureTotalPound, 2, ',', ' '); ?> £<br>
										<?php echo number_format($factureTotalDollar, 2, ',', ' '); ?> $<br>
										<?php echo number_format($factureTotalAed, 2, ',', ' '); ?> AED
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="<?=$_SESSION['user']->isSuperUser() != false ? 'col-xl-3' : 'col-xl-4'?> col-sm-6 col-12">
				<div class="card">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3">
								<i class="fas fa-money-bill"></i>
							</span>
							<div class="dash-count">
								<div class="dash-title">Encaissements reçus</div>
								<div class="dash-counts">
									<p class="text-success">
										<?php echo number_format($totalReglement, 2, ',', ' '); ?> DH<br>
										<?php echo number_format($totalReglementEuro, 2, ',', ' '); ?> €<br>
										<?php echo number_format($totalReglementPound, 2, ',', ' '); ?> £<br>
										<?php echo number_format($totalReglementDollar, 2, ',', ' '); ?> $<br>
										<?php echo number_format($totalReglementAed, 2, ',', ' '); ?> AED
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if($_SESSION['user']->isSuperUser() != false) :?>
				<div class="col-xl-3 col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon bg-9">
									<i class="fas fa-shopping-basket"></i>
								</span>
								<div class="dash-count">
									<div class="dash-title">Charges</div>
									<div class="dash-counts">
										<p class="text-danger"><?php echo number_format($charges, 2, ',', ' '); ?> DH</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif;?>
		</div>
	</div>
</div>

<script>
	$(function() {
		// envoi du formulaire en ajax
		$('form#statsForm').ajaxForm({
			beforeSubmit: function() {
				$("#statsForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				$("#statsForm .loading").fadeOut();
				$(".stats-result").html(theResponse);
			}
		});
	})
</script>