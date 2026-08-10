<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'paymentForm':
            paymentForm($_POST);
            break;
        case 'editPayment':
            editPayment($_POST);
            break;
        case 'deletePayment':
            deletePayment($_POST);
            break;
        case "addPayment":
            addPayment($_POST);
            break;
        case "removePhotoPayment":
            removePhotoPayment($_POST);
            break;
        case 'pdfPayment':
            pdfPayment($_GET);
            break;
        case 'getRowRappel':
            getRowRappel($_POST);
            break;
    }
}

function getRowRappel($data)
{
?>
    <tr>
        <td>
            <select class="select select_type" name="type[]">
                <option value="domaine">Nom de domaine</option>
                <option value="hosting">Hébergement</option>
                <option value="ssl">Certificat SSL</option>
            </select>
        </td>
        <td><input type="text" class="form-control" name="domaine[]" value=""></td>
        <td><input type="date" class="form-control" name="date_expir[]" value=""></td>
        <td>
            <i class="fas fa-plus-circle add-row-rappel" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
            <i class="fas fa-minus-circle remove-row-rappel" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne"></i>
        </td>
    </tr>
<?php
}
function addPayment($data)
{
    $indices = array("id_facture", "montant");
    if (fieldCheck($data, $indices)) {
        if (buildPayment($data)->add() == 1) {

            $facture = facture::find($data['id_facture'],$_SESSION['agence']);

            // Les relances de paiement (échéancier J-15/J-10/J-5/J0 puis retard) sont désormais
            // générées et envoyées automatiquement par relance::runDailyReminders() (voir
            // components/com_relance/classes/relance.php), plus par une boucle ici à l'ajout
            // d'un paiement.

            // Ajout des rappels
            if(isset($data['rappel_domaine'])){
                $cpt = 0;
                foreach($data['type'] as $type){
                    $rappel = new rappel();
                    $rappel->setClient($facture->getClient());
                    $rappel->setType($type);
                    $rappel->setDomaine($data['domaine'][$cpt]);
                    $rappel->setDateExpir($data['date_expir'][$cpt]);
                    $rappel->setDateAdd(date('Y-m-d'));
                    $rappel->add();
                    $cpt++;
                }
            }
            // Fin ajout des rappels
            
            // check si la facture est reglée totalement pour changer le status des relance lié
            if($facture->getReste() <= 0){
                $relances = relance::findByFacture($facture->getId());
                foreach($relances as $relance){
                    $relance->setTraite(1);
                    $relance->edit();
                }
            } 

            $facture->checkPayment();
            sendPaymentConfirmationEmail($facture, $data['montant']);
            syncDevisStatutPaiement($facture);

            // Tout paiement (même partiel) tente de déclencher le lancement de
            // projet (Drive + Trello + Slack #familly + email support), mais
            // projectNotifier::launch() ne fait réellement rien si un ticket
            // Trello existe déjà pour ce client (déjà lancé précédemment).
            projectNotifier::launch($facture->getClient(), $facture->getDevis(), 'Paiement ajouté sur la facture #' . $facture->getNumero() . ' (' . number_format($data['montant'], 2, ',', ' ') . ' ' . $facture->getDevise() . ').');

            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Quand une facture est intégralement soldée, le devis d'origine passe aussi à
// "Paiement effectué" (statu=4), ce qui déclenche le ticket Trello pour le responsable technique.
function syncDevisStatutPaiement($facture)
{
    if ($facture->getReste() > 0) {
        return;
    }
    $devis = $facture->getDevis();
    if (!$devis || !$devis->getId() || $devis->getStatu() == 4) {
        return;
    }
    $devis->setStatu(4);
    $devis->setUserEdited($_SESSION['user']);
    $devis->setLastEdit(date('Y-m-d H:i:s'));
    $devis->edit();
    projectNotifier::launch($devis->getClient(), $devis, 'Devis N°' . $devis->getNumero() . ' passé en "Paiement effectué".');
}

function sendPaymentConfirmationEmail($facture, $montant)
{
    if (!defined('SMTP_HOST') || SMTP_HOST == '') {
        return;
    }
    $client = $facture->getClient();
    if (!$client->getEmail()) {
        return;
    }

    require_once '../../../vendor/autoload.php';

    global $siteURL;
    $baseUrl = (defined('PUBLIC_SITE_URL') && PUBLIC_SITE_URL) ? PUBLIC_SITE_URL : $siteURL;
    $espaceClientLink = $baseUrl . "index.php?option=com_elogin";
    $reste = $facture->getReste();

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_USERNAME, 'Hello World');
        $mail->addAddress($client->getEmail(), trim($client->getPrenom() . ' ' . $client->getNom()));
        $mail->addCC('contact@helloworld-agency.com');

        $mail->isHTML(true);
        $isEn = ($facture->getLangue() == 'en');

        if ($isEn) {
            $mail->Subject = "Payment confirmation — Invoice N°" . $facture->getNumero();
            $mail->Body = "Hello " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "We confirm we have received your payment of <strong>" . number_format($montant, 2, ',', ' ') . " " . $facture->getDevise() . "</strong> for invoice N°" . $facture->getNumero() . ". Thank you!<br><br>"
                . ($reste > 0
                    ? "Remaining balance: <strong>" . number_format($reste, 2, ',', ' ') . " " . $facture->getDevise() . "</strong>.<br><br>"
                        . "You can find the details of your invoices and payments at any time from your client portal: <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a> (also available from our mobile app).<br><br>"
                    : "This invoice is now fully paid. Please download your invoice from your client portal: <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a>, also available from our mobile app.<br><br>")
                . "Feel free to reach out with any question, we are always happy to help.<br><br>"
                . "Have a great day,<br>The Hello World team";
            $mail->AltBody = "Hello " . $client->getPrenom() . ", we confirm receipt of your payment of " . $montant . " " . $facture->getDevise() . " for invoice N°" . $facture->getNumero() . "."
                . ($reste > 0 ? "" : " This invoice is now fully paid, please download it from your client portal or mobile app: " . $espaceClientLink);
        } else {
            $mail->Subject = "Confirmation de règlement — Facture N°" . $facture->getNumero();
            $mail->Body = "Bonjour " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "Nous vous confirmons avoir bien reçu votre règlement de <strong>" . number_format($montant, 2, ',', ' ') . " " . $facture->getDevise() . "</strong> pour la facture N°" . $facture->getNumero() . ". Nous vous en remercions !<br><br>"
                . ($reste > 0
                    ? "Solde restant à régler : <strong>" . number_format($reste, 2, ',', ' ') . " " . $facture->getDevise() . "</strong>.<br><br>"
                        . "Vous pouvez à tout moment retrouver le détail de vos factures et règlements depuis votre espace client : <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a> (également accessible depuis notre application mobile).<br><br>"
                    : "Cette facture est désormais soldée intégralement. Merci de télécharger votre facture depuis votre espace client : <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a>, également accessible depuis notre application mobile.<br><br>")
                . "N'hésitez pas à revenir vers nous pour la moindre question, nous sommes toujours ravis de vous accompagner.<br><br>"
                . "Belle journée à vous,<br>L'équipe Hello World";
            $mail->AltBody = "Bonjour " . $client->getPrenom() . ", nous confirmons la réception de votre règlement de " . $montant . " " . $facture->getDevise() . " pour la facture N°" . $facture->getNumero() . "."
                . ($reste > 0 ? "" : " Cette facture est soldée intégralement, merci de la télécharger depuis votre espace client ou l'application mobile : " . $espaceClientLink);
        }

        $mail->send();
    } catch (\Exception $e) {
        // Le paiement est déjà enregistré : un échec d'envoi d'email ne doit pas faire échouer l'opération.
    }
}

function editPayment($data)
{
    $indices = array("id", "id_facture", "montant");
    if (fieldCheck($data, $indices)) {
        if (buildPayment($data, $data['id'])->edit() == 1) {
            $facture = facture::find($data['id_facture'],$_SESSION['agence']);
            $facture->checkPayment();
            syncDevisStatutPaiement($facture);
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deletePayment($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $payment = payment::find($id);
        if ($payment->delete() == 1) {
            facture::find($payment->getFacture()->getId(),$_SESSION['agence'])->checkPayment();
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function removePhotoPayment($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $payment = payment::find($id);
        $payment->setRegImg('');
        if($payment->edit() == 1 && file_exists("../../../images/reglements/" . $payment->getRegImg())){
            @unlink("../../../images/reglements/" . $payment->getRegImg());
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function paymentForm($data)
{

    $indices = array("id_facture");
    if (fieldCheck($data, $indices)) {

        $facture = facture::find($data['id_facture'],$_SESSION['agence']);

        if (isset($data['id']) && !empty($data['id'])) {
            $payment = payment::find($data['id']);
            $btnText = "Modifier paiement";
            $action = "components/com_facture/controleurs/router.php?task=editPayment";
            $task = "edit";
        } else {
            $btnText = "Ajouter paiement";
            $action = "components/com_facture/controleurs/router.php?task=addPayment";
            $task = "add";
        }
?>
        <form method="post" action="<?php echo $action; ?>" id="paymentForm" enctype="multipart/form-data">
            <div class="msgbox"></div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Montant <span class="text-danger">*</span></label>
                        <input class="form-control" type="number" step="any" name="montant" value="<?php if (isset($payment)) echo $payment->getMontant();
                                                                                                    else echo $facture->getReste(); ?>" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Date réception<span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="date_payment" value="<?php if (isset($payment)) echo normaldate($payment->getDatePayment()); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Méthode de paiement <span class="text-danger">*</span></label>
                        <select class="select" name="methode_payment" required>
                            <option value="Chèque" <?php if (isset($payment) && $payment->getMethodePayment() == 'Chèque') echo "selected"; ?>>Chèque</option>
                            <option value="Traite" <?php if (isset($payment) && $payment->getMethodePayment() == 'Traite') echo "selected"; ?>>Traite</option>
                            <option value="Espèce" <?php if (isset($payment) && $payment->getMethodePayment() == 'Espèce') echo "selected"; ?>>Espèce</option>
                            <option value="Virement" <?php if (isset($payment) && $payment->getMethodePayment() == 'Virement') echo "selected"; ?>>Virement</option>
                            <option value="Paiement enline" <?php if (isset($payment) && $payment->getMethodePayment() == 'Paiement enline') echo "selected"; ?>>Paiement enline</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Date validation<span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="date_validation" value="<?php if (isset($payment)) echo normaldate($payment->getDateValidation()); ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Détail</label>
                <textarea name="detail" class="form-control"><?php if (isset($payment)) echo $payment->getDetail(); ?></textarea>
            </div>

            <?php $allPayments = payment::findAll($data['id_facture']); ?>
            <?php if (!isset($payment) && sizeof($allPayments) == 0): ?>
            <!-- Toggle Switch -->
            <div>
                <label class="row form-group toggle-switch">
                    <span class="col-6 toggle-switch-content ml-0">
                        <span class="d-block text-dark">Créer relance paiement</span>
                    </span>
                    <span class="col-6">
                        <input type="checkbox" name="rappel" value="1" class="toggle-switch-input">
                        <span class="toggle-switch-label mr-auto mt-2">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </span>
                </label>
            </div>
            <!-- /Toggle Switch -->

            <?php
            // test si la facture contient hosting/domaine/ssl
            $itemsFacture = $facture->getItems();
            $flag = false;
            $services = array(8,7,77,96,99,74,73);
            foreach($itemsFacture as $item){
                if(in_array($item->getService()->getId(),$services)) $flag = true;
            }
            ?>
            <?php if($flag): ?>
            <!-- Toggle Switch -->
            <div>
                <label class="row form-group toggle-switch">
                    <span class="col-6 toggle-switch-content ml-0">
                        <span class="d-block text-dark">Créer rappel (Hébergement/Nom de domaine/SSL)</span>
                    </span>
                    <span class="col-6">
                        <input type="checkbox" name="rappel_domaine" value="1" class="toggle-switch-input" id="toggle-domaine">
                        <span class="toggle-switch-label mr-auto mt-2 toggle-domaine">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </span>
                </label>
            </div>
            <!-- /Toggle Switch -->
            
            <div class="col-md-12 rappel_domaine_box" style="background:#E9E9E9;padding-top: 10px;border-radius: 5px;margin-bottom: 10px;display:none">
                <div class="form-group">
                    <label>Ajout de rappel</label>

                    <div class="myResponsivTable mb-4">
                        <table class="table table-stripped table-center table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Domaine</th>
                                    <th>Date expiration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="select" name="type[]">
                                            <option value="domaine">Nom de domaine</option>
                                            <option value="hosting">Hébergement</option>
                                            <option value="ssl">Certificat SSL</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="domaine[]" value=""></td>
                                    <td><input type="date" class="form-control" name="date_expir[]" value=""></td>
                                    <td>
                                        <i class="fas fa-plus-circle add-row-rappel" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
                                        <i class="fas fa-minus-circle remove-row-rappel" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php endif; ?>
            <?php endif; ?> 

            <div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Règlement</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($payment) && $payment->getRegImg() != '' ? "images/reglements/" . $payment->getRegImg() : "assets/img/profiles/avatar-02.jpg"; ?>
                            <?php $filename = isset($payment) ? explode('.',$payment->getRegImg()) : '';?>
                            <?php if(isset($filename[1]) && strtolower($filename[1]) == 'pdf'): ?>
							    <a href="<?php echo $photoLink;?>" target="_blank"><img id="avatarImg" class="avatar-img" src="assets/img/pdf.png" alt="Profile Image"></a>
                            <?php else: ?>
                                <a href="<?php echo $photoLink;?>"data-fancybox><img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image"></a>
							<?php endif; ?>
                            <input type="file" name="photo[]" id="edit_img">
							
                            <span class="avatar-edit" style="bottom:0;color:green;">
								<i data-feather="edit-2" class="fa fa-upload shadow-soft"></i>
							</span>
						</label>

                        <?php if (isset($payment)) : ?>
                        <a class="avatar-remove" style="top:0;color:red;left: 72px;" data-id="<?php echo $payment->getId(); ?>">
                            <i class="fa fa-trash shadow-soft"></i>
                        </a>
                        <?php endif; ?>
					</div>
				</div>
			</div>

            <?php if (isset($payment)) : ?>
                <input type="hidden" name="id" value="<?php echo $payment->getId(); ?>">
            <?php endif; ?>
            <input type="hidden" name="id_facture" value="<?php echo $data['id_facture'] ?>">

            <div class="submit-section">
                <button class="btn btn-primary submit-btn submit" name="<?php echo $task; ?>"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $btnText; ?></button>
            </div>
        </form>
        <script>
            $(function() {
                $(document).on("click", ".toggle-domaine", function() {
                    if($("#toggle-domaine").is(':checked'))
                        $(".rappel_domaine_box").slideUp();
                    else
                        $(".rappel_domaine_box").slideDown();
                })

                $(document).on("click", ".add-row-rappel", function() {
                    var $btn = $(this);
                    var order = '';
                    $.post("components/com_facture/controleurs/router.php?task=getRowRappel", order, function(theResponse) {
                        $btn.parent().parent().after(theResponse);
                        $('.select_type').select2();
                    })
                })

                $(document).on("click", ".remove-row-rappel", function() {
                    var $btn = $(this);
                    if (confirm("Etes-vous sure !")) {
                        $btn.parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().remove()
                        }, 1000);
                    }
                })

                // Select 2
                if ($('.select').length > 0) {
                    $('.select').select2({
                        minimumResultsForSearch: -1,
                        width: '100%'
                    });
                }

                // Datetimepicker
                if ($('.datetimepicker').length > 0) {
                    $('.datetimepicker').datetimepicker({
                        format: 'DD/MM/YYYY',
                        icons: {
                            up: "fas fa-angle-up",
                            down: "fas fa-angle-down",
                            next: 'fas fa-angle-right',
                            previous: 'fas fa-angle-left'
                        }
                    });
                }

                $(document).on("click", ".avatar-remove", function() {
                    var $btn = $(this);
                    var id = $btn.attr("data-id");
                    if (confirm("Etes-vous sure !")) {
                        var order = 'id=' + id;
                        $.post("components/com_facture/controleurs/router.php?task=removePhotoPayment", order, function(theResponse) {
                            if (parseInt(theResponse) == 1) {
                                $('.avatar-img').attr('src','assets/img/profiles/avatar-02.jpg');
                            } else {
                                $('#factureForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                            }
                        })
                    }
                })

                // envoi du formulaire en ajax
                $('form#paymentForm').ajaxForm({
                    beforeSubmit: function() {
                        $("#paymentForm .loading").css('display', 'inline-block');
                    },
                    success: function(theResponse) {
                        $("#paymentForm .loading").fadeOut();
                        $("html, body").animate({
                            scrollTop: 0
                        }, "slow");

                        var msgsucces = "Paiement ajouté avec succès";
                        if ($(".submit").attr("name") === "edit") {
                            msgsucces = "Paiement modifié avec succès";
                        }
                        if (parseInt(theResponse) === 1) {
                            $('#paymentForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                            setTimeout(function() {
                                document.location.reload();
                            }, 1500)

                        } else if (parseInt(theResponse) === 0) {
                            $('#paymentForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                        } else {
                            $('#paymentForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                        }
                    }
                });
            })
        </script>
<?php
    }
}

function buildPayment($data, $id = null)
{
    $payment = new payment();

    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
        $photo = uploadFiles('photo','../../../images/reglements/',  array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));    
    }

    if ($id) {
        $payment = payment::find($id);
    }

    if(isset($photo[0])) {
		$payment->setRegImg($photo[0]);
	}

    $payment->setFacture(facture::find($data['id_facture'],$_SESSION['agence']));
    $payment->setMontant($data['montant']);
    $payment->setDatePayment(dateBD($data['date_payment']));
    $payment->setMethodePayment($data['methode_payment']);
	$payment->setDateValidation(dateBD($data['date_validation']));
    $payment->setDetail($data['detail']);
    $payment->setDateAdd(date("Y-m-d H:i:s"));
    $payment->setLastEdit(date("Y-m-d H:i:s"));

    return $payment;
}

function pdfPayment($data)
{
	global $db;
	if (isset($data["id"]) && !empty($data["id"])) {
		
		require '../../../vendor/autoload.php';
		require '../../../includes/traduction.php';
		
		$payment = payment::find($data["id"]);
		$payment->pdfPayment("show",$data["index"]);

		
	}
}
