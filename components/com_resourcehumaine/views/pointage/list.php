<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Pointage</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Pointage</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistique de pointage - entièrement basée sur le pointage web self-service
             (espace employé). L'ancien import Excel (classe `pointage`, table crm_pointage,
             controleur filterPointage()) reste intact côté backend mais n'est plus exposé sur
             cette page. -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0"><i class="fa fa-wifi mr-1"></i> Statistique de pointage</h4>
                        <form method="post" action="" id="pointageWebFilterForm" class="mb-0">
                            <div class="form-group mb-0">
                                <input type="month" class="form-control" value="<?=date('Y-m')?>" name="month_web" style="width:220px">
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="col-12 pointage-web-msgbox msgbox"></div>
                        <div class="pointage-web-content">
                            <div class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin mr-2"></i>Chargement...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jours & horaires de travail - un seul box, deux panneaux : à gauche la dérogation
             manuelle travaillé/non travaillé (jours-travail-content), à droite l'horaire de
             référence + dérogation d'heures (horaires-travail-content). Deux sections
             indépendantes (filtres mois, AJAX, données séparés) simplement regroupées visuellement
             - global à l'entreprise, change le calcul retard/absence de tous les employés, pas
             seulement l'affichage. -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row jt-split-row">
                            <div class="col-lg-6 jt-split-pane jt-split-pane-left">
                                <div class="jt-split-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h4 class="jt-split-title"><i class="fa fa-calendar-alt mr-1"></i> Gestion des jours de travail</h4>
                                        <p class="text-muted mb-0" style="font-size:.82rem;">Cliquez un jour pour forcer "travaillé" ou "non travaillé", ou revenir à la règle automatique.</p>
                                    </div>
                                    <form method="post" action="" id="joursTravailFilterForm" class="mb-0">
                                        <div class="form-group mb-0">
                                            <input type="month" class="form-control" value="<?=date('Y-m')?>" name="month_jt" style="width:200px">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 jours-travail-msgbox msgbox"></div>
                                <div class="jt-legend">
                                    <span><i class="jt-dot jt-dot-travaille"></i> Travaillé</span>
                                    <span><i class="jt-dot jt-dot-non-travaille"></i> Non travaillé</span>
                                    <span><i class="fa fa-thumbtack jt-override-icon-legend"></i> Dérogation manuelle</span>
                                </div>
                                <div class="jours-travail-content">
                                    <div class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin mr-2"></i>Chargement...</div>
                                </div>
                            </div>
                            <div class="col-lg-6 jt-split-pane jt-split-pane-right">
                                <div class="jt-split-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <h4 class="jt-split-title"><i class="fa fa-business-time mr-1"></i> Horaires de travail</h4>
                                        <p class="text-muted mb-0" style="font-size:.82rem;">Horaire de référence, et dérogation possible pour un jour précis. Survolez un jour pour voir son horaire.</p>
                                    </div>
                                    <form method="post" action="" id="horairesTravailFilterForm" class="mb-0">
                                        <div class="form-group mb-0">
                                            <input type="month" class="form-control" value="<?=date('Y-m')?>" name="month_hr" style="width:200px">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 horaires-travail-msgbox msgbox"></div>
                                <div class="horaires-travail-content">
                                    <div class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin mr-2"></i>Chargement...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Localisation du bureau - remplace le contrôle Wi-Fi/IP (pointageIpAutorisee(), qui
             exige une IP publique fixe - payante chez la plupart des FAI marocains pour une ligne
             résidentielle) par une vérification de position GPS/Wi-Fi fournie par le navigateur de
             l'employé au moment du pointage (voir pointerWeb() + pointagelocalisation::
             estDansLeRayon()). Gratuit, insensible à un changement d'IP. -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="fa fa-location-dot mr-1"></i> Localisation du bureau</h4>
                        <p class="text-muted mb-0" style="font-size:.82rem;">Utilisée pour vérifier que l'employé pointe bien depuis le bureau (géolocalisation du navigateur) - plus besoin d'IP fixe.</p>
                    </div>
                    <div class="card-body">
                        <div class="col-12 localisation-bureau-msgbox msgbox"></div>
                        <form id="localisationBureauForm" class="loc-bureau-form">
                            <div class="hr-field">
                                <label>Latitude</label>
                                <input type="number" step="0.0000001" name="latitude" id="locBureauLatitude" class="form-control" value="<?= htmlspecialchars($localisationBureau->getLatitude()) ?>" required>
                            </div>
                            <div class="hr-field">
                                <label>Longitude</label>
                                <input type="number" step="0.0000001" name="longitude" id="locBureauLongitude" class="form-control" value="<?= htmlspecialchars($localisationBureau->getLongitude()) ?>" required>
                            </div>
                            <div class="hr-field">
                                <label>Rayon toléré (mètres)</label>
                                <input type="number" step="1" min="10" name="rayon_metres" id="locBureauRayon" class="form-control" value="<?= htmlspecialchars($localisationBureau->getRayonMetres()) ?>" required>
                            </div>
                            <a href="javascript:void(0)" id="locBureauMapLink" class="btn btn-outline-secondary" target="_blank"><i class="fa fa-map-location-dot mr-1"></i>Voir sur la carte</a>
                            <button type="submit" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2 loading" style="display:none;"></span>Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Modal "Horaire du jour" - déclenché en cliquant une case du calendrier "Horaires de travail". -->
