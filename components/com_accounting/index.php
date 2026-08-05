<?php

@$task = $_GET['task'];
@$secure = $_SESSION['secure'];

if($_SESSION['user']->getProfil()->getProfil() != "Commercial" && !$_SESSION['user']->isResourceHumaine() ){
    if(!isset($secure) || $secure != $secureCode){
        include_once("components/com_dashboard/views/dashboard/password.php");
        return;
    }
}

switch ($task)
{
    case 'cnss':
        if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
            $action1 = "components/com_accounting/controleurs/router.php?task=addCnss";
            $action2 = "components/com_accounting/controleurs/router.php?task=editCnss";
            $action3 = "components/com_accounting/controleurs/router.php?task=deleteCnss";
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $cnsses = cnss::findAll($agence->getId());
            $cnss_date_asc = cnss::findOneOrderByDate($_SESSION['agence'],'asc');
            $start_year = $cnss_date_asc ? date('Y',strtotime($cnss_date_asc->getDate().'-1')) : '2024';
            $cnss_date_desc = cnss::findOneOrderByDate($_SESSION['agence'],'desc');
            $end_year = $cnss_date_desc ? date('Y',strtotime($cnss_date_desc->getDate().'-1')) : '2024';
            if(isset($_GET['id_cnss']) && !empty($_GET['id_cnss'])){
                $id_cnss = intval($_GET['id_cnss']);
                $cnss = cnss::find($id_cnss,$_SESSION['agence']);
            }

            // KPIs de l'en-tête : montant en attente de dépôt, effectifs déclarés (employés actifs
            // de l'agence), mois de l'année en cours sans aucune cotisation déposée.
            $anneeCourante = date('Y');
            $cnssAnneeCourante = cnss::findByYear($_SESSION['agence'], $anneeCourante);
            $kpiMontantAPayer = 0;
            $kpiMontantDepose = 0;
            foreach ($cnssAnneeCourante as $c) {
                if ($c->getStatus() != 1) {
                    $kpiMontantAPayer += $c->getAmount();
                } else {
                    $kpiMontantDepose += $c->getAmount();
                }
            }

            // Alerte "mois non déposés" : depuis la date de création de l'agence (et non une
            // année fixe arbitraire) jusqu'au mois en cours inclus - même principe que le KPI
            // "mois sans déclaration TVA" de la page TVA. $kpiMoisManquants alimente à la fois la
            // carte KPI ci-dessus et la bannière d'alerte cliquable ci-dessous, pour ne jamais
            // afficher deux totaux différents sur la même page.
            $moisNomsFrCnss = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
            $anneeCreationAgence = $agence->getDateAdd() ? (int) date('Y', strtotime($agence->getDateAdd())) : 2022;
            $moisCourantCnss = (int) date('n');
            $manquantsParAnneeCnss = array();
            for ($y = $anneeCreationAgence; $y <= (int) $anneeCourante; $y++) {
                $moisDebut = ($y == $anneeCreationAgence) ? (int) date('n', strtotime($agence->getDateAdd() ?: ($y . '-01-01'))) : 1;
                $moisFin = ($y == (int) $anneeCourante) ? $moisCourantCnss : 12;
                for ($m = $moisDebut; $m <= $moisFin; $m++) {
                    $ligneMoisCnss = cnss::findByDate($_SESSION['agence'], $y . '-' . sprintf('%02d', $m));
                    if (empty($ligneMoisCnss)) {
                        if (!isset($manquantsParAnneeCnss[$y])) {
                            $manquantsParAnneeCnss[$y] = array();
                        }
                        $manquantsParAnneeCnss[$y][] = array('num' => $m, 'nom' => $moisNomsFrCnss[$m]);
                    }
                }
            }
            $kpiMoisManquants = array_sum(array_map('count', $manquantsParAnneeCnss));

            $kpiEffectifsDeclares = 0;
            foreach (resourcehumaine::findAll() as $r) {
                if ($r->isActive() && $r->getAgency() && $r->getAgency()->getId() == $agence->getId()) {
                    $kpiEffectifsDeclares++;
                }
            }

            include_once("components/com_accounting/views/cnss/list.php");
        }
        break;
    case 'tva':
        if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
            $action1 = "components/com_accounting/controleurs/router.php?task=addTva";
            $action2 = "components/com_accounting/controleurs/router.php?task=editTva";
            $action3 = "components/com_accounting/controleurs/router.php?task=deleteTva";
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $tvaes = tva::findAll($agence->getId());
            // Icône "relevé(s) bancaire(s) lié(s)" affichée sur chaque ligne de la liste - simple
            // traçabilité par recoupement de dates (quel relevé importé couvre le mois de cette
            // déclaration), pas une lecture du contenu des lignes - voir releveLot::compterLotsParTva().
            $releveLotParTva = releveLot::compterLotsParTva($agence->getId());
            $tva_date_asc = tva::findOneOrderByDate($_SESSION['agence'],'asc');
            $start_year = $tva_date_asc ? date('Y',strtotime($tva_date_asc->getDate().'-1')) : '2024';
            $tva_date_desc = tva::findOneOrderByDate($_SESSION['agence'],'desc');
            $end_year = $tva_date_desc ? date('Y',strtotime($tva_date_desc->getDate().'-1')) : '2024';
            if(isset($_GET['id_tva']) && !empty($_GET['id_tva'])){
                $id_tva = intval($_GET['id_tva']);
                $tva = tva::find($id_tva,$_SESSION['agence']);
                // Relevés bancaires (BANK STATEMENT) dont la période couvre le mois de cette
                // déclaration (crm_tva.date = dernier jour du mois, voir tva::buildTva()) - voir
                // releveLot::findAllByTva().
                $dateFinPeriodeTva = new DateTime($tva->getDate());
                $dateDebutPeriodeTva = (clone $dateFinPeriodeTva)->modify('first day of this month');
                $relevesLiesTva = releveLot::findAllByTva($tva->getAgence()->getId(), $dateDebutPeriodeTva->format('Y-m-d'), $dateFinPeriodeTva->format('Y-m-d'));
            }

            // Estimation temps réel (indicative) : voir la section "Estimation CRM" de la
            // page. Calculée séparément de la déclaration officielle ci-dessous (jamais
            // confondues), avec une comparaison automatique quand une déclaration existe déjà
            // pour le même mois.
            $simAnnee = isset($_GET['annee']) && !empty($_GET['annee']) ? intval($_GET['annee']) : intval(date('Y'));
            $simMois = isset($_GET['mois']) && !empty($_GET['mois']) ? intval($_GET['mois']) : intval(date('n'));
            $simCredit = isset($_GET['credit']) && $_GET['credit'] !== '' ? floatval($_GET['credit']) : tvaSimulateur::creditReporteExistant($simAnnee, $simMois, $_SESSION['agence']);
            $simulation = tvaSimulateur::simuler($simAnnee, $simMois, $_SESSION['agence'], $simCredit);
            $simHistorique = tvaSimulateur::historique($_SESSION['agence']);

            $declarationOfficielle = 0;
            $lignesOfficielles = tva::findByDate($_SESSION['agence'], $simAnnee . '-' . sprintf('%02d', $simMois));
            foreach ($lignesOfficielles as $ligneOfficielle) {
                $declarationOfficielle += (float) $ligneOfficielle->getAmount();
            }
            $declarationExiste = !empty($lignesOfficielles);

            include_once("components/com_accounting/views/tva/list.php");
        }
        break;
    case 'bilan':
        if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
            $action1 = "components/com_accounting/controleurs/router.php?task=addBilan";
            $action2 = "components/com_accounting/controleurs/router.php?task=editBilan";
            $action3 = "components/com_accounting/controleurs/router.php?task=deleteBilan";
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $bilans = bilan::findAll($agence->getId());
            if(isset($_GET['id_bilan']) && !empty($_GET['id_bilan'])){
                $id_bilan = intval($_GET['id_bilan']);
                $bilan = bilan::find($id_bilan,$_SESSION['agence']);
            }

            // KPIs de l'en-tête : montant en attente de dépôt, montant déjà déposé, dernière
            // année pour laquelle un bilan a été déposé.
            $kpiMontantDu = 0;
            $kpiMontantDepose = 0;
            $kpiDerniereAnnee = null;
            foreach ($bilans as $b) {
                if ($b->getStatus() == 1) {
                    $kpiMontantDepose += $b->getAmount();
                    if ($kpiDerniereAnnee === null || $b->getYear() > $kpiDerniereAnnee) {
                        $kpiDerniereAnnee = $b->getYear();
                    }
                } else {
                    $kpiMontantDu += $b->getAmount();
                }
            }

            // Alerte "années non déposées" depuis la création de l'agence - même principe que le
            // KPI "mois manquants" de la page TVA/CNSS, à la maille annuelle (bilan annuel).
            $anneesManquantesInfo = comAccountingAnneesManquantes('bilan', $agence);
            $anneesManquantes = $anneesManquantesInfo['annees'];
            $anneeCreationAgence = $anneesManquantesInfo['anneeCreation'];

            include_once("components/com_accounting/views/bilan/list.php");
        }
        break;
    case 'taxeprofessionnelle':
        if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
            $action1 = "components/com_accounting/controleurs/router.php?task=addTaxeprofessionnelle";
            $action2 = "components/com_accounting/controleurs/router.php?task=editTaxeprofessionnelle";
            $action3 = "components/com_accounting/controleurs/router.php?task=deleteTaxeprofessionnelle";
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $taxesprofessionnelles = taxeprofessionnelle::findAll($agence->getId());
            if(isset($_GET['id_taxeprofessionnelle']) && !empty($_GET['id_taxeprofessionnelle'])){
                $id_taxeprofessionnelle = intval($_GET['id_taxeprofessionnelle']);
                $taxeprofessionnelle = taxeprofessionnelle::find($id_taxeprofessionnelle,$_SESSION['agence']);
            }

            // KPIs de l'en-tête + alerte "années non déposées", même principe que Bilan/Impôts.
            $kpiMontantDu = 0;
            $kpiMontantDepose = 0;
            $kpiDerniereAnnee = null;
            foreach ($taxesprofessionnelles as $t) {
                if ($t->getStatus() == 1) {
                    $kpiMontantDepose += $t->getAmount();
                    if ($kpiDerniereAnnee === null || $t->getYear() > $kpiDerniereAnnee) {
                        $kpiDerniereAnnee = $t->getYear();
                    }
                } else {
                    $kpiMontantDu += $t->getAmount();
                }
            }
            $anneesManquantesInfo = comAccountingAnneesManquantes('taxeprofessionnelle', $agence);
            $anneesManquantes = $anneesManquantesInfo['annees'];
            $anneeCreationAgence = $anneesManquantesInfo['anneeCreation'];

            include_once("components/com_accounting/views/taxeprofessionnelle/list.php");
        }
        break;
    case 'impot':
        if ($_SESSION['user']->hasDroit('add', 'com_accounting')) {
            $action1 = "components/com_accounting/controleurs/router.php?task=addImpot";
            $action2 = "components/com_accounting/controleurs/router.php?task=editImpot";
            $action3 = "components/com_accounting/controleurs/router.php?task=deleteImpot";
            $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
            $impots = impot::findAll($agence->getId());
            if(isset($_GET['id_impot']) && !empty($_GET['id_impot'])){
                $id_impot = intval($_GET['id_impot']);
                $impot = impot::find($id_impot,$_SESSION['agence']);
            }

            // KPIs de l'en-tête (IS + IR confondus) : montant dû sur l'année en cours, montant
            // déjà déposé, et nombre de déclarations passées restées impayées (toutes années).
            $anneeCourante = date('Y');
            $kpiMontantDu = 0;
            $kpiMontantDepose = 0;
            $kpiDeclarationsEnRetard = 0;
            foreach ($impots as $i) {
                if ($i->getStatus() == 1) {
                    if ($i->getYear() == $anneeCourante) {
                        $kpiMontantDepose += $i->getAmount();
                    }
                } else {
                    $kpiDeclarationsEnRetard++;
                    if ($i->getYear() == $anneeCourante) {
                        $kpiMontantDu += $i->getAmount();
                    }
                }
            }

            // Alerte "années non déposées", depuis la création de l'agence - un an compte IS et
            // IR séparément (contrairement à Bilan/Taxe pro à déclaration unique) : une année où
            // seul l'IS a été déposé doit quand même signaler l'IR manquant.
            $anneeCreationAgence = $agence->getDateAdd() ? (int) date('Y', strtotime($agence->getDateAdd())) : 2022;
            $anneesManquantes = array();
            for ($y = $anneeCreationAgence; $y <= (int) $anneeCourante; $y++) {
                $typesTrouves = array();
                foreach (impot::findByYear($agence->getId(), (string) $y) as $i) {
                    $typesTrouves[$i->getType()] = true;
                }
                foreach (array('IS', 'IR') as $typeAttendu) {
                    if (!isset($typesTrouves[$typeAttendu])) {
                        $anneesManquantes[] = array('annee' => $y, 'type' => $typeAttendu);
                    }
                }
            }

            include_once("components/com_accounting/views/impot/list.php");
        }
        break;
    default :

        break;
}