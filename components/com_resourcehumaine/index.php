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
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
            $action = "components/com_resourcehumaine/controleurs/router.php?task=addResourceHumaine";
            $submitName = "add";
            $submitValue = "Ajouter resource humaine";
            $agencies = agence::findAll($_SESSION['langue']);
            $profils = profil::findAll();
            include_once("components/com_resourcehumaine/views/resourcehumaine/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id,$_SESSION['agence']);
                $action = "components/com_resourcehumaine/controleurs/router.php?task=editResourceHumaine";
                $submitName = "edit";
                $submitValue = "Modifier resource humaine";
                $agencies = agence::findAll($_SESSION['langue']);
                $profils = profil::findAll();
                include_once("components/com_resourcehumaine/views/resourcehumaine/edit.php");
            }
        }
        break;
    case 'duplicate' :
        if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id,$_SESSION['agence']);
                $action = "components/com_resourcehumaine/controleurs/router.php?task=duplicateResourceHumaine";
                $submitName = "duplicate";
                $submitValue = "Dupliquer resource humaine";
                $agencies = agence::findAll($_SESSION['langue']);
                $profils = profil::findAll();
                include_once("components/com_resourcehumaine/views/resourcehumaine/duplicate.php");
            }
        }
        break;
    case 'show':
        if ($_SESSION['user']->hasDroit('view', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id);
                $files = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
                $absences = absence::findAllByResourcehumaine($resourcehumaine->getId());
                include_once("components/com_resourcehumaine/views/resourcehumaine/detail.php");
            }
        }
        break;
    case 'file':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $action1 = "components/com_resourcehumaine/controleurs/router.php?task=addFileResourceHumaine";
                $action2 = "components/com_resourcehumaine/controleurs/router.php?task=editFileResourceHumaine";
                $action3 = "components/com_resourcehumaine/controleurs/router.php?task=deleteFileResourceHumaine";
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id);
                $files = fileresourcehumaine::findAllByResourcehumaine($resourcehumaine->getId());
                if(isset($_GET['id_file']) && !empty($_GET['id_file'])){
                    $id_file = intval($_GET['id_file']);
                    $file = fileresourcehumaine::find($id_file);
                }
                include_once("components/com_resourcehumaine/views/fileresourcehumaine/list.php");
            }
        }
        break;
    case 'absence':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $action1 = "components/com_resourcehumaine/controleurs/router.php?task=addAbsenceResourceHumaine";
                $action2 = "components/com_resourcehumaine/controleurs/router.php?task=editAbsenceResourceHumaine";
                $action3 = "components/com_resourcehumaine/controleurs/router.php?task=deleteAbsenceResourceHumaine";
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id);
                $absences = absence::findAllByResourcehumaine($resourcehumaine->getId());
                if(isset($_GET['id_absence']) && !empty($_GET['id_absence'])){
                    $id_absence = intval($_GET['id_absence']);
                    $absence = absence::find($id_absence);
                }
                include_once("components/com_resourcehumaine/views/absence/list.php");
            }
        }
        break;
    case 'bonus':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $action1 = "components/com_resourcehumaine/controleurs/router.php?task=addBonusResourceHumaine";
                $action2 = "components/com_resourcehumaine/controleurs/router.php?task=editBonusResourceHumaine";
                $action3 = "components/com_resourcehumaine/controleurs/router.php?task=deleteBonusResourceHumaine";
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id);
                $bonuses = bonus::findAllByResourcehumaine($resourcehumaine->getId());
                if(isset($_GET['id_bonus']) && !empty($_GET['id_bonus'])){
                    $id_bonus = intval($_GET['id_bonus']);
                    $bonus = bonus::find($id_bonus);
                }
                include_once("components/com_resourcehumaine/views/bonus/list.php");
            }
        }
        break;
    case 'pointage':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
                $action1 = "components/com_resourcehumaine/controleurs/router.php?task=importPointage";
                include_once("components/com_resourcehumaine/views/pointage/list.php");
        }
        break;
    case 'payslip':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $action1 = "components/com_resourcehumaine/controleurs/router.php?task=addPayslip";
                $action2 = "components/com_resourcehumaine/controleurs/router.php?task=editPayslip";
                $action3 = "components/com_resourcehumaine/controleurs/router.php?task=deletePayslip";
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id);
                $payslips = payslip::findAllByResourcehumaine($resourcehumaine->getId());
                if(isset($_GET['id_file']) && !empty($_GET['id_file'])){
                    $id_file = intval($_GET['id_file']);
                    $file = payslip::find($id_file);
                }
                include_once("components/com_resourcehumaine/views/payslip/list.php");
            }
        }
        break;
    case 'request':
        if ($_SESSION['user']->hasDroit('add', 'com_realisation')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $resourcehumaine = resourcehumaine::find($id);
                $requests = request::findAllByResourcehumaine($resourcehumaine->getId());
                include_once("components/com_resourcehumaine/views/request/list.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
            $resources_humaines_titulaire = resourcehumaine::findAllByStatus("Titulaire");
            $resources_humaines_stagaire = resourcehumaine::findAllByStatus("Stagaire");
            $resources_humaines_periode_de_test = resourcehumaine::findAllByStatus("Periode de test");
            include_once("components/com_resourcehumaine/views/resourcehumaine/list.php");
        }
        break;
}