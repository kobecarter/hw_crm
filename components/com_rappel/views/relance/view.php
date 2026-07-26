<?php
global $db;
$SQLselect = "SELECT * FROM " . __prefixe_db__ . "relances WHERE id=" . $id;
$result = $db->query($SQLselect);
$data = $db->fetch_assoc($result);
$client = (rappel::find($data["id_rappel"],$_SESSION['agence']))->getClient();
?>
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="invoice-item">
                            <div class="row">

                            </div>
                        </div>

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="invoice-info">
                                        <strong class="customer-text">De: </strong>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="invoice-info">
                                        <strong class="customer-text"><?php echo $config->getNom(); ?></strong>
                                        <p class="invoice-details invoice-details-two">
                                            <?php echo $config->getEmail(); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="invoice-logo">
                                        <img src="images/config/<?php echo $config->getLogo(); ?>" alt="logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="invoice-info">
                                        <strong class="customer-text">À: </strong>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="invoice-info">
                                        <strong class="customer-text"><?php echo $client->getRaisonSocial(); ?></strong>
                                        <p class="invoice-details invoice-details-two">
                                            <?php echo $client->getEmail(); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="invoice-info">
                                        <strong class="customer-text">Date: </strong>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="invoice-info">
                                        <p class="invoice-details invoice-details-two">
                                            <?php
                                            echo normaldate2(date("Y-m-d", strtotime($data["date_send"]))) . ', ';
                                            echo strftime("%H:%M", strtotime($data["date_send"])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="invoice-info">
                                        <strong class="customer-text">Objet: </strong>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="invoice-info">
                                        <p class="invoice-details invoice-details-two">
                                            Relance renouvellement
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Invoice Item -->

                        <!-- Invoice Item -->
                        <div class="invoice-item">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php echo $data["message"]; ?>
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