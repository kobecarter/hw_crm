<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="row justify-content-center">
			<div class="col-12">
			    <div class="card">
			        <div class="card-body">
			            <?php include('wizard_process.php'); ?>
			        </div>
			    </div>
				<div class="card">
					<div class="card-body">
						<div class="invoice-item">
							<div class="row">
								<div class="col-md-6">
									<div class="invoice-logo">
										<img src="images/config/<?php echo $config->getLogo(); ?>" alt="logo">
									</div>
								</div>
								<div class="col-md-6">
									<p class="invoice-details">
										<strong>Devis N°</strong> <?php echo $devis->getNumero(); ?> <br>
										<strong>Date:</strong> <?php echo normaldate($devis->getDateDevis()); ?>
									</p>
								</div>
							</div>
						</div>

						<!-- Invoice Item -->
						<div class="invoice-item">
							<div class="row">
								<div class="col-md-6">
									<div class="invoice-info">
										<strong class="customer-text m-0"><?php echo $agence->getNom(); ?></strong>
										<p class="invoice-details invoice-details-two">
											<?php echo $agence->getAdresse(); ?>
										</p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="invoice-info invoice-info2">
										<strong class="customer-text m-0">Devis pour</strong>
										<p class="invoice-details">
											<?php echo $devis->getClient()->getRaisonSocial(); ?> <br>
											<?php echo $devis->getClient()->getAdresse(); ?><br>
											<?php if($devis->getClient()->getICE()) :?>
											ICE : <?php echo $devis->getClient()->getICE(); ?><br>
											<?php endif;?>
										</p>
									</div>
								</div>
							</div>
						</div>
						<!-- /Invoice Item -->

						<!-- Invoice Item -->
						<div class="invoice-item invoice-table-wrap">
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table class="invoice-table table table-bordered">
											<thead>
												<tr>
													<th>Description</th>
													<th class="text-center">Prix</th>
													<th class="text-center">Quantité</th>
													<th class="text-right">Total</th>
												</tr>
											</thead>
											<tbody>
												<?php $items = $devis->getItems(); ?>
												<?php foreach($items as $item): 
												$discount = $item->getDiscount()>0 ? ($item->getDiscount() * $item->getTotal()) / 100 : 0;
                                                $soustotal += ($item->getTotal() - $discount);
											    ?>
												<tr>
													<td><?php echo $item->getTitre(); ?></td>
													<td class="text-center"><?php echo number_format($item->getPrix(), 2, ',', ' '). ' ' . $devis->getDevise(); ?></td>
													<td class="text-center"><?php echo $item->getQte() . ' x ' . $item->getUnite(); ?></td>
													<td class="text-right"><?php echo number_format($item->getTotal(), 2, ',', ' '). ' ' . $devis->getDevise(); ?></td>
												</tr>
                                                
												<?php 
												endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="col-md-6 col-xl-4 ml-auto">
									<div class="table-responsive">
										<table class="invoice-table-two table">
											<tbody>
											<tr>
												<th>TVA (20%):</th>
												<td><span><?php echo number_format(($devis->getTotal() - $devis->getTotal() / 1.2), 2, ',', ' ') . ' ' . $devis->getDevise(); ?></span></td>
											</tr>
											<?php if($devis->getDiscount() != ''): ?>	
											<tr>
												<th>Réduction:</th>
												<td><span>-<?php echo $devis->getDiscountVal(); ?><?php echo $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%'; ?></span></td>
											</tr>
											<?php endif; ?>	
											<tr>
												<th>Total TTC:</th>
												<td><span><?php echo number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise(); ?></span></td>
											</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<!-- /Invoice Item -->

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->