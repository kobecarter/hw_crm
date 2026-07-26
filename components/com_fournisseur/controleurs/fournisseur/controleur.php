<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addFournisseur':
            addFournisseur($_POST);
            break;
        case 'editFournisseur':
            editFournisseur($_POST);
            break;
        case 'deleteFournisseur':
            deleteFournisseur($_POST);
            break;
        case "enableFournisseur":
            enableFournisseur($_POST);
            break;
		case "filterFournisseur":
            filterFournisseur($_POST);
            break;	
		case "exportFournisseur":
            exportFournisseur($_GET);
            break;
        case "removeDoc":
            removeDoc($_POST);
            break;    
    }
}

function removeDoc($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $fournisseur = fournisseur::find($id);
        $fournisseur->setDoc('');
        if($fournisseur->edit() == 1 && file_exists("../../../images/fournisseurs/" . $fournisseur->getDoc())){
            @unlink("../../../images/fournisseurs/" . $fournisseur->getDoc());
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function addFournisseur($data)
{
    $indices = array("nom");
    if (fieldCheck($data, $indices)) {
        if (buildFournisseur($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editFournisseur($data)
{
    $indices = array("id", "titre");
    if (fieldCheck($data, $indices)) {
        if (buildFournisseur($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteFournisseur($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $fournisseur = new fournisseur();
        $fournisseur->setId($data['id']);
        if ($fournisseur->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableFournisseur($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices))
    {
        $fournisseur = fournisseur::find($data['id'],$_SESSION['agence']);
        $fournisseur->setActive($data['state'] == "oui" ? 0 : 1);
        if ($fournisseur->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildFournisseur($data, $id = null)
{
    $fournisseur = new fournisseur();
	
	$photo = $doc = array();
    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
        $photo = uploadFiles('photo','../../../images/fournisseurs/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
    }
    
    if(isset($_FILES['doc']) && $_FILES['doc']['name'][0]!=''){
        $doc = uploadFiles('doc','../../../images/fournisseurs/',  array('jpg','jpeg','gif','png','pdf','doc','docx','xlsx','JPG','JPEG','GIF','PNG','PDF','DOC','DOCX','XLSX'));
    }

    if($id){
        $fournisseur = fournisseur::find($id,$_SESSION['agence']);
    }
	
	if(isset($photo[0])) {
		$fournisseur->setPhoto($photo[0]);
	}
	if(isset($doc[0])) {
		$fournisseur->setDoc($doc[0]);
	}
	
	$fournisseur->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
	$fournisseur->setSource($data['source']);
    $fournisseur->setActive(isset($data['active']) ? 1 : 0);
    $fournisseur->setValide(isset($data['valide']) ? 1 : 0);
    $fournisseur->setTitre($data['titre']);
	$fournisseur->setPrenom($data['prenom']);
    $fournisseur->setNom($data['nom']);
	$fournisseur->setRaisonSocial($data['raison_social']);
	$fournisseur->setICE($data['ice']);
    $fournisseur->setTel($data['tel']);
	$fournisseur->setTel2($data['tel2']);
	$fournisseur->setTel3($data['tel3']);
    $fournisseur->setEmail($data['email']);
    $fournisseur->setCp($data['cp']);
    $fournisseur->setAdresse($data['adresse']);
	$fournisseur->setAdresse2($data['adresse2']);
    $fournisseur->setVille($data['ville']);
    $fournisseur->setPays($data['pays']);
    $fournisseur->setCategorie($data['categorie']);
    $fournisseur->setLien($data['lien']);
    $fournisseur->setUserAdd($_SESSION['user']->getId());
    $fournisseur->setDateAdd(date("Y-m-d"));
    $fournisseur->setLastEdit(date("Y-m-d"));

    return $fournisseur;
}

function filterFournisseur($data)
{
	$indices = array("year");
	if (fieldCheck($data, $indices)) {
		$year = intval($data['year']);

		$fournisseurs = fournisseur::filterFournisseur($year,$_SESSION['agence']);
?>
		<table class="table table-stripped table-center table-hover datatable">
			<thead class="thead-light">
				<tr>
					<th>ID</th>
					<th>Nom complet</th>
					<th>Téléphone</th>
					<th>Email</th>
					<th>Raison social</th>
					<th class="text-right">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($fournisseurs as $fournisseur): ?>
				<tr>
					<td><?php echo $fournisseur->getId(); ?></td>
					<td>
						<?php $photoLink = $fournisseur->getPhoto() != '' ? "images/fournisseurs/" . $fournisseur->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
						<h2 class="table-avatar">
							<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Fournisseur Image"> <?php echo $fournisseur->getPrenom() . " " . $fournisseur->getNom(); ?></a>
						</h2>
					</td>
					<td><?php echo $fournisseur->getTel(); ?></td>
					<td><?php echo $fournisseur->getEmail(); ?></td>
					<td><?php echo $fournisseur->getRaisonSocial(); ?></td>
					<td class="text-right">
						<?php $state = $fournisseur->isActive() ? 'oui' : 'non'; ?>
						<?php $title = $fournisseur->isActive() ? 'Actif' : 'Inactif'; ?>
						<?php $color = $fournisseur->isActive() ? 'text-success' : 'text-danger'; ?>
						<?php $ico = $fournisseur->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
						<a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $fournisseur->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>

						<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a> 

						<a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $fournisseur->getId(); ?>"><i class="far fa-trash-alt"></i></a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<script>
			$(function() {
				if ($('.datatable').length > 0) {
					$('.datatable').DataTable({
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

function exportFournisseur($data){
	
		$year = isset($data['year']) ? intval($data['year']) : false;
		$fournisseurs = fournisseur::filterFournisseur($year,$_SESSION['agence']);
		$rows = array();
	
		// header
		$header = array(
			'Année' => 'Année',
			'Fournisseur' => 'Fournisseur',
			'Prestation' => 'Prestation',
			'Facture Payée' => 'Facture Payée',
			'Facture Impayée' => 'Facture Impayée',
			'Remarque' => 'Remarque',
		);
		array_push($rows,$header);
		$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');


		foreach($fournisseurs as $fournisseur){
			$nomFournisseur = $fournisseur->getRaisonSocial() != '' ? $fournisseur->getRaisonSocial() : $fournisseur->getNom() . ' ' . $fournisseur->getPrenom();
			$nbrFacture = facture::count(1, $year, $fournisseur->getId());
			$nbrFactureImp = facture::count(0, $year, $fournisseur->getId()) + facture::count(2, $year, $fournisseur->getId());
			
			$factures = facture::facturePerYearFournisseur(false, $year, $fournisseur->getId());
			
			$prestations = '';
			foreach($factures as $facture){
				$items = $facture->getItems();

				foreach($items as $item){
					//echo utf8_decode($item->getTitre()).'****';
					$prestations .= ($item->getTitre()).' *_*_*_* ';
				}
			}
			$row['Année'] = $year;
			$row['Fournisseur'] = $nomFournisseur;
			$row['Prestation'] = $prestations;
			$row['Facture Payée'] = $nbrFacture;
			$row['Facture Impayée'] = $nbrFactureImp;
			$row['Remarque'] = "";
			array_push($rows,$row);
		}

		include_once("../classes/xlsxwriter.class.php");

		$filename = "fournisseurs.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$writer = new XLSXWriter();
		$writer->setAuthor('Hello World'); 
		$cpt = 0;
		foreach($rows as $row){
			if($cpt == 0)
				$writer->writeSheetRow('Sheet1', $row, $styles1);
			else{
				$writer->writeSheetRow('Sheet1', $row, $row_options = array('height'=>20,'wrap_text'=>true));
				//$writer->insert_textbox('C2', 'A simple textbox with some text');
			}

			$cpt++;
		}
		
		$writer->writeToStdOut();
}