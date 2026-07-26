<div class="row justify-content-center">
    
    <?php if(isValidDate($facture->getDateDebut()) && isValidDate($facture->getDateFin())) :?>
        <div class="col-12">
            <?php
                // Define the two dates
                $today = new DateTime();
                $startDate = new DateTime($facture->getDateDebut());
                $endDate = new DateTime($facture->getDateFin());
                
                // Calculate the difference
                $interval1 = $startDate->diff($endDate);
                $interval2 = $today->diff($endDate);
                
                // Get the difference in days
                $days = $interval1->days;
                $rest_days = $interval2->days;
            ?>
            <div class="row mb-5 text-center">
                <div class="col-md-6">
                    <h3>Date limite : <span class="text-success"><?=$days?> jour(s)</span></h3>
                </div>
                <div class="col-md-6">
                    <?php if($startDate <= $today) :?>
                        <h3>Il rest : <span class="text-danger"><?=$rest_days?> jour(s)</span></h3>
                    <?php else :?>
                        <h3><span class="text-danger">Ça ne démarre pas encore</span></h3>
                    <?php endif;?>
                </div>
            </div>
        </div>
    <?php endif;?>
    <!-- Lightbox -->
    <div class="col-12">
        <div class="wizard">
            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top" title="Devis">
                    <?php 
                        $wizard_status_quote = "";
                        if($facture->getDevis()){
                            if($facture->getDevis()->getStatu() == 0){
                                $wizard_status_quote = "";
                            }
                            elseif($facture->getDevis()->getStatu() == 5){
                                // devis refusé
                                $wizard_status_quote = "wizard-danger";
                            }else if($facture->getDevis()->getStatu() == 4){
                                $wizard_status_quote = "wizard-success";
                                $wizard_status_quote_url = "components/com_devis/controleurs/router.php?task=pdfDevis&id=".$facture->getDevis()->getId();;
                            }else{
                                $wizard_status_quote = "wizard-warning";
                            }
                        }
                    ?>
                    <a class="<?=$wizard_status_quote?> nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center" href="index.php?option=com_devis&task=edit&id=<?php echo $facture->getDevis()->getId(); ?>" id="step1-tab" data-bs-toggle="tab" role="tab" aria-controls="step1" aria-selected="true">
                        <i class="fa fa-file-invoice-dollar"></i>
                        <span class="wizard-item-title">Devis</span>
                    </a>
                </li>
                <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top" title="Contrat">
                    <?php 
                        $wizard_status_contrat = "";
                        $wizard_status_contrat_url = "javascript:void(0)";
                        if($facture){
                            $contract = contract::findByDevis($facture->getDevis()->getId(),$_SESSION['agence'],$facture->getLangue());
                            if($contract->getStatus() == 3){
                                // contrat signé
                                $wizard_status_contrat = "wizard-success";
                                $wizard_status_contrat_url = "components/com_contract/controleurs/router.php?task=pdfContract&id=".$contract->getId();
                            }
                            elseif($contract->getStatus() == 4){
                                // contrat refusé
                                $wizard_status_contrat = "wizard-danger";
                                $wizard_status_contrat_url = "components/com_contract/controleurs/router.php?task=pdfContract&id=".$contract->getId();
                            }elseif($contract->getStatus() == 1 || $contract->getStatus() == 2){
                                $wizard_status_contrat = "wizard-warning";
                                $wizard_status_contrat_url = "components/com_contract/controleurs/router.php?task=pdfContract&id=".$contract->getId();
                            }
                        }
                    ?>
                    <a <?php if($wizard_status_contrat_url != "javascript:void(0)") :?> target="_blank" <?php endif;?> class="<?=$wizard_status_contrat?> nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center" href="<?=$wizard_status_contrat_url?>" id="step2-tab" data-bs-toggle="tab" role="tab" aria-controls="step2" aria-selected="false" title="Contract">
                        <i class="fa fa-file-invoice-dollar"></i>
                        <span class="wizard-item-title">Contrat</span>
                    </a>
                </li>
                <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top" title="Facture">
                    <?php
                        $wizard_status_invoice = "";
                        if($facture){
                            $wizard_status_invoice = "wizard-success";
                        }
                    ?>
                    <a class="<?=$wizard_status_invoice?> nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center" href="javascript:void(0)" id="step3-tab" data-bs-toggle="tab" role="tab" aria-controls="step3" aria-selected="false">
                        <i class="fa fa-file-invoice"></i>
                        <span class="wizard-item-title">Facture</span>
                    </a>
                </li>
                <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top" title="Paiements">
                    <?php
                        $wizard_status_payment = "";
                        if($facture){
                            if($facture->getTotal() == $facture->getReste()){
                                $wizard_status_payment = "wizard-danger";
                            }elseif($facture->getTotal() > $facture->getReste() && $facture->getReste() > 0)	{
                                $wizard_status_payment = "wizard-warning";
                            }elseif($facture->getReste() <= 0){
                                $wizard_status_payment = "wizard-success";
                            }
                        }
                        
                    ?>
                    <a class="<?=$wizard_status_payment?> nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>" id="step3-tab" data-bs-toggle="tab" role="tab" aria-controls="step3" aria-selected="false">
                        <i class="far fa-money-bill-alt"></i>
                        <span class="wizard-item-title">Paiements</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php if($facture->getRemarqueMoi()) :?>
         <div class="col-12 mt-5">
             <div class="text-center">
                 <?=$facture->getRemarqueMoi()?>
            </div>
         </div>
     <?php endif;?>
</div>