<?php

if (isset($task) && !empty($task)) {
	switch ($task) {
		case 'addDevis':
			addDevis($_POST);
			break;
		case 'editDevis':
			editDevis($_POST);
			break;
		case 'deleteDevis':
			deleteDevis($_POST);
			break;
		case "getRowDevis":
			getRowDevis();
			break;
		case 'removeItemDevis':
			removeItemDevis($_POST);
			break;
		case 'customItemDevis':
			customItemDevis($_POST);
			break;
		case 'editItemDevis':
			editItemDevis($_POST);
			break;
		case 'getServicePrice':
			getServicePrice($_POST);
			break;
		case 'pdfDevis':
			pdfDevis($_GET);
			break;
		case 'createInvoice':
			createInvoice($_POST);
			break;
	}
}

function createInvoice($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$devis = devis::find($data['id'], $_SESSION['agence']);

		$facture = new facture();
		$facture->setClient($devis->getClient());
		$facture->setDateFacture(date("Y-m-d"));
		$facture->setStatu($devis->getStatu());
		$facture->setTotal($devis->getTotal());
		$facture->setDevise($devis->getDevise());
		$facture->setDiscount($devis->getDiscount());
		$facture->setDiscountVal($devis->getDiscountVal());
		$facture->setDateAdd(date("Y-m-d H:i:s"));
		$facture->setLastEdit(date("Y-m-d H:i:s"));


		if ($facture->add() == 1) {
			// Ajout des lignes facture
			$id_facture = facture::getLastId();
			$facture = facture::find($id_facture,$_SESSION['agence']);
			$items = $devis->getItems();
			foreach ($items as $item) {
				$item_facture = new item_facture();
				$item_facture->setFacture($facture);
				$item_facture->setService($item->getService());
				$item_facture->setQte($item->getQte());
				$item_facture->setPrix($item->getPrix());
				$item_facture->setTotal($item->getTotal());
				$item_facture->setUnite($item->getUnite());
				$item_facture->setTitre($item->getTitre());
				$item_facture->setDescription($item->getDescription());
				$item_facture->setOrdre($item->getOrdre());
				$item_facture->add();
			}

			// calcul et mise a jour total facture / generer numéro facture
			//$facture->setTotalItems();
			$facture->generateNumero();
			$facture->edit();

			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}
function getServicePrice($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$service = service::find($data["id"]);
		$price = $service->getPrix();

		if (isset($data['id_item']) && !empty($data['id_item'])) {
			$item_devis = item_devis::find($data['id_item']);
			$price = $item_devis->getPrix();
		}

		echo $price;
	}
}
function addDevis($data)
{
	$indices = array("client");
	if (fieldCheck($data, $indices)) {
		if (buildDevis($data)->add() == 1) {
			// Ajout des lignes devis
			$id_devis = devis::getLastId();
			$devis = devis::find($id_devis, $_SESSION['agence']);

			if (isset($data["id_service"]) && !empty($data["id_service"])) {
				$cpt = 0;
				foreach ($data["id_service"] as $id_service) {
					$service = service::find($id_service);
					$item_devis = new item_devis();
					$item_devis->setDevis($devis);
					$item_devis->setService($service);
					$item_devis->setQte($data["qte"][$cpt]);
					$item_devis->setPrix($data["prix"][$cpt]);
					$item_devis->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
					$item_devis->setUnite($service->getUnite());
					$item_devis->setTitre($service->getTitre());
					$item_devis->setDescription($service->getDescription());
					$item_devis->setOrdre($cpt);
					$item_devis->add();
					$cpt++;
				}

				// calcul et mise a jour total devis / generer numéro devis
				$devis->setTotalItems();
				$devis->generateNumero();
				$devis->edit();
			}
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function editDevis($data)
{
	$indices = array("id", "client");
	if (fieldCheck($data, $indices)) {
		if (buildDevis($data, $data['id'])->edit() == 1) {
			if (isset($data["id_service"]) && !empty($data["id_service"])) {
				$cpt = 0;
				foreach ($data["id_service"] as $id_service) {
					$service = service::find($id_service);
					if (isset($data["item_id"][$cpt]) && !empty($data["item_id"][$cpt])) {
						$item_devis = item_devis::find($data["item_id"][$cpt]);
						$item_devis->setService($service);
						$item_devis->setQte($data["qte"][$cpt]);
						$item_devis->setPrix($data["prix"][$cpt]);
						$item_devis->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
						$item_devis->setOrdre($data["ordre"][$cpt]);
						$item_devis->edit();
					} else {
						$item_devis = new item_devis();
						$item_devis->setDevis(devis::find($data['id']));
						$item_devis->setService($service);
						$item_devis->setQte($data["qte"][$cpt]);
						$item_devis->setPrix($data["prix"][$cpt]);
						$item_devis->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
						$item_devis->setUnite($service->getUnite());
						$item_devis->setTitre($service->getTitre());
						$item_devis->setDescription($service->getDescription());
						$item_devis->setOrdre($cpt);
						$item_devis->add();
					}
					$cpt++;
				}

				$devis = devis::find($data['id'], $_SESSION['agence']);
				$devis->setTotalItems();
				$devis->edit();
			}
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function deleteDevis($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$devis = devis::find($id, $_SESSION['agence']);
		if ($devis->delete() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function removeItemDevis($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$id = $data["id"];
		$item_devis = item_devis::find($id);
		if ($item_devis->delete() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

function getRowDevis()
{
	$services = service::findAll($_SESSION['langue'], true);
?>
	<tr>
		<td></td>
		<td>
			<select class="chosen-select service-select" name="id_service[]">
				<?php foreach ($services as $service) : ?>
					<option value="<?php echo $service->getId() ?>"><?php echo $service->getTitre(); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<input type="number" name="qte[]" value="1" class="form-control qte-input">
		</td>
		<td>
			<input type="number" step="any" name="prix[]" value="<?php echo $services[0]->getPrix(); ?>" class="form-control price-input">
		</td>
		<td>
			<input type="number" step="any" name="soustotal[]" value="<?php echo $services[0]->getPrix(); ?>" class="form-control total-input">
		</td>
		<td class="add-remove text-right">
			<input type="hidden" name="item_id[]" value="0" class="id-item-input">
			<i class="fas fa-plus-circle add-row" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
			<i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer cette ligne"></i>
		</td>
	</tr>
	<?php
}

function customItemDevis($data)
{
	$indices = array("id");
	if (fieldCheck($data, $indices)) {
		$item_devis = item_devis::find($data['id']);
	?>
		<form method="post" action="components/com_devis/controleurs/router.php?task=editItemDevis" id="customForm" enctype="multipart/form-data">
			<div class="msgbox"></div>
			<div class="form-group">
				<label>Titre <span class="text-danger">*</span></label>
				<input class="form-control" type="text" name="titre" value="<?php echo $item_devis->getTitre(); ?>">
			</div>
			<div class="form-group">
				<label>Description <span class="text-danger">*</span></label>
				<textarea class="form-control" name="description" id="description"><?php echo $item_devis->getDescription(); ?></textarea>
				<script type="text/javascript">
					CKEDITOR.replace('description', {
						//allowedContent: true,
						allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href];',
						filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
					});
				</script>
			</div>
			<div class="form-group">
				<label>Unité <span class="text-danger">*</span></label>
				<input class="form-control" type="text" name="unite" value="<?php echo $item_devis->getUnite(); ?>">
			</div>
			<input type="hidden" name="id" value="<?php echo $item_devis->getId(); ?>">
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

						var msgsucces = "Devis ajoutée avec succès";
						if ($(".submit").attr("name") === "edit") {
							msgsucces = "devis modifiée avec succès";
						}
						if (parseInt(theResponse) === 1) {
							$('#customForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

							setTimeout(function() {
								$("#dialog-custom").modal('hide');
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

function editItemDevis($data)
{
	$indices = array("id", "titre", "description", "unite");
	if (fieldCheck($data, $indices)) {
		$item_devis = item_devis::find($data['id']);
		$item_devis->setTitre($data['titre']);
		$item_devis->setDescription($data['description']);
		$item_devis->setUnite($data['unite']);
		if ($item_devis->edit() == 1)
			echo "1";
		else
			echo "2";
	} else
		echo "0";
}

function buildDevis($data, $id = null)
{
	$devis = new devis();

	if ($id) {
		$devis = devis::find($id, $_SESSION['agence']);
	}

	$devis->setNumero($data['numero']);
	$devis->setClient(client::find($data['client'],$_SESSION['agence']));
	$devis->setDateDevis(dateBD($data['date_devis']));
	$devis->setStatu($data['statu']);
	$devis->setDevise($data['devise']);
	$devis->setDiscount($data['discount']);
	$devis->setDiscountVal($data['discount_val']);
	$devis->setConditionPaiment($data['condition_paiment']);
	$devis->setRemarque($data['remarque']);
	$devis->setDateAdd(date("Y-m-d H:i:s"));
	$devis->setLastEdit(date("Y-m-d H:i:s"));

	return $devis;
}

function pdfDevis($data)
{
	global $db;
	if (isset($data["id"]) && !empty($data["id"])) {
		$devis = devis::find($data["id"], $_SESSION['agence']);
		$items = $devis->getItems();
		$client = $devis->getClient();
		$config = new config($db);
		$invoiceFor = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();

		require_once '../../../vendor/autoload.php';
		$mpdf = new \Mpdf\Mpdf();

		$htmlInvoice = '<html>
<head>
<style>
body {
	font-family: montserrat;
	font-size: 10pt;
}
p {	margin: 0pt; }
table.items {
}
td { vertical-align: top; }
.items td {
	border-left: 0.1mm solid #FFF;
	border-right: 0.1mm solid #FFF;
	border-bottom: 0.1mm solid #CCC;
}
table thead td { background-color: #EEEEEE;
	text-align: center;
	border-left: 0.1mm solid #FFF;
	border-right: 0.1mm solid #FFF;
}
.items td.blanktotal {
	background-color: #EEEEEE;
	border: 0.1mm solid #FFF;
	background-color: #FFFFFF;
	border: 0mm none #000000;
}
.items td.totals {
	text-align: right;
	border-bottom: 0.1mm solid #CCC;
}
.items td.cost {
	text-align: "." center;
}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
	<td><img src="../../../images/config/' . $config->getLogo() . '" width="100"></td>
	<td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>HW LABEL, ' . $config->getAdresse() . '</strong><br>
	<p style="font-size: 8pt;"><strong>t:</strong> ' . $config->getTel() . '  |  <strong>e:</strong> ' . $config->getEmail() . ' | <strong>w:</strong> www.helloworld-agency.com</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">
<p style="font-size:8pt;"><strong>IF</strong> 26162283 | <strong>TP</strong> 45101756 | <strong>RC</strong> 91301 | <strong>ICE</strong> 002142777000089</p>
<div style="margin-top:5pt;">Page {PAGENO} sur {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">Devis pour<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:#08c3df">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Total devis</td></tr>
<tr><td style="border-top:#08c3df solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">Date devis</td></tr>
<tr><td style="border-top:#08c3df solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($devis->getDateDevis()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° devis</td></tr>
<tr><td style="border-top:#08c3df solid 0.5pt;"><strong style="font-size: 12pt;">' . $devis->getNumero() . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">Prix HT</td>
<td width="20%">Quantité</td>
<td width="20%" align="right">Total HT</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
		$soustotal = 0;
		foreach ($items as $item) {
			$soustotal += $item->getTotal();
			$htmlInvoice .= '<tr>
<td><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
<td align="center" style="vertical-align:middle;">' . number_format($item->getPrix(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
<td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . $item->getUnite() . '</td>
<td align="right" style="vertical-align:middle;" class="cost">' . number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr>';
		}

		$htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">Sous-total HT</td>
<td class="totals cost">' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr>';

		if ($devis->getDiscount() != '') {
			$discoutSign = $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%';
			// test réduction
			if ($devis->getDiscount() == 'percentage') {
				$soustotal = $soustotal - ($soustotal * $devis->getDiscountVal / 100);
			} elseif ($devis->getDiscount() == 'amount') {
				$soustotal = $soustotal - $devis->getDiscountVal;
			}

			$htmlInvoice .= '<tr>
<td class="totals">Réduction</td>
<td class="totals cost">- ' . $devis->getDiscountVal() . $discoutSign . '</td>
</tr>';
		}

		$htmlInvoice .= '<tr>
<td class="totals">TVA</td>
<td class="totals cost">' . number_format(($soustotal * 0.2), 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr>
<tr style="background:#08c3df;">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #08c3df;"><b>TOTAL TTC</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
		if ($devis->getRemarque() != '') {
			$htmlInvoice .= '<strong>Remarque: </strong>' . $devis->getRemarque();
		};
		$htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:#08c3df;">Merci d\'avoir choisi Hello World !!</h3>
<p style="font-size:8pt;"><strong>Conditions: </strong>La validation de la proposition financière implique l\'acceptation complète et entière des Conditions Générales de vente présentées sur le site : <a href="https://www.helloworld-agency.com/conditions-generales-de-ventes/">https://www.helloworld-agency.com/conditions-generales-de-ventes/</a></p>
<p style="font-size:8pt;"><strong>Conditions de paiement: </strong>' . $devis->getConditionPaiment() . '</p>
</div>
</body>
</html>';

		$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];

		$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];

		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 20,
			'margin_right' => 15,
			'margin_top' => 48,
			'margin_bottom' => 25,
			'margin_header' => 10,
			'margin_footer' => 10,

			'fontDir' => array_merge($fontDirs, [
				'../../../fonts/',
			]),
			'fontdata' => $fontData + [
				'montserrat' => [
					'R' => 'Montserrat-Regular.ttf',
					'B' => 'Montserrat-Bold.ttf',
				]
			],
			'default_font' => 'montserrat'
		]);

		$mpdf->SetProtection(array('print', 'copy'));
		$mpdf->SetTitle("Devis #" . $devis->getNumero());
		$mpdf->SetAuthor("Hello World");
		$mpdf->SetWatermarkText("");
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.05;
		$mpdf->SetDisplayMode('fullpage');

		$mpdf->WriteHTML($htmlInvoice);

		$mpdf->Output();
	}
}
