<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'pointerWeb':
            pointerWeb($_POST);
            break;
        case 'filterPointageWeb':
            filterPointageWeb($_POST);
            break;
        case 'cronPointageRappel':
            cronPointageRappelEndpoint();
            break;
        case 'filterJoursTravail':
            filterJoursTravail($_POST);
            break;
        case 'toggleJourTravail':
            toggleJourTravail($_POST);
            break;
        case 'filterHorairesTravail':
            filterHorairesTravail($_POST);
            break;
        case 'saveHoraireReference':
            saveHoraireReference($_POST);
            break;
        case 'saveHoraireJour':
            saveHoraireJour($_POST);
            break;
        case 'resetHoraireJour':
            resetHoraireJour($_POST);
            break;
        case 'saveLocalisationBureau':
            saveLocalisationBureau($_POST);
            break;
        case 'justifyRetard':
            justifyRetard($_POST);
            break;
    }
}

// Poste dans le canal family via le webhook déjà utilisé pour ce canal (même motif que
// postSlackLaunch() dans components/com_facture/classes/projectNotifier.php) - fire-and-forget,
// pas de token bot nécessaire pour ce canal.
function pointageSlackPostMessageFamily($text)
{
    if (!defined('SLACK_WEBHOOK_URL_FAMILY') || SLACK_WEBHOOK_URL_FAMILY == '') {
        return false;
    }
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
    return true;
}

// Pointage self-service (espace employé) - jamais de hasDroit ici (voir router.php, gated
// isResourceHumaine() uniquement). La présence au bureau est REVÉRIFIÉE ici côté serveur (IP OU
// position GPS/Wi-Fi à portée du bureau) : masquer les boutons côté client (voir la vue) est un
// confort d'UX, pas une barrière - sans ce contrôle serveur, n'importe qui pourrait pointer
// depuis n'importe où en POSTant directement sur cette tâche. La géolocalisation est le contrôle
// principal (POINTAGE_ALLOWED_IPS exige une IP publique fixe, payante chez la plupart des FAI
// marocains pour une ligne résidentielle) - l'IP reste un second chemin possible si elle est
// configurée un jour.
function pointerWeb($data)
{
    // Pas de header Content-Type: application/json ici volontairement - jQuery $.post()
    // parserait alors automatiquement la réponse en objet AVANT qu'elle n'atteigne le success
    // callback, cassant le JSON.parse(theResponse) manuel côté vue (même motif que
    // recalculateBonus() dans bonus/controleur.php, qui répond aussi en JSON texte brut).

    if (!$_SESSION['user']->isResourceHumaine()) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }

    $latitude = (isset($data['latitude']) && $data['latitude'] !== '') ? (float) $data['latitude'] : null;
    $longitude = (isset($data['longitude']) && $data['longitude'] !== '') ? (float) $data['longitude'] : null;
    $positionFournie = $latitude !== null && $longitude !== null;
    $positionOk = $positionFournie && pointagelocalisation::estDansLeRayon($latitude, $longitude);

    if (!$positionOk && !pointageIpAutorisee()) {
        if (!$positionFournie) {
            echo json_encode(array('success' => 0, 'message' => 'Impossible de vérifier votre position. Autorisez l\'accès à la localisation dans votre navigateur puis réessayez.'));
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Vous devez être au bureau pour pointer.'));
        }
        return;
    }

    $etapeDemandee = isset($data['etape']) ? $data['etape'] : '';
    if (!in_array($etapeDemandee, pointageweb::$etapes, true)) {
        echo json_encode(array('success' => 0, 'message' => 'Étape invalide.'));
        return;
    }

    $resourcehumaine = $_SESSION['user'];

    $blocage = pointageweb::jourBloquePourPointage($resourcehumaine, date('Y-m-d'));
    if ($blocage === 'ferie') {
        echo json_encode(array('success' => 0, 'message' => 'Aujourd\'hui est un jour férié — le pointage n\'est pas disponible.'));
        return;
    }
    if ($blocage === 'conge') {
        echo json_encode(array('success' => 0, 'message' => 'Vous êtes en congé aujourd\'hui — le pointage n\'est pas disponible.'));
        return;
    }

    $item = pointageweb::findOrCreateAujourdhui($resourcehumaine);

    $etapeAttendue = $item->getProchaineEtape();
    if ($etapeAttendue === null) {
        echo json_encode(array('success' => 0, 'message' => 'Vous avez déjà terminé vos 4 pointages du jour.'));
        return;
    }
    if ($etapeAttendue !== $etapeDemandee) {
        echo json_encode(array('success' => 0, 'message' => 'Étape hors séquence — prochaine étape attendue : ' . $etapeAttendue . '.'));
        return;
    }

    $maintenant = date('H:i:s');
    $ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    switch ($etapeDemandee) {
        case 'arrivee':
            $item->setHeureArrivee($maintenant);
            $item->setIpArrivee($ip);
            break;
        case 'depart_pause':
            $item->setHeureDepartPause($maintenant);
            $item->setIpDepartPause($ip);
            break;
        case 'retour_pause':
            $item->setHeureRetourPause($maintenant);
            $item->setIpRetourPause($ip);
            break;
        case 'depart':
            $item->setHeureDepart($maintenant);
            $item->setIpDepart($ip);
            break;
    }

    $maintenantDatetime = date('Y-m-d H:i:s');
    if ($item->getId()) {
        $item->setLastEdit($maintenantDatetime);
        $succes = $item->edit() == 1;
    } else {
        $item->setDateAdd($maintenantDatetime);
        $item->setLastEdit($maintenantDatetime);
        $succes = $item->add() == 1;
    }

    if (!$succes) {
        echo json_encode(array('success' => 0, 'message' => 'Erreur lors de l\'enregistrement.'));
        return;
    }

    echo json_encode(array(
        'success' => 1,
        'etape' => $etapeDemandee,
        'heure' => $maintenant,
        'prochaine_etape' => $item->getProchaineEtape(),
        'minutes_travaillees' => $item->getMinutesTravaillees(),
    ));
}

