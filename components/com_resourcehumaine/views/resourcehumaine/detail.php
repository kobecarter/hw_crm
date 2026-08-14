<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Resource humaine</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Resource humaine : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php include("components/com_resourcehumaine/views/resourcehumaine/_profile_header.php"); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="div-table-information customer-details-group">
                            <div class="row justify-content-center">
                                <div class="col-7">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Matricule</h6>
                                                        <p><?= $resourcehumaine->getReference() ? $resourcehumaine->getReference() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Référence pointage</h6>
                                                        <p><?= $resourcehumaine->getReferencePointage() ? $resourcehumaine->getReferencePointage() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>CIN</h6>
                                                        <p><?= $resourcehumaine->getCin() ? $resourcehumaine->getCin() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Prénom</h6>
                                                        <p><?= $resourcehumaine->getFirstName() ? $resourcehumaine->getFirstName() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Nom</h6>
                                                        <p><?= $resourcehumaine->getLastName() ? $resourcehumaine->getLastName() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Email</h6>
                                                        <p><?= $resourcehumaine->getEmail() ? $resourcehumaine->getEmail() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Téléphone</h6>
                                                        <p><?= $resourcehumaine->getPhone() ? $resourcehumaine->getPhone() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Adresse</h6>
                                                        <p><?= $resourcehumaine->getAddress() ? $resourcehumaine->getAddress() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Ville</h6>
                                                        <p><?= $resourcehumaine->getCity() ? $resourcehumaine->getCity() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Source de prospection</h6>
                                                        <p><?= $resourcehumaine->getProspectingSource() ? $resourcehumaine->getProspectingSource() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Numéro de CNSS</h6>
                                                        <p><?= $resourcehumaine->getCnssNumber() ? $resourcehumaine->getCnssNumber() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Salaire premier jour</h6>
                                                        <p><?= $resourcehumaine->getSalaireInitial() !== null && $resourcehumaine->getSalaireInitial() !== '' ? number_format((float) $resourcehumaine->getSalaireInitial(), 0, ',', ' ') . ' DH' : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Salaire actuel</h6>
                                                        <p><?= $resourcehumaine->getSalaireActuel() !== null && $resourcehumaine->getSalaireActuel() !== '' ? number_format((float) $resourcehumaine->getSalaireActuel(), 0, ',', ' ') . ' DH' : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Fonction</h6>
                                                        <p><?= $resourcehumaine->getFunction() ? $resourcehumaine->getFunction() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Statut</h6>
                                                        <p><?= $resourcehumaine->getStatus() ? $resourcehumaine->getStatus() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Date début</h6>
                                                        <p><?= normaldate($resourcehumaine->getStartDate()) ? $resourcehumaine->getStartDate() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Date de signature de contrat</h6>
                                                        <p><?= normaldate($resourcehumaine->getContractSigningDate()) ? $resourcehumaine->getContractSigningDate() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Date fin</h6>
                                                        <p><?= normaldate($resourcehumaine->getEndDate()) ? $resourcehumaine->getEndDate() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Remarque</h6>
                                                        <p><?= normaldate($resourcehumaine->getRemark()) ? $resourcehumaine->getRemark() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $start_date = $resourcehumaine->getStartDate();
                        $end_date = $resourcehumaine->getEndDate() ? $resourcehumaine->getEndDate() : date("Y-m-d");
                        $total_holidays_consumed = 0;
                        foreach ($absences as $value) {
                            $total_holidays_consumed += ($value->getNatureOfAbsence() == 1 ? $value->getNumberOfDays() : 0);
                        }
                        $anneesConge = array();
                        $total_holidays = 0;
                        for ($i = date("Y", strtotime($start_date)); $i <= date("Y", strtotime($end_date)); $i++) {
                            $total_holidays_per_year = 0;
                            $moisDetail = array();
                            foreach (months() as $month) {
                                $month_date = date("Y-m-t", strtotime($i . "-" . $month['number'] . "-1"));
                                $start_month_date = date("Y-m-d", strtotime($start_date));
                                $end_month_date = date("Y-m-d", strtotime($end_date));
                                $acquis = ($month_date >= $start_month_date && $month_date <= $end_month_date);
                                if ($acquis) {
                                    $total_holidays_per_year += 1.5;
                                    $total_holidays += 1.5;
                                }
                                $moisDetail[] = array('name' => $month['name'], 'acquis' => $acquis);
                            }
                            $anneesConge[] = array('annee' => $i, 'mois' => $moisDetail, 'total' => $total_holidays_per_year);
                        }
                        $total_holidays_restant = $total_holidays - $total_holidays_consumed;

                        $statsPointageMoisDetail = pointageweb::calculerStatsMois($resourcehumaine, date('Y-m'));
                        $absencesNonJustifieesMoisDetail = 0;
                        foreach (absence::findByDateMonthly($resourcehumaine->getId(), date('Y-m')) as $uneAbsenceMoisDetail) {
                            if ($uneAbsenceMoisDetail->getNatureOfAbsence() == 3) {
                                $absencesNonJustifieesMoisDetail += $uneAbsenceMoisDetail->getNumberOfDays();
                            }
                        }
                        ?>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex">
                                <div class="card flex-fill mb-0 leave-gauge-card">
                                    <div class="card-body">
                                        <div class="leave-gauge-row">
                                            <div class="leave-gauge-canvas-wrap">
                                                <canvas id="leaveGaugeCanvas" width="150" height="150" data-total="<?= (float) $total_holidays ?>" data-restant="<?= (float) $total_holidays_restant ?>"></canvas>
                                                <div class="leave-gauge-center">
                                                    <span class="leave-gauge-value" id="leaveGaugeValue">0</span>
                                                    <span class="leave-gauge-unit">jours restants</span>
                                                </div>
                                            </div>
                                            <div class="leave-gauge-legend">
                                                <div class="leave-gauge-legend-item"><span class="leave-dot leave-dot-total"></span> Congé acquis <b><?= $total_holidays ?> j</b></div>
                                                <div class="leave-gauge-legend-item"><span class="leave-dot leave-dot-consumed"></span> Congé consommé <b><?= $total_holidays_consumed ?> j</b></div>
                                                <div class="leave-gauge-legend-item"><span class="leave-dot leave-dot-remaining"></span> Congé restant <b><?= $total_holidays_restant ?> j</b></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6 d-flex">
                                <div class="card flex-fill mb-0">
                                    <div class="card-body">
                                        <div class="dash-widget-header">
                                            <span class="dash-widget-icon bg-9" data-toggle="tooltip" title="Cumul des minutes de retard à l'arrivée sur le mois en cours, calculé à partir du pointage web (référence 09:00, jamais au-delà de 2h sinon compté en absence).">
                                                <i class="fa fa-clock"></i>
                                            </span>
                                            <div class="dash-count">
                                                <div class="dash-title">Retard ce mois-ci</div>
                                                <div class="dash-counts">
                                                    <p><?= floor($statsPointageMoisDetail['retard_minutes'] / 60) ?>h<?= str_pad($statsPointageMoisDetail['retard_minutes'] % 60, 2, '0', STR_PAD_LEFT) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-muted mt-3 mb-0"><?= $statsPointageMoisDetail['retard_jours'] ?> jour(s) concerné(s)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex">
                                <div class="card flex-fill mb-0">
                                    <div class="card-body">
                                        <div class="dash-widget-header">
                                            <span class="dash-widget-icon bg-1" data-toggle="tooltip" title="Jours d'absence 'Non justifié' enregistrés ce mois-ci, y compris ceux détectés automatiquement (aucun pointage, ou retard de plus de 2h).">
                                                <i class="fa fa-user-clock"></i>
                                            </span>
                                            <div class="dash-count">
                                                <div class="dash-title">Absence(s) ce mois-ci</div>
                                                <div class="dash-counts">
                                                    <p><?= $absencesNonJustifieesMoisDetail ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-muted mt-3 mb-0">Non justifié</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="javascript:void(0)" data-toggle="collapse" data-target="#leaveMonthDetail" class="text-decoration-none font-weight-bold">
                                <i class="fa fa-chevron-down mr-1"></i> Détail mensuel par année
                            </a>
                            <div class="collapse mt-3" id="leaveMonthDetail">
                                <div class="table-responsive">
                                    <table class="table mb-0 text-center">
                                        <thead>
                                            <tr>
                                                <th class="text-secondary">Année</th>
                                                <?php foreach (months() as $month) : ?>
                                                    <th><?= $month['name'] ?></th>
                                                <?php endforeach; ?>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($anneesConge as $annee) : ?>
                                                <tr>
                                                    <td><?= $annee['annee'] ?></td>
                                                    <?php foreach ($annee['mois'] as $mois) : ?>
                                                        <th><?= $mois['acquis'] ? '<span class="text-success">1.5</span>' : '0' ?></th>
                                                    <?php endforeach; ?>
                                                    <th><span class="text-success"><?= $annee['total'] ?></span></th>
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

        <?php
        $salaireInitial = $resourcehumaine->getSalaireInitial();
        $salaireActuel = $resourcehumaine->getSalaireActuel();
        $salaireRenseigne = $salaireInitial !== null && $salaireInitial !== '' && $salaireActuel !== null && $salaireActuel !== '';
        $evolutionSalairePct = null;
        if ($salaireRenseigne && (float) $salaireInitial > 0) {
            $evolutionSalairePct = round(((((float) $salaireActuel) - ((float) $salaireInitial)) / ((float) $salaireInitial)) * 100, 1);
        }
        $maxSalaireAffiche = max((float) $salaireInitial, (float) $salaireActuel, 1);
        $salairePxMin = 50;
        $salairePxMax = 170;
        $hauteurInitialePx = $salaireRenseigne ? round($salairePxMin + (((float) $salaireInitial / $maxSalaireAffiche) * ($salairePxMax - $salairePxMin))) : $salairePxMin;
        $hauteurActuellePx = $salaireRenseigne ? round($salairePxMin + (((float) $salaireActuel / $maxSalaireAffiche) * ($salairePxMax - $salairePxMin))) : $salairePxMin;
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card salary-evolution-card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fa fa-coins mr-2"></i>Évolution salariale</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$salaireRenseigne) : ?>
                            <p class="text-muted mb-0">
                                Salaire non renseigné.
                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                                    <a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId() ?>">Compléter la fiche</a>
                                <?php endif; ?>
                            </p>
                        <?php else : ?>
                            <div class="salary-3d-scene">
                                <div class="salary-3d-floor">
                                    <div class="salary-3d-bar-group">
                                        <div class="salary-3d-bar salary-3d-bar-initial" style="--bar-h: <?= $hauteurInitialePx ?>px;">
                                            <div class="salary-3d-face salary-3d-top"></div>
                                            <div class="salary-3d-face salary-3d-front"></div>
                                            <div class="salary-3d-face salary-3d-right"></div>
                                        </div>
                                    </div>
                                    <div class="salary-3d-bar-group">
                                        <div class="salary-3d-bar salary-3d-bar-actuel" style="--bar-h: <?= $hauteurActuellePx ?>px;">
                                            <div class="salary-3d-face salary-3d-top"></div>
                                            <div class="salary-3d-face salary-3d-front"></div>
                                            <div class="salary-3d-face salary-3d-right"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="salary-3d-legend">
                                <div class="salary-3d-legend-item">
                                    <span class="salary-dot salary-dot-initial"></span> Salaire premier jour
                                    <b><?= number_format((float) $salaireInitial, 0, ',', ' ') ?> DH</b>
                                </div>
                                <?php if ($evolutionSalairePct !== null) : ?>
                                    <div class="salary-evolution-badge <?= $evolutionSalairePct > 0 ? 'salary-up' : ($evolutionSalairePct < 0 ? 'salary-down' : 'salary-flat') ?>">
                                        <i class="fa fa-<?= $evolutionSalairePct > 0 ? 'arrow-up' : ($evolutionSalairePct < 0 ? 'arrow-down' : 'equals') ?>"></i>
                                        <?= ($evolutionSalairePct > 0 ? '+' : '') . $evolutionSalairePct ?>%
                                    </div>
                                <?php endif; ?>
                                <div class="salary-3d-legend-item">
                                    <span class="salary-dot salary-dot-actuel"></span> Salaire actuel
                                    <b><?= number_format((float) $salaireActuel, 0, ',', ' ') ?> DH</b>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Liste des fichiers</h4>
                    </div>
                    <div class="card-body">
                        <?php $documentsManquantsDetail = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $files); ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <?php if (!empty($documentsManquantsDetail)) : ?>
                                    <p class="text-danger mb-0"><i class="fa fa-exclamation-triangle mr-1"></i> Profil non finalisé — documents manquants : <?= htmlspecialchars(implode(', ', $documentsManquantsDetail)) ?>. <a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>">Voir le détail</a></p>
                                <?php else : ?>
                                    <p class="text-success mb-0"><i class="fa fa-check-circle mr-1"></i> Dossier complet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 msgbox"></div>

                            <div class="col-sm-12">
                                <div class="div-file">
                                    <ul class="sortable grid">
                                        <?php
                                        foreach ($files as $file) {
                                        ?>
                                            <li class="d-flex justify-content-between">
                                                <a href="./images/resourceshumaines/files/<?php echo $file->getFile(); ?>" target="_blank">
                                                    <div class="d-flex align-items-center">
                                                        <i class="far fa-file-pdf icon" alt="<?= $file->getTitle() ?>" title="Cliquer pour voir le fichier"></i>
                                                        <h3><?= $file->getTitle() ?></h3>
                                                    </div>
                                                </a>
                                                <div>
                                                    <a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId() ?>&id_file=<?= $file->getId() ?>" class="btn btn-info btn-sm editFile"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:void(0)" id="delete_<?php echo $file->getId(); ?>" class="btn btn-danger btn-sm deleteFile"><i class="fa fa-trash"></i></a>
                                                </div>
                                                
                                            </li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                    if (sizeof($files) <= 0) :
                                    ?>
                                        <p class="text-center">Il n'y a pas de fichiers</p>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Absences et congés</h4>
                    </div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Date D'absence</th>
                                        <th>Date de retour</th>
                                        <th>Nombre des jours</th>
                                        <th>Nature d'absence</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($absences as $absence): ?>
                                        <tr>
                                            <td><?php echo $absence->getId(); ?></td>
                                            <td><?php echo normaldate($absence->getStartDate()); ?></td>
                                            <td><?php echo normaldate($absence->getEndDate()); ?></td>
                                            <td><b><?php echo $absence->getNumberOfDays(); ?></b></td>
                                            <td>
                                                <?php if ($absence->getNatureOfAbsence() == 1) : ?>
                                                    <span class="badge badge-secondary">Congé</span>
                                                <?php elseif ($absence->getNatureOfAbsence() == 2) : ?>
                                                    <span class="badge badge-info">Maladie (Certificat)</span>
                                                <?php elseif ($absence->getNatureOfAbsence() == 3) : ?>
                                                    <span class="badge badge-primary">Non justifié</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($absence->getNatureOfAbsence() != 1) : ?>
                                                    <?php if ($absence->getStatus() == 1) : ?>
                                                        <span class="badge badge-success">Déductible</span>
                                                    <?php else : ?>
                                                        <span class="badge badge-danger">Non déductible</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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
    $(function() {

        // Jauge circulaire "Congé restant" (Canvas, arc animé depuis 0 jusqu'à la vraie
        // proportion). Bien plus parlant qu'un chiffre plat pour visualiser en un coup d'œil
        // la part de congé déjà consommée.
        (function() {
            var canvas = document.getElementById('leaveGaugeCanvas');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            var dpr = window.devicePixelRatio || 1;
            var size = canvas.width;
            canvas.width = size * dpr;
            canvas.height = size * dpr;
            canvas.style.width = size + 'px';
            canvas.style.height = size + 'px';
            ctx.scale(dpr, dpr);

            var total = parseFloat(canvas.getAttribute('data-total')) || 0;
            var restant = parseFloat(canvas.getAttribute('data-restant')) || 0;
            var proportion = total > 0 ? Math.max(0, Math.min(1, restant / total)) : 0;

            var cx = size / 2,
                cy = size / 2,
                radius = size / 2 - 12;

            function drawGauge(progress) {
                ctx.clearRect(0, 0, size, size);
                // piste de fond
                ctx.beginPath();
                ctx.arc(cx, cy, radius, 0, Math.PI * 2);
                ctx.lineWidth = 14;
                ctx.strokeStyle = 'rgba(109, 40, 217, 0.10)';
                ctx.stroke();
                // arc de progression (part du congé restant), départ en haut, sens horaire
                var startAngle = -Math.PI / 2;
                var endAngle = startAngle + Math.PI * 2 * proportion * progress;
                var grad = ctx.createLinearGradient(0, 0, size, size);
                grad.addColorStop(0, '#6d28d9');
                grad.addColorStop(1, '#4338ca');
                ctx.beginPath();
                ctx.arc(cx, cy, radius, startAngle, endAngle);
                ctx.lineWidth = 14;
                ctx.lineCap = 'round';
                ctx.strokeStyle = grad;
                ctx.stroke();
            }

            drawGauge(0);
            var $valeur = $('#leaveGaugeValue');
            if (typeof gsap !== 'undefined') {
                var etat = { p: 0, v: 0 };
                gsap.to(etat, {
                    p: 1,
                    v: restant,
                    duration: 1.3,
                    ease: 'power2.out',
                    onUpdate: function() {
                        drawGauge(etat.p);
                        $valeur.text(Math.round(etat.v));
                    }
                });
            } else {
                drawGauge(1);
                $valeur.text(Math.round(restant));
            }
        })();

        // Barres 3D "salaire premier jour / salaire actuel" : montée depuis 0 à l'ouverture,
        // léger flottement continu ensuite pour renforcer l'illusion de profondeur.
        (function() {
            var $bars = $('.salary-3d-bar');
            if (!$bars.length) return;
            var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (typeof gsap === 'undefined' || reduceMotion) return;

            gsap.from($bars, {
                scaleY: 0,
                transformOrigin: 'bottom center',
                duration: 1.1,
                stagger: 0.18,
                ease: 'back.out(1.5)',
                clearProps: 'transform',
                onComplete: function() {
                    gsap.to('.salary-3d-bar-group', {
                        z: 8,
                        duration: 1.8,
                        ease: 'sine.inOut',
                        yoyo: true,
                        repeat: -1,
                        stagger: 0.3
                    });
                }
            });
        })();

        var msgsucces = "Absence supprimé avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_resourcehumaine/controleurs/router.php?task=deleteAbsenceResourceHumaine", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        // envoi du formulaire en ajax
        $('form#absenceResourceHumaineForm').ajaxForm({
            beforeSubmit: function() {
                $("#absenceResourceHumaineForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#absenceResourceHumaineForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Absence ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Absence modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#absenceResourceHumaineForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#absenceResourceHumaineForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#absenceResourceHumaineForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>