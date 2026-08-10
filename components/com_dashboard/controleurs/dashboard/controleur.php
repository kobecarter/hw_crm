    <?php

    if (isset($task) && !empty($task)) {
        switch ($task) {
            case 'validateCodePin':
                validateCodePin($_POST);
                break; 
            case 'stats':
                stats($_POST);
                break;
            case 'statsGlobal':
                statsGlobal($_POST);
                break;    
            case 'getChiffreYear':
                getChiffreYear($_POST);
                break;
            case 'getCAperCompany':
                getCAperCompany($_POST);
                break;
        }
    }

    function getCAperCompany($data)
    {
        if(isset($data['year']) && !empty($data['year'])){
            $year = $data['year'];
            $from = $year . '-01-01';
            $to = $year . '-12-31';
            
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
		    
		    echo $ca_verse . '__--__--__' . $ca_hwlabel . '__--__--__' . $ca_hello_dubai . '__--__--__' . $charge_verse . '__--__--__' . $charge_hwlabel . '__--__--__' . $charge_hello_dubai;
        }
    }
    
    function validateCodePin($data){
        global $secureCode;
        $indices = array("code_pin");
        if (fieldCheck($data, $indices)) {
            if (isset($data['code_pin']) && $data['code_pin'] == $secureCode) {
                $_SESSION['secure'] = $secureCode;
                echo '1';
            }else{
                echo '2';
            }
        } else {
            echo '0';
        }
    }

    function getChiffreYear($data)
    {
        if(isset($data['year']) && !empty($data['year'])){
            $year = $data['year'];
            $paymentPerMonth = $paymentPerMonthEur = $paymentPerMonthPound = $paymentPerMonthDollar = $paymentPerMonthAed = $chargePerMonth = array();
            
            $devisNumber = devis::count(false, $year,$_SESSION['agence']);
            $devisNumberInvalide = devis::count(2, $year,$_SESSION['agence']);
            $devisNumberInvalide2 = devis::count(2, $year - 1,$_SESSION['agence']);
            $devisNumberInvalide3 = devis::count(2, $year - 2,$_SESSION['agence']);

            $factureNumber = facture::count(false, $year,false,$_SESSION['agence']);
            $factureTotal = facture::total(false, $year,'DH',$_SESSION['agence']);
            $factureTotalEur = facture::total(false, $year, '€',$_SESSION['agence']);
            $factureTotalPound = facture::total(false, $year, '£',$_SESSION['agence']);
            $factureTotalDollar = facture::total(false, $year, '$',$_SESSION['agence']);
            $factureTotalAed = facture::total(false, $year, 'AED',$_SESSION['agence']);
            $creances = facture::getCreance(false,'DH',$_SESSION['agence']);

            // Les créances
            $creancesYear = facture::getCreance($year,'DH',$_SESSION['agence']);
            $creancesYearEuro = facture::getCreance($year,'€',$_SESSION['agence']);
            $creancesYearPound = facture::getCreance($year,'£',$_SESSION['agence']);
            $creancesYearDollar = facture::getCreance($year,'$',$_SESSION['agence']);
            $creancesYearAed = facture::getCreance($year,'AED',$_SESSION['agence']);
            $creancesYear2 = facture::getCreance($year - 1,'DH',$_SESSION['agence']);
            $creancesYear3 = facture::getCreance($year - 2,'DH',$_SESSION['agence']);

            // Nombre des clients
            $clientNumber = client::count(false,$_SESSION['agence']);
            $clientNumberYear = client::count($year,$_SESSION['agence']);
            $clientNumberYear2 = client::count($year - 1,$_SESSION['agence']);
            $clientNumberYear3 = client::count($year - 2,$_SESSION['agence']);
            
            $factureNumber1 = facture::count(1, $year,false,$_SESSION['agence']);
            $factureNumber2 = facture::count(2, $year,false,$_SESSION['agence']);
            $factureNumber3 = facture::count(3, $year,false,$_SESSION['agence']);
            ?>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-1">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <div class="dash-count">
                                <div class="dash-title">Créances</div>
                                <div class="dash-counts money-sensitive">
                                    <p><?php echo number_format($creances, 2, ',', ' '); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-5" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-3 mb-0 money-sensitive">
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYear, 2, ',', ' '); ?> Dh</span> année <?php echo $year; ?><br>
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearEuro, 2, ',', ' '); ?> €</span> année <?php echo $year; ?><br>
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearPound, 2, ',', ' '); ?> £</span> année <?php echo $year; ?><br>
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearDollar, 2, ',', ' '); ?> $</span> année <?php echo $year; ?><br>
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo number_format($creancesYearAed, 2, ',', ' '); ?> AED</span> année <?php echo $year; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <a href="index.php?option=com_client" class="dash-widget-icon bg-2">
                                <i class="fas fa-users"></i>
                            </a>
                            <div class="dash-count">
                                <div class="dash-title">Clients</div>
                                <div class="dash-counts money-sensitive">
                                    <p><?php echo number_format($clientNumber, 0, '', ','); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-6" role="progressbar" style="width: 65%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-3 mb-0">
                            <span class="text-success mr-1"><i class="fas fa-user mr-1"></i><?php echo $clientNumberYear; ?></span> année <?php echo $year; ?><br>
                            <span class="text-success mr-1"><i class="fas fa-user mr-1"></i><?php echo $clientNumberYear2; ?></span> année <?php echo ($year - 1); ?><br>
                            <span class="text-success mr-1"><i class="fas fa-user mr-1"></i><?php echo $clientNumberYear3; ?></span> année <?php echo ($year - 2); ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <a href="index.php?option=com_facture" class="dash-widget-icon bg-3">
                                <i class="fas fa-file-alt"></i>
                            </a>
                            <div class="dash-count">
                                <div class="dash-title">Factures</div>
                                <div class="dash-counts money-sensitive">
                                    <p><?php echo number_format($factureNumber, 0, '', ','); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-7" role="progressbar" style="width: 85%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-3 mb-0 money-sensitive">
                            <span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotal, 2, ',', ' '); ?> Dh</span> année <?php echo $year; ?><br>
                            <span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalEur, 2, ',', ' '); ?> €</span> année <?php echo $year; ?><br>
                            <span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalPound, 2, ',', ' '); ?> £</span> année <?php echo $year; ?><br>
                            <span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalDollar, 2, ',', ' '); ?> $</span> année <?php echo $year; ?><br>
                            <span class="text-success mr-1"><i class="fas fa-money-bill-alt mr-1"></i><?php echo number_format($factureTotalAed, 2, ',', ' '); ?> AED</span> année <?php echo $year; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <a href="index.php?option=com_devis" class="dash-widget-icon bg-4">
                                <i class="far fa-file"></i>
                            </a>
                            <div class="dash-count">
                                <div class="dash-title">Devis</div>
                                <div class="dash-counts money-sensitive">
                                    <p><?php echo number_format($devisNumber, 0, '', ','); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="progress progress-sm mt-3">
                            <div class="progress-bar bg-8" role="progressbar" style="width: 45%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-muted mt-3 mb-0">
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo $devisNumberInvalide; ?></span> devis invalide : année <?php echo $year; ?><br>
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo $devisNumberInvalide2; ?></span> devis invalide : année <?php echo $year - 1; ?><br>
                            <span class="text-danger mr-1"><i class="fas fa-arrow-down mr-1"></i><?php echo $devisNumberInvalide3; ?></span> devis invalide : année <?php echo $year - 2; ?>
                        </p>
                    </div>
                </div>
            </div>
            __--__--__
            <div>
                <span>En MAD</span>
                <p class="h4 text-primary mr-5"><?php echo number_format(payment::total($year,false,'DH',$_SESSION['agence']), 2, ',', ' '); ?></p>
            </div>
            <div>
                <span>En Euro</span>
                <p class="h4 text-success mr-5"><?php echo number_format(payment::total($year, false, '€',$_SESSION['agence']), 2, ',', ' '); ?></p>
            </div>
            <div>
                <span>En Pound</span>
                <p class="h4 text-warning mr-5"><?php echo number_format(payment::total($year, false, '£',$_SESSION['agence']), 2, ',', ' '); ?></p>
            </div>
            <div>
                <span>En Dollar</span>
                <p class="h4 text-info mr-5"><?php echo number_format(payment::total($year, false, '$',$_SESSION['agence']), 2, ',', ' '); ?></p>
            </div>
            <div>
                <span>En Aed</span>
                <p class="h4 text-secondary mr-5"><?php echo number_format(payment::total($year, false, 'AED',$_SESSION['agence']), 2, ',', ' '); ?></p>
            </div>
            <?php if($_SESSION['user']->isSuperUser() != false):?>
                <div>
                    <span>Charges</span>
                    <p class="h4 text-danger mr-5"><?php echo number_format(charge::total($year,false,$_SESSION['agence']), 2, ',', ' '); ?></p>
                </div>
            <?php endif;?>
            __--__--__
            <div class="row">
                <div class="col-4">
                    <div class="mt-4">
                        <p class="mb-2 text-truncate"><i class="fas fa-circle text-danger mr-1"></i> Impayée</p>
                        <h5><?php echo $factureNumber3; ?></h5>
                    </div>
                </div>
                <div class="col-4">
                    <div class="mt-4">
                        <p class="mb-2 text-truncate"><i class="fas fa-circle text-success mr-1"></i> Payée</p>
                        <h5><?php echo $factureNumber1; ?></h5>
                    </div>
                </div>
                <div class="col-4">
                    <div class="mt-4">
                        <p class="mb-2 text-truncate"><i class="fas fa-circle text-warning mr-1"></i> Payée partiallement</p>
                        <h5><?php echo $factureNumber2; ?></h5>
                    </div>
                </div>
            </div>
            
            <?php 
            for ($i = 1; $i < 13; $i++) {
                $total = payment::total($year, $i,'DH',$_SESSION['agence']);
                $total2 = payment::total($year, $i, '€',$_SESSION['agence']);
                $total3 = payment::total($year, $i, '£',$_SESSION['agence']);
                $total4 = payment::total($year, $i, '$',$_SESSION['agence']);
                $total6 = payment::total($year, $i, 'AED',$_SESSION['agence']);
                if($_SESSION['user']->isSuperUser() != false){
                    $total5 = charge::total($year, $i,$_SESSION['agence']);
                }
                array_push($paymentPerMonth, $total);
                array_push($paymentPerMonthEur, $total2);
                array_push($paymentPerMonthPound, $total3);
                array_push($paymentPerMonthDollar, $total4);
                array_push($paymentPerMonthAed, $total6);
                if($_SESSION['user']->isSuperUser() != false){
                    array_push($chargePerMonth, $total5);
                }						
            } 
            echo '__--__--__' . implode(',', $paymentPerMonth) . '__--__--__';
            echo implode(',', $paymentPerMonthEur) . '__--__--__';
            echo implode(',', $paymentPerMonthPound) . '__--__--__';
            echo implode(',', $paymentPerMonthDollar) . '__--__--__';
            echo implode(',', $paymentPerMonthAed) . '__--__--__';
            if($_SESSION['user']->isSuperUser() != false){
                echo implode(',', $chargePerMonth);
            }
            
            echo '__--__--__' . $factureNumber1 . '__--__--__';
            echo $factureNumber3 . '__--__--__';
            echo $factureNumber2;
        }
    }

    function stats($data)
    {
        $from = dateBD($data['from']);
        $to = dateBD($data['to']);
        
        $creances = facture::getCreanceByDate($from, $to,'DH',$_SESSION['agence']);
        $creancesEuro = facture::getCreanceByDate($from, $to,'€',$_SESSION['agence']);
        $creancesPound = facture::getCreanceByDate($from, $to,'£',$_SESSION['agence']);
        $creancesDollar = facture::getCreanceByDate($from, $to,'$',$_SESSION['agence']);
        $creancesAed = facture::getCreanceByDate($from, $to,'AED',$_SESSION['agence']);

        $factureTotal = facture::getCAbyDate(false, $from, $to,'DH',$_SESSION['agence']);
        $factureTotalEuro = facture::getCAbyDate(false, $from, $to,'€',$_SESSION['agence']);
        $factureTotalPound = facture::getCAbyDate(false, $from, $to,'£',$_SESSION['agence']);
        $factureTotalDollar = facture::getCAbyDate(false, $from, $to,'$',$_SESSION['agence']);
        $factureTotalAed = facture::getCAbyDate(false, $from, $to,'AED',$_SESSION['agence']);

        $totalReglement = payment::getReglementbyDate($from, $to,'DH',$_SESSION['agence']);
        $totalReglementEuro = payment::getReglementbyDate($from, $to,'€',$_SESSION['agence']);
        $totalReglementPound = payment::getReglementbyDate($from, $to,'£',$_SESSION['agence']);
        $totalReglementDollar = payment::getReglementbyDate($from, $to,'$',$_SESSION['agence']);
        $totalReglementAed = payment::getReglementbyDate($from, $to,'AED',$_SESSION['agence']);

        $charges = charge::getCharge($from, $to,$_SESSION['agence']);
        ?>
        <div class="col-xl-3 col-sm-6 col-12">
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

        <div class="col-xl-3 col-sm-6 col-12">
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

        <div class="col-xl-3 col-sm-6 col-12">
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
        <?php
    }
    
    function statsGlobal($data)
    {
        $from = dateBD($data['from']);
        $to = dateBD($data['to']);
        
        $creances = facture::getCreanceByDate($from, $to,'DH',false);
        $creancesEuro = facture::getCreanceByDate($from, $to,'€',false);
        $creancesPound = facture::getCreanceByDate($from, $to,'£',false);
        $creancesDollar = facture::getCreanceByDate($from, $to,'$',false);
        $creancesAed = facture::getCreanceByDate($from, $to,'AED',false);

        $factureTotal = facture::getCAbyDate(false, $from, $to,'DH',false);
        $factureTotalEuro = facture::getCAbyDate(false, $from, $to,'€',false);
        $factureTotalPound = facture::getCAbyDate(false, $from, $to,'£',false);
        $factureTotalDollar = facture::getCAbyDate(false, $from, $to,'$',false);
        $factureTotalAed = facture::getCAbyDate(false, $from, $to,'AED',false);

        $totalReglement = payment::getReglementbyDate($from, $to,'DH',false);
        $totalReglementEuro = payment::getReglementbyDate($from, $to,'€',false);
        $totalReglementPound = payment::getReglementbyDate($from, $to,'£',false);
        $totalReglementDollar = payment::getReglementbyDate($from, $to,'$',false);
        $totalReglementAed = payment::getReglementbyDate($from, $to,'AED',false);

        $charges = charge::getCharge($from, $to, false, 'DH');
        $chargesEuro = charge::getCharge($from, $to, false, '€');
        $chargesPound = charge::getCharge($from, $to, false, '£');
        $chargesDollar = charge::getCharge($from, $to, false, '$');
        $chargesAed = charge::getCharge($from, $to, false, 'AED');
        ?>
        <div class="col-xl-3 col-sm-6 col-12">
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

        <div class="col-xl-3 col-sm-6 col-12">
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

        <div class="col-xl-3 col-sm-6 col-12">
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
        __--__--__
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
	    
	    echo $ca_verse . '__--__--__' . $ca_hwlabel . '__--__--__' . $ca_hello_dubai . '__--__--__' . $charge_verse . '__--__--__' . $charge_hwlabel . '__--__--__' . $charge_hello_dubai;
    }