// Tableau mensuel "Pointage web" côté admin (page index.php?option=com_resourcehumaine&
// task=pointage) - même motif AJAX que filterPointage() (pointage/controleur.php, import
// existant) : une ligne par employé, agrégée sur le mois choisi, jamais mélangé à l'import.
// Inclut retard/absence (calculerStatsMois()) + le calendrier jour par jour (calendrierMois()).
// N'affiche que les employés actifs (isActive(), champ "active" - même notion que le filtre
// "Actifs uniquement" de la liste RH, indépendante du statut Titulaire/Stagiaire/Periode de
// test) - chaque employé actif apparaît toujours, même sans aucune activité ce mois-ci, pour que
// la liste et son calendrier reflètent l'effectif réel, pas seulement ceux qui ont déjà pointé.
function filterPointageWeb($data)
{
    if (!$_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
        return;
    }
    $mois = isset($data['month']) && $data['month'] !== '' ? $data['month'] : date('Y-m');
    $resources_humaines = array_values(array_filter(
        resourcehumaine::findAllByStatuses(array("Titulaire", "Periode de test")),
        function ($employe) { return $employe->isActive(); }
    ));

    $lignes = array();
    $totalMinutesGlobal = 0;
    $totalJoursGlobal = 0;
    $totalRetardMinutesGlobal = 0;
    $totalRetardJoursGlobal = 0;
    $totalAbsenceJoursGlobal = 0;
    foreach ($resources_humaines as $employe) {
        $pointages = pointageweb::findAllByResourcehumaineAndMonth($employe->getId(), $mois);
        $minutesEmploye = 0;
        $arriveesMinutes = array();
        foreach ($pointages as $p) {
            $minutesEmploye += $p->getMinutesTravaillees();
            if ($p->getHeureArrivee()) {
                list($h, $m) = explode(':', $p->getHeureArrivee());
                $arriveesMinutes[] = ((int) $h) * 60 + (int) $m;
            }
        }
        $arriveeMoyenneMinutes = !empty($arriveesMinutes) ? array_sum($arriveesMinutes) / count($arriveesMinutes) : null;

        $statsMois = pointageweb::calculerStatsMois($employe, $mois);
        $absenceJoursEmploye = 0;
        foreach (absence::findByDateMonthly($employe->getId(), $mois) as $uneAbsence) {
            if ($uneAbsence->getNatureOfAbsence() == 3) {
                $absenceJoursEmploye += $uneAbsence->getNumberOfDays();
            }
        }

        $lignes[] = array(
            'employe' => $employe,
            'jours' => count($pointages),
            'minutes' => $minutesEmploye,
            'arrivee_moyenne' => $arriveeMoyenneMinutes !== null ? sprintf('%02d:%02d', floor($arriveeMoyenneMinutes / 60), $arriveeMoyenneMinutes % 60) : '—',
            'retard_minutes' => $statsMois['retard_minutes'],
            'retard_jours' => $statsMois['retard_jours'],
            'absence_jours' => $absenceJoursEmploye,
            'calendrier' => pointageweb::calendrierMois($employe, $mois),
        );
        $totalMinutesGlobal += $minutesEmploye;
        $totalJoursGlobal += count($pointages);
        $totalRetardMinutesGlobal += $statsMois['retard_minutes'];
        $totalRetardJoursGlobal += $statsMois['retard_jours'];
        $totalAbsenceJoursGlobal += $absenceJoursEmploye;
    }

    // Chemin relatif au FICHIER (__DIR__), pas à l'ancien "components/..." relatif à la racine :
    // ce contrôleur est atteint via router.php, requêté directement en AJAX (jamais par
    // l'index.php racine), donc le cwd PHP est le dossier du contrôleur, pas la racine du site.
    include(__DIR__ . '/../../views/pointage/pointage_web_table.php');
}

