<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_contract')) {
            // Seuls les devis déjà "Accepté" (ou plus loin dans le pipeline contrat) et sans
            // contrat existant sont proposés à l'étape 1 - évite de laisser l'utilisateur choisir
            // un devis qui sera de toute façon refusé par contratStatutsDevisAutorises() côté
            // serveur (cf. com_contract/controleurs/contract/controleur.php).
            $tousLesDevis = devis::findAll(false,false,false,$_SESSION['agence']);
            $devises = array_values(array_filter($tousLesDevis, function ($d) {
                $statutsAutorises = array(devis::STATU_ACCEPTE, devis::STATU_CONTRAT_EN_ATTENTE, devis::STATU_PAIEMENT_EFFECTUE, devis::STATU_CONTRAT_SIGNE);
                if (!in_array($d->getStatu(), $statutsAutorises) || $d->hasContrat() || $d->getProforma() == 1) {
                    return false;
                }
                $factureLiee = $d->getFacture();
                return !($factureLiee->getId() && $factureLiee->getProforma() == 1);
            }));
            include_once("components/com_contract/views/contract/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_contract')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $contract = contract::find($id, $_SESSION['agence'], $_SESSION['langue']);
                $devises = devis::findAll(false,false,false,$_SESSION['agence']);
                $action = "components/com_contract/controleurs/router.php?task=editContract";
                $submitName = "edit";
                $submitValue = "Modifier contract";
                include_once("components/com_contract/views/contract/edit.php");
            }
        }
        break;
    case 'editeur' :
        if ($_SESSION['user']->hasDroit('edit', 'com_contract')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $contract = contract::find($id, $_SESSION['agence'], $_SESSION['langue']);
                include_once("components/com_contract/views/contract/editeur.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_contract')) {
            $contracts = contract::findAll($_SESSION["agence"],$_SESSION["langue"]);
            include_once("components/com_contract/views/contract/list.php");
        }
        break;
}