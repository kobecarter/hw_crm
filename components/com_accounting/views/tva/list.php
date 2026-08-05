<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Tva</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Tva </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_accounting')) : ?>
                        <a href="index.php?option=com_accounting&task=tva" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une tva" target="_blank">
                            <i class="fas fa-plus"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
        $moisNomsFr = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
        $seuil = $agence->getTvaSeuilAlerte() !== null && $agence->getTvaSeuilAlerte() !== '' ? (float) $agence->getTvaSeuilAlerte() : 0;
        $tauxAgence = (float) $agence->getTva();
        $objectifAtteint = $seuil > 0 ? $simulation['tva_due'] <= $seuil : true;
        $ecartDeclaration = $declarationOfficielle - $simulation['tva_due'];

        // KPI d'alerte : liste des mois sans aucune déclaration de TVA enregistrée pour
        // l'agence courante, depuis janvier 2022 jusqu'au mois en cours inclus — permet de
        // repérer d'un coup d'œil les trous dans l'historique comptable (ex: mois jamais reçus
        // du comptable), indépendamment de $start_year/$end_year qui ne couvrent que la plage
        // déjà renseignée.
        $manquantsParAnnee = array();
        $anneeCourante = (int) date('Y');
        $moisCourant = (int) date('n');
        for ($y = 2022; $y <= $anneeCourante; $y++) {
            $moisMax = ($y == $anneeCourante) ? $moisCourant : 12;
            for ($m = 1; $m <= $moisMax; $m++) {
                $ligneMois = tva::findByDate($_SESSION['agence'], $y . '-' . sprintf('%02d', $m));
                if (empty($ligneMois)) {
                    if (!isset($manquantsParAnnee[$y])) {
                        $manquantsParAnnee[$y] = array();
                    }
                    $manquantsParAnnee[$y][] = array('num' => $m, 'nom' => $moisNomsFr[$m]);
                }
            }
        }
        $nbMoisManquants = array_sum(array_map('count', $manquantsParAnnee));

        // Rappel d'échéance de dépôt TVA — respecte la périodicité de l'agence (mensuelle ou
        // trimestrielle, cf. agence::getTvaPeriodicite()). On regarde d'abord la dernière période
        // pleinement écoulée : si elle n'est pas encore déclarée, c'est elle qu'on affiche avec sa
        // date limite (le 20 du mois suivant) ; sinon on affiche calmement la période en cours.
        $periodiciteTva = $agence->getTvaPeriodicite() === 'trimestriel' ? 'trimestriel' : 'mensuel';
        $periodeCloturee = tva::periodeReference($periodiciteTva, null, -1);
        $periodeDeclaree = true;
        $curseurPeriode = clone $periodeCloturee['debut'];
        while ($curseurPeriode <= $periodeCloturee['fin']) {
            if (empty(tva::findByDate($_SESSION['agence'], $curseurPeriode->format('Y-m')))) {
                $periodeDeclaree = false;
                break;
            }
            $curseurPeriode->modify('+1 month');
        }
        $periodeEcheance = $periodeDeclaree ? tva::periodeReference($periodiciteTva, null, 0) : $periodeCloturee;
        $joursAvantEcheance = (int) (new DateTime('today'))->diff($periodeEcheance['limite'])->format('%r%a');
        $echeanceUrgente = !$periodeDeclaree && $joursAvantEcheance <= 7;
        if (!$periodeDeclaree) {
            $echeanceTexte = $joursAvantEcheance < 0
                ? 'TVA de ' . $periodeEcheance['libelle'] . ' en retard de ' . abs($joursAvantEcheance) . ' jour' . (abs($joursAvantEcheance) > 1 ? 's' : '')
                : 'TVA de ' . $periodeEcheance['libelle'] . ' à déposer — dans ' . $joursAvantEcheance . ' jour' . ($joursAvantEcheance > 1 ? 's' : '');
        } else {
            $echeanceTexte = 'Dépôts à jour — période ' . $periodeEcheance['libelle'] . ' en cours';
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card <?= $nbMoisManquants > 0 ? 'tva-manquants-card kpi-blink' : '' ?> mb-0" id="tva-manquants-card">
                    <div class="card-body">
                        <?php if ($nbMoisManquants > 0) : ?>
                            <div class="d-flex align-items-start" id="tva-manquants-content">
                                <span class="dash-widget-icon bg-9 mr-3"><i class="fa fa-exclamation-triangle"></i></span>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 text-danger">
                                        <span id="tva-manquants-count"><?= $nbMoisManquants ?></span> mois sans déclaration TVA enregistrée depuis 2022
                                    </h5>
                                    <p class="text-muted mb-2" style="font-size:0.85rem;">
                                        Aucune ligne trouvée pour ces mois dans le suivi comptable ci-dessous — cliquez sur un mois pour
                                        l'ajouter rapidement (à compléter ensuite), ou vérifiez auprès du comptable.
                                    </p>
                                    <?php foreach ($manquantsParAnnee as $anneeM => $listeMois) : ?>
                                        <div class="mb-1 tva-manquants-annee" id="tva-manquants-annee-<?= $anneeM ?>">
                                            <strong><?= $anneeM ?></strong>
                                            (<span class="tva-manquants-annee-count"><?= count($listeMois) ?></span>) :
                                            <?php foreach ($listeMois as $moisInfo) : ?>
                                                <a href="javascript:void(0)" class="badge bg-danger-light tva-missing-month mr-1 mb-1"
                                                   data-annee="<?= $anneeM ?>" data-mois="<?= $moisInfo['num'] ?>" data-mois-nom="<?= $moisInfo['nom'] ?>"
                                                   data-toggle="tooltip" data-placement="top" data-original-title="Cliquez pour ajouter la TVA de <?= $moisInfo['nom'] ?> <?= $anneeM ?>">
                                                    <?= $moisInfo['nom'] ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="d-flex align-items-center">
                                <span class="dash-widget-icon bg-3 mr-3"><i class="fa fa-check"></i></span>
                                <div>
                                    <strong class="text-success">Aucun mois manquant</strong>
                                    <span class="text-muted"> — toutes les déclarations de TVA depuis 2022 sont enregistrées.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card tva-estimation-card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fa fa-bolt mr-2 text-warning"></i>Estimation CRM (temps réel, indicative)</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Calculée automatiquement à partir des règlements clients encaissés et des charges payées déjà
                            enregistrées dans le CRM — pour anticiper avant la fin du mois. <strong>Ne remplace pas la
                            déclaration officielle</strong>, calculée par votre comptable et enregistrée ci-dessous.
                        </p>

                        <form method="get" action="index.php" class="row align-items-end">
                            <input type="hidden" name="option" value="com_accounting">
                            <input type="hidden" name="task" value="tva">
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Mois</label>
                                    <select class="form-control" name="mois">
                                        <?php foreach ($moisNomsFr as $num => $nom) : ?>
                                            <option value="<?= $num ?>" <?= $num == $simMois ? 'selected' : '' ?>><?= $nom ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Année</label>
                                    <input type="number" class="form-control" name="annee" value="<?= (int) $simAnnee ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label>Crédit TVA du mois précédent (DH)</label>
                                    <input type="number" step="0.01" class="form-control" name="credit" value="<?= htmlspecialchars($simCredit) ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary">Calculer</button>
                            </div>
                        </form>

                        <div class="row mt-2">
                            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                                <div class="card flex-fill mb-0">
                                    <div class="card-body">
                                        <div class="dash-widget-header">
                                            <span class="dash-widget-icon bg-3"><i class="fa fa-arrow-down"></i></span>
                                            <div class="dash-count">
                                                <div class="dash-title">TVA collectée (ventes encaissées)</div>
                                                <div class="dash-counts"><p><?= number_format($simulation['tva_collectee'], 2, ',', ' ') ?> DH</p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                                <div class="card flex-fill mb-0">
                                    <div class="card-body">
                                        <div class="dash-widget-header">
                                            <span class="dash-widget-icon bg-2"><i class="fa fa-arrow-up"></i></span>
                                            <div class="dash-count">
                                                <div class="dash-title">TVA déductible (achats payés)</div>
                                                <div class="dash-counts"><p><?= number_format($simulation['tva_deductible'], 2, ',', ' ') ?> DH</p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                                <div class="card flex-fill mb-0">
                                    <div class="card-body">
                                        <div class="dash-widget-header">
                                            <span class="dash-widget-icon bg-4"><i class="fa fa-history"></i></span>
                                            <div class="dash-count">
                                                <div class="dash-title">Crédit reporté</div>
                                                <div class="dash-counts"><p><?= number_format($simulation['credit_reporte'], 2, ',', ' ') ?> DH</p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                                <div class="card flex-fill mb-0 <?= !$objectifAtteint ? 'kpi-blink' : '' ?>">
                                    <div class="card-body">
                                        <div class="dash-widget-header">
                                            <span class="dash-widget-icon <?= $objectifAtteint ? 'bg-3' : 'bg-9' ?>"><i class="fa fa-file-invoice-dollar"></i></span>
                                            <div class="dash-count">
                                                <div class="dash-title">TVA estimée à payer</div>
                                                <div class="dash-counts"><p><?= number_format($simulation['tva_due'], 2, ',', ' ') ?> DH</p></div>
                                            </div>
                                        </div>
                                        <?php if ($seuil > 0) : ?>
                                            <span class="badge <?= $objectifAtteint ? 'bg-success-light' : 'bg-danger-light' ?> mt-2">
                                                <?= $objectifAtteint ? 'Objectif respecté (≤ ' . number_format($seuil, 0, ',', ' ') . ' DH)' : 'Dépassement de ' . number_format($simulation['tva_due'] - $seuil, 2, ',', ' ') . ' DH' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($declarationExiste) : ?>
                            <div class="tva-ecart-callout <?= abs($ecartDeclaration) < 50 ? 'tva-ecart-ok' : 'tva-ecart-warn' ?> mt-3">
                                <i class="fa <?= abs($ecartDeclaration) < 50 ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
                                <strong>Comparaison avec la déclaration officielle de <?= $moisNomsFr[$simMois] ?> <?= $simAnnee ?></strong> :
                                le comptable a déclaré <strong><?= number_format($declarationOfficielle, 2, ',', ' ') ?> DH</strong>,
                                l'estimation CRM était de <strong><?= number_format($simulation['tva_due'], 2, ',', ' ') ?> DH</strong>
                                — écart de <strong><?= number_format(abs($ecartDeclaration), 2, ',', ' ') ?> DH</strong>.
                            </div>
                        <?php else : ?>
                            <div class="tva-ecart-callout tva-ecart-neutral mt-3">
                                <i class="fa fa-info-circle mr-2"></i>
                                Aucune déclaration officielle enregistrée pour <?= $moisNomsFr[$simMois] ?> <?= $simAnnee ?> pour l'instant —
                                ajoutez-la ci-dessous une fois reçue du comptable pour voir la comparaison.
                            </div>
                        <?php endif; ?>

                        <div class="row align-items-end mt-3">
                            <div class="col-sm-6 col-md-4">
                                <div class="form-group mb-0">
                                    <label>Simulateur — objectif TVA due (DH)</label>
                                    <input type="number" step="0.01" class="form-control" id="tva-objectif" value="<?= $seuil > 0 ? $seuil : '' ?>">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-8">
                                <p class="mb-0 mt-2 mt-md-0">
                                    Pour atteindre cet objectif : environ <strong id="tva-besoin-ht">—</strong> HT de factures d'achat
                                    complémentaires (<strong id="tva-besoin-ttc">—</strong> TTC à <?= $tauxAgence ?>% de TVA).
                                </p>
                            </div>
                        </div>

                        <?php
                        // Le ZIP comptable doit couvrir une période TVA complète : un mois pour une
                        // agence mensuelle, tout le trimestre civil pour une agence trimestrielle —
                        // sinon un export "mensuel" par défaut couperait artificiellement les 2/3 des
                        // achats/ventes d'un trimestre réel.
                        $periodeExportDefaut = tva::periodeReference($periodiciteTva, new DateTime(sprintf('%04d-%02d-01', $simAnnee, $simMois)), 0);
                        ?>
                        <div class="row align-items-end mt-4 pt-3" style="border-top:1px dashed var(--border-soft, rgba(30,30,60,0.08));">
                            <div class="col-md-12 mb-2">
                                <strong><i class="far fa-file-excel mr-2 text-success"></i>Tableau comptable à télécharger</strong>
                                <p class="text-muted mb-0" style="font-size:0.85rem;">
                                    Télécharge un ZIP prêt à transmettre au comptable : le tableau Excel (ventes/paiements comptés dans la
                                    TVA collectée, achats/charges comptés dans la TVA déductible, et la liste complète des ventes et des
                                    achats/charges ajoutés sur la période, toutes devises), <strong>ainsi que les PDF des factures et les
                                    justificatifs des achats</strong> réellement comptés dans le calcul — pour la date que vous choisissez.
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label>Du</label>
                                    <input type="text" class="form-control datetimepicker" id="export-tva-from" value="<?= $periodeExportDefaut['debut']->format('d/m/Y') ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label>Au</label>
                                    <input type="text" class="form-control datetimepicker" id="export-tva-to" value="<?= $periodeExportDefaut['fin']->format('d/m/Y') ?>">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-0">
                                <button type="button" id="export-tva-comptable" class="btn btn-success"><i class="fas fa-file-archive mr-1"></i> Télécharger le dossier comptable (ZIP)</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tva-section-separator">
                    <i class="fa fa-file-signature mr-2"></i>Déclaration officielle (suivi comptable)
                </div>
            </div>
        </div>

        <?php
        // Cumul par année : montant net déclaré, majorations et statut de dépôt au fisc,
        // calculés une fois ici pour être réutilisés à la fois par la ligne de synthèse et
        // par le détail mensuel dépliable de chaque année (évite de reparcourir tva::findByDate()
        // deux fois pour la même donnée).
        $anneesTva = array();
        for ($i = $end_year; $i >= $start_year; $i--) {
            $tvaAnnee = tva::findByYear($_SESSION['agence'], $i);
            if (empty($tvaAnnee) && $i != date('Y')) {
                continue;
            }
            $moisDetail = array();
            $cumulMontant = 0;
            $cumulMajoration = 0;
            $montantDepose = 0;
            $montantNonDepose = 0;
            foreach (months() as $month) {
                $tvaMois = tva::findByDate($_SESSION['agence'], $i . '-' . $month['number']);
                $mMontant = 0;
                $mMajoration = 0;
                $mDeposeComplet = !empty($tvaMois);
                foreach ($tvaMois as $t) {
                    $mMontant += (float) $t->getAmount();
                    $mMajoration += (float) $t->getIncreasion();
                    $cumulMontant += (float) $t->getAmount();
                    $cumulMajoration += (float) $t->getIncreasion();
                    $ligneTotal = (float) $t->getAmount() + (float) $t->getIncreasion();
                    if ($t->getStatus() == 1) {
                        $montantDepose += $ligneTotal;
                    } else {
                        $montantNonDepose += $ligneTotal;
                        $mDeposeComplet = false;
                    }
                }
                $moisDetail[] = array('nom' => $month['name'], 'montant' => $mMontant, 'majoration' => $mMajoration, 'has' => !empty($tvaMois), 'depose' => $mDeposeComplet);
            }
            $totalDu = $cumulMontant + $cumulMajoration;
            $anneesTva[] = array(
                'annee' => $i,
                'nb' => count($tvaAnnee),
                'cumul_montant' => $cumulMontant,
                'cumul_majoration' => $cumulMajoration,
                'total_du' => $totalDu,
                'depose' => $montantDepose,
                'non_depose' => $montantNonDepose,
                'pct_depose' => $totalDu > 0 ? round($montantDepose / $totalDu * 100) : 0,
                'mois' => $moisDetail,
            );
        }
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Cumul TVA par année</h4>
                        <p class="text-muted mb-0" style="font-size:0.85rem;">Montant net déclaré, majorations de retard cumulées et
                            statut de dépôt au fisc — cliquez sur une année pour voir le détail mois par mois.</p>
                    </div>
                    <div class="card-body">
                        <?php if (empty($anneesTva)) : ?>
                            <p class="text-muted mb-0">Aucune déclaration de TVA enregistrée pour l'instant.</p>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Année</th>
                                            <th class="text-center">Déclarations</th>
                                            <th class="text-right">TVA nette cumulée</th>
                                            <th class="text-right">Majorations cumulées</th>
                                            <th class="text-right">Total dû</th>
                                            <th style="min-width:200px;">Déposé au fisc</th>
                                            <th class="text-right">Restant à déposer</th>
                                            <th class="text-center">Détail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($anneesTva as $an) : ?>
                                            <tr>
                                                <td><strong style="font-size:1.05rem;"><?= $an['annee'] ?></strong></td>
                                                <td class="text-center"><span class="badge badge-light"><?= $an['nb'] ?></span></td>
                                                <td class="text-right"><?= number_format($an['cumul_montant'], 2, ',', ' ') ?> DH</td>
                                                <td class="text-right">
                                                    <?= $an['cumul_majoration'] > 0 ? '<span class="text-warning">' . number_format($an['cumul_majoration'], 2, ',', ' ') . ' DH</span>' : '<span class="text-muted">—</span>' ?>
                                                </td>
                                                <td class="text-right"><strong><?= number_format($an['total_du'], 2, ',', ' ') ?> DH</strong></td>
                                                <td>
                                                    <div class="progress" style="height:16px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width:<?= $an['pct_depose'] ?>%;" title="<?= $an['pct_depose'] ?>% déposé"><?= $an['pct_depose'] ?>%</div>
                                                    </div>
                                                    <small class="text-muted"><?= number_format($an['depose'], 2, ',', ' ') ?> DH déposés</small>
                                                </td>
                                                <td class="text-right">
                                                    <?= $an['non_depose'] > 0 ? '<span class="text-danger font-weight-bold">' . number_format($an['non_depose'], 2, ',', ' ') . ' DH</span>' : '<span class="text-success">0 DH</span>' ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="javascript:void(0)" data-toggle="collapse" data-target="#tva-annee-<?= $an['annee'] ?>" class="btn btn-sm btn-white text-primary tva-year-toggle" aria-expanded="<?= $an['annee'] == date('Y') ? 'true' : 'false' ?>">
                                                        <i class="fa fa-chevron-down"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="8" class="p-0 border-0">
                                                    <div class="collapse<?= $an['annee'] == date('Y') ? ' show' : '' ?>" id="tva-annee-<?= $an['annee'] ?>">
                                                        <div class="p-3" style="background:#f8f7fc;">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm text-center mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <?php foreach ($an['mois'] as $m) : ?>
                                                                                <th class="text-secondary" style="font-size:0.75rem;"><?= mb_substr($m['nom'], 0, 3) ?></th>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <?php foreach ($an['mois'] as $m) : ?>
                                                                                <td>
                                                                                    <?php if (!$m['has']) : ?>
                                                                                        <span class="text-muted">—</span>
                                                                                    <?php else : ?>
                                                                                        <strong><?= number_format($m['montant'] + $m['majoration'], 0, ',', ' ') ?> DH</strong>
                                                                                        <?php if ($m['majoration'] > 0) : ?>
                                                                                            <br><small class="text-warning">dont <?= number_format($m['majoration'], 0, ',', ' ') ?> maj.</small>
                                                                                        <?php endif; ?>
                                                                                        <br><span class="badge <?= $m['depose'] ? 'badge-success' : 'badge-danger' ?>" style="font-size:10px;"><?= $m['depose'] ? 'Déposée' : 'Non déposée' ?></span>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                <div class="card-header">
						<h4 class="card-title">Liste des tva</h4>
					</div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Montant</th>
                                        <th>Majoration</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Charges</th>
                                        <th class="text-center">Relevé bancaire</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tvaes as $tvaRow): ?>
                                        <tr>
                                            <td><?php echo $tvaRow->getId(); ?></td>
                                            <td><b><?php echo floatval($tvaRow->getAmount()); ?> MAD</b></td>
                                            <td><b><?php echo floatval($tvaRow->getIncreasion()); ?> MAD</b></td>
                                            <td><?php echo date("m/Y",strtotime($tvaRow->getDate())); ?></td>
                                            <td>
                                                <?php if ($tvaRow->getStatus() == 1) : ?>
                                                    <span class="badge badge-success">Poussé</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">No poussé</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($tvaRow->getIdCharge() != '' && $tvaRow->getIdCharge() !== null) : ?>
                                                    <a href="index.php?option=com_charge&task=edit&id=<?= $tvaRow->getIdCharge() ?>" target="_blank" class="badge badge-success" data-toggle="tooltip" data-placement="top" data-original-title="Voir la charge liée">Poussé <i class="fa fa-external-link-alt ml-1" style="font-size:10px;"></i></a>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary">Pas encore</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php $nbRelevesTvaRow = isset($releveLotParTva[$tvaRow->getId()]) ? $releveLotParTva[$tvaRow->getId()] : 0; ?>
                                                <?php if ($nbRelevesTvaRow > 0) : ?>
                                                    <a href="index.php?option=com_rapprochement" class="badge badge-success" data-toggle="tooltip" data-placement="top" data-original-title="<?= $nbRelevesTvaRow ?> relevé(s) bancaire(s) couvrant la période de cette TVA — voir dans BANK STATEMENT"><i class="fa fa-university mr-1"></i><?= $nbRelevesTvaRow ?></a>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary" data-toggle="tooltip" data-placement="top" data-original-title="Aucun relevé bancaire lié à cette TVA"><i class="fa fa-university mr-1"></i>Aucun</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($tvaRow->getDoc() != '') : ?>
                                                    <a href="images/tva/<?php echo $tvaRow->getDoc(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox=""><i class="fa fa-file"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) : ?>
                                                    <a href="index.php?option=com_accounting&task=tva&id_tva=<?= $tvaRow->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) : ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $tvaRow->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

        <div class="row">
            <div class="col-md-12">
                <div class="card" id="tva-add-form-card">
                    <div class="card-header">
						<h4 class="card-title"><?=isset($tva) ? 'Modifier la tva' : 'Ajouter une tva'?></h4>
					</div>
                    <div class="card-body">
                        <div id="tva-missing-reminder" class="alert alert-warning d-none" style="font-size:0.9rem;">
                            <i class="fa fa-arrow-down mr-1"></i>
                            Vous ajoutez la TVA de <strong id="tva-missing-reminder-mois"></strong> — saisissez le montant puis cliquez sur
                            <strong>"Ajouter"</strong> en bas du formulaire pour l'enregistrer réellement.
                        </div>
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Widget flottant : échéance TVA (collé à droite, s'ouvre au clic sur l'icône calendrier ;
     une alerte rouge avec icône alarme apparaît automatiquement si l'échéance est proche ou
     dépassée — même comportement que le widget "jours fériés" du tableau de bord). -->
