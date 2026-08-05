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
        $charge = buildCharge($data);
        if ($charge->add() != 1) {
            echo "2";
            return;
        }
        // Capturé immédiatement après l'insertion, avant toute autre INSERT (creerBulletinDepuisCharge()
        // insère aussi dans crm_payslip, ce qui écraserait LAST_INSERT_ID() si on appelait
        // charge::getLastId() après coup — même bug déjà rencontré et corrigé côté TVA/charges).
        $idCharge = charge::getLastId();

        if (isset($data['is_payslip']) && $data['is_payslip'] == '1'
            && isset($data['id_resourcehumaine_payslip']) && $data['id_resourcehumaine_payslip'] !== ''
            && $charge->getPhoto() != '') {
            creerBulletinDepuisCharge($data, $idCharge, $charge->getPhoto());
        }

        echo "1";
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
	$charge->setTvaTaux(isset($data['tva_taux']) && $data['tva_taux'] !== '' ? $data['tva_taux'] : null);
	$charge->setTvaDeductible(isset($data['tva_deductible']) ? 1 : 0);
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

// Bulletin de paie déposé sur la page Charges (dropzone IA) : le justificatif déjà enregistré
// pour la charge (uploadFiles() dans buildCharge() ci-dessus, dossier images/charges/) est
// dupliqué vers l'espace employé (images/resourceshumaines/payslips/) — uploadFiles() utilise
// move_uploaded_file(), le fichier temporaire d'origine n'existe donc plus, une simple copie
// du fichier déjà sauvegardé est le seul moyen de le faire atterrir dans les deux dossiers.
// Aucune charge normale n'est affectée : n'est appelée que lorsque l'extraction IA a réussi
// et que l'employé a été confirmé côté formulaire (jamais de liaison automatique silencieuse).
function creerBulletinDepuisCharge($data, $idCharge, $nomFichierCharge)
{
    $resourcehumaine = resourcehumaine::find(intval($data['id_resourcehumaine_payslip']));
    if (!$resourcehumaine || $resourcehumaine->getId() == 0) {
        return false;
    }

    $cheminSource = '../../../images/charges/' . $nomFichierCharge;
    if (!file_exists($cheminSource)) {
        return false;
    }

    $dossierDestination = '../../../images/resourceshumaines/payslips';
    $ext = substr($nomFichierCharge, strrpos($nomFichierCharge, '.') + 1);
    $nomBase = basename($nomFichierCharge, '.' . $ext);
    $n = '';
    while (file_exists("$dossierDestination/$nomBase$n.$ext")) {
        $n++;
    }
    $nomFichierDestination = "$nomBase$n.$ext";

    if (!@copy($cheminSource, "$dossierDestination/$nomFichierDestination")) {
        return false;
    }

    $mois = isset($data['payslip_mois']) && $data['payslip_mois'] !== '' ? intval($data['payslip_mois']) : (int) date('n');
    $annee = isset($data['payslip_annee']) && $data['payslip_annee'] !== '' ? intval($data['payslip_annee']) : (int) date('Y');

    $payslip = new payslip();
    $payslip->setResourcehumaine($resourcehumaine);
    // Même convention de nommage que l'ajout direct depuis la fiche employé, voir
    // payslip::titreAuto() - "Bulletin de paie {Nom} {MM/YYYY}".
    $payslip->setTitle(payslip::titreAuto($resourcehumaine, $mois, $annee));
    $payslip->setDate(date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $annee, $mois))));
    $payslip->setFile($nomFichierDestination);
    $payslip->setIdCharge($idCharge);

    return $payslip->add() == 1;
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