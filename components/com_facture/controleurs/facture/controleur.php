<?php
include_once(__DIR__ . '../../../vendor/autoload.php');

if (isset($task) && !empty($task)) {
	switch ($task) {
		case 'addFacture':
			addFacture($_POST);
			break;
		case 'verifierDossierDriveClient':
			verifierDossierDriveClient($_POST);
			break;
		case 'editFacture':
			editFacture($_POST);
			break;
		case 'deleteFacture':
			deleteFacture($_POST);
			break;
		case 'archiveFacture':
			archiveFacture($_POST);
			break;
		case 'retablirFacture':
			retablirFacture($_POST);
			break;
		case "getRowFacture":
			getRowFacture();
			break;
		case 'removeItemFacture':
			removeItemFacture($_POST);
			break;
		case 'customItemFacture':
			customItemFacture($_POST);
			break;
		case 'editItemFacture':
			editItemFacture($_POST);
			break;
		case 'getServicePrice':
			getServicePrice($_POST);
			break;
		case 'getServiceUnite':
			getServiceUnite($_POST);
			break;
		case 'pdfFacture':
			pdfFacture($_GET);
			break;
		case 'pdfFactures':
			pdfFactures($_GET);
			break;
		case 'filterFacture':
			filterFacture($_POST);
			break;
		case 'addFactureAvoir':
			addFactureAvoir($_POST);
			break;
		case 'sendViaMailFacture':
			sendViaMailFacture($_GET);
			break;
		case 'exportFacture':
			exportFacture($_GET);
			break;
		case 'exportFactureTest':
			exportFactureTest($_GET);
			break;
		case 'findAllByClientApi':
			findAllByClientApi($_GET);
			break;
		case 'getPaymentsByClientApi':
			getPaymentsByClientApi($_GET);
			break;
		case 'pdfFactureApi':
			pdfFactureApi($_GET);
			break;
		case 'sendEmailsForeachFacture':
			sendEmailsForeachFacture();
			break;
		case 'change':
			global $db;
			$clinets = client::findAll(false, false, 3);
			foreach ($clinets as $key => $client) {
				$password = $client->getEmail() . "@2024";
				echo $password . " - ";
				$SQLupdate = sprintf(
					"UPDATE crm_client SET  password = %s where id = " . $client->getId(),
					GetSQLValueString(md5($password), "text")
				);

				if (!$db->query($SQLupdate)) {
					echo 1;
				} else {
					echo 0;
				}
			}

			break;
	}
}


function filterFacture($data)
{
	$indices = array("from", "to");
	if (fieldCheck($data, $indices)) {
		$from = dateBD($data['from']);
		$to = dateBD($data['to']);
		$devise = false;
		if(isset($data['devise']) && !empty($data['devise'])){
			$devise = $data['devise'];
		}
		$factures = facture::findAll(false, $from, $to, true, false, false, $_SESSION['agence'],$devise);
		if(isset($data['status']) && !empty($data['status'])){
			$factures = array_filter($factures, function($facture) use ($data){
				if($data['status'] == 'litige'){
					return $facture->getStatu() == 3;
				}else{
					if($data['status'] == 'unpaid'){
						return $facture->getTotal() == $facture->getReste() && $facture->getStatu() != 3;
					}else if($data['status'] == 'partial'){
						return $facture->getTotal() > $facture->getReste() && $facture->getReste() > 0 && $facture->getStatu() != 3;
					}else if($data['status'] == 'paid'){
						return $facture->getReste() <= 0 && $facture->getStatu() != 3;
					}
				}
			});
		}
?>
		<table class="table table-stripped table-center table-hover datatable datatable-facture" data-order="[]">
			<thead class="thead-light">
				<tr>
					<th>ID</th>
					<th>Numéro</th>
					<th>Client</th>
					<th></th>
					<th>Date</th>
					<th>Montant</th>
					<th>Reste</th>
					<th>Status</th>
					<th>Send Mail</th>
					<th></th>
					<th class="text-right">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($factures as $facture) : ?>
					<?php
					$payments = payment::findAll($facture->getId());
					$nbrPayment = sizeof($payments);

					if ($facture->getStatu() == 3) {
						$statu = '<span class="badge bg-primary-light">Litige (' . $nbrPayment . ')</span>';
					} else {
						if ($facture->getTotal() == $facture->getReste()) {
							$statu = '<span class="badge bg-danger-light">Impayée (' . $nbrPayment . ')</span>';
						} elseif ($facture->getTotal() > $facture->getReste() && $facture->getReste() > 0) {
							$statu = '<span class="badge bg-warning-light">Payée partialement (' . $nbrPayment . ')</span>';
						} elseif ($facture->getReste() <= 0) {
							$statu = '<span class="badge bg-success-light">Payée (' . $nbrPayment . ')</span>';
						}
					}

					$nom = $facture->getClient()->getRaisonSocial() != '' ? $facture->getClient()->getRaisonSocial() : $facture->getClient()->getNom() . ' ' . $facture->getClient()->getPrenom();
					$activity = "Ajouté par " . $facture->getUserAdded()->getNom() . " | Modifié par " . $facture->getUserEdited()->getNom();
					?>
					<tr>
						<td><?php echo $facture->getId(); ?></td>
						<td><a href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>">#<?php echo $facture->getNumero(); ?></a></td>
						<td>
							<?php $photoLink = $facture->getClient()->getPhoto() != '' ? "images/clients/" . $facture->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
							<h2 class="table-avatar">
								<a href="<?php echo $photoLink; ?>" data-fancybox><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $nom; ?></a>
							</h2>
						</td>
						<td><?php echo $facture->isAvoir() ? '<span class="badge bg-info-light">avoir</span>' : ''; ?></td>
						<td data-sort="<?= strtotime($facture->getDateFacture()) ?>"><?php echo normaldate($facture->getDateFacture()); ?></td>
						<td><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
						<td><?php echo number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
						<td><?php echo $statu; ?></td>
						<td><a onclick="return confirm('Êtes-vous sûr de vouloir envoyer ce fichier?')" href="components/com_facture/controleurs/router.php?task=sendViaMailFacture&id=<?php echo $facture->getId(); ?>" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi de facture via Mail" data-id="<?= $facture->getId(); ?>" target="_blank"><i class="far fa-paper-plane"></i></td>
						<td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
						<td class="text-right">
							<div class="dropdown dropdown-action">
								<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
								<div class="dropdown-menu dropdown-menu-right">
									<?php if ($_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
										<a class="dropdown-item text-warning" href="index.php?option=com_facture&task=edit&id=<?php echo $facture->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('add', 'com_facture') || $_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
										<a class="dropdown-item text-secondary" href="index.php?option=com_facture&task=avoir&id=<?php echo $facture->getId(); ?>"><i class="fa fa-file-invoice-dollar mr-3"></i>Avoir</a>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
										<a class="dropdown-item text-info" href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('delete', 'com_facture')) : ?>
										<a class="dropdown-item text-danger deleteFacture" href="javascript:void(0);" data-id="<?php echo $facture->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
										<a class="dropdown-item text-success" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>"><i class="far fa-money-bill-alt mr-2"></i>Reglement</a>
										<?php if($facture->getReste() <= 0): ?>
										<a class="dropdown-item text-danger" href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
										<?php endif; ?>
									<?php endif; ?>
									<?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) : ?>
										<?php if ($facture->getDevis()->getId() != 0): ?>
											<a class="dropdown-item text-info" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $facture->getDevis()->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>Devis</a>
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
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
				
				if ($('.datatable-facture').length > 0) {
					$('.datatable-facture').DataTable({
						aoColumnDefs: [{
							bSortable: !1,
							aTargets: [0, 1]
						}],
						aaSorting: [],
					});
				}
			})
		</script>
	<?php
	}
}

