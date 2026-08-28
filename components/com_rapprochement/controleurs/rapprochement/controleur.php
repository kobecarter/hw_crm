<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'previewReleve':
            previewReleve($_POST, $_FILES);
            break;
        case 'confirmerReleve':
            confirmerReleve($_POST);
            break;
        case 'creerJustificatifManuel':
            creerJustificatifManuel($_POST, $_FILES);
            break;
        case 'validerLigne':
            validerLigne($_POST, $_FILES);
            break;
        case 'ignorerLigne':
            ignorerLigne($_POST);
            break;
        case 'creerChargeEtLier':
            creerChargeEtLier($_POST);
            break;
        case 'listerBulletinsPaie':
            listerBulletinsPaie($_POST);
            break;
        case 'supprimerLot':
            supprimerLot($_POST);
            break;
        case 'annulerRapprochementFacture':
            annulerRapprochementFacture($_POST);
            break;
    }
}

// Détecte, parmi les en-têtes d'un CSV bancaire, les colonnes Date/Libellé/Débit/Crédit (ou
// Montant signé unique) via une liste de synonymes usuels - aucun fichier réel n'a pu être testé
// à la conception de ce module, donc ce mapping générique est volontairement large et pourra être
// affiné une fois testé sur un vrai export BCP/BMCE.
function detecterColonnesCsv($enTetes)
{
    $synonymes = array(
        'date' => array('date', 'date operation', "date d'operation", 'date valeur', 'date comptable'),
        'libelle' => array('libelle', 'libellé', 'description', 'intitule', 'intitulé', 'operation', 'opération', 'detail', 'détail', 'nature de l\'operation'),
        'debit' => array('debit', 'débit', 'retrait', 'sortie', 'montant debit', 'montant débit'),
        'credit' => array('credit', 'crédit', 'depot', 'dépôt', 'entree', 'entrée', 'montant credit', 'montant crédit'),
        'montant' => array('montant', 'amount'),
    );
    $colonnes = array();
    foreach ($enTetes as $index => $nom) {
        $nomNorm = mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $nom)), 'UTF-8');
        foreach ($synonymes as $champ => $variantes) {
            if (!isset($colonnes[$champ]) && in_array($nomNorm, $variantes)) {
                $colonnes[$champ] = $index;
            }
        }
    }
    return $colonnes;
}

// Normalise une date de relevé (français JJ/MM/AAAA, ISO AAAA-MM-JJ, avec / ou - comme
// séparateur) vers le format AAAA-MM-JJ attendu par la base - plus tolérant que dateBD() qui
// suppose toujours un format JJ/MM/AAAA, car le format réel d'un export bancaire est inconnu.
function normaliserDateOperation($valeur)
{
    $valeur = trim((string) $valeur);
    if ($valeur === '') {
        return null;
    }
    if (preg_match('/^(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/', $valeur, $m)) {
        return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
    }
    if (preg_match('/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})/', $valeur, $m)) {
        return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
    }
    return null;
}

// Identifie automatiquement, parmi les comptes bancaires enregistrés (Gestion des banques),
// celui auquel appartient le relevé déposé - à partir du nom de banque/RIB/IBAN repérés dans le
// document (CSV : contenu brut du fichier ; PDF : champs extraits par l'IA). Un match RIB/IBAN
// (fort) prime sur un simple match de nom de banque (faible, deux comptes pouvant partager la
// même banque) ; en cas d'absence ou d'ambiguïté, ne devine jamais - retourne null.
function detecterCompteBancaire($texteSignature, $comptes)
{
    $normaliser = function ($texte) {
        return preg_replace('/[^A-Z0-9]/', '', mb_strtoupper((string) $texte, 'UTF-8'));
    };
    $texteNorm = $normaliser($texteSignature);
    if ($texteNorm === '' || empty($comptes)) {
        return null;
    }

    $matchsForts = array();
    $matchsFaibles = array();
    foreach ($comptes as $compte) {
        $rib = $normaliser($compte->getRib());
        $iban = $normaliser($compte->getIbanNumber());
        if (($rib !== '' && mb_strlen($rib) >= 6 && mb_strpos($texteNorm, $rib) !== false)
            || ($iban !== '' && mb_strlen($iban) >= 6 && mb_strpos($texteNorm, $iban) !== false)) {
            $matchsForts[$compte->getId()] = $compte;
            continue;
        }
        $banque = $normaliser($compte->getBanque());
        if ($banque !== '' && mb_strpos($texteNorm, $banque) !== false) {
            $matchsFaibles[$compte->getId()] = $compte;
        }
    }

    if (count($matchsForts) === 1) {
        return reset($matchsForts);
    }
    if (empty($matchsForts) && count($matchsFaibles) === 1) {
        return reset($matchsFaibles);
    }
    return null;
}

function parserCsvReleve($contenu)
{
    $contenu = preg_replace('/^\xEF\xBB\xBF/', '', (string) $contenu);
    $lignes = preg_split('/\r\n|\r|\n/', trim((string) $contenu));
    if (count($lignes) < 2) {
        return array();
    }

    // Détection du délimiteur (virgule vs point-virgule, ce dernier très fréquent dans les
    // exports bancaires marocains/français).
    $delimiteur = (substr_count($lignes[0], ';') > substr_count($lignes[0], ',')) ? ';' : ',';

    // Certains exports bancaires ajoutent quelques lignes d'en-tête (titulaire, RIB, période)
    // avant le vrai tableau de transactions - on cherche donc la première ligne qui ressemble à
    // un en-tête de colonnes (Date + Libellé reconnus) plutôt que de supposer que c'est toujours
    // la ligne 0.
    $indexEnTete = null;
    $colonnes = array();
    $dernierEnTeteEssaye = array();
    for ($i = 0; $i < min(10, count($lignes)); $i++) {
        $enTetesEssai = str_getcsv($lignes[$i], $delimiteur);
        $colonnesEssai = detecterColonnesCsv($enTetesEssai);
        if (isset($colonnesEssai['date']) && isset($colonnesEssai['libelle'])) {
            $indexEnTete = $i;
            $colonnes = $colonnesEssai;
            break;
        }
        $dernierEnTeteEssaye = $enTetesEssai;
    }

    if ($indexEnTete === null) {
        throw new Exception("Colonnes Date/Libellé non reconnues dans ce CSV. En-têtes détectés : " . implode(', ', $dernierEnTeteEssaye));
    }

    $transactions = array();
    for ($i = $indexEnTete + 1; $i < count($lignes); $i++) {
        if (trim($lignes[$i]) === '') {
            continue;
        }
        $valeurs = str_getcsv($lignes[$i], $delimiteur);

        $debit = null;
        $credit = null;
        if (isset($colonnes['debit']) && isset($valeurs[$colonnes['debit']]) && trim($valeurs[$colonnes['debit']]) !== '') {
            $debit = (float) str_replace(array(' ', ','), array('', '.'), $valeurs[$colonnes['debit']]);
        }
        if (isset($colonnes['credit']) && isset($valeurs[$colonnes['credit']]) && trim($valeurs[$colonnes['credit']]) !== '') {
            $credit = (float) str_replace(array(' ', ','), array('', '.'), $valeurs[$colonnes['credit']]);
        }
        if ($debit === null && $credit === null && isset($colonnes['montant']) && isset($valeurs[$colonnes['montant']]) && trim($valeurs[$colonnes['montant']]) !== '') {
            $montant = (float) str_replace(array(' ', ','), array('', '.'), $valeurs[$colonnes['montant']]);
            if ($montant < 0) {
                $debit = abs($montant);
            } elseif ($montant > 0) {
                $credit = $montant;
            }
        }

        $transactions[] = array(
            'date' => isset($valeurs[$colonnes['date']]) ? trim($valeurs[$colonnes['date']]) : '',
            'libelle' => isset($valeurs[$colonnes['libelle']]) ? trim($valeurs[$colonnes['libelle']]) : '',
            'debit' => $debit,
            'credit' => $credit
        );
    }
    return $transactions;
}

// Nom de compte affiché : même chaîne de repli partout dans ce module (label -> raison sociale ->
// nom de banque brut).
function nomAfficheCompte($bank)
{
    if ($bank->getLabel() !== null && $bank->getLabel() !== '') {
        return $bank->getLabel();
    }
    if ($bank->getRaisonSociale() !== null && $bank->getRaisonSociale() !== '') {
        return $bank->getRaisonSociale();
    }
    return $bank->getBanque();
}

