<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addBank':
            addBank($_POST);
            break;
        case 'editBank':
            editBank($_POST);
            break;
        case 'deleteBank':
            deleteBank($_POST);
            break;
        case 'deleteBanks' :
            deleteBanks($_POST);
            break;
        case 'deleteLogo' :
            deleteLogo($_POST);
            break;
        case 'getBanksByAgence' :
            getBanksByAgence($_GET);
            break;
    }
}

function addBank($data)
{
    $indices = array("raison_sociale");
    if (fieldCheck($data, $indices)) {
        if (buildBank($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editBank($data)
{
    $indices = array("id", "raison_sociale");
    if (fieldCheck($data, $indices)) {
        if (buildBank($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBank($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $bank = bank::find($id, $_SESSION["langue"]);
        if ($bank->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBanks($data)
{
    $indices = array("ids");
    if (fieldCheck($data, $indices)) {
        if (bank::deleteMultiple($data) == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}   


function buildBank($data, $id = null)
{
    global $db;
    $bank = new bank();

    if ($id) {
        $bank = bank::find($id,$_SESSION["langue"]);
    }

    
    $bank->setRaisonSociale($data['raison_sociale']);
    $bank->setSiegeSocial($data['siege_social']);
    $bank->setNumeroRegistreCommerce($data['numero_registre_commerce']);
    $bank->setIce($data['ice']);
    $bank->setRib($data['rib']);
    $bank->setCodeSwift($data['code_swift']);
    $bank->setBanque($data['banque']);
    $bank->setLabel(isset($data['label']) ? $data['label'] : null);
    $bank->setExcluRapprochement(isset($data['exclu_rapprochement']) && $data['exclu_rapprochement'] ? 1 : 0);
    $bank->setIbanNumber($data['iban_number']);
    $bank->setCurrency($data['currency']);
    if (isset($data['agence']) && !empty($data['agence'])) {
        $bank->setAgence(agence::find($data['agence'], $_SESSION['langue']));
    }
    $bank->setDateAdd(date("Y-m-d H:i:s"));
    $bank->setLastEdit(date("Y-m-d H:i:s"));

    return $bank;
}

function getBanksByAgence($data)
{
    $id_agence = isset($data['id_agence']) ? intval($data['id_agence']) : 0;
    $currentBankId = isset($data['id_bank_actuel']) ? intval($data['id_bank_actuel']) : 0;

    // Règle stricte par agence + repli pool Maroc - voir bank::findAllPourFormulaire() (partagée
    // avec le premier chargement PHP de com_devis/com_facture, pour que la liste y soit identique
    // dès le départ plutôt que seulement après ce rafraîchissement AJAX).
    $banks = bank::findAllPourFormulaire($id_agence);

    // en édition, on garde la banque déjà assignée visible même si elle ne correspond plus à l'agence
    if ($currentBankId && !in_array($currentBankId, array_map(function ($b) { return $b->getId(); }, $banks))) {
        $bankActuelle = bank::find($currentBankId);
        if ($bankActuelle->getId()) {
            $banks[] = $bankActuelle;
        }
    }

    echo '<option value="" selected disabled>Sélectionner</option>';
    foreach ($banks as $bank) {
        // Comptes personnels (Hamid/Zakaria - "PERSO" dans la raison sociale, voir $reglesParAgence
        // ci-dessus) : marqués pour que le JS du formulaire devis/facture (assets/js/ia-bank-
        // filter.js) force automatiquement "Proforma" + TVA à 0 dès qu'un de ces comptes est
        // choisi - ces comptes ne sont pas éligibles à une facturation avec TVA.
        $estPerso = stripos($bank->getRaisonSociale(), 'PERSO') !== false;
        echo '<option value="' . $bank->getId() . '"' . ($estPerso ? ' data-perso="1"' : '') . '>' . htmlspecialchars($bank->getRaisonSociale() . ' ' . $bank->getRib()) . '</option>';
    }
}