// Endpoint public (cron paresseux, même motif que cronCheckEcheanceTvaEndpoint() dans
// com_accounting/controleurs/router.php) : appelé périodiquement (10-15 min) par un service
// externe planifié (cron-job.org). Un seul endpoint qui décide lui-même s'il doit poster le
// rappel du matin et/ou du soir, avec dédoublonnage "déjà envoyé aujourd'hui" (crm_pointage_rappel).
function cronPointageRappelEndpoint()
{
    header('Content-Type: application/json');

    $fourni = isset($_GET['secret']) ? $_GET['secret'] : (isset($_SERVER['HTTP_X_WEBHOOK_SECRET']) ? $_SERVER['HTTP_X_WEBHOOK_SECRET'] : '');
    if (!defined('POINTAGE_CRON_SECRET') || POINTAGE_CRON_SECRET === '' || !hash_equals(POINTAGE_CRON_SECRET, (string) $fourni)) {
        error_log('cronPointageRappelEndpoint - tentative non autorisée depuis ' . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'inconnue'));
        http_response_code(403);
        echo json_encode(array('error' => 'forbidden'));
        return;
    }

    global $db;
    $maintenant = new DateTime();
    $aujourdhui = $maintenant->format('Y-m-d');
    $heureActuelle = $maintenant->format('H:i');
    $resultats = array('matin' => false, 'soir' => false);

    if (!pointageweb::estJourTravaille($maintenant)) {
        echo json_encode(array('success' => 1, 'jour_travaille' => false, 'resultats' => $resultats));
        return;
    }

    $ligne = $db->queryS("SELECT * FROM " . __prefixe_db__ . "pointage_rappel WHERE id = 1");
    $etat = !empty($ligne) ? $ligne[0] : array('last_morning_sent' => null, 'last_evening_sent' => null);

    $estSamedi = ((int) $maintenant->format('N')) == 6;
    $heureLimiteMatin = $estSamedi ? '09:30' : '09:00';

    if ($heureActuelle >= $heureLimiteMatin && $etat['last_morning_sent'] !== $aujourdhui) {
        $messagesMatin = array(
            ":sunny: Bonjour à toutes et à tous ! Une nouvelle journée pleine d'énergie commence 💪 Un petit coup d'œil à votre planning et n'oubliez pas de pointer votre arrivée sur le CRM 😉",
            ":coffee: Debout les champions ! ☕ C'est parti pour une nouvelle journée — check votre planning et pensez à pointer votre arrivée !",
            ":wave: Hello l'équipe ! La journée commence, direction le planning et un petit clic sur \"Arrivée\" avant de se lancer 🚀",
        );
        $texte = $messagesMatin[array_rand($messagesMatin)];
        if (pointageSlackPostMessageFamily($texte)) {
            $db->query(sprintf("UPDATE " . __prefixe_db__ . "pointage_rappel SET last_morning_sent = %s WHERE id = 1", GetSQLValueString($aujourdhui, "date")));
            $resultats['matin'] = true;
        }
    }

    if ($heureActuelle >= '17:50' && $etat['last_evening_sent'] !== $aujourdhui) {
        $messagesSoir = array(
            ":tada: C'est la fin de la journée, youpiii ! 🎉 Merci pour votre travail aujourd'hui — n'oubliez pas de pointer votre départ avant de filer !",
            ":checkered_flag: Et voilà, une journée de plus dans la boîte ! 🙌 Petit réflexe avant de partir : pointez votre départ sur le CRM. Bonne soirée à tous !",
            ":crescent_moon: 17h50, c'est l'heure ! N'oubliez pas de pointer votre départ avant de rentrer 🏃 Bonne fin de journée à toute l'équipe !",
        );
        $texte = $messagesSoir[array_rand($messagesSoir)];
        if (pointageSlackPostMessageFamily($texte)) {
            $db->query(sprintf("UPDATE " . __prefixe_db__ . "pointage_rappel SET last_evening_sent = %s WHERE id = 1", GetSQLValueString($aujourdhui, "date")));
            $resultats['soir'] = true;
        }
    }

    // Détection auto d'absence (une fois par jour, après 20h, sur la journée d'HIER seulement -
    // jamais aujourd'hui, pour laisser le temps aux éventuelles régularisations/congés de la
    // journée d'être enregistrés avant de juger). Dédoublonné via last_absence_check, même motif
    // que last_morning_sent/last_evening_sent ci-dessus. Fusionné dans cet endpoint déjà pingé
    // périodiquement plutôt que d'ajouter un nouveau secret/case - un seul cron paresseux qui
    // décide lui-même de tout ce qu'il y a à faire aujourd'hui.
    $resultats['absences_creees'] = 0;
    if ($heureActuelle >= '20:00' && $etat['last_absence_check'] !== $aujourdhui) {
        $hier = (new DateTime('yesterday'))->format('Y-m-d');
        $employes = resourcehumaine::findAllByStatuses(array("Titulaire", "Periode de test"));
        foreach ($employes as $employe) {
            $classification = pointageweb::classifierJour($employe, $hier);
            if (!$classification || $classification['type'] !== 'absence') {
                continue;
            }
            $absenceAbsence = new absence();
            $absenceAbsence->setResourcehumaine($employe);
            $absenceAbsence->setNumberOfDays(1);
            $absenceAbsence->setNatureOfAbsence(3); // Non justifié - n'entame jamais le solde de congés payés.
            $absenceAbsence->setStartDate($hier);
            $absenceAbsence->setEndDate($hier);
            $absenceAbsence->setStatus(1); // Déductible - éditable ensuite par l'admin.
            $absenceAbsence->setRemark('Absence détectée automatiquement (' . $classification['motif'] . ')');
            $absenceAbsence->setJustification('');
            $absenceAbsence->setDateAdd(date('Y-m-d H:i:s'));
            $absenceAbsence->setLastEdit(date('Y-m-d H:i:s'));
            if ($absenceAbsence->add() == 1) {
                $resultats['absences_creees']++;
            }
        }
        $db->query(sprintf("UPDATE " . __prefixe_db__ . "pointage_rappel SET last_absence_check = %s WHERE id = 1", GetSQLValueString($aujourdhui, "date")));
    }

    echo json_encode(array('success' => 1, 'jour_travaille' => true, 'resultats' => $resultats));
}