// Étape 1/2 du nouveau flux : analyse intégralement le fichier déposé (détection du compte,
// parsing, matching en simulation, agrégation des commissions) et renvoie tout en JSON SANS
// écrire une seule ligne en base - même le fichier lui-même n'est sauvegardé que parce que
// uploadFiles() est le seul mécanisme capable de déplacer un fichier $_FILES (il persistera même
// si l'aperçu est ensuite abandonné, cf. Hors périmètre du plan approuvé). C'est
// confirmerReleve() qui matérialise réellement le lot à partir de ce même JSON renvoyé tel quel
// par le navigateur.
function previewReleve($data, $files)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('add', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    if (!isset($files['document']['name'][0]) || $files['document']['name'][0] === '' || $files['document']['error'][0] !== UPLOAD_ERR_OK) {
        echo json_encode(array('success' => 0, 'message' => 'Fichier manquant'));
        return;
    }

    $idAgence = $_SESSION['agence'];

    // Certaines agences facturent toutes depuis le Maroc et partagent le même pool de comptes
    // bancaires (même regroupement que bank::getBanksByAgence() côté Gestion des banques) : un
    // relevé appartenant à Verse Concept doit être reconnu même déposé depuis la session HW Label,
    // sans que l'utilisateur ait à changer d'agence avant d'importer.
    $groupeMaroc = array(1, 3, 25);
    $agencesARechercher = in_array($idAgence, $groupeMaroc) ? $groupeMaroc : $idAgence;
    $comptes = array_values(array_filter(bank::findAll($agencesARechercher), function ($b) {
        return !$b->getExcluRapprochement();
    }));
    if (empty($comptes)) {
        echo json_encode(array('success' => 0, 'message' => 'Aucun compte bancaire configuré pour cette agence. Ajoutez-en un dans Gestion des banques.'));
        return;
    }

    $nomOriginal = $files['document']['name'][0];
    $extension = strtolower(pathinfo($nomOriginal, PATHINFO_EXTENSION));
    if (!in_array($extension, array('csv', 'pdf'))) {
        echo json_encode(array('success' => 0, 'message' => 'Format non supporté (CSV ou PDF uniquement)'));
        return;
    }

    $fichiersUploades = uploadFiles('document', '../../../images/releves/', array('csv', 'pdf', 'CSV', 'PDF'));
    if (empty($fichiersUploades[0])) {
        echo json_encode(array('success' => 0, 'message' => "Échec de l'enregistrement du fichier"));
        return;
    }
    $nomFichierSource = $fichiersUploades[0];
    $cheminAbsolu = realpath('../../../images/releves/' . $nomFichierSource);
    if ($cheminAbsolu === false) {
        echo json_encode(array('success' => 0, 'message' => "Échec de l'enregistrement du fichier"));
        return;
    }

    $transactionsBrutes = array();
    $bank = null;

    // Sélection manuelle du compte (voir afficherChoixCompteManuel() côté navigateur) : posée
    // quand une PREMIÈRE tentative d'auto-détection a échoué sur ce même fichier déjà uploadé.
    // Prime sur l'auto-détection - inutile de la relancer si l'utilisateur a déjà tranché - et
    // n'est acceptée que si l'id correspond bien à un compte de la liste autorisée pour cette
    // agence (jamais un id arbitraire fourni par le navigateur).
    $idBankForce = isset($data['id_bank_force']) && !empty($data['id_bank_force']) ? intval($data['id_bank_force']) : null;
    if ($idBankForce !== null) {
        foreach ($comptes as $c) {
            if ($c->getId() == $idBankForce) {
                $bank = $c;
                break;
            }
        }
    }

    try {
        if ($extension === 'csv') {
            // Pas d'IA nécessaire pour un CSV : le compte est identifié par simple recherche
            // textuelle du RIB/IBAN/nom de banque dans le contenu brut du fichier.
            $contenuBrut = file_get_contents($cheminAbsolu);
            if ($bank === null) {
                $bank = detecterCompteBancaire($contenuBrut, $comptes);
            }
            $transactionsBrutes = parserCsvReleve($contenuBrut);
        } else {
            $extrait = aiExtractor::extractReleveBancaire($cheminAbsolu, $extension);
            if ($bank === null) {
                $signature = (isset($extrait['banque_detectee']) ? $extrait['banque_detectee'] : '') . ' '
                    . (isset($extrait['rib_detecte']) ? $extrait['rib_detecte'] : '') . ' '
                    . (isset($extrait['iban_detecte']) ? $extrait['iban_detecte'] : '');
                $bank = detecterCompteBancaire($signature, $comptes);
            }
            foreach ($extrait['transactions'] as $t) {
                $transactionsBrutes[] = array(
                    'date' => isset($t['date']) ? $t['date'] : '',
                    'libelle' => isset($t['libelle']) ? $t['libelle'] : '',
                    'debit' => isset($t['debit']) && $t['debit'] !== '' ? (float) str_replace(',', '.', $t['debit']) : null,
                    'credit' => isset($t['credit']) && $t['credit'] !== '' ? (float) str_replace(',', '.', $t['credit']) : null
                );
            }
        }
    } catch (Exception $e) {
        echo json_encode(array('success' => 0, 'message' => $e->getMessage()));
        return;
    }

    // Compte non reconnu automatiquement (RIB/IBAN absents ou non lus par l'IA sur un PDF, format
    // inhabituel sur un CSV...) : plutôt que de bloquer l'import, on renvoie la liste des comptes
    // disponibles pour que l'utilisateur tranche lui-même - voir afficherChoixCompteManuel() côté
    // navigateur, qui rappelle previewReleve() avec id_bank_force une fois le choix fait. Le
    // fichier est déjà sur le disque (uploadFiles() ci-dessus) donc pas besoin de le redéposer.
    if (!$bank) {
        echo json_encode(array(
            'success' => 0,
            'bank_non_detecte' => true,
            'message' => "Compte bancaire non reconnu automatiquement dans ce document.",
            'comptes_disponibles' => array_map(function ($c) {
                return array('id' => $c->getId(), 'nom' => nomAfficheCompte($c));
            }, $comptes)
        ));
        return;
    }

    if (empty($transactionsBrutes)) {
        echo json_encode(array('success' => 0, 'message' => 'Aucune transaction détectée dans ce fichier'));
        return;
    }

    // Le compte détecté peut appartenir à une agence différente de celle de la session en cours
    // (ex: un relevé Verse Concept déposé depuis la session HW Label) - détecté ici, mais PAS
    // encore appliqué à la session : contrairement à l'ancien importReleve(), l'aperçu ne change
    // rien, c'est confirmerReleve() qui bascule réellement la session au moment de la validation.
    $idAgenceReelle = $bank->getAgence() ? $bank->getAgence()->getId() : $idAgence;
    $agenceBasculee = ($idAgenceReelle != $idAgence);
    $langueAgenceReelle = $agenceBasculee ? (($idAgenceReelle == 2) ? 'en' : 'fr') : $_SESSION['langue'];
    $agenceObjet = agence::find($idAgenceReelle, $langueAgenceReelle);

    $lignes = array();
    $lignesCommission = array();
    $compteurs = array('matched_facture' => 0, 'a_valider' => 0, 'sans_justificatif' => 0, 'debit_commission' => 0, 'doublon' => 0);
    $dateMin = null;
    $dateMax = null;
    $nbLignesValides = 0;
    $lotsImportExistantsVus = array();

    foreach ($transactionsBrutes as $t) {
        $dateOperation = normaliserDateOperation($t['date']);
        if ($dateOperation === null || trim((string) $t['libelle']) === '') {
            continue;
        }
        if (empty($t['debit']) && empty($t['credit'])) {
            continue;
        }

        $ligneSimulee = new releveLigne();
        $ligneSimulee->setDateOperation($dateOperation);
        $ligneSimulee->setLibelle($t['libelle']);
        $ligneSimulee->setDebit($t['debit']);
        $ligneSimulee->setCredit($t['credit']);

        // Une ligne déjà importée dans un lot antérieur (même compte/date/libellé/montant) n'est
        // jamais soumise au moteur de matching - inutile de proposer une suggestion pour une
        // opération déjà traitée. Dès qu'au moins une ligne est ainsi détectée, la validation est
        // bloquée côté navigateur (voir afficherApercu()) : l'utilisateur doit supprimer l'ancien
        // relevé avant de pouvoir réimporter celui-ci - voir trouverDoublons().
        $nbLignesValides++;
        $doublonsExistants = releveLigne::trouverDoublons($bank->getId(), $dateOperation, $t['debit'], $t['credit'], $t['libelle']);
        if (!empty($doublonsExistants)) {
            $ligneSimulee->setStatut('doublon');
            $ligneSimulee->setDonneesMatchingArray(array(
                'type' => 'ligne_dupliquee',
                'date_import_existant' => $doublonsExistants[0]->getDateAdd(),
                'lot_import_existant' => $doublonsExistants[0]->getLotImport()
            ));
            $lotsImportExistantsVus[$doublonsExistants[0]->getLotImport()] = true;
        } elseif (!empty($t['credit'])) {
            rapprochementMoteur::matcherCredit($ligneSimulee, $idAgenceReelle);
        } else {
            rapprochementMoteur::matcherDebit($ligneSimulee, $idAgenceReelle);
        }

        if ($dateMin === null || $dateOperation < $dateMin) {
            $dateMin = $dateOperation;
        }
        if ($dateMax === null || $dateOperation > $dateMax) {
            $dateMax = $dateOperation;
        }

        $infos = $ligneSimulee->getDonneesMatchingArray();
        if (isset($infos['type']) && $infos['type'] === 'debit_commission') {
            $lignesCommission[] = $ligneSimulee;
            $compteurs['debit_commission']++;
        } elseif (isset($infos['type']) && $infos['type'] === 'ligne_dupliquee') {
            $compteurs['doublon']++;
        } else {
            $compteurs[$ligneSimulee->getStatut()] = isset($compteurs[$ligneSimulee->getStatut()]) ? $compteurs[$ligneSimulee->getStatut()] + 1 : 1;
        }

        $lignes[] = array(
            'date_operation' => $ligneSimulee->getDateOperation(),
            'libelle' => $ligneSimulee->getLibelle(),
            'debit' => $ligneSimulee->getDebit(),
            'credit' => $ligneSimulee->getCredit(),
            'statut' => $ligneSimulee->getStatut(),
            'donnees_matching' => $infos
        );
    }

    $agregatCommissions = !empty($lignesCommission) ? rapprochementMoteur::agregerCommissions($lignesCommission, $idAgenceReelle) : null;

    $periodicite = $agenceObjet->getTvaPeriodicite() === 'trimestriel' ? 'trimestriel' : 'mensuel';
    $periodeLibelle = null;
    if ($dateMin !== null) {
        $periode = tva::periodeReference($periodicite, new DateTime($dateMin), 0);
        $periodeLibelle = tva::libellePeriode($periode['debut'], $periodicite);
    }

    // Le relevé déposé est-il, dans son ENSEMBLE, un doublon d'un relevé déjà importé (même
    // compte, mêmes lignes, toutes rattachées au même lot antérieur) ? Différent d'un doublon
    // PARTIEL (quelques lignes seulement en commun avec un ou plusieurs anciens relevés) - dans ce
    // cas précis, afficherApercu() côté navigateur affiche un message dédié ("ce relevé bancaire a
    // déjà été importé") plutôt que le message générique "X ligne(s) déjà importée(s)". Dans les
    // deux cas, la validation reste bloquée : l'utilisateur doit supprimer l'ancien relevé avant
    // de pouvoir réimporter celui-ci (voir confirmerReleve() ci-dessous, qui revérifie et refuse).
    $releveEntierementDejaImporte = $nbLignesValides > 0 && $compteurs['doublon'] === $nbLignesValides && count($lotsImportExistantsVus) === 1;
    $lotExistantInfo = null;
    if ($releveEntierementDejaImporte) {
        $lotExistant = releveLot::findByLotImport(array_key_first($lotsImportExistantsVus));
        if ($lotExistant->getId()) {
            $lotExistantInfo = array(
                'date_debut' => $lotExistant->getDateDebut(),
                'date_fin' => $lotExistant->getDateFin(),
                'date_add' => $lotExistant->getDateAdd() ? date('Y-m-d', strtotime($lotExistant->getDateAdd())) : null,
                'fichier_source' => $lotExistant->getFichierSource()
            );
        }
    }

    echo json_encode(array(
        'success' => 1,
        'lot_import' => uniqid('lot_'),
        'id_bank' => $bank->getId(),
        'banque' => nomAfficheCompte($bank),
        'id_agence' => $idAgenceReelle,
        'agence_basculee' => $agenceBasculee,
        'nouvelle_agence' => $agenceBasculee ? $agenceObjet->getNom() : null,
        'fichier_source' => $nomFichierSource,
        'date_debut' => $dateMin,
        'date_fin' => $dateMax,
        'periode_libelle' => $periodeLibelle,
        'lignes' => $lignes,
        'commissions' => $agregatCommissions,
        'compteurs' => $compteurs,
        'releve_entierement_deja_importe' => $releveEntierementDejaImporte,
        'lot_existant_info' => $lotExistantInfo
    ));
}

