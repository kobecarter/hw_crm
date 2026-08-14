<?php

// Pointage web self-service (espace employé, com_elogin) : une ligne par employé par jour,
// remplie progressivement au fil des 4 pointages (arrivée/départ pause/retour pause/départ),
// avec IP capturée à chaque étape. Volontairement distinct de la classe `pointage` (import
// Excel depuis la pointeuse physique, 4 créneaux positionnels sans sémantique) - ne touche
// jamais à cette table/ce flux existant.
class pointageweb
{
    static $table = __prefixe_db__ . "pointage_web";

    // Ordre des étapes - sert à la fois à déterminer la prochaine étape attendue (vue employé)
    // et à valider côté serveur qu'on ne saute pas une étape (contrôleur).
    public static $etapes = array('arrivee', 'depart_pause', 'retour_pause', 'depart');

    private $id;
    private $resourcehumaine;
    private $date;
    private $heure_arrivee;
    private $heure_depart_pause;
    private $heure_retour_pause;
    private $heure_depart;
    private $ip_arrivee;
    private $ip_depart_pause;
    private $ip_retour_pause;
    private $ip_depart;
    private $date_add;
    private $last_edit;

    public function __construct(){
        $this->id = 0;
    }

    public function getId(){ return $this->id; }
    public function getResourcehumaine(){ return $this->resourcehumaine; }
    public function getDate(){ return $this->date; }
    public function getHeureArrivee(){ return $this->heure_arrivee; }
    public function getHeureDepartPause(){ return $this->heure_depart_pause; }
    public function getHeureRetourPause(){ return $this->heure_retour_pause; }
    public function getHeureDepart(){ return $this->heure_depart; }
    public function getIpArrivee(){ return $this->ip_arrivee; }
    public function getIpDepartPause(){ return $this->ip_depart_pause; }
    public function getIpRetourPause(){ return $this->ip_retour_pause; }
    public function getIpDepart(){ return $this->ip_depart; }
    public function getDateAdd(){ return $this->date_add; }
    public function getLastEdit(){ return $this->last_edit; }

    public function setId($id){ $this->id = $id; }
    public function setResourcehumaine($resourcehumaine){ $this->resourcehumaine = $resourcehumaine; }
    public function setDate($date){ $this->date = $date; }
    public function setHeureArrivee($v){ $this->heure_arrivee = $v; }
    public function setHeureDepartPause($v){ $this->heure_depart_pause = $v; }
    public function setHeureRetourPause($v){ $this->heure_retour_pause = $v; }
    public function setHeureDepart($v){ $this->heure_depart = $v; }
    public function setIpArrivee($v){ $this->ip_arrivee = $v; }
    public function setIpDepartPause($v){ $this->ip_depart_pause = $v; }
    public function setIpRetourPause($v){ $this->ip_retour_pause = $v; }
    public function setIpDepart($v){ $this->ip_depart = $v; }
    public function setDateAdd($v){ $this->date_add = $v; }
    public function setLastEdit($v){ $this->last_edit = $v; }

    // Prochaine étape attendue ('arrivee'|'depart_pause'|'retour_pause'|'depart'), ou null si
    // les 4 sont déjà pointées (journée terminée) - source de vérité unique, utilisée par la
    // vue (quel bouton activer) ET le contrôleur (refuse une étape hors séquence).
    public function getProchaineEtape()
    {
        $getters = array(
            'arrivee' => 'getHeureArrivee',
            'depart_pause' => 'getHeureDepartPause',
            'retour_pause' => 'getHeureRetourPause',
            'depart' => 'getHeureDepart',
        );
        foreach (self::$etapes as $etape) {
            if (!$this->{$getters[$etape]}()) {
                return $etape;
            }
        }
        return null;
    }

