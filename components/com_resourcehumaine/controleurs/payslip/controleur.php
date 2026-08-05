<?php

use Mpdf\Tag\NewColumn;

if (isset($task) && !empty($task)) {
    switch ($task) {
        case "addPayslip" :
            addPayslip($_POST);
            break;
        case "editPayslip" :
            editPayslip($_POST);
            break;
        case "deletePayslip" :
            deletePayslip($_POST);
            break;
    }
}

function addPayslip($data)
{
    $indices = array("id_resourcehumaine", "file", "date");
    if (validatePayslip($data, $indices)) {

        $dossier = "../../../images/resourceshumaines/payslips";
        $files = [];
        if(isset($_FILES['file']) && $_FILES['file']['name'][0]!=''){
            $files = uploadFiles('file', $dossier,  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
        }

        $payslip = buildPayslip($data, $files[0]);
        if ($payslip->add() == 1) {
            $idPayslip = payslip::getLastId();
            list($annee, $mois) = explode('-', $data["date"]);
            $idCharge = creerChargeDepuisPayslipRH($payslip->getResourcehumaine(), intval($mois), intval($annee), $files[0]);
            if ($idCharge) {
                $payslipAvecCharge = payslip::find($idPayslip);
                $payslipAvecCharge->setIdCharge($idCharge);
                $payslipAvecCharge->edit();
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }

}

function editPayslip($data)
{
    $indices = array("id","id_resourcehumaine","date");
    if (validatePayslip($data, $indices)) {
        $dossier = "../../../images/resourceshumaines/payslips";
        $files = [];
        if(isset($_FILES['file']) && $_FILES['file']['name'][0]!=''){
            $files = uploadFiles('file', $dossier,  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
        }

        if(buildPayslip($data, $files , $data['id'])->edit() == 1)
            echo "1";
        else
            echo "2";
    } else {
        echo "0";
    }

}

function deletePayslip($data)
{
    $indices = array("id");
    if (validatePayslip($data, $indices)) {
        $id = $data["id"];
        $payslip = payslip::find($id, $_SESSION["langue"]);
        if ($payslip->delete() == 1) {
            if (file_exists("../../../../images/resourceshumaines/payslips/" . $payslip->getFile())) {
                @unlink("../../../../images/resourceshumaines/payslips/" . $payslip->getFile());
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function validatePayslip($data = array(), $indices = array())
{
    foreach ($indices as $indice) {
        if ($indice == "file") {
            if (!isset($_FILES["file"]) || $_FILES["file"]["name"][0] == "") {
                return false;
            }
        } else {
            if (!isset($data[$indice]) || empty($data[$indice])) {
                return false;
            }
        }
    }
    return true;
}

function buildPayslip($data, $files = [], $id = null)
{
    $payslip = new payslip();
    if($id){
        $payslip = payslip::find($id);
        if(isset($files) && sizeof($files)>0) {
            $payslip->setFile($files[0]);
        }
    }else{
        if(isset($files)) {
            $payslip->setFile($files);
        }
    }

    $resourcehumaine = resourcehumaine::find($data["id_resourcehumaine"]);
    $payslip->setResourcehumaine($resourcehumaine);
    // Le titre n'est plus saisi à la main : convention unique "Bulletin de paie {Nom} {MM/YYYY}",
    // voir payslip::titreAuto(). $data["date"] vient d'un <input type="month"> = "YYYY-MM".
    list($annee, $mois) = explode('-', $data["date"]);
    $payslip->setTitle(payslip::titreAuto($resourcehumaine, intval($mois), intval($annee)));
    $payslip->setDate($data["date"]."-1");

    return $payslip;
}

// Bulletin de paie ajouté directement depuis la fiche employé (pas via la dropzone IA de la page
// Charges, seul chemin qui créait une charge jusqu'ici) : crée systématiquement la charge
// correspondante, avec le fichier déjà déposé copié vers images/charges/ pour qu'elle ait son
// propre justificatif (le payslip garde le sien dans images/resourceshumaines/payslips/) —
// miroir de creerBulletinDepuisCharge() côté com_charge/controleurs/charge/controleur.php, dans
// l'autre sens. Retourne l'id de la charge créée, ou null si la charge n'a pas pu être créée
// (le bulletin de paie reste ajouté dans tous les cas : on ne bloque jamais l'ajout du document
// pour un problème côté charge).
function creerChargeDepuisPayslipRH($resourcehumaine, $mois, $annee, $nomFichierPayslip)
{
    $nomFichierDestination = null;
    if ($nomFichierPayslip) {
        $cheminSource = '../../../images/resourceshumaines/payslips/' . $nomFichierPayslip;
        if (file_exists($cheminSource)) {
            $dossierDestination = '../../../images/charges';
            $ext = substr($nomFichierPayslip, strrpos($nomFichierPayslip, '.') + 1);
            $nomBase = basename($nomFichierPayslip, '.' . $ext);
            $n = '';
            while (file_exists("$dossierDestination/$nomBase$n.$ext")) {
                $n++;
            }
            $nomFichierDestination = "$nomBase$n.$ext";
            if (!@copy($cheminSource, "$dossierDestination/$nomFichierDestination")) {
                $nomFichierDestination = null;
            }
        }
    }

    $moisNoms = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
    $nomMois = isset($moisNoms[$mois]) ? $moisNoms[$mois] : '';
    $dateFinMois = date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $annee, $mois)));

    $charge = new charge();
    $charge->setAgence($resourcehumaine->getAgency());
    $charge->setPaidBy($_SESSION['user']);
    $charge->setUser($_SESSION['user']);
    $charge->setType('fixe');
    $charge->setTitre('Salaire ' . trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName()) . ' — ' . $nomMois . ' ' . $annee);
    $charge->setDescription('Charge créée automatiquement depuis le bulletin de paie ajouté sur la fiche employé.');
    $charge->setTotal($resourcehumaine->getSalaireActuel() ? $resourcehumaine->getSalaireActuel() : 0);
    $charge->setDevise('DH');
    $charge->setTvaDeductible(0);
    $charge->setPaid(1);
    $charge->setFacture(0);
    $charge->setRefunded(0);
    $charge->setDatePayment($dateFinMois);
    $charge->setModePayment('virement');
    $charge->setDateCharge($dateFinMois);
    $charge->setDateAdd(date('Y-m-d'));
    $charge->setLastEdit(date('Y-m-d'));
    if ($nomFichierDestination) {
        $charge->setPhoto($nomFichierDestination);
    }

    if ($charge->add() != 1) {
        return null;
    }
    return charge::getLastId();
}
