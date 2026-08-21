<?php

// Espace Fidélité (admin CRM) : gère les points de fidélité des clients.
// Les données vivent dans la base du SITE (table hw_points_client), pas dans
// le CRM — le CRM n'est qu'une couche d'administration par-dessus une source
// de vérité unique, pour ne pas dupliquer/désynchroniser les points déjà
// attribués automatiquement côté site (avis, parrainage, attestations,
// téléchargements, réseaux sociaux, connexion régulière).
class fidelite
{
    private static $siteDb = null;
    private static $sitePrefix = null;

    // Chemin vers le config.php du SITE, dérivé RELATIVEMENT à ce fichier
    // plutôt qu'en dur : ce code tourne aussi bien en local (XAMPP) qu'en
    // production tant que les deux applications restent des dossiers frères
    // (même hypothèse déjà faite ailleurs dans ce repo — $apiURL, côté site,
    // pointe vers "../hw_crm/components/" sur ce même principe). 4 niveaux
    // au-dessus de ce fichier (classes/ → com_fidelite/ → components/ →
    // hw_crm/) est la racine commune aux deux applications.
    private static function siteConfigPath()
    {
        return dirname(__DIR__, 4) . '/helloworld/hw-admin/config.php';
    }

    // Se connecte à la base du SITE en lisant SES identifiants directement
    // dans son propre config.php (jamais dupliqués ici) — portée isolée dans
    // une closure pour ne pas laisser fuir $siteURL/$apiURL/etc. définis
    // par ce fichier.
    private static function siteCreds()
    {
        return call_user_func(function () {
            include self::siteConfigPath();
            return array(
                'host' => $host,
                'login' => $login,
                'password' => $password,
                'db' => $dataBaseName,
                'prefix' => $prefixe_db,
            );
        });
    }

    private static function siteDb()
    {
        if (self::$siteDb === null) {
            $creds = self::siteCreds();
            self::$siteDb = dbfactory::factory("mysql", $creds['host'], $creds['login'], $creds['password'], $creds['db']);
            self::$sitePrefix = $creds['prefix'];
        }
        return self::$siteDb;
    }

    private static function sitePrefix()
    {
        self::siteDb();
        return self::$sitePrefix;
    }

