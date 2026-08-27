<?php
// Sans cet autoload, getToken()/getInfoFromToken() (JWT) échouent silencieusement
// (Throwable avalé -> null) quand ce contrôleur est atteint directement via l'API
// serveur-à-serveur du site (pas via index.php, qui le charge déjà ailleurs) —
// même correctif que com_facture/controleurs/facture/controleur.php.
require_once dirname(__DIR__, 4) . '/vendor/autoload.php';
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addClient':
            addClient($_POST);
            break;
        case 'editClient':
            editClient($_POST);
            break;
        case 'deleteClient':
            deleteClient($_POST);
            break;
        case "enableClient":
            enableClient($_POST);
            break;
        case "archiveClient":
            archiveClient($_POST);
            break;
        case "retablirClient":
            retablirClient($_POST);
            break;
        case "filterClient":
            filterClient($_POST);
            break;
        case "exportClient":
            exportClient($_GET);
            break;
        case "exportClientWithFilter":
            exportClientWithFilter($_GET);
            break;
        case "exportEmail":
            exportEmail($_GET);
            break;
        case "getClientSelect":
            getClientSelect($_GET);
            break;    
        case "loginApi":
            loginApi($_POST);
            break;
        case "googleLoginApi":
            googleLoginApi($_POST);
            break;
        case "facebookLoginApi":
            facebookLoginApi($_POST);
            break;
        case "verifyEmailApi":
            verifyEmailApi($_POST);
            break;
        case "setNewPasswordApi":
            setNewPasswordApi($_POST);
            break;
        case "getInfoFromTokenApi":
            getInfoFromTokenApi($_GET);
            break;
        case "findClientByIdApi":
            findClientByIdApi($_GET);
            break;
        case "updateProfileApi":
            updateProfileApi($_POST);
            break;
        case "addAttestation":
            addAttestation($_POST);
            break;
        case "deleteAttestation":
            deleteAttestation($_POST);
            break;
        case "findAttestationsByClientApi":
            findAttestationsByClientApi($_GET);
            break;
        case "signAttestationApi":
            signAttestationApi($_POST);
            break;
        case "pdfAttestationApi":
            pdfAttestationApi($_GET);
            break;
        case "countPendingAttestationsApi":
            countPendingAttestationsApi($_GET);
            break;
        case "findClientSocialsByClientApi":
            findClientSocialsByClientApi($_GET);
            break;
        case "findParrainagesByClientApi":
            findParrainagesByClientApi($_GET);
            break;
        case "createParrainageApi":
            createParrainageApi($_POST);
            break;
        case "downloadAttestationAdmin":
            downloadAttestationAdmin($_GET);
            break;
    }
}

function getClientSelect()
{
    $lastId = client::getLastId();
    $clients = client::findAll(true,false,$_SESSION['agence']);
    ?>
    <select class="chosen-select form-select form-control client-select" name="client" required>
	    <option value="" selected disabled>Sélectionner</option>
		<?php foreach ($clients as $client) : ?>
			<?php $sl = $lastId == $client->getId() ? "selected" : ""; ?>
			<option value="<?php echo $client->getId() ?>" data-agence="<?php echo $client->getAgence()->getId(); ?>" <?php echo $sl; ?>><?php echo $client->getNom() . ' ' . $client->getPrenom() . ' - ' . $client->getRaisonSocial(); ?></option>
		<?php endforeach; ?>
	</select>
	<script>
		$(".chosen-select").select2();
		$(document).trigger("clientSelectRefreshed");
	</script>
    <?php
}

