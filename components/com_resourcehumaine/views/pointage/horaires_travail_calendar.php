<?php
/**
 * Fragment AJAX injecté dans .horaires-travail-content (voir list.php) par
 * filterHorairesTravail(). Variables reçues : $jours (un par jour du mois, 'horaire' =>
 * pointageweb::getHoraireJour()), $decalageDebut, $mois, $reference (horairereference courant,
 * pour préremplir le formulaire au-dessus du calendrier). Grille CSS 7 colonnes, mêmes classes
 * .jt-grid/.jt-weekday que "Gestion des jours de travail" (styles dans list.php, statique).
 */
$joursSemaineLabels = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
$joursSemaineLabelsLongs = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
?>
<form id="horaireReferenceForm" class="horaire-reference-form">
	<div class="hr-field">
		<label>Début matin</label>
		<input type="time" name="heure_debut_matin" value="<?= substr($reference->getHeureDebutMatin(), 0, 5) ?>" required>
	</div>
	<div class="hr-field">
		<label>Fin matin</label>
		<input type="time" name="heure_fin_matin" value="<?= substr($reference->getHeureFinMatin(), 0, 5) ?>" required>
	</div>
	<div class="hr-field">
		<label>Début après-midi</label>
		<input type="time" name="heure_debut_apresmidi" value="<?= substr($reference->getHeureDebutApresmidi(), 0, 5) ?>" required>
	</div>
	<div class="hr-field">
		<label>Fin après-midi</label>
		<input type="time" name="heure_fin_apresmidi" value="<?= substr($reference->getHeureFinApresmidi(), 0, 5) ?>" required>
	</div>
	<button type="submit" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2 loading" style="display:none;"></span>Enregistrer la référence</button>
</form>

<div class="jt-grid">
	<?php foreach ($joursSemaineLabels as $label) : ?>
		<div class="jt-weekday"><?= $label ?></div>
	<?php endforeach; ?>

	<?php for ($i = 0; $i < $decalageDebut; $i++) : ?>
		<div class="jt-day jt-empty"></div>
	<?php endfor; ?>

	<?php foreach ($jours as $jourInfo) :
		$dateObj = new DateTime($jourInfo['date']);
		$jourSemaineIso = (int) $dateObj->format('N');

		// Dimanche : jamais travaillé, jamais d'horaire à configurer - case neutre, non cliquable,
		// pas d'infobulle. Samedi : jamais d'après-midi (quand il est travaillé, c'est matin
		// uniquement) - l'infobulle et le formulaire d'édition n'exposent que début/fin matin.
		if ($jourSemaineIso === 7) :
	?>
			<div class="jt-day jt-not-applicable" title="Dimanche — jamais travaillé"><?= $jourInfo['jour'] ?></div>
		<?php continue; endif;

		$horaire = $jourInfo['horaire'];
		$isOverride = $horaire['source'] === 'override';
		$estSamedi = $jourSemaineIso === 6;

		$times = array(
			array('fa-hourglass-start', 'Début matin', substr($horaire['heure_debut_matin'], 0, 5)),
			array('fa-mug-hot', 'Fin matin', substr($horaire['heure_fin_matin'], 0, 5)),
		);
		if (!$estSamedi) {
			$times[] = array('fa-utensils', 'Début après-midi', substr($horaire['heure_debut_apresmidi'], 0, 5));
			$times[] = array('fa-hourglass-end', 'Fin après-midi', substr($horaire['heure_fin_apresmidi'], 0, 5));
		}
		$tooltip = array(
			'date' => normaldate($jourInfo['date']),
			'jourSemaine' => $joursSemaineLabelsLongs[$jourSemaineIso - 1],
			'statutLabel' => $isOverride ? 'Horaire personnalisé' : 'Horaire de référence',
			'statutColor' => $isOverride ? '#6366f1' : '#22c55e',
			'extra' => $estSamedi ? 'Samedi — matin uniquement' : '',
			'times' => $times,
		);
	?>
		<div
			class="jt-day jt-horaire-day<?= $isOverride ? ' jt-override' : '' ?>"
			data-tooltip="<?= htmlspecialchars(json_encode($tooltip)) ?>"
			data-date="<?= $jourInfo['date'] ?>"
			data-samedi="<?= $estSamedi ? '1' : '0' ?>"
			data-heure-debut-matin="<?= substr($horaire['heure_debut_matin'], 0, 5) ?>"
			data-heure-fin-matin="<?= substr($horaire['heure_fin_matin'], 0, 5) ?>"
			data-heure-debut-apresmidi="<?= substr($horaire['heure_debut_apresmidi'] ?? '', 0, 5) ?>"
			data-heure-fin-apresmidi="<?= substr($horaire['heure_fin_apresmidi'] ?? '', 0, 5) ?>"
			data-override="<?= $isOverride ? '1' : '0' ?>"
		>
			<?= $jourInfo['jour'] ?>
			<?php if ($isOverride) : ?><i class="fa fa-thumbtack jt-override-icon"></i><?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
