<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addCharge':
            addCharge($_POST);
            break;
        case 'editCharge':
            editCharge($_POST);
            break;
        case 'deleteCharge':
            deleteCharge($_POST);
            break;
        case "enableCharge":
            enableCharge($_POST);
            break;
        case "exportCharges":
            exportCharges($_GET);
            break;
    }
}

function addCharge($data)
{
    $indices = array("titre");
    if (fieldCheck($data, $indices)) {
        if (buildCharge($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editCharge($data)
{
    $indices = array("id", "titre");
    if (fieldCheck($data, $indices)) {
        if (buildCharge($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteCharge($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $charge = charge::find($id,$_SESSION['agence']);
        if ($charge->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableCharge($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices))
    {
        $charge = charge::find($data['id'],$_SESSION['agence']);
        $charge->setPaid($data['state'] == "oui" ? 0 : 1);
        if ($charge->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildCharge($data, $id = null)
{
    $charge = new charge();
	
	$photo = array();
    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
        $photo = uploadFiles('photo','../../../images/charges/',  array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    }
	
    if($id){
		$charge = charge::find($id,$_SESSION['agence']);
    }
	if(isset($photo[0])) {
		$charge->setPhoto($photo[0]);
	}
	$charge->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
	$charge->setPaidBy(user::find($data['paid_by']));
    $charge->setUser(user::find($data['id_user']));
	$charge->setType($data['type']);
	$charge->setTitre($data['titre']);
	$charge->setDescription($data['description']);
	$charge->setTotal($data['total']);
	$charge->setDevise($data['devise']);
    $charge->setPaid(isset($data['paid']) ? 1 : 0);
	$charge->setFacture(isset($data['facture']) ? 1 : 0);
	$charge->setRefunded(isset($data['refunded']) ? 1 : 0);
    $charge->setDatePayment(dateBD($data['date_payment']));
	$charge->setModePayment($data['mode_payment']);
	$charge->setDateCharge(dateBD($data['date_charge']));
    $charge->setDateAdd(date("Y-m-d"));
    $charge->setLastEdit(date("Y-m-d"));

    return $charge;
}

function exportCharges($data)
{
	$indices = array("from", "to");
	$from = false;
	$to = false;
	if (fieldCheck($data, $indices)) {
		$from = dateBD($data['from']);
		$to = dateBD($data['to']);
	}
	$charges = charge::findAll(false,$_SESSION['agence'], $from, $to);

	// Create new Spreadsheet object
	$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(15);

	$header = [
		"Charge",
		"Type",
		"Montant",
		"Rembourssé",
		"Date charge",
		"Date paiement",
		"Description"
	];

	foreach ($header as $index => $h) {
		$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
		$spreadsheet->getActiveSheet()->setCellValue($columnLetter . '1', $h); // Set headers in row 1
		$spreadsheet->getActiveSheet()->getStyle($columnLetter . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle($columnLetter . '1')->getFill()->getStartColor()->setARGB('f2f2f2');
		$spreadsheet->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(30);
		$spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight(40);
		$spreadsheet->getActiveSheet()->getStyle($index)->getAlignment()
			->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle($index)->getAlignment()
			->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center text horizontally;
	}
	$row_index = 2;
	foreach ($charges as $key => $charge) {
		$row = [
			$charge->getTitre(),
			$charge->getType(),
			number_format($charge->getTotal(), 2, ',', ' '). ' ' . $charge->getDevise(),
			$charge->isRefunded() ? "Rembourssé" : "Non Rembourssé",
			normaldate($charge->getDateCharge()),
			normaldate($charge->getDatePayment()),
			$charge->getDescription()
		];
		foreach ($row as $key1 => $value) {
			$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key1 + 1);
			$spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row_index, $value);
			$spreadsheet->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(30);
			$spreadsheet->getActiveSheet()->getRowDimension($row_index)->setRowHeight(40);
			$spreadsheet->getActiveSheet()->getStyle($key1)->getAlignment()
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
			$spreadsheet->getActiveSheet()->getStyle($key1)->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		}
		$row_index++;
	}

	// Clear any previous output
	ob_end_clean();
	ob_start();

	// Output to browser
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$filename = "charges";
	if (fieldCheck($data, $indices)) {
	    $filename = "charges_from_".dateBD($data['from'])."_to_".dateBD($data['to']);
	}
	header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
	header('Cache-Control: max-age=0');

	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save('php://output');
	exit;
}