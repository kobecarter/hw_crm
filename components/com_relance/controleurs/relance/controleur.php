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
        case 'getListeSelonCategorie':
            getListeSelonCategorie($_POST);
            break;
        case 'envoyerRelanceManuelle':
            envoyerRelanceManuelle($_POST);
            break;
        case 'genererEmailRelance':
            genererEmailRelance($_POST);
            break;
        case 'enregistrerRelanceEmail':
            enregistrerRelanceEmail($_POST);
            break;
    }
}

// Rédaction IA du corps de l'email (voir aiRelanceEmail::generate()) pour la catégorie choisie
// dans le formulaire d'ajout de relance (facture/paiement/expiration domaine/hébergement/autre
// service) - affiché ensuite dans un éditeur pour relecture avant envoi/programmation, jamais
// envoyé directement depuis cette tâche.
function genererEmailRelance($data)
{
    header('Content-Type: application/json');
    $indices = array("client", "categorie");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => "Client et catégorie requis."));
        return;
    }
    $client = client::find(intval($data['client']), $_SESSION['agence']);
    if ($client->getId() == 0) {
        echo json_encode(array('success' => 0, 'message' => "Client introuvable."));
        return;
    }
    $facture = isset($data['facture']) && !empty($data['facture']) ? facture::find(intval($data['facture']), $_SESSION['agence']) : new facture();
    // Catégories "expiration_domaine"/"hebergement" : désignation + date d'expiration transmises
    // directement par le JS (déjà affichées dans la liste déroulante "Domaine"/"Hébergement", voir
    // getListeSelonCategorie()) plutôt que relues en base ici - évite un aller-retour supplémentaire
    // pour une info déjà sous la main côté formulaire.
    $domaineInfo = array(
        'domaine' => isset($data['domaine']) ? $data['domaine'] : null,
        'expiration' => isset($data['date_expiration']) ? $data['date_expiration'] : null,
    );

    try {
        $corps = aiRelanceEmail::generate($client, $facture, $data['categorie'], $_SESSION['langue'], $domaineInfo);
        echo json_encode(array(
            'success' => 1,
            'sujet' => aiRelanceEmail::libelleCategorie($data['categorie']),
            'corps' => $corps,
        ));
    } catch (\Throwable $e) {
        error_log('genererEmailRelance - ' . $e->getMessage());
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
    }
}