    // Liste des clients (CRM) avec leur total de points (site), triés par
    // total décroissant. $agence filtre côté CRM comme le reste du module client.
    public static function findAllClientsWithTotals($agence)
    {
        $clients = client::findAll(false, false, $agence);
        $db = self::siteDb();
        $totals = array();
        $rows = $db->queryS("SELECT id_client, SUM(points) AS total FROM " . self::sitePrefix() . "points_client GROUP BY id_client");
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $totals[(int) $row['id_client']] = (int) $row['total'];
            }
        }
        $items = array();
        foreach ($clients as $c) {
            $items[] = array(
                'client' => $c,
                'total' => isset($totals[$c->getId()]) ? $totals[$c->getId()] : 0,
            );
        }
        usort($items, function ($a, $b) { return $b['total'] <=> $a['total']; });
        return $items;
    }

    // Filleuls recommandés par ce client (site, hw_parrainage), plus récent d'abord.
    public static function findParrainagesByClient($idClient)
    {
        $db = self::siteDb();
        $rows = $db->queryS(sprintf(
            "SELECT filleul_nom, filleul_entreprise, filleul_email, statut, recompense, recompense_donnee, date_add FROM " . self::sitePrefix() . "parrainage WHERE id_parrain = %d ORDER BY date_add DESC",
            (int) $idClient
        ));
        return is_array($rows) ? $rows : array();
    }

    // Historique complet des points d'un client (site), plus récent d'abord.
    public static function findHistoryByClient($idClient)
    {
        $db = self::siteDb();
        $rows = $db->queryS(sprintf(
            "SELECT id, points, type, libelle, date_add FROM " . self::sitePrefix() . "points_client WHERE id_client = %d ORDER BY date_add DESC",
            (int) $idClient
        ));
        return is_array($rows) ? $rows : array();
    }

    // Total actuel d'un client.
    public static function getTotalByClient($idClient)
    {
        $db = self::siteDb();
        $rows = $db->queryS(sprintf(
            "SELECT SUM(points) AS total FROM " . self::sitePrefix() . "points_client WHERE id_client = %d",
            (int) $idClient
        ));
        return (is_array($rows) && count($rows) > 0 && $rows[0]['total'] !== null) ? (int) $rows[0]['total'] : 0;
    }

    // Ajustement manuel depuis le CRM : $points peut être négatif (retrait).
    // type='manuel_crm' pour distinguer clairement des attributions automatiques
    // du site dans l'historique.
    public static function addManualPoints($idClient, $points, $libelle)
    {
        $idClient = (int) $idClient;
        $points = (int) $points;
        if ($idClient <= 0 || $points === 0) {
            return false;
        }
        $db = self::siteDb();
        $libelle = trim((string) $libelle) !== '' ? trim((string) $libelle) : ($points > 0 ? 'Ajustement manuel (crédit)' : 'Ajustement manuel (retrait)');
        $now = date("Y-m-d H:i:s");
        // NB: mysql::query() (com_config/classes/mysql.php) ne fait aucun `return` —
        // son retour est TOUJOURS null, y compris en cas de succès. Le seul moyen
        // fiable de détecter une vraie erreur ici est d'inspecter ->error sur le
        // lien mysqli juste après l'appel.
        $db->query(sprintf(
            "INSERT INTO " . self::sitePrefix() . "points_client (id_client, points, type, libelle, date_add) VALUES (%d, %d, 'manuel_crm', '%s', '%s')",
            $idClient, $points, $db->getLink()->real_escape_string($libelle), $now
        ));
        $ok = empty($db->getLink()->error);
        if ($ok) {
            self::checkRewardThresholds($idClient);
        }
        return $ok ? true : false;
    }

    // Paliers de récompenses (seuils/libellés) : voir le commentaire côté
    // site (clRewardThresholds() dans components/com_client/controleurs/
    // client/controleur.php) — les DEUX listes doivent rester identiques.
    const REWARD_THRESHOLDS = array(
        10  => 'Audit SEO offert',
        20  => 'Formation offerte',
        50  => 'Crédits publicitaires Google Ads',
        100 => 'Remise de 10% sur votre prochaine facture',
    );

    // Débloque (INSERT IGNORE, contrainte UNIQUE(id_client, seuil)) toute
    // récompense nouvellement atteinte après un ajustement manuel — même
    // logique que côté site, pour qu'un crédit fait depuis le CRM déclenche
    // aussi les paliers.
    public static function checkRewardThresholds($idClient)
    {
        $idClient = (int) $idClient;
        if ($idClient <= 0) return;
        $total = self::getTotalByClient($idClient);
        $db = self::siteDb();
        $now = date("Y-m-d H:i:s");
        foreach (self::REWARD_THRESHOLDS as $seuil => $libelle) {
            if ($total < $seuil) continue;
            $db->query(sprintf(
                "INSERT IGNORE INTO " . self::sitePrefix() . "client_rewards (id_client, seuil, libelle, date_debloque) VALUES (%d, %d, '%s', '%s')",
                $idClient, $seuil, $db->getLink()->real_escape_string($libelle), $now
            ));
        }
    }

    // Récompenses débloquées pour un client (les plus récentes d'abord).
    public static function findRewardsByClient($idClient)
    {
        $db = self::siteDb();
        $rows = $db->queryS(sprintf(
            "SELECT id, seuil, libelle, statut, date_debloque, date_affecte, affecte_par FROM " . self::sitePrefix() . "client_rewards WHERE id_client = %d ORDER BY seuil DESC",
            (int) $idClient
        ));
        return is_array($rows) ? $rows : array();
    }

    // Marque une récompense comme affectée (donnée) par l'agence : déduit le
    // seuil du solde de points du client (le client "dépense" les points
    // qu'il a utilisés pour ce cadeau — le déblocage lui-même reste acquis
    // pour toujours, seul le solde diminue), puis marque statut=1 et
    // notifie_don=0 pour que le client voie une notification + un popup
    // "contactez l'agence" à sa prochaine visite.
    public static function markRewardGiven($rewardId, $affectePar)
    {
        $rewardId = (int) $rewardId;
        if ($rewardId <= 0) return false;
        $db = self::siteDb();

        $rows = $db->queryS(sprintf("SELECT * FROM " . self::sitePrefix() . "client_rewards WHERE id = %d LIMIT 1", $rewardId));
        if (!is_array($rows) || count($rows) === 0) return false;
        $reward = $rows[0];
        if ((int) $reward['statut'] === 1) return true; // déjà donnée, rien à refaire

        $now = date("Y-m-d H:i:s");
        $db->query(sprintf(
            "INSERT INTO " . self::sitePrefix() . "points_client (id_client, points, type, libelle, date_add) VALUES (%d, %d, 'recompense_donnee', '%s', '%s')",
            (int) $reward['id_client'], -1 * (int) $reward['seuil'],
            $db->getLink()->real_escape_string('Cadeau remis : ' . $reward['libelle']), $now
        ));
        if (!empty($db->getLink()->error)) return false;

        $db->query(sprintf(
            "UPDATE " . self::sitePrefix() . "client_rewards SET statut = 1, notifie_don = 0, date_affecte = '%s', affecte_par = '%s' WHERE id = %d",
            $now, $db->getLink()->real_escape_string((string) $affectePar), $rewardId
        ));
        return empty($db->getLink()->error);
    }

    // Supprime une ligne de l'historique des points (correction d'erreur,
    // point mal attribué...). N'affecte pas les récompenses déjà débloquées
    // (le déblocage reste acquis même si un point isolé est corrigé après coup).
    public static function deletePointEntry($pointId)
    {
        $pointId = (int) $pointId;
        if ($pointId <= 0) return false;
        $db = self::siteDb();
        $db->query(sprintf("DELETE FROM " . self::sitePrefix() . "points_client WHERE id = %d", $pointId));
        return empty($db->getLink()->error);
    }
}
