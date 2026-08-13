<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addDevis':
            addDevis($_POST);
            break;
        case 'editDevis':
            editDevis($_POST);
            break;
        case 'checkContrat':
            checkContrat($_POST);
            break;
        case 'deleteDevis':
            deleteDevis($_POST);
            break;
        case "getRowDevis":
            getRowDevis($_POST);
            break;
        case "getRowCondition":
            getRowCondition();
            break;
        case "sendSlackDevis":
            sendSlackDevis($_POST);
            break;
        case "sendEmailDevis":
            sendEmailDevis($_POST);
            break;
        case "removeCondition":
            removeCondition($_POST);
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
        case 'getServiceUnite':
            getServiceUnite($_POST);
            break;
        case 'switchUnitiesByLanguage':
            switchUnitiesByLanguage($_POST);
            break;
        case 'pdfDevis':
            pdfDevis($_GET);
            break;
        case 'pdfDevisTexte':
            pdfDevisTexte($_GET);
            break;    
        case 'createInvoice':
            createInvoice($_POST);
            break;
        case 'sendMail':
            sendMail($_POST);
            break;
        case 'duplicateDevis':
            duplicateDevis($_POST);
            break;
        case 'findAllByClientApi':
            findAllByClientApi($_GET);
            break;
        case 'pdfDevisApi':
            pdfDevisApi($_GET);
            break;
        case 'slackEventWebhook':
            slackEventWebhook();
            break;
        case 'cronVerifierValidationDevisSlack':
            cronVerifierValidationDevisSlackEndpoint();
            break;
    }
}


