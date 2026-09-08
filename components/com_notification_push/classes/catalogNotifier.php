<?php

// Détecte les nouvelles formations/nouveaux services publiés sur le site public
// (helloworld-agency.com, hors de ce dépôt) pour notifier les clients qui ont activé les
// notifications. Pas de déclencheur temps réel possible ici : le catalogue vit entièrement sur le
// site distant (siteCatalog::findFormations()/findServices(), pont HTTP), donc on compare
// périodiquement (voir com_notification_push/controleurs/router.php, appelé une fois par jour par
// un service externe planifié) la liste actuelle à crm_catalog_notified pour ne notifier que les
// id jamais vus.
class catalogNotifier
{
    public static function runDiff()
    {
        global $db;
        $result = array('formations' => 0, 'services' => 0);

        $result['formations'] = self::diffAndNotify('formation', siteCatalog::findFormations('fr'), 'Une nouveauté vient d\'arriver 🌟', ' — venez apprendre avec nous !');
        $result['services'] = self::diffAndNotify('service', siteCatalog::findServices('fr'), 'On a pensé à vous ✨', ' est maintenant disponible, curieux de découvrir ?');

        return $result;
    }

    private static function diffAndNotify($type, $items, $title, $messageSuffix = '')
    {
        global $db;
        if (empty($items)) {
            return 0;
        }

        $sent = 0;
        foreach ($items as $item) {
            $externalId = (string) $item['id'];
            $already = $db->queryS(sprintf(
                "SELECT id FROM crm_catalog_notified WHERE type = '%s' AND external_id = '%s' LIMIT 1",
                $db->getLink()->real_escape_string($type),
                $db->getLink()->real_escape_string($externalId)
            ));
            if (is_array($already) && count($already) > 0) {
                continue; // déjà notifié lors d'un run précédent
            }

            $clientIds = self::optedInClientIds();
            pushNotifier::broadcast($clientIds, $title, $item['titre'] . $messageSuffix, array('type' => $type, 'id' => $item['id']));

            $db->query(sprintf(
                "INSERT INTO crm_catalog_notified (type, external_id, date_notified) VALUES ('%s', '%s', '%s')",
                $db->getLink()->real_escape_string($type),
                $db->getLink()->real_escape_string($externalId),
                date('Y-m-d H:i:s')
            ));
            $sent++;
        }
        return $sent;
    }

    private static function optedInClientIds()
    {
        global $db;
        $rows = $db->queryS("SELECT id FROM crm_client WHERE active = 1 AND notifications_enabled = 1");
        if (!is_array($rows)) {
            return array();
        }
        return array_map(function ($row) {
            return (int) $row['id'];
        }, $rows);
    }
}