// Calendrier mensuel "Gestion des jours de travail" côté admin (page task=pointage) - un jour par
// case, dit s'il est travaillé (règle automatique, jour férié com_holiday, OU dérogation
// manuelle) et si une dérogation existe. Global à l'entreprise (pas par employé), contrairement
// au reste de cette page.
function filterJoursTravail($data)
{
    if (!$_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
        return;
    }
    $mois = isset($data['month']) && $data['month'] !== '' ? $data['month'] : date('Y-m');

    $overridesParDate = array();
    foreach (jourtravailoverride::findAllByMonth($mois) as $o) {
        $overridesParDate[$o->getDate()] = $o;
    }

    $premierJour = new DateTime($mois . '-01');
    $dernierJourMois = (int) $premierJour->format('t');
    $decalageDebut = ((int) $premierJour->format('N')) - 1; // Nb de cases vides avant le 1 (0 = lundi).

    $jours = array();
    for ($jour = 1; $jour <= $dernierJourMois; $jour++) {
        $date = sprintf('%s-%02d', $mois, $jour);
        $dateObj = new DateTime($date);
        $override = isset($overridesParDate[$date]) ? $overridesParDate[$date] : null;
        // Un jour férié (com_holiday) est automatiquement non travaillé, sans dérogation à créer -
        // sauf si l'admin a explicitement forcé ce jour via une dérogation manuelle, qui prime
        // toujours (ex: un jour férié exceptionnellement travaillé).
        $ferie = $override ? null : holiday::findByDate($date);
        $estFerie = $ferie && $ferie->getId();
        $jours[] = array(
            'date' => $date,
            'jour' => $jour,
            'jour_semaine' => (int) $dateObj->format('N'),
            'est_travaille' => $estFerie ? false : pointageweb::estJourTravaille($dateObj),
            'override' => $override !== null,
            'ferie' => $estFerie,
            'ferie_nom' => $estFerie ? $ferie->getName() : '',
            'remark' => $override ? $override->getRemark() : '',
        );
    }

    include(__DIR__ . '/../../views/pointage/jours_travail_calendar.php');
}

