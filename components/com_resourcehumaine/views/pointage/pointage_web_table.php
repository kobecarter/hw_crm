<?php
/**
 * Fragment AJAX injecté dans .pointage-web-content (voir list.php) par filterPointageWeb().
 * Variables reçues : $lignes (une par employé actif, même sans pointage ce mois-ci ; chaque
 * ligne porte aussi 'calendrier' => pointageweb::calendrierMois()), $totalMinutesGlobal,
 * $totalJoursGlobal, $totalRetardMinutesGlobal, $totalRetardJoursGlobal, $totalAbsenceJoursGlobal,
 * $mois. Cartes KPI en .dash-widget-icon : habillage verre + tilt 3D +
 * entrée GSAP automatiques via .glass-page (modern-theme.css/js), aucune classe custom à inventer
 * ici. Les styles .cal-day/.cal-legend et le modal de justification vivent dans list.php (statique,
 * pas réinjecté à chaque filtre) - ce fragment ne contient que le balisage qui varie par mois.
 */
$joursLabels = array('L', 'M', 'M', 'J', 'V', 'S', 'D');
$joursLabelsLongs = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
// Habillage de l'infobulle stylée au survol (voir .cal-tooltip dans list.php) - un statut par
// type de jour, réutilisé pour construire le badge coloré + le texte de l'infobulle.
$statutsCalendrier = array(
	'ok' => array('label' => 'À l\'heure', 'color' => '#22c55e'),
	'retard' => array('label' => 'Retard', 'color' => '#f59e0b'),
	'retard_justifie' => array('label' => 'Retard justifié', 'color' => '#16a34a'),
	'absence_non_justifiee' => array('label' => 'Absence non justifiée', 'color' => '#ef4444'),
	'absence_justifiee' => array('label' => 'Absence justifiée', 'color' => '#16a34a'),
	'absence_en_attente' => array('label' => 'Absence pressentie', 'color' => '#ef4444'),
	'conge' => array('label' => 'Congé', 'color' => '#6366f1'),
	'ferie' => array('label' => 'Jour férié', 'color' => '#a855f7'),
	'weekend' => array('label' => 'Non travaillé', 'color' => '#9aa2b1'),
	'futur' => array('label' => 'À venir', 'color' => '#c2c7d1'),
);
?>
<div class="row pointage-kpi-row" style="margin-bottom: 20px;">
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-3"><i class="fa fa-business-time"></i></span>
					<div class="dash-count">
						<div class="dash-title">Heures travaillées</div>
						<div class="dash-counts"><p><?= floor($totalMinutesGlobal / 60) ?>h<?= str_pad($totalMinutesGlobal % 60, 2, '0', STR_PAD_LEFT) ?></p></div>
					</div>
				</div>
				<p class="text-muted mt-3 mb-0">Tous employés, mois sélectionné</p>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-2"><i class="fa fa-calendar-check"></i></span>
					<div class="dash-count">
						<div class="dash-title">Jours pointés</div>
						<div class="dash-counts"><p><?= $totalJoursGlobal ?></p></div>
					</div>
				</div>
				<p class="text-muted mt-3 mb-0">Cumulés, tous employés</p>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-9"><i class="fa fa-clock"></i></span>
					<div class="dash-count">
						<div class="dash-title">Retard total</div>
						<div class="dash-counts"><p><?= floor($totalRetardMinutesGlobal / 60) ?>h<?= str_pad($totalRetardMinutesGlobal % 60, 2, '0', STR_PAD_LEFT) ?></p></div>
					</div>
				</div>
				<p class="text-muted mt-3 mb-0"><?= $totalRetardJoursGlobal ?> jour(s) concerné(s)</p>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-sm-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="dash-widget-header">
					<span class="dash-widget-icon bg-1"><i class="fa fa-user-clock"></i></span>
					<div class="dash-count">
						<div class="dash-title">Absences non justifiées</div>
						<div class="dash-counts"><p><?= $totalAbsenceJoursGlobal ?></p></div>
					</div>
				</div>
				<p class="text-muted mt-3 mb-0">Jours "Non justifié", auto + manuel</p>
			</div>
		</div>
	</div>
</div>

<div class="cal-legend">
	<span><i class="cal-dot cal-ok"></i> À l'heure</span>
	<span><i class="cal-dot cal-retard"></i> Retard <small>(cliquer pour justifier)</small></span>
	<span><i class="cal-dot cal-retard-justifie"></i> Retard justifié</span>
	<span><i class="cal-dot cal-absence-non-justifiee"></i> Absence non justifiée <small>(cliquer pour justifier)</small></span>
	<span><i class="cal-dot cal-absence-justifiee"></i> Absence justifiée</span>
	<span><i class="cal-dot cal-absence-en-attente"></i> Absence pressentie <small>(pas encore traitée par le cron)</small></span>
	<span><i class="cal-dot cal-conge"></i> Congé</span>
	<span><i class="cal-dot cal-ferie"></i> Jour férié</span>
	<span><i class="cal-dot cal-weekend"></i> Non travaillé</span>
</div>