// Étape 2/2 : reçoit le JSON renvoyé par previewReleve (repassé tel quel par le navigateur, y
// compris les éventuelles corrections du bloc "commissions" - HT/TVA/taux) et matérialise
// réellement le lot : bascule de session si besoin, crm_releve_lot, chaque crm_releve_ligne, le
// payment+checkPayment() pour chaque crédit non-ambigu, et la charge agrégée commissions (avec
// le relevé bancaire lui-même comme justificatif). Les cas nécessitant une confirmation
// (debit_reconnu/credit_ambigu/debit_tva/debit_charge_existante/sans_justificatif) restent
// EXACTEMENT comme avant : insérés en a_valider, traités ensuite via validerLigne/ignorerLigne.
function confirmerReleve($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('add', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }

    $payload = isset($data['payload']) ? json_decode($data['payload'], true) : null;
    if (!is_array($payload) || empty($payload['lignes']) || empty($payload['id_bank']) || empty($payload['lot_import'])) {
        echo json_encode(array('success' => 0, 'message' => 'Aperçu invalide ou expiré - veuillez redéposer le fichier'));
        return;
    }

    $bank = bank::find(intval($payload['id_bank']));
    if (!$bank || !$bank->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Compte bancaire introuvable'));
        return;
    }

    $idAgence = $_SESSION['agence'];
    $idAgenceReelle = $bank->getAgence() ? $bank->getAgence()->getId() : $idAgence;
    $agenceBasculee = false;
    if ($idAgenceReelle != $idAgence) {
        $_SESSION['agence'] = $idAgenceReelle;
        $_SESSION['langue'] = ($idAgenceReelle == 2) ? 'en' : 'fr';
        $agenceBasculee = true;
    }
    $agenceObjet = agence::find($idAgenceReelle, $_SESSION['langue']);

    // Blocage simple : si ne serait-ce qu'UNE ligne de ce dépôt est déjà en base (même compte/
    // date/libellé/montant), on refuse tout net, avant toute écriture (lot / charge commissions /
    // lignes) - l'utilisateur doit supprimer l'ancien relevé (bouton "Supprimer cet import" sur sa
    // carte dans "Relevés importés") puis réimporter. Revérifié ici (pas seulement à l'aperçu, qui
    // a normalement déjà désactivé le bouton "Valider la lecture" dans ce cas) pour couvrir un
    // aperçu resté ouvert un moment ou un autre import concurrent du même relevé.
    foreach ($payload['lignes'] as $l) {
        if (!isset($l['date_operation']) || trim((string) $l['libelle']) === '') {
            continue;
        }
        if (!empty(releveLigne::trouverDoublons($bank->getId(), $l['date_operation'], isset($l['debit']) ? $l['debit'] : null, isset($l['credit']) ? $l['credit'] : null, $l['libelle']))) {
            echo json_encode(array(
                'success' => 0,
                'message' => "Ce relevé a déjà été importé (au moins une ligne identique existe déjà). Supprimez l'ancien relevé dans la liste \"Relevés importés\", puis réimportez ce fichier."
            ));
            return;
        }
    }

    $lot = new releveLot();
    $lot->setAgence($agenceObjet);
    $lot->setBank($bank);
    $lot->setLotImport($payload['lot_import']);
    $lot->setFichierSource(isset($payload['fichier_source']) ? $payload['fichier_source'] : null);
    $lot->setDateDebut(isset($payload['date_debut']) ? $payload['date_debut'] : null);
    $lot->setDateFin(isset($payload['date_fin']) ? $payload['date_fin'] : null);
    $lot->setPeriodeLibelle(isset($payload['periode_libelle']) ? $payload['periode_libelle'] : null);
    $lot->setDateAdd(date('Y-m-d H:i:s'));
    $lot->add();

    // Charge agrégée des commissions bancaires du lot : une seule charge pour toutes les lignes
    // "commission" détectées, avec le relevé bancaire lui-même (déjà sauvegardé par previewReleve)
    // comme justificatif copié dans images/charges/ - jamais de document séparé à fournir.
    $idChargeCommission = null;
    if (!empty($payload['commissions']) && !empty($payload['fichier_source'])) {
        $nomFichierCharge = null;
        $cheminSource = '../../../images/releves/' . $payload['fichier_source'];
        if (file_exists($cheminSource)) {
            $ext = substr($payload['fichier_source'], strrpos($payload['fichier_source'], '.') + 1);
            $nomBase = basename($payload['fichier_source'], '.' . $ext);
            $n = '';
            while (file_exists("../../../images/charges/$nomBase$n.$ext")) {
                $n++;
            }
            $nomFichierCharge = "$nomBase$n.$ext";
            @copy($cheminSource, '../../../images/charges/' . $nomFichierCharge);
        }

        $commissions = $payload['commissions'];
        $charge = new charge();
        $charge->setAgence($agenceObjet);
        $charge->setUser($_SESSION['user']);
        $charge->setPaidBy($_SESSION['user']);
        $charge->setType('variable');
        $charge->setTitre('Commissions bancaires — ' . nomAfficheCompte($bank) . (isset($payload['periode_libelle']) && $payload['periode_libelle'] ? ' (' . $payload['periode_libelle'] . ')' : ''));
        $charge->setDescription('Charge agrégée depuis BANK STATEMENT : ' . intval($commissions['nb_lignes']) . ' ligne(s) de commission - justificatif = relevé bancaire du lot.');
        $charge->setTotal((float) $commissions['total']);
        $charge->setDevise('DH');
        $charge->setTvaTaux((float) $commissions['taux']);
        $charge->setTvaDeductible(1);
        $charge->setPaid(1);
        $charge->setFacture(0);
        $charge->setRefunded(0);
        $charge->setDateCharge(isset($payload['date_fin']) ? $payload['date_fin'] : date('Y-m-d'));
        $charge->setDatePayment(isset($payload['date_fin']) ? $payload['date_fin'] : date('Y-m-d'));
        $charge->setModePayment('virement');
        if ($nomFichierCharge) {
            $charge->setPhoto($nomFichierCharge);
        }
        $charge->setDateAdd(date('Y-m-d H:i:s'));
        $charge->setLastEdit(date('Y-m-d H:i:s'));
        $charge->add();
        $idChargeCommission = charge::getLastId();
    }

    $compteurs = array('matched_facture' => 0, 'matched_charge' => 0, 'a_valider' => 0, 'sans_justificatif' => 0);
    $nbImportees = 0;

    foreach ($payload['lignes'] as $l) {
        if (!isset($l['date_operation']) || trim((string) $l['libelle']) === '') {
            continue;
        }
        $infos = isset($l['donnees_matching']) && is_array($l['donnees_matching']) ? $l['donnees_matching'] : array();

        $ligne = new releveLigne();
        $ligne->setAgence($agenceObjet);
        $ligne->setBank($bank);
        $ligne->setLotImport($payload['lot_import']);
        $ligne->setDateOperation($l['date_operation']);
        $ligne->setLibelle($l['libelle']);
        $ligne->setDebit(isset($l['debit']) ? $l['debit'] : null);
        $ligne->setCredit(isset($l['credit']) ? $l['credit'] : null);
        $ligne->setStatut(isset($l['statut']) ? $l['statut'] : 'a_valider');
        $ligne->setDonneesMatchingArray($infos);
        $ligne->setDateAdd(date('Y-m-d H:i:s'));
        $ligne->setLastEdit(date('Y-m-d H:i:s'));

        if (isset($infos['type']) && $infos['type'] === 'credit_rapproche' && isset($infos['facture']['id_facture'])) {
            // Crédit non-ambigu (une seule facture candidate) : matérialisé automatiquement ici,
            // sans intervention manuelle supplémentaire - jamais pendant l'aperçu.
            $facture = facture::find(intval($infos['facture']['id_facture']), $idAgenceReelle);
            if ($facture && $facture->getId()) {
                $payment = new payment();
                $payment->setFacture($facture);
                $payment->setMontant($ligne->getCredit());
                $payment->setDatePayment($ligne->getDateOperation());
                $payment->setMethodePayment('virement');
                $payment->setDateAdd(date('Y-m-d H:i:s'));
                $payment->setLastEdit(date('Y-m-d H:i:s'));
                $payment->add();
                $ligne->setIdPayment(payment::getLastId());
                $facture->checkPayment();
                $ligne->setStatut('matched_facture');
            } else {
                // La facture a disparu entre l'aperçu et la confirmation (cas limite) : jamais de
                // paiement fantôme, la ligne retombe en validation manuelle.
                $ligne->setStatut('a_valider');
                $ligne->setDonneesMatchingArray(array('type' => 'credit_ambigu', 'candidats' => array()));
            }
        } elseif (isset($infos['type']) && $infos['type'] === 'debit_commission' && $idChargeCommission) {
            $ligne->setIdCharge($idChargeCommission);
            $ligne->setStatut('matched_charge');
        }

        $ligne->add();
        $nbImportees++;
        $statut = $ligne->getStatut();
        $compteurs[$statut] = isset($compteurs[$statut]) ? $compteurs[$statut] + 1 : 1;
    }

    // Regroupement par période (mois ou trimestre selon la TVA de l'agence) : tous les lots déjà
    // en base pour ce même compte et cette même période (celui qu'on vient d'insérer inclus) -
    // sert à poser la question "voulez-vous faire le rapprochement bancaire maintenant ?" une
    // fois que le/les relevé(s) de la période sont réunis, plutôt que de forcer la résolution
    // ligne par ligne immédiatement après chaque import pris isolément.
    $groupePeriode = array();
    if ($lot->getPeriodeLibelle()) {
        foreach (releveLot::findAllByPeriode($idAgenceReelle, $bank->getId(), $lot->getPeriodeLibelle()) as $lotPeriode) {
            $compteursLotPeriode = releveLigne::compterParLot($lotPeriode->getLotImport());
            $groupePeriode[] = array(
                'id' => $lotPeriode->getId(),
                'lot_import' => $lotPeriode->getLotImport(),
                'fichier_source' => $lotPeriode->getFichierSource(),
                'date_debut' => $lotPeriode->getDateDebut(),
                'date_fin' => $lotPeriode->getDateFin(),
                'est_nouveau' => $lotPeriode->getLotImport() === $payload['lot_import'],
                'a_valider' => $compteursLotPeriode['a_valider'] + $compteursLotPeriode['sans_justificatif']
            );
        }
    }

    echo json_encode(array(
        'success' => 1,
        'lot_import' => $payload['lot_import'],
        'total' => $nbImportees,
        'compteurs' => $compteurs,
        'agence_basculee' => $agenceBasculee,
        'nouvelle_agence' => $agenceBasculee ? $agenceObjet->getNom() : null,
        'periode_libelle' => $lot->getPeriodeLibelle(),
        'groupe_periode' => $groupePeriode
    ));
}

