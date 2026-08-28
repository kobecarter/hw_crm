<?php

// Catalogue réel du site public (services, formations, réalisations, témoignages,
// coordonnées), consommé via l'API hw-admin/crmCatalogApi.php du site (secret partagé
// CRM_BRIDGE_SECRET, même patron que com_fidelite/controleurs/router.php côté CRM mais dans le
// sens inverse) - PAS le contenu de crm_expertise/crm_realisation/crm_temoignage/crm_config
// (entités CRM historiques, quelques lignes non maintenues, sans rapport avec les vraies pages
// du site). Reproduit exactement les filtres utilisés par l'espace client web pour l'onglet
// Découvrir (voir helloworld/components/com_client/views/client/facture.php) : services
// "vitrine" (actifs, racine, home=1) et formations à venir, pour que le mobile affiche la même
// chose que le web plutôt qu'une liste parallèle.
//
// Avant cette version, ce fichier lisait la base du site directement via fidelite::siteDb()
// (connexion mysqli avec les identifiants trouvés en incluant le config.php du site par chemin
// relatif) - impossible dès que le CRM et le site ne sont plus sur le même serveur, ce qui est
// le cas réel en production (CRM sur helloworldlabel.ae, site sur helloworld-agency.com). D'où
// ce pont HTTP, symétrique de celui déjà en place pour la fidélité.
class siteCatalog
{
    // Domaine public réel, pour les liens "Découvrir" qui doivent ouvrir la vraie page du site
    // (pas l'URL locale de dev, jamais joignable depuis le téléphone d'un client).
    const PUBLIC_SITE_URL = "https://www.helloworld-agency.com/";

    private static function call($task, $params = array())
    {
        global $hwaURL;
        if (!defined('CRM_BRIDGE_SECRET') || CRM_BRIDGE_SECRET === '') {
            error_log('siteCatalog::call - CRM_BRIDGE_SECRET non configuré (config.secrets.php).');
            return null;
        }
        $query = array_merge(array('task' => $task, 'secret' => CRM_BRIDGE_SECRET), $params);
        $url = rtrim($hwaURL, '/') . '/hw-admin/crmCatalogApi.php?' . http_build_query($query);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log('siteCatalog::call - ' . $task . ' : erreur de connexion (' . $curlError . ')');
            return null;
        }
        if ($httpCode !== 200) {
            error_log('siteCatalog::call - ' . $task . ' : HTTP ' . $httpCode);
            return null;
        }
        $decoded = json_decode($response, true);
        return isset($decoded['data']) ? $decoded['data'] : null;
    }

    public static function findServices($langue = 'fr')
    {
        $data = self::call('apiServices', array('langue' => $langue));
        if (!is_array($data)) {
            return array();
        }
        $items = array();
        foreach ($data as $row) {
            $items[] = array(
                "id" => $row['id'],
                "titre" => $row['titre'],
                "extrait" => $row['extrait'],
                "photo" => $row['photo'],
                "lien" => !empty($row['slug'])
                    ? self::PUBLIC_SITE_URL . 'service/' . $row['slug'] . '/'
                    : self::PUBLIC_SITE_URL,
            );
        }
        return $items;
    }

    public static function findFormations($langue = 'fr', $limit = 6)
    {
        $data = self::call('apiFormations', array('langue' => $langue));
        if (!is_array($data)) {
            return array();
        }
        $items = array();
        foreach ($data as $row) {
            $items[] = array(
                "id" => $row['id'],
                "titre" => $row['titre'],
                "extrait" => $row['extrait'],
                "photo" => $row['photo'],
                "date_debut" => $row['date_debut'],
                "date_fin" => $row['date_fin'],
                "lieu" => $row['lieu'],
                "lien" => !empty($row['slug'])
                    ? self::PUBLIC_SITE_URL . 'formation/' . $row['slug'] . '/'
                    : self::PUBLIC_SITE_URL,
            );
            if (count($items) >= $limit) {
                break;
            }
        }
        return $items;
    }

    // Équivalent simplifié de url_rewriting() (helloworld/includes/functions) pour construire
    // le même lien que reference::getLink() sans dépendre de cette fonction (définie côté site,
    // pas chargée dans le contexte CRM).
    private static function slugify($value)
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim($value, '-');
    }

    public static function findReferences($langue = 'fr')
    {
        $data = self::call('apiReferences', array('langue' => $langue));
        if (!is_array($data)) {
            return array();
        }
        $items = array();
        foreach ($data as $row) {
            $items[] = array(
                "id" => $row['id'],
                "titre" => $row['titre'],
                "extrait" => $row['extrait'],
                "photo" => $row['photo'],
                "lien" => !empty($row['titre'])
                    ? self::PUBLIC_SITE_URL . 'reference/' . self::slugify($row['titre']) . '/' . $row['id'] . '/'
                    : self::PUBLIC_SITE_URL,
            );
        }
        return $items;
    }

    public static function siteConfig()
    {
        $data = self::call('apiConfig');
        return is_array($data) ? $data : array();
    }

    public static function findDigitalExpertVideos($langue = 'fr')
    {
        $data = self::call('apiDigitalExpertVideos', array('langue' => $langue));
        if (!is_array($data)) {
            return array();
        }
        $items = array();
        foreach ($data as $row) {
            $items[] = array(
                "id" => $row['id'],
                "titre" => $row['titre'],
                "extrait" => $row['extrait'],
                "localisation" => isset($row['localisation']) ? $row['localisation'] : '',
                "date_shooting" => isset($row['date_shooting']) ? $row['date_shooting'] : null,
                "photo" => $row['photo'],
                "youtube_id" => isset($row['youtube_id']) ? $row['youtube_id'] : '',
                "youtube_url" => isset($row['youtube_url']) ? $row['youtube_url'] : '',
            );
        }
        return $items;
    }

    public static function findTestimonials($langue = 'fr')
    {
        $data = self::call('apiTestimonials', array('langue' => $langue));
        if (!is_array($data)) {
            return array();
        }
        $items = array();
        foreach ($data as $row) {
            $items[] = array(
                "author" => $row['author'],
                "fonction" => $row['fonction'],
                "testimonial" => $row['testimonial'],
                "photo" => !empty($row['photo']) ? $row['photo'] : null,
                "active" => true,
            );
        }
        return $items;
    }
}