<div id="horaireJourModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="horaireJourForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="tva-confirm-icon"><i class="fa fa-business-time"></i></div>
                    <h5 class="modal-title mt-3" id="horaireJourModalTitle">—</h5>
                </div>
                <div class="modal-body">
                    <div class="msgbox"></div>
                    <input type="hidden" name="date" id="horaireJourDate">
                    <div class="hr-field-grid">
                        <div class="hr-field">
                            <label>Début matin</label>
                            <input type="time" name="heure_debut_matin" id="horaireJourDebutMatin" class="form-control" required>
                        </div>
                        <div class="hr-field">
                            <label>Fin matin</label>
                            <input type="time" name="heure_fin_matin" id="horaireJourFinMatin" class="form-control" required>
                        </div>
                        <div class="hr-field hr-field-pm">
                            <label>Début après-midi</label>
                            <input type="time" name="heure_debut_apresmidi" id="horaireJourDebutApresmidi" class="form-control" required>
                        </div>
                        <div class="hr-field hr-field-pm">
                            <label>Fin après-midi</label>
                            <input type="time" name="heure_fin_apresmidi" id="horaireJourFinApresmidi" class="form-control" required>
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-2" id="horaireJourSamediNote" style="display:none;font-size:.8rem;"><i class="fa fa-info-circle mr-1"></i>Samedi : matin uniquement, pas d'après-midi.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="horaireJourResetBtn" style="display:none;"><i class="fa fa-undo mr-1"></i>Revenir à la référence</button>
                    <button type="submit" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2 loading" style="display:none;"></span>Enregistrer pour ce jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Infobulle stylée au survol d'une case du calendrier "Statistique de pointage" - remplace le
     title natif du navigateur (plat, non habillé) par une petite carte verre affichant le statut
     du jour + les 4 horaires de pointage. Un seul élément partagé, positionné/rempli en JS à
     chaque survol plutôt qu'une infobulle par case (moins coûteux, plus simple à maintenir). -->
<div id="calTooltip" class="cal-tooltip" role="tooltip"></div>

<!-- Modal de confirmation "jour de travail" (motif .tva-confirm-modal établi, voir CLAUDE.md) -
     jamais de confirm() natif du navigateur pour une action qui affecte tout le calcul retard/
     absence de l'équipe pour ce jour. -->
<div id="jourTravailModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="tva-confirm-icon"><i class="fa fa-calendar-day"></i></div>
                <h5 class="modal-title mt-3" id="jourTravailModalTitle">—</h5>
            </div>
            <div class="modal-body text-center" id="jourTravailModalBody">
                <p class="mb-1" id="jourTravailModalEtat"></p>
                <div class="d-flex justify-content-center flex-wrap" style="gap:10px;margin-top:16px;" id="jourTravailModalActions"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal "Justifier l'absence" - déclenché en cliquant une case rouge (absence non justifiée)
     du calendrier ci-dessus (voir pointage_web_table.php). Réutilise l'endpoint AJAX existant
     editAbsenceResourceHumaine (components/com_resourcehumaine/controleurs/absence/controleur.php)
     - déjà pensé pour répondre en texte brut "1"/"2"/"0", jamais utilisé en AJAX jusqu'ici mais
     rien ne s'y oppose (pas de redirection, pas de header dépendant d'un contexte page). -->