// Enregistre la relance (nouvelle ou existante via $data['id']) avec son sujet/corps d'email
// relus/corrigés dans le formulaire, puis selon $data['mode'] : 'immediat' envoie tout de suite
// (traite=1/date_envoi posé), 'programme' se contente d'enregistrer (envoyée plus tard par le
// cron envoyerRelancesProgrammeesCron, voir relance::envoyerRelancesProgrammees()). Un échec
// d'envoi en mode immédiat NE supprime PAS la relance déjà créée - elle reste visible comme non
// traitée, à renvoyer manuellement plutôt que silencieusement perdue.
function enregistrerRelanceEmail($data)
{
    $indices = array("client", "categorie", "email_sujet", "email_corps", "date", "mode");
    if (!fieldCheck($data, $indices)) {
        echo "0";
        return;
    }

    $data['type'] = 'Email'; // ce flux envoie toujours un email, quoi que contienne le formulaire
    $idExistant = isset($data['id']) && !empty($data['id']) ? $data['id'] : null;
    $relance = buildrelance($data, $idExistant);
    $ok = $idExistant ? $relance->edit() == 1 : $relance->add() == 1;
    if (!$ok) {
        echo "2";
        return;
    }
    if (!$idExistant) {
        $relance->setId(relance::getLastId());
    }

    if ($data['mode'] === 'immediat') {
        try {
            relance::envoyerEmailPersonnalise($relance, $data['email_sujet'], $data['email_corps']);
            $relance->setTraite(1);
            $relance->setDateEnvoi(date('Y-m-d H:i:s'));
            $relance->edit();
            echo "1|envoye";
        } catch (\Throwable $e) {
            error_log('enregistrerRelanceEmail - envoi immédiat - relance ' . $relance->getId() . ' - ' . $e->getMessage());
            echo "1|erreur_envoi";
        }
    } else {
        echo "1|programme";
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

// Liste dynamique affichée sous "Catégorie" dans le formulaire relance, selon le couple
// client+catégorie choisi : factures impayées (catégorie "facture"), règlements du client
// (catégorie "paiement"), ou rappels de domaine/hébergement du client (catégories
// "expiration_domaine"/"hebergement" - rappel::getType() === 'domaine'|'hosting', même table
// crm_rappel, seul le filtre de type change). La catégorie "autre_service" (et l'absence de
// catégorie) n'a pas de liste associée - réponse vide, la section reste cachée côté vue.
// Les options "Règlement" portent un attribut data-facture (la facture liée au règlement choisi,
// seule chose que la table crm_relance sait relier via sa colonne id_facture existante) ; les
// options "Domaine"/"Hébergement" portent data-domaine/data-expiration, lus uniquement au moment
// de la génération IA (genererEmailRelance) pour enrichir le contexte, jamais persistés sur la
// relance. Même nom de champ "rappel_domaine" pour les deux catégories - le JS du formulaire lit
// cet unique select, peu importe la catégorie active.
function getListeSelonCategorie($data)
{
    $indices = array("id", "categorie");
    if (!fieldCheck($data, $indices)) {
        echo '';
        return;
    }
    $idClient = intval($data['id']);
    $categorie = $data['categorie'];
    // Présélection (relance existante rouverte pour modification, voir rechargerListeSelonCategorie()
    // côté vue) - permet de retrouver exactement la facture/règlement/domaine déjà choisis à la
    // création, plutôt que de rouvrir la liste vierge.
    $idFactureSelectionnee = isset($data['id_facture']) && !empty($data['id_facture']) ? intval($data['id_facture']) : null;
    $idRappelSelectionne = isset($data['id_rappel']) && !empty($data['id_rappel']) ? intval($data['id_rappel']) : null;

    if ($categorie === 'facture') {
        $factures = facture::ofClient($idClient, false, false, true);
        ?>
        <label>Facture</label>
        <select class="chosen-select" name="facture">
            <option value="">Sélectionner</option>
            <?php foreach ($factures as $facture) : ?>
                <?php if ($facture->getReste() > 0) : ?>
                <option value="<?php echo $facture->getId() ?>" <?php echo $facture->getId() == $idFactureSelectionnee ? 'selected' : ''; ?>><?php echo $facture->getNumero() . ' - ' . normaldate($facture->getDateFacture()) . ' (' . number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise() . ')'; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <?php
    } elseif ($categorie === 'paiement') {
        $payments = payment::ofClient($idClient, $_SESSION['agence']);
        ?>
        <label>Règlement</label>
        <select class="chosen-select" name="payment_ref">
            <option value="">Sélectionner</option>
            <?php foreach ($payments as $paymentItem) : ?>
                <?php $facturePayment = $paymentItem->getFacture(); ?>
                <option value="<?php echo $paymentItem->getId() ?>" data-facture="<?php echo $facturePayment ? $facturePayment->getId() : ''; ?>" <?php echo ($facturePayment && $facturePayment->getId() == $idFactureSelectionnee) ? 'selected' : ''; ?>>
                    <?php echo ($facturePayment ? 'N°' . $facturePayment->getNumero() : '') . ' - ' . normaldate($paymentItem->getDatePayment()) . ' (' . number_format($paymentItem->getMontant(), 2, ',', ' ') . ($facturePayment ? ' ' . $facturePayment->getDevise() : '') . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="facture" value="<?php echo $idFactureSelectionnee ? $idFactureSelectionnee : ''; ?>">
        <?php
    } elseif ($categorie === 'expiration_domaine' || $categorie === 'hebergement') {
        $typeRappel = $categorie === 'hebergement' ? 'hosting' : 'domaine';
        $libelleListe = $categorie === 'hebergement' ? 'Hébergement' : 'Domaine';
        $rappels = array_filter(rappel::findAll(false, $_SESSION['agence'], $idClient), function ($r) use ($typeRappel) {
            return $r->getType() === $typeRappel;
        });
        ?>
        <label><?php echo $libelleListe; ?></label>
        <select class="chosen-select" name="rappel_domaine">
            <option value="">Sélectionner</option>
            <?php foreach ($rappels as $rappelItem) : ?>
                <option value="<?php echo $rappelItem->getId() ?>" data-domaine="<?php echo htmlspecialchars($rappelItem->getDomaine()); ?>" data-expiration="<?php echo $rappelItem->getDateExpir(); ?>" <?php echo $rappelItem->getId() == $idRappelSelectionne ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($rappelItem->getDomaine()) . ' - expire le ' . normaldate($rappelItem->getDateExpir()); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    } else {
        echo '';
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
    $relance->setFacture(isset($data['facture']) && !empty($data['facture']) ? facture::find($data['facture'],$_SESSION['agence']) : new facture());
    $relance->setRappel(isset($data['rappel_domaine']) && !empty($data['rappel_domaine']) ? rappel::find($data['rappel_domaine'],$_SESSION['agence']) : new rappel());
    $relance->setType(isset($data['type']) ? $data['type'] : null);
    $relance->setCategorie(isset($data['categorie']) ? $data['categorie'] : null);
    $relance->setEmailSujet(isset($data['email_sujet']) ? $data['email_sujet'] : null);
    $relance->setEmailCorps(isset($data['email_corps']) ? $data['email_corps'] : null);
    $relance->setDate($data['date']);
    $relance->setRemarque(isset($data['remarque']) ? $data['remarque'] : null);
    $relance->setTraite(isset($data['traite']) ? 1 : 0);
    $relance->setDateAdd(date('Y-m-d'));
    $relance->setLastEdit(date('Y-m-d'));
    return $relance;
}