function checkContrat($data)
{
    $indices = array("id","status");
    if (fieldCheck($data, $indices)) {
        $devis = devis::find($data['id'], $_SESSION['agence']);
        $contrat = contract::findByDevis($data['id'], $_SESSION['agence']);

        // check si le devis est associé à un contrat
        if($contrat->getId() != 0){
            // check si le status a mettre a jour est payé
            if($data['status'] == 4){
                // check si le status est different de signé
                if($contrat->getStatus() != 3){
                    echo "1";
                }
            }

            // check si le contrat est signé
            if($contrat->getStatus() == 3){
                // si le statut devis est différent de "payé"
                if($data['status'] != 4){
                    echo "2";
                }
            }
        }

        
    }
}
function duplicateDevis($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $devis = devis::find($data['id'], $_SESSION['agence']);

        $devisCopy = new devis();
        $devisCopy->setUserAdded($devis->getUserAdded());
        $devisCopy->setBank($devis->getBank());
        $devisCopy->setClient($devis->getClient());
        $devisCopy->setDateDevis(date("Y-m-d"));
        $devisCopy->setStatu(0);
        $devisCopy->setTotal($devis->getTotal());
        $devisCopy->setDevise($devis->getDevise());
        $devisCopy->setDiscount($devis->getDiscount());
        $devisCopy->setDiscountVal($devis->getDiscountVal());
        $devisCopy->setLangue($devis->getLangue());
        $devisCopy->setConditionPaiment($devis->getConditionPaiment());
        $devisCopy->setRemarque($devis->getRemarque());
        $devisCopy->setProforma($devis->getProforma());
        $devisCopy->setPack($devis->getPack());
        $devisCopy->setDateAdd(date("Y-m-d H:i:s"));
        $devisCopy->setLastEdit(date("Y-m-d H:i:s"));


        if ($devisCopy->add() == 1) {
            // Ajout des lignes facture
            $id_devis = devis::getLastId();
            $devisCopy = devis::find($id_devis, $_SESSION['agence']);
            $items = $devis->getItems();
            foreach ($items as $item) {
                $item_devis = new item_devis();
                $item_devis->setDevis($devisCopy);
                $item_devis->setService($item->getService());
                $item_devis->setQte($item->getQte());
                $item_devis->setDiscount($item->getDiscount());
                $item_devis->setPrix($item->getPrix());
                $item_devis->setTotal($item->getTotal());
                $item_devis->setUnite($item->getUnite());
                $item_devis->setTitre($item->getTitre());
                $item_devis->setDescription($item->getDescription());
                $item_devis->setOrdre($item->getOrdre());
                $item_devis->add();
            }

            // calcul et mise a jour total facture / generer numéro facture
            //$facture->setTotalItems();
            $devisCopy->generateNumero();
            $devisCopy->edit();

            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function sendMail($data)
{
    require '../../../vendor/PHPMailer-master/src/PHPMailer.php';
    require '../../../vendor/PHPMailer-master/src/SMTP.php';

    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $devis = devis::find($data['id'], $_SESSION['agence']);
        $config = new config($db);
        $client = $devis->getClient();

        $mail = new PHPMailer(true);

        //$mail->isSMTP(); // Paramétrer le Mailer pour utiliser SMTP 
        //$mail->Host = 'mail.votredomaine.com'; // Spécifier le serveur SMTP
        //$mail->SMTPAuth = true; // Activer authentication SMTP
        //$mail->Username = 'user@votredomaine.com'; // Votre adresse email d'envoi
        //$mail->Password = 'secret'; // Le mot de passe de cette adresse email
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Accepter SSL
        //$mail->Port = 465;

        $mail->setFrom($config->getEmail(), 'Hello World'); // Personnaliser l'envoyeur
        $mail->addAddress($client->getEmail(), $client->getNom() . ' ' . $client->getPrenom()); // Ajouter le destinataire
        //$mail->addAddress('To2@example.com'); 
        $mail->addReplyTo('info@example.com', 'Information'); // L'adresse de réponse
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');

        $mail->addAttachment('/var/tmp/file.tar.gz'); // Ajouter un attachement
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');
        $mail->isHTML(true); // Paramétrer le format des emails en HTML ou non

        $mail->Subject = 'Here is the subject';
        $mail->Body = 'This is the HTML message body';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        echo "OK";
    }
}

function createInvoice($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {

        $devis = devis::find($data['id'], $_SESSION['agence']);
        $facture = createInvoiceFromDevis($devis);

        if ($facture) {
            echo "1|" . $facture->getId();
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Crée la facture correspondant à un devis (mêmes lignes/total/conditions),
// réutilisée à la fois par le bouton "+ Facture" et par le paiement inline
// déclenché depuis le devis quand le statut passe à "Paiement effectué".
function createInvoiceFromDevis($devis)
{
    $facture = new facture();
    $facture->setDevis($devis);
    $facture->setUserAdded($_SESSION['user']);
    $facture->setBank($devis->getBank());
    $facture->setClient($devis->getClient());
    $facture->setDateFacture(date("Y-m-d"));
    $facture->setDateDebut(date("Y-m-d"));
    // Date fin = date de fin du contrat lié à ce devis, s'il existe et qu'elle est renseignée. Sinon vide.
    $contratLie = contract::findByDevis($devis->getId(), $_SESSION['agence'], $_SESSION['langue']);
    if ($contratLie && $contratLie->getId() && $contratLie->getDateFin()) {
        $facture->setDateFin($contratLie->getDateFin());
    }
    $facture->setStatu(0);
    $facture->setTotal($devis->getTotal());
    $facture->setDevise($devis->getDevise());
    $facture->setDiscount($devis->getDiscount());
    $facture->setShowSignature(0);
    $facture->setDiscountVal($devis->getDiscountVal());
    $facture->setLangue($devis->getLangue());
    $facture->setConditionPaiment($devis->getConditionPaiment());
    $facture->setRemarque($devis->getRemarque());
    $facture->setDateAdd(date("Y-m-d H:i:s"));
    $facture->setLastEdit(date("Y-m-d H:i:s"));

    if ($facture->add() != 1) {
        return false;
    }

    $id_facture = facture::getLastId();
    $facture = facture::find($id_facture, $_SESSION['agence']);

    $items = $devis->getItems();
    foreach ($items as $item) {
        $item_facture = new item_facture();
        $item_facture->setFacture($facture);
        $item_facture->setService($item->getService());
        $item_facture->setQte($item->getQte());
        $item_facture->setDiscount($item->getDiscount());
        $item_facture->setPrix($item->getPrix());
        $item_facture->setTotal($item->getTotal());
        $item_facture->setUnite($item->getUnite());
        $item_facture->setTitre($item->getTitre());
        $item_facture->setDescription($item->getDescription());
        $item_facture->setOrdre($item->getOrdre());
        $item_facture->add();
    }

    // calcul et mise a jour total facture / generer numéro facture
    $facture->generateNumero();
    $facture->edit();

    $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
    $agence->setNumeroIncrementFacture($agence->getNumeroIncrementFacture() + 1);
    $agence->edit();

    return $facture;
}

// Enregistre le paiement saisi depuis le devis au moment où son statut passe à
// "Paiement effectué" : crée la facture si besoin, ajoute le paiement dessus, et
// marque les relances traitées si la facture est désormais soldée (les relances de
// paiement elles-mêmes sont générées séparément par relance::runDailyReminders()).
function registerDevisPayment($devis, $data)
{
    $facture = facture::findByDevis($devis->getId(), $_SESSION['agence']);
    if (!$facture || !$facture->getId()) {
        $facture = createInvoiceFromDevis($devis);
        if (!$facture) {
            return;
        }
    }

    $payment = new payment();
    if (isset($_FILES['payment_photo']) && $_FILES['payment_photo']['name'][0] != '') {
        $photo = uploadFiles('payment_photo', '../../../images/reglements/', array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'JPG', 'JPEG', 'GIF', 'PNG', 'PDF'));
        if (isset($photo[0])) {
            $payment->setRegImg($photo[0]);
        }
    } else {
        $payment->setRegImg('');
    }
    $payment->setFacture($facture);
    $payment->setMontant($data['payment_montant']);
    $payment->setDatePayment(dateBD($data['payment_date_payment']));
    $payment->setMethodePayment($data['payment_methode_payment']);
    $payment->setDateValidation(dateBD($data['payment_date_validation']));
    $payment->setDetail(isset($data['payment_detail']) ? $data['payment_detail'] : '');
    $payment->setDateAdd(date("Y-m-d H:i:s"));
    $payment->setLastEdit(date("Y-m-d H:i:s"));
    $payment->add();

    $facture->checkPayment();

    // Les relances de paiement (échéancier J-15/J-10/J-5/J0 puis retard) sont désormais générées
    // et envoyées automatiquement par relance::runDailyReminders(), plus par une boucle ici. On
    // garde uniquement l'arrêt des relances en cours quand la facture est soldée.
    if ($facture->getReste() <= 0) {
        $relances = relance::findByFacture($facture->getId());
        foreach ($relances as $relance) {
            $relance->setTraite(1);
            $relance->edit();
        }
    }

    require_once __DIR__ . '/../../../com_facture/controleurs/payment/controleur.php';
    if (function_exists('sendPaymentConfirmationEmail')) {
        sendPaymentConfirmationEmail($facture, $data['payment_montant']);
    }
}
function getServicePrice($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $service = service::find($data["id"]);
        $price = $service->getPrix();

        /*if (isset($data['id_item']) && !empty($data['id_item'])) {
			$item_devis = item_devis::find($data['id_item']);
			$price = $item_devis->getPrix();
		}*/

        echo $price;
    }
}

function switchUnitiesByLanguage($data)
{
    $indices = array("langue");
    if (fieldCheck($data, $indices)) {
        $unities = getUnities()[$data['langue']];
        echo  json_encode($unities);
    }else{
        echo json_encode([]);
    }
    return;
}

function getServiceUnite($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $service = service::find($data["id"]);
        $unite = $service->getUnite();

        /*if (isset($data['id_item']) && !empty($data['id_item'])) {
			$item_devis = item_devis::find($data['id_item']);
			$price = $item_devis->getPrix();
		}*/

        echo $unite;
    }
}
function addDevis($data)
{
    $indices = array("client", "bank");
    if (isset($data['statu']) && $data['statu'] == 4) {
        $indices[] = 'payment_montant';
        $indices[] = 'payment_date_payment';
        $indices[] = 'payment_methode_payment';
        $indices[] = 'payment_date_validation';
    }
    if (fieldCheck($data, $indices)) {
        if (buildDevis($data)->add() == 1) {
            $id_devis = devis::getLastId();

            // conditions de paiement
            $cpt = 0;
            if (isset($data["amount"])){
                foreach ($data["amount"] as $amount) {
                    $condition = new condition();
                    $condition->setDevis(devis::find($id_devis,$_SESSION['agence']));
                    $condition->setCondition($data['condition'][$cpt]);
                    $condition->setMontant($amount);
                    $condition->setDate($data['date_rappel'][$cpt]);
                    $condition->add();
                    $cpt++;
                }
            }

            // Ajout des lignes devis
            $devis = devis::find($id_devis, $_SESSION['agence']);

            if (isset($data["id_service"]) && !empty($data["id_service"])) {
                $cpt = 0;
                foreach ($data["id_service"] as $id_service) {
                    $service = service::find($id_service);
                    $item_devis = new item_devis();
                    $item_devis->setDevis($devis);
                    $item_devis->setService($service);
                    $item_devis->setQte($data["qte"][$cpt]);
                    $item_devis->setDiscount($data["rms"][$cpt]);
                    $item_devis->setPrix($data["prix"][$cpt]);
                    $item_devis->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
                    $item_devis->setUnite($data["unite"][$cpt]);
                    $item_devis->setTitre($data["item_devis_service_title"][$cpt]);
                    $item_devis->setDescription($data["item_devis_service_description"][$cpt]);
                    $item_devis->setOrdre($cpt);
                    $item_devis->add();
                    $cpt++;
                }

                // calcul et mise a jour total devis / generer numéro devis
                $devis->setTotalItems();
                $devis->generateNumero();
                $devis->edit();
                $agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
                $agence->setNumeroIncrementDevis($agence->getNumeroIncrementDevis()+1);
                $agence->edit();
            }
            if(isset($data['generate_contract']) && $data['generate_contract'] == 1){
                $contract = new contract();

                $contract->setDevis($devis);
                $contract->setTitre($data['titre_contract']);
                $contract->setDuration($data['duration_contract']);
                $contract->setGarantie($data['garantie_contract']);
                $contract->setVille($data['ville_contract']);
                $contract->setDate(dateBD($data['date_contract']));
                $contract->setTribunal($data['tribunal_contract']);
                $contract->setNombreDePaiement($data['nombre_de_paiement_contract']);
                $contract->setTexte("");
                $contract->setShowSignature($data['show_signature_contract']);
                $contract->setStatus(1);
                $contract->setDateAdd(date("Y-m-d H:i:s"));
                $contract->setLastEdit(date("Y-m-d H:i:s"));
                $contract->setLangue($devis->getLangue());
                $contract->add();
            }

            // Statut "Paiement effectué" saisi dès la création : on enregistre tout de suite
            // le paiement (facture créée à la volée si besoin, relance obligatoire si partiel)
            if ($data['statu'] == 4) {
                registerDevisPayment(devis::find($id_devis, $_SESSION['agence']), $data);
            }

            echo "1|" . $id_devis;
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editDevis($data)
{

    $indices = array("id", "client", "bank");
    if (fieldCheck($data, $indices)) {

        $oldStatu = devis::find($data['id'], $_SESSION['agence'])->getStatu();

        // check contrat
        $contrat = contract::findByDevis($data['id'], $_SESSION['agence']);
        if($contrat->getId() != 0){
            // check si le status a mettre a jour est payé
            if($data['statu'] == 4){
                // check si le status est different de signé
                if($contrat->getStatus() != 3){
                    echo "2"; exit;
                }
            }

            // check si le contrat est signé
            if($contrat->getStatus() == 3){
                // si le statut devis est différent de "payé"
                if($data['statu'] != 4){
                    echo "3"; exit;
                }
            }
        }

        // Passage au statut "Paiement effectué" : le paiement est obligatoire
        if ($oldStatu != 4 && isset($data['statu']) && $data['statu'] == 4) {
            $paymentIndices = array('payment_montant', 'payment_date_payment', 'payment_methode_payment', 'payment_date_validation');
            if (!fieldCheck($data, $paymentIndices)) {
                echo "0"; exit;
            }
        }

        if (buildDevis($data, $data['id'])->edit() == 1) {
            
            // conditions de paiement
            $cpt = 0;
            if (isset($data["amount"])){
                
                foreach ($data["amount"] as $amount) {
                    if(isset($data["condition_id"][$cpt])){
                        $condition = condition::find($data["condition_id"][$cpt]);
                        $condition->setDevis(devis::find($data['id'],$_SESSION['agence']));
                        $condition->setCondition($data['condition'][$cpt]);
                        $condition->setMontant($amount);
                        $condition->setDate($data['date_rappel'][$cpt]);
                        $condition->edit();
                    }else{
                        $condition = new condition();
                        $condition->setDevis(devis::find($data['id'],$_SESSION['agence']));
                        $condition->setCondition($data['condition'][$cpt]);
                        $condition->setMontant($amount);
                        $condition->setDate($data['date_rappel'][$cpt]);
                        $condition->add();
                    }
                    $cpt++;
                }
            }
            
            // service
            if (isset($data["id_service"]) && !empty($data["id_service"])) {
                $cpt = 0;
                foreach ($data["id_service"] as $id_service) {
                    $service = service::find($id_service);
                    if (isset($data["item_id"][$cpt]) && !empty($data["item_id"][$cpt])) {
                        $item_devis = item_devis::find($data["item_id"][$cpt]);
                        $item_devis->setService($service);
                        $item_devis->setQte($data["qte"][$cpt]);
                        $item_devis->setDiscount($data["rms"][$cpt]);
                        $item_devis->setPrix($data["prix"][$cpt]);
                        $item_devis->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
                        $item_devis->setUnite($data["unite"][$cpt]);
                        $item_devis->setTitre($data["item_devis_service_title"][$cpt]);
                        $item_devis->setDescription($data["item_devis_service_description"][$cpt]);
                        $item_devis->setOrdre($data["ordre"][$cpt]);
                        $item_devis->edit();
                    } else {
                        $item_devis = new item_devis();
                        $item_devis->setDevis(devis::find($data['id'],$_SESSION['agence']));
                        $item_devis->setService($service);
                        $item_devis->setQte($data["qte"][$cpt]);
                        $item_devis->setDiscount($data["rms"][$cpt]);
                        $item_devis->setPrix($data["prix"][$cpt]);
                        $item_devis->setTotal($data["qte"][$cpt] * $data["prix"][$cpt]);
                        $item_devis->setUnite($data["unite"][$cpt]);
                        $item_devis->setTitre($data["item_devis_service_title"][$cpt]);
                        $item_devis->setDescription($data["item_devis_service_description"][$cpt]);
                        $item_devis->setOrdre($cpt);
                        $item_devis->add();
                    }
                    $cpt++;
                }

                $devis = devis::find($data['id'], $_SESSION['agence']);
                $devis->setTotalItems();
                $devis->edit();
            }
            if(isset($data['generate_contract']) && $data['generate_contract'] == 1){
                $devis = devis::find($data['id'], $_SESSION['agence']);
                $contract = contract::findByDevis($devis->getId(),$_SESSION['agence'],$devis->getLangue());
                if(!$contract->getId()){
                    $contract = new contract();
                    $contract->setDevis($devis);
                    $contract->setTitre($data['titre_contract']);
                    $contract->setDuration($data['duration_contract']);
                    $contract->setGarantie($data['garantie_contract']);
                    $contract->setVille($data['ville_contract']);
                    $contract->setDate(dateBD($data['date_contract']));
                    $contract->setTribunal($data['tribunal_contract']);
                    $contract->setNombreDePaiement($data['nombre_de_paiement_contract']);
                    $contract->setTexte("");
                    $contract->setShowSignature($data['show_signature_contract']);
                    $contract->setStatus(1);
                    $contract->setDateAdd(date("Y-m-d H:i:s"));
                    $contract->setLastEdit(date("Y-m-d H:i:s"));
                    $contract->setLangue($devis->getLangue());
                    $contract->add();
                }
            }

            // Statut qui passe à "Paiement effectué" : paiement enregistré (facture créée si besoin,
            // relance obligatoire si partiel) puis ticket Trello pour le responsable technique
            if ($oldStatu != 4 && $data['statu'] == 4) {
                registerDevisPayment(devis::find($data['id'], $_SESSION['agence']), $data);
                $updatedDevis = devis::find($data['id'], $_SESSION['agence']);
                projectNotifier::launch($updatedDevis->getClient(), $updatedDevis, 'Devis N°' . $updatedDevis->getNumero() . ' passé en "Paiement effectué".');
            }

            // Statut qui passe à "Accepté" : email automatique de remerciement au client (montant,
            // conditions de paiement, contrat à signer si applicable), dans la langue du devis, et
            // création de la facture liée si elle n'existe pas encore (nécessaire pour la demande
            // d'acompte proposée en popup juste après l'enregistrement).
            $accFactureId = '';
            if ($oldStatu != 2 && $data['statu'] == 2) {
                require_once '../../../vendor/autoload.php';
                $acceptedDevis = devis::find($data['id'], $_SESSION['agence']);
                sendDevisAcceptedEmailToClient($acceptedDevis);

                $accFacture = facture::findByDevis($acceptedDevis->getId(), $_SESSION['agence']);
                if (!$accFacture || !$accFacture->getId()) {
                    $accFacture = createInvoiceFromDevis($acceptedDevis);
                }
                $accFactureId = $accFacture ? $accFacture->getId() : '';
            }

            echo "1" . ($accFactureId !== '' ? "|" . $accFactureId : "");
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
        $last_quote = devis::findLastQuote($_SESSION['agence']);
        if ($devis->delete() == 1) {
            if($last_quote->getId() == $devis->getId()){
				$agence = agence::find($_SESSION['agence'],$_SESSION['langue']);
				$agence->setNumeroIncrementDevis($agence->getNumeroIncrementDevis()-1);
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

function sendSlackDevis($data)
{
    header('Content-Type: application/json');

    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Devis invalide'));
        return;
    }

    if (!defined('SLACK_WEBHOOK_URL') || SLACK_WEBHOOK_URL == '') {
        echo json_encode(array('success' => 0, 'message' => "Webhook Slack non configuré (config.secrets.php)."));
        return;
    }

    $devis = devis::find($data['id'], $_SESSION['agence']);
    if (!$devis->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Devis introuvable'));
        return;
    }

    global $siteURL;
    $pdfLink = $siteURL . "components/com_devis/controleurs/router.php?task=pdfDevisTexte&id=" . $devis->getId();
    $client = $devis->getClient();

    $texte = ":page_facing_up: *Nouveau devis à valider* — N°" . $devis->getNumero() . "\n"
        . "*Client :* " . $client->getPrenom() . " " . $client->getNom() . ($client->getRaisonSocial() ? " (" . $client->getRaisonSocial() . ")" : "") . "\n"
        . "*Montant :* " . $devis->getTotal() . " " . $devis->getDevise() . "\n"
        . "*Ajouté par :* " . $_SESSION['user']->getNom() . "\n"
        . "*PDF :* " . $pdfLink;

    $payload = array('text' => $texte);

    $ch = curl_init(SLACK_WEBHOOK_URL);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15
    ));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        echo json_encode(array('success' => 0, 'message' => "Erreur de connexion à Slack : " . $curlError));
        return;
    }
    if ($httpCode !== 200 || trim($response) !== 'ok') {
        echo json_encode(array('success' => 0, 'message' => "Erreur Slack (" . $httpCode . "): " . $response));
        return;
    }

    // "Envoyer sur Slack" n'est qu'une notification interne pour relecture/validation par
    // l'équipe - ce n'est PAS un envoi au client, donc le statut reste en Brouillon tant que la
    // validation n'a pas eu lieu (message "je valide le devis <numéro>" dans commercial-hw, voir
    // cronVerifierValidationDevisSlackEndpoint()) ou que le statut n'est pas changé manuellement
    // depuis le CRM. Avant cette correction, cliquer ce bouton faisait passer le devis en Envoyé
    // immédiatement, avant même toute validation - ce qui ne reflétait pas la réalité.
    echo json_encode(array('success' => 1, 'statu' => $devis->getStatu()));
}

function sendEmailDevis($data)
{
    header('Content-Type: application/json');

    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Devis invalide'));
        return;
    }

    $devis = devis::find($data['id'], $_SESSION['agence']);
    if (!$devis->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Devis introuvable'));
        return;
    }

    require_once '../../../vendor/autoload.php';
    $result = sendDevisPdfEmailToClient($devis);
    if ($result === true) {
        $statu = markDevisEnvoyeSiBrouillon($devis);
        echo json_encode(array('success' => 1, 'statu' => $statu));
    } else {
        echo json_encode(array('success' => 0, 'message' => $result));
    }
}

// Un devis envoyé (email ou Slack) sort de l'état "Brouillon" : s'il y est encore,
// on le passe à "Envoyé" (1). On ne touche à rien si son statut a déjà avancé plus loin.
function markDevisEnvoyeSiBrouillon($devis)
{
    if ($devis->getStatu() != 0) {
        return $devis->getStatu();
    }
    $devis->setStatu(1);
    $devis->setLastEdit(date('Y-m-d H:i:s'));
    $devis->edit();
    return 1;
}

function getRowDevis($data)
{
    $services = service::findAll($data["lang"], true);
?>
    <tr>
        <td><input type="number" name="ordre[]" value="1" class="form-control"></td>
        <td>
            <select class="chosen-select service-select" name="id_service[]" style="width:500px;" required>
                <option value="" selected>Sélectionner</option>
                <?php foreach ($services as $service) : ?>
                    <option  data-title="<?=$service->getTitre()?>"  data-description="<?=$service->getDescription()?>" value="<?php echo $service->getId() ?>"><?php echo $service->getTitre(); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="item_devis_service_title[]" value="">
			<input type="hidden" name="item_devis_service_description[]" value="">
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
    			    $unities = getUnities()[isset($devis) ? $devis->getLangue() : 'fr'];
    			    foreach ($unities as $key=>  $value) : ?>
    				<option value="<?php echo $key ?>" ><?= $value ?></option>
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
			$.post("components/com_devis/controleurs/router.php?task=getServicePrice", order, function(theResponse) {
				var discountTotal = (parseFloat(discount) * parseFloat(theResponse)) / 100; 
				var total = (parseFloat(theResponse) * parseInt(qte)) - discountTotal;
				select.parent().parent().find(".price-input").val(theResponse);
				select.parent().parent().find(".total-input").val(total);
				select.parent().parent().find('input[name="item_devis_service_title[]"]').val(title)
				select.parent().parent().find('input[name="item_devis_service_description[]"]').val(description)
			});
			$.post("components/com_devis/controleurs/router.php?task=getServiceUnite", order, function(theResponse2) {
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


function getRowCondition(){
?>
    <tr>
        <td><input type="text" name="amount[]" value="" class="form-control amount-input" required></td>
        <td><input type="text" name="condition[]" value="" class="form-control condition-input" required></td>
        <td><input type="date" name="date_rappel[]" value="" class="form-control date-input" required></td>
        <td>
            <i class="fas fa-plus-circle add-row-condition" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter ligne"></i>
            <i class="fas fa-minus-circle remove-row" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer ligne"></i>
        </td>
    </tr>
    <?php
}

function removeCondition($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $condition = condition::find($id);
        if ($condition->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
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
            <div class="form-group">
                <label>Unité <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="unite" value="<?php echo $item_devis->getUnite(); ?>">
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
            <input type="hidden" name="id" value="<?php echo $item_devis->getId(); ?>">
            <input type="hidden" name="id_service" value="<?php echo $item_devis->getService()->getId(); ?>">
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

function editItemDevis($data)
{
    $indices = array("id", "titre", "description", "unite");
    if (fieldCheck($data, $indices)) {
        $item_devis = item_devis::find($data['id']);
        $item_devis->setTitre($data['titre']);
        $item_devis->setDescription($data['description']);
        $item_devis->setUnite($data['unite']);
        if ($item_devis->edit() == 1) {
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

function buildDevis($data, $id = null)
{

    $devis = new devis();

    if ($id) {
        $devis = devis::find($id, $_SESSION['agence']);
        $devis->setUserEdited($_SESSION['user']);
    }else{
        $devis->setUserAdded($_SESSION['user']);
    }

    $banqueChoisie = bank::find($data['bank']);
    // Comptes personnels (Hamid/Zakaria - "PERSO" dans la raison sociale) : jamais de TVA, toujours
    // proforma - imposé ici plutôt qu'uniquement côté JS (assets/js/ia-bank-filter.js), pour que la
    // règle tienne même si le formulaire est soumis sans que le JS ait tourné.
    $estBanquePersonnelle = $banqueChoisie->getId() && stripos($banqueChoisie->getRaisonSociale(), 'PERSO') !== false;

    $devis->setNumero($data['numero']);
    $devis->setBank($banqueChoisie);
    $devis->setClient(client::find($data['client'],$_SESSION['agence']));
    $devis->setDateDevis(dateBD($data['date_devis']));
    $devis->setStatu($data['statu']);
    $devis->setDevise($data['devise']);
    $devis->setDiscount($data['discount']);
    $devis->setDiscountVal($data['discount_val']);
    $devis->setConditionPaiment($data['condition_paiment']);
    $devis->setRemarque($data['remarque']);
    //$devis->setMotifRefus($data['motif_refus']);
    $devis->setProforma($estBanquePersonnelle ? 1 : (isset($data['proforma']) ? 1 : 0));
    $devis->setPack(isset($data['pack']) ? 1 : 0);
    $devis->setTVA($estBanquePersonnelle ? 0 : $data['tva']);
    $devis->setLangue($data['langue']);
    $devis->setDateAdd(date("Y-m-d H:i:s"));
    $devis->setLastEdit(date("Y-m-d H:i:s"));

    return $devis;
}

function pdfDevis($data)
{
    global $db;
    if (isset($data["id"]) && !empty($data["id"])) {

        require_once '../../../vendor/autoload.php';
        require_once '../../../includes/traduction.php';

        $devis = devis::find($data["id"], $_SESSION['agence']);
        
        $items = $devis->getItems();
        $client = $devis->getClient();
        $agence = agence::find($_SESSION["agence"],$_SESSION["langue"]);
        $config = new config($db);
        $invoiceFor = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();
        $color = $agence->getColor();
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
	<td><img src="../../../images/agences/' . $agence->getLogo() . '" width="100"></td>
	<td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
	<p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> '.$agence->getWebsite().'</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

if($agence->getIce() != ''){
$htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> '.$agence->getIf().' | <strong>TP</strong> '.$agence->getTp().' | <strong>RC</strong> '.$agence->getRc().' | <strong>ICE</strong> '.$agence->getIce().'</p>';
}

$htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR'][$devis->getLangue()] . ' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">' . $traduction['DEVIS_POUR'][$devis->getLangue()] . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:'.$color.'">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">' . $traduction['TOTAL_DEVIS'][$devis->getLangue()] . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td></tr>
</table>

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">' . $traduction['DATE_DEVIS'][$devis->getLangue()] . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($devis->getDateDevis(),$devis->getLangue()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $traduction['DEVIS'][$devis->getLangue()] . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $devis->getNumero() . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td width="45%" style="text-align:left;">Description</td>
<td width="15%">' . $traduction['PRIX_HT'][$devis->getLangue()] . '</td>
<td width="20%">' . $traduction['QTE'][$devis->getLangue()] . '</td>
<td width="20%" align="right">' . $traduction['TOTAL_HT'][$devis->getLangue()] . '</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
        $soustotal = 0;
        foreach ($items as $item) {
            $discount = $item->getDiscount()>0 ? ($item->getDiscount() * $item->getTotal()) / 100 : 0;
            $soustotal += ($item->getTotal() - $discount);
            $htmlInvoice .= '<tr>
                                <td><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
                                <td align="center" style="vertical-align:middle;">' . number_format($item->getPrix(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
                                <td align="center" style="vertical-align:middle;">' . $item->getQte() . ' x ' . getUnities()[$devis->getLangue()][$item->getUnite()]  . '</td>
                                <td align="right" style="vertical-align:middle;" class="cost">' . ($item->getDiscount() > 0 ?  '<del>'.number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise().'</del>' : number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise()) . '</td>
                                </tr>';
                                if($item->getDiscount() > 0){
                                    $htmlInvoice .= '<tr>
                                        <td colspan="3" style="color:#4caf50"><strong>'.str_replace(array('text_value','discount_value'),array($item->getTitre(),$item->getDiscount()),$traduction['PRICE_AFTER_DISCOUNT'][$devis->getLangue()]).'</strong></td>
                                        <td align="right"  style="color:#4caf50"><strong>' . number_format($item->getTotal() - $discount , 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
                                    </tr>';
                                }
        }

        $htmlInvoice .= '<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="2" rowspan="6"></td>
<td class="totals">' . $traduction['SSTOTAL_HT'][$devis->getLangue()] . '</td>
<td class="totals cost">' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
</tr>';
        

        // nouveau total HT
        if ($devis->getDiscount() != '') {
            $discoutSign = $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%';
            // test réduction
            if ($devis->getDiscount() == 'percentage') {
                $soustotal = $soustotal - ($soustotal * $devis->getDiscountVal() / 100);
            } elseif ($devis->getDiscount() == 'amount') {
                $soustotal = $soustotal - $devis->getDiscountVal();
            }
            
            $htmlInvoice .= '<tr>
            <td class="totals">' . $traduction['REDUCTION'][$devis->getLangue()] . ' - ' . $devis->getDiscountVal() . $discoutSign . '</td>
            <td class="totals cost"> ' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
            </tr>';
        }

        // Calcule TVA		
        //$tva = $devis->isProforma() ? 0 : $soustotal * $agence->getTva()/100;
        $tva = $devis->getTva();

        $htmlInvoice .= '<tr><td class="totals">' . $traduction['TVA'][$devis->getLangue()] . '</td>
            <td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
        </tr>';

        // if ($devis->getDiscount() != '') {
        //     $htmlInvoice .= '<tr>
        //     <td class="totals">' . $traduction['TOTAL_TTC'][$devis->getLangue()] . '</td>
        //     <td class="totals cost"> ' . number_format($soustotal + $tva, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
        //     </tr>';
        // }
        // if ($devis->getDiscount() != '') {
        //     $discoutSign = $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%';
        //     // test réduction
        //     if ($devis->getDiscount() == 'percentage') {
        //         $soustotal = $soustotal - ($soustotal * $devis->getDiscountVal() / 100);
        //     } elseif ($devis->getDiscount() == 'amount') {
        //         $soustotal = $soustotal - $devis->getDiscountVal();
        //     }

        //     $htmlInvoice .= '<tr>
        //         <td class="totals">' . $traduction['REDUCTION'][$devis->getLangue()] . '</td>
        //         <td class="totals cost">- ' . $devis->getDiscountVal() . $discoutSign . '</td>
        //     </tr>';
        // }
        $totalLabel = $devis->isProforma() ? 'TOTAL' : $traduction['TOTAL_TTC'][$devis->getLangue()];
        $htmlInvoice .= '<tr style="background:'.$color.';">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
        if ($devis->getRemarque() != '') {
            $htmlInvoice .= '<strong>' . $traduction['REMARQUE'][$devis->getLangue()] . ': </strong>' . strip_tags($devis->getRemarque());
        };
        $htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:'.$color.';">' . $traduction['THANK_YOU_FOR'][$devis->getLangue()] .$agence->getNom(). ' !!</h3>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS'][$devis->getLangue()].': </strong>'.$agence->getConditions().'</p>
<p style="font-size:8pt;margin-top:15pt;"><strong>' . $traduction['CONDITIONS_PAYMENT'][$devis->getLangue()] . ': </strong>' . $devis->getConditionPaiment() . '</p>';

// Conditions de paiement
foreach($devis->getConditions() as $condition){
    $htmlInvoice .= '<p>'. $condition->getMontant() .' '. $condition->getCondition() .'</p>';
}

if($devis->getBank()){
        $htmlInvoice .= '<p style="font-size:8pt;"><br>';
        if($devis->getBank()->getRaisonSociale() != '') $htmlInvoice .= '<strong>'.$traduction['RAISON_SOCIALE'][$devis->getLangue()].':</strong> '. $devis->getBank()->getRaisonSociale() .' <br>';
        if($devis->getBank()->getRib() != '') $htmlInvoice .= '<strong>'.$traduction['ACCOUNT_NUMBER'][$devis->getLangue()].':</strong> '. $devis->getBank()->getRib() .' <br>';
        if($devis->getBank()->getIbanNumber() != '') $htmlInvoice .= '<strong>'.$traduction['IBAN'][$devis->getLangue()].':</strong> '. $devis->getBank()->getIbanNumber() .' <br>';
        if($devis->getBank()->getBanque() != '') $htmlInvoice .= '<strong>'.$traduction['BRANCH'][$devis->getLangue()].':</strong> '. $devis->getBank()->getBanque() .' <br>';
        if($devis->getBank()->getCodeSwift() != '') $htmlInvoice .= '<strong>'.$traduction['SWIFT_CODE'][$devis->getLangue()].':</strong> '. $devis->getBank()->getCodeSwift() .' <br>';
        if($devis->getBank()->getCurrency() != '') $htmlInvoice .= '<strong>'.$traduction['CURRENCY'][$devis->getLangue()].':</strong> '. $devis->getBank()->getCurrency() .' <br>';
        
        $htmlInvoice .= '</p>';
    }
    
    $htmlInvoice .= '</div>
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
        
        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($htmlInvoice);

        $file_name = $traduction['DEVIS'][$devis->getLangue()] . ' - ' . str_replace(array('/', '\\'), '-', $invoiceFor) . '.pdf';
        $mpdf->Output($file_name, 'I');
    }
}

// API

function findAllByClientApi($data){
    $deviss = devis::findAllByClientApi($data['client'],false,true,false);
    // Convert array to UTF-8
    array_walk_recursive($deviss, function (&$item) {
        if (is_string($item)) {
            $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
        }
    });
    echo json_encode($deviss);
}

function pdfDevisApi($data)
{
	if (isset($data["id"]) && !empty($data["id"])) {
		
		require '../../../vendor/autoload.php';
		require '../../../includes/traduction.php';
		echo devis::pdfdevisApi($data["id"],	"download");

		
	}
}

function pdfDevisTexte($data, $forEmailAttachment = false)
{
    global $db;
    if (isset($data["id"]) && !empty($data["id"])) {

        require_once '../../../vendor/autoload.php';
        require_once '../../../includes/traduction.php';

        $dirPath = "../../../";

        $devis = devis::find($data["id"], $_SESSION['agence']);
        
        $items = $devis->getItems();
        $client = $devis->getClient();
        $agence = agence::find($_SESSION["agence"],$_SESSION["langue"]);
        $config = new config($db);
        $invoiceFor = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : $client->getNom() . ' ' . $client->getPrenom();
        $color = $agence->getColor();
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
table thead td { background-color: '.$color.';
	text-align: center;
	border-left: 0.1mm solid #FFF;
	border-right: 0.1mm solid #FFF;
	font-weight:bold;
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
.div-signature{
    text-align:right;
}
</style>
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%">
<tr>
	<td><img src="../../../images/agences/' . $agence->getLogo() . '" width="100"></td>
	<td align="right" style="vertical-align: middle;"><strong style="font-size: 8pt;"><br><br>' . $agence->getAdresse() . '</strong><br>
	<p style="font-size: 8pt;"><strong>t:</strong> ' . $agence->getTel() . '  |  <strong>e:</strong> ' . $agence->getEmail() . ' | <strong>w:</strong> '.$agence->getWebsite().'</p></td>
</tr>
</table>
<hr>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #CCC; font-size: 9pt; text-align: center; padding-top: 3mm; ">';

if($agence->getIce() != ''){
$htmlInvoice .= '<p style="font-size:8pt;"><strong>IF</strong> '.$agence->getIf().' | <strong>TP</strong> '.$agence->getTp().' | <strong>RC</strong> '.$agence->getRc().' | <strong>ICE</strong> '.$agence->getIce().'</p>';
}

$htmlInvoice .= '<div style="margin-top:5pt;">Page {PAGENO} ' . $traduction['SUR'][$devis->getLangue()] . ' {nb}</div>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->
<table width="100%">
<tr>
<td width="35%" style="vertical-align: middle; font-size:8pt;">' . $traduction['DEVIS_POUR'][$devis->getLangue()] . '<hr style="margin:1pt 0 6pt 0;"><span style="font-weight: bold; font-size: 10pt; color:'.$color.'">' . $invoiceFor . '</span><br /><span style="font-family:dejavusanscondensed;">&#9742;</span> ' . $client->getTel() . '<br>' . $client->getEmail() . '<br>' . $client->getICE() . '<br /></td>
<td width="30%"></td>

<td width="35%" style="text-align: right;">

<table style="margin-bottom:5pt;">
<tr><td style="font-size:8pt;">' . $traduction['DATE_DEVIS'][$devis->getLangue()] . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . normaldate2($devis->getDateDevis(),$devis->getLangue()) . '</strong></td></tr>
</table>

<table>
<tr><td style="font-size:8pt;">N° ' . $traduction['DEVIS'][$devis->getLangue()] . '</td></tr>
<tr><td style="border-top:#e3d3aa solid 0.5pt;"><strong style="font-size: 12pt;">' . $devis->getNumero() . '</strong></td></tr>
</table>
</td>
</tr></table>
<br />
<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
<thead>
<tr>
<td style="text-align:left;" colspan="3">Prestations</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->';
        $soustotal = 0;
        foreach ($items as $item) {
            $discount = $item->getDiscount()>0 ? ($item->getDiscount() * $item->getTotal()) / 100 : 0;
            $soustotal += ($item->getTotal() - $discount);
            //$pricePer = $item->getService()->getFacturation() == 'periodique' ?  'x '. $item->getQte() . ' ' . getUnities()[$devis->getLangue()][$item->getUnite()] : '';
            //$price = $item->getService()->getFacturation() == 'periodique' ? ' '.number_format($item->getPrix(), 2, ',', ' ') . ' ' . $devis->getDevise() .' '. $par : ($item->getDiscount() > 0 ?  '<del>'.number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise().'</del>' : number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise());
            $pricePer = ' x <strong>'. $item->getQte() . '</strong> ' . getUnities()[$devis->getLangue()][$item->getUnite()] . ' = ' . number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise();
            $price = number_format($item->getPrix(), 2, ',', ' ') . ' ' . $devis->getDevise();
            
            // test packages
            if($devis->isPack()){
                $pricePer = '';
                $price = number_format($item->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise();
            }
            $htmlInvoice .= '<tr>
                                <td colspan="3"><strong>' . $item->getTitre() . '</strong><div style="font-size:8pt; color:#999">' .  $item->getDescription() . '</div></td>
                            </tr>
                            <tr bgcolor="#e2f6ed">
                                <td colspan="3" style="font-size:7pt">' . ($devis->isPack() ? $traduction['COUT_PRESTATION_PACK'][$devis->getLangue()] : $traduction['COUT_PRESTATION'][$devis->getLangue()]) . ' <strong>' . $price . '</strong>'.$pricePer.'</td>
                            </tr>';
                            
                            if(!$devis->isPack()){
                                if($item->getService()->getFacturation() == 'unique'){
                                    $htmlInvoice .= '<tr bgcolor="#e2f6ed"><td colspan="3" style="font-size:7pt">' . $traduction['REGLEMENT_SEUL_FOIS'][$devis->getLangue()] . '</td></tr>';
                                }else{
                                    $htmlInvoice .= '<tr bgcolor="#e2f6ed"><td colspan="3" style="font-size:7pt">' . $traduction['REGLEMNT_PERIODE'][$devis->getLangue()] . ' ('.$item->getQte() . ' '. getUnities()[$devis->getLangue()][$item->getUnite()] .')</td></tr>';
                                }
                            }
                            
                            if($item->getDiscount() > 0){
                                $htmlInvoice .= '<tr>
                                    <td style="color:#4caf50"><strong>'.str_replace(array('text_value','discount_value'),array($item->getTitre(),$item->getDiscount()),$traduction['PRICE_AFTER_DISCOUNT'][$devis->getLangue()]).'</strong></td>
                                    <td align="right" style="color:#4caf50" colspan="2"><strong>' . number_format($item->getTotal() - $discount , 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
                                </tr>';
                            }
        }

        if(!$devis->isPack()){
        $htmlInvoice .= '<!-- END ITEMS HERE -->
        <tr>
        <td class="blanktotal" rowspan="6"></td>
        <td class="totals">' . $traduction['SSTOTAL_HT'][$devis->getLangue()] . '</td>
        <td class="totals cost">' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
        </tr>';
        }
        

        // nouveau total HT
        if ($devis->getDiscount() != '') {
            $discoutSign = $devis->getDiscount() == 'amount' ? ' ' . $devis->getDevise() : '%';
            // test réduction
            if ($devis->getDiscount() == 'percentage') {
                $soustotal = $soustotal - ($soustotal * $devis->getDiscountVal() / 100);
            } elseif ($devis->getDiscount() == 'amount') {
                $soustotal = $soustotal - $devis->getDiscountVal();
            }
            
            $htmlInvoice .= '<tr>
            <td class="totals">' . $traduction['REDUCTION'][$devis->getLangue()] . ' - ' . $devis->getDiscountVal() . $discoutSign . '</td>
            <td class="totals cost"> ' . number_format($soustotal, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
            </tr>';
        }

        // Calcule TVA		
        //$tva = $devis->isProforma() ? 0 : $soustotal * $agence->getTva()/100;
        $tva = $devis->getTva();

        $htmlInvoice .= '<tr>';
        if($devis->isPack()) $htmlInvoice .= '<td class="blanktotal" rowspan="6"></td>';
        $htmlInvoice .= '<td class="totals">' . $traduction['TVA'][$devis->getLangue()] . '</td>
            <td class="totals cost">' . number_format($tva, 2, ',', ' ') . ' ' . $devis->getDevise() . '</td>
        </tr>';

        $totalLabel = $devis->isProforma() ? 'TOTAL' : $traduction['TOTAL_TTC'][$devis->getLangue()];
        $htmlInvoice .= '<tr style="background:'.$color.';">
<td class="totals" style="color:#FFF; border-right:0.1mm solid #e3d3aa;"><b>' . $totalLabel . '</b></td>
<td class="totals cost" style="color:#FFF;"><strong>' . number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise() . '</strong></td>
</tr>
</tbody>
</table><div style="margin-top:50t;"><p style="font-size:8pt;">';
        if ($devis->getRemarque() != '') {
            $htmlInvoice .= '<strong>' . $traduction['REMARQUE'][$devis->getLangue()] . ': </strong>' . strip_tags($devis->getRemarque());
        };
        $htmlInvoice .= '</p></div>
<div style="margin-top:100t;">
<h3 style="color:'.$color.';">' . $traduction['THANK_YOU_FOR'][$devis->getLangue()] .$agence->getNom(). ' !!</h3>
<p style="font-size:8pt;"><strong>'.$traduction['CONDITIONS'][$devis->getLangue()].': </strong>'.$agence->getConditions().'</p>
<p style="font-size:8pt;margin-top:15pt;"><strong>' . $traduction['CONDITIONS_PAYMENT'][$devis->getLangue()] . ': </strong>' . $devis->getConditionPaiment() . '</p>';

// Conditions de paiement
foreach($devis->getConditions() as $condition){
    $htmlInvoice .= '<p>'. $condition->getMontant() .' '. $condition->getCondition() .'</p>';
}

if($devis->getBank()){
        $htmlInvoice .= '<p style="font-size:8pt;"><br>';
        if($devis->getBank()->getRaisonSociale() != '') $htmlInvoice .= '<strong>'.$traduction['RAISON_SOCIALE'][$devis->getLangue()].':</strong> '. $devis->getBank()->getRaisonSociale() .' <br>';
        if($devis->getBank()->getRib() != '') $htmlInvoice .= '<strong>'.$traduction['ACCOUNT_NUMBER'][$devis->getLangue()].':</strong> '. $devis->getBank()->getRib() .' <br>';
        if($devis->getBank()->getIbanNumber() != '') $htmlInvoice .= '<strong>'.$traduction['IBAN'][$devis->getLangue()].':</strong> '. $devis->getBank()->getIbanNumber() .' <br>';
        if($devis->getBank()->getBanque() != '') $htmlInvoice .= '<strong>'.$traduction['BRANCH'][$devis->getLangue()].':</strong> '. $devis->getBank()->getBanque() .' <br>';
        if($devis->getBank()->getCodeSwift() != '') $htmlInvoice .= '<strong>'.$traduction['SWIFT_CODE'][$devis->getLangue()].':</strong> '. $devis->getBank()->getCodeSwift() .' <br>';
        if($devis->getBank()->getCurrency() != '') $htmlInvoice .= '<strong>'.$traduction['CURRENCY'][$devis->getLangue()].':</strong> '. $devis->getBank()->getCurrency() .' <br>';
        
        $htmlInvoice .= '</p>';
    }
    
    /*$htmlInvoice .= '<div class="div-signature" style="padding-top: 50px;padding-bottom: 50px;">
                                <img src="' . $dirPath . 'images/agences/' . $agence->getSignature() . '" width="300">
                            </div>';*/
    
    $htmlInvoice .= '</div>
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
        
        $htmlInvoice = mb_convert_encoding($htmlInvoice, 'UTF-8', 'UTF-8');
        $mpdf->WriteHTML($htmlInvoice);

        $file_name = $traduction['DEVIS'][$devis->getLangue()] . ' - ' . str_replace(array('/', '\\'), '-', $invoiceFor) . '.pdf';
        $file_path = $dirPath . 'uploads/' . $file_name;
        if ($forEmailAttachment) {
            $mpdf->Output($file_path, 'F');
            return realpath($file_path);
        }
        $mpdf->Output($file_name, 'I');
    }
}

// Endpoint public (Slack Events API) : Slack POST ici un JSON à chaque message
// écrit dans le channel où le bot est invité. Gère le handshake de vérification
// d'URL et, pour un vrai message, détecte un numéro de devis (10 chiffres) +
// un mot-clé d'action (valide / accepté / refusé / contrat).
function slackEventWebhook()
{
    header('Content-Type: application/json');

    $rawBody = file_get_contents('php://input');
    $payload = json_decode($rawBody, true);

    if (!is_array($payload)) {
        echo json_encode(array('ok' => true));
        return;
    }

    if (isset($payload['type']) && $payload['type'] === 'url_verification') {
        echo json_encode(array('challenge' => isset($payload['challenge']) ? $payload['challenge'] : ''));
        return;
    }

    // Slack relivre le même événement si on ne répond pas assez vite : on ignore les relivraisons
    // pour ne pas ré-envoyer l'email / re-changer le statut plusieurs fois pour un même message.
    if (!empty($_SERVER['HTTP_X_SLACK_RETRY_NUM'])) {
        echo json_encode(array('ok' => true));
        return;
    }

    if (!isset($payload['type']) || $payload['type'] !== 'event_callback' || !isset($payload['event'])) {
        echo json_encode(array('ok' => true));
        return;
    }

    $event = $payload['event'];

    if (
        !isset($event['type']) || $event['type'] !== 'message'
        || !empty($event['bot_id'])
        || !empty($event['subtype'])
        || !isset($event['text'])
    ) {
        echo json_encode(array('ok' => true));
        return;
    }

    $text = $event['text'];
    $channel = isset($event['channel']) ? $event['channel'] : '';
    $threadTs = isset($event['ts']) ? $event['ts'] : null;

    if (!preg_match('/\b(\d{10})\b/', $text, $matches)) {
        echo json_encode(array('ok' => true));
        return;
    }
    $numero = $matches[1];

    $devis = devis::findByNumero($numero);
    if (!$devis->getId()) {
        slackPostMessage($channel, ":warning: Devis N°" . $numero . " introuvable.", $threadTs);
        echo json_encode(array('ok' => true));
        return;
    }

    // "Utilisateur système" pour les champs id_user_edited / last_edit lors des mises à jour
    $_SESSION['user'] = user::find(defined('SLACK_BOT_ACTING_USER_ID') ? SLACK_BOT_ACTING_USER_ID : 5);

    $textLower = mb_strtolower($text);

    if (strpos($textLower, 'refus') !== false) {
        $devis->setStatu(5);
        $devis->setUserEdited($_SESSION['user']);
        $devis->setLastEdit(date('Y-m-d H:i:s'));
        $devis->edit();
        slackPostMessage($channel, ":x: Devis N°" . $numero . " marqué comme *Devis Refusé*.", $threadTs);
    } elseif (strpos($textLower, 'accept') !== false) {
        $devis->setStatu(2);
        $devis->setUserEdited($_SESSION['user']);
        $devis->setLastEdit(date('Y-m-d H:i:s'));
        $devis->edit();
        slackPostMessage($channel, ":white_check_mark: Devis N°" . $numero . " marqué comme *Accepté*.", $threadTs);
    } elseif (strpos($textLower, 'valide') !== false) {
        $devis->setStatu(1);
        $devis->setUserEdited($_SESSION['user']);
        $devis->setLastEdit(date('Y-m-d H:i:s'));
        $devis->edit();

        $mailResult = sendDevisPdfEmailToClient($devis);
        if ($mailResult === true) {
            slackPostMessage($channel, ":email: Devis N°" . $numero . " validé (statut *Envoyé*) et le PDF a été envoyé par email à " . $devis->getClient()->getEmail() . ".", $threadTs);
        } else {
            slackPostMessage($channel, ":warning: Devis N°" . $numero . " marqué *Envoyé*, mais l'email n'a pas pu être envoyé : " . $mailResult, $threadTs);
        }
    } elseif (strpos($textLower, 'contrat') !== false) {
        slackPostMessage($channel, ":bell: Reçu, merci de préparer le contrat pour le devis N°" . $numero . ".", $threadTs);
    }

    echo json_encode(array('ok' => true));
}

function slackPostMessage($channel, $text, $threadTs = null)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '' || empty($channel)) {
        return false;
    }
    $payload = array('channel' => $channel, 'text' => $text);
    if ($threadTs) {
        $payload['thread_ts'] = $threadTs;
    }
    $ch = curl_init('https://slack.com/api/chat.postMessage');
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . SLACK_BOT_TOKEN
        ),
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    return is_array($decoded) && !empty($decoded['ok']);
}

function sendDevisPdfEmailToClient($devis)
{
    require_once '../../../vendor/autoload.php';

    $client = $devis->getClient();
    if (!$client->getEmail()) {
        return "le client n'a pas d'adresse email.";
    }
    if (!defined('SMTP_HOST') || SMTP_HOST == '') {
        return "SMTP non configuré (config.secrets.php).";
    }

    $pdfPath = pdfDevisTexte(array('id' => $devis->getId()), true);
    if (!$pdfPath || !file_exists($pdfPath)) {
        return "le PDF n'a pas pu être généré.";
    }

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

        $espaceClientLink = espaceClientLink($_SESSION['agence']);

        $mail->setFrom(SMTP_USERNAME, 'Hello World');
        $mail->addAddress($client->getEmail(), trim($client->getPrenom() . ' ' . $client->getNom()));
        $mail->addCC('contact@helloworld-agency.com');
        $mail->addAttachment($pdfPath, 'Devis-' . $devis->getNumero() . '.pdf');

        $mail->isHTML(true);
        $isEn = ($devis->getLangue() == 'en');

        if ($isEn) {
            $mail->Subject = "Your quote N°" . $devis->getNumero() . " is ready!";
            $mail->Body = "Hello " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "We hope you are doing well! ✨<br><br>"
                . "We are pleased to send you your quote N°" . $devis->getNumero() . " as an attachment.<br><br>"
                . "Handy reminder: you can find this quote, along with all your quotes and invoices, at any time from your client portal: <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a> (also available from our mobile app).<br><br>"
                . "Feel free to reach out with any question, we are always happy to help.<br><br>"
                . "Have a great day,<br>The Hello World team";
            $mail->AltBody = "Hello " . $client->getPrenom() . ", please find attached your quote N°" . $devis->getNumero() . ". Client portal: " . $espaceClientLink;
        } else {
            $mail->Subject = "Votre devis N°" . $devis->getNumero() . " est prêt !";
            $mail->Body = "Bonjour " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "Nous espérons que vous allez bien ! ✨<br><br>"
                . "C'est avec grand plaisir que nous vous transmettons votre devis N°" . $devis->getNumero() . " en pièce jointe.<br><br>"
                . "Petit rappel utile : vous pouvez à tout moment retrouver ce devis, ainsi que l'ensemble de vos devis et factures, directement depuis votre espace client : <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a> (également accessible depuis notre application mobile).<br><br>"
                . "N'hésitez pas à revenir vers nous pour la moindre question, nous sommes toujours ravis de vous accompagner.<br><br>"
                . "Belle journée à vous,<br>L'équipe Hello World";
            $mail->AltBody = "Bonjour " . $client->getPrenom() . ", veuillez trouver ci-joint votre devis N°" . $devis->getNumero() . ". Espace client : " . $espaceClientLink;
        }

        $mail->send();
        copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
        return true;
    } catch (\Exception $e) {
        return $mail->ErrorInfo;
    }
}

// Devis passé à "Accepté" : email professionnel automatique de remerciement, avec le montant à
// régler, les conditions de paiement, et le contrat à signer s'il y en a un pour ce devis. Rédigé
// dans la langue du devis (fr/en) puisque c'est ce qui détermine la langue de tous les emails client.
function sendDevisAcceptedEmailToClient($devis)
{
    if (!defined('SMTP_HOST') || SMTP_HOST == '') {
        return "SMTP non configuré (config.secrets.php).";
    }
    $client = $devis->getClient();
    if (!$client->getEmail()) {
        return "le client n'a pas d'adresse email.";
    }

    require_once '../../../vendor/autoload.php';

    $espaceClientLink = espaceClientLink($_SESSION['agence']);
    $isEn = ($devis->getLangue() == 'en');

    $conditions = $devis->getConditions();
    $conditionsHtml = '';
    foreach ($conditions as $condition) {
        $conditionsHtml .= "<li>" . htmlspecialchars($condition->getMontant()) . " — " . htmlspecialchars($condition->getCondition()) . "</li>";
    }
    if ($conditionsHtml == '' && $devis->getConditionPaiment() != '') {
        $conditionsHtml = "<li>" . $devis->getConditionPaiment() . "</li>";
    }

    $contract = contract::findByDevis($devis->getId(), $_SESSION['agence'], $devis->getLangue());
    $hasContractToSign = ($contract->getId() && $contract->getStatus() != 3);

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

        $montant = number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise();

        if ($isEn) {
            $mail->Subject = "Thank you for your trust — Quote N°" . $devis->getNumero() . " accepted";
            $mail->Body = "Hello " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "Thank you for your trust and for validating quote N°" . $devis->getNumero() . "! We are excited to get started.<br><br>"
                . "Amount to be paid: <strong>" . $montant . "</strong><br><br>"
                . ($conditionsHtml != '' ? "Payment terms:<ul>" . $conditionsHtml . "</ul><br>" : "")
                . ($hasContractToSign ? "A contract has also been prepared for this quote and is awaiting your signature.<br><br>" : "")
                . "You can find all of this (quote, payment terms" . ($hasContractToSign ? ", contract" : "") . ") from your client portal: <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a> (also available from our mobile app).<br><br>"
                . "Feel free to reach out with any question, we are always happy to help.<br><br>"
                . "Have a great day,<br>The Hello World team";
            $mail->AltBody = "Hello " . $client->getPrenom() . ", thank you for validating quote N°" . $devis->getNumero() . ". Amount to be paid: " . $montant . ". Client portal: " . $espaceClientLink;
        } else {
            $mail->Subject = "Merci pour votre confiance — Devis N°" . $devis->getNumero() . " accepté";
            $mail->Body = "Bonjour " . htmlspecialchars($client->getPrenom()) . ",<br><br>"
                . "Merci pour votre confiance et pour la validation du devis N°" . $devis->getNumero() . " ! Nous sommes ravis de démarrer avec vous.<br><br>"
                . "Montant à régler : <strong>" . $montant . "</strong><br><br>"
                . ($conditionsHtml != '' ? "Conditions de paiement :<ul>" . $conditionsHtml . "</ul><br>" : "")
                . ($hasContractToSign ? "Un contrat a également été préparé pour ce devis et est en attente de votre signature.<br><br>" : "")
                . "Vous pouvez retrouver tout ceci (devis, conditions de paiement" . ($hasContractToSign ? ", contrat" : "") . ") depuis votre espace client : <a href=\"" . $espaceClientLink . "\">" . $espaceClientLink . "</a> (également accessible depuis notre application mobile).<br><br>"
                . "N'hésitez pas à revenir vers nous pour la moindre question, nous sommes toujours ravis de vous accompagner.<br><br>"
                . "Belle journée à vous,<br>L'équipe Hello World";
            $mail->AltBody = "Bonjour " . $client->getPrenom() . ", merci pour la validation du devis N°" . $devis->getNumero() . ". Montant à régler : " . $montant . ". Espace client : " . $espaceClientLink;
        }

        $mail->send();
        copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
        return true;
    } catch (\Exception $e) {
        return $mail->ErrorInfo;
    }
}

// Validation d'un devis depuis Slack, en INTERROGEANT nous-mêmes l'API Slack (polling) plutôt
// qu'en attendant que Slack nous appelle (voir slackEventWebhook() ci-dessus) : ce dernier utilise
// l'Events API, qui exige que Slack puisse atteindre notre serveur en HTTPS public - impossible
// depuis ce déploiement local (http://localhost/hw_crm/), donc ce webhook ne se déclenche jamais
// ici en pratique. Ce cron reproduit le même principe que
// com_resourcehumaine/controleurs/joboffer/controleur.php::cronVerifierValidationSlackEndpoint() -
// appelé périodiquement par un service externe (cron-job.org), il va lui-même chercher l'historique
// du canal, ce qui fonctionne même en local puisque c'est le CRM qui initie la connexion sortante.
// Réutilise volontairement la même reconnaissance (numéro à 10 chiffres) et le même effet
// (statut Envoyé + sendDevisPdfEmailToClient()) que slackEventWebhook(), pour rester cohérent avec
// ce qui existait déjà - seule la détection du déclencheur change (polling vs push).
function cronVerifierValidationDevisSlackEndpoint()
{
    header('Content-Type: application/json');
    $provided = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('DEVIS_VALIDATION_CRON_SECRET') || DEVIS_VALIDATION_CRON_SECRET === '' || !hash_equals(DEVIS_VALIDATION_CRON_SECRET, (string) $provided)) {
        error_log('cronVerifierValidationDevisSlackEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    // Aucune session utilisateur active dans ce contexte (appel externe direct) - nécessaire pour
    // les find()/edit() ci-dessous, qui lisent $_SESSION['user']/$_SESSION['agence']. L'agence est
    // ensuite corrigée automatiquement à la bonne valeur par devis::findByNumero() une fois le
    // devis trouvé (il la déduit du client propriétaire), donc "1" ici n'est qu'un point de départ.
    bootstrapSystemSession(RELANCE_CRON_ACTING_USER_ID, 1, 'fr');
    $_SESSION['user'] = user::find(defined('SLACK_BOT_ACTING_USER_ID') ? SLACK_BOT_ACTING_USER_ID : RELANCE_CRON_ACTING_USER_ID);

    $resume = array('traites' => 0, 'deja_traites' => 0, 'introuvables' => 0, 'details' => array());

    $nomCanal = defined('SLACK_CHANNEL_COMMERCIAL') ? SLACK_CHANNEL_COMMERCIAL : 'commercial-hw';
    $channelId = devisSlackResolveChannelId($nomCanal);
    if (!$channelId) {
        echo json_encode(array('error' => 'Canal Slack "' . $nomCanal . '" introuvable ou bot absent du canal'));
        return;
    }

    foreach (devisSlackFetchHistory($channelId, 50) as $msg) {
        if (empty($msg['text']) || !empty($msg['bot_id'])) {
            continue;
        }
        $texteMsg = trim($msg['text']);

        // "je ne valide pas .../je refuse ..." vérifié en premier pour ne jamais déclencher une
        // validation sur un message qui contient pourtant le mot "valide" par la négative.
        if (preg_match('/je\s+(?:ne\s+valide\s+pas|refuse)\s+le\s+devis/iu', $texteMsg)) {
            continue;
        }
        if (!preg_match('/je\s+valide\s+le\s+devis\D{0,15}(\d{10})/iu', $texteMsg, $m)) {
            continue;
        }
        $numero = $m[1];

        $devis = devis::findByNumero($numero);
        if (!$devis->getId()) {
            $resume['introuvables']++;
            $resume['details'][] = array('numero' => $numero, 'action' => 'introuvable');
            continue;
        }

        // Retrouvé "à neuf" juste avant d'agir : si son statut a déjà avancé (déjà traité par un
        // run précédent du cron - le même message reste dans les 50 derniers un moment - ou changé
        // manuellement entre-temps), on ne repasse jamais dessus, jamais de second envoi.
        if ($devis->getStatu() != 0) {
            $resume['deja_traites']++;
            continue;
        }

        $devis->setUserEdited($_SESSION['user']);
        $devis->setLastEdit(date('Y-m-d H:i:s'));
        $devis->setStatu(1);
        $devis->edit();

        $mailResult = sendDevisPdfEmailToClient($devis);
        $threadTs = isset($msg['ts']) ? $msg['ts'] : null;
        if ($mailResult === true) {
            slackPostMessage($channelId, ":email: Devis N°" . $numero . " validé (statut *Envoyé*) et le PDF a été envoyé par email à " . $devis->getClient()->getEmail() . ".", $threadTs);
        } else {
            slackPostMessage($channelId, ":warning: Devis N°" . $numero . " marqué *Envoyé*, mais l'email n'a pas pu être envoyé : " . $mailResult, $threadTs);
        }

        $resume['traites']++;
        $resume['details'][] = array('numero' => $numero, 'action' => 'envoye', 'email_ok' => $mailResult === true);
    }

    echo json_encode($resume);
}

// Résout le nom d'un canal Slack (ex: "commercial-hw") vers son ID (ex: "C0123456789") -
// conversations.history exige l'ID, contrairement à chat.postMessage qui accepte un nom brut.
// Copié depuis com_resourcehumaine::joboffreSlackResolveChannelId() (même convention dans ce
// codebase : chaque module garde ses propres copies locales des helpers Slack plutôt qu'un
// fichier partagé).
function devisSlackResolveChannelId($nomCanal)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '') {
        return null;
    }
    $nomCanal = ltrim($nomCanal, '#');
    $cursor = '';
    for ($page = 0; $page < 5; $page++) {
        $url = 'https://slack.com/api/conversations.list?types=public_channel,private_channel&limit=200' . ($cursor ? '&cursor=' . urlencode($cursor) : '');
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . SLACK_BOT_TOKEN),
            CURLOPT_TIMEOUT => 15
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($response, true);
        if (!is_array($decoded) || empty($decoded['ok']) || empty($decoded['channels'])) {
            return null;
        }
        foreach ($decoded['channels'] as $c) {
            if (strcasecmp($c['name'], $nomCanal) === 0) {
                return $c['id'];
            }
        }
        $cursor = isset($decoded['response_metadata']['next_cursor']) ? $decoded['response_metadata']['next_cursor'] : '';
        if (!$cursor) {
            break;
        }
    }
    return null;
}

// Copié depuis com_resourcehumaine::joboffreSlackFetchHistory() - mêmes raisons (voir ci-dessus).
function devisSlackFetchHistory($channelId, $limit = 50)
{
    if (!defined('SLACK_BOT_TOKEN') || SLACK_BOT_TOKEN == '') {
        return array();
    }
    $ch = curl_init('https://slack.com/api/conversations.history?channel=' . urlencode($channelId) . '&limit=' . intval($limit));
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . SLACK_BOT_TOKEN),
        CURLOPT_TIMEOUT => 15
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($response, true);
    if (!is_array($decoded) || empty($decoded['ok'])) {
        return array();
    }
    return isset($decoded['messages']) ? $decoded['messages'] : array();
}