// Résout le fichier à utiliser comme justificatif (photo) d'une charge créée depuis une ligne de
// relevé : le fichier déposé dans le champ $champFichier s'il y en a un, sinon une copie du
// relevé bancaire du lot lui-même (seul justificatif "gratuit" déjà disponible sans action
// supplémentaire de l'utilisateur) - jamais aucun des deux : la charge reste sans photo plutôt
// que d'échouer.
function resoudreJustificatifFichier($files, $champFichier, releveLigne $ligne)
{
    if (isset($files[$champFichier]['name'][0]) && !empty($files[$champFichier]['name'][0])) {
        $uploades = uploadFiles($champFichier, '../../../images/charges/', array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'JPG', 'JPEG', 'GIF', 'PNG', 'PDF'));
        if (!empty($uploades[0])) {
            return $uploades[0];
        }
    }
    $lot = releveLot::findByLotImport($ligne->getLotImport());
    if ($lot && $lot->getFichierSource()) {
        $cheminSource = '../../../images/releves/' . $lot->getFichierSource();
        if (file_exists($cheminSource)) {
            $ext = substr($lot->getFichierSource(), strrpos($lot->getFichierSource(), '.') + 1);
            $nomBase = basename($lot->getFichierSource(), '.' . $ext);
            $n = '';
            while (file_exists("../../../images/charges/$nomBase$n.$ext")) {
                $n++;
            }
            $nomFichierCharge = "$nomBase$n.$ext";
            if (@copy($cheminSource, '../../../images/charges/' . $nomFichierCharge)) {
                return $nomFichierCharge;
            }
        }
    }
    return null;
}