function getServicePrice($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$service = service::find($data["id"]);
		$price = $service->getPrix();

		/*if (isset($data['id_item']) && !empty($data['id_item'])) {
			$item_facture = item_facture::find($data['id_item']);
			$price = $item_facture->getPrix();
		}*/

		echo $price;
	}
}

function getServiceUnite($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$service = service::find($data["id"]);
		$unite = $service->getUnite();

		/*if (isset($data['id_item']) && !empty($data['id_item'])) {
			$item_facture = item_facture::find($data['id_item']);
			$price = $item_facture->getPrix();
		}*/

		echo $unite;
	}
}

// Étape de validation avant création d'une facture : cherche, dans le dossier Drive du
// pays/ville du client, un dossier au nom déjà proche du sien (sans y correspondre exactement -
// une correspondance exacte est de toute façon retrouvée en silence, jamais dupliquée). Appelé en
// AJAX depuis le formulaire "Ajouter facture" AVANT l'envoi réel : si rien de similaire n'est
// trouvé, le formulaire s'envoie normalement sans interruption ; sinon une modale de confirmation
// est affichée côté client (voir components/com_facture/views/facture/form.php).
function verifierDossierDriveClient($data)
{
	header('Content-Type: application/json');
	$indices = array("client");
	if (!fieldCheck($data, $indices)) {
		echo json_encode(array('configure' => false, 'similaires' => array()));
		return;
	}
	$client = client::find($data['client'], $_SESSION['agence']);
	if (!$client->getId() || !class_exists('googleDriveClient')) {
		echo json_encode(array('configure' => false, 'similaires' => array()));
		return;
	}
	echo json_encode(googleDriveClient::verifierDossierClientSimilaire($client));
}

