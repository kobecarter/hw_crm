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
            if ($toutesAgences && $c->getAgence()) {
                $item['agence'] = $c->getAgence()->getNom();
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
            }
            $items[] = $item;
        }
        if (!empty($items)) {
            $groupes['charges'] = array('label' => 'Charges', 'icon' => 'fa-receipt', 'items' => $items);
        }
    }

    echo json_encode(array('success' => 1, 'terme' => $terme, 'toutesAgences' => $toutesAgences, 'groupes' => $groupes));
}
