<?php

/* Client Google Drive minimal (compte de service), sans dépendance Composer :
   authentification OAuth2 "service account" (JWT signé RS256 fabriqué à la
   main avec openssl) puis appels REST directs à l'API Drive v3. Utilisé pour
   créer automatiquement le dossier client (Maroc/International) à la création
   d'une facture. Toute méthode échoue silencieusement (retourne false/null)
   si les identifiants ne sont pas configurés — n'a jamais vocation à bloquer
   la création de facture. */
class googleDriveClient
{
    private static $accessToken = null;

    public static function isConfigured()
    {
        return defined('GOOGLE_SERVICE_ACCOUNT_EMAIL')
            && GOOGLE_SERVICE_ACCOUNT_EMAIL !== ''
            && defined('GOOGLE_SERVICE_ACCOUNT_PRIVATE_KEY')
            && GOOGLE_SERVICE_ACCOUNT_PRIVATE_KEY !== '';
    }

    private static function getAccessToken()
    {
        if (self::$accessToken) {
            return self::$accessToken;
        }
        if (!self::isConfigured()) {
            return null;
        }

        $now = time();
        $header = self::base64UrlEncode(json_encode(array('alg' => 'RS256', 'typ' => 'JWT')));
        $claims = self::base64UrlEncode(json_encode(array(
            'iss' => GOOGLE_SERVICE_ACCOUNT_EMAIL,
            'scope' => 'https://www.googleapis.com/auth/drive',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        )));
        $unsigned = $header . '.' . $claims;

        $privateKey = str_replace('\\n', "\n", GOOGLE_SERVICE_ACCOUNT_PRIVATE_KEY);
        $signature = '';
        $signed = openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$signed) {
            error_log('googleDriveClient: échec de signature JWT (clé privée invalide ?)');
            return null;
        }
        $jwt = $unsigned . '.' . self::base64UrlEncode($signature);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            )),
            CURLOPT_TIMEOUT => 15,
        ));
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log('googleDriveClient: erreur cURL token - ' . $curlError);
            return null;
        }
        $decoded = json_decode($response, true);
        if (empty($decoded['access_token'])) {
            error_log('googleDriveClient: échec obtention token - ' . $response);
            return null;
        }
        self::$accessToken = $decoded['access_token'];
        return self::$accessToken;
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function request($method, $url, $body = null)
    {
        $token = self::getAccessToken();
        if (!$token) {
            return null;
        }
        $ch = curl_init($url);
        $headers = array('Authorization: Bearer ' . $token, 'Content-Type: application/json; charset=utf-8');
        $opts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 20,
        );
        if ($method === 'POST') {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        } else {
            $opts[CURLOPT_CUSTOMREQUEST] = $method;
        }
        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log('googleDriveClient: erreur cURL - ' . $curlError);
            return null;
        }
        if ($httpCode >= 300) {
            error_log('googleDriveClient: erreur API Drive (' . $httpCode . ') - ' . $response);
            return null;
        }
        return json_decode($response, true);
    }

    /* Cherche un dossier nommé $name directement sous $parentId ; le crée
       s'il n'existe pas. Retourne son ID, ou null en cas d'échec. */
    public static function findOrCreateFolder($name, $parentId)
    {
        $escapedName = str_replace("'", "\\'", $name);
        $query = "name = '{$escapedName}' and '{$parentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
        $searchUrl = 'https://www.googleapis.com/drive/v3/files?q=' . urlencode($query) . '&fields=files(id,name)&supportsAllDrives=true&includeItemsFromAllDrives=true';
        $result = self::request('GET', $searchUrl);
        if ($result && !empty($result['files'][0]['id'])) {
            return $result['files'][0]['id'];
        }

        $createUrl = 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true';
        $created = self::request('POST', $createUrl, array(
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => array($parentId),
        ));
        return !empty($created['id']) ? $created['id'] : null;
    }

    /* Crée (ou retrouve) toute une arborescence de dossiers imbriqués sous
       $rootFolderId, ex: ['MAROC', 'MARRAKECH', 'CLIENT XYZ']. Retourne
       l'ID du dernier dossier (le dossier client), ou null en cas d'échec. */
    public static function createFolderPath($segments, $rootFolderId)
    {
        $currentParent = $rootFolderId;
        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }
            $folderId = self::findOrCreateFolder($segment, $currentParent);
            if (!$folderId) {
                return null;
            }
            $currentParent = $folderId;
        }
        return $currentParent;
    }

    public static function shareFolder($folderId, $email, $role = 'writer')
    {
        $url = 'https://www.googleapis.com/drive/v3/files/' . $folderId . '/permissions?supportsAllDrives=true&sendNotificationEmail=false';
        $result = self::request('POST', $url, array(
            'role' => $role,
            'type' => 'user',
            'emailAddress' => $email,
        ));
        return !empty($result['id']);
    }

    public static function getFolderLink($folderId)
    {
        return 'https://drive.google.com/drive/folders/' . $folderId;
    }

    /* Normalisation grossière (minuscules, accents retirés au mieux, ponctuation réduite à des
       espaces) pour comparer deux noms de dossier indépendamment de la casse/accentuation/
       ponctuation - même idée que normaliserTitreServiceIa() côté devis (assets JS), ici côté
       serveur puisque la comparaison se fait avant même d'afficher quoi que ce soit au client. */
    private static function normaliserNomDossier($texte)
    {
        $t = mb_strtolower(trim((string) $texte), 'UTF-8');
        $translitere = @iconv('UTF-8', 'ASCII//TRANSLIT', $t);
        if ($translitere !== false) {
            $t = $translitere;
        }
        $t = preg_replace('/[^a-z0-9]+/', ' ', $t);
        return trim($t);
    }

    /* Liste les dossiers déjà présents directement sous $parentId (sans filtre de nom) - utilisé
       par rechercherDossiersSimilaires() pour comparer un nom proposé à l'existant plutôt que de
       ne tester qu'une correspondance exacte comme le fait findOrCreateFolder(). */
    private static function listerDossiers($parentId)
    {
        $query = "'{$parentId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
        $url = 'https://www.googleapis.com/drive/v3/files?q=' . urlencode($query) . '&fields=files(id,name)&pageSize=200&supportsAllDrives=true&includeItemsFromAllDrives=true';
        $result = self::request('GET', $url);
        return ($result && !empty($result['files'])) ? $result['files'] : array();
    }

    /* Cherche, parmi les dossiers déjà présents sous $parentId, ceux dont le nom RESSEMBLE à $nom
       sans y correspondre exactement (une correspondance exacte est déjà gérée silencieusement et
       sans intervention par findOrCreateFolder - inutile de la remonter ici). Retourne un tableau
       trié par score de ressemblance décroissant, uniquement au-dessus de $seuil (%). */
    public static function rechercherDossiersSimilaires($nom, $parentId, $seuil = 55)
    {
        if (!self::isConfigured() || !$parentId) {
            return array();
        }
        $nomNormalise = self::normaliserNomDossier($nom);
        if ($nomNormalise === '') {
            return array();
        }

        $correspondances = array();
        foreach (self::listerDossiers($parentId) as $fichier) {
            $autreNormalise = self::normaliserNomDossier($fichier['name']);
            if ($autreNormalise === '' || $autreNormalise === $nomNormalise) {
                continue;
            }
            similar_text($nomNormalise, $autreNormalise, $score);
            if ($score >= $seuil) {
                $correspondances[] = array(
                    'id' => $fichier['id'],
                    'nom' => $fichier['name'],
                    'score' => (int) round($score),
                    'lien' => self::getFolderLink($fichier['id']),
                );
            }
        }
        usort($correspondances, function ($a, $b) { return $b['score'] <=> $a['score']; });
        return $correspondances;
    }

    /* Résout le dossier PARENT (Maroc/Ville ou International/Pays - des dossiers "bucket" jamais
       source de doublon ambigu, donc créés/retrouvés directement) puis cherche, sous ce parent,
       des dossiers au nom proche de celui qui serait proposé pour ce client - SANS créer le
       dossier client lui-même. Utilisé à la création d'une facture pour proposer une étape de
       validation si un dossier similaire existe déjà, avant de laisser ensureClientFolder() créer
       ou retrouver le dossier normalement. */
    public static function verifierDossierClientSimilaire($client)
    {
        $resultatVide = array('configure' => false, 'nom_propose' => null, 'parent_id' => null, 'similaires' => array());
        if (!self::isConfigured() || !$client || $client->getId() == 0) {
            return $resultatVide;
        }

        $pays = trim($client->getPays());
        $isMaroc = ($pays === '') || (stripos($pays, 'maroc') !== false) || (stripos($pays, 'morocco') !== false);
        if ($isMaroc) {
            $ville = trim($client->getVille());
            $segmentsParent = array('MAROC', mb_strtoupper($ville !== '' ? $ville : 'NON CLASSÉ', 'UTF-8'));
        } else {
            $segmentsParent = array('INTERNATIONAL', mb_strtoupper($pays, 'UTF-8'));
        }

        $parentId = self::createFolderPath($segmentsParent, GOOGLE_DRIVE_ROOT_FOLDER_ID);
        if (!$parentId) {
            return $resultatVide;
        }

        $nomClient = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getNom() . ' ' . $client->getPrenom());
        $nomPropose = mb_strtoupper($nomClient, 'UTF-8');

        return array(
            'configure' => true,
            'nom_propose' => $nomPropose,
            'parent_id' => $parentId,
            'similaires' => self::rechercherDossiersSimilaires($nomPropose, $parentId),
        );
    }

    /* Point d'entrée partagé : classe le client (Maroc par ville / International
       par pays, en MAJUSCULES), crée (ou retrouve, idempotent) son dossier, et
       le partage avec GOOGLE_DRIVE_SHARE_WITH_EMAIL. Appelée à la fois depuis
       la création de facture et depuis la création du ticket Trello (paiement
       devis effectué) : peu importe laquelle des deux arrive en premier, le
       dossier n'est créé qu'une seule fois, l'autre le retrouve simplement.
       Retourne le lien du dossier, ou null si non configuré / échec.

       $dossierExistantChoisi : ID d'un dossier Drive déjà existant, choisi par l'utilisateur à
       l'étape de validation "dossier similaire trouvé" (voir verifierDossierClientSimilaire() et
       com_facture/controleurs/facture/controleur.php:addFacture) plutôt que de laisser cette
       méthode créer un nouveau dossier - évite le doublon que cette étape de validation existe
       justement pour prévenir. */
    public static function ensureClientFolder($client, $dossierExistantChoisi = null)
    {
        if (!self::isConfigured() || !$client || $client->getId() == 0) {
            return null;
        }

        if ($dossierExistantChoisi) {
            if (defined('GOOGLE_DRIVE_SHARE_WITH_EMAIL') && GOOGLE_DRIVE_SHARE_WITH_EMAIL !== '') {
                self::shareFolder($dossierExistantChoisi, GOOGLE_DRIVE_SHARE_WITH_EMAIL, 'writer');
            }
            return self::getFolderLink($dossierExistantChoisi);
        }

        $pays = trim($client->getPays());
        $isMaroc = ($pays === '') || (stripos($pays, 'maroc') !== false) || (stripos($pays, 'morocco') !== false);

        if ($isMaroc) {
            $ville = trim($client->getVille());
            $sousDossier = $ville !== '' ? $ville : 'NON CLASSÉ';
            $segments = array('MAROC', mb_strtoupper($sousDossier, 'UTF-8'));
        } else {
            $segments = array('INTERNATIONAL', mb_strtoupper($pays, 'UTF-8'));
        }

        $nomClient = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getNom() . ' ' . $client->getPrenom());
        $segments[] = mb_strtoupper($nomClient, 'UTF-8');

        $folderId = self::createFolderPath($segments, GOOGLE_DRIVE_ROOT_FOLDER_ID);
        if (!$folderId) {
            error_log('googleDriveClient::ensureClientFolder: échec création dossier pour client #' . $client->getId());
            return null;
        }

        if (defined('GOOGLE_DRIVE_SHARE_WITH_EMAIL') && GOOGLE_DRIVE_SHARE_WITH_EMAIL !== '') {
            self::shareFolder($folderId, GOOGLE_DRIVE_SHARE_WITH_EMAIL, 'writer');
        }

        return self::getFolderLink($folderId);
    }
}
