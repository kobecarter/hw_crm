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
        $absence = new absence();
        $absence->setId($data['id']);
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