    // Minutes travaillées = (départ pause - arrivée) + (départ - retour pause). Si la journée
    // est en cours (pas encore de départ pause, ou pas encore de départ final), calcule jusqu'à
    // maintenant plutôt que de laisser un trou - c'est ce qui alimente le compteur temps réel.
    public function getMinutesTravaillees()
    {
        if (!$this->heure_arrivee) {
            return 0;
        }
        $jour = $this->date;
        $arrivee = strtotime($jour . ' ' . $this->heure_arrivee);
        $minutes = 0;

        if ($this->heure_depart_pause) {
            $minutes += (strtotime($jour . ' ' . $this->heure_depart_pause) - $arrivee) / 60;
        } else {
            // Toujours en matinée : compte jusqu'à maintenant.
            $minutes += (time() - $arrivee) / 60;
            return max(0, round($minutes));
        }

        if ($this->heure_retour_pause) {
            if ($this->heure_depart) {
                $minutes += (strtotime($jour . ' ' . $this->heure_depart) - strtotime($jour . ' ' . $this->heure_retour_pause)) / 60;
            } else {
                $minutes += (time() - strtotime($jour . ' ' . $this->heure_retour_pause)) / 60;
            }
        }

        return max(0, round($minutes));
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_resourcehumaine, date, heure_arrivee, heure_depart_pause, heure_retour_pause, heure_depart, ip_arrivee, ip_depart_pause, ip_retour_pause, ip_depart, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->resourcehumaine->getId(), "int"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->heure_arrivee, "text"),
            GetSQLValueString($this->heure_depart_pause, "text"),
            GetSQLValueString($this->heure_retour_pause, "text"),
            GetSQLValueString($this->heure_depart, "text"),
            GetSQLValueString($this->ip_arrivee, "text"),
            GetSQLValueString($this->ip_depart_pause, "text"),
            GetSQLValueString($this->ip_retour_pause, "text"),
            GetSQLValueString($this->ip_depart, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)) {
            $this->setId($db->last_id());
            return 1;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET heure_arrivee = %s, heure_depart_pause = %s, heure_retour_pause = %s, heure_depart = %s, ip_arrivee = %s, ip_depart_pause = %s, ip_retour_pause = %s, ip_depart = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->heure_arrivee, "text"),
            GetSQLValueString($this->heure_depart_pause, "text"),
            GetSQLValueString($this->heure_retour_pause, "text"),
            GetSQLValueString($this->heure_depart, "text"),
            GetSQLValueString($this->ip_arrivee, "text"),
            GetSQLValueString($this->ip_depart_pause, "text"),
            GetSQLValueString($this->ip_retour_pause, "text"),
            GetSQLValueString($this->ip_depart, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $item = new pointageweb();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s", GetSQLValueString($id, "int"));
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    public static function findByResourcehumaineAndDate($id_resourcehumaine, $date)
    {
        global $db;
        $item = null;
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s AND date = %s",
            GetSQLValueString($id_resourcehumaine, "int"),
            GetSQLValueString($date, "date")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    // Ligne du jour pour cet employé, ou un objet neuf (pas encore inséré, id=0) si aucun
    // pointage n'a encore été fait aujourd'hui - le contrôleur décide add() vs edit() selon
    // getId(). Utilisé par la vue employé à chaque affichage.
    public static function findOrCreateAujourdhui($resourcehumaine)
    {
        $existant = self::findByResourcehumaineAndDate($resourcehumaine->getId(), date('Y-m-d'));
        if ($existant) {
            return $existant;
        }
        $item = new pointageweb();
        $item->setResourcehumaine($resourcehumaine);
        $item->setDate(date('Y-m-d'));
        return $item;
    }

    // Tous les pointages d'un employé pour un mois donné (format 'YYYY-MM') - alimente le
    // tableau mensuel admin (voir filterPointageWeb).
    public static function findAllByResourcehumaineAndMonth($id_resourcehumaine, $mois)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_resourcehumaine = %s AND date LIKE %s ORDER BY date ASC",
            GetSQLValueString($id_resourcehumaine, "int"),
            GetSQLValueString($mois . '%', "text")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function build($data)
    {
        $item = new pointageweb();
        $item->setId($data['id']);
        $item->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
        $item->setDate($data['date']);
        $item->setHeureArrivee($data['heure_arrivee']);
        $item->setHeureDepartPause($data['heure_depart_pause']);
        $item->setHeureRetourPause($data['heure_retour_pause']);
        $item->setHeureDepart($data['heure_depart']);
        $item->setIpArrivee($data['ip_arrivee']);
        $item->setIpDepartPause($data['ip_depart_pause']);
        $item->setIpRetourPause($data['ip_retour_pause']);
        $item->setIpDepart($data['ip_depart']);
        $item->setDateAdd($data['date_add']);
        $item->setLastEdit($data['last_edit']);
        return $item;
    }

    // "Ce jour est-il congé/férié pour cet employé" - jamais de pointage possible ces jours-là.
    // Réutilisé à l'identique côté serveur (pointerWeb(), la vraie barrière) et côté vue
    // (profile_pointage.php, juste pour l'affichage). Ne retient que nature=1 (Congé) parmi les
    // absences couvrant la date - une absence "Non justifié"/"Justifié" existante ne bloque pas
    // le pointage, seul un congé approuvé le fait.
    public static function jourBloquePourPointage($resourcehumaine, $date)
    {
        $ferie = holiday::findByDate($date);
        if ($ferie && $ferie->getId()) {
            return 'ferie';
        }
        $absence = absence::findByDate($resourcehumaine->getId(), $date);
        if ($absence && $absence->getId() && $absence->getNatureOfAbsence() == 1) {
            return 'conge';
        }
        return null;
    }

    // "Ce jour est-il un jour attendu de pointage" : d'abord une éventuelle dérogation manuelle
    // (jourtravailoverride, gérée depuis la page admin task=pointage - ex: un samedi de
    // rattrapage forcé travaillé, un jour normalement ouvré forcé chômé), sinon la règle
    // automatique par défaut : lundi-vendredi toujours, samedi un sur deux (semaines ISO paires),
    // jamais le dimanche. Static ici (pas une fonction globale dans pointageweb/controleur.php)
    // pour être fiable partout via l'autoload, y compris depuis des contextes qui n'incluent
    // jamais ce contrôleur (tableau de bord employé, fiche admin) - même règle que les rappels
    // Slack programmés (cronPointageRappelEndpoint), cohérence garantie.
    public static function estJourTravaille($date)
    {
        $override = jourtravailoverride::findByDate($date->format('Y-m-d'));
        if ($override && $override->getId()) {
            return $override->getEstTravaille() == 1;
        }
        return self::estJourTravailleAutomatique($date);
    }

    // Seulement la règle automatique (lundi-vendredi, samedi un sur deux), sans regarder les
    // dérogations - séparée de estJourTravaille() pour que toggleJourTravail() (controleur) puisse
    // calculer la valeur "par défaut" à enregistrer quand l'admin crée une dérogation portant
    // uniquement sur l'horaire (pas sur travaillé/non travaillé), sans écraser la règle du jour.
    public static function estJourTravailleAutomatique($date)
    {
        $jourSemaineIso = (int) $date->format('N');
        if ($jourSemaineIso >= 1 && $jourSemaineIso <= 5) {
            return true;
        }
        if ($jourSemaineIso == 6) {
            return ((int) $date->format('W')) % 2 === 0;
        }
        return false;
    }

    // Horaire effectif d'une date : dérogation jourtravailoverride si elle porte un horaire
    // spécifique, sinon l'horaire de référence de l'entreprise (horairereference, éditable depuis
    // la page admin task=pointage - "Gestion des horaires de travail"). Toujours les 4 bornes
    // (matin début/fin, après-midi début/fin) même si seule la première sert aujourd'hui au calcul
    // du retard - les autres alimentent l'affichage/l'infobulle et un futur calcul plus complet.
    public static function getHoraireJour($date)
    {
        $override = jourtravailoverride::findByDate($date);
        if ($override && $override->getId() && $override->getHeureDebutMatin()) {
            return array(
                'heure_debut_matin' => $override->getHeureDebutMatin(),
                'heure_fin_matin' => $override->getHeureFinMatin(),
                'heure_debut_apresmidi' => $override->getHeureDebutApresmidi(),
                'heure_fin_apresmidi' => $override->getHeureFinApresmidi(),
                'source' => 'override',
            );
        }
        $reference = horairereference::find();
        return array(
            'heure_debut_matin' => $reference->getHeureDebutMatin(),
            'heure_fin_matin' => $reference->getHeureFinMatin(),
            'heure_debut_apresmidi' => $reference->getHeureDebutApresmidi(),
            'heure_fin_apresmidi' => $reference->getHeureFinApresmidi(),
            'source' => 'reference',
        );
    }

    // Classe UN jour donné pour cet employé : 'absence' (aucun pointage, ou arrivée >2h après
    // l'heure de référence), 'retard' (arrivée en retard mais <=2h, avec le nombre de minutes),
    // 'ok' (à l'heure) ou null (jour non attendu / pas encore jugeable / déjà couvert par
    // congé-férié-absence). Brique de base unique réutilisée par calculerStatsMois() (vue
    // mensuelle, admin + espace employé) ET par le cron de détection d'absence (un seul jour :
    // hier) - jamais deux endroits qui recalculent la même règle métier (seuil 2h, même
    // convention que l'ancien pointage::getDelay() dans pointage/controleur.php). L'heure de
    // référence n'est plus fixe : getHoraireJour() résout la dérogation du jour ou, à défaut,
    // l'horaire de référence de l'entreprise (horairereference).
    public static function classifierJour($resourcehumaine, $date)
    {
        $dateObj = new DateTime($date);
        if (!self::estJourTravaille($dateObj)) {
            return null;
        }
        $horaire = self::getHoraireJour($date);
        $heureDebutMatin = $horaire['heure_debut_matin'];

        $aujourdhui = date('Y-m-d');
        $heureActuelle = date('H:i');
        // Grâce de 2h avant de juger le jour même - on ne veut pas marquer "absent" un employé
        // qui a jusqu'à (heure de référence + 2h) pour pointer.
        $heureGrace = date('H:i', strtotime($heureDebutMatin) + 7200);
        if ($date > $aujourdhui || ($date === $aujourdhui && $heureActuelle < $heureGrace)) {
            return null;
        }
        if (self::jourBloquePourPointage($resourcehumaine, $date)) {
            return null;
        }
        $absenceExistante = absence::findByDate($resourcehumaine->getId(), $date);
        if ($absenceExistante && $absenceExistante->getId()) {
            return null;
        }

        $pointage = self::findByResourcehumaineAndDate($resourcehumaine->getId(), $date);
        $arrivee = $pointage ? $pointage->getHeureArrivee() : null;

        if (!$arrivee) {
            return array('type' => 'absence', 'motif' => 'aucun pointage enregistré');
        }
        $minutesRetard = (strtotime($date . ' ' . $arrivee) - strtotime($date . ' ' . $heureDebutMatin)) / 60;
        if ($minutesRetard > 120) {
            return array('type' => 'absence', 'motif' => 'retard de plus de 2h');
        }
        if ($minutesRetard > 0) {
            return array('type' => 'retard', 'minutes' => (int) round($minutesRetard));
        }
        return array('type' => 'ok');
    }

    // Agrège classifierJour() sur tout un mois - alimente les KPI (dashboard employé, fiche
    // admin, page task=pointage).
    public static function calculerStatsMois($resourcehumaine, $mois)
    {
        $retardMinutes = 0;
        $retardJours = 0;
        $absenceJours = array();

        $premierJour = new DateTime($mois . '-01');
        $dernierJourMois = (int) $premierJour->format('t');

        for ($jour = 1; $jour <= $dernierJourMois; $jour++) {
            $date = sprintf('%s-%02d', $mois, $jour);
            $classification = self::classifierJour($resourcehumaine, $date);
            if (!$classification) {
                continue;
            }
            if ($classification['type'] === 'absence') {
                $absenceJours[] = $date;
            } elseif ($classification['type'] === 'retard') {
                $retardMinutes += $classification['minutes'];
                $retardJours++;
            }
        }

        return array(
            'retard_minutes' => (int) round($retardMinutes),
            'retard_jours' => $retardJours,
            'absence_jours' => $absenceJours,
        );
    }

    // Détail jour par jour d'un mois pour un employé - alimente le calendrier de la page admin
    // task=pointage (une case par jour : couleur selon le type). Contrairement à classifierJour()
    // (qui ne renvoie qu'un verdict retard/absence/ok/null), cette méthode distingue explicitement
    // POURQUOI un jour est neutre (weekend, férié, congé, à venir) - nécessaire pour la légende
    // couleur - et expose l'id de la vraie ligne absence quand il y en a une (nature 2 ou 3),
    // seule condition pour pouvoir la "justifier" depuis le calendrier (une absence seulement
    // pressentie par le calcul, pas encore convertie en ligne par le cron, n'a rien à justifier).
    public static function calendrierMois($resourcehumaine, $mois)
    {
        $jours = array();
        $premierJour = new DateTime($mois . '-01');
        $dernierJourMois = (int) $premierJour->format('t');
        $aujourdhui = date('Y-m-d');
        $heureActuelle = date('H:i');

        $pointagesParDate = array();
        foreach (self::findAllByResourcehumaineAndMonth($resourcehumaine->getId(), $mois) as $p) {
            $pointagesParDate[$p->getDate()] = $p;
        }

        for ($jour = 1; $jour <= $dernierJourMois; $jour++) {
            $date = sprintf('%s-%02d', $mois, $jour);
            $dateObj = new DateTime($date);
            $entry = array('date' => $date, 'jour' => $jour);
            $horaireJour = self::getHoraireJour($date);
            $heureDebutMatinJour = $horaireJour['heure_debut_matin'];
            $heureGraceJour = date('H:i', strtotime($heureDebutMatinJour) + 7200);

            if (!self::estJourTravaille($dateObj)) {
                $entry['type'] = 'weekend';
            } elseif ($date > $aujourdhui || ($date === $aujourdhui && $heureActuelle < $heureGraceJour)) {
                $entry['type'] = 'futur';
            } else {
                $blocage = self::jourBloquePourPointage($resourcehumaine, $date);
                if ($blocage === 'ferie') {
                    $ferie = holiday::findByDate($date);
                    $entry['type'] = 'ferie';
                    $entry['label'] = $ferie ? $ferie->getName() : 'Jour férié';
                } elseif ($blocage === 'conge') {
                    $entry['type'] = 'conge';
                } else {
                    $absenceDuJour = absence::findByDate($resourcehumaine->getId(), $date);
                    if ($absenceDuJour && $absenceDuJour->getId() && $absenceDuJour->getNatureOfAbsence() == 3) {
                        $entry['type'] = 'absence_non_justifiee';
                        $entry['absence_id'] = $absenceDuJour->getId();
                        $entry['remark'] = $absenceDuJour->getRemark();
                        $entry['number_of_days'] = $absenceDuJour->getNumberOfDays();
                    } elseif ($absenceDuJour && $absenceDuJour->getId() && $absenceDuJour->getNatureOfAbsence() == 2) {
                        $entry['type'] = 'absence_justifiee';
                        $entry['absence_id'] = $absenceDuJour->getId();
                        $entry['remark'] = $absenceDuJour->getRemark();
                    } else {
                        $pointage = isset($pointagesParDate[$date]) ? $pointagesParDate[$date] : null;
                        $arrivee = $pointage ? $pointage->getHeureArrivee() : null;
                        if (!$arrivee) {
                            $entry['type'] = 'absence_en_attente';
                        } else {
                            $minutesRetard = (strtotime($date . ' ' . $arrivee) - strtotime($date . ' ' . $heureDebutMatinJour)) / 60;
                            if ($minutesRetard > 120) {
                                $entry['type'] = 'absence_en_attente';
                            } elseif ($minutesRetard > 0) {
                                $entry['type'] = 'retard';
                                $entry['minutes'] = (int) round($minutesRetard);
                            } else {
                                $entry['type'] = 'ok';
                            }
                        }
                    }
                }
            }

            // Horaires des 4 pointages du jour (arrivée/départ pause/retour pause/départ), s'il y
            // en a - alimente l'infobulle au survol de la case, quel que soit son type (utile même
            // un jour "ok" ou "retard", pas seulement les absences).
            if (isset($pointagesParDate[$date])) {
                $p = $pointagesParDate[$date];
                $entry['pointage'] = array(
                    'arrivee' => $p->getHeureArrivee(),
                    'depart_pause' => $p->getHeureDepartPause(),
                    'retour_pause' => $p->getHeureRetourPause(),
                    'depart' => $p->getHeureDepart(),
                );
            }
            $entry['horaire_attendu'] = $horaireJour;

            $jours[] = $entry;
        }
        return $jours;
    }
}
