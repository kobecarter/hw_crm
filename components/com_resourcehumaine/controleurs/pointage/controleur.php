<?php
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'importPointage':
            importPointage($_POST);
            break;

        case 'filterPointage':
            filterPointage($_POST);
            break;
    }
}

function importPointage($data)
{
    if(isset($_FILES['pointage']) && $_FILES['pointage']['name'][0]!=''){
        $tempFilePath = $_FILES['pointage']['tmp_name'][0];

        $spreadsheet = IOFactory::load($tempFilePath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $jsonData = []; 
        function isColumnAfterD($column) {
            // Convert Excel-like column label to a numeric index
            $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
            return $columnIndex >= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString('E');
        }

        // Extract date headers from row 4
        $dateHeaders = [];

        foreach ($sheetData[4] as $column => $cellValue) {
            if (isColumnAfterD($column)) {
                $dateHeaders[$column] = $cellValue; 
            }
        }

        // Process each data row from row 5 onward
        foreach ($sheetData as $rowIndex => $row) {
            if ($rowIndex < 5) { // Skip until data rows
                continue;
            }
            if (!empty($row['C'])) { // Check if there's a name
                $dataEntry = [
                    "reference_pointage" => $row['A'],
                    "times" => []
                ];

                // Loop through date headers and get times for each date
                foreach ($dateHeaders as $column => $date) {
                    $time = isset($row[$column]) ? $row[$column] : "--:--"; // Default if empty
            
                    // Split the time string by newline and then space
                    $timeEntries = explode("\n",$time);
                    // Map each time entry to an array of time parts
                    $timeArray = array_merge(...array_map(function($entry) {
                        return explode(" ", $entry);
                    }, $timeEntries));

                    $remainingTimes = [];
                    foreach($timeArray as $value){
                        if($value != "--:--"){
                            array_push($remainingTimes,$value);
                        }
                    }
                    
                    // Add the times to the data entry
                    $dataEntry["times"][] = [
                        "day" => DateTime::createFromFormat('Y/m/d', $date)->format('Y-m-d'),
                        "times" => array_chunk($timeArray,4)[0]
                    ];
                }

                $jsonData[] = $dataEntry;
            }
        }
      
        foreach ($jsonData as $key => $value) {
            $respourcehumaine = resourcehumaine::findByReferencePointage($value['reference_pointage']);
            $data = [];
            foreach ($value['times'] as $key => $time) {
                $data['id_resourcehumaine'] = $respourcehumaine->getId();
                $data['date'] = $time['day'];
                $data['hour1'] = $time['times'][0] == '--:--' ? null : $time['times'][0];
                $data['hour2'] = $time['times'][1] == '--:--' ? null : $time['times'][1];
                $data['hour3'] = $time['times'][2] == '--:--' ? null : $time['times'][2];
                $data['hour4'] = $time['times'][3] == '--:--' ? null : $time['times'][3];
                $data['delay'] = null;
                $data['overtime'] = null;

                if($time['times'][0] != '--:--'){
                    $time1ToCompare = new DateTime($time['times'][0].":00");
                    $time2ToCompare = new DateTime("09:00:00");
                    if(strtotime($time['times'][0].":00") > strtotime("09:00:00")){
                        $interval = $time1ToCompare->diff($time2ToCompare);
                        $timeDifference = $interval->format('%H:%I:%S');
                        $data['delay'] = $timeDifference;
                    }
                    
                }
                $overtime = null;
                for ($i=3; $i >=1 ; $i--) { 
                    if($time['times'][$i] != '--:--'){
                        $overtime = $time['times'][$i];
                        break;
                    }
                }
                if($overtime){
                    $time1ToCompare = new DateTime($overtime.":00");
                    if(date('N',strtotime($data['date'])) == 5){
                        $time2ToCompare = new DateTime("18:30:00");
                    }else{
                        $time2ToCompare = new DateTime("18:00:00");
                    }
                    $interval = $time1ToCompare->diff($time2ToCompare);
                    $timeDifference = $interval->format('%H:%I:%S');
                    $data['overtime'] = $timeDifference == "00:00:00" ? null : $timeDifference;
                }
                build($data)->add();
            }
        }
        echo 1;
        // echo json_encode($jsonData, JSON_PRETTY_PRINT);
    }
}

function filterPointage($data){
    $resources_humaines = resourcehumaine::findAllByStatuses(["Titulaire","Periode de test"]);
    $global_delay = 0;
    $global_delay_numbers = 0;
    $global_count_absences = 0;
    $global_sum_absences_days = 0;
    foreach ($resources_humaines as $key => $value) {
        $pointage = pointage::findAllByResourcehumaineAndMonth($value->getId(),date($data['date']));
        $absences = absence::findByDateMonthly($value->getId(),date('Y-m',strtotime($data['date'])));
        foreach($pointage as $key2 => $value2) {
            if($value2->getDelay()){
                $global_delay += (strtotime($value2->getDelay()) - strtotime("00:00:00"));
                $global_delay_numbers++;
            }
        }
        foreach ($absences as $key => $absence) {
            if($absence->getNatureOfAbsence() == 3){
                $global_sum_absences_days += floatval($absence->getNumberOfDays());
                $global_count_absences++;
            }
        }
    }
    ?>
    <div class="row text-center">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-secondary">Total de minutes de retard </h3>
                    <h4 class="text-danger"><?=gmdate("H:i:s", $global_delay)?></h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-secondary">Compteur de retard </h3>
                    <h4 class="text-danger"><?=$global_delay_numbers?></h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-secondary">Total des absences</h3>
                    <h4 class="text-danger"><?=$global_count_absences?></h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-secondary">Nombre de jours d'absence</h3>
                    <h4 class="text-danger"><?=$global_sum_absences_days?></h4>
                </div>
            </div>
        </div>
    </div>
    <table class="table mb-0 text-center">
        <thead>
            <tr>
                <th class="text-secondary">Jours</th>
                <?php
                $start_day = 1;
                $end_day = date("t",strtotime(date($data['date']."-01")));
                for ($i=$start_day; $i <= $end_day; $i++) :
                    if($i<=9){
                        $i = "0".$i;
                    }
                ?>
                    <th><?=date($data['date'].'-').$i?></th>
                <?php endfor;?>
                <td class="text-danger">Retard</td>
                <td class="text-danger">Nombre de retard</td>
                <td class="text-success">Heurs supplémentaire</td>
                <td class="text-success"> Nombre des heurs supplémentaire</td>
                <td class="text-danger">Total d'absences</td>
                <td class="text-danger">Nombre d'absences</td>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($resources_humaines as $key => $value) :?>
                <tr>
                    <th><?=$value->getFullName()?></th>
                    <?php 
                    $pointage = pointage::findAllByResourcehumaineAndMonth($value->getId(),date($data['date']));
                    $absences = absence::findByDateMonthly($value->getId(),date('Y-m',strtotime($data['date'])));
                    if(sizeof($pointage)>0):
                    $delay = 0;
                    $delay_numbers = 0;
                    $overtime = 0;
                    $overtime_numbers = 0;
                    // Caculate total absences days for each employee
                    $count_absences = 0;
                    $sum_absences_days = 0;
                    foreach ($absences as $key => $absence) {
                        if($absence->getNatureOfAbsence() == 3){
                            $sum_absences_days += floatval($absence->getNumberOfDays());
                            $count_absences++;
                        }
                    }
                    foreach($pointage as $key2 => $value2) :
                        if($value2->getDelay()){
                            $delay += (strtotime($value2->getDelay()) - strtotime("00:00:00"));
                            $delay_numbers++;
                        }
                        if($value2->getOvertime()){
                            $overtime += (strtotime($value2->getOvertime()) - strtotime("00:00:00"));
                            $overtime_numbers++;
                        }
                        ?>
                        
                        <td>
                            <?php
                                echo ($value2->gethour1() && $value2->gethour1() != "--:--" ? $value2->gethour1() : "--:--")."<br>";
                                echo ($value2->gethour2() && $value2->gethour2() != "--:--" ? $value2->gethour2() : "--:--")."<br>";
                                echo ($value2->gethour3() && $value2->gethour3() != "--:--" ? $value2->gethour3() : "--:--")."<br>";
                                echo ($value2->gethour4() && $value2->gethour4() != "--:--" ? $value2->gethour4() : "--:--")."<br>";
                                echo '<span class="text-danger">'.($value2->getDelay() && $value2->getDelay() != "--:--" ? $value2->getDelay() : "--:--").'</span><br>';
                                echo '<span class="text-success">'.($value2->getOvertime() && $value2->getOvertime() != "--:--" ? $value2->getOvertime() : "--:--").'</span><br>';
                            ?>
                        </td>
                    <?php
                        endforeach;
                    ?>
                        <td><span class="text-danger"><?=gmdate("H:i:s", $delay)?></span></td>
                        <td><span class="text-danger"><?=$delay_numbers?></span></td>
                        <td><span class="text-success"><?=gmdate("H:i:s", $overtime)?></span></td>
                        <td><span class="text-success"><?=$overtime_numbers?></span></td>
                        <td><span class="text-danger"><?=$sum_absences_days?></span></td>
                        <td><span class="text-danger"><?=$count_absences?></span></td>
                    <?php
                    endif;
                        if(sizeof($pointage)<=0):
                        for ($i=$start_day; $i <= $end_day; $i++) :
                    ?>
                        <td>
                            --:--<br/>
                            --:--<br/>
                            --:--<br/>
                            --:--<br/>
                        </td>
                    <?php 
                        endfor;
                    ?>
                        <td>--:--</td>
                        <td>0</td>
                        <td>--:--</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    <?php
                        endif;
                    ?>
                </tr>
            <?php
                endforeach;
            ?>
                
        </tbody>
    </table>
    <?php
}

function build($data)
{
    $pointage = new pointage();
	
	$pointage->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
    $pointage->setDate($data['date']);
    $pointage->setHour1($data['hour1']);
    $pointage->setHour2($data['hour2']);
    $pointage->setHour3($data['hour3']);
    $pointage->setHour4($data['hour4']);
    $pointage->setDelay($data['delay']);
    $pointage->setOvertime($data['overtime']);
    $pointage->setDateAdd(date("Y-m-d"));
    $pointage->setLastEdit(date("Y-m-d"));

    return $pointage;
}