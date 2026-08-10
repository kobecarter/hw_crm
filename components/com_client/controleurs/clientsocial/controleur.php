<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addClientSocial':
            addClientSocial($_POST);
            break;
        case 'editClientSocial':
            editClientSocial($_POST);
            break;
        case 'deleteClientSocial':
            deleteClientSocial($_POST);
            break;
        case 'revealClientSocialPassword':
            revealClientSocialPassword($_POST);
            break;
        case 'generateClientSocialToken':
            generateClientSocialToken($_POST);
            break;
        case 'revokeClientSocialToken':
            revokeClientSocialToken($_POST);
            break;
    }
}

// Génère un accès temporaire (24h) - réservé aux superadmins en session normale : c'est une
// délégation d'accès, jamais accessible depuis un accès temporaire lui-même (pas de "chaîne" de
// jetons). Le code n'est renvoyé qu'ICI, une seule fois - le front doit l'afficher immédiatement,
// il ne sera plus jamais récupérable ensuite (seul son hash est stocké).
function generateClientSocialToken($data)
{
    global $siteURL;
    if (!isset($_SESSION['user']) || !$_SESSION['user']->isSuperUser()) {
        echo json_encode(array('error' => 'forbidden'));
        return;
    }
    $indices = array("id_client", "scope_type");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('error' => 'missing_fields'));
        return;
    }
    if ($data['scope_type'] === 'specific' && empty($data['scope_plateforme'])) {
        echo json_encode(array('error' => 'missing_plateforme'));
        return;
    }
    $client = client::find($data['id_client'], $_SESSION['agence']);
    if ($client->getId() == 0) {
        echo json_encode(array('error' => 'not_found'));
        return;
    }
    $result = clientsocialtoken::generate(
        $client,
        $data['scope_type'] === 'specific' ? 'specific' : 'all',
        isset($data['scope_plateforme']) ? $data['scope_plateforme'] : null,
        $_SESSION['user']->getId()
    );
    $url = $siteURL . 'components/com_client/controleurs/socialaccess/router.php?token=' . $result['token'];

    if (!empty($data['send_slack']) && defined('SLACK_WEBHOOK_URL_FAMILY') && SLACK_WEBHOOK_URL_FAMILY != '') {
        $clientNom = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getTitre() . ' ' . $client->getNom() . ' ' . $client->getPrenom());
        $porteeTexte = $data['scope_type'] === 'specific' ? ('accès limité à ' . $data['scope_plateforme']) : 'accès à tous les comptes';
        $text = ":key: *Vérification réseaux sociaux* — " . $clientNom . " (" . $porteeTexte . "). Lien (valable 24h, code à transmettre séparément) : " . $url;
        $ch = curl_init(SLACK_WEBHOOK_URL_FAMILY);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json; charset=utf-8'),
            CURLOPT_POSTFIELDS => json_encode(array('text' => $text)),
            CURLOPT_TIMEOUT => 15
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    echo json_encode(array('url' => $url, 'code' => $result['code']));
}

function revokeClientSocialToken($data)
{
    if (!isset($_SESSION['user']) || !$_SESSION['user']->isSuperUser()) {
        echo "2";
        return;
    }
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo "0";
        return;
    }
    $t = clientsocialtoken::find($data['id']);
    if ($t->getId() == 0) {
        echo "2";
        return;
    }
    if ($t->revoke() == 1) {
        echo "1";
    } else {
        echo "2";
    }
}

function addClientSocial($data)
{
    $indices = array("id_client", "plateforme");
    if (fieldCheck($data, $indices)) {
        if (!socialAccessAllowed($data['id_client'], $data['plateforme'])) {
            echo "2";
            return;
        }
        if (buildClientSocial($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editClientSocial($data)
{
    $indices = array("id", "id_client", "plateforme");
    if (fieldCheck($data, $indices)) {
        // find() sans filtre d'agence ici (un accès temporaire n'a pas forcément la même agence
        // en session que le client réel) - le vrai contrôle est socialAccessAllowed() juste après,
        // qui vérifie explicitement que le token correspond à CE client précis.
        $existing = clientsocial::find($data['id']);
        if ($existing->getId() == 0 || !$existing->getClient() || $existing->getClient()->getId() == 0) {
            echo "2";
            return;
        }
        if (!socialAccessAllowed($existing->getClient()->getId(), $existing->getPlateforme())) {
            echo "2";
            return;
        }
        if (buildClientSocial($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteClientSocial($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $existing = clientsocial::find($data['id']);
        if ($existing->getId() == 0 || !$existing->getClient() || $existing->getClient()->getId() == 0) {
            echo "2";
            return;
        }
        if (!socialAccessAllowed($existing->getClient()->getId(), $existing->getPlateforme())) {
            echo "2";
            return;
        }
        if ($existing->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

// Déchiffrement à la demande : jamais envoyé dans le HTML de la page, seulement sur ce clic
// explicite - et re-vérifié serveur ici (pas seulement caché en CSS/JS côté front). Autorisé pour
// un superadmin en session normale OU un accès temporaire valide scopé sur ce client/cette
// plateforme (socialRevealAllowed(), includes/functions/functions.php).
function revealClientSocialPassword($data)
{
    $indices = array("id");
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('error' => 'missing_id'));
        return;
    }
    $existing = clientsocial::find($data['id']);
    if ($existing->getId() == 0 || !$existing->getClient() || $existing->getClient()->getId() == 0) {
        echo json_encode(array('error' => 'not_found'));
        return;
    }
    if (!socialRevealAllowed($existing->getClient()->getId(), $existing->getPlateforme())) {
        echo json_encode(array('error' => 'forbidden'));
        return;
    }
    echo json_encode(array('password' => decryptSocialCredential($existing->getPasswordEnc())));
}

function buildClientSocial($data, $id = null)
{
    $clientsocial = new clientsocial();

    if ($id) {
        $clientsocial = clientsocial::find($id);
    }

    // findAny() (pas find()) : ce contrôleur est aussi appelé depuis un accès temporaire sans
    // $_SESSION['user'] (components/com_client/controleurs/socialaccess/router.php) - find()
    // ferait une erreur fatale dans ce cas. L'autorisation réelle est déjà vérifiée par
    // socialAccessAllowed() dans chaque fonction appelante ci-dessus.
    $clientsocial->setClient(client::findAny($data['id_client']));
    $clientsocial->setPlateforme($data['plateforme']);
    $clientsocial->setLogin(isset($data['login']) ? $data['login'] : '');
    // Un champ mot de passe laissé vide (modification sans le changer) ne doit PAS écraser la
    // valeur chiffrée existante par une chaîne vide.
    if (isset($data['password']) && $data['password'] !== '') {
        $clientsocial->setPasswordEnc(encryptSocialCredential($data['password']));
    } elseif ($id) {
        $existingPass = clientsocial::find($id);
        $clientsocial->setPasswordEnc($existingPass->getPasswordEnc());
    } else {
        $clientsocial->setPasswordEnc('');
    }
    $clientsocial->setLien(isset($data['lien']) ? $data['lien'] : '');
    $clientsocial->setEmailRecuperation(isset($data['email_recuperation']) ? $data['email_recuperation'] : '');
    $clientsocial->setTelephoneRecuperation(isset($data['telephone_recuperation']) ? $data['telephone_recuperation'] : '');
    $clientsocial->setRemarque(isset($data['remarque']) ? $data['remarque'] : '');
    $clientsocial->setDateAdd(date("Y-m-d"));
    $clientsocial->setLastEdit(date("Y-m-d"));

    return $clientsocial;
}