// Annule complètement un lot déjà confirmé : défait tout ce que confirmerReleve()/validerLigne()
// ont matérialisé pour ce lot (paiements, charges - agrégat commissions compris, bulletins de
// paie, déclarations TVA marquées payées), puis supprime les lignes et le lot lui-même. Seuls les
// éléments référencés par une ligne DE CE LOT sont touchés - jamais une charge/un paiement créé
// par ailleurs. Aucune confirmation supplémentaire ici : la fenêtre de confirmation côté
// navigateur est la seule barrière avant cet appel, volontairement explicite sur ce qui sera perdu.
function supprimerLot($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('delete', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['lot_import']) || empty($data['lot_import'])) {
        echo json_encode(array('success' => 0, 'message' => 'Lot manquant'));
        return;
    }

    $lot = releveLot::findByLotImport($data['lot_import']);
    if (!$lot->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Lot introuvable'));
        return;
    }

    $idAgence = $lot->getAgence()->getId();
    if ($_SESSION['agence'] != $idAgence) {
        $_SESSION['agence'] = $idAgence;
        $_SESSION['langue'] = ($idAgence == 2) ? 'en' : 'fr';
    }

    $lignes = releveLigne::findAll($idAgence, $lot->getLotImport());

    $idsCharge = array();
    $idsTva = array();

    foreach ($lignes as $ligne) {
        $infosLigne = $ligne->getDonneesMatchingArray();

        // "credit_reglement_existant" (bouton "Associer" sur un règlement déjà enregistré du
        // client, voir validerLigne()) liait un règlement qui existait déjà AVANT cet import - même
        // logique que "debit_charge_existante" ci-dessous : jamais supprimé, seulement délié (la
        // ligne du relevé disparaît, pas le règlement du client).
        $reglementPreExistant = isset($infosLigne['type']) && $infosLigne['type'] === 'credit_reglement_existant';
        if ($ligne->getIdPayment() && !$reglementPreExistant) {
            $payment = payment::find($ligne->getIdPayment());
            if ($payment->getId()) {
                $facture = $payment->getFacture();
                $payment->delete();
                if ($facture && $facture->getId()) {
                    $facture->checkPayment();
                }
            }
        }
        // "debit_charge_existante" liait une charge qui existait déjà AVANT cet import (le
        // rapprochement s'est contenté de la retrouver) - elle n'a jamais été créée par ce lot et
        // ne doit donc jamais être supprimée ici, seulement déliée (ce que fait deleteByLot() en
        // supprimant la ligne). Toute autre charge référencée, elle, a bien été créée par ce lot.
        $chargePreExistante = isset($infosLigne['type']) && $infosLigne['type'] === 'debit_charge_existante';
        if ($ligne->getIdCharge() && !$chargePreExistante && !in_array($ligne->getIdCharge(), $idsCharge)) {
            $idsCharge[] = $ligne->getIdCharge();
        }
        if ($ligne->getIdTva() && !in_array($ligne->getIdTva(), $idsTva)) {
            $idsTva[] = $ligne->getIdTva();
        }
    }

    // Une même charge (l'agrégat commissions notamment) peut être référencée par plusieurs lignes
    // du lot - traitée une seule fois ici grâce à la déduplication ci-dessus.
    foreach ($idsCharge as $idCharge) {
        $bulletin = payslip::findByIdCharge($idCharge);
        if ($bulletin->getId()) {
            $bulletin->delete();
        }
        $charge = charge::find($idCharge, $idAgence);
        if ($charge->getId()) {
            $charge->delete();
        }
    }

    foreach ($idsTva as $idTva) {
        $declaration = tva::find($idTva, $idAgence);
        if ($declaration->getId()) {
            $declaration->setStatus(0);
            $declaration->setDatePayment(null);
            $declaration->edit();
        }
    }

    releveLigne::deleteByLot($lot->getLotImport());
    $lot->delete();

    echo json_encode(array('success' => 1));
}

// Bulletins déjà enregistrés pour un employé - affiché dans l'onglet "Bulletin de paie" de la
// fenêtre de justificatif manuel pour que l'utilisateur voie d'un coup d'œil ce qui existe déjà
// (éviter un doublon pour le même mois) avant de créer un nouveau bulletin.
function listerBulletinsPaie($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('view', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id_resourcehumaine']) || empty($data['id_resourcehumaine'])) {
        echo json_encode(array('success' => 0, 'message' => 'Employé manquant'));
        return;
    }

    $bulletins = payslip::findAllByResourcehumaine(intval($data['id_resourcehumaine']));
    usort($bulletins, function ($a, $b) {
        return strtotime($b->getDate()) <=> strtotime($a->getDate());
    });

    // Règle 4 (listes distinctes) : un bulletin déjà lié à une AUTRE ligne de relevé reste proposé
    // (jamais caché), mais séparément des bulletins disponibles - le sélectionner déclenche la
    // fenêtre de réaffectation côté client (voir verifierReaffectationCharge() côté serveur, qui
    // bloque tant que la confirmation n'est pas explicite).
    $idsChargeDejaLies = releveLigne::findAllIdChargeLies();

    $disponibles = array();
    $dejaAffectes = array();
    foreach ($bulletins as $b) {
        $entree = array(
            'id' => $b->getId(),
            'title' => $b->getTitle(),
            'date' => $b->getDate(),
            'id_charge' => $b->getIdCharge()
        );
        if ($b->getIdCharge() && in_array($b->getIdCharge(), $idsChargeDejaLies)) {
            $dejaAffectes[] = $entree;
        } else {
            $disponibles[] = $entree;
        }
    }

    echo json_encode(array('success' => 1, 'bulletins' => $disponibles, 'bulletins_deja_affectes' => $dejaAffectes));
}

