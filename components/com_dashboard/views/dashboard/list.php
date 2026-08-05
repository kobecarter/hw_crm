<?php if ($_SESSION['user']->isResourceHumaine()) : 
	require_once("components/com_resourcehumaine/views/resourcehumaine/profile.php");
?>
	
<?php else : ?>
	<!-- Page Wrapper -->
	<div class="page-wrapper glass-page">
		<div class="content container-fluid">
			<div class="row">
				<div class="col-12">
					<?php if ($_SESSION['user']->isSuperUser() != false) : ?>
						<a href="index.php?option=com_dashboard&task=stats" class="btn btn-primary pull-right mb-3" style="float:right">Statistiques</a>
						<a href="index.php?option=com_dashboard&task=globalStats" class="btn btn-info pull-right mb-3 mr-3" style="float:right">Statistiques globales</a>
					<?php endif; ?>

					<div class="dropdown mr-5" data-toggle="dropdown" style="width:100px;float:right">
						<a href="javascript:void(0);" class="btn btn-white btn-md dropdown-toggle current-year" role="button" data-toggle="dropdown">Année <?php echo date('Y'); ?></a>
						<div class="dropdown-menu dropdown-menu-right switch-year">
							<a href="javascript:void(0);" class="dropdown-item" data-year="<?php echo date('Y'); ?>">Année <?php echo date('Y'); ?></a>
							<a href="javascript:void(0);" class="dropdown-item" data-year="<?php echo date('Y') - 1; ?>">Année <?php echo date('Y') - 1; ?></a>
							<a href="javascript:void(0);" class="dropdown-item" data-year="<?php echo date('Y') - 2; ?>">Année <?php echo date('Y') - 2; ?></a>
						</div>
					</div>
				</div>
			</div>

			<div class="row global-stats">
				<?php if ($_SESSION['user']->isSuperUser() != false) : ?>
					<div class="col-xl-3 col-sm-6 col-12">
						<div class="card">
							<div class="card-body">
								<div class="dash-widget-header">
									<span class="dash-widget-icon bg-1" data-toggle="tooltip" title="Montant total restant à encaisser sur l'ensemble des factures ouvertes (impayées ou partiellement payées).">
										<i class="fas fa-dollar-sign"></i>
									</span>
									<div class="dash-count">
										<div class="dash-title" data-toggle="tooltip" title="Montant total restant à encaisser sur l'ensemble des factures ouvertes (impayées ou partiellement payées).">Créances</div>
										<div class="dash-counts money-sensitive">
											<p><?php echo number_format($creances, 2, ',', ' '); ?></p>
										</div>
									</div>
								</div>
								<div class="progress progress-sm mt-3">
									<div class="progress-bar bg-5" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<p class="text-muted mt-3 mb-0 money-sensitive">
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYear, 2, ',', ' '); ?> Dh</span> année <?php echo date('Y'); ?><br>
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearEuro, 2, ',', ' '); ?> €</span> année <?php echo date('Y'); ?><br>
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearPound, 2, ',', ' '); ?> £</span> année <?php echo date('Y'); ?><br>
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearDollar, 2, ',', ' '); ?> $</span> année <?php echo date('Y'); ?><br>
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearAed, 2, ',', ' '); ?> AED</span> année <?php echo date('Y'); ?>
								</p>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<div class="<?= $_SESSION['user']->isSuperUser() != false ? 'col-xl-3' : 'col-xl-4' ?> col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<a href="index.php?option=com_facture&task=client" class="dash-widget-icon bg-2" data-toggle="tooltip" title="Nombre total de clients actifs enregistrés pour cette agence. Cliquer pour voir la liste.">
									<i class="fas fa-users"></i>
								</a>
								<div class="dash-count">
									<div class="dash-title" data-toggle="tooltip" title="Nombre total de clients actifs enregistrés pour cette agence.">Clients</div>
									<div class="dash-counts money-sensitive">
										<p><?php echo number_format($clientNumber, 0, '', ','); ?></p>
									</div>
								</div>
							</div>
							<div class="progress progress-sm mt-3">
								<div class="progress-bar bg-6" role="progressbar" style="width: 65%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<?php if ($_SESSION['user']->isSuperUser() != false) : ?>
								<p class="text-muted mt-3 mb-0">
									<span class="text-success mr-1"><i class="fas fa-user mr-1"></i><?php echo $clientNumberYear; ?></span> année <?php echo date('Y'); ?><br>
									<span class="text-success mr-1"><i class="fas fa-user mr-1"></i><?php echo $clientNumberYear2; ?></span> année <?php echo (date('Y') - 1); ?><br>
									<span class="text-success mr-1"><i class="fas fa-user mr-1"></i><?php echo $clientNumberYear3; ?></span> année <?php echo (date('Y') - 2); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="<?= $_SESSION['user']->isSuperUser() != false ? 'col-xl-3' : 'col-xl-4' ?> col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<a href="index.php?option=com_facture&task=facture" class="dash-widget-icon bg-3" data-toggle="tooltip" title="Nombre total de factures émises cette année, tous statuts confondus. Cliquer pour voir la liste.">
									<i class="fas fa-file-alt"></i>
								</a>
								<div class="dash-count">
									<div class="dash-title" data-toggle="tooltip" title="Nombre total de factures émises cette année, tous statuts confondus.">Factures</div>
									<div class="dash-counts money-sensitive">
										<p><?php echo number_format($factureNumber, 0, '', ','); ?></p>
									</div>
								</div>
							</div>
							<div class="progress progress-sm mt-3">
								<div class="progress-bar bg-7" role="progressbar" style="width: 85%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<?php if ($_SESSION['user']->isSuperUser() != false) : ?>
								<p class="text-muted mt-3 mb-0 money-sensitive">
									<span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotal, 2, ',', ' '); ?> Dh</span> année <?php echo date('Y'); ?><br>
									<span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalEur, 2, ',', ' '); ?> €</span> année <?php echo date('Y'); ?><br>
									<span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalPound, 2, ',', ' '); ?> £</span> année <?php echo date('Y'); ?><br>
									<span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalDollar, 2, ',', ' '); ?> $</span> année <?php echo date('Y'); ?><br>
									<span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalAed, 2, ',', ' '); ?> AED</span> année <?php echo date('Y'); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="<?= $_SESSION['user']->isSuperUser() != false ? 'col-xl-3' : 'col-xl-4' ?> col-sm-6 col-12">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<a href="index.php?option=com_facture&task=devis" class="dash-widget-icon bg-4" data-toggle="tooltip" title="Nombre total de devis créés cette année, tous statuts confondus. Cliquer pour voir la liste.">
									<i class="far fa-file"></i>
								</a>
								<div class="dash-count">
									<div class="dash-title" data-toggle="tooltip" title="Nombre total de devis créés cette année, tous statuts confondus.">Devis</div>
									<div class="dash-counts money-sensitive">
										<p><?php echo number_format($devisNumber, 0, '', ','); ?></p>
									</div>
								</div>
							</div>
							<div class="progress progress-sm mt-3">
								<div class="progress-bar bg-8" role="progressbar" style="width: 45%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
							<?php if ($_SESSION['user']->isSuperUser() != false) : ?>
								<p class="text-muted mt-3 mb-0">
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo $devisNumberInvalide; ?></span> devis invalide : année <?php echo date('Y'); ?><br>
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo $devisNumberInvalide2; ?></span> devis invalide : année <?php echo date('Y') - 1; ?><br>
									<span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo $devisNumberInvalide3; ?></span> devis invalide : année <?php echo date('Y') - 2; ?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php if ($_SESSION['user']->isSuperUser() != false) : ?>
				<div class="row">
					<div class="col-xl-7 d-flex">
						<div class="card flex-fill">
							<div class="card-header">
								<div class="d-flex justify-content-between align-items-center">
									<h5 class="card-title" data-toggle="tooltip" title="Total des paiements reçus par devise, mois par mois, sur l'année sélectionnée.">Encaissements reçus</h5>
									<div class="dropdown" data-toggle="dropdown">
										<span class="d-none selected-current-year"><?php echo date('Y'); ?></span>
										<a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle current-year" role="button" data-toggle="dropdown">Année <?php echo date('Y'); ?></a>
										<div class="dropdown-menu dropdown-menu-right switch-year">
											<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y'); ?>">Année <?php echo date('Y'); ?></a>
											<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y') - 1; ?>">Année <?php echo date('Y') - 1; ?></a>
											<a href="javascript:void(0);" class="dropdown-item select-current-year" data-year="<?php echo date('Y') - 2; ?>">Année <?php echo date('Y') - 2; ?></a>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="das-chart-content">
									<div class="das-chart">
										<div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap">
											<div class="w-md-100 d-flex align-items-center mb-3 ca-box money-sensitive">
												<div>
													<span>En MAD</span>
													<p class="h4 text-primary mr-5"><?php echo number_format(payment::total(date('Y'), false, 'DH', $_SESSION['agence']), 2, ',', ' '); ?></p>
												</div>
												<div>
													<span>En Euro</span>
													<p class="h4 text-success mr-5"><?php echo number_format(payment::total(date('Y'), false, '€', $_SESSION['agence']), 2, ',', ' '); ?></p>
												</div>
												<div>
													<span>En Pound</span>
													<p class="h4 text-warning mr-5"><?php echo number_format(payment::total(date('Y'), false, '£', $_SESSION['agence']), 2, ',', ' '); ?></p>
												</div>
												<div>
													<span>En Dollar</span>
													<p class="h4 text-info mr-5"><?php echo number_format(payment::total(date('Y'), false, '$', $_SESSION['agence']), 2, ',', ' '); ?></p>
												</div>
												<div>
													<span>En Aed</span>
													<p class="h4 text-secondary mr-5"><?php echo number_format(payment::total(date('Y'), false, 'AED', $_SESSION['agence']), 2, ',', ' '); ?></p>
												</div>
												<div>
													<span>Charges</span>
													<p class="h4 text-danger mr-5"><?php echo number_format(charge::total(date('Y'), false, $_SESSION['agence']), 2, ',', ' '); ?></p>
												</div>
											</div>
										</div>

										<div id="sales_chart" class="money-sensitive"></div>

										<?php $paymentPerMonth = $paymentPerMonthEur = $paymentPerMonthPound = $paymentPerMonthDollar = $paymentPerMonthAed = $chargePerMonth = array(); ?>
										<?php for ($i = 1; $i < 13; $i++) {
											$total = payment::total(date('Y'), $i, 'DH', $_SESSION['agence']);
											$total2 = payment::total(date('Y'), $i, '€', $_SESSION['agence']);
											$total3 = payment::total(date('Y'), $i, '£', $_SESSION['agence']);
											$total4 = payment::total(date('Y'), $i, '$', $_SESSION['agence']);
											$total6 = payment::total(date('Y'), $i, 'AED', $_SESSION['agence']);
											$total5 = charge::total(date('Y'), $i, $_SESSION['agence']);
											array_push($paymentPerMonth, $total);
											array_push($paymentPerMonthEur, $total2);
											array_push($paymentPerMonthPound, $total3);
											array_push($paymentPerMonthDollar, $total4);
											array_push($paymentPerMonthAed, $total6);
											array_push($chargePerMonth, $total5);
										} ?>
										<script>
											var paymentPerMonth = [<?php echo implode(',', $paymentPerMonth); ?>];
											var paymentPerMonthEur = [<?php echo implode(',', $paymentPerMonthEur); ?>];
											var paymentPerMonthPound = [<?php echo implode(',', $paymentPerMonthPound); ?>];
											var paymentPerMonthDollar = [<?php echo implode(',', $paymentPerMonthDollar); ?>];
											var paymentPerMonthAed = [<?php echo implode(',', $paymentPerMonthAed); ?>];
											var chargePerMonth = [<?php echo implode(',', $chargePerMonth); ?>];
										</script>
										<?php //echo implode($paymentPerMonthEur,','); 
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-5 d-flex">
						<div class="card flex-fill">
							<div class="card-header">
								<div class="d-flex justify-content-between align-items-center">
									<h5 class="card-title" data-toggle="tooltip" title="Répartition des factures de l'année par statut de paiement : payée, impayée, ou partiellement payée.">Facture Analytics</h5>
									<div class="dropdown" data-toggle="dropdown">
										<a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle current-year" role="button" data-toggle="dropdown">Année <?php echo date('Y'); ?></a>
										<div class="dropdown-menu dropdown-menu-right switch-year">
											<a href="javascript:void(0);" class="dropdown-item" data-year="<?php echo date('Y'); ?>">Année <?php echo date('Y'); ?></a>
											<a href="javascript:void(0);" class="dropdown-item" data-year="<?php echo date('Y') - 1; ?>">Année <?php echo date('Y') - 1; ?></a>
											<a href="javascript:void(0);" class="dropdown-item" data-year="<?php echo date('Y') - 2; ?>">Année <?php echo date('Y') - 2; ?></a>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body">
								<div id="invoice_chart"></div>
								<script>
								    var STATS_GLOBAL = false;
									var facturePaye = <?php echo $facturePaye = facture::count(1, date('Y'), false, $_SESSION['agence']); ?>;
									var factureimpaye = <?php echo $factureimpaye = facture::count(3, date('Y'), false, $_SESSION['agence']); ?>;
									var facturePartial = <?php echo $facturePartial = facture::count(2, date('Y'), false, $_SESSION['agence']); ?>;
								</script>
								<div class="text-center text-muted invoice-box">
									<div class="row">
										<div class="col-4">
											<div class="mt-4">
												<p class="mb-2 text-truncate"><i class="fas fa-circle text-danger mr-1"></i> Impayée</p>
												<h5><?php echo $factureimpaye; ?></h5>
											</div>
										</div>
										<div class="col-4">
											<div class="mt-4">
												<p class="mb-2 text-truncate"><i class="fas fa-circle text-success mr-1"></i> Payée</p>
												<h5><?php echo $facturePaye; ?></h5>
											</div>
										</div>
										<div class="col-4">
											<div class="mt-4">
												<p class="mb-2 text-truncate"><i class="fas fa-circle text-warning mr-1"></i> Payée partiallement</p>
												<h5><?php echo $facturePartial; ?></h5>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="row">
				<div class="col-md-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<div class="row">
								<div class="col">
									<h5 class="card-title">Factures récentes</h5>
								</div>
								<div class="col-auto">
									<a href="index.php?option=com_facture&task=facture" class="btn-right btn btn-sm btn-outline-primary">Voir tout</a>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="mb-3">
								<!--<div class="progress progress-md rounded-pill mb-3">
											<div class="progress-bar bg-success" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-warning" role="progressbar" style="width: 28%" aria-valuenow="28" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<div class="row">
											<div class="col-auto">
												<i class="fas fa-circle text-success mr-1"></i> Paid
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-warning mr-1"></i> Unpaid
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-danger mr-1"></i> Overdue
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-info mr-1"></i> Draft
											</div>
										</div>-->
							</div>

							<div class="table-responsive">

								<table class="table table-stripped table-hover">
									<thead class="thead-light">
										<tr>
											<th>Client</th>
											<th>Montant</th>
											<th>Date</th>
											<th>Status</th>
											<th class="text-right">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($lastFactures as $facture) : ?>
											<?php
											switch ($facture->getStatu()) {
												case '1':
													$statu = '<span class="badge bg-success-light">Payée</span>';
													break;
												case '2':
													$statu = '<span class="badge bg-warning-light">Payée partialement</span>';
													break;
												default:
													$statu = '<span class="badge bg-danger-light">Impayée</span>';
													break;
											}
											?>
											<tr>
												<td>
													<?php $photoLink = $facture->getClient()->getPhoto() != '' ? "images/clients/" . $facture->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
													<h2 class="table-avatar">
														<a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $facture->getClient()->getRaisonSocial(); ?></a>
													</h2>
												</td>
												<td class="money-sensitive"><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
												<td><?php echo normaldate($facture->getDateFacture()); ?></td>
												<td><?php echo $statu; ?></td>
												<td class="text-right">
													<div class="dropdown dropdown-action">
														<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item text-warning" href="index.php?option=com_facture&task=edit&id=<?php echo $facture->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
															<a class="dropdown-item text-info" href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
															<a class="dropdown-item text-danger delete" href="javascript:void(0);" data-id="<?php echo $facture->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>

															<a class="dropdown-item text-success" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>"><i class="far fa-money-bill-alt mr-2"></i>Reglement</a>
															<a class="dropdown-item text-danger" href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
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
				<div class="col-md-6 col-sm-6">
					<div class="card">
						<div class="card-header">
							<div class="row">
								<div class="col">
									<h5 class="card-title">Devis récents</h5>
								</div>
								<div class="col-auto">
									<a href="index.php?option=com_facture&task=devis" class="btn-right btn btn-sm btn-outline-primary">Voir tout</a>
								</div>
							</div>
						</div>
						<div class="card-body">
							<!--<div class="mb-3">
										<div class="progress progress-md rounded-pill mb-3">
											<div class="progress-bar bg-success" role="progressbar" style="width: 39%" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-danger" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
											<div class="progress-bar bg-warning" role="progressbar" style="width: 26%" aria-valuenow="26" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<div class="row">
											<div class="col-auto">
												<i class="fas fa-circle text-success mr-1"></i> Sent
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-warning mr-1"></i> Draft
											</div>
											<div class="col-auto">
												<i class="fas fa-circle text-danger mr-1"></i> Expired
											</div>
										</div>
									</div>-->
							<div class="table-responsive">
								<table class="table table-hover">
									<thead class="thead-light">
										<tr>
											<th>Client</th>
											<th>Date</th>
											<th>Montant</th>
											<th>Status</th>
											<th class="text-right">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($lastDevis as $devis) : ?>
											<?php
											switch($devis->getStatu()){
                                                case '1' : $statu = '<span class="badge bg-success-light">Envoyé</span>'; break;
                                                case '2' : $statu = '<span class="badge bg-success-light">Accepté</span>'; break;
                                                case '3' : $statu = '<span class="badge bg-primary-light">Contrat en attente de signature</span>'; break;
                                                case '4' : $statu = '<span class="badge bg-success text-white">Paiement effectué</span>'; break;
                                                case '5' : $statu = '<span class="badge bg-danger text-white">Devis Refusé</span>'; break;
                                                default : $statu = '<span class="badge bg-warning-light">Brouillon</span>'; break;	
                                            }
											?>
											<tr>
												<td>
													<?php $photoLink = $devis->getClient()->getPhoto() != '' ? "images/clients/" . $devis->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
													<h2 class="table-avatar">
														<a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $devis->getClient()->getRaisonSocial(); ?></a>
													</h2>
												</td>
												<td><?php echo normaldate($devis->getDateDevis()); ?></td>
												<td class="money-sensitive"><?php echo number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise(); ?></td>
												<td><?php echo $statu; ?></td>
												<td class="text-right">
													<div class="dropdown dropdown-action">
														<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
														<div class="dropdown-menu dropdown-menu-right">
															<a class="dropdown-item text-warning" href="index.php?option=com_devis&task=edit&id=<?php echo $devis->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
															<a class="dropdown-item text-info" href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
															<a class="dropdown-item text-danger delete" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>

															<a class="dropdown-item text-danger" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $devis->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
															<a class="dropdown-item text-success create-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-file-invoice mr-2"></i>+ Facture</a>
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

	<!-- Add Category Modal -->
	<div id="dialog-custom" class="modal custom-modal fade" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<div class="div-currencies">
								<div class="div-currency">
									<a class="btn-show-encashment" data-url="index.php?option=com_facture&task=invoice-by-currency&currency=DH&currency_name=MAD&color=primary" href="javascript:void(0)">
										<div class="card bg-primary">
											<div class="card-body">
												<h2 class="mb-0 text-white">MAD</h2>
											</div>
										</div>
									</a>
								</div>
								<div class="div-currency">
									<a class="btn-show-encashment" data-url="index.php?option=com_facture&task=invoice-by-currency&currency=€&currency_name=Euro&color=success" href="javascript:void(0)">
										<div class="card bg-success">
											<div class="card-body">
												<h2 class="mb-0 text-white">Euro</h2>
											</div>
										</div>
									</a>
								</div>
								<div class="div-currency">
									<a class="btn-show-encashment" data-url="index.php?option=com_facture&task=invoice-by-currency&currency=£&currency_name=Pound&color=warning" href="javascript:void(0)">
										<div class="card bg-warning">
											<div class="card-body">
												<h2 class="mb-0 text-white">Pound</h2>
											</div>
										</div>
									</a>
								</div>
								<div class="div-currency">
									<a class="btn-show-encashment" data-url="index.php?option=com_facture&task=invoice-by-currency&currency=$&currency_name=Dollar&color=info" href="javascript:void(0)">
										<div class="card bg-info">
											<div class="card-body">
												<h2 class="mb-0 text-white">Dollar</h2>
											</div>
										</div>
									</a>
								</div>
								<div class="div-currency">
									<a class="btn-show-encashment" data-url="index.php?option=com_facture&task=invoice-by-currency&currency=AED&currency_name=Aed&color=secondary" href="javascript:void(0)">
										<div class="card bg-secondary">
											<div class="card-body">
												<h2 class="mb-0 text-white">Aed</h2>
											</div>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Add Category Modal -->

	<script type="text/javascript">
		$(function() {
			$(document).on("click", ".btn-show-encashment", function() {
				let self = $(this)
				let url = self.attr('data-url')
				let year = $(".selected-current-year").text()
				let whole_url = url + "&year=" + year
				location.href = whole_url
			})
			$(document).on("click", ".ca-box p", function() {
				var self = $(this);
				if (!self.parent('div').is(':last-child')) {
					var title = 'Détail des encaissements';
					$(".modal-title").html(title);
					$("#dialog-custom").modal('show');
				}
			})

		});
	</script>


<?php endif; ?>