<div id="tva-echeance-widget" class="tva-echeance-widget <?= $echeanceUrgente ? 'urgent' : '' ?>">
    <button type="button" class="tva-echeance-widget-tab" id="tva-echeance-widget-tab" title="Échéance TVA">
        <i class="fas fa-calendar-alt"></i>
        <?php if ($echeanceUrgente) :?><span class="tva-echeance-widget-dot"></span><?php endif;?>
    </button>
    <div class="tva-echeance-widget-panel" id="tva-echeance-widget-panel">
        <div class="tva-echeance-widget-header">
            <i class="fa fa-calendar-times mr-2"></i>Échéance TVA
        </div>
        <div class="tva-echeance-widget-date">
            <?php if ($echeanceUrgente) :?><i class="fa fa-bell tva-echeance-alarm-icon"></i><?php endif;?>
            <?= $periodeEcheance['limite']->format('d/m/Y') ?>
        </div>
        <div class="tva-echeance-widget-sub">Déclaration <?= $periodiciteTva == 'trimestriel' ? 'trimestrielle' : 'mensuelle' ?></div>
        <div class="tva-echeance-widget-statut <?= $echeanceUrgente ? 'urgent' : ($periodeDeclaree ? 'ok' : 'warning') ?>">
            <?= $echeanceTexte ?>
        </div>
    </div>