// Fenêtre "insérer le justificatif" : pour une ligne sans_justificatif, 3 façons de la résoudre -
// $data['mode'] vaut 'charge' (défaut, charge générique), 'payslip' (le virement est un salaire :
// bulletin de paie lié à un employé, même mécanique que creerBulletinDepuisCharge() côté Charges)
// ou 'fournisseur' (achat identifié : la charge référence un fournisseur existant dans son
// titre/description - aucune nouvelle table "facture d'achat", cf. Gestion des fournisseurs qui
// n'a qu'un champ "doc" unique par fournisseur, pas de sous-table de factures).
function creerJustificatifManuel($data, $files)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne manquante'));
        return;
    }
    $ligne = releveLigne::find(intval($data['id']));
    if (!$ligne->getId() || $ligne->getStatut() !== 'sans_justificatif') {
        echo json_encode(array('success' => 0, 'message' => 'Ligne introuvable ou déjà traitée'));
        return;
    }

    $mode = isset($data['mode']) ? $data['mode'] : 'charge';
    $titre = isset($data['titre']) && trim($data['titre']) !== '' ? trim($data['titre']) : $ligne->getLibelle();
    $montant = isset($data['montant']) && $data['montant'] !== '' ? (float) str_replace(',', '.', $data['montant']) : $ligne->getDebit();
    // Commune aux 3 modes (charge simple, bulletin de paie, fournisseur) - jamais spécifique à un
    // seul, exportée telle quelle dans le dossier comptable Excel (cf. exportTvaComptable()).
    $remarque = isset($data['remarque']) && trim($data['remarque']) !== '' ? trim($data['remarque']) : null;

    if ($mode === 'payslip') {
        if (!isset($data['id_resourcehumaine']) || empty($data['id_resourcehumaine'])) {
            echo json_encode(array('success' => 0, 'message' => "Choisissez l'employé correspondant"));
            return;
        }
        $resourcehumaine = resourcehumaine::find(intval($data['id_resourcehumaine']));
        if (!$resourcehumaine || $resourcehumaine->getId() == 0) {
            echo json_encode(array('success' => 0, 'message' => 'Employé introuvable'));
            return;
        }

        // Un bulletin de paie de cet employé existe déjà (saisi par ailleurs, ou un mois
        // précédent laissé de côté) : on se contente de lier sa charge, jamais de recréer un
        // doublon - même principe que "Choisir la charge correspondante" pour un débit reconnu.
        if (isset($data['id_charge_bulletin_existant']) && !empty($data['id_charge_bulletin_existant'])) {
            $chargeExistante = charge::find(intval($data['id_charge_bulletin_existant']), $ligne->getAgence()->getId());
            if (!$chargeExistante || !$chargeExistante->getId()) {
                echo json_encode(array('success' => 0, 'message' => 'Bulletin introuvable'));
                return;
            }
            $conflit = verifierReaffectationCharge($chargeExistante->getId(), $ligne, !empty($data['force_reaffectation']));
            if ($conflit !== null) {
                echo json_encode($conflit);
                return;
            }
            if ($remarque !== null) {
                $chargeExistante->setRemarque($remarque);
                $chargeExistante->edit();
            }
            $ligne->setIdCharge($chargeExistante->getId());
            $ligne->setStatut('matched_charge');
            $ligne->setLastEdit(date('Y-m-d H:i:s'));
            $ligne->edit();

            echo json_encode(array('success' => 1, 'action' => 'bulletin_lie', 'id_charge' => $chargeExistante->getId()));
            return;
        }

        $moisNoms = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
        $dateOperation = new DateTime($ligne->getDateOperation());
        $mois = isset($data['payslip_mois']) && $data['payslip_mois'] !== '' ? intval($data['payslip_mois']) : (int) $dateOperation->format('n');
        $annee = isset($data['payslip_annee']) && $data['payslip_annee'] !== '' ? intval($data['payslip_annee']) : (int) $dateOperation->format('Y');
        $nomMois = isset($moisNoms[$mois]) ? $moisNoms[$mois] : '';

        $nomFichierCharge = resoudreJustificatifFichier($files, 'justificatif', $ligne);

        $charge = new charge();
        $charge->setAgence($ligne->getAgence());
        $charge->setUser($_SESSION['user']);
        $charge->setPaidBy($_SESSION['user']);
        $charge->setType('fixe');
        $charge->setTitre('Bulletin de paie ' . $nomMois . ' ' . $annee . ' — ' . $resourcehumaine->getFullName());
        $charge->setDescription('Charge créée depuis BANK STATEMENT — salaire rapproché au relevé bancaire.');
        $charge->setRemarque($remarque);
        $charge->setTotal($montant);
        $charge->setDevise('DH');
        $charge->setTvaTaux(null);
        $charge->setTvaDeductible(0);
        $charge->setPaid(1);
        $charge->setFacture(0);
        $charge->setRefunded(0);
        $charge->setDateCharge($ligne->getDateOperation());
        $charge->setDatePayment($ligne->getDateOperation());
        $charge->setModePayment('virement');
        if ($nomFichierCharge) {
            $charge->setPhoto($nomFichierCharge);
        }
        $charge->setDateAdd(date('Y-m-d H:i:s'));
        $charge->setLastEdit(date('Y-m-d H:i:s'));
        $charge->add();
        $idCharge = charge::getLastId();

        // Le même fichier (déjà copié dans images/charges/ ci-dessus) est dupliqué vers l'espace
        // employé - même contrainte que creerBulletinDepuisCharge() (com_charge) : uploadFiles()
        // a déjà déplacé le fichier d'origine, seule une copie permet de le faire atterrir dans
        // les deux dossiers.
        if ($nomFichierCharge) {
            $cheminSource = '../../../images/charges/' . $nomFichierCharge;
            $dossierDestination = '../../../images/resourceshumaines/payslips';
            if (file_exists($cheminSource) && is_dir($dossierDestination)) {
                $ext = substr($nomFichierCharge, strrpos($nomFichierCharge, '.') + 1);
                $nomBase = basename($nomFichierCharge, '.' . $ext);
                $n = '';
                while (file_exists("$dossierDestination/$nomBase$n.$ext")) {
                    $n++;
                }
                $nomFichierDestination = "$nomBase$n.$ext";
                if (@copy($cheminSource, "$dossierDestination/$nomFichierDestination")) {
                    $payslip = new payslip();
                    $payslip->setResourcehumaine($resourcehumaine);
                    $payslip->setTitle('Bulletin de paie ' . $nomMois . ' ' . $annee);
                    $payslip->setDate(date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $annee, $mois))));
                    $payslip->setFile($nomFichierDestination);
                    $payslip->setIdCharge($idCharge);
                    $payslip->add();
                }
            }
        }

        $ligne->setIdCharge($idCharge);
        $ligne->setStatut('matched_charge');
        $ligne->setLastEdit(date('Y-m-d H:i:s'));
        $ligne->edit();

        echo json_encode(array('success' => 1, 'action' => 'bulletin_cree', 'id_charge' => $idCharge));
        return;
    }

    if ($mode === 'fournisseur') {
        if (!isset($data['id_fournisseur']) || empty($data['id_fournisseur'])) {
            echo json_encode(array('success' => 0, 'message' => 'Choisissez le fournisseur correspondant'));
            return;
        }
        $fournisseur = fournisseur::find(intval($data['id_fournisseur']));
        if (!$fournisseur || !$fournisseur->getId()) {
            echo json_encode(array('success' => 0, 'message' => 'Fournisseur introuvable'));
            return;
        }
        $nomFournisseur = trim((string) $fournisseur->getRaisonSocial()) !== ''
            ? $fournisseur->getRaisonSocial()
            : trim($fournisseur->getPrenom() . ' ' . $fournisseur->getNom());

        $nomFichierCharge = resoudreJustificatifFichier($files, 'justificatif', $ligne);
        $titreFournisseur = isset($data['titre']) && trim($data['titre']) !== '' ? trim($data['titre']) : 'Achat — ' . $nomFournisseur;

        $charge = new charge();
        $charge->setAgence($ligne->getAgence());
        $charge->setUser($_SESSION['user']);
        $charge->setPaidBy($_SESSION['user']);
        $charge->setType('variable');
        $charge->setTitre($titreFournisseur);
        $charge->setDescription('Charge créée depuis BANK STATEMENT — fournisseur : ' . $nomFournisseur . '.');
        $charge->setRemarque($remarque);
        $charge->setTotal($montant);
        $charge->setDevise('DH');
        $charge->setTvaTaux(null);
        $charge->setTvaDeductible(0);
        $charge->setPaid(1);
        $charge->setFacture(0);
        $charge->setRefunded(0);
        $charge->setDateCharge($ligne->getDateOperation());
        $charge->setDatePayment($ligne->getDateOperation());
        $charge->setModePayment('virement');
        if ($nomFichierCharge) {
            $charge->setPhoto($nomFichierCharge);
        }
        $charge->setDateAdd(date('Y-m-d H:i:s'));
        $charge->setLastEdit(date('Y-m-d H:i:s'));
        $charge->add();
        $idCharge = charge::getLastId();

        $ligne->setIdCharge($idCharge);
        $ligne->setStatut('matched_charge');
        $ligne->setLastEdit(date('Y-m-d H:i:s'));
        $ligne->edit();

        echo json_encode(array('success' => 1, 'action' => 'charge_creee', 'id_charge' => $idCharge));
        return;
    }

    // Mode par défaut : charge générique.
    $nomFichierCharge = resoudreJustificatifFichier($files, 'justificatif', $ligne);

    $charge = new charge();
    $charge->setAgence($ligne->getAgence());
    $charge->setUser($_SESSION['user']);
    $charge->setPaidBy($_SESSION['user']);
    $charge->setType('variable');
    $charge->setTitre($titre);
    $charge->setDescription('Charge créée depuis BANK STATEMENT — justificatif inséré manuellement (relevé bancaire).');
    $charge->setRemarque($remarque);
    $charge->setTotal($montant);
    $charge->setDevise('DH');
    $charge->setTvaTaux(null);
    $charge->setTvaDeductible(0);
    $charge->setPaid(1);
    $charge->setFacture(0);
    $charge->setRefunded(0);
    $charge->setDateCharge($ligne->getDateOperation());
    $charge->setDatePayment($ligne->getDateOperation());
    $charge->setModePayment('virement');
    if ($nomFichierCharge) {
        $charge->setPhoto($nomFichierCharge);
    }
    $charge->setDateAdd(date('Y-m-d H:i:s'));
    $charge->setLastEdit(date('Y-m-d H:i:s'));
    $charge->add();
    $idCharge = charge::getLastId();

    $ligne->setIdCharge($idCharge);
    $ligne->setStatut('matched_charge');
    $ligne->setLastEdit(date('Y-m-d H:i:s'));
    $ligne->edit();

    echo json_encode(array('success' => 1, 'action' => 'charge_creee', 'id_charge' => $idCharge));
}

// Sécurité anti-doublon (réaffectation) : avant de lier une charge existante (ou le bulletin de
// paie qui la porte) à une ligne de relevé, vérifie qu'elle n'est pas déjà rattachée à une AUTRE
// ligne. Sans confirmation explicite ($force), retourne le tableau à renvoyer tel quel en JSON
// (la fenêtre de confirmation côté client s'ouvre alors avec le détail de l'ancienne affectation).
// Avec $force, défait l'ancienne liaison (cette ligne d'origine repasse "à valider" - elle garde
// ses donnees_matching d'import, jamais modifiées depuis, donc sa suggestion d'origine réapparaît
// telle quelle si on veut la retraiter plus tard) et retourne null pour laisser l'appelant
// poursuivre la nouvelle liaison.
function verifierReaffectationCharge($idCharge, releveLigne $ligneCourante, $force)
{
    $ancienneLigne = releveLigne::findLigneParCharge($idCharge, $ligneCourante->getId());
    if (!$ancienneLigne) {
        return null;
    }
    if (!$force) {
        $banqueAncienne = $ancienneLigne->getBank();
        return array(
            'success' => 0,
            'needs_confirmation' => 1,
            'message' => "Cette charge est déjà affectée à un enregistrement. Êtes-vous sûr de vouloir la réaffecter ?",
            'ancienne_affectation' => array(
                'id_ligne' => $ancienneLigne->getId(),
                'date_operation' => $ancienneLigne->getDateOperation() ? date('d/m/Y', strtotime($ancienneLigne->getDateOperation())) : '',
                'libelle' => $ancienneLigne->getLibelle(),
                'montant' => $ancienneLigne->getDebit() ? $ancienneLigne->getDebit() : $ancienneLigne->getCredit(),
                'compte' => $banqueAncienne ? nomAfficheCompte($banqueAncienne) : ''
            )
        );
    }

    $ancienneLigne->setIdCharge(null);
    $ancienneLigne->setStatut('a_valider');
    $ancienneLigne->setLastEdit(date('Y-m-d H:i:s'));
    $ancienneLigne->edit();
    return null;
}

