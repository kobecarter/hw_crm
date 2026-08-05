<?php

if ($_SESSION['user']->hasDroit('view', 'com_rapprochement')) {
    // Les comptes personnels (ex: remboursement de frais) sont exclus de la checklist et de la
    // détection automatique - seuls les comptes de l'entreprise concernent BANK STATEMENT.
    $banks = array_values(array_filter(bank::findAll($_SESSION['agence']), function ($b) {
        return !$b->getExcluRapprochement();
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
        // suppression en aveugle) : une charge "existante trouvée" (debit_charge_existante) était
        // déjà là avant cet import - elle est seulement déliée, jamais supprimée ; les autres
        // charges référencées par une ligne de ce lot ont, elles, été créées PAR cet import.
        $idsChargeASupprimer = array();
        $idsPaymentASupprimer = array();
        $idsTvaARevert = array();
        $nbChargesExistantesLiees = 0;
        foreach ($lignes as $l) {
            $infosL = $l->getDonneesMatchingArray();
            if ($l->getIdPayment() && !in_array($l->getIdPayment(), $idsPaymentASupprimer)) {
                $idsPaymentASupprimer[] = $l->getIdPayment();
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
                'nb_charges_liees' => $nbChargesExistantesLiees
            )
        );
    }

    // Pour la fenêtre "Insérer le justificatif" (lien vers un bulletin de paie ou un fournisseur
    // existant) - listes globales, chargées une seule fois pour alimenter les <select> des modales.
    $employesActifs = array_values(array_filter(resourcehumaine::findAll(), function ($r) {
        return $r->isActive();
    }));
    $fournisseursActifs = fournisseur::findAll(true);

    // Assignation manuelle (crédit -> facture, débit -> charge existante) : la détection
    // automatique (montant exact + nom/date proches) ne trouve pas toujours le bon candidat -
    // l'utilisateur doit pouvoir choisir n'importe quelle facture ouverte ou charge existante de
    // l'agence, pas seulement celles proposées automatiquement (jamais de choix imposé : ces
    // listes complètes alimentent un second niveau de sélection, en plus des candidats détectés).
    $facturesOuvertes = facture::findAll(-1, false, false, false, false, false, $_SESSION['agence']);
    $idsChargeDejaLies = releveLigne::findAllIdChargeLies();
    $chargesDisponibles = array_values(array_filter(charge::findAll(true, $_SESSION['agence']), function ($c) use ($idsChargeDejaLies) {
        return !in_array($c->getId(), $idsChargeDejaLies);
    }));

    include_once ("components/com_rapprochement/views/rapprochement/list.php");
}
