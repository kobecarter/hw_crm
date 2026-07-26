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
						<form method="post" action="components/com_dashboard/controleurs/router.php?task=statsGlobal" id="statsForm">
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
										<p class="text-danger">
										    <?php echo number_format($charges, 2, ',', ' '); ?> DH<br>
										    <?php echo number_format($chargesEuro, 2, ',', ' '); ?> €<br>
										    <?php echo number_format($chargesPound, 2, ',', ' '); ?> £<br>
										    <?php echo number_format($chargesDollar, 2, ',', ' '); ?> $<br>
										    <?php echo number_format($chargesAed, 2, ',', ' '); ?> AED
									    </p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif;?>
		</div>
		<div class="row">
		    <div class="col-xl-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Contribution au chiffre d'affaire</h5>
							<div class="dropdown" data-toggle="dropdown">
								<span class="d-none selected-current-year"><?php echo date('Y'); ?></span>
								<a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle current-year" role="button" data-toggle="dropdown">Année <?php echo date('Y'); ?></a>
								<div class="dropdown-menu dropdown-menu-right switch-year-global">
									<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y'); ?>">Année <?php echo date('Y'); ?></a>
									<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y') - 1; ?>">Année <?php echo date('Y') - 1; ?></a>
									<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y') - 2; ?>">Année <?php echo date('Y') - 2; ?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
					    <?php
					    // chiffre d'affaire global HW LABEL
					    $ca_hwlabel = payment::getReglementbyDate($from, $to, 'DH', 1);
					    $ca_hwlabel += payment::getReglementbyDate($from, $to, '€', 1) * 10;
					    $ca_hwlabel += payment::getReglementbyDate($from, $to, '£', 1) * 12;
					    $ca_hwlabel += payment::getReglementbyDate($from, $to, '$', 1) * 9;
					    $ca_hwlabel += payment::getReglementbyDate($from, $to, 'AED', 1) * 2.5;
					    
					    // chiffre d'affaire global VERSE CONCEPT
					    $ca_verse = payment::getReglementbyDate($from, $to, 'DH', 3);
					    $ca_verse += payment::getReglementbyDate($from, $to, '€', 3) * 10;
					    $ca_verse += payment::getReglementbyDate($from, $to, '£', 3) * 12;
					    $ca_verse += payment::getReglementbyDate($from, $to, '$', 3) * 9;
					    $ca_verse += payment::getReglementbyDate($from, $to, 'AED', 3) * 2.5;
					    
					    // chiffre d'affaire global HELLO WORLD DUBAI
					    $ca_hello_dubai = payment::getReglementbyDate($from, $to, 'DH', 2);
					    $ca_hello_dubai += payment::getReglementbyDate($from, $to, '€', 2) * 10;
					    $ca_hello_dubai += payment::getReglementbyDate($from, $to, '£', 2) * 12;
					    $ca_hello_dubai += payment::getReglementbyDate($from, $to, '$', 2) * 9;
					    $ca_hello_dubai += payment::getReglementbyDate($from, $to, 'AED', 2) * 2.5;
					    
					    $totalCA = $ca_hwlabel + $ca_verse + $ca_hello_dubai;
					    ?>
						<div id="ca_per_company_chart"></div>
						<script>
						    var STATS_GLOBAL = true;
							var ca_hwlabel = <?php echo $ca_hwlabel; ?>;
							var ca_verse = <?php echo $ca_verse; ?>;
							var ca_hello_dubai = <?php echo $ca_hello_dubai; ?>;
						</script>
						<div class="text-center text-muted invoice-box">
							<div class="row">
								<div class="col-4">
									<div class="mt-4">
										<p class="mb-2 text-truncate"><i class="fas fa-circle text-danger mr-1"></i> HW LABEL</p>
										<h5 class="ca_hwlabel"><?php echo number_format($ca_hwlabel, 2, ',', ' '); ?> DH</h5>
									</div>
								</div>
								<div class="col-4">
									<div class="mt-4">
										<p class="mb-2 text-truncate"><i class="fas fa-circle text-success mr-1"></i> VERSE CONCEPT</p>
										<h5 class="ca_verse"><?php echo number_format($ca_verse, 2, ',', ' '); ?> DH</h5>
									</div>
								</div>
								<div class="col-4">
									<div class="mt-4">
										<p class="mb-2 text-truncate"><i class="fas fa-circle text-warning mr-1"></i> HELLO WORLD DUBAI</p>
										<h5 class="ca_hwdubai"><?php echo number_format($ca_hello_dubai, 2, ',', ' '); ?> DH</h5>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer text-center"><h4 class="mb-0">Total encaissement : <span class="text-success total-ca"><?php echo number_format($totalCA, 2, ',', ' '); ?> DH</span></h4></div>
				</div>
			</div>
		</div>
		<div class="row">
		    <div class="col-xl-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Charges par société</h5>
							<div class="dropdown" data-toggle="dropdown">
								<span class="d-none selected-current-year"><?php echo date('Y'); ?></span>
								<a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle current-year" role="button" data-toggle="dropdown">Année <?php echo date('Y'); ?></a>
								<div class="dropdown-menu dropdown-menu-right switch-year-global">
									<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y'); ?>">Année <?php echo date('Y'); ?></a>
									<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y') - 1; ?>">Année <?php echo date('Y') - 1; ?></a>
									<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y') - 2; ?>">Année <?php echo date('Y') - 2; ?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
					    <?php
					    // charge global HW LABEL
					    $charge_hwlabel = charge::getCharge($from, $to, 1, 'DH');
					    $charge_hwlabel += charge::getCharge($from, $to, 1, '€') * 10;
					    $charge_hwlabel += charge::getCharge($from, $to, 1, '£') * 12;
					    $charge_hwlabel += charge::getCharge($from, $to, 1, '$') * 9;
					    $charge_hwlabel += charge::getCharge($from, $to, 1, 'AED') * 2.5;
					    
					    // charge global VERSE CONCEPT
					    $charge_verse = charge::getCharge($from, $to, 3, 'DH');
					    $charge_verse += charge::getCharge($from, $to, 3, '€') * 10;
					    $charge_verse += charge::getCharge($from, $to, 3, '£') * 12;
					    $charge_verse += charge::getCharge($from, $to, 3, '$') * 9;
					    $charge_verse += charge::getCharge($from, $to, 3, 'AED') * 2.5;
					    
					    // charge global HELLO WORLD DUBAI
					    $charge_hello_dubai = charge::getCharge($from, $to, 2, 'DH');
					    $charge_hello_dubai += charge::getCharge($from, $to, 2, '€') * 10;
					    $charge_hello_dubai += charge::getCharge($from, $to, 2, '£') * 12;
					    $charge_hello_dubai += charge::getCharge($from, $to, 2, '$') * 9;
					    $charge_hello_dubai += charge::getCharge($from, $to, 2, 'AED') * 2.5;
					    
					    $totalCharge = $charge_hwlabel + $charge_verse + $charge_hello_dubai;
					    ?>
						<div id="charge_per_company_chart"></div>
						<script>
						    var STATS_GLOBAL = true;
							var charge_hwlabel = <?php echo $charge_hwlabel; ?>;
							var charge_verse = <?php echo $charge_verse; ?>;
							var charge_hello_dubai = <?php echo $charge_hello_dubai; ?>;
						</script>
						<div class="text-center text-muted invoice-box">
							<div class="row">
								<div class="col-4">
									<div class="mt-4">
										<p class="mb-2 text-truncate"><i class="fas fa-circle text-danger mr-1"></i> HW LABEL</p>
										<h5 class="charge_hwlabel"><?php echo number_format($charge_hwlabel, 2, ',', ' '); ?> DH</h5>
									</div>
								</div>
								<div class="col-4">
									<div class="mt-4">
										<p class="mb-2 text-truncate"><i class="fas fa-circle text-success mr-1"></i> VERSE CONCEPT</p>
										<h5 class="charge_verse"><?php echo number_format($charge_verse, 2, ',', ' '); ?> DH</h5>
									</div>
								</div>
								<div class="col-4">
									<div class="mt-4">
										<p class="mb-2 text-truncate"><i class="fas fa-circle text-warning mr-1"></i> HELLO WORLD DUBAI</p>
										<h5 class="charge_hwdubai"><?php echo number_format($charge_hello_dubai, 2, ',', ' '); ?> DH</h5>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer text-center"><h4 class="mb-0">Total charge : <span class="text-danger total-charge"><?php echo number_format($totalCharge, 2, ',', ' '); ?> DH</span></h4></div>
				</div>
			</div>
		</div>
	</div>
</div>