// Même sécurité anti-doublon que verifierReaffectationCharge() ci-dessus, appliquée aux
// règlements existants du client (fenêtre "Choisir la facture" -> panneau "Règlements déjà
// enregistrés" -> "Associer ce règlement", plutôt que de créer un nouveau paiement en doublon).
function verifierReaffectationPayment($idPayment, releveLigne $ligneCourante, $force)
{
    $ancienneLigne = releveLigne::findLigneParPayment($idPayment, $ligneCourante->getId());
    if (!$ancienneLigne) {
        return null;
    }
    if (!$force) {
        $banqueAncienne = $ancienneLigne->getBank();
        return array(
            'success' => 0,
            'needs_confirmation' => 1,
            'message' => "Ce règlement est déjà affecté à un enregistrement. Êtes-vous sûr de vouloir le réaffecter ?",
            'ancienne_affectation' => array(
                'id_ligne' => $ancienneLigne->getId(),
                'date_operation' => $ancienneLigne->getDateOperation() ? date('d/m/Y', strtotime($ancienneLigne->getDateOperation())) : '',
                'libelle' => $ancienneLigne->getLibelle(),
                'montant' => $ancienneLigne->getDebit() ? $ancienneLigne->getDebit() : $ancienneLigne->getCredit(),
                'compte' => $banqueAncienne ? nomAfficheCompte($banqueAncienne) : ''
            )
        );
    }

    $ancienneLigne->setIdPayment(null);
    $ancienneLigne->setStatut('a_valider');
    $ancienneLigne->setLastEdit(date('Y-m-d H:i:s'));
    $ancienneLigne->edit();
    return null;
}

function validerLigne($data, $files = array())
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne manquante'));
        return;
    }

    $ligne = releveLigne::find(intval($data['id']));
    if (!$ligne->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne introuvable'));
        return;
    }
    if (in_array($ligne->getStatut(), array('matched_facture', 'matched_charge', 'matched_tva', 'ignore'))) {
        echo json_encode(array('success' => 0, 'message' => 'Cette ligne a déjà été traitée'));
        return;
    }

    $infos = $ligne->getDonneesMatchingArray();

    // Charge déjà existante trouvée pour ce montant/cette date : on se contente de lier, jamais
    // de créer une deuxième charge en doublon - mais si cette charge n'avait encore aucun
    // justificatif et qu'un fichier est déposé ici (la "facture d'achat"), on l'attache au passage.
    if (isset($infos['type']) && $infos['type'] === 'debit_charge_existante') {
        if (!isset($data['id_charge_existante']) || empty($data['id_charge_existante'])) {
            echo json_encode(array('success' => 0, 'message' => 'Choisissez la charge correspondante'));
            return;
        }
        $chargeExistante = charge::find(intval($data['id_charge_existante']), $ligne->getAgence()->getId());
        if (!$chargeExistante || !$chargeExistante->getId()) {
            echo json_encode(array('success' => 0, 'message' => 'Charge introuvable'));
            return;
        }
        $conflit = verifierReaffectationCharge($chargeExistante->getId(), $ligne, !empty($data['force_reaffectation']));
        if ($conflit !== null) {
            echo json_encode($conflit);
            return;
        }
        if (isset($files['justificatif_valider']['name'][0]) && !empty($files['justificatif_valider']['name'][0])) {
            $uploades = uploadFiles('justificatif_valider', '../../../images/charges/', array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'JPG', 'JPEG', 'GIF', 'PNG', 'PDF'));
            if (!empty($uploades[0])) {
                $chargeExistante->setPhoto($uploades[0]);
                $chargeExistante->edit();
            }
        }
        $ligne->setIdCharge($chargeExistante->getId());
        $ligne->setStatut('matched_charge');
        $ligne->setLastEdit(date('Y-m-d H:i:s'));
        $ligne->edit();

        echo json_encode(array('success' => 1, 'action' => 'charge_liee', 'id_charge' => $chargeExistante->getId()));
        return;
    }

    // Débit reconnu : la charge suggérée n'a jamais été créée avant ce clic explicite - un
    // justificatif (facture d'achat) déposé ici est attaché dès la création. Si l'utilisateur a
    // choisi une charge existante via "Choisir" (ex: cet achat Genious a déjà été saisi
    // manuellement ce mois-ci), on se contente de la lier - jamais de doublon créé à l'aveugle.
    if (isset($infos['type']) && $infos['type'] === 'debit_reconnu') {
        if (isset($data['id_charge_existante']) && !empty($data['id_charge_existante'])) {
            $chargeExistante = charge::find(intval($data['id_charge_existante']), $ligne->getAgence()->getId());
            if (!$chargeExistante || !$chargeExistante->getId()) {
                echo json_encode(array('success' => 0, 'message' => 'Charge introuvable'));
                return;
            }
            $conflit = verifierReaffectationCharge($chargeExistante->getId(), $ligne, !empty($data['force_reaffectation']));
            if ($conflit !== null) {
                echo json_encode($conflit);
                return;
            }
            $ligne->setIdCharge($chargeExistante->getId());
            $ligne->setStatut('matched_charge');
            $ligne->setLastEdit(date('Y-m-d H:i:s'));
            $ligne->edit();

            echo json_encode(array('success' => 1, 'action' => 'charge_liee', 'id_charge' => $chargeExistante->getId()));
            return;
        }

        $charge = new charge();
        $charge->setAgence($ligne->getAgence());
        $charge->setUser($_SESSION['user']);
        $charge->setPaidBy($_SESSION['user']);
        $charge->setType(isset($infos['charge_type']) ? $infos['charge_type'] : 'fixe');
        $charge->setTitre(isset($infos['titre']) ? $infos['titre'] : $ligne->getLibelle());
        $banqueLigne = $ligne->getBank();
        $nomCompte = $banqueLigne->getLabel() !== null && $banqueLigne->getLabel() !== ''
            ? $banqueLigne->getLabel()
            : ($banqueLigne->getRaisonSociale() !== null && $banqueLigne->getRaisonSociale() !== '' ? $banqueLigne->getRaisonSociale() : $banqueLigne->getBanque());
        $charge->setDescription('Charge créée depuis BANK STATEMENT (' . $nomCompte . ')');
        $charge->setTotal(isset($infos['montant']) ? $infos['montant'] : $ligne->getDebit());
        $charge->setDevise('DH');
        $charge->setTvaTaux(null);
        $charge->setTvaDeductible(0);
        $charge->setPaid(1);
        $charge->setFacture(0);
        $charge->setRefunded(0);
        $charge->setDateCharge($ligne->getDateOperation());
        $charge->setDatePayment($ligne->getDateOperation());
        $charge->setModePayment('virement');
        if (isset($files['justificatif_valider']['name'][0]) && !empty($files['justificatif_valider']['name'][0])) {
            $uploades = uploadFiles('justificatif_valider', '../../../images/charges/', array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'JPG', 'JPEG', 'GIF', 'PNG', 'PDF'));
            if (!empty($uploades[0])) {
                $charge->setPhoto($uploades[0]);
            }
        }
        $charge->setDateAdd(date('Y-m-d H:i:s'));
        $charge->setLastEdit(date('Y-m-d H:i:s'));
        $charge->add();
        $idCharge = charge::getLastId();

        $ligne->setIdCharge($idCharge);
        $ligne->setStatut('matched_charge');
        $ligne->setLastEdit(date('Y-m-d H:i:s'));
        $ligne->edit();

        echo json_encode(array('success' => 1, 'action' => 'charge_creee', 'id_charge' => $idCharge));
        return;
    }

    // Crédit ambigu : l'utilisateur choisit manuellement parmi les candidats proposés - soit une
    // facture (crée un nouveau règlement), soit un règlement déjà enregistré du client (panneau
    // "Règlements déjà enregistrés" -> "Associer" : ce crédit correspond à un paiement déjà saisi
    // manuellement, on se contente de lier, jamais de créer un deuxième règlement en doublon).
    if (isset($infos['type']) && $infos['type'] === 'credit_ambigu') {
        if (!empty($data['id_payment_existant'])) {
            $paymentExistant = payment::find(intval($data['id_payment_existant']));
            if (!$paymentExistant || !$paymentExistant->getId() || !$paymentExistant->getFacture() || !$paymentExistant->getFacture()->getClient() || $paymentExistant->getFacture()->getClient()->getAgence()->getId() != $ligne->getAgence()->getId()) {
                echo json_encode(array('success' => 0, 'message' => 'Règlement introuvable'));
                return;
            }
            $conflit = verifierReaffectationPayment($paymentExistant->getId(), $ligne, !empty($data['force_reaffectation']));
            if ($conflit) {
                echo json_encode($conflit);
                return;
            }

            $ligne->setIdPayment($paymentExistant->getId());
            $ligne->setStatut('matched_facture');
            // Marqueur distinct de "credit_rapproche" (facture -> nouveau règlement) : ce règlement
            // existait AVANT ce rapprochement - "Annuler" (annulerRapprochementFacture()) et
            // supprimerLot() doivent tous deux savoir ne JAMAIS le supprimer, seulement le délier.
            $ligne->setDonneesMatchingArray(array('type' => 'credit_reglement_existant', 'id_payment' => $paymentExistant->getId()));
            $ligne->setLastEdit(date('Y-m-d H:i:s'));
            $ligne->edit();

            echo json_encode(array('success' => 1, 'action' => 'reglement_lie', 'id_payment' => $paymentExistant->getId()));
            return;
        }

        if (!isset($data['id_facture']) || empty($data['id_facture'])) {
            echo json_encode(array('success' => 0, 'message' => 'Choisissez une facture parmi les candidats proposés'));
            return;
        }
        $facture = facture::find(intval($data['id_facture']), $ligne->getAgence()->getId());
        if (!$facture || !$facture->getId()) {
            echo json_encode(array('success' => 0, 'message' => 'Facture introuvable'));
            return;
        }
        $payment = new payment();
        $payment->setFacture($facture);
        $payment->setMontant($ligne->getCredit());
        $payment->setDatePayment($ligne->getDateOperation());
        $payment->setMethodePayment('virement');
        $payment->setDateAdd(date('Y-m-d H:i:s'));
        $payment->setLastEdit(date('Y-m-d H:i:s'));
        $payment->add();
        $idPayment = payment::getLastId();
        $facture->checkPayment();

        $ligne->setIdPayment($idPayment);
        $ligne->setStatut('matched_facture');
        // Même forme que le match automatique credit_rapproche (un seul candidat) : ce règlement a
        // bien été CRÉÉ par ce rapprochement, "Annuler" et supprimerLot() peuvent donc le supprimer
        // sans risque de perdre un paiement du client antérieur à cet import.
        $ligne->setDonneesMatchingArray(array('type' => 'credit_rapproche', 'facture' => array('id_facture' => $facture->getId(), 'numero' => $facture->getNumero())));
        $ligne->setLastEdit(date('Y-m-d H:i:s'));
        $ligne->edit();

        echo json_encode(array('success' => 1, 'action' => 'paiement_lie', 'id_payment' => $idPayment));
        return;
    }

    // Paiement TVA : jamais rapproché automatiquement (même si un seul candidat a été détecté) -
    // l'utilisateur doit confirmer explicitement à quelle déclaration ce relevé correspond via la
    // fenêtre de confirmation.
    if (isset($infos['type']) && $infos['type'] === 'debit_tva') {
        if (!isset($data['id_tva']) || empty($data['id_tva'])) {
            echo json_encode(array('success' => 0, 'message' => 'Choisissez la déclaration TVA correspondante'));
            return;
        }
        $declarationTva = tva::find(intval($data['id_tva']), $ligne->getAgence()->getId());
        if (!$declarationTva || !$declarationTva->getId()) {
            echo json_encode(array('success' => 0, 'message' => 'Déclaration TVA introuvable'));
            return;
        }
        $declarationTva->setStatus(1);
        $declarationTva->setDatePayment($ligne->getDateOperation());
        $declarationTva->edit();

        $ligne->setIdTva($declarationTva->getId());
        $ligne->setStatut('matched_tva');
        $ligne->setLastEdit(date('Y-m-d H:i:s'));
        $ligne->edit();

        echo json_encode(array('success' => 1, 'action' => 'tva_liee', 'id_tva' => $declarationTva->getId()));
        return;
    }

    echo json_encode(array('success' => 0, 'message' => 'Aucune action de validation possible pour cette ligne'));
}

