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

    // Règle stricte par agence (demandée explicitement, remplace l'ancien "pool Maroc" pour CES
    // 3 agences précises - ids de crm_bank en dur, voir mémoire projet pour le mapping complet) :
    //   - Verse Concept (agence 3)  : compte Verse Concept + comptes perso Hamid/Zakaria
    //   - HW Label (agence 1)      : les 2 comptes HW Label (BMCE + BP) + HW Label Devise + comptes perso Hamid/Zakaria
    //   - Dubai (agence 2)         : uniquement le compte HELLOWORLDLABEL - FZCO
    // Toute AUTRE agence (25 "HELLO WORLD", 26 "Ina & Co", ou une future agence) garde l'ancien
    // comportement inchangé (pool Maroc pour 25, filtre strict sur sa propre id sinon) - ces deux-là
    // n'étaient pas couvertes par la nouvelle règle, on ne leur change rien.
    $reglesParAgence = array(
        3 => array(1, 11, 7),
        1 => array(6, 12, 10, 11, 7),
        2 => array(2),
    );

    if (isset($reglesParAgence[$id_agence])) {
        $idsAutorises = $reglesParAgence[$id_agence];
        $banks = array();
        foreach (bank::findAll(false) as $b) {
            if (in_array($b->getId(), $idsAutorises)) {
                $banks[] = $b;
            }
        }
        // Conserve l'ordre déclaré dans $idsAutorises plutôt que l'ordre SQL, plus lisible dans le select.
        usort($banks, function ($a, $b) use ($idsAutorises) {
            return array_search($a->getId(), $idsAutorises) <=> array_search($b->getId(), $idsAutorises);
        });
    } else {
        // ces agences facturent toutes depuis le Maroc et partagent le même pool de comptes bancaires :
        // choisir l'une d'elles doit proposer les banques de tout le groupe, pas seulement celles
        // strictement rattachées à cette agence précise.
        $groupeMaroc = array(1, 3, 25); // HW LABEL SARL, VERSE CONCEPT, HELLO WORLD
        $agencesARechercher = in_array($id_agence, $groupeMaroc) ? $groupeMaroc : $id_agence;

        $banks = $id_agence ? bank::findAll($agencesARechercher) : array();
    }

    // en édition, on garde la banque déjà assignée visible même si elle ne correspond plus à l'agence
    if ($currentBankId && !in_array($currentBankId, array_map(function ($b) { return $b->getId(); }, $banks))) {
        $bankActuelle = bank::find($currentBankId);
        if ($bankActuelle->getId()) {
            $banks[] = $bankActuelle;
        }
    }

    echo '<option value="" selected disabled>Sélectionner</option>';
    foreach ($banks as $bank) {
        echo '<option value="' . $bank->getId() . '">' . htmlspecialchars($bank->getRaisonSociale() . ' ' . $bank->getRib()) . '</option>';
    }
}