function addClient($data)
{
    $indices = array("nom", "fonction", "agence");
    if (fieldCheck($data, $indices)) {
        if (buildClient($data)->add() == 1) {
            $newId = client::getLastId();
            if (isset($data['presentation_file']) && !empty($data['presentation_file'])) {
                $presentation = new presentationFile();
                $presentation->setIdClient($newId);
                $presentation->setFichier($data['presentation_file']);
                $presentation->setExtractedJson(isset($data['presentation_extracted']) ? $data['presentation_extracted'] : '');
                $presentation->add();
            }
            echo "1|" . $newId;
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editClient($data)
{
    $indices = array("id", "titre", "fonction");
    if (fieldCheck($data, $indices)) {
        if (buildClient($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteClient($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        // find($id, $agence) plutôt que new client()+setId() : cette dernière forme ne vérifie
        // JAMAIS que le client appartient bien à l'agence de l'utilisateur connecté - n'importe
        // quel id valide, même d'une autre agence, se faisait supprimer (IDOR sur suppression).
        $client = client::find($data['id'], $_SESSION['agence']);
        if ($client->getId() == 0) {
            echo "2";
            return;
        }
        if ($client->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableClient($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices)) {
        $client = client::find($data['id'], $_SESSION['agence']);
        $client->setActive($data['state'] == "oui" ? 0 : 1);
        if ($client->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function archiveClient($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $client = client::find($data['id'], $_SESSION['agence']);
        $client->setArchived(1);
        if ($client->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function retablirClient($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $client = client::find($data['id'], $_SESSION['agence']);
        $client->setArchived(0);
        if ($client->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildClient($data, $id = null)
{
    $client = new client();

    $photo = array();
    if (isset($_FILES['photo']) && $_FILES['photo']['name'][0] != '') {
        $photo = uploadFiles('photo', '../../../images/clients/',  array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
    }

    $siteWebPrecedent = null;
    if ($id) {
        $client = client::find($id, $_SESSION['agence']);
        $siteWebPrecedent = $client->getSiteWeb();
        $client->setUserEdited($_SESSION['user']);
        $client->setAgence(agence::find($data['agence'], $_SESSION['langue']));
    } else {
        $client->setUserAdded($_SESSION['user']);
        $agenceId = isset($data['agence']) && !empty($data['agence']) ? $data['agence'] : $_SESSION['agence'];
        $client->setAgence(agence::find($agenceId, $_SESSION['langue']));
    }

    $nouveauSiteWeb = isset($data['site_web']) ? trim($data['site_web']) : '';
    $client->setSiteWeb($nouveauSiteWeb);

    // Priorité de la photo affichée : un fichier uploadé dans cette même soumission l'emporte
    // toujours (dernière action de l'utilisateur = photo manuelle) ; sinon, si le site internet
    // vient d'être ajouté/modifié, on génère son logo pour remplacer la photo. On retente aussi
    // la génération si le site est déjà renseigné mais qu'aucune photo n'a jamais été obtenue
    // (échec réseau ponctuel lors d'un essai précédent) : sans ce filet, un client resterait
    // bloqué sans logo pour toujours, la détection de changement ne se déclenchant qu'une fois.
    // Si un logo est déjà en place, on ne touche à rien.
    if (isset($photo[0])) {
        $client->setPhoto($photo[0]);
    } elseif ($nouveauSiteWeb != '' && ($nouveauSiteWeb !== $siteWebPrecedent || $client->getPhoto() == '')) {
        $logo = client::generateLogoPhoto($nouveauSiteWeb);
        if ($logo) {
            $client->setPhoto($logo);
        }
    }

    $client->setActive(isset($data['active']) ? 1 : 0);
    $client->setSource($data['source']);
    $client->setTitre($data['titre']);
    $client->setPrenom($data['prenom']);
    $client->setNom($data['nom']);
    $client->setRaisonSocial($data['raison_social']);
    $client->setFonction($data['fonction']);
    $client->setICE($data['ice']);
    $client->setRc($data['rc']);
    $client->setTel($data['tel']);
    $client->setTel2($data['tel2']);
    $client->setTel3($data['tel3']);
    $client->setEmail($data['email_client']);
    $client->setCp($data['cp']);
    $client->setAdresse($data['adresse']);
    $client->setAdresse2($data['adresse2']);
    $client->setVille($data['ville']);
    $client->setRegion($data['region']);
    $client->setPays($data['pays']);
    $client->setDateAdd(date("Y-m-d"));
    $client->setLastEdit(date("Y-m-d"));

    if (isset($data['password']) && !empty($data['password'])) {
        $client->setPassword($data['password']);
    }

    return $client;
}

function filterClient($data)
{
    $indices = array("from", "to");
    if (fieldCheck($data, $indices)) {
        $from = dateBD($data['from']);
        $to = dateBD($data['to']);

        $clients = client::filterClient(false, $_SESSION['agence'], $from, $to);
?>
        <table class="table table-stripped table-center table-hover datatable-client">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Raison social</th>
                    <th></th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <?php $activity = "Ajouté par " . $client->getUserAdded()->getNom() . " | Modifié par " . $client->getUserEdited()->getNom(); ?>
                    <tr>
                        <td><?php echo $client->getId(); ?></td>
                        <td>
                            <?php $photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                            <h2 class="table-avatar">
                                <a href="index.php?option=com_client&task=edit&id=<?= $client->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $client->getPrenom() . " " . $client->getNom(); ?></a>
                            </h2>
                        </td>
                        <td><?php echo $client->getTel(); ?></td>
                        <td><?php echo $client->getEmail(); ?></td>
                        <td><?php echo $client->getRaisonSocial(); ?></td>
                        <td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
                        <td class="text-right">
                            <?php $state = $client->isActive() ? 'oui' : 'non'; ?>
                            <?php $title = $client->isActive() ? 'Actif' : 'Inactif'; ?>
                            <?php $color = $client->isActive() ? 'text-success' : 'text-danger'; ?>
                            <?php $ico = $client->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
                            <?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) : ?>
                                <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enableClient" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $client->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
                                <a href="index.php?option=com_client&task=edit&id=<?= $client->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']->hasDroit('delete', 'com_client')) : ?>
                                <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 deleteClient" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $client->getId(); ?>"><i class="far fa-trash-alt"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script>
            $(function() {
                // Tooltip
				if ($('[data-toggle="tooltip"]').length > 0) {
					$('[data-toggle="tooltip"]').tooltip();
				}
                
                if ($('.datatable-client').length > 0) {
                    $('.datatable-client').DataTable({
                        aoColumnDefs: [{
                            bSortable: !1,
                            aTargets: [0, 1]
                        }],
                        aaSorting: []
                    });
                }
            })
        </script>
<?php
    }
}

function exportClient($data)
{
    $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
    $year = isset($data['year']) ? intval($data['year']) : false;
    $clients = client::filterClient($year, $_SESSION['agence']);
    $rows = array();

    // Define styles for the header, first data row, and subsequent data rows
    $styles_header = array(
        'font' => 'Arial',
        'font-size' => 12,
        'font-style' => 'bold',
        'fill' => '#ddd',
        'halign' => 'center',
        'border' => 'left,right,top,bottom',
    );
    $styles1_first_row = array(
        'font' => 'Arial',
        'font-size' => 11,
        'font-style' => 'bold',
        'fill' => '#eee', // Change the fill color for the first data row
        'halign' => 'center',
        'border' => 'left,right,top,bottom',
    );
    $styles1_subsequent_rows_Payee = array(
        'font' => 'Arial',
        'font-size' => 10,
        'halign' => 'center',
        'fill' => '#CCFFCC',
        'border' => 'left,right,top,bottom',
    );
    $styles1_subsequent_rows_Impayee = array(
        'font' => 'Arial',
        'font-size' => 10,
        'halign' => 'center',
        'fill' => '#FFCCCC',
        'border' => 'left,right,top,bottom',
    );

    include_once("../classes/xlsxwriter.class.php");

    $filename = "clients.xlsx";
    header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $writer = new XLSXWriter();
    $xlsxTempDir = __DIR__ . '/../../../../images/tmp';
    if (!is_dir($xlsxTempDir)) {
        @mkdir($xlsxTempDir, 0775, true);
    }
    $writer->setTempDir($xlsxTempDir);

    $header_format = array(
        'font-style' => 'bold',
        'align' => 'center',
        'font-size' => 13, // Set the font size to 14
        'padding' => 10,
    );

    // Set up a header title
    $header_title = $agence->getNom();

    // Add the header title manually to cell C1
    $writer->writeSheetHeader('Sheet1', []);

    // Write the header title to cell C1
    $writer->writeSheetRow('Sheet1', ['', '', $header_title], $header_format);


    $writer->setAuthor('Hello World');

    // Header row
    $header = array('Année' => 'Année', 'Client' => 'Client', 'Prestation' => 'Prestation', 'Total' => 'Total',  'Facture Payée' => 'Facture Payée', 'Facture Impayée' => 'Facture Impayée', 'Remarque' => 'Remarque');
    $writer->writeSheetRow('Sheet1', $header, $styles_header);

    foreach ($clients as $client) {
        $nomClient = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();
        $nbrFacture = facture::count(1, $year, $client->getId(), $agence->getId());
        $nbrFactureImp = facture::count(0, $year, $client->getId(), $agence->getId()) + facture::count(2, $year, $client->getId(), $agence->getId()) - facture::count(1, $year, $client->getId(), $agence->getId());
        $factures = facture::facturePerYearClient(false, $year, $client->getId(), $agence->getId());

        $prestations = '';
        $IdAll = '';
        $Remarque = '';
        $AllPrestations = '';
        $AllTotal = '';
        $Status = '';
        $Total = 0;
        foreach ($factures as $facture) {
            $items = $facture->getItems();

            foreach ($items as $item) {
                if (count($items) > 1) {
                    $prestations .= ($item->getTitre()) . "\n";
                    $Total += $item->getTotal();
                } else {
                    $prestations .= $item->getTitre();
                    $Total = $item->getTotal();
                }
            }
            $IdAll .= $facture->getNumero() . ' _ ';
            $Remarque .= $facture->getRemarque() . ' _ ';
            $AllPrestations .= ($prestations) . ' _ ';
            $AllTotal .= ($Total) . ' _ ';
            $Status .= $facture->getStatu() . ' _ ';
            $prestations = '';
            $Total = 0;
        }

        $prestationsArray = explode(' _ ', $AllPrestations);
        $IdAllArray = explode(' _ ', $IdAll);
        $StatusArray = explode(' _ ', $Status);
        $RemarqueArray = explode(' _ ', $Remarque);
        $TotalArray = explode(' _ ', $AllTotal);
        $rowCount = count($prestationsArray);

        // Create multiple rows for Prestation
        for ($i = 0; $i < $rowCount; $i++) {
            $row = array(
                'Année' => ($i === 0) ? $year : '', // Show the year in the first row only
                'Client' => ($i === 0) ? $nomClient : $IdAllArray[$i - 1], // Show the client name in the first row only
                'Prestation' => ($i !== 0) ? $prestationsArray[$i - 1] : '',
                'Total' => ($i !== 0) ? $TotalArray[$i - 1] : '',
                'Facture Payée' => ($i === 0) ? $nbrFacture : '', // Show the number of paid invoices in the first row only
                'Facture Impayée' => ($i === 0) ? $nbrFactureImp : '', // Show the number of unpaid invoices in the first row only
                'Remarque' => ($i === 0) ? '' : $RemarqueArray[$i - 1], // Hide Remarque in subsequent rows
            );

            // Apply the appropriate style based on the row index
            $row_style = ($i === 0) ? $styles1_first_row : (($StatusArray[$i - 1] == '1') ? $styles1_subsequent_rows_Payee : $styles1_subsequent_rows_Impayee);

            $writer->writeSheetRow('Sheet1', $row, $row_style);
        }
    }

    $writer->writeToStdOut();
}

function exportClientWithFilter ($data)
{
        $from = isset($data['from']) && !empty($data['from']) ? dateBD($data['from']) : false;
        $to = isset($data['to']) && !empty($data['to']) ? dateBD($data['to']) : false;
        $clients = client::filterClient(false, $_SESSION['agence'], $from, $to);

		// Create new Spreadsheet object
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(15);

		$header = [
			"Nom complete",
			"Téléphone",
			"Email",
			"Raison social",
            "Date de création"
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
		foreach ($clients as $key => $client) {
			$row = [
				$client->getRaisonSocial(),
                $client->getEmail(),
                $client->getTel(),
                $client->getNom() . ' ' . $client->getPrenom(),
                $client->getDateAdd(),
				
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
		header('Content-Disposition: attachment;filename="clients.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
}

function exportEmail()
{

    $clients = client::findAll(false, false, $_SESSION['agence']);
    // Create an array to hold the email data.
    $rows = array();

    // Header for the export file.
    $header = array(
        'Client' => 'Client',
        'Email' => 'Email',
    );
    array_push($rows, $header);
    $styles1 = array('font' => 'Arial', 'font-size' => 10, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');
    // Iterate through the clients and add client names and email addresses to the export array.
    foreach ($clients as $client) {
        $nomClient = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();

        $row = array(
            'Client' => $nomClient,
            'Email' => $client->getEmail(), // Replace with the appropriate method to get the email address from the client object.
        );
        array_push($rows, $row);
    }

    // Perform the Excel export using XLSXWriter (assuming you have the XLSXWriter class available).
    include_once("../classes/xlsxwriter.class.php");

    $filename = "clientEmail.xlsx";
    header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $writer = new XLSXWriter();
    $xlsxTempDir = __DIR__ . '/../../../../images/tmp';
    if (!is_dir($xlsxTempDir)) {
        @mkdir($xlsxTempDir, 0775, true);
    }
    $writer->setTempDir($xlsxTempDir);
    $writer->setAuthor('Hello World');
    $cpt = 0;
    foreach ($rows as $row) {
        if ($cpt == 0)
            $writer->writeSheetRow('Sheet1', $row, $styles1);
        else {
            $writer->writeSheetRow('Sheet1', $row, $row_options = array('height' => 20, 'wrap_text' => true));
            //$writer->insert_textbox('C2', 'A simple textbox with some text');
        }

        $cpt++;
    }

    $writer->writeToStdOut();
}

// API 

function loginApi($data)
{
    echo client::loginApi($data['email'], $data['password']);
}

function googleLoginApi($data)
{
    echo client::googleLoginApi($data);
}

function facebookLoginApi($data)
{
    echo client::facebookLoginApi($data);
}

function verifyEmailApi($data)
{
    echo client::verifyEmailApi($data['email']);
}

function setNewPasswordApi($data)
{
    echo client::setNewPasswordApi($data);
}

function getInfoFromTokenApi($data)
{
    echo client::getInfoFromTokenApi($data['token']);
}

function findClientByIdApi($data)
{
    echo client::findClientByIdApi($data['id']);
}

function updateProfileApi($data)
{
    echo client::updateProfileApi($data);
}

function addAttestation($data)
{
    $idClient = isset($data['id_client']) ? (int) $data['id_client'] : 0;
    $titre = isset($data['titre']) ? trim($data['titre']) : '';
    $message = isset($data['message']) ? trim($data['message']) : '';
    if ($idClient <= 0 || $titre === '') {
        echo "0";
        return;
    }
    $fichier = attestation::handleUpload('fichier');
    if ($fichier === null) {
        // Pas de fichier valide (absent, ou extension non autorisée) : le
        // document est le cœur de l'attestation, on n'enregistre pas sans lui.
        echo "3";
        return;
    }
    $a = new attestation();
    $a->setIdClient($idClient)
      ->setIdAgence(isset($_SESSION['agence']) ? $_SESSION['agence'] : 1)
      ->setTitre($titre)
      ->setMessage($message !== '' ? $message : null)
      ->setFichier($fichier)
      ->setIdUserAdded(isset($_SESSION['user']) ? $_SESSION['user']->getId() : null);
    $a->add();
    echo "1";
}

function deleteAttestation($data)
{
    $id = isset($data['id']) ? (int) $data['id'] : 0;
    echo ($id > 0 && attestation::deleteApi($id)) ? "1" : "0";
}

function findAttestationsByClientApi($data)
{
    $items = attestation::findAllByClientApi(isset($data['client']) ? $data['client'] : 0);
    array_walk_recursive($items, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
    echo json_encode($items);
}

// Accès admin réservé à hasDroit('view','com_client') côté router.php — permet
// à l'agence de rouvrir le document envoyé, signé ou non, pour vérifier ce
// qu'elle a réellement transmis au client.
function downloadAttestationAdmin($data)
{
    $id = isset($data['id']) ? (int) $data['id'] : 0;
    if ($id <= 0 || !attestation::streamFileAdmin($id)) {
        header('HTTP/1.1 404 Not Found');
        echo 'Document introuvable.';
    }
}

function signAttestationApi($data)
{
    $idClient = isset($data['client']) ? $data['client'] : 0;
    $idAttestation = isset($data['id']) ? $data['id'] : 0;
    $nom = isset($data['nom']) ? $data['nom'] : '';
    echo json_encode(attestation::signApi($idClient, $idAttestation, $nom));
}

function pdfAttestationApi($data)
{
    $idClient = isset($data['client']) ? $data['client'] : 0;
    $idAttestation = isset($data['id']) ? $data['id'] : 0;
    echo json_encode(attestation::pdfApi($idClient, $idAttestation));
}

function countPendingAttestationsApi($data)
{
    echo json_encode(array("count" => attestation::countPendingByClientApi(isset($data['client']) ? $data['client'] : 0)));
}

function findClientSocialsByClientApi($data)
{
    $items = clientsocial::findAllByClientApi(isset($data['client']) ? $data['client'] : 0);
    array_walk_recursive($items, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
    echo json_encode($items);
}

function findParrainagesByClientApi($data)
{
    $items = clientparrainage::findAllByClientApi(isset($data['client']) ? $data['client'] : 0);
    array_walk_recursive($items, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
    echo json_encode($items);
}

function createParrainageApi($data)
{
    echo json_encode(clientparrainage::createApi(
        isset($data['client']) ? $data['client'] : 0,
        isset($data['parrain_nom']) ? $data['parrain_nom'] : '',
        isset($data['parrain_email']) ? $data['parrain_email'] : '',
        isset($data['filleul_nom']) ? $data['filleul_nom'] : '',
        isset($data['filleul_entreprise']) ? $data['filleul_entreprise'] : '',
        isset($data['filleul_email']) ? $data['filleul_email'] : '',
        isset($data['filleul_tel']) ? $data['filleul_tel'] : '',
        isset($data['message']) ? $data['message'] : ''
    ));
}
