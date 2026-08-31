<?php

if ($_SESSION['user']->hasDroit('view', 'com_rapprochement')) {
    // Certaines agences facturent toutes depuis le Maroc et partagent le même pool de comptes
    // bancaires (même regroupement que previewReleve()/bank::getBanksByAgence()) : les comptes
    // personnels de HW Label (Hamid, Zakaria) doivent aussi apparaître pour Verse Concept, qui
    // n'a aucun compte personnel qui lui soit propre - sans ce regroupement, le mode "Compte
    // courant" de la fenêtre "Insérer le justificatif" reste vide pour cette agence.
    $groupeMaroc = array(1, 3, 25);
    $agencesARechercher = in_array($_SESSION['agence'], $groupeMaroc) ? $groupeMaroc : $_SESSION['agence'];
    // Les comptes personnels (ex: remboursement de frais) sont exclus de la checklist et de la
    // détection automatique - seuls les comptes de l'entreprise concernent BANK STATEMENT.
    $banks = array_values(array_filter(bank::findAll($agencesARechercher), function ($b) {
        return !$b->getExcluRapprochement();
    }));
    // Comptes personnels, à l'inverse - alimentent uniquement le mode "Compte courant" de la
    // fenêtre "Insérer le justificatif" (un débit qui n'est pas une charge de l'agence mais un
    // transfert vers le compte courant d'un titulaire).
    $banksPerso = array_values(array_filter(bank::findAll($agencesARechercher), function ($b) {
        return $b->getExcluRapprochement();
    }));

    // La liste plate d'un seul lot est remplacée par une liste de lots (un import = une carte
    // résumé), le plus récent déplié par défaut - chaque carte porte déjà ses lignes et ses
    // compteurs pour éviter un aller-retour AJAX supplémentaire au clic (le nombre de lots reste
    // limité, un par relevé importé).
    $lots = releveLot::findAll($_SESSION['agence']);
    $lotsData = array();
    foreach ($lots as $lot) {
        $lignes = releveLigne::findAll($_SESSION['agence'], $lot->getLotImport());

        // Résumé affiché dans la fenêtre "Supprimer cet import" - calculé ici plutôt qu'en JS
        // pour que la fenêtre annonce EXACTEMENT ce que supprimerLot() va faire (jamais une
        // suppression en aveugle) : une charge "existante trouvée" (debit_charge_existante) ou un
        // règlement "déjà enregistré" associé (credit_reglement_existant, voir "Associer ce
        // règlement") étaient déjà là avant cet import - ils sont seulement déliés, jamais
        // supprimés ; les autres charges/paiements référencés par une ligne de ce lot ont, eux,
        // été créés PAR cet import.
        $idsChargeASupprimer = array();
        $idsPaymentASupprimer = array();
        $idsTvaARevert = array();
        $nbChargesExistantesLiees = 0;
        $nbPaiementsExistantsLies = 0;
        foreach ($lignes as $l) {
            $infosL = $l->getDonneesMatchingArray();
            if ($l->getIdPayment()) {
                if (isset($infosL['type']) && $infosL['type'] === 'credit_reglement_existant') {
                    $nbPaiementsExistantsLies++;
                } elseif (!in_array($l->getIdPayment(), $idsPaymentASupprimer)) {
                    $idsPaymentASupprimer[] = $l->getIdPayment();
                }
            }
            if ($l->getIdCharge()) {
                if (isset($infosL['type']) && $infosL['type'] === 'debit_charge_existante') {
                    $nbChargesExistantesLiees++;
                } elseif (!in_array($l->getIdCharge(), $idsChargeASupprimer)) {
                    $idsChargeASupprimer[] = $l->getIdCharge();
                }
            }
            if ($l->getIdTva() && !in_array($l->getIdTva(), $idsTvaARevert)) {
                $idsTvaARevert[] = $l->getIdTva();
            }
        }

        $lotsData[] = array(
            'lot' => $lot,
            'compteurs' => releveLigne::compterParLot($lot->getLotImport()),
            'lignes' => $lignes,
            'resume_suppression' => array(
                'nb_lignes' => count($lignes),
                'nb_charges' => count($idsChargeASupprimer),
                'nb_paiements' => count($idsPaymentASupprimer),
                'nb_tva' => count($idsTvaARevert),
                'nb_charges_liees' => $nbChargesExistantesLiees,
                'nb_paiements_lies' => $nbPaiementsExistantsLies
            )
        );
    }

    // Pour la fenêtre "Insérer le justificatif" (lien vers un bulletin de paie ou un fournisseur
    // existant) - listes globales, chargées une seule fois pour alimenter les <select> des modales.
    // Les titulaires inactifs restent proposés (un bulletin de paie peut concerner un mois où le
    // titulaire était encore actif, même s'il a quitté depuis) - pas les autres statuts inactifs
    // (stagiaire, période de test...), triés après les actifs et signalés dans leur libellé.
    $employesPourJustificatif = array_values(array_filter(resourcehumaine::findAll(), function ($r) {
        return $r->isActive() || $r->getStatus() === 'Titulaire';
    }));
    usort($employesPourJustificatif, function ($a, $b) {
        return $b->isActive() <=> $a->isActive();
    });
    $fournisseursActifs = fournisseur::findAll(true);

    // Assignation manuelle (crédit -> facture, débit -> charge existante) : la détection
    // automatique (montant exact + nom/date proches) ne trouve pas toujours le bon candidat -
    // l'utilisateur doit pouvoir choisir n'importe quelle facture ou charge existante de l'agence,
    // pas seulement celles proposées automatiquement (jamais de choix imposé : ces listes
    // complètes alimentent un second niveau de sélection, en plus des candidats détectés). Toutes
    // les factures sont incluses, MÊME déjà payées (pas de filtre statu=-1) : l'objectif ici est de
    // lier un paiement à une facture, pas de suivre les impayés - une facture déjà soldée reste un
    // choix valide (double règlement à rapprocher, correction, etc.).
    $facturesOuvertes = facture::findAll(false, false, false, false, false, false, $_SESSION['agence']);
    // Fenêtre "Rechercher le client" (fenêtre "Choisir la facture", quand aucun client n'a été
    // détecté automatiquement dans le libellé du crédit) - liste complète des clients actifs de
    // l'agence pour une recherche par nom, puis filtrage des factures ci-dessus par client choisi
    // (tout en JS, aucun aller-retour AJAX supplémentaire nécessaire).
    $clientsPourRapprochement = client::findAll(true, false, $_SESSION['agence']);

    // Fenêtre "Choisir la facture" : dès qu'un client est identifié pour la ligne (candidat
    // auto-détecté ou recherche manuelle), on affiche AUSSI toutes ses factures et tous ses
    // règlements déjà enregistrés - jamais une affectation à l'aveugle, l'admin voit tout l'historique
    // du client avant de confirmer. Regroupées par client une seule fois ici (même convention "tout
    // charger au chargement de la page, filtrer en JS" que $facturesOuvertes/$chargesDisponibles).
    $facturesParClient = array();
    foreach ($facturesOuvertes as $fParClient) {
        $cParClient = $fParClient->getClient();
        if (!$cParClient) {
            continue;
        }
        $facturesParClient[$cParClient->getId()][] = array(
            'id' => $fParClient->getId(),
            'numero' => $fParClient->getNumero(),
            'date' => $fParClient->getDateFacture(),
            'total' => round((float) $fParClient->getTotal(), 2),
            'reste' => round((float) $fParClient->getReste(), 2),
            'devise' => $fParClient->getDevise()
        );
    }

    // "Associer ce règlement" (panneau ci-dessus) : un règlement déjà rattaché à une AUTRE ligne
    // de relevé reste sélectionnable (comme les charges "déjà affectées") mais son badge le
    // signale, et le serveur bloque avec une confirmation avant réaffectation (même patron que
    // verifierReaffectationCharge(), voir verifierReaffectationPayment() côté contrôleur).
    $idsPaymentDejaLies = releveLigne::findAllIdPaymentLies();

    $reglementsParClient = array();
    foreach (payment::findAll(false, true, $_SESSION['agence']) as $pParClient) {
        $factureDuReglement = $pParClient->getFacture();
        $cDuReglement = $factureDuReglement ? $factureDuReglement->getClient() : null;
        if (!$cDuReglement) {
            continue;
        }
        $reglementsParClient[$cDuReglement->getId()][] = array(
            'id' => $pParClient->getId(),
            'date' => $pParClient->getDatePayment(),
            'montant' => round((float) $pParClient->getMontant(), 2),
            'methode' => $pParClient->getMethodePayment(),
            // Numéro tel qu'imprimé sur le reçu PDF du paiement (payment::pdfPayment()) : le
            // numéro de facture suivi du numero_sequence assigné une fois pour toutes à la
            // création (facture::assignNextPaymentSeq()) - pas juste le numéro de facture seul,
            // qui ne permet pas de distinguer entre eux plusieurs règlements d'une même facture.
            'facture_numero' => $factureDuReglement->getNumero() . ($pParClient->getNumeroSequence() !== null && $pParClient->getNumeroSequence() !== '' ? '-' . $pParClient->getNumeroSequence() : ''),
            'devise' => $factureDuReglement->getDevise(),
            'deja_lie' => in_array($pParClient->getId(), $idsPaymentDejaLies)
        );
    }

    $idsChargeDejaLies = releveLigne::findAllIdChargeLies();
    $toutesLesChargesRapprochement = charge::findAll(true, $_SESSION['agence']);
    $chargesDisponibles = array_values(array_filter($toutesLesChargesRapprochement, function ($c) use ($idsChargeDejaLies) {
        return !in_array($c->getId(), $idsChargeDejaLies);
    }));
    // Règle 4 (listes distinctes) : les charges déjà rattachées à une AUTRE ligne de relevé restent
    // visibles (pas cachées comme avant) mais dans un second groupe distinct du <select> - la
    // sécurité anti-doublon (verifierReaffectationCharge() côté controleur) bloque leur sélection
    // tant que l'utilisateur n'a pas confirmé la réaffectation dans la fenêtre dédiée.
    $chargesDejaAffectees = array_values(array_filter($toutesLesChargesRapprochement, function ($c) use ($idsChargeDejaLies) {
        return in_array($c->getId(), $idsChargeDejaLies);
    }));

    include_once ("components/com_rapprochement/views/rapprochement/list.php");
}
