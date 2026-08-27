<?php
/**
 * Fragment AJAX injecté dans .jours-travail-content (voir list.php) par filterJoursTravail().
 * Variables reçues : $jours (un par jour du mois, 'est_travaille'/'override'/'ferie'/'ferie_nom'/
 * 'remark'), $decalageDebut (nb de cases vides avant le 1er pour aligner sur la bonne colonne),
 * $mois. Grille CSS 7 colonnes (styles .jt-grid/.jt-day dans list.php, statique).
 */
$joursSemaineLabels = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
?>
<div class="jt-grid">
	<?php foreach ($joursSemaineLabels as $label) : ?>
		<div class="jt-weekday"><?= $label ?></div>
	<?php endforeach; ?>

	<?php for ($i = 0; $i < $decalageDebut; $i++) : ?>
		<div class="jt-day jt-empty"></div>
	<?php endfor; ?>

	<?php foreach ($jours as $jourInfo) :
		$classes = 'jt-day ' . ($jourInfo['est_travaille'] ? 'jt-travaille' : 'jt-non-travaille');
		if ($jourInfo['override']) { $classes .= ' jt-override'; }
		if ($jourInfo['ferie']) { $classes .= ' jt-ferie'; }
		$title = normaldate($jourInfo['date']) . ' — ' . ($jourInfo['est_travaille'] ? 'Travaillé' : 'Non travaillé');
		if ($jourInfo['override']) {
			$title .= ' (dérogation manuelle)';
		} elseif ($jourInfo['ferie']) {
			$title .= ' (jour férié — ' . $jourInfo['ferie_nom'] . ')';
		} else {
			$title .= ' (règle automatique)';
		}
		if (!empty($jourInfo['remark'])) { $title .= "\n" . $jourInfo['remark']; }
	?>
		<div
			class="<?= $classes ?>"
			title="<?= htmlspecialchars($title) ?>"
			data-date="<?= $jourInfo['date'] ?>"
			data-est-travaille="<?= $jourInfo['est_travaille'] ? '1' : '0' ?>"
			data-override="<?= $jourInfo['override'] ? '1' : '0' ?>"
			data-ferie="<?= $jourInfo['ferie'] ? '1' : '0' ?>"
			data-ferie-nom="<?= htmlspecialchars($jourInfo['ferie_nom']) ?>"
			data-remark="<?= htmlspecialchars($jourInfo['remark']) ?>"
		>
			<?= $jourInfo['jour'] ?>
			<?php if ($jourInfo['override']) : ?><i class="fa fa-thumbtack jt-override-icon"></i>
			<?php elseif ($jourInfo['ferie']) : ?><i class="fa fa-star jt-ferie-icon"></i>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
