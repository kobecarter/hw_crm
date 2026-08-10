<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addAbsenceResourceHumaine':
            addAbsenceResourceHumaine($_POST);
            break;
        case 'editAbsenceResourceHumaine':
            editAbsenceResourceHumaine($_POST);
            break;
        case 'deleteAbsenceResourceHumaine':
            deleteAbsenceResourceHumaine($_POST);
            break;
    }
}

function addAbsenceResourceHumaine($data)
{
    $indices = array("id_resourcehumaine","start_date","end_date","back_date","number_of_days","nature_of_absence");
    if (fieldCheck($data, $indices)) {
        // Dossier employé incomplet (CIN, contrat, engagement de confidentialité...) : pas de
        // demande de congé tant que la situation n'est pas régularisée avec l'administration -
        // même contrôle que le bandeau "Profil non finalisé" déjà affiché ailleurs sur la fiche
        // (fileresourcehumaine::documentsManquants()). Contrôle refait ici côté serveur, pas
        // seulement dans la vue : le formulaire masqué côté client ne suffit pas à empêcher un
        // POST direct.
        $employe = resourcehumaine::find($data['id_resourcehumaine']);
        $manquants = fileresourcehumaine::documentsManquants($employe->getStatus(), fileresourcehumaine::findAllByResourcehumaine($employe->getId()));
        if (!empty($manquants)) {
            echo "3";
            return;
        }
        if (buildAbsenceResourceHumaine($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editAbsenceResourceHumaine($data)
{
    $indices = array("id_absence","id_resourcehumaine", "start_date","end_date","back_date","number_of_days","nature_of_absence");
    if (fieldCheck($data, $indices)) {
        if (buildAbsenceResourceHumaine($data, $data['id_absence'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteAbsenceResourceHumaine($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find() + vérification manuelle de l'agence via l'employé lié (absence::find($id) ne
        // prend pas d'agence en paramètre) - sans ce contrôle, n'importe quel id d'absence valide,
        // même d'une autre agence, se faisait supprimer (IDOR).
        $absence = absence::find($data['id']);
        $employeAbsence = $absence->getResourcehumaine();
        if ($absence->getId() == 0 || !$employeAbsence || !$employeAbsence->getAgency() || $employeAbsence->getAgency()->getId() != $_SESSION['agence']) {
            echo "2";
            return;
        }
        if ($absence->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildAbsenceResourceHumaine($data, $id = null)
{
    $absence = new absence();
	
	$justification = array();
    if(isset($_FILES['justification']) && $_FILES['justification']['name'][0]!=''){
        $justification = uploadFiles('justification','../../../images/resourceshumaines/absences',  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
    }

    if($id){
        $absence = absence::find($id);
    }
	
	if(isset($justification[0])) {
		$absence->setJustification($justification[0]);
	}
	$absence->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
    $absence->setNumberOfDays($data['number_of_days']);
    $absence->setNatureOfAbsence($data['nature_of_absence']);
    $absence->setStartDate(isset($data['start_date']) && !empty($data['start_date']) ? dateBD($data['start_date']) : null);
    $absence->setEndDate(isset($data['end_date']) && !empty($data['end_date']) ? dateBD($data['end_date']) : null);
    $absence->setBackDate(isset($data['back_date']) && !empty($data['back_date']) ? dateBD($data['back_date']) : null);
    $absence->setStatus($data['status']);
	$absence->setRemark($data['remark']);
    $absence->setDateAdd(date("Y-m-d"));
    $absence->setLastEdit(date("Y-m-d"));

    return $absence;
}