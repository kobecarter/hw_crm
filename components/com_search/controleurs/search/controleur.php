<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'rechercheGlobale':
            rechercheGlobale($_GET);
            break;
    }
}

// Recherche globale du bandeau haut : interroge chaque module pour lequel l'utilisateur a le
// droit "view", limite à quelques résultats par module (aperçu, pas une liste exhaustive - la
// page du module reste l'endroit pour ça), et renvoie une structure groupée identique à celle de
// getAlertesUrgentes() (label/icon/items, chaque item = titre/sous_titre/url) pour réutiliser le
// même rendu JS côté client.
//
// toutesAgences=1 : lève le filtre d'agence sur les modules qui en ont un (client/facture/devis/
// resourcehumaine/charge - fournisseur::search() n'a jamais eu de filtre agence). Déclenché
// uniquement depuis le JS après un premier essai sans résultat dans l'agence courante, jamais par
// défaut - chaque item porte alors un "agence" (nom) pour que l'utilisateur sache d'où vient le
// résultat.
function rechercheGlobale($data)
{
    header('Content-Type: application/json');
    $terme = isset($data['q']) ? trim($data['q']) : '';
    if (mb_strlen($terme) < 2) {
        echo json_encode(array('success' => 1, 'groupes' => array()));
        return;
    }

    $toutesAgences = isset($data['toutesAgences']) && $data['toutesAgences'] == '1';
    $agence = $toutesAgences ? false : $_SESSION['agence'];
    $user = $_SESSION['user'];
    $groupes = array();

    if ($user->hasDroit('view', 'com_client')) {
        $items = array();
        foreach (client::search($terme, $agence) as $c) {
            $nom = trim((string) $c->getRaisonSocial()) !== '' ? $c->getRaisonSocial() : trim($c->getPrenom() . ' ' . $c->getNom());
            $item = array(
                'titre' => $nom !== '' ? $nom : '(sans nom)',
                'sous_titre' => trim((string) $c->getEmail()) !== '' ? $c->getEmail() : ((string) $c->getTel()),
                'url' => 'index.php?option=com_client&task=showDetails&id=' . $c->getId()
            );
            // Agence, statut actif/inactif et chiffre d'affaires : toujours affichés, pas
            // seulement pour les résultats "autres agences" - utile même sur un résultat de
            // l'agence courante pour éviter d'avoir à ouvrir la fiche juste pour ces 3 infos.
            if ($c->getAgence()) {
                $item['agence'] = $c->getAgence()->getNom();
                $item['agence_id'] = $c->getAgence()->getId();
            }
            $item['actif'] = $c->isActive() ? true : false;
            // Chiffre d'affaires réalisé avec ce client (paiements réellement encaissés, toutes
            // factures confondues). Toujours affiché, y compris à 0 - un "0 DH" est une
            // information (client jamais facturé) plutôt qu'un simple silence.
            $caParDevise = $c->getChiffreAffaireParDevise();
            if (!empty($caParDevise)) {
                $parts = array();
                foreach ($caParDevise as $devise => $montant) {
                    $parts[] = number_format($montant, 0, ',', ' ') . ' ' . $devise;
                }
                $item['ca'] = implode(' + ', $parts);
            } else {
                $item['ca'] = '0 DH';
            }
            $items[] = $item;
        }
        if (!empty($items)) {
            $groupes['clients'] = array('label' => 'Clients', 'icon' => 'fa-user-tie', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_facture')) {
        $items = array();
        foreach (facture::search($terme, $agence) as $f) {
            $client = $f->getClient();
            $nomClient = $client ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : '';
            $item = array(
                'titre' => 'Facture N°' . $f->getNumero(),
                'sous_titre' => $nomClient,
                'url' => 'index.php?option=com_facture&task=show&id=' . $f->getId()
            );
            if ($toutesAgences && $client && $client->getAgence()) {
                $item['agence'] = $client->getAgence()->getNom();
                $item['agence_id'] = $client->getAgence()->getId();
            }
            $items[] = $item;
        }
        if (!empty($items)) {
            $groupes['factures'] = array('label' => 'Factures', 'icon' => 'fa-file-invoice', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_devis')) {
        $items = array();
        foreach (devis::search($terme, $agence) as $d) {
            $client = $d->getClient();
            $nomClient = $client ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : '';
            $item = array(
                'titre' => 'Devis N°' . $d->getNumero(),
                'sous_titre' => $nomClient,
                'url' => 'index.php?option=com_devis&task=edit&id=' . $d->getId()
            );
            if ($toutesAgences && $client && $client->getAgence()) {
                $item['agence'] = $client->getAgence()->getNom();
                $item['agence_id'] = $client->getAgence()->getId();
            }
            $items[] = $item;
        }
        if (!empty($items)) {
            $groupes['devis'] = array('label' => 'Devis', 'icon' => 'fa-file-signature', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_fournisseur')) {
        $items = array();
        foreach (fournisseur::search($terme) as $f) {
            $nom = trim((string) $f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getPrenom() . ' ' . $f->getNom());
            $items[] = array(
                'titre' => $nom !== '' ? $nom : '(sans nom)',
                'sous_titre' => (string) $f->getEmail(),
                'url' => 'index.php?option=com_fournisseur&task=edit&id=' . $f->getId()
            );
        }
        // com_fournisseur n'a jamais été filtré par agence : ne pas relancer sous
        // toutesAgences, il ne peut pas y avoir de nouveaux résultats.
        if (!empty($items) && !$toutesAgences) {
            $groupes['fournisseurs'] = array('label' => 'Fournisseurs', 'icon' => 'fa-truck', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_resourcehumaine')) {
        $items = array();
        foreach (resourcehumaine::search($terme, $agence) as $employe) {
            $item = array(
                'titre' => $employe->getFirstName() . ' ' . $employe->getLastName(),
                'sous_titre' => 'Employé',
                'url' => 'index.php?option=com_resourcehumaine&task=edit&id=' . $employe->getId()
            );
            if ($toutesAgences && $employe->getAgency()) {
                $item['agence'] = $employe->getAgency()->getNom();
            }
            $items[] = $item;
        }
        if (!empty($items)) {
            $groupes['employes'] = array('label' => 'Employés', 'icon' => 'fa-id-badge', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_charge')) {
        $items = array();
        foreach (charge::search($terme, $agence) as $c) {
            $item = array(
                'titre' => $c->getTitre(),
                'sous_titre' => number_format((float) $c->getTotal(), 2, ',', ' ') . ' ' . $c->getDevise(),
                'url' => 'index.php?option=com_charge&task=edit&id=' . $c->getId()
            );
            if ($toutesAgences && $c->getAgence()) {
                $item['agence'] = $c->getAgence()->getNom();
                $item['agence_id'] = $c->getAgence()->getId();
            }
            $items[] = $item;
        }
        if (!empty($items)) {
            $groupes['charges'] = array('label' => 'Charges', 'icon' => 'fa-receipt', 'items' => $items);
        }
    }

    // Comptes bancaires : nom de banque, RIB/IBAN, ICE - typiquement atteint par une recherche
    // par mot-clé institutionnel (ex: "BMCE") ou par un numéro de compte.
    if ($user->hasDroit('view', 'com_bank')) {
        $items = array();
        foreach (bank::search($terme) as $b) {
            $items[] = array(
                'titre' => trim($b->getBanque() . ($b->getLabel() ? ' - ' . $b->getLabel() : '')),
                'sous_titre' => trim((string) $b->getRib()) !== '' ? 'RIB ' . $b->getRib() : (string) $b->getRaisonSociale(),
                'url' => 'index.php?option=com_bank&task=edit&id=' . $b->getId()
            );
        }
        if (!empty($items)) {
            $groupes['banques'] = array('label' => 'Comptes bancaires', 'icon' => 'fa-university', 'items' => $items);
        }
    }

    // Agences elles-mêmes (nom, raison sociale, gérant, ville, ICE/RC...) : tester le mot-clé
    // "espace agence" (nom d'agence tapé directement) en plus des modules ci-dessus. Gardé sur le
    // droit 'edit' plutôt que 'view' car seule la page d'édition (task=edit) permet réellement de
    // consulter le détail d'une agence dans ce module - pas de page de consultation séparée.
    if ($user->hasDroit('edit', 'com_agence')) {
        $items = array();
        foreach (agence::search($terme, $_SESSION['langue']) as $a) {
            $nom = trim((string) $a->getNom()) !== '' ? $a->getNom() : (string) $a->getRaisonSocial();
            $items[] = array(
                'titre' => $nom !== '' ? $nom : '(sans nom)',
                'sous_titre' => trim((string) $a->getManager()) !== '' ? 'Gérant : ' . $a->getManager() : (string) $a->getEmail(),
                'url' => 'index.php?option=com_agence&task=edit&id=' . $a->getId()
            );
        }
        if (!empty($items)) {
            $groupes['agences'] = array('label' => 'Agences', 'icon' => 'fa-building', 'items' => $items);
        }
    }

    // Aucun résultat exact dans aucun module : interpréter l'intention de la recherche (mot-clé
    // métier + reste de la saisie = probablement un nom propre) plutôt que de renvoyer une liste
    // vide - voir suggestionsRecherche() ci-dessous. Ne se déclenche jamais si des résultats
    // exacts existent déjà, pour ne pas noyer une recherche qui a fonctionné.
    $aucunResultatExact = empty($groupes);
    if ($aucunResultatExact) {
        $suggestions = suggestionsRecherche($terme, $user);
        if (!empty($suggestions)) {
            $groupes['suggestions'] = array('label' => 'Suggestions', 'icon' => 'fa-lightbulb', 'items' => $suggestions);
        }
    }

    echo json_encode(array('success' => 1, 'terme' => $terme, 'toutesAgences' => $toutesAgences, 'aucunResultatExact' => $aucunResultatExact, 'groupes' => $groupes));
}

// Fallback "intention" quand aucun module n'a de résultat exact pour la saisie brute. Repose sur
// deux idées simples plutôt que sur un appel IA à chaque frappe (la recherche est déclenchée à
// chaque pause de frappe, un aller-retour LLM serait trop lent pour une barre de recherche) :
//  1. Un mot-clé métier reconnu (ex: "CONGE", "FACTURE") dans la saisie retire ce mot et relance
//     une recherche sur le reste (probablement un nom propre) dans le module concerné, en plus de
//     proposer un lien direct vers la page du module.
//  2. À défaut de mot-clé reconnu, la saisie entière est retentée contre les comptes bancaires
//     (un nom de banque ou un numéro de compte n'a souvent pas de mot-clé qui le précède).
function suggestionsRecherche($terme, $user)
{
    $items = array();
    $motsCongé = array('conge', 'conges', 'congé', 'congés', 'vacance', 'vacances', 'absence', 'absences');
    $motsFacture = array('facture', 'factures');
    $motsFournisseur = array('fournisseur', 'fournisseurs');

    $tokens = preg_split('/\s+/', trim($terme), -1, PREG_SPLIT_NO_EMPTY);
    $motCle = null;
    $reste = array();
    foreach ($tokens as $mot) {
        $bas = mb_strtolower($mot);
        if ($motCle === null && (in_array($bas, $motsCongé, true) || in_array($bas, $motsFacture, true) || in_array($bas, $motsFournisseur, true))) {
            $motCle = $bas;
            continue;
        }
        $reste[] = $mot;
    }
    $resteTerme = trim(implode(' ', $reste));

    if ($motCle !== null && in_array($motCle, $motsCongé, true)) {
        if ($resteTerme !== '' && $user->hasDroit('view', 'com_resourcehumaine')) {
            foreach (resourcehumaine::search($resteTerme, false) as $employe) {
                $items[] = array(
                    'titre' => 'Congés de ' . $employe->getFirstName() . ' ' . $employe->getLastName(),
                    'sous_titre' => 'Aucun résultat exact pour « ' . $terme . ' » - vouliez-vous dire les congés de cet employé ?',
                    'url' => 'index.php?option=com_resourcehumaine&task=absence&id=' . $employe->getId()
                );
            }
        }
    } elseif ($motCle !== null && in_array($motCle, $motsFacture, true)) {
        if ($resteTerme !== '') {
            if ($user->hasDroit('view', 'com_fournisseur')) {
                foreach (fournisseur::search($resteTerme) as $f) {
                    $nom = trim((string) $f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getPrenom() . ' ' . $f->getNom());
                    $items[] = array(
                        'titre' => 'Fournisseur : ' . ($nom !== '' ? $nom : '(sans nom)'),
                        'sous_titre' => 'Vouliez-vous dire ce fournisseur ?',
                        'url' => 'index.php?option=com_fournisseur&task=edit&id=' . $f->getId()
                    );
                }
            }
            if ($user->hasDroit('view', 'com_client')) {
                foreach (client::search($resteTerme, false) as $c) {
                    $nom = trim((string) $c->getRaisonSocial()) !== '' ? $c->getRaisonSocial() : trim($c->getPrenom() . ' ' . $c->getNom());
                    $items[] = array(
                        'titre' => 'Client : ' . ($nom !== '' ? $nom : '(sans nom)'),
                        'sous_titre' => 'Vouliez-vous dire les factures de ce client ?',
                        'url' => 'index.php?option=com_client&task=showDetails&id=' . $c->getId()
                    );
                }
            }
        }
        if ($user->hasDroit('view', 'com_facture')) {
            $items[] = array('titre' => 'Page Factures', 'sous_titre' => 'Parcourir toutes les factures', 'url' => 'index.php?option=com_facture');
        }
        if ($user->hasDroit('view', 'com_fournisseur')) {
            $items[] = array('titre' => 'Page Fournisseurs', 'sous_titre' => 'Parcourir tous les fournisseurs', 'url' => 'index.php?option=com_fournisseur');
        }
    } elseif ($motCle !== null && in_array($motCle, $motsFournisseur, true)) {
        if ($resteTerme !== '' && $user->hasDroit('view', 'com_fournisseur')) {
            foreach (fournisseur::search($resteTerme) as $f) {
                $nom = trim((string) $f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getPrenom() . ' ' . $f->getNom());
                $items[] = array(
                    'titre' => $nom !== '' ? $nom : '(sans nom)',
                    'sous_titre' => 'Vouliez-vous dire ce fournisseur ?',
                    'url' => 'index.php?option=com_fournisseur&task=edit&id=' . $f->getId()
                );
            }
        }
        if ($user->hasDroit('view', 'com_fournisseur')) {
            $items[] = array('titre' => 'Page Fournisseurs', 'sous_titre' => 'Parcourir tous les fournisseurs', 'url' => 'index.php?option=com_fournisseur');
        }
    }

    // Aucun mot-clé reconnu (ou mot-clé reconnu mais sans résultat) : dernière tentative, la
    // saisie entière est peut-être un nom de banque ou un numéro de compte tapé seul.
    if (empty($items) && $user->hasDroit('view', 'com_bank')) {
        foreach (bank::search($terme) as $b) {
            $items[] = array(
                'titre' => trim($b->getBanque() . ($b->getLabel() ? ' - ' . $b->getLabel() : '')),
                'sous_titre' => 'Vouliez-vous dire ce compte bancaire ?',
                'url' => 'index.php?option=com_bank&task=edit&id=' . $b->getId()
            );
        }
    }

    return array_slice($items, 0, 8);
}
