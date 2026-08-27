<?php

// Catalogue réel du site public (services + formations), lu directement dans
// la base du SITE via la connexion partagée de fidelite::siteDb() - PAS le
// contenu de crm_expertise (entité CRM historique, 6 lignes non maintenues,
// sans rapport avec les vraies pages services du site). Reproduit exactement
// les filtres utilisés par l'espace client web pour l'onglet Découvrir
// (voir helloworld/components/com_client/views/client/facture.php) : services
// "vitrine" (actifs, racine, home=1) et formations à venir, pour que le
// mobile affiche la même chose que le web plutôt qu'une liste parallèle.
class siteCatalog
{
    // Base pour les images (servies localement en dev) - même hypothèse de
    // dossiers frères que fidelite::siteConfigPath().
    private static function siteBaseUrl()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        return "http://" . $host . "/helloworld/";
    }

    // Domaine public réel, pour les liens "Découvrir" qui doivent ouvrir la
    // vraie page du site (pas l'URL locale de dev, jamais joignable
    // depuis le téléphone d'un client).
    const PUBLIC_SITE_URL = "https://www.helloworld-agency.com/";

    public static function findServices($langue = 'fr')
    {
        $db = \fidelite::siteDb();
        $prefix = \fidelite::sitePrefix();
        $sql = sprintf(
            "SELECT A.id AS ID, A.photo, B.titre, B.extrait, B.slug FROM %sservice A " .
            "LEFT JOIN %sdetails_service B ON A.id = B.id_service AND B.langue = %s " .
            "WHERE A.active = 1 AND (A.id_parent = 0 OR A.id_parent IS NULL) AND A.home = 1 " .
            "ORDER BY A.ordre ASC",
            $prefix,
            $prefix,
            GetSQLValueString($langue, "text")
        );
        $rows = $db->queryS($sql);
        if (!is_array($rows)) {
            return array();
        }
        $base = self::siteBaseUrl();
        $items = array();
        foreach ($rows as $row) {
            $items[] = array(
                "id" => (int) $row['ID'],
                "titre" => self::cleanText($row['titre']),
                "extrait" => self::cleanText($row['extrait']),
                "photo" => !empty($row['photo']) ? $base . 'images/services/' . $row['photo'] : "",
                "lien" => !empty($row['slug'])
                    ? self::PUBLIC_SITE_URL . 'service/' . $row['slug'] . '/'
                    : self::PUBLIC_SITE_URL,
            );
        }
        return $items;
    }

    // Le CMS du site stocke titre/extrait avec des entités HTML (CKEditor) -
    // on les décode et on retire les balises ici, une fois pour toutes,
    // plutôt que de faire porter ça à chaque écran mobile qui consomme ce
    // catalogue.
    // Retourne toujours une chaîne (jamais null) : certaines fiches ont un
    // champ NULL en base (ex: reference #31 "CALLIOPE" sans extrait) - les
    // modèles mobile déclarent ces champs non-nullables, un null ferait
    // planter tout le parsing de la liste pour un seul élément incomplet.
    private static function cleanText($value)
    {
        if (empty($value)) {
            return "";
        }
        return trim(html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8'));
    }

    public static function findFormations($langue = 'fr', $limit = 6)
    {
        $db = \fidelite::siteDb();
        $prefix = \fidelite::sitePrefix();
        $sql = sprintf(
            "SELECT A.id AS ID, A.photo, A.date_debut, A.date_fin, A.lieu, B.titre, B.extrait, B.slug FROM %sformation A " .
            "LEFT JOIN %sdetails_formation B ON A.id = B.id_formation AND B.langue = %s " .
            "WHERE A.active = 1 ORDER BY A.date_debut ASC",
            $prefix,
            $prefix,
            GetSQLValueString($langue, "text")
        );
        $rows = $db->queryS($sql);
        if (!is_array($rows)) {
            return array();
        }
        $base = self::siteBaseUrl();
        $today = date('Y-m-d');
        $items = array();
        foreach ($rows as $row) {
            $end = !empty($row['date_fin']) ? $row['date_fin'] : $row['date_debut'];
            if (!empty($end) && substr($end, 0, 10) < $today) {
                continue;
            }
            $items[] = array(
                "id" => (int) $row['ID'],
                "titre" => self::cleanText($row['titre']),
                "extrait" => self::cleanText($row['extrait']),
                "photo" => !empty($row['photo']) ? $base . 'images/formations/' . $row['photo'] : "",
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

    // Réalisations / cas clients réels du site (hw_reference), pas
    // crm_realisation (2 lignes CRM sans rapport) - reproduit exactement le
    // filtre de la page publique https://www.helloworld-agency.com/r-alisations-et-cas-clients/
    // (components/com_reference/index.php : active=1, sans limite, triées
    // par id décroissant) pour que le mobile affiche la même chose que le
    // site plutôt qu'un sous-ensemble différent.
    public static function findReferences($langue = 'fr')
    {
        $db = \fidelite::siteDb();
        $prefix = \fidelite::sitePrefix();
        $sql = sprintf(
            "SELECT A.id AS ID, A.photo, B.nom_client, B.extrait, B.site_web FROM %sreference A " .
            "LEFT JOIN %sdetails_reference B ON A.id = B.id_reference AND B.langue = %s " .
            "WHERE A.active = 1 ORDER BY A.id DESC",
            $prefix,
            $prefix,
            GetSQLValueString($langue, "text")
        );
        $rows = $db->queryS($sql);
        if (!is_array($rows)) {
            return array();
        }
        $base = self::siteBaseUrl();
        $items = array();
        foreach ($rows as $row) {
            $nomClient = self::cleanText($row['nom_client']);
            $items[] = array(
                "id" => (int) $row['ID'],
                "titre" => $nomClient,
                "extrait" => self::cleanText($row['extrait']),
                "photo" => !empty($row['photo']) ? $base . 'images/references/' . $row['photo'] : "",
                "lien" => !empty($nomClient)
                    ? self::PUBLIC_SITE_URL . 'reference/' . self::slugify($nomClient) . '/' . $row['ID'] . '/'
                    : self::PUBLIC_SITE_URL,
            );
        }
        return $items;
    }

    // Équivalent simplifié de url_rewriting() (helloworld/includes/functions)
    // pour construire le même lien que reference::getLink() sans dépendre de
    // cette fonction (définie côté site, pas chargée dans le contexte API CRM).
    private static function slugify($value)
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim($value, '-');
    }

    // Vidéos "Digital Expert" réelles du site (hw_video, catégorie id=14,
    // même filtre que components/com_frontpage/index.php sur la home du
    // site : video::findAllByCategorie($lang, 14, true, false)). Les vidéos
    // sont des ID YouTube (hw_video.video), ouvertes en lightbox côté site -
    // on reconstruit la même URL YouTube pour que le mobile les ouvre dans
    // le navigateur/l'app YouTube plutôt que d'embarquer un lecteur.
    public static function findDigitalExpertVideos($langue = 'fr')
    {
        $db = \fidelite::siteDb();
        $prefix = \fidelite::sitePrefix();
        $sql = sprintf(
            "SELECT A.id AS ID, A.video, A.photo, A.localisation, A.date_shooting, B.titre, B.extrait " .
            "FROM %svideo A LEFT JOIN %sdetails_video B ON A.id = B.id_video AND B.langue = %s " .
            "WHERE A.id_categorie = 14 AND A.active = 1 ORDER BY A.ordre DESC",
            $prefix,
            $prefix,
            GetSQLValueString($langue, "text")
        );
        $rows = $db->queryS($sql);
        if (!is_array($rows)) {
            return array();
        }
        $base = self::siteBaseUrl();
        $items = array();
        foreach ($rows as $row) {
            if (empty($row['video'])) {
                continue;
            }
            $items[] = array(
                "id" => (int) $row['ID'],
                "titre" => self::cleanText($row['titre']),
                "extrait" => self::cleanText($row['extrait']),
                "localisation" => self::cleanText($row['localisation']),
                "date_shooting" => $row['date_shooting'],
                "photo" => !empty($row['photo']) ? $base . 'images/videos/' . $row['photo'] : "",
                "youtube_id" => $row['video'],
                "youtube_url" => "https://www.youtube.com/watch?v=" . $row['video'],
            );
        }
        return $items;
    }

    // Coordonnées et réseaux sociaux réels du site (hw_config) - le
    // crm_config du CRM contient des valeurs obsolètes (ex: un email
    // "verse-concept.com" hérité d'un tout autre client, jamais mis à jour).
    public static function siteConfig()
    {
        $db = \fidelite::siteDb();
        $prefix = \fidelite::sitePrefix();
        $rows = $db->queryS("SELECT email, tel, tel2, facebook, twitter, instagram, youtube, linkedin FROM " . $prefix . "config LIMIT 1");
        if (!is_array($rows) || count($rows) === 0) {
            return array();
        }
        return $rows[0];
    }

    // Vrais avis clients du site (hw_temoignage, 28 actifs) - pas
    // crm_temoignage (2 lignes seulement, jamais rempli côté CRM).
    public static function findTestimonials($langue = 'fr')
    {
        $db = \fidelite::siteDb();
        $prefix = \fidelite::sitePrefix();
        $sql = sprintf(
            "SELECT A.id AS ID, A.photo, B.nom, B.fonction, B.temoignage FROM %stemoignage A " .
            "LEFT JOIN %sdetails_temoignage B ON A.id = B.id_temoignage AND B.langue = %s " .
            "WHERE A.active = 1 ORDER BY A.ordre ASC",
            $prefix,
            $prefix,
            GetSQLValueString($langue, "text")
        );
        $rows = $db->queryS($sql);
        if (!is_array($rows)) {
            return array();
        }
        $base = self::siteBaseUrl();
        $items = array();
        foreach ($rows as $row) {
            $auteur = self::cleanText($row['nom']);
            $texte = self::cleanText($row['temoignage']);
            if (empty($auteur) || empty($texte)) {
                continue;
            }
            $items[] = array(
                "author" => $auteur,
                "fonction" => self::cleanText($row['fonction']),
                "testimonial" => $texte,
                "photo" => !empty($row['photo']) ? $base . 'images/temoignages/' . $row['photo'] : null,
                "active" => true,
            );
        }
        return $items;
    }
}