function addFacture($data)
{
	$indices = array("client", "bank");
	if (fieldCheck($data, $indices)) {

		if (buildFacture($data)->add() == 1) {
			// Ajout des lignes facture
			$id_facture = facture::getLastId();
			$facture = facture::find($id_facture, $_SESSION['agence']);
			if (isset($data["id_service"]) && !empty($data["id_service"])) {
				$cpt = 0;
				foreach ($data["id_service"] as $id_service) {
					$service = service::find($id_service);
					$item_facture = new item_facture();
					$item_facture->setFacture($facture);
					$item_facture->setService($service);
					$item_facture->setQte($data["qte"][$cpt]);
					$item_facture->setDiscount($data["rms"][$cpt]);
					$item_facture->setPrix($data["prix"][$cpt]);
					$item_facture->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
					$item_facture->setUnite($data["unite"][$cpt]);
					$item_facture->setTitre($data["item_facture_service_title"][$cpt]);
					$item_facture->setDescription($data["item_facture_service_description"][$cpt]);
					$item_facture->setOrdre($cpt);
					$item_facture->add();
					$cpt++;
				}

				// calcul et mise a jour total facture / generer numéro facture
				$facture->setTotalItems();
				$facture->generateNumero();
				$facture->edit();
				if (!isset($data['proforma'])) {
					$agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
					$agence->setNumeroIncrementFacture($agence->getNumeroIncrementFacture() + 1);
					$agence->edit();
				}
			}
			$dossierDriveChoisi = isset($data['drive_folder_override']) && $data['drive_folder_override'] !== '' ? $data['drive_folder_override'] : null;
			projectNotifier::launch($facture->getClient(), $facture->getDevis(), 'Facture #' . $facture->getNumero() . ' créée.', $dossierDriveChoisi);
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function addFactureAvoir($data)
{
	$indices = array("client", "bank");
	if (fieldCheck($data, $indices)) {
		if (buildFacture($data, true)->add() == 1) {
			// Ajout des lignes facture avoir
			$id_facture = facture::getLastId();
			$facture = facture::find($id_facture, $_SESSION['agence']);

			if (isset($data["id_service"]) && !empty($data["id_service"])) {
				$cpt = 0;
				foreach ($data["id_service"] as $id_service) {
					$service = service::find($id_service);
					$item_facture = new item_facture();
					$item_facture->setFacture($facture);
					$item_facture->setService($service);
					$item_facture->setQte($data["qte"][$cpt]);
					$item_facture->setDiscount($data["rms"][$cpt]);
					$item_facture->setPrix($data["prix"][$cpt]);
					$item_facture->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
					$item_facture->setUnite($data["unite"][$cpt]);
					$item_facture->setTitre($data["item_facture_service_title"][$cpt]);
					$item_facture->setDescription($data["item_facture_service_description"][$cpt]);
					$item_facture->setOrdre($cpt);
					$item_facture->add();
					$cpt++;
				}

				// calcul et mise a jour total facture / generer numéro facture
				$facture->setTotalItems();
				$facture->generateNumero();
				$facture->edit();

				// archiver la facture apres l'ajout de la avoir
				$id_facture_ref = $facture->getIdFacture();
				$facture = facture::find($id_facture_ref, $_SESSION['agence']);
				$facture->setArchived(1);
				$facture->edit();
			}
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function editFacture($data)
{
	$indices = array("id", "client", "bank");
	if (fieldCheck($data, $indices)) {
		if (buildFacture($data, $data['id'])->edit() == 1) {
			if (isset($data["id_service"]) && !empty($data["id_service"])) {
				$cpt = 0;
				foreach ($data["id_service"] as $id_service) {
					$service = service::find($id_service);
					if (isset($data["item_id"][$cpt]) && !empty($data["item_id"][$cpt])) {
						$item_facture = item_facture::find($data["item_id"][$cpt]);
						$item_facture->setService($service);
						$item_facture->setQte($data["qte"][$cpt]);
						$item_facture->setDiscount($data["rms"][$cpt]);
						$item_facture->setPrix($data["prix"][$cpt]);
						$item_facture->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
						$item_facture->setUnite($data["unite"][$cpt]);
						$item_facture->setTitre($data["item_facture_service_title"][$cpt]);
						$item_facture->setDescription($data["item_facture_service_description"][$cpt]);
						$item_facture->setOrdre($data["ordre"][$cpt]);
						$item_facture->edit();
					} else {
						$item_facture = new item_facture();
						$item_facture->setFacture(facture::find($data['id'], $_SESSION['agence']));
						$item_facture->setService($service);
						$item_facture->setQte($data["qte"][$cpt]);
						$item_facture->setDiscount($data["rms"][$cpt]);
						$item_facture->setPrix($data["prix"][$cpt]);
						$item_facture->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
						$item_facture->setUnite($data["unite"][$cpt]);
						$item_facture->setTitre($data["item_facture_service_title"][$cpt]);
						$item_facture->setDescription($data["item_facture_service_description"][$cpt]);
						$item_facture->setOrdre($cpt);
						$item_facture->add();
					}
					$cpt++;
				}

				$facture = facture::find($data['id'], $_SESSION['agence']);
				$facture->setTotalItems();
				if ($facture->getNumero() == '' && !$facture->isProforma()) $facture->generateNumero();
				$facture->edit();
			}
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function deleteFacture($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$facture = facture::find($id, $_SESSION['agence']);
		$last_invoice = facture::findLastInvoice($_SESSION['agence']);

		if ($facture->delete() == 1) {
		    // supression des relances lié a cette facture
		    $relances = relance::findByFacture($id);
		    foreach($relances as $relance){
		        $relance->delete();
		    }
		    
		    // Mise à jour numéro factures
			if ($last_invoice->getId() == $facture->getId() && !isset($data['proforma'])) {
				$agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
				$agence->setNumeroIncrementFacture($agence->getNumeroIncrementFacture() - 1);
				$agence->edit();
			}
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function archiveFacture($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$facture = facture::find($id, $_SESSION['agence']);
		$facture->setArchived(1);
		if ($facture->edit() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function retablirFacture($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$facture = facture::find($id, $_SESSION['agence']);
		$facture->setArchived(0);
		if ($facture->edit() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function removeItemFacture($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$item_facture = item_facture::find($id);
		if ($item_facture->delete() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function getRowFacture()
{
	$services = service::findAll($_SESSION['langue'], true);
	?>
	<tr>
		<td><input type="number" name="ordre[]" value="1" class="form-control"></td>
		<td>
			<select class="chosen-select service-select" name="id_service[]" style="width:500px;" required>
				<option value="" selected>Sélectionner</option>
				<?php foreach ($services as $service) : ?>
					<option data-title="<?= $service->getTitre() ?>" data-description="<?= $service->getDescription() ?>" value="<?php echo $service->getId() ?>"><?php echo $service->getTitre(); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="item_facture_service_title[]" value="">
			<input type="hidden" name="item_facture_service_description[]" value="">
		</td>
		<td>
			<input type="number" name="qte[]" value="1" class="form-control qte-input">
		</td>
		<td>
            <input type="number" step="any" name="rms[]" value="0" min="0" max="100" class="form-control discount-input">
        </td>
		<td>
			<input type="number" step="any" name="prix[]" value="<?php echo $services[0]->getPrix(); ?>" class="form-control price-input" style="width:100px;">
		</td>
		<td>
			<select class="chosen-select unite-input" name="unite[]" style="width:300px;" required>
				<option value="" selected>Sélectionner</option>
				<?php
				$unities = getUnities()[isset($facture) ? $facture->getLangue() : 'fr'];
				foreach ($unities as $key =>  $value) : ?>
					<option value="<?php echo $key ?>"><?= $value ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input type="number" step="any" name="soustotal[]" value="<?php echo $services[0]->getPrix(); ?>" class="form-control total-input" style="width:100px;">
		</td>
		<td class="add-remove text-right">
			<input type="hidden" name="item_id[]" value="0" class="id-item-input">
			<i class="fas fa-magic ask-ai-item-row" data-toggle="tooltip" data-placement="top" data-original-title="Assistant IA"></i>
			<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
			<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer cette ligne"></i>
		</td>
	</tr>
	<script>
		$(".chosen-select").select2();
		$(document).on("change", ".service-select", function() {
			var select = $(this);
			var id = select.val();
			var title = select.find(':selected').attr('data-title');
			var description = select.find(':selected').attr('data-description');
			var qte = select.parent().parent().find(".qte-input").val();
			var discount = select.parent().parent().find(".discount-input").val();
			var id_item = select.parent().parent().find(".id-item-input").val();
			var order = 'id=' + id + '&id_item=' + id_item;
			$.post("components/com_facture/controleurs/router.php?task=getServicePrice", order, function(theResponse) {
				var total = parseFloat(theResponse) * parseInt(qte)
				select.parent().parent().find(".price-input").val(theResponse);
				select.parent().parent().find(".total-input").val(total);
				select.parent().parent().find('input[name="item_facture_service_title[]"]').val(title)
				select.parent().parent().find('input[name="item_facture_service_description[]"]').val(description)
			});
			$.post("components/com_facture/controleurs/router.php?task=getServiceUnite", order, function(theResponse2) {
				select.parent().parent().find(".unite-input").val(theResponse2);
			});
		})

		$(document).on("keyup change", ".qte-input", function() {
			var input = $(this);
			var discount = input.parent().parent().find(".discount-input").val();
			var qte = input.val();
			var prix = input.parent().parent().find(".price-input").val();
			var subtotal = prix * qte
			var discountTotal = (parseFloat(discount) * subtotal)  / 100;
			var total = subtotal - discountTotal;
			input.parent().parent().find(".total-input").val(total);
		})
		
		$(document).on("keyup change", ".discount-input", function() {
			var input = $(this);
			var discount = input.val();
			var qte = input.parent().parent().find(".qte-input").val();
			var prix = input.parent().parent().find(".price-input").val();
			var subtotal = prix * parseInt(qte)
			var discountTotal = (parseFloat(discount) * subtotal)  / 100;
			var total = subtotal - discountTotal;
			input.parent().parent().find(".total-input").val(total);
		})

		$(document).on("keyup", ".price-input", function() {
		    var input = $(this);
			var discount = input.parent().parent().find(".discount-input").val();
			var qte = input.parent().parent().find(".qte-input").val();
			var prix = input.val();
			var subtotal = discount * prix
			var discountTotal = (parseFloat(discount) * subtotal)  / 100;
			var total = subtotal - discountTotal;
			input.parent().parent().find(".total-input").val(total);
		})
	</script>
	<?php
}

function customItemFacture($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$item_facture = item_facture::find($data['id']);
	?>
		<form method="post" action="components/com_facture/controleurs/router.php?task=editItemFacture" id="customForm" enctype="multipart/form-data">
			<div class="msgbox"></div>
			<div class="form-group">
				<label>Titre <span class="text-danger">*</span></label>
				<input class="form-control" type="text" name="titre" value="<?php echo $item_facture->getTitre(); ?>">
			</div>
			<div class="form-group">
				<label>Description <span class="text-danger">*</span></label>
				<textarea class="form-control" name="description" id="description"><?php echo $item_facture->getDescription(); ?></textarea>
				<script type="text/javascript">
					if (CKEDITOR.instances.description) {
						CKEDITOR.instances.description.destroy(true);
					}
					CKEDITOR.replace('description', {
						//allowedContent: true,
						allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href];',
						filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
					});
				</script>
			</div>
			<div class="form-group d-none">
				<label>Unité <span class="text-danger">*</span></label>
				<input class="form-control" type="text" name="unite" value="<?php echo $item_facture->getUnite(); ?>">
			</div>
			<label class="row form-group toggle-switch">
				<span class="col-12 toggle-switch-content ml-0">
					<span class="d-block text-danger">Voulez-vous également mettre à jour le service d'origine ?</span>
				</span>
				<span class="col-12">
					<input type="checkbox" name="original_service" class="toggle-switch-input" value="1">
					<span class="toggle-switch-label mr-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
			<input type="hidden" name="id" value="<?php echo $item_facture->getId(); ?>">
			<input type="hidden" name="id_service" value="<?php echo $item_facture->getService()->getId(); ?>">
			<div class="submit-section">
				<button class="btn btn-primary submit-btn">Mettre à jour</button>
			</div>
		</form>
		<script>
			$(function() {

				// envoi du formulaire en ajax
				$('form#customForm').ajaxForm({
					beforeSubmit: function() {
						$("#customForm .loading").css('display', 'inline-block');
					},
					success: function(theResponse) {
						$("#customForm .loading").fadeOut();
						$("html, body").animate({
							scrollTop: 0
						}, "slow");

						var msgsucces = "Facture ajoutée avec succès";
						if ($(".submit").attr("name") === "edit") {
							msgsucces = "facture modifiée avec succès";
						}
						if (parseInt(theResponse) === 1) {
							$('#customForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

							setTimeout(function() {
								$("#dialog-custom").modal('hide');
								location.reload()
							}, 1500)

						} else if (parseInt(theResponse) === 0) {
							$('#customForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
						} else {
							$('#customForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
						}
					}
				});
			})
		</script>
<?php
	}
}

function editItemFacture($data)
{
	$indices = array("id", "titre", "description", "unite");
	if (fieldCheck($data, $indices)) {
		$item_facture = item_facture::find($data['id']);
		$item_facture->setTitre($data['titre']);
		$item_facture->setDescription($data['description']);
		$item_facture->setUnite($data['unite']);
		if ($item_facture->edit() == 1) {
			if (isset($data['original_service']) && $data['original_service'] == '1') {
				$service = service::find($data['id_service']);
				$service->setTitre($data['titre']);
				$service->setDescription($data['description']);
				$service->setUnite($data['unite']);
				$service->edit();
			}
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function buildFacture($data, $id = null, $factureavoir = false)
{
	$facture = new facture();

	if ($id && !$factureavoir) {
		$facture = facture::find($id, $_SESSION['agence']);
		$facture->setUserEdited($_SESSION['user']);
	} else {
		$facture->setUserAdded($_SESSION['user']);
	}

	$banqueFactureChoisie = bank::find($data['bank']);
	// Comptes personnels (Hamid/Zakaria - "PERSO" dans la raison sociale) : toujours proforma -
	// imposé ici plutôt qu'uniquement côté JS (assets/js/ia-bank-filter.js), même règle que côté
	// devis (com_devis/controleurs/devis/controleur.php:buildDevis).
	$estBanquePersonnelleFacture = $banqueFactureChoisie->getId() && stripos($banqueFactureChoisie->getRaisonSociale(), 'PERSO') !== false;

	$facture->setNumero($data['numero']);
	$facture->setClient(client::find($data['client'], $_SESSION['agence']));
	$facture->setBank($banqueFactureChoisie);
	$facture->setDateFacture(dateBD($data['date_facture']));
	$facture->setStatu($data['statu']);
	$facture->setDevise($data['devise']);
	$facture->setDiscount($data['discount']);
	$facture->setDiscountVal($data['discount_val']);
	$facture->setProforma($estBanquePersonnelleFacture ? 1 : (isset($data['proforma']) ? 1 : 0));
	$facture->setShowSignature(isset($data['show_signature']) ? 1 : 0);
	$facture->setLangue($data['langue']);
	$facture->setConditionPaiment($data['condition_paiment']);
	$facture->setRemarque($data['remarque']);
	$facture->setRemarqueMoi($data['remarque_moi']);
	$facture->setAdditionalInformation($data['additional_information']);
	$facture->setDateDebut(dateBD($data['date_debut']));
	$facture->setDateFin(dateBD($data['date_fin']));
	$facture->setDateAdd(date("Y-m-d H:i:s"));
	$facture->setLastEdit(date("Y-m-d H:i:s"));
	$facture->setAvoir(isset($data['avoir']) ? 1 : 0);
	$facture->setArchived(isset($data['archived']) ? 1 : 0);
	$facture->setIdFacture(isset($data['id_facture']) ? $data['id_facture'] : NULL);

	// print_r($facture);die;
	return $facture;
}

/* Le lancement de projet (dossier Drive + ticket Trello + Slack #familly +
   email support) est désormais géré par la classe partagée projectNotifier
   (components/com_facture/classes/projectNotifier.php), appelée depuis ici,
   depuis le paiement, et depuis le changement de statut devis — un seul
   ticket Trello par client, jamais de doublon. */

function pdfFacture($data)
{
	global $db;
	if (isset($data["id"]) && !empty($data["id"])) {

		require '../../../vendor/autoload.php';
		require '../../../includes/traduction.php';

		$facture = facture::find($data["id"], $_SESSION['agence']);
		if($facture->getReste() <= 0)
			$facture->pdfFacture("show");
		else
			echo '<h2 align="center" style="margin-top:100px;">Vous devez régler cette facture pour pouvoir la télécharger<h2>';
	}
}

function pdfFactures($data)
{
	if (isset($data["from"]) && !empty($data["from"] && isset($data["to"]) && !empty($data["to"]))) {

		require '../../../vendor/autoload.php';
		require '../../../includes/traduction.php';

		$from = dateBD($data['from']);
		$to = dateBD($data['to']);
		$devise = false;
		if(isset($data['devise']) && !empty($data['devise'])){
			$devise = $data['devise'];
		}
		$factures = facture::findAll(false, $from, $to, true, false, false, $_SESSION['agence'],$devise);
		if(isset($data['status']) && !empty($data['status'])){
			$factures = array_filter($factures, function($facture) use ($data){
				if($data['status'] == 'litige'){
					return $facture->getStatu() == 3;
				}else{
					if($data['status'] == 'unpaid'){
						return $facture->getTotal() == $facture->getReste() && $facture->getStatu() != 3;
					}else if($data['status'] == 'partial'){
						return $facture->getTotal() > $facture->getReste() && $facture->getReste() > 0 && $facture->getStatu() != 3;
					}else if($data['status'] == 'paid'){
						return $facture->getReste() <= 0 && $facture->getStatu() != 3;
					}
				}
			});
		}
		facture::pdfFactures($factures);
	}
}

function sendViaMailFacture($data)
{


	global $db;
	if (isset($data["id"]) && !empty($data["id"])) {

		require '../../../vendor/autoload.php';
		require	 '../../../includes/traduction.php';

		$facture = facture::find($data["id"], $_SESSION['agence']);
		$file_name = $facture->pdfFacture("download");

		$facture->sendViaMailFacture($file_name);
	}
}

function exportFacture($data)
{
	$indices = array("from", "to");
	if (fieldCheck($data, $indices)) {
		$from = dateBD($data['from']);
		$to = dateBD($data['to']);

		$factures = facture::findAll(false, $from, $to, true, false, false, $_SESSION['agence']);
		if(isset($data['status']) && !empty($data['status'])){
			$factures = array_filter($factures, function($facture) use ($data){
				if($data['status'] == 'litige'){
					return $facture->getStatu() == 3;
				}else{
					if($data['status'] == 'unpaid'){
						return $facture->getTotal() == $facture->getReste() && $facture->getStatu() != 3;
					}else if($data['status'] == 'partial'){
						return $facture->getTotal() > $facture->getReste() && $facture->getReste() > 0 && $facture->getStatu() != 3;
					}else if($data['status'] == 'paid'){
						return $facture->getReste() <= 0 && $facture->getStatu() != 3;
					}
				}
			});
		}

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
		$styles_row = array(
			'font' => 'Arial',
			'font-size' => 11,
			'halign' => 'center',
			'border' => 'left,right,top,bottom',
		);
		$styles_rows_payee = array(
			'font' => 'Arial',
			'font-size' => 11,
			'halign' => 'center',
			'fill' => '#CCFFCC',
			'border' => 'left,right,top,bottom',
		);

		include_once("../classes/xlsxwriter.class.php");

		$filename = "factures.xlsx";
		header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$writer = new XLSXWriter();


		$header_format = array(
			'font-style' => 'bold',
			'align' => 'center',
			'font-size' => 13, // Set the font size to 14
			'padding' => 10,
		);

		// Set up a header title
		$currentAgence = agence::find($_SESSION['agence'], $_SESSION['langue']);
		$header_title = $currentAgence->getNom();

		// Add the header title manually to cell C1
		$writer->writeSheetHeader('Sheet1', []);

		// Write the header title to cell C1
		$writer->writeSheetRow('Sheet1', ['', '', $header_title], $header_format);

		$writer->setAuthor('Hello World');

		// Header row
		$header = array('Numero' => 'Numéro', 'Client' => 'Client', 'Date' => 'Date', 'Montant' => 'Montant',  'Reste' => 'Reste', 'Statut' => 'Statut');
		$writer->writeSheetRow('Sheet1', $header, $styles_header);

		foreach ($factures as $facture) {

			$payments = payment::findAll($facture->getId());
			$nbrPayment = sizeof($payments);
			if ($facture->getTotal() == $facture->getReste())
				$statu = 'Impayée(' . $nbrPayment . ')';
			elseif ($facture->getTotal() > $facture->getReste() && $facture->getReste() > 0)
				$statu = 'Payée partialement(' . $nbrPayment . ')';
			elseif ($facture->getReste() <= 0)
				$statu = 'Payée(' . $nbrPayment . ')';

			$row = array(
				'Numero' => $facture->isProforma() ? $facture->getSecNumero() : $facture->getNumero(), // Show the year in the first row only
				'Client' => $facture->getClient()->getRaisonSocial(), // Show the client name in the first row only
				'Date' => normaldate($facture->getDateFacture()),
				'Montant' => number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(),
				'Reste' => number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(), // Show the number of paid invoices in the first row only
				'Statut' => $statu, // Show the number of unpaid invoices in the first row only
			);

			// Apply the appropriate style based on the row index
			$row_style = $facture->getReste() <= 0 ? $styles_rows_payee : $styles_row;

			$writer->writeSheetRow('Sheet1', $row, $row_style);
		}

		$writer->writeToStdOut();
	}
}

function exportFactureTest($data)
{
	$indices = array("from", "to");
	if (fieldCheck($data, $indices)) {
		$from = dateBD($data['from']);
		$to = dateBD($data['to']);
		$devise = false;
		if(isset($data['devise']) && !empty($data['devise'])){
			$devise = $data['devise'];
		}
		$factures = facture::findAll(false, $from, $to, true, false, false, $_SESSION['agence'],$devise);
        if(isset($data['status']) && !empty($data['status'])){
			$factures = array_filter($factures, function($facture) use ($data){
				if($data['status'] == 'litige'){
					return $facture->getStatu() == 3;
				}else{
					if($data['status'] == 'unpaid'){
						return $facture->getTotal() == $facture->getReste() && $facture->getStatu() != 3;
					}else if($data['status'] == 'partial'){
						return $facture->getTotal() > $facture->getReste() && $facture->getReste() > 0 && $facture->getStatu() != 3;
					}else if($data['status'] == 'paid'){
						return $facture->getReste() <= 0 && $facture->getStatu() != 3;
					}
				}
			});
		}

		// Create new Spreadsheet object
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(15);

		$header = [
			"Date",
			"Ste",
			"Facture",
			"Devise",
			"Montant total",
			"Payer",
			"Reste"
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
		$total = 0;
		$total_paid = 0;
		$total_unpaid = 0;
		foreach ($factures as $key => $facture) {
			$row = [
				normaldate($facture->getDateFacture()),
				$facture->getClient()->getRaisonSocial(),
				"#".$facture->getNumero(),
				$facture->getDevise(),
				number_format($facture->getTotal(), 2, ',', ' '),
				number_format($facture->getTotal() - $facture->getReste(), 2, ',', ' '),
				number_format($facture->getReste(), 2, ',', ' ')
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
			$total += $facture->getTotal();
			$total_paid += ($facture->getTotal() - $facture->getReste());
			$total_unpaid += $facture->getReste();
			$row_index++;
		}

		$footer = [
			"",
			"",
			"",
			"Total",
			number_format($total, 2, ',', ' '),
			number_format($total_paid, 2, ',', ' '),
			number_format($total_unpaid, 2, ',', ' ')
		];

		foreach ($footer as $index => $value) {
			$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
			$spreadsheet->getActiveSheet()->setCellValue($columnLetter . $row_index, $value); // Set footer in row 1
			$spreadsheet->getActiveSheet()->getStyle($columnLetter . $row_index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
			$spreadsheet->getActiveSheet()->getStyle($columnLetter . $row_index)->getFill()->getStartColor()->setARGB('fedfde');
			$spreadsheet->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(30);
			$spreadsheet->getActiveSheet()->getRowDimension($row_index)->setRowHeight(40);
			$spreadsheet->getActiveSheet()->getStyle(0)->getAlignment()
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
			$spreadsheet->getActiveSheet()->getStyle(0)->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Center text horizontally;
		}
		// Clear any previous output
		ob_end_clean();
		ob_start();

		// Output to browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="factures.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}
}
//API
function findAllByClientApi($data)
{
	$factures = facture::findAllByClientApi($data['client'], false, false, true, false, false);
	// Convert array to UTF-8
    array_walk_recursive($factures, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
	echo json_encode($factures);
}

function getPaymentsByClientApi($data)
{
	$payments = facture::getPaymentsByClientApi($data['client']);
	array_walk_recursive($payments, function (&$item) {
		if (is_string($item)) {
			$item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
		}
	});
	echo json_encode($payments);
}

function pdfFactureApi($data)
{
	if (isset($data["id"]) && !empty($data["id"])) {

		require '../../../vendor/autoload.php';
		require '../../../includes/traduction.php';

		echo facture::pdfFactureApi($data["id"],	"download");
	}
}

function sendEmailsForeachFacture()
{
	global $siteURL, $db;
	$factures = facture::findAllWillFinish();
	$config = new config($db);
	foreach ($factures as $facture) {
		$facture_to_update = new facture();
		$facture_to_update->setId($facture['id']);
		$facture_to_update->setCountSending($facture['count_sending']);
		if (intval($facture['count_sending']) < 3) {
			$agence = agence::find($facture['id_agence']);
			// Define the two dates
			$today = new DateTime();
			$startDate = new DateTime($facture['date_debut']);
			$endDate = new DateTime($facture['date_fin']);

			// Calculate the difference
			$interval1 = $startDate->diff($endDate);
			$interval2 = $today->diff($endDate);

			// Get the difference in days
			$days = $interval1->days;
			$rest_days = $interval2->days;
			$status = "";

			if ($facture['total'] == $facture['rest']) {
				$status = "Impayée";
			} elseif ($facture['total'] > $facture['rest'] && $facture['rest'] > 0) {
				$status = "Payée partialement";
			} elseif ($facture['total'] <= 0) {
				$status = "Payée";
			}
			$mail = new PHPMailer(true);
			try {                                           //Send using SMTP
				$mail->Host       = 'helloworldlabel.ae';                     //Set the SMTP server to send through                                 //Enable SMTP authentication
				$mail->Username   = "contact@helloworldlabel.ae";                     //SMTP username           //Enable implicit TLS encryption                                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

				//Recipients
				$mail->setFrom($agence->getEmail(), $agence->getNom());
				$mail->addAddress($facture['email'], $facture['nom']);     //Add a recipient

				//Content
				$mail->isHTML(true);                                  //Set email format to HTML
				$mail->Subject = ($agence->getId() == 2 ? 'Project Progress Update' : 'Etat d\'avancement de votre projet ');
				$mail->AltBody = ($agence->getId() == 2 ? 'Project Progress Update' : 'Etat d\'avancement de votre projet ');
				$messageFr = 'Bonjour ' . $facture['nom'] . ',<br/></br>

                                Nous espérons que vous allez bien.<br/>
                                Nous souhaitons vous informer que votre projet touche bientôt à sa fin. Il reste actuellement ' . $rest_days . ' jour(s) avant la date de livraison prévue. Nous tenons à vous remercier pour votre confiance et votre collaboration tout au long de ce processus.<br/><br/>
                                
                                Afin de préparer la clôture du projet, une facture finale vous sera émise.<br/>
                                Nous vous prions de bien vouloir préparer le paiement afin d\'assurer une transaction fluide et ponctuelle. Votre promptitude à ce sujet est grandement appréciée.<br/><br/>
                                
                                Si vous avez des questions ou des préoccupations concernant la fin du projet ou le processus de paiement, n\'hésitez pas à nous contacter. Notre équipe est à votre disposition pour vous assister et assurer une clôture harmonieuse du projet.<br/><br/>
                                
                                Nous vous remercions encore pour votre collaboration et votre confiance.<br/><br/>
                                
                                Cordialement.';
				$messageEn = 'Hello ' . $facture['nom'] . ',
    
                                        We hope this message finds you well.<br/>
                                        
                                        We would like to inform you that your project is nearing completion. There are currently ' . $rest_days . ' day(s) left until the scheduled delivery date. We sincerely appreciate your trust and collaboration throughout this process.<br/><br/>
                                        
                                        To facilitate the project closure, a final invoice will be issued to you. <br/>
                                        Kindly prepare the payment to ensure a smooth and timely transaction. Your prompt attention to this matter would be greatly appreciated.<br/><br/>
                                        
                                        If you have any questions or concerns regarding the project completion or the payment process, please feel free to contact us. Our team is here to assist you and ensure a seamless project closure.<br/><br/>
                                        
                                        Once again, thank you for your cooperation and trust.<br/><br/>
                                        
                                        Best regards.';

				$mailBody = '<html><body>
                                		<table border="0" width="100%">
                                		<tr>
                                		<td bgcolor="#F6F6F6" align="center">
                                
                                		<table border="0" cellpadding="15" cellspacing="0" width="640">
                                			<tr>
                                				<td align="center"><a href="' . $siteURL . '"><img src="' . $siteURL . 'images/agences/' . $agence->getLogo() . '" alt="' . $agence->getNom() . '" height="100" /></a></td>
                                			</tr>
                                			<tr bgcolor="#FFFFFF">
                                				<td align="center"><h1 style="font-weight:normal; margin-bottom:5px;">' . ($agence->getId() == 2 ? 'Project Progress Update' : 'Etat d\'avancement de votre projet ') . '</h1></td>
                                			</tr>
                                			<tr bgcolor="#FFFFFF">
                                				<td>
                                				' . ($agence->getId() == 2 ? $messageEn : $messageFr) . '
                                				</td>
                                			</tr>
                                			<tr>
                                				<td align="center"><p><font size="-3" color="#666666">' . $agence->getNom() . '<br/>
                                		E-mail : ' . $agence->getEmail() . '<br/>
                                		Téléphone : ' . $agence->getTel() . '<br/>
                                        Adresse : ' . $agence->getAdresse() . '<br/>
                                			</tr>
                                		</table>
                                		</td>
                                		</tr>
                                		</table>
                                		</body>
                                		</html>';
				$mail->Body = $mailBody;
				if ($mail->send()) {
					$facture_to_update->increaseCountSending();
					return 'The emails have been sent';
				} else {
					return 'The emails have not been sent';
				}
			} catch (Exception $e) {
				return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
		}
	}
	return;
}