<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="row justify-content-center">
            <div class="col-xl-8">
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
                                        <strong>Avoir N°</strong> <?php echo $facture->getNumero(); ?> <br>
                                        <strong>Date:</strong> <?php echo normaldate($facture->getDateFacture()); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="invoice-info">
                                        <strong class="customer-text"><?php echo $config->getNom(); ?></strong>
                                        <p class="invoice-details invoice-details-two">
                                            <?php echo $config->getAdresse(); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p class="invoice-details">
                                        <strong>Avoir sur la facture N° </strong><?php echo (facture::find($facture->getIdFacture(),$_SESSION['agence']))->getNumero(); ?><br><br>
                                        <?php echo $facture->getClient()->getRaisonSocial(); ?> <br>
                                        <?php echo $facture->getClient()->getAdresse(); ?><br>
                                        ICE : <?php echo $facture->getClient()->getICE(); ?><br>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="invoice-info">
                                        <strong class="customer-text">Méthode de paiement</strong>
                                        <p class="invoice-details invoice-details-two">
                                            Par chèque
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
                                                <?php $items = $facture->getItems(); ?>
                                                <?php foreach ($items as $item) : ?>
                                                    <tr>
                                                        <td><?php echo $item->getTitre(); ?></td>
                                                        <td class="text-center"><?php echo number_format($item->getPrix(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                                        <td class="text-center"><?php echo $item->getQte() . ' x ' . $item->getUnite(); ?></td>
                                                        <td class="text-right"><?php echo number_format($item->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4 ml-auto">
                                    <div class="table-responsive">
                                        <table class="invoice-table-two table">
                                            <tbody>
                                                <?php if (!$facture->isProforma()) : ?>
                                                    <tr>
                                                        <th>TVA (20%):</th>
                                                        <td><span><?php echo number_format(($facture->getTotal() - $facture->getTotal() / 1.2), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></span></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($facture->getDiscount() != '') : ?>
                                                    <tr>
                                                        <th>Réduction:</th>
                                                        <td><span>-<?php echo $facture->getDiscountVal(); ?><?php echo $facture->getDiscount() == 'amount' ? ' ' . $facture->getDevise() : '%'; ?></span></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <th>Total TTC:</th>
                                                    <td><span><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></span></td>
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