</div>
<?php if ($echeanceUrgente) :?>
<div class="tva-echeance-widget-toast" id="tva-echeance-widget-toast">
    <button type="button" class="tva-echeance-widget-toast-close" id="tva-echeance-widget-toast-close">&times;</button>
    <i class="fa fa-bell tva-echeance-widget-toast-icon"></i>
    <div><strong>Échéance TVA imminente !</strong><br><?= $echeanceTexte ?></div>
</div>
<?php endif;?>
<script>
	(function () {
		var tvaEcheanceUrgente = <?= $echeanceUrgente ? 'true' : 'false' ?>;

		function openWidget() {
			$('#tva-echeance-widget').addClass('open');
		}
		function closeWidget() {
			$('#tva-echeance-widget').removeClass('open');
		}
		function showToast() {
			$('#tva-echeance-widget-toast').addClass('show');
		}
		function hideToast() {
			$('#tva-echeance-widget-toast').removeClass('show');
		}

		$(document).on('click', '#tva-echeance-widget-tab', function () {
			if ($('#tva-echeance-widget').hasClass('open')) {
				closeWidget();
			} else {
				openWidget();
			}
		});
		$(document).on('click', function (e) {
			if (!$(e.target).closest('#tva-echeance-widget').length) {
				closeWidget();
			}
		});
		$(document).on('click', '#tva-echeance-widget-toast-close', function () {
			hideToast();
		});

		// Le message reste affiché tant que l'utilisateur ne le ferme pas lui-même.
		if (tvaEcheanceUrgente) {
			setTimeout(showToast, 900);
		}
	})();
