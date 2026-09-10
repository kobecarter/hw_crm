<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addRelance':
            addRelance($_POST);
            break;
        case 'editRelance':
            editRelance($_POST);
            break;
        case 'deleteRelance':
            deleteRelance($_POST);
            break;   
        case 'prolongerRelance':
            prolongerRelance($_POST);
            break;  
        case 'getFactureByClient':
            getFactureByClient($_POST);
            break;
        case 'envoyerRelanceManuelle':
            envoyerRelanceManuelle($_POST);
            break;
    }
}

// Déclenché depuis com_facture/views/facture/payment.php (bouton "Envoyer une relance" sur
// chaque ligne de règlement sans preuve jointe) - envoie immédiatement un rappel annonçant le
// montant DE CE RÈGLEMENT précis (pas le reste dû global de la facture), voir
// relance::envoyerRelanceManuelle(). id_payment (pas id_facture) : le montant vient du paiement
// lui-même, retrouvé via payment::find() (même restriction d'accès - superuser ou payment ajouté
// par l'utilisateur courant - que la liste affichée sur cette page). Répond en JSON (pas juste
// "1"/"0") pour pouvoir afficher le message d'erreur précis (facture déjà soldée, client sans
// email...) plutôt qu'un message générique.
function envoyerRelanceManuelle($data)
{
    header('Content-Type: application/json');
    $indices = array("id_payment");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => "Règlement manquant."));
        return;
    }
    $payment = payment::find(intval($data['id_payment']));
    if (!$payment || $payment->getId() == 0) {
        echo json_encode(array('success' => 0, 'message' => "Règlement introuvable."));
        return;
    }
    $facture = $payment->getFacture();
    try {
        relance::envoyerRelanceManuelle($facture, $payment->getMontant());
        echo json_encode(array('success' => 1));
    } catch (\Throwable $e) {
        error_log('envoyerRelanceManuelle - paiement ' . $payment->getId() . ' - ' . $e->getMessage());
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
    }
}

function getFactureByClient($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $factures = facture::ofClient($data['id'], false, false, true);
        ?>
        <select class="chosen-select" name="facture" required>
            <option value="">Sélectionner</option>
            <?php foreach ($factures as $facture) : ?>
                <?php if($facture->getReste() > 0): ?>
                <option value="<?php echo $facture->getId() ?>"><?php echo $facture->getNumero() . ' - ' . normaldate($facture->getDateFacture()) . ' ('. number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() .')'; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <?php
    }
}
function prolongerRelance($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {

        $relance = relance::find($data['id'],$_SESSION['agence']);

        $btnText = "Prolonger la relance";
        $action = "components/com_relance/controleurs/router.php?task=addRelance";
        ?>
        <form method="post" action="<?php echo $action; ?>" id="relanceForm" enctype="multipart/form-data">
            <input type="hidden" name="client" value="<?php echo $relance->getClient()->getId(); ?>">
            <input type="hidden" name="type" value="<?php echo $relance->getType(); ?>">
            <input type="hidden" name="remarque" value="">

            <div class="msgbox"></div>

            <div class="form-group">
                <label>Date de relance<span class="text-danger"> * </span></label>
                <input type="datetime-local" class="form-control" name="date" value="<?php if (isset($relance)) echo $relance->getDate(); ?>" required>
            </div>

            <div class="submit-section">
                <button class="btn btn-primary submit-btn submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $btnText; ?></button>
            </div>
        </form>
        <script>
            $(function() {

                // envoi du formulaire en ajax
                $('form#relanceForm').ajaxForm({
                    beforeSubmit: function() {
                        $("#relanceForm .loading").css('display', 'inline-block');
                    },
                    success: function(theResponse) {
                        $("#relanceForm .loading").fadeOut();

                        var msgsucces = "Relance ajoutée avec succès";

                        if (parseInt(theResponse) === 1) {
                            $('#relanceForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                            setTimeout(function() {
                                document.location.reload();
                            }, 1500)

                        } else if (parseInt(theResponse) === 0) {
                            $('#relanceForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                        } else {
                            $('#relanceForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                        }
                    }
                });
            })
        </script>
<?php
    }
}

function addRelance($data)
{
    $indices = array("client",'type', "date");
    if (fieldCheck($data, $indices)) {
        if (buildrelance($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editRelance($data)
{
    $indices = array("id", "client", "type", "date");
    if (fieldCheck($data, $indices)) {
        if (buildrelance($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteRelance($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $id = $data["id"];
        $relance = relance::find($id,$_SESSION['agence']);
        if ($relance->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildrelance($data, $id = null)
{
    $relance = new relance();

    if ($id) {
        $relance = relance::find($id,$_SESSION['agence']);
    }

    $photo = array();
    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]!=''){
        $photo = uploadFiles('photo','../../../images/relances/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
    }
	
	if(isset($photo[0])) {
		$relance->setPhoto($photo[0]);
	}

    $relance->setClient(client::find($data['client'],$_SESSION['agence']));
    $relance->setFacture(facture::find($data['facture'],$_SESSION['agence']));
    $relance->setType($data['type']);
    $relance->setDate($data['date']);
    $relance->setRemarque($data['remarque']);
    $relance->setTraite(isset($data['traite']) ? 1 : 0);
    $relance->setDateAdd(date('Y-m-d'));
    $relance->setLastEdit(date('Y-m-d'));
    return $relance;
}

