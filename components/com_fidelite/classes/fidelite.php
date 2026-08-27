<?php

// Espace Fidélité (admin CRM) : gère les points de fidélité des clients.
// Les données vivent dans le CRM (crm_points_client / crm_client_rewards) -
// c'est le CRM la source de vérité, pas le site : l'espace client (site)
// consulte/écrit ces points via l'API exposée par com_fidelite/controleurs/router.php
// (task=apiXxx, gardée par FIDELITE_API_SECRET), jamais une connexion DB directe
// entre les deux applications.
class fidelite
{
    // Liste des clients (CRM) avec leur total de points, triés par total
    // décroissant. $agence filtre côté CRM comme le reste du module client.
    public static function findAllClientsWithTotals($agence)
    {
        global $db;
        $clients = client::findAll(false, false, $agence);
        $totals = array();
        $rows = $db->queryS("SELECT id_client, SUM(points) AS total FROM crm_points_client GROUP BY id_client");
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

    // Filleuls recommandés par ce client, plus récent d'abord.
    public static function findParrainagesByClient($idClient)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT filleul_nom, filleul_entreprise, filleul_email, statut, recompense, recompense_donnee, date_add FROM crm_client_parrainage WHERE id_parrain = %d ORDER BY date_add DESC",
            (int) $idClient
        ));
        return is_array($rows) ? $rows : array();
    }

    // Historique complet des points d'un client, plus récent d'abord.
    public static function findHistoryByClient($idClient)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT id, points, type, libelle, date_add FROM crm_points_client WHERE id_client = %d ORDER BY date_add DESC",
            (int) $idClient
        ));
        return is_array($rows) ? $rows : array();
    }

    // Total actuel d'un client.
    public static function getTotalByClient($idClient)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT SUM(points) AS total FROM crm_points_client WHERE id_client = %d",
            (int) $idClient
        ));
        return (is_array($rows) && count($rows) > 0 && $rows[0]['total'] !== null) ? (int) $rows[0]['total'] : 0;
    }

    // Ajoute une ligne de points ($points peut être négatif). $type distingue
    // la source : manuel_crm (ajustement admin), ou un type transmis par le
    // site via l'API (avis, parrainage, attestation, attestation_telechargement,
    // connexion_reguliere...).
    public static function addPoints($idClient, $points, $type, $libelle)
    {
        global $db;
        $idClient = (int) $idClient;
        $points = (int) $points;
        if ($idClient <= 0 || $points === 0) {
            return false;
        }
        $type = trim((string) $type) !== '' ? trim((string) $type) : 'manuel_crm';
        $libelle = trim((string) $libelle) !== '' ? trim((string) $libelle) : ($points > 0 ? 'Ajustement manuel (crédit)' : 'Ajustement manuel (retrait)');
        $now = date("Y-m-d H:i:s");
        // NB: mysql::query() (com_config/classes/mysql.php) ne fait aucun `return` —
        // son retour est TOUJOURS null, y compris en cas de succès. Le seul moyen
        // fiable de détecter une vraie erreur ici est d'inspecter ->error sur le
        // lien mysqli juste après l'appel.
        $db->query(sprintf(
            "INSERT INTO crm_points_client (id_client, points, type, libelle, date_add) VALUES (%d, %d, '%s', '%s', '%s')",
            $idClient, $points, $db->getLink()->real_escape_string($type), $db->getLink()->real_escape_string($libelle), $now
        ));
        $ok = empty($db->getLink()->error);
        if ($ok) {
            self::checkRewardThresholds($idClient);
        }
        return $ok ? true : false;
    }

    // Ajustement manuel depuis le CRM : $points peut être négatif (retrait).
    // type='manuel_crm' pour distinguer clairement des attributions automatiques
    // du site dans l'historique.
    public static function addManualPoints($idClient, $points, $libelle)
    {
        return self::addPoints($idClient, $points, 'manuel_crm', $libelle);
    }

    // Paliers de récompenses (seuils/libellés) : seule copie qui existe
    // désormais (avant, une copie identique devait être maintenue côté site -
    // plus la peine, le site consulte l'API plutôt que de dupliquer la règle).
    const REWARD_THRESHOLDS = array(
        10  => 'Audit SEO offert',
        20  => 'Formation offerte',
        50  => 'Crédits publicitaires Google Ads',
        100 => 'Remise de 10% sur votre prochaine facture',
    );

    // Débloque (INSERT IGNORE, contrainte UNIQUE(id_client, seuil)) toute
    // récompense nouvellement atteinte après un ajustement de points.
    public static function checkRewardThresholds($idClient)
    {
        global $db;
        $idClient = (int) $idClient;
        if ($idClient <= 0) return;
        $total = self::getTotalByClient($idClient);
        $now = date("Y-m-d H:i:s");
        foreach (self::REWARD_THRESHOLDS as $seuil => $libelle) {
            if ($total < $seuil) continue;
            $db->query(sprintf(
                "INSERT IGNORE INTO crm_client_rewards (id_client, seuil, libelle, date_debloque) VALUES (%d, %d, '%s', '%s')",
                $idClient, $seuil, $db->getLink()->real_escape_string($libelle), $now
            ));
        }
    }

    // Récompenses débloquées pour un client (les plus récentes d'abord).
    public static function findRewardsByClient($idClient)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT id, seuil, libelle, statut, date_debloque, date_affecte, affecte_par FROM crm_client_rewards WHERE id_client = %d ORDER BY seuil DESC",
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
        global $db;
        $rewardId = (int) $rewardId;
        if ($rewardId <= 0) return false;

        $rows = $db->queryS(sprintf("SELECT * FROM crm_client_rewards WHERE id = %d LIMIT 1", $rewardId));
        if (!is_array($rows) || count($rows) === 0) return false;
        $reward = $rows[0];
        if ((int) $reward['statut'] === 1) return true; // déjà donnée, rien à refaire

        $now = date("Y-m-d H:i:s");
        $db->query(sprintf(
            "INSERT INTO crm_points_client (id_client, points, type, libelle, date_add) VALUES (%d, %d, 'recompense_donnee', '%s', '%s')",
            (int) $reward['id_client'], -1 * (int) $reward['seuil'],
            $db->getLink()->real_escape_string('Cadeau remis : ' . $reward['libelle']), $now
        ));
        if (!empty($db->getLink()->error)) return false;

        $db->query(sprintf(
            "UPDATE crm_client_rewards SET statut = 1, notifie_don = 0, date_affecte = '%s', affecte_par = '%s' WHERE id = %d",
            $now, $db->getLink()->real_escape_string((string) $affectePar), $rewardId
        ));
        return empty($db->getLink()->error);
    }

    // Supprime une ligne de l'historique des points (correction d'erreur,
    // point mal attribué...). N'affecte pas les récompenses déjà débloquées
    // (le déblocage reste acquis même si un point isolé est corrigé après coup).
    public static function deletePointEntry($pointId)
    {
        global $db;
        $pointId = (int) $pointId;
        if ($pointId <= 0) return false;
        $db->query(sprintf("DELETE FROM crm_points_client WHERE id = %d", $pointId));
        return empty($db->getLink()->error);
    }

    // --- Ajouté pour l'API REST mobile (hw_crm/api) ---
    // Mêmes valeurs/types que clAwardPoints() côté site (components/com_client/
    // controleurs/client/controleur.php) : CL_POINTS_AVIS=10, PARRAINAGE=15,
    // ATTESTATION=20, SOCIAL=3 - dupliquées ici en constantes plutôt qu'incluses
    // depuis le site, pour ne pas exécuter tout le bootstrap web (session,
    // routing) depuis un contexte API stateless.
    const POINTS_AVIS = 10;
    const POINTS_PARRAINAGE = 15;
    const POINTS_ATTESTATION = 20;
    const POINTS_SOCIAL = 3;

    public static function hasPointsOfType($idClient, $type)
    {
        global $db;
        $rows = $db->queryS(sprintf(
            "SELECT id FROM crm_points_client WHERE id_client = %d AND type = '%s' LIMIT 1",
            (int) $idClient, $db->getLink()->real_escape_string($type)
        ));
        return is_array($rows) && count($rows) > 0;
    }

    // Crée une recommandation (parrainage) - mêmes règles que createParrainageApi()
    // côté site (components/com_client/controleurs/client/controleur.php) : anti
    // auto-parrainage, anti-doublon (même parrain+email filleul), puis +15 points.
    // Écrit dans crm_client_parrainage (même table que findParrainagesByClient() lit) :
    // le CRM reste la seule source de vérité, comme pour les points. L'email
    // d'invitation au filleul (best-effort côté site) n'est PAS répliqué ici -
    // le mobile se contente de tracer la recommandation pour l'agence.
    public static function createParrainage($parrainClient, $filleulNom, $filleulEntreprise, $filleulEmail, $filleulTel, $message)
    {
        global $db;
        $filleulEmail = trim((string) $filleulEmail);
        $filleulNom = trim((string) $filleulNom);
        if ($filleulNom === '' || $filleulEmail === '') {
            return array('success' => false, 'code' => 'missing', 'message' => 'Nom et email du filleul requis.');
        }
        $parrainEmail = $parrainClient->getEmail();
        if ($parrainEmail !== null && strcasecmp($parrainEmail, $filleulEmail) === 0) {
            return array('success' => false, 'code' => 'self', 'message' => 'Vous ne pouvez pas vous parrainer vous-même.');
        }

        $idParrain = (int) $parrainClient->getId();

        $dup = $db->queryS(sprintf(
            "SELECT id FROM crm_client_parrainage WHERE id_parrain = %d AND filleul_email = '%s' LIMIT 1",
            $idParrain, $db->getLink()->real_escape_string($filleulEmail)
        ));
        if (is_array($dup) && count($dup) > 0) {
            return array('success' => false, 'code' => 'dup', 'message' => 'Vous avez déjà recommandé cette personne.');
        }

        $parrainNom = trim($parrainClient->getRaisonSocial()) !== ''
            ? $parrainClient->getRaisonSocial()
            : trim($parrainClient->getPrenom() . ' ' . $parrainClient->getNom());

        $now = date('Y-m-d H:i:s');
        $db->query(sprintf(
            "INSERT INTO crm_client_parrainage (id_parrain, parrain_nom, parrain_email, filleul_nom, filleul_entreprise, filleul_email, filleul_tel, message, statut, recompense_donnee, date_add) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', 0, 0, '%s')",
            $idParrain,
            $db->getLink()->real_escape_string($parrainNom),
            $db->getLink()->real_escape_string((string) $parrainEmail),
            $db->getLink()->real_escape_string($filleulNom),
            $db->getLink()->real_escape_string((string) $filleulEntreprise),
            $db->getLink()->real_escape_string($filleulEmail),
            $db->getLink()->real_escape_string((string) $filleulTel),
            $db->getLink()->real_escape_string((string) $message),
            $now
        ));
        if (!empty($db->getLink()->error)) {
            return array('success' => false, 'code' => 'error', 'message' => 'Erreur lors de l\'enregistrement.');
        }

        self::awardPoints($idParrain, self::POINTS_PARRAINAGE, 'parrainage', 'Recommandation de ' . $filleulNom);

        return array('success' => true, 'code' => 'ok', 'message' => 'Merci ! Nous contactons votre filleul rapidement.');
    }

    // Alias sémantique d'addPoints() pour les appelants API mobile (avis, parrainage,
    // social_follow) - même insertion crm_points_client, désormais la même connexion
    // partout dans ce fichier (avant : siteDb(), table du site, jamais lue par le CRM).
    public static function awardPoints($idClient, $points, $type, $libelle)
    {
        return self::addPoints($idClient, $points, $type, $libelle);
    }
}