// Bascule ou réinitialise la dérogation d'un jour précis - déclenché par le modal de confirmation
// de la carte "Gestion des jours de travail". 'reset' supprime la dérogation (retour à la règle
// automatique lundi-vendredi + un samedi sur deux), sinon force travaillé/non travaillé.
function toggleJourTravail($data)
{
    if (!$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }

    $date = isset($data['date']) ? $data['date'] : '';
    $action = isset($data['action']) ? $data['action'] : '';
    if (!$date || !in_array($action, array('set_travaille', 'set_non_travaille', 'reset'), true)) {
        echo json_encode(array('success' => 0, 'message' => 'Paramètres invalides.'));
        return;
    }

    $existant = jourtravailoverride::findByDate($date);

    if ($action === 'reset') {
        if ($existant && $existant->getId()) {
            $existant->delete();
        }
        echo json_encode(array('success' => 1));
        return;
    }

    $item = $existant ? $existant : new jourtravailoverride();
    $item->setDate($date);
    $item->setEstTravaille($action === 'set_travaille' ? 1 : 0);
    $item->setRemark(isset($data['remark']) ? trim($data['remark']) : '');
    $item->setLastEdit(date('Y-m-d H:i:s'));

    if ($existant && $existant->getId()) {
        $succes = $item->edit() == 1;
    } else {
        $item->setDateAdd(date('Y-m-d H:i:s'));
        $succes = $item->add() == 1;
    }

    echo json_encode(array('success' => $succes ? 1 : 0));
}

// Calendrier mensuel "Horaires de travail" côté admin (page task=pointage) - un jour par case,
// avec l'horaire effectif (dérogation ou référence) et une marque si dérogation. Passe aussi
// l'horaire de référence courant pour préremplir le petit formulaire au-dessus du calendrier.
function filterHorairesTravail($data)
{
    if (!$_SESSION['user']->hasDroit('view', 'com_resourcehumaine')) {
        return;
    }
    $mois = isset($data['month']) && $data['month'] !== '' ? $data['month'] : date('Y-m');
    $reference = horairereference::find();

    $premierJour = new DateTime($mois . '-01');
    $dernierJourMois = (int) $premierJour->format('t');
    $decalageDebut = ((int) $premierJour->format('N')) - 1;

    $jours = array();
    for ($jour = 1; $jour <= $dernierJourMois; $jour++) {
        $date = sprintf('%s-%02d', $mois, $jour);
        $horaire = pointageweb::getHoraireJour($date);
        $jours[] = array(
            'date' => $date,
            'jour' => $jour,
            'horaire' => $horaire,
        );
    }

    include(__DIR__ . '/../../views/pointage/horaires_travail_calendar.php');
}