<div class="table-responsive">
	<table class="table mb-0 text-center">
		<thead class="thead-light">
			<tr>
				<th class="text-left">Employé</th>
				<th>Jours pointés</th>
				<th>Heures travaillées</th>
				<th>Arrivée moyenne</th>
				<th>Retard</th>
				<th>Absences</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($lignes)) : ?>
				<tr><td colspan="6" class="text-center text-muted">Aucun employé actif à afficher.</td></tr>
			<?php else : ?>
				<?php foreach ($lignes as $index => $ligne) :
					$empId = $ligne['employe']->getId();
					$calId = 'cal-emp-' . $empId;
				?>
					<tr>
						<td class="text-left">
							<a href="javascript:void(0)" class="cal-toggle" data-toggle="collapse" data-target="#<?= $calId ?>" aria-expanded="false">
								<i class="fa fa-chevron-right cal-toggle-icon mr-1"></i><?= htmlspecialchars($ligne['employe']->getFullName()) ?>
							</a>
						</td>
						<td><?= $ligne['jours'] ?></td>
						<td><?= floor($ligne['minutes'] / 60) ?>h<?= str_pad($ligne['minutes'] % 60, 2, '0', STR_PAD_LEFT) ?></td>
						<td><?= htmlspecialchars($ligne['arrivee_moyenne']) ?></td>
						<td>
							<?php if ($ligne['retard_minutes'] > 0) : ?>
								<span class="badge badge-warning"><?= floor($ligne['retard_minutes'] / 60) ?>h<?= str_pad($ligne['retard_minutes'] % 60, 2, '0', STR_PAD_LEFT) ?> (<?= $ligne['retard_jours'] ?> j)</span>
							<?php else : ?>
								<span class="text-muted">—</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($ligne['absence_jours'] > 0) : ?>
								<span class="badge badge-danger"><?= $ligne['absence_jours'] ?> j</span>
							<?php else : ?>
								<span class="text-muted">—</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr class="collapse cal-row" id="<?= $calId ?>">
						<td colspan="6">
							<div class="cal-strip">
								<?php foreach ($ligne['calendrier'] as $jourInfo) :
									$type = $jourInfo['type'];
									$dateObj = new DateTime($jourInfo['date']);
									$statut = $statutsCalendrier[$type];
									$statutLabel = $statut['label'];
									if ($type === 'retard' || $type === 'retard_justifie') { $statutLabel .= ' — ' . $jourInfo['minutes'] . ' min'; }
									if ($type === 'ferie') { $statutLabel = $jourInfo['label']; }

									$extra = '';
									if (in_array($type, array('absence_non_justifiee', 'absence_justifiee', 'retard_justifie'), true) && !empty($jourInfo['remark'])) {
										$extra = $jourInfo['remark'];
									} elseif ($type === 'absence_en_attente') {
										$extra = 'Pas encore traitée par le cron quotidien.';
									}

									// Les 4 horaires de pointage du jour, s'il y en a - toujours utile au survol,
									// quel que soit le type (même un jour "ok" ou "retard"). Forme générique
									// {icone, libellé, valeur} - réutilisée telle quelle par le calendrier
									// "Horaires de travail" (jours_travail_calendar.php) pour ses propres horaires.
									$times = null;
									if (!empty($jourInfo['pointage'])) {
										$p = $jourInfo['pointage'];
										$times = array(
											array('fa-sign-in-alt', 'Arrivée', $p['arrivee'] ? substr($p['arrivee'], 0, 5) : null),
											array('fa-mug-hot', 'Départ pause', $p['depart_pause'] ? substr($p['depart_pause'], 0, 5) : null),
											array('fa-utensils', 'Retour pause', $p['retour_pause'] ? substr($p['retour_pause'], 0, 5) : null),
											array('fa-sign-out-alt', 'Départ', $p['depart'] ? substr($p['depart'], 0, 5) : null),
										);
									}

									$tooltip = array(
										'date' => normaldate($jourInfo['date']),
										'jourSemaine' => $joursLabelsLongs[((int) $dateObj->format('N')) - 1],
										'statutLabel' => $statutLabel,
										'statutColor' => $statut['color'],
										'extra' => $extra,
										'times' => $times,
									);

									$estJustifiable = $type === 'absence_non_justifiee';
									$estRetardJustifiable = $type === 'retard';
								?>
									<div
										class="cal-day cal-<?= str_replace('_', '-', $type) ?><?= $estJustifiable ? ' cal-justifiable' : '' ?><?= $estRetardJustifiable ? ' cal-retard-justifiable' : '' ?>"
										data-tooltip="<?= htmlspecialchars(json_encode($tooltip)) ?>"
										<?php if ($estJustifiable) : ?>
										data-absence-id="<?= $jourInfo['absence_id'] ?>"
										data-employe-id="<?= $empId ?>"
										data-employe-nom="<?= htmlspecialchars($ligne['employe']->getFullName()) ?>"
										data-date="<?= $jourInfo['date'] ?>"
										data-number-of-days="<?= $jourInfo['number_of_days'] ?>"
										data-remark="<?= htmlspecialchars($jourInfo['remark']) ?>"
										<?php endif; ?>
										<?php if ($estRetardJustifiable) : ?>
										data-employe-id="<?= $empId ?>"
										data-employe-nom="<?= htmlspecialchars($ligne['employe']->getFullName()) ?>"
										data-date="<?= $jourInfo['date'] ?>"
										data-minutes="<?= $jourInfo['minutes'] ?>"
										<?php endif; ?>
									><?= $jourInfo['jour'] ?></div>
								<?php endforeach; ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
