<?php
include_once(__DIR__ . '../../../vendor/autoload.php'); // Include Composer's autoloader

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addResourceHumaine':
            addResourceHumaine($_POST);
            break;
        case 'duplicateResourceHumaine':
            duplicateResourceHumaine($_POST);
            break;
        case 'editResourceHumaine':
            editResourceHumaine($_POST);
            break;
        case 'deleteResourceHumaine':
            deleteResourceHumaine($_POST);
            break;
        case 'exportAbsenceFile':
            exportAbsenceFile($_POST);
            break;
        case 'getRowWorkDate':
            getRowWorkDate();
            break;
        case 'removeRowWorkDate':
            removeRowWorkDate($_POST);
            break;
    }
}

function addResourceHumaine($data)
{
    $indices = array("id_profil","id_agency","cin","firstname","lastname","email","address","phone","second_phone","city","function","status","start_date");
    if (fieldCheck($data, $indices)) {
        if (buildResourceHumaine($data)->add() == 1) {
            $id_resourcehumaine = resourcehumaine::getLastId();
            if(isset($data["workdate_start_date"])){
                foreach ($data["workdate_start_date"] as $key => $value) {
                    $workdate = new workdate();
                    $workdate->setResourcehumaine(resourcehumaine::find($id_resourcehumaine));
                    $workdate->setStartDate($data['workdate_start_date'][$key]);
                    $workdate->setContractSigningDate($data['workdate_contract_signing_date'][$key]);
                    $workdate->setEndDate($data['workdate_end_date'][$key]);
                    $workdate->add();
                }
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function duplicateResourceHumaine($data)
{
    $indices = array("id","id_profil","id_agency","cin","firstname","lastname","email","address","phone","second_phone","city","function","status","start_date");
    if (fieldCheck($data, $indices)) {
        if (buildResourceHumaine($data, $data['id'])->add() == 1) {
            $id_resourcehumaine = resourcehumaine::getLastId();
            if(isset($data["workdate_start_date"])){
                foreach ($data["workdate_start_date"] as $key => $value) {
                    $workdate = new workdate();
                    $workdate->setResourcehumaine(resourcehumaine::find($id_resourcehumaine));
                    $workdate->setStartDate($data['workdate_start_date'][$key]);
                    $workdate->setContractSigningDate($data['workdate_contract_signing_date'][$key]);
                    $workdate->setEndDate($data['workdate_end_date'][$key]);
                    $workdate->add();
                }
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editResourceHumaine($data)
{
    $indices = array("id","id_profil","id_agency","cin","firstname","lastname","email","address","phone","second_phone","city","function","status","start_date");
    if (fieldCheck($data, $indices)) {
        if (buildResourceHumaine($data, $data['id'])->edit() == 1) {
            if(isset($data["workdate_start_date"])){
                foreach ($data["workdate_start_date"] as $key => $value) {
                    if(isset($data["workdate_id"][$key]) && !empty($data["workdate_id"][$key])){
                        $workdate = workdate::find($data["workdate_id"][$key]);
                        $workdate->setStartDate($data['workdate_start_date'][$key]);
                        $workdate->setContractSigningDate($data['workdate_contract_signing_date'][$key]);
                        $workdate->setEndDate($data['workdate_end_date'][$key]);
                        $workdate->edit();
                    }else{
                        $workdate = new workdate();
                        $workdate->setResourcehumaine(resourcehumaine::find($data['id']));
                        $workdate->setStartDate($data['workdate_start_date'][$key]);
                        $workdate->setContractSigningDate($data['workdate_contract_signing_date'][$key]);
                        $workdate->setEndDate($data['workdate_end_date'][$key]);
                        $workdate->add();
                    }
                }
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteResourceHumaine($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find() puis vérification manuelle de l'agence (resourcehumaine::find($id) ne prend pas
        // d'agence en paramètre, contrairement à client/fournisseur/tva/...) - sans ce contrôle,
        // n'importe quel id d'employé valide, même d'une autre agence, se faisait supprimer (IDOR).
        $resourcehumaine = resourcehumaine::find($data['id']);
        if ($resourcehumaine->getId() == 0 || !$resourcehumaine->getAgency() || $resourcehumaine->getAgency()->getId() != $_SESSION['agence']) {
            echo "2";
            return;
        }
        if ($resourcehumaine->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildResourceHumaine($data, $id = null)
{
    $resourcehumaine = new resourcehumaine();
	
	$photo = array();
    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
        $photo = uploadFiles('photo','../../../images/resourceshumaines',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
    }

    if($id){
        $resourcehumaine = resourcehumaine::find($id);
    }else{
        if($data['status'] == "Stagaire"){
            $resourcehumaine_stagaires = resourcehumaine::findAllByStatus($data['status']);
            $data['reference'] = sizeof($resourcehumaine_stagaires) > 0 ? sizeof($resourcehumaine_stagaires)+1 : 1;
        }
    }
	
	if(isset($photo[0])) {
		$resourcehumaine->setPhoto($photo[0]);
	}
    if(isset($data['password']) && !empty($data['password'])){
        $resourcehumaine->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
    }
	
    $resourcehumaine->setReference($data['reference']);
    $resourcehumaine->setReferencePointage($data['reference_pointage']);
	$resourcehumaine->setAgency(agence::find($data['id_agency'],$_SESSION['langue']));
    $resourcehumaine->setCin($data['cin']);
    $resourcehumaine->setCnssNumber($data['cnss_number']);
    $resourcehumaine->setFirstName($data['firstname']);
	$resourcehumaine->setLastName($data['lastname']);
    $resourcehumaine->setEmail($data['email']);
	$resourcehumaine->setPhone($data['phone']);
	$resourcehumaine->setSecondPhone($data['second_phone']);
	$resourcehumaine->setAddress($data['address']);
    $resourcehumaine->setCity($data['city']);
    $resourcehumaine->setProspectingSource($data['prospecting_source']);
    $resourcehumaine->setRib($data['rib']);
    $resourcehumaine->setSalaireInitial(isset($data['salaire_initial']) && $data['salaire_initial'] !== '' ? $data['salaire_initial'] : null);
    $resourcehumaine->setSalaireActuel(isset($data['salaire_actuel']) && $data['salaire_actuel'] !== '' ? $data['salaire_actuel'] : null);
	$resourcehumaine->setFunction($data['function']);
    $resourcehumaine->setProfil(isset($data['id_profil']) ? profil::find($data['id_profil']) : new profil());
    $resourcehumaine->setStatus($data['status']);
	$resourcehumaine->setStartDate(dateBD($data['start_date']));
	$resourcehumaine->setContractSigningDate(dateBD($data['contract_signing_date']));
    $resourcehumaine->setEndDate(dateBD($data['end_date']));
    $resourcehumaine->setRemark($data['remark']);
    $resourcehumaine->setActive(isset($data['active']) ? 1 : 0);
    $resourcehumaine->setDateAdd(date("Y-m-d"));
    $resourcehumaine->setLastEdit(date("Y-m-d"));
    return $resourcehumaine;
}


function exportAbsenceFile($data){
    global $db;
     // Ensure $db is accessible
    $resourcehumaines = resourcehumaine::findAll();
    $end_day_of_month = date('t', strtotime($data['month'].'-01'));
    $month = date('m', strtotime($data['month'].'-01'));
    $year = date('Y', strtotime($data['month'].'-01'));

    // Create new Spreadsheet object
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);

    // Define your headers
    $header = [''];
    for ($i=1; $i <= $end_day_of_month; $i++) { 
        $day = $i;
        if($day<=9){
            $day = "0".$day;
        }
        // die($day_letter." ".$month_letter." ".$year); 
        array_push($header,$day."/".$month."/".$year);
    }
    array_push($header,"Total d'absences");
    // Set header values
    foreach ($header as $index => $h) {
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
        $spreadsheet->getActiveSheet()->setCellValue($columnLetter . '1', $h); // Set headers in row 1
        $spreadsheet->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(30);
        // $spreadsheet->getActiveSheet()->getRowDimension($index)->setRowHeight(30);
        $spreadsheet->getActiveSheet()->getStyle($index)->getAlignment()
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle($index)->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center text horizontally;
        
    }
    //Define holidays
    $holidays_dates = [];
    $holidays = holiday::findAll();
    foreach ($holidays as $key => $value) {
        $dates = getDatesFromRange($value->getStartDate(), $value->getEndDate());
        $holidays_dates = array_merge($holidays_dates,$dates);
    }

    // die(json_encode($holidays_dates) );
    // Define body
    $rows = [];
    foreach ($resourcehumaines as $key => $value) {
        echo $value->getFirstname()." ".$value->getLastname()."<br>";
        $strtotimestartdate = strtotime($value->getStartDate());
        $strtotimenddate = $value->getEndDate() ? strtotime($value->getEndDate()) : null;
        $strtotimedatestartmonth = strtotime($year."-".$month.'-01');
        $strtotimedateendmonth = strtotime($year."-".$month." - ".$end_day_of_month);
        
        if(
            (
                $strtotimestartdate <= $strtotimedatestartmonth || 
                (
                    $strtotimestartdate > $strtotimedatestartmonth && 
                    $strtotimestartdate <= $strtotimedateendmonth  
                )
            ) && 
            (
                !$value->getEndDate() || 
                (
                    $strtotimenddate > $strtotimedatestartmonth
                )
            )
        ){
            $rowlines = [$value->getFirstname()." ".$value->getLastname()];
            // get absence by employee
            $absences = absence::findByDateMonthly($value->getId(),$year."-".$month);
            // Caculate total absences days for each employee
            $sum_absences_days = 0;
            foreach ($absences as $key => $absence) {
                $sum_absences_days += floatval($absence->getNumberOfDays());
            }
            for ($i=1; $i <= $end_day_of_month; $i++) {
                $day = $i;
                if($day<=9){
                    $day = "0".$day;
                }
                $date = $year."-".$month."-".$day;
                // check holidays
                $check_holiday = holiday::findByDate($date);
                $check_absence = absence::findByDate($value->getId(),$date);
                $val = "";
                if($check_absence && $check_absence->getId()){
                    if($check_absence->getNatureOfAbsence() == 1){
                        $val = "red_vacation";
                    }else if($check_absence->getNatureOfAbsence() == 2){
                        $val = "red_sick";
                    }else if($check_absence->getNatureOfAbsence() == 3){
                        $val = "red_no_justify";
                    }
                    if($val != "red_vacation"){
                        if($check_absence->getStatus() == 1){
                            $val.="_d";
                        }else{
                            $val.="_nd";
                        }
                    }
                    
                }
                // The weekends
                $dayOfWeek = date('N', strtotime($date));
                if (in_array($dayOfWeek,[7])) {
                    $val = "weekend";
                }
                if($check_holiday && $check_holiday->getId()){
                    $val = "green";
                }
                 // The dates the employee not work yet
                if(strtotime($value->getStartDate()) > strtotime($date)){
                    $val = "not_yet";
                }
                
                if($value->getEndDate() && strtotime($value->getEndDate()) < strtotime($date)){
                    $val = "not_yet";
                }
                array_push($rowlines,$val);
            }
            // Push sum absences days of each employer in the last column of row
            array_push($rowlines,strval($sum_absences_days));
            // Push row
            array_push($rows,$rowlines);
        }
    }

    // Fill data from database
    $row = 2; // Start from the second row for data
    foreach ($rows as $data) {
        foreach ($data as $index => $value) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            
            $spreadsheet->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(30);
            $nameValue = "";
            if($index == 0){
                $nameValue = $value;
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, $nameValue); // Set values in the appropriate row
            }else if(sizeof($data) - 1 == $index){
                $nameValue = $value;
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, $nameValue); // Set values in the appropriate row
            }
            
            // Color filled fields green
            if($value == "red_vacation"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, "Congé"); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('72c0fd'); // Light green color
                endif;
            }else if($value == "red_sick_d"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, "Justifié (Déductible)"); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('fff491'); // Light green color
                endif;
            }else if($value == "red_sick_nd"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, "Justifié (Non Déductible)"); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('fff491'); // Light green color
                endif;
            }else if($value == "red_no_justify_d"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, "Non justifié (Déductible)"); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('f39f99'); // Light green color
                endif;
            } else if($value == "red_no_justify_nd"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, "Non justifié (Non Déductible)"); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('f39f99'); // Light green color
                endif;
            }    

            if($value == "weekend"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, ""); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('dfdfdf'); // Light green color
                endif;
            }

            if($value == "green"){
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('CCFFCC'); // Light green color
                endif;
            }

            if($value == "not_yet"){
                $spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row, ""); // Set values in the appropriate row
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                if($index < sizeof($data)-1) :
                    $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getFill()->getStartColor()->setARGB('000000'); // Light green color
                endif;
            }
            // Set vertical alignment to center
            $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center text horizontally;

        }
          // Set the height for the current row (for example, 40)
        $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
        $row++;
    }
    $row_footer_index = sizeof($rows)+ 4;
    $rows_footer = [
        ["name"=>"Jour férié","color"=>"CCFFCC"],
        ["name"=>"Weekend","color"=>"dfdfdf"],
        ["name"=>"Congé","color"=>"72c0fd"],
        ["name"=>"Justifié","color"=>"fff491"],
        ["name"=>"Non justifié","color"=>"f39f99"],
        ["name"=>"Non encore rejoint / Départ confirmé","color"=>"000000"]
    ];
    foreach ($rows_footer as $index => $data) {
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1);
        $spreadsheet->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(40);
        $spreadsheet->getActiveSheet()->setCellValue($columnLetter.$row_footer_index, $data['name']);
        $spreadsheet->getActiveSheet()->getStyle($columnLetter.$row_footer_index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $spreadsheet->getActiveSheet()->getStyle($columnLetter.$row_footer_index)->getFill()->getStartColor()->setARGB($data['color']); // Light green color
        if($data['color'] == "000000"){
            $spreadsheet->getActiveSheet()->getStyle($columnLetter.$row_footer_index)->getFont()->getColor()->setARGB('FFFFFF');
        }
       
        // Set vertical alignment to center
        $spreadsheet->getActiveSheet()->getStyle($columnLetter.$row_footer_index)->getAlignment()
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center text horizontally;

          // Set the height for the current row (for example, 40)
        $spreadsheet->getActiveSheet()->getRowDimension($row_footer_index)->setRowHeight(20);
        $row_footer_index++;
    }
     // Clear any previous output
     ob_end_clean();
     ob_start();

    // Output to browser
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="absences_' . date('m-Y') . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function getRowWorkDate()
{
	?>
	<tr>
		<td>
            <input type="date" name="workdate_start_date[]" class="form-control" required>
        </td>
        <td>
            <input type="date" name="workdate_contract_signing_date[]" class="form-control">
        </td>
		<td>
            <input type="date" name="workdate_end_date[]" class="form-control">
        </td>
		<td class="add-remove text-right">
			<input type="hidden" name="workdate_id[]" value="0" class="id-item-input">
			<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer la ligne"></i>
		</td>
	</tr>
	<?php
}

function removeRowWorkDate($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$item_facture = workdate::find($id);
		if ($item_facture->delete() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