// Met à jour l'horaire de référence de l'entreprise (singleton) - formulaire en haut de la carte
// "Horaires de travail".
function saveHoraireReference($data)
{
    if (!$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }
    $indices = array('heure_debut_matin', 'heure_fin_matin', 'heure_debut_apresmidi', 'heure_fin_apresmidi');
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Veuillez renseigner les 4 horaires.'));
        return;
    }

    $reference = horairereference::find();
    $reference->setHeureDebutMatin($data['heure_debut_matin']);
    $reference->setHeureFinMatin($data['heure_fin_matin']);
    $reference->setHeureDebutApresmidi($data['heure_debut_apresmidi']);
    $reference->setHeureFinApresmidi($data['heure_fin_apresmidi']);
    $reference->setLastEdit(date('Y-m-d H:i:s'));
    $succes = $reference->edit() == 1;

    echo json_encode(array('success' => $succes ? 1 : 0));
}

// Force un horaire spécifique pour UN jour précis (dérogation à l'horaire de référence) - déclenché
// par le modal de la carte "Horaires de travail". Ne touche jamais à travaillé/non travaillé : si
// la dérogation n'existe pas encore, la crée avec est_travaille = la règle automatique du jour
// (jamais l'inverse - changer l'horaire ne doit jamais, en effet de bord, transformer un jour
// chômé en jour travaillé ou vice-versa).
function saveHoraireJour($data)
{
    if (!$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }
    $date = isset($data['date']) ? $data['date'] : '';
    if (!$date) {
        echo json_encode(array('success' => 0, 'message' => 'Date manquante.'));
        return;
    }
    // Dimanche : jamais d'horaire à configurer (jamais travaillé) - refusé aussi côté serveur, pas
    // seulement retiré du calendrier admin. Samedi : jamais d'après-midi, seuls les 2 champs matin
    // sont exigés/retenus - toujours vrai, que ce samedi précis soit travaillé ou non cette
    // semaine-là (l'alternance est gérée séparément par jourtravailoverride.est_travaille).
    $jourSemaineIso = (int) (new DateTime($date))->format('N');
    if ($jourSemaineIso === 7) {
        echo json_encode(array('success' => 0, 'message' => 'Dimanche n\'a jamais d\'horaire de travail.'));
        return;
    }
    $estSamedi = $jourSemaineIso === 6;

    $indices = $estSamedi
        ? array('heure_debut_matin', 'heure_fin_matin')
        : array('heure_debut_matin', 'heure_fin_matin', 'heure_debut_apresmidi', 'heure_fin_apresmidi');
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Veuillez renseigner les horaires.'));
        return;
    }

    $existant = jourtravailoverride::findByDate($date);
    $item = $existant ? $existant : new jourtravailoverride();
    $item->setDate($date);
    $item->setHeureDebutMatin($data['heure_debut_matin']);
    $item->setHeureFinMatin($data['heure_fin_matin']);
    $item->setHeureDebutApresmidi($estSamedi ? null : $data['heure_debut_apresmidi']);
    $item->setHeureFinApresmidi($estSamedi ? null : $data['heure_fin_apresmidi']);
    $item->setLastEdit(date('Y-m-d H:i:s'));

    if ($existant && $existant->getId()) {
        $succes = $item->edit() == 1;
    } else {
        $item->setEstTravaille(pointageweb::estJourTravailleAutomatique(new DateTime($date)) ? 1 : 0);
        $item->setRemark('');
        $item->setDateAdd(date('Y-m-d H:i:s'));
        $succes = $item->add() == 1;
    }

    echo json_encode(array('success' => $succes ? 1 : 0));
}

// Retire la dérogation d'horaire d'un jour (revient à l'horaire de référence) - ne supprime la
// ligne que si elle ne sert plus à rien d'autre (travaillé/non travaillé toujours conforme à la
// règle automatique), sinon se contente de vider les 4 champs horaires.
function resetHoraireJour($data)
{
    if (!$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }
    $date = isset($data['date']) ? $data['date'] : '';
    if (!$date) {
        echo json_encode(array('success' => 0, 'message' => 'Date manquante.'));
        return;
    }

    $existant = jourtravailoverride::findByDate($date);
    if (!$existant || !$existant->getId()) {
        echo json_encode(array('success' => 1));
        return;
    }

    $estAutomatique = pointageweb::estJourTravailleAutomatique(new DateTime($date));
    if ((int) $existant->getEstTravaille() === ($estAutomatique ? 1 : 0)) {
        // La ligne ne portait qu'un horaire, plus utile maintenant qu'on le retire.
        $succes = $existant->delete() == 1;
    } else {
        // La ligne porte aussi une dérogation travaillé/non travaillé encore active - la garder,
        // juste vider les horaires.
        $existant->setHeureDebutMatin(null);
        $existant->setHeureFinMatin(null);
        $existant->setHeureDebutApresmidi(null);
        $existant->setHeureFinApresmidi(null);
        $existant->setLastEdit(date('Y-m-d H:i:s'));
        $succes = $existant->edit() == 1;
    }

    echo json_encode(array('success' => $succes ? 1 : 0));
}