</script>

<!-- Popup "Ajouter la TVA d'un mois manquant" -->
<div id="tva-missing-modal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="tva-confirm-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                <h5 class="modal-title mt-3">Déclaration TVA manquante</h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Voulez-vous ajouter la TVA de <strong id="tva-missing-modal-mois">—</strong> ?</p>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    Vous allez être amené(e) au formulaire ci-dessous avec ce mois déjà sélectionné —
                    <strong>il faudra ensuite saisir le montant et cliquer sur "Ajouter" pour l'enregistrer réellement.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                <button type="button" id="tva-missing-modal-confirm" class="btn btn-primary"><i class="fa fa-arrow-down mr-1"></i> Remplir le formulaire</button>
            </div>
        </div>
    </div>
</div>

<script>
	(function () {
		var tvaDue = <?= json_encode((float) $simulation['tva_due']) ?>;
		var tauxAgence = <?= json_encode($tauxAgence) ?>;
		var objectifInput = document.getElementById('tva-objectif');
		if (!objectifInput) return;

		function recalculerBesoin() {
			var objectif = parseFloat(objectifInput.value);
			var elHT = document.getElementById('tva-besoin-ht');
			var elTTC = document.getElementById('tva-besoin-ttc');
			if (isNaN(objectif) || tauxAgence <= 0) {
				elHT.textContent = '—';
				elTTC.textContent = '—';
				return;
			}
			var besoinHT = tvaDue > objectif ? (tvaDue - objectif) / (tauxAgence / 100) : 0;
			var besoinTTC = besoinHT * (1 + tauxAgence / 100);
			var fmt = function (n) { return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH'; };
			elHT.textContent = fmt(besoinHT);
			elTTC.textContent = fmt(besoinTTC);
		}

		objectifInput.addEventListener('input', recalculerBesoin);
		recalculerBesoin();
	})();

	document.getElementById('export-tva-comptable').addEventListener('click', function () {
		var from = document.getElementById('export-tva-from').value;
		var to = document.getElementById('export-tva-to').value;
		window.open('components/com_accounting/controleurs/router.php?task=exportTvaComptable&from=' + encodeURIComponent(from) + '&to=' + encodeURIComponent(to), '_blank');
	});
</script>

<script type="text/javascript">
    $(function() {

        // Ajout depuis l'alerte "mois manquants" : clic sur un mois -> popup de confirmation
        // designée -> à la confirmation, ouvre le vrai formulaire "Ajouter une tva" avec le
        // mois/année déjà sélectionnés, pour que le comptable saisisse le montant réel (pas de
        // ligne à 0 créée automatiquement). $tvaCibleManquante garde la trace du badge visé pour
        // pouvoir le faire passer au vert quand ce même mois est effectivement soumis.
        var $tvaCibleManquante = null;

        $(document).on("click", ".tva-missing-month", function() {
            var $badge = $(this);
            $tvaCibleManquante = {
                annee: $badge.attr("data-annee"),
                mois: $badge.attr("data-mois"),
                moisNom: $badge.attr("data-mois-nom"),
                badge: $badge
            };

            $('#tva-missing-modal-mois').text($tvaCibleManquante.moisNom + ' ' + $tvaCibleManquante.annee);
            $('#tva-missing-modal').modal('show');
        });

        $(document).on("click", "#tva-missing-modal-confirm", function() {
            if (!$tvaCibleManquante) return;
            $('#tva-missing-modal').modal('hide');

            var moisPadded = ("0" + $tvaCibleManquante.mois).slice(-2);
            $('#month-input').val($tvaCibleManquante.annee + '-' + moisPadded);

            $('#tva-missing-reminder-mois').text($tvaCibleManquante.moisNom + ' ' + $tvaCibleManquante.annee);
            $('#tva-missing-reminder').removeClass('d-none');

            setTimeout(function() {
                var $formCard = $('#tva-add-form-card');
                $('html, body').animate({ scrollTop: $formCard.offset().top - 80 }, 'slow');
                $formCard.addClass('tva-form-highlight');
                setTimeout(function() { $formCard.removeClass('tva-form-highlight'); }, 1800);
            }, 350);
        });

        $(document).on("click", ".deleteDoc", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteDocTva", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        setTimeout(function() {
                            $btn.parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Document supprimé avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        var msgsucces = "Tva supprimé avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteTva", order, function(theResponse) {
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
        $('form#tvaForm').ajaxForm({
            beforeSubmit: function() {
                $("#tvaForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#tvaForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Tva ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Tva modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#tvaForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    // Si cet ajout correspond au mois visé depuis l'alerte "mois manquants" (popup
                    // -> formulaire pré-rempli), on fait passer son badge au vert avant le rechargement
                    // de page (qui, lui, le retirera pour de bon puisqu'il ne sera plus manquant).
                    var submittedDate = $('#tvaForm input[name=date]').val();
                    if ($tvaCibleManquante) {
                        var moisPadded = ("0" + $tvaCibleManquante.mois).slice(-2);
                        if (submittedDate === ($tvaCibleManquante.annee + '-' + moisPadded)) {
                            $tvaCibleManquante.badge.removeClass("bg-danger-light").addClass("bg-success-light");
                            $tvaCibleManquante.badge.html('<i class="fa fa-check mr-1"></i>' + $tvaCibleManquante.moisNom);
                        }
                        $tvaCibleManquante = null;
                    }

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#tvaForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#tvaForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>