<div class="modal fade" id="justifyAbsenceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="justifyAbsenceForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-user-check mr-2"></i>Justifier l'absence — <span id="justifyEmployeNom"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="msgbox"></div>
                    <p class="text-muted" id="justifyDateLabel"></p>

                    <input type="hidden" name="id_absence" id="justifyIdAbsence">
                    <input type="hidden" name="id_resourcehumaine" id="justifyIdResourcehumaine">
                    <input type="hidden" name="start_date" id="justifyStartDate">
                    <input type="hidden" name="end_date" id="justifyEndDate">
                    <input type="hidden" name="back_date" id="justifyBackDate">
                    <input type="hidden" name="number_of_days" id="justifyNumberOfDays">

                    <div class="form-group">
                        <label>Décision</label>
                        <div class="form-checks">
                            <div class="form-check mb-2">
                                <input type="radio" name="nature_of_absence" class="form-check-input" value="2" id="justifyNature2" checked>
                                <label class="form-check-label" for="justifyNature2">Justifier — l'administration autorise cette absence</label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="radio" name="nature_of_absence" class="form-check-input" value="3" id="justifyNature3">
                                <label class="form-check-label" for="justifyNature3">Laisser "Non justifié"</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Statut</label>
                        <div class="form-checks">
                            <div class="form-check mb-2">
                                <input type="radio" name="status" class="form-check-input" value="0" id="justifyStatus0" checked>
                                <label class="form-check-label" for="justifyStatus0">Non déductible</label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="radio" name="status" class="form-check-input" value="1" id="justifyStatus1">
                                <label class="form-check-label" for="justifyStatus1">Déductible</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remarque</label>
                        <textarea class="form-control" name="remark" id="justifyRemark" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Justificatif (optionnel)</label>
                        <input type="file" name="justification[]" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2 loading" style="display:none;"></span>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Calendrier mensuel par employé (task=pointage) - une case par jour, couleur selon le type
       (calculée côté serveur par pointageweb::calendrierMois()). Volontairement en dehors du
       fragment AJAX (pointage_web_table.php) : ces règles ne changent jamais d'un mois à l'autre,
       pas besoin de les réinjecter à chaque filtre. */
    .cal-legend { display: flex; flex-wrap: wrap; gap: 4px 18px; margin: 4px 0 18px; font-size: .82rem; color: #6c7a93; }
    .cal-legend .cal-dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; margin-right: 5px; vertical-align: middle; }
    .cal-legend small { opacity: .75; }
    .cal-toggle { color: inherit; text-decoration: none; }
    .cal-toggle:hover { color: #6366f1; text-decoration: none; }
    .cal-toggle-icon { transition: transform .2s ease; font-size: .72rem; }
    .cal-toggle[aria-expanded="true"] .cal-toggle-icon { transform: rotate(90deg); }
    .cal-row > td { background: rgba(99,102,241,0.04); padding: 14px 18px; }
    .cal-strip { display: flex; flex-wrap: wrap; gap: 4px; }
    .cal-day {
        width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center;
        font-size: .68rem; font-weight: 600; color: #fff; cursor: default; user-select: none;
        transition: transform .12s ease;
    }
    .cal-day.cal-justifiable { cursor: pointer; box-shadow: 0 0 0 2px rgba(239,68,68,0.25); }
    .cal-day.cal-justifiable:hover { transform: scale(1.15); }
    .cal-ok { background: #22c55e; }
    .cal-retard { background: #f59e0b; }
    .cal-absence-non-justifiee { background: #ef4444; }
    .cal-absence-justifiee { background: #86efac; color: #14532d; }
    .cal-absence-en-attente {
        background: repeating-linear-gradient(45deg, #fca5a5, #fca5a5 4px, #fee2e2 4px, #fee2e2 8px);
        color: #991b1b;
    }
    .cal-conge { background: #6366f1; }
    .cal-ferie { background: #a855f7; }
    .cal-weekend { background: #e2e5ec; color: #9aa2b1; }
    .cal-futur { background: transparent; border: 1px dashed #d8dce5; color: #c2c7d1; }
    :root[data-theme="dark"] .cal-legend { color: #9aa3b5; }
    :root[data-theme="dark"] .cal-row > td { background: rgba(99,102,241,0.08); }
    :root[data-theme="dark"] .cal-weekend { background: #2a2f3c; color: #6b7382; }
    :root[data-theme="dark"] .cal-futur { border-color: #3a3f4c; color: #4b5160; }

    /* Infobulle stylée "verre" au survol d'une case du calendrier - remplace le title natif du
       navigateur. Un seul élément partagé (#calTooltip), positionné/rempli en JS. */
    .cal-tooltip {
        position: fixed; z-index: 3000; min-width: 210px; max-width: 260px;
        background: rgba(255,255,255,0.94); backdrop-filter: blur(18px) saturate(180%); -webkit-backdrop-filter: blur(18px) saturate(180%);
        border: 1px solid rgba(255,255,255,0.7); border-radius: 14px;
        box-shadow: 0 16px 40px rgba(15,23,42,0.2);
        padding: 14px 16px; color: #1f2430;
        opacity: 0; transform: translateY(6px) scale(.98); pointer-events: none;
        transition: opacity .15s ease, transform .15s ease;
    }
    .cal-tooltip.show { opacity: 1; transform: translateY(0) scale(1); }
    .cal-tooltip .ctt-date { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #9aa2b1; margin-bottom: 6px; }
    .cal-tooltip .ctt-status { display: flex; align-items: center; gap: 7px; font-weight: 700; font-size: .88rem; margin-bottom: 10px; }
    .cal-tooltip .ctt-status .dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
    .cal-tooltip .ctt-times { display: grid; grid-template-columns: auto 1fr; gap: 5px 10px; font-size: .78rem; }
    .cal-tooltip .ctt-times .lbl { color: #6c7a93; display: flex; align-items: center; gap: 6px; }
    .cal-tooltip .ctt-times .lbl i { width: 12px; text-align: center; font-size: .72rem; }
    .cal-tooltip .ctt-times .val { font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; font-weight: 600; text-align: right; }
    .cal-tooltip .ctt-extra { margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.08); color: #6c7a93; font-size: .74rem; font-style: italic; }
    :root[data-theme="dark"] .cal-tooltip {
        background: rgba(20,23,28,0.92); border-color: rgba(255,255,255,0.12); color: #eef0ef;
        box-shadow: 0 16px 40px rgba(0,0,0,0.5);
    }
    :root[data-theme="dark"] .cal-tooltip .ctt-date { color: #6b7382; }
    :root[data-theme="dark"] .cal-tooltip .ctt-times .lbl { color: #8b91a0; }
    :root[data-theme="dark"] .cal-tooltip .ctt-extra { color: #8b91a0; border-top-color: rgba(255,255,255,0.1); }

    /* Un seul box "Jours & horaires de travail" scindé en deux panneaux côte à côte (bordure
       verticale entre les deux sur desktop, empilés avec bordure horizontale en dessous de
       lg). Chaque panneau garde son propre filtre mois / AJAX / contenu, seule la présentation
       est fusionnée. */
    .jt-split-row { margin: 0 -8px; }
    .jt-split-pane { padding: 4px 20px; }
    .jt-split-pane-right { border-left: 1px solid rgba(0,0,0,0.08); }
    .jt-split-title { font-size: 1.08rem; font-weight: 700; margin: 0 0 2px; }
    .jt-split-header { margin-bottom: 14px; gap: 10px; }
    @media (max-width: 991.98px) {
        .jt-split-pane-right { border-left: none; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 24px; margin-top: 20px; }
    }
    :root[data-theme="dark"] .jt-split-pane-right { border-left-color: rgba(255,255,255,0.1); }
    :root[data-theme="dark"] .jt-split-pane-right { border-top-color: rgba(255,255,255,0.1); }

    /* Calendrier admin "Gestion des jours de travail" - grille classique 7 colonnes (contrairement
       au strip par employé ci-dessus, celui-ci est un vrai mois avec en-têtes de jours). */
    .jt-legend { display: flex; flex-wrap: wrap; gap: 4px 18px; margin: 4px 0 16px; font-size: .82rem; color: #6c7a93; }
    .jt-legend .jt-dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; margin-right: 5px; vertical-align: middle; }
    .jt-dot-travaille { background: #22c55e; }
    .jt-dot-non-travaille { background: #e2e5ec; }
    .jt-override-icon-legend { color: #6366f1; margin-right: 5px; }
    .jt-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; max-width: 560px; }
    .jt-weekday { text-align: center; font-size: .72rem; font-weight: 700; letter-spacing: .04em; color: #9aa2b1; text-transform: uppercase; padding-bottom: 4px; }
    .jt-day {
        position: relative; aspect-ratio: 1 / 1; border-radius: 8px; display: flex; align-items: center; justify-content: center;
        font-size: .82rem; font-weight: 600; cursor: pointer; user-select: none; transition: transform .12s ease, box-shadow .12s ease;
    }
    .jt-day:hover { transform: scale(1.08); box-shadow: 0 4px 10px rgba(0,0,0,0.12); }
    .jt-day.jt-empty { visibility: hidden; cursor: default; }
    .jt-day.jt-empty:hover { transform: none; box-shadow: none; }
    .jt-travaille { background: #dcfce7; color: #15803d; }
    .jt-non-travaille { background: #e2e5ec; color: #838ba0; }
    .jt-day.jt-override { box-shadow: 0 0 0 2px #6366f1; }
    .jt-override-icon { position: absolute; bottom: 2px; right: 2px; font-size: .55rem; color: #6366f1; }
    :root[data-theme="dark"] .jt-legend { color: #9aa3b5; }
    :root[data-theme="dark"] .jt-weekday { color: #6b7382; }
    :root[data-theme="dark"] .jt-dot-non-travaille { background: #2a2f3c; }
    :root[data-theme="dark"] .jt-travaille { background: #14532d; color: #86efac; }
    :root[data-theme="dark"] .jt-non-travaille { background: #2a2f3c; color: #8b91a0; }

    /* Carte "Horaires de travail" - formulaire de référence + calendrier (réutilise .jt-grid/
       .jt-day, juste une teinte neutre indigo au lieu du vert/gris travaillé/non-travaillé). */
    .horaire-reference-form { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid rgba(0,0,0,0.08); }
    .hr-field-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .hr-field { display: flex; flex-direction: column; gap: 4px; }
    .hr-field label { font-size: .74rem; font-weight: 600; color: #6c7a93; margin-bottom: 0; }
    .hr-field input[type="time"] { border: 1px solid #d8dce5; border-radius: 8px; padding: 6px 10px; font-size: .85rem; background: #fff; }
    .jt-horaire-day { background: #eef0fd; color: #4338ca; }
    /* Dimanche : jamais travaillé, jamais d'horaire - case neutre non cliquable, distincte des
       jours normalement gris "non travaillé" ailleurs (celle-ci n'a même pas d'infobulle). */
    .jt-day.jt-not-applicable { background: transparent; border: 1px dashed #e2e5ec; color: #c2c7d1; cursor: default; }
    .jt-day.jt-not-applicable:hover { transform: none; box-shadow: none; }
    .hr-field.hr-field-pm { transition: opacity .15s ease; }
    :root[data-theme="dark"] .horaire-reference-form { border-bottom-color: rgba(255,255,255,0.1); }
    :root[data-theme="dark"] .hr-field label { color: #9aa3b5; }
    :root[data-theme="dark"] .hr-field input[type="time"] { background: #1b1f26; border-color: #333947; color: #eef0ef; }
    :root[data-theme="dark"] .jt-horaire-day { background: #1e2140; color: #a5b4fc; }
    :root[data-theme="dark"] .jt-day.jt-not-applicable { border-color: #333947; color: #4b5160; }

    /* Carte "Localisation du bureau" - même famille visuelle que .horaire-reference-form. */
    .loc-bureau-form { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 14px; }
    .loc-bureau-form .hr-field { min-width: 160px; }
    .hr-field input[type="number"] { border: 1px solid #d8dce5; border-radius: 8px; padding: 6px 10px; font-size: .85rem; background: #fff; }
    :root[data-theme="dark"] .hr-field input[type="number"] { background: #1b1f26; border-color: #333947; color: #eef0ef; }
</style>

<script type="text/javascript">
    // Micro-animations GSAP propres à cette page : modern-theme.js scanne la page une seule fois
    // au chargement (DOMContentLoaded), donc les tuiles KPI + lignes de tableau injectées ensuite
    // en AJAX (à chaque changement de mois) n'héritent pas automatiquement de l'entrée en
    // cascade / du tilt 3D déjà câblés globalement pour .glass-page - on les rejoue nous-même ici
    // sur le contenu fraîchement injecté, avec le même vocabulaire visuel (stagger { amount, from:
    // 'start' } - jamais un délai fixe par item, cf. convention du projet).
    function animatePointageWebContent() {
        if (typeof gsap === 'undefined') {
            return;
        }
        var $content = document.querySelector('.pointage-web-content');
        if (!$content) {
            return;
        }
        var kpiIcons = $content.querySelectorAll('.dash-widget-icon');
        if (kpiIcons.length) {
            gsap.from(kpiIcons, {
                scale: 0.5,
                opacity: 0,
                duration: 0.5,
                stagger: { amount: 0.4, from: 'start' },
                ease: 'back.out(1.7)',
                clearProps: 'all'
            });
        }
        var rows = $content.querySelectorAll('table tbody tr:not(.cal-row)');
        if (rows.length) {
            gsap.from(rows, {
                opacity: 0,
                y: 6,
                duration: 0.35,
                stagger: { amount: 0.5, from: 'start' },
                ease: 'power1.out',
                clearProps: 'all'
            });
        }
        // Même tilt 3D interactif que .glass-page .card:has(.dash-widget-icon) ailleurs dans
        // l'app (modern-theme.js) - rejoué ici car ce binding ne se fait qu'une fois au
        // chargement de page, avant que ce contenu AJAX n'existe.
        $content.querySelectorAll('.card:has(.dash-widget-icon)').forEach(function(card) {
            card.addEventListener('mousemove', function(e) {
                var rect = card.getBoundingClientRect();
                var px = (e.clientX - rect.left) / rect.width - 0.5;
                var py = (e.clientY - rect.top) / rect.height - 0.5;
                card.style.transform = 'perspective(800px) rotateY(' + (px * 6).toFixed(2) + 'deg) rotateX(' + (py * -6).toFixed(2) + 'deg) translateY(-3px)';
            });
            card.addEventListener('mouseleave', function() {
                card.style.transform = '';
            });
        });
    }

    function filterPointageWeb(month) {
        let order = "month=" + month
        $.post("components/com_resourcehumaine/controleurs/router.php?task=filterPointageWeb", order, function(theResponse) {
            $(".pointage-web-content").html(theResponse);
            animatePointageWebContent();
        });
    }
    // yyyy-mm-dd (attribut data-date, format base) -> dd/mm/yyyy (format attendu par dateBD()
    // côté serveur, même convention que le datepicker du formulaire admin absence/form.php).
    function toDMY(isoDate) {
        var parts = isoDate.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    function addOneDayDMY(isoDate) {
        var d = new Date(isoDate + 'T00:00:00');
        d.setDate(d.getDate() + 1);
        var pad = function(n) { return n < 10 ? '0' + n : n; };
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear();
    }

    // Calendrier "Gestion des jours de travail" - même motif AJAX que filterPointageWeb() mais
    // pour le calendrier global à l'entreprise (pas par employé).
    function filterJoursTravail(month) {
        let order = "month=" + month;
        $.post("components/com_resourcehumaine/controleurs/router.php?task=filterJoursTravail", order, function(theResponse) {
            $(".jours-travail-content").html(theResponse);
        });
    }
    function formatDateFr(isoDate) {
        var d = new Date(isoDate + 'T00:00:00');
        return d.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }
    function envoyerToggleJourTravail(date, action) {
        $.post("components/com_resourcehumaine/controleurs/router.php?task=toggleJourTravail", { date: date, action: action }, function(theResponse) {
            var data;
            try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }
            if (data.success === 1) {
                $('#jourTravailModal').modal('hide');
                $('.jours-travail-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Jour mis à jour.</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                filterJoursTravail($('input[name="month_jt"]').val());
                // Le calendrier par employé (Statistique de pointage) dépend aussi de la règle
                // jour travaillé/non travaillé pour son calcul retard/absence - le rafraîchir
                // aussi pour rester cohérent avec ce qui vient de changer.
                filterPointageWeb($('input[name="month_web"]').val());
            } else {
                $('.jours-travail-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Erreur lors de la mise à jour.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
            }
        }).fail(function() {
            $('.jours-travail-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">La requête a échoué.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
        });
    }

    // Infobulle stylée du calendrier "Statistique de pointage" - construit le HTML depuis le
    // data-tooltip (JSON) posé sur chaque .cal-day (voir pointage_web_table.php), positionne la
    // carte au-dessus/en-dessous de la case selon la place disponible dans le viewport.
    var $calTooltip = null;
    function buildCalTooltipContent(info) {
        var html = '<div class="ctt-date">' + info.jourSemaine + ' ' + info.date + '</div>';
        html += '<div class="ctt-status"><span class="dot" style="background:' + info.statutColor + ';"></span>' + info.statutLabel + '</div>';
        if (info.times) {
            html += '<div class="ctt-times">';
            info.times.forEach(function(l) {
                html += '<span class="lbl"><i class="fa ' + l[0] + '"></i>' + l[1] + '</span><span class="val">' + (l[2] || '—') + '</span>';
            });
            html += '</div>';
        }
        if (info.extra) {
            html += '<div class="ctt-extra">' + info.extra + '</div>';
        }
        return html;
    }
    function positionCalTooltip($cell) {
        var rect = $cell[0].getBoundingClientRect();
        var ttWidth = $calTooltip.outerWidth();
        var ttHeight = $calTooltip.outerHeight();
        var left = rect.left + (rect.width / 2) - (ttWidth / 2);
        left = Math.max(8, Math.min(left, window.innerWidth - ttWidth - 8));
        var top = rect.top - ttHeight - 10;
        if (top < 8) {
            top = rect.bottom + 10;
        }
        $calTooltip.css({ left: left + 'px', top: top + 'px' });
    }

    // Calendrier "Horaires de travail" - même motif AJAX que filterJoursTravail(), inclut aussi le
    // petit formulaire d'horaire de référence (rendu côté serveur avec les valeurs courantes).
    function filterHorairesTravail(month) {
        let order = "month=" + month;
        $.post("components/com_resourcehumaine/controleurs/router.php?task=filterHorairesTravail", order, function(theResponse) {
            $(".horaires-travail-content").html(theResponse);
        });
    }

    $(function() {
        $calTooltip = $('#calTooltip');
        $(document).on('mouseenter', '[data-tooltip]', function() {
            var info;
            try { info = JSON.parse($(this).attr('data-tooltip')); } catch (e) { return; }
            $calTooltip.html(buildCalTooltipContent(info));
            positionCalTooltip($(this));
            $calTooltip.addClass('show');
        });
        $(document).on('mouseleave', '[data-tooltip]', function() {
            $calTooltip.removeClass('show');
        });

        // Carte "Localisation du bureau" - lien carte tenu à jour en direct pendant la saisie,
        // formulaire enregistré via saveLocalisationBureau (voir pointageweb/controleur.php).
        function updateLocBureauMapLink() {
            var lat = $('#locBureauLatitude').val();
            var lng = $('#locBureauLongitude').val();
            if (lat && lng) {
                $('#locBureauMapLink').attr('href', 'https://www.google.com/maps?q=' + encodeURIComponent(lat) + ',' + encodeURIComponent(lng));
            }
        }
        updateLocBureauMapLink();
        $(document).on('input', '#locBureauLatitude, #locBureauLongitude', updateLocBureauMapLink);

        $('#localisationBureauForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type=submit]');
            $btn.prop('disabled', true).find('.loading').show();
            $.post("components/com_resourcehumaine/controleurs/router.php?task=saveLocalisationBureau", $form.serialize(), function(theResponse) {
                var data;
                try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }
                if (data.success === 1) {
                    $('.localisation-bureau-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Localisation du bureau mise à jour.</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                } else {
                    $('.localisation-bureau-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Erreur lors de la mise à jour.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                }
            }).fail(function() {
                $('.localisation-bureau-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">La requête a échoué.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
            }).always(function() {
                $btn.prop('disabled', false).find('.loading').hide();
            });
        });

        $(document).on("change", "input[name='month_web']", function() {
            event.preventDefault();
            filterPointageWeb($(this).val())
        })
        filterPointageWeb($('input[name="month_web"]').val())

        $(document).on("change", "input[name='month_jt']", function() {
            event.preventDefault();
            filterJoursTravail($(this).val())
        })
        filterJoursTravail($('input[name="month_jt"]').val())

        // Ouverture du modal de confirmation "jour de travail" - contenu construit dynamiquement
        // selon l'état actuel du jour cliqué (règle automatique vs dérogation déjà en place).
        $(document).on('click', '.jours-travail-content .jt-day:not(.jt-empty)', function() {
            var $day = $(this);
            var date = $day.data('date');
            var estTravaille = $day.data('est-travaille') == 1;
            var isOverride = $day.data('override') == 1;

            $('#jourTravailModalTitle').text(formatDateFr(date));
            var etatLabel = (estTravaille ? 'Travaillé' : 'Non travaillé') + (isOverride ? ' — dérogation manuelle' : ' — règle automatique');
            $('#jourTravailModalEtat').text('État actuel : ' + etatLabel);

            var $actions = $('#jourTravailModalActions').empty();
            var actionOppose = estTravaille ? 'set_non_travaille' : 'set_travaille';
            var labelOppose = estTravaille ? 'Forcer non travaillé' : 'Forcer travaillé';
            $('<button type="button" class="btn btn-primary"></button>').text(labelOppose).on('click', function() {
                envoyerToggleJourTravail(date, actionOppose);
            }).appendTo($actions);

            if (isOverride) {
                $('<button type="button" class="btn btn-outline-secondary"></button>').html('<i class="fa fa-undo mr-1"></i>Revenir à la règle automatique').on('click', function() {
                    envoyerToggleJourTravail(date, 'reset');
                }).appendTo($actions);
            }

            $('#jourTravailModal').modal('show');
        });

        $(document).on("change", "input[name='month_hr']", function() {
            event.preventDefault();
            filterHorairesTravail($(this).val())
        })
        filterHorairesTravail($('input[name="month_hr"]').val())

        // Formulaire d'horaire de référence - injecté en AJAX (voir horaires_travail_calendar.php),
        // délégué comme le reste de ce contenu variable.
        $(document).on('submit', '#horaireReferenceForm', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type=submit]');
            $btn.prop('disabled', true).find('.loading').show();
            $.post("components/com_resourcehumaine/controleurs/router.php?task=saveHoraireReference", $form.serialize(), function(theResponse) {
                var data;
                try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }
                if (data.success === 1) {
                    $('.horaires-travail-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Horaire de référence mis à jour.</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                    filterHorairesTravail($('input[name="month_hr"]').val());
                    // Le calendrier "Statistique de pointage" dépend aussi de l'horaire de
                    // référence pour son calcul retard - le rafraîchir aussi.
                    filterPointageWeb($('input[name="month_web"]').val());
                } else {
                    $('.horaires-travail-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Erreur lors de la mise à jour.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                }
            }).always(function() {
                $btn.prop('disabled', false).find('.loading').hide();
            });
        });

        // Ouverture du modal "Horaire du jour" - préremplit les 4 champs avec l'horaire effectif
        // du jour cliqué (dérogation si elle existe, sinon référence).
        $(document).on('click', '.horaires-travail-content .jt-day:not(.jt-empty):not(.jt-not-applicable)', function() {
            var $day = $(this);
            var date = $day.data('date');
            var isOverride = $day.data('override') == 1;
            var estSamedi = $day.data('samedi') == 1;

            $('#horaireJourModalTitle').text(formatDateFr(date));
            $('#horaireJourDate').val(date);
            $('#horaireJourDebutMatin').val($day.data('heure-debut-matin'));
            $('#horaireJourFinMatin').val($day.data('heure-fin-matin'));
            $('#horaireJourDebutApresmidi').val($day.data('heure-debut-apresmidi'));
            $('#horaireJourFinApresmidi').val($day.data('heure-fin-apresmidi'));
            // Samedi n'a jamais d'après-midi - masque ces deux champs et les rend non requis
            // plutôt que de bloquer la soumission du formulaire sur des champs sans objet.
            $('.hr-field-pm').toggle(!estSamedi);
            $('#horaireJourDebutApresmidi, #horaireJourFinApresmidi').prop('required', !estSamedi);
            $('#horaireJourSamediNote').toggle(estSamedi);
            $('#horaireJourForm .msgbox').empty();
            $('#horaireJourResetBtn').toggle(isOverride).off('click').on('click', function() {
                $.post("components/com_resourcehumaine/controleurs/router.php?task=resetHoraireJour", { date: date }, function(theResponse) {
                    var data;
                    try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }
                    if (data.success === 1) {
                        $('#horaireJourModal').modal('hide');
                        $('.horaires-travail-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Jour remis à l\'horaire de référence.</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                        filterHorairesTravail($('input[name="month_hr"]').val());
                        filterPointageWeb($('input[name="month_web"]').val());
                    } else {
                        $('#horaireJourForm .msgbox').html('<div class="alert alert-danger" role="alert">Erreur lors de la mise à jour.</div>');
                    }
                });
            });

            $('#horaireJourModal').modal('show');
        });

        $('#horaireJourForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type=submit]');
            $btn.prop('disabled', true).find('.loading').show();
            $.post("components/com_resourcehumaine/controleurs/router.php?task=saveHoraireJour", $form.serialize(), function(theResponse) {
                var data;
                try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }
                if (data.success === 1) {
                    $('#horaireJourModal').modal('hide');
                    $('.horaires-travail-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Horaire du jour mis à jour.</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                    filterHorairesTravail($('input[name="month_hr"]').val());
                    filterPointageWeb($('input[name="month_web"]').val());
                } else {
                    $form.find('.msgbox').html('<div class="alert alert-danger" role="alert">Erreur lors de la mise à jour.</div>');
                }
            }).always(function() {
                $btn.prop('disabled', false).find('.loading').hide();
            });
        });

        // Ouverture du modal "Justifier l'absence" - délégué sur .pointage-web-content (statique,
        // jamais remplacé lui-même, seul son contenu l'est à chaque filtre mensuel) : pas besoin de
        // re-binder après chaque rafraîchissement AJAX, contrairement aux animations GSAP.
        $(document).on('click', '.cal-day.cal-justifiable', function() {
            var $day = $(this);
            var iso = $day.data('date');
            $('#justifyEmployeNom').text($day.data('employe-nom'));
            $('#justifyDateLabel').text('Journée du ' + toDMY(iso));
            $('#justifyIdAbsence').val($day.data('absence-id'));
            $('#justifyIdResourcehumaine').val($day.data('employe-id'));
            $('#justifyStartDate').val(toDMY(iso));
            $('#justifyEndDate').val(toDMY(iso));
            $('#justifyBackDate').val(addOneDayDMY(iso));
            $('#justifyNumberOfDays').val($day.data('number-of-days') || 1);
            $('#justifyRemark').val($day.data('remark') || '');
            $('#justifyNature2').prop('checked', true);
            $('#justifyStatus0').prop('checked', true);
            $('#justifyAbsenceForm .msgbox').empty();
            $('#justifyAbsenceModal').modal('show');
        });

        $('#justifyAbsenceForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type=submit]');
            $btn.prop('disabled', true).find('.loading').show();

            var formData = new FormData(this);
            $.ajax({
                url: 'components/com_resourcehumaine/controleurs/router.php?task=editAbsenceResourceHumaine',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(theResponse) {
                    var code = parseInt(theResponse);
                    if (code === 1) {
                        $('#justifyAbsenceModal').modal('hide');
                        $('.pointage-web-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Absence mise à jour.</strong><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
                        filterPointageWeb($('input[name="month_web"]').val());
                    } else {
                        $form.find('.msgbox').html('<div class="alert alert-danger" role="alert">Erreur lors de la mise à jour. Vérifiez les champs.</div>');
                    }
                },
                error: function() {
                    $form.find('.msgbox').html('<div class="alert alert-danger" role="alert">La requête a échoué.</div>');
                },
                complete: function() {
                    $btn.prop('disabled', false).find('.loading').hide();
                }
            });
        });
    });
</script>