// Met à jour la localisation du bureau (singleton) - latitude/longitude/rayon en mètres, formulaire
// de la carte "Localisation du bureau" (page task=pointage). C'est ce point qui sert de référence
// à pointagelocalisation::estDansLeRayon(), appelé par pointerWeb() à chaque pointage.
function saveLocalisationBureau($data)
{
    if (!$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }
    $indices = array('latitude', 'longitude', 'rayon_metres');
    if (!fieldCheck($data, $indices) || !is_numeric($data['latitude']) || !is_numeric($data['longitude']) || !is_numeric($data['rayon_metres'])) {
        echo json_encode(array('success' => 0, 'message' => 'Veuillez renseigner une latitude, une longitude et un rayon valides.'));
        return;
    }

    $localisation = pointagelocalisation::find();
    $localisation->setLatitude((float) $data['latitude']);
    $localisation->setLongitude((float) $data['longitude']);
    $localisation->setRayonMetres((int) $data['rayon_metres']);
    $localisation->setLastEdit(date('Y-m-d H:i:s'));
    $succes = $localisation->edit() == 1;

    echo json_encode(array('success' => $succes ? 1 : 0));
}

// Justifie un retard (case orange "Retard" du calendrier par employé, voir
// pointage_web_table.php) - upsert une ligne retardjustification pour (employé, date), avec
// remarque et justificatif optionnel (même dossier/extensions que la justification d'absence,
// voir components/com_resourcehumaine/controleurs/absence/controleur.php). Une fois enregistré,
// ce retard ne compte plus dans les totaux (classifierJour()/calendrierMois() la consultent).
function justifyRetard($data)
{
    if (!$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }
    $indices = array('id_resourcehumaine', 'date');
    if (!fieldCheck($data, $indices)) {
        echo json_encode(array('success' => 0, 'message' => 'Paramètres invalides.'));
        return;
    }

    // Pas de vérification d'agence ici (contrairement à deleteAbsenceResourceHumaine) : la liste
    // "Statistique de pointage" qui alimente cette case (filterPointageWeb(),
    // resourcehumaine::findAllByStatuses()) est volontairement globale, tous employés toutes
    // agences confondus, pas filtrée par $_SESSION['agence'] - un admin peut légitimement
    // justifier le retard d'un employé d'une autre agence que celle actuellement active. Un
    // premier essai avec cette vérification bloquait justement ce cas ("Erreur lors de la mise à
    // jour" en prod alors que la requête était légitime).
    $employe = resourcehumaine::find($data['id_resourcehumaine']);
    if (!$employe || !$employe->getId()) {
        echo json_encode(array('success' => 0, 'message' => 'Employé introuvable.'));
        return;
    }

    $justification = array();
    if (isset($_FILES['justification']) && $_FILES['justification']['name'][0] != '') {
        $justification = uploadFiles('justification', '../../../images/resourceshumaines/retards', array('PDF', 'pdf', 'jpg', 'jpeg', 'gif', 'png', 'webp', 'JPG', 'JPEG', 'GIF', 'PNG', 'WEBP'));
    }

    $existant = retardjustification::findByDate($employe->getId(), $data['date']);
    $item = $existant && $existant->getId() ? $existant : new retardjustification();
    if (isset($justification[0])) {
        $item->setJustification($justification[0]);
    }
    $item->setResourcehumaine($employe);
    $item->setDate($data['date']);
    $item->setRemark(isset($data['remark']) ? trim($data['remark']) : '');
    $item->setLastEdit(date('Y-m-d H:i:s'));

    if ($existant && $existant->getId()) {
        $succes = $item->edit() == 1;
    } else {
        $item->setDateAdd(date('Y-m-d H:i:s'));
        $succes = $item->add() == 1;
    }

    echo json_encode(array('success' => $succes ? 1 : 0));
}