// Annule un rapprochement crédit -> facture déjà confirmé (bouton "Annuler" sur une ligne
// "Facture rapprochée") : la ligne repasse "à valider" avec 0 candidat (comme un import frais -
// l'utilisateur peut choisir une autre facture ou un autre règlement via "Choisir"). Si le
// règlement lié avait été CRÉÉ par cette confirmation (type credit_rapproche, qu'il vienne du
// matching automatique à candidat unique ou d'un choix manuel de facture), il est supprimé et la
// facture recalculée ; s'il s'agissait d'un règlement du client déjà existant, simplement associé
// (type credit_reglement_existant, voir "Associer ce règlement"), il n'est JAMAIS supprimé -
// seulement délié, exactement comme supprimerLot() épargne les charges "debit_charge_existante".
function annulerRapprochementFacture($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne manquante'));
        return;
    }

    $ligne = releveLigne::find(intval($data['id']));
    if (!$ligne->getId() || $ligne->getStatut() !== 'matched_facture') {
        echo json_encode(array('success' => 0, 'message' => "Cette ligne n'est pas un rapprochement de facture actif"));
        return;
    }

    $infos = $ligne->getDonneesMatchingArray();
    $reglementPreExistant = isset($infos['type']) && $infos['type'] === 'credit_reglement_existant';

    if ($ligne->getIdPayment() && !$reglementPreExistant) {
        $payment = payment::find($ligne->getIdPayment());
        if ($payment->getId()) {
            $facture = $payment->getFacture();
            $payment->delete();
            if ($facture && $facture->getId()) {
                $facture->checkPayment();
            }
        }
    }

    $ligne->setIdPayment(null);
    $ligne->setStatut('a_valider');
    $ligne->setDonneesMatchingArray(array('type' => 'credit_ambigu', 'candidats' => array()));
    $ligne->setLastEdit(date('Y-m-d H:i:s'));
    $ligne->edit();

    echo json_encode(array('success' => 1));
}

function ignorerLigne($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne manquante'));
        return;
    }
    $ligne = releveLigne::find(intval($data['id']));
    if (!$ligne->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne introuvable'));
        return;
    }
    $ligne->setStatut('ignore');
    $ligne->setLastEdit(date('Y-m-d H:i:s'));
    $ligne->edit();
    echo json_encode(array('success' => 1));
}

// Fenêtre "Choisir la charge correspondante" (debit_reconnu / debit_charge_existante) : quand
// aucune charge de la liste ne correspond réellement (ex: un virement fourre-tout au nom d'un
// employé, sans rapport avec la charge suggérée par le montant/la date), l'utilisateur doit pouvoir
// créer la charge manquante sans quitter la fenêtre plutôt que d'être bloqué au seul choix parmi
// les charges existantes.
function creerChargeEtLier($data)
{
    header('Content-Type: application/json');
    if (!$_SESSION['user']->hasDroit('edit', 'com_rapprochement')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé'));
        return;
    }
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne manquante'));
        return;
    }
    $ligne = releveLigne::find(intval($data['id']));
    if (!$ligne->getId() || in_array($ligne->getStatut(), array('matched_facture', 'matched_charge', 'matched_tva', 'ignore'))) {
        echo json_encode(array('success' => 0, 'message' => 'Ligne introuvable ou déjà traitée'));
        return;
    }
    $titre = isset($data['titre']) && trim($data['titre']) !== '' ? trim($data['titre']) : $ligne->getLibelle();
    $montant = isset($data['montant']) && $data['montant'] !== '' ? (float) str_replace(',', '.', $data['montant']) : $ligne->getDebit();
    if (!$montant || $montant <= 0) {
        echo json_encode(array('success' => 0, 'message' => 'Montant invalide'));
        return;
    }
    $type = isset($data['type_charge']) && in_array($data['type_charge'], array('fixe', 'variable')) ? $data['type_charge'] : 'variable';

    $charge = new charge();
    $charge->setAgence($ligne->getAgence());
    $charge->setUser($_SESSION['user']);
    $charge->setPaidBy($_SESSION['user']);
    $charge->setType($type);
    $charge->setTitre($titre);
    $charge->setDescription('Charge créée depuis BANK STATEMENT — ajoutée manuellement via "Choisir la charge".');
    $charge->setTotal($montant);
    $charge->setDevise('DH');
    $charge->setTvaTaux(null);
    $charge->setTvaDeductible(0);
    $charge->setPaid(1);
    $charge->setFacture(0);
    $charge->setRefunded(0);
    $charge->setDateCharge($ligne->getDateOperation());
    $charge->setDatePayment($ligne->getDateOperation());
    $charge->setModePayment('virement');
    $charge->setDateAdd(date('Y-m-d H:i:s'));
    $charge->setLastEdit(date('Y-m-d H:i:s'));
    $charge->add();
    $idCharge = charge::getLastId();

    $ligne->setIdCharge($idCharge);
    $ligne->setStatut('matched_charge');
    $ligne->setLastEdit(date('Y-m-d H:i:s'));
    $ligne->edit();

    echo json_encode(array('success' => 1, 'id_charge' => $idCharge));
}
