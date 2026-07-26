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
            $tva_date_asc = tva::findOneOrderByDate($_SESSION['agence'],'asc');
            $start_year = $tva_date_asc ? date('Y',strtotime($tva_date_asc->getDate().'-1')) : '2024';
            $tva_date_desc = tva::findOneOrderByDate($_SESSION['agence'],'desc');
            $end_year = $tva_date_desc ? date('Y',strtotime($tva_date_desc->getDate().'-1')) : '2024';
            if(isset($_GET['id_tva']) && !empty($_GET['id_tva'])){
                $id_tva = intval($_GET['id_tva']);
                $tva = tva::find($id_tva,$_SESSION['agence']);
            }
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
            include_once("components/com_accounting/views/taxeprofessionnelle/list.php");
        }
        break;
    default :
        
        break;
}