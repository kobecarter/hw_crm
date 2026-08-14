<?php
/**
 * Tableau de bord de l'espace employé (com_elogin). Exclusif à ce contexte (aucune autre
 * vue ne l'inclut) — libre de tout re-templater sans impacter le CRM admin. La logique de
 * calcul des congés (par mois/année depuis la date de début de contrat) est reprise telle
 * quelle de l'ancienne version, seul le rendu change.
 */
$startDate = $resourcehumaine->getStartDate();
$endDate = $resourcehumaine->getEndDate() ? $resourcehumaine->getEndDate() : date("Y-m-d");
$totalHolidays = 0;
$congesParAnnee = array();
for ($annee = date("Y", strtotime($startDate)); $annee <= date("Y", strtotime($endDate)); $annee++) {
	$totalAnnee = 0;
	foreach (months() as $mois) {
		$dateFinMois = date("Y-m-t", strtotime($annee . "-" . $mois['number'] . "-1"));
		$debutMois = date("Y-m-d", strtotime($startDate));
		$finMois = date("Y-m-d", strtotime($endDate));
		if ($dateFinMois >= $debutMois && $dateFinMois <= $finMois) {
			$totalAnnee += 1.5;
			$totalHolidays += 1.5;
		}
	}
	$congesParAnnee[$annee] = $totalAnnee;
}
$congesConsommes = 0;
foreach ($absences as $uneAbsence) {
	$congesConsommes += ($uneAbsence->getNatureOfAbsence() == 1 ? $uneAbsence->getNumberOfDays() : 0);
}
$congesRestants = $totalHolidays - $congesConsommes;
$pctConges = $totalHolidays > 0 ? max(0, min(100, round(($congesRestants / $totalHolidays) * 100))) : 0;

$anciennete = (new DateTime($startDate))->diff(new DateTime());
$anciennteLabel = ($anciennete->y > 0 ? $anciennete->y . ' an' . ($anciennete->y > 1 ? 's' : '') . ' ' : '') . $anciennete->m . ' mois';

$documentsManquantsProfil = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $files);
$dossierComplet = empty($documentsManquantsProfil);

$mesDemandes = request::findAllByResourcehumaine($resourcehumaine->getId());
$demandesEnAttente = 0;
foreach ($mesDemandes as $uneDemande) {
	if ($uneDemande->getStatus() == 0) {
		$demandesEnAttente++;
	}
}

$salaireInitial = floatval($resourcehumaine->getSalaireInitial());
$salaireActuel = floatval($resourcehumaine->getSalaireActuel());
$evolutionSalairePct = $salaireInitial > 0 ? round((($salaireActuel - $salaireInitial) / $salaireInitial) * 100, 1) : 0;
$maxSalaire = max($salaireInitial, $salaireActuel, 1);

$totalCommissionValideeProfil = parrainage::totalCommissionValidee($resourcehumaine->getId());

$statsPointageMoisProfil = pointageweb::calculerStatsMois($resourcehumaine, date('Y-m'));
$absencesNonJustifieesMoisProfil = 0;
foreach (absence::findByDateMonthly($resourcehumaine->getId(), date('Y-m')) as $uneAbsenceMois) {
	if ($uneAbsenceMois->getNatureOfAbsence() == 3) {
		$absencesNonJustifieesMoisProfil += $uneAbsenceMois->getNumberOfDays();
	}
}

$empPhotoProfil = $resourcehumaine->getPhoto() ? "./images/resourceshumaines/" . $resourcehumaine->getPhoto() : "./images/default-image.jpeg";
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title">Bonjour <?= htmlspecialchars($resourcehumaine->getFirstName()) ?> 👋</h1>
		<p class="emp-page-subtitle">Voici un aperçu de votre espace personnel</p>
	</div>

	<div class="emp-card emp-card-tilt emp-hero" style="margin-bottom: 20px;">
		<div class="emp-hero-avatar-frame">
			<img src="<?= htmlspecialchars($empPhotoProfil) ?>" onerror="this.src='./images/default-image.jpeg'" alt="">
		</div>
		<div class="emp-hero-info">
			<h2 class="emp-hero-name"><?= htmlspecialchars($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName()) ?></h2>
			<p class="emp-hero-role"><?= htmlspecialchars($resourcehumaine->getFunction() ?: 'Collaborateur') ?></p>
			<div class="emp-hero-chips">
				<span class="emp-chip"><i class="fa fa-id-badge"></i> <?= htmlspecialchars($resourcehumaine->getReference() ?: 'Matricule non défini') ?></span>
				<span class="emp-chip"><i class="fa fa-envelope"></i> <?= htmlspecialchars($resourcehumaine->getEmail()) ?></span>
				<span class="emp-chip"><i class="fa fa-calendar-check"></i> Depuis le <?= normaldate($startDate) ?: htmlspecialchars($startDate) ?></span>
			</div>
		</div>
		<a href="index.php?task=myProfileEdit" class="emp-btn-mini" style="width:auto;padding:0 16px;height:38px;position:relative;z-index:1;flex-shrink:0;" title="Modifier mon profil">
			<i class="fa fa-user-edit mr-1"></i> Modifier
		</a>
	</div>

	<div class="emp-grid emp-kpi-grid" style="margin-bottom: 20px;">
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-gauge" data-percent="<?= $pctConges ?>">
				<div class="emp-gauge-inner">
					<span class="emp-gauge-value" data-value="<?= $congesRestants ?>">0</span>
					<span class="emp-gauge-unit">Jours</span>
				</div>
			</div>
			<div>
				<div class="emp-kpi-label">Congés restants</div>
				<div style="font-size:.75rem;color:var(--emp-ink-soft);margin-top:2px;"><?= $congesConsommes ?> pris sur <?= $totalHolidays ?></div>
			</div>
		</div>
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-kpi-icon kpi-2"><i class="fa fa-briefcase"></i></div>
			<div>
				<div class="emp-kpi-value"><?= $anciennteLabel ?></div>
				<div class="emp-kpi-label">Ancienneté</div>
			</div>
		</div>
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-kpi-icon kpi-3"><i class="fa fa-folder-open"></i></div>
			<div>
				<div class="emp-kpi-value"><?= $dossierComplet ? '✓' : count($documentsManquantsProfil) ?></div>
				<div class="emp-kpi-label"><?= $dossierComplet ? 'Dossier complet' : 'document(s) manquant(s)' ?></div>
			</div>
		</div>
		<a href="index.php?task=parrainage" class="emp-card emp-card-tilt emp-kpi-card" style="text-decoration:none;">
			<div class="emp-kpi-icon kpi-2"><i class="fa fa-handshake"></i></div>
			<div>
				<div class="emp-kpi-value"><?= number_format($totalCommissionValideeProfil, 0, ',', ' ') ?> MAD</div>
				<div class="emp-kpi-label">Commission de parrainage</div>
			</div>
		</a>
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-kpi-icon kpi-3"><i class="fa fa-clock"></i></div>
			<div>
				<div class="emp-kpi-value"><?= floor($statsPointageMoisProfil['retard_minutes'] / 60) ?>h<?= str_pad($statsPointageMoisProfil['retard_minutes'] % 60, 2, '0', STR_PAD_LEFT) ?></div>
				<div class="emp-kpi-label">Retard ce mois-ci</div>
			</div>
		</div>
		<div class="emp-card emp-card-tilt emp-kpi-card">
			<div class="emp-kpi-icon kpi-4"><i class="fa fa-comments"></i></div>
			<div>
				<div class="emp-kpi-value"><?= $demandesEnAttente ?></div>
				<div class="emp-kpi-label">Demande(s) en attente</div>
			</div>
		</div>
		<a href="index.php?task=absences" class="emp-card emp-card-tilt emp-kpi-card" style="text-decoration:none;">
			<div class="emp-kpi-icon kpi-5"><i class="fa fa-user-clock"></i></div>
			<div>
				<div class="emp-kpi-value"><?= $absencesNonJustifieesMoisProfil ?></div>
				<div class="emp-kpi-label">Jour(s) d'absence ce mois-ci</div>
			</div>
		</a>
	</div>

	<?php if ($salaireInitial > 0 || $salaireActuel > 0) : ?>
	<div class="emp-card" style="margin-bottom: 20px;">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-chart-line"></i> Évolution du salaire</h3>
			<?php if ($evolutionSalairePct != 0) : ?>
				<span class="emp-badge <?= $evolutionSalairePct > 0 ? 'emp-badge-green' : 'emp-badge-red' ?>">
					<i class="fa fa-arrow-<?= $evolutionSalairePct > 0 ? 'up' : 'down' ?> mr-1"></i><?= abs($evolutionSalairePct) ?> %
				</span>
			<?php endif; ?>
		</div>
		<div class="emp-salary-row">
			<span class="emp-salary-label">Salaire initial</span>
			<div class="emp-salary-track"><div class="emp-salary-fill" style="width:<?= round(($salaireInitial / $maxSalaire) * 100) ?>%;"></div></div>
			<span class="emp-salary-value"><?= number_format($salaireInitial, 0, ',', ' ') ?> DH</span>
		</div>
		<div class="emp-salary-row">
			<span class="emp-salary-label">Salaire actuel</span>
			<div class="emp-salary-track"><div class="emp-salary-fill is-current" style="width:<?= round(($salaireActuel / $maxSalaire) * 100) ?>%;"></div></div>
			<span class="emp-salary-value"><?= number_format($salaireActuel, 0, ',', ' ') ?> DH</span>
		</div>
	</div>
	<?php endif; ?>

	<div class="emp-card" style="margin-bottom: 20px;">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-bolt"></i> Accès rapide</h3>
		</div>
		<div class="emp-grid emp-quicklinks">
			<a href="index.php?task=absences" class="emp-card emp-card-tilt emp-quicklink-card">
				<div class="emp-quicklink-icon"><i class="fa fa-umbrella-beach"></i></div>
				<span class="emp-quicklink-label">Absences & congés</span>
				<i class="fa fa-arrow-right emp-quicklink-arrow"></i>
			</a>
			<a href="index.php?task=files" class="emp-card emp-card-tilt emp-quicklink-card">
				<div class="emp-quicklink-icon"><i class="fa fa-folder-open"></i></div>
				<span class="emp-quicklink-label">Mes fichiers</span>
				<i class="fa fa-arrow-right emp-quicklink-arrow"></i>
			</a>
			<a href="index.php?task=payslips" class="emp-card emp-card-tilt emp-quicklink-card">
				<div class="emp-quicklink-icon"><i class="fa fa-file-invoice-dollar"></i></div>
				<span class="emp-quicklink-label">Bulletins de paie</span>
				<i class="fa fa-arrow-right emp-quicklink-arrow"></i>
			</a>
			<a href="index.php?task=bonuses" class="emp-card emp-card-tilt emp-quicklink-card">
				<div class="emp-quicklink-icon"><i class="fa fa-gift"></i></div>
				<span class="emp-quicklink-label">Mes bonus</span>
				<i class="fa fa-arrow-right emp-quicklink-arrow"></i>
			</a>
			<a href="index.php?task=requests" class="emp-card emp-card-tilt emp-quicklink-card">
				<div class="emp-quicklink-icon"><i class="fa fa-comments"></i></div>
				<span class="emp-quicklink-label">Mes demandes</span>
				<i class="fa fa-arrow-right emp-quicklink-arrow"></i>
			</a>
			<a href="index.php?task=parrainage" class="emp-card emp-card-tilt emp-quicklink-card">
				<div class="emp-quicklink-icon"><i class="fa fa-handshake"></i></div>
				<span class="emp-quicklink-label">Parrainage client</span>
				<i class="fa fa-arrow-right emp-quicklink-arrow"></i>
			</a>
		</div>
	</div>

	<div class="emp-card">
		<div class="emp-card-header">
			<h3 class="emp-card-title"><i class="fa fa-chart-pie"></i> Détail des congés par année</h3>
		</div>
		<div class="table-responsive">
			<table class="table table-sm text-center mb-0">
				<thead>
					<tr>
						<th class="text-left text-secondary">Année</th>
						<?php foreach (months() as $mois) : ?>
							<th><?= $mois['name'] ?></th>
						<?php endforeach; ?>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($annee = date("Y", strtotime($startDate)); $annee <= date("Y", strtotime($endDate)); $annee++) : ?>
						<tr>
							<td class="text-left"><b><?= $annee ?></b></td>
							<?php foreach (months() as $mois) :
								$dateFinMois = date("Y-m-t", strtotime($annee . "-" . $mois['number'] . "-1"));
								$debutMois = date("Y-m-d", strtotime($startDate));
								$finMois = date("Y-m-d", strtotime($endDate));
								$acquis = ($dateFinMois >= $debutMois && $dateFinMois <= $finMois);
							?>
								<td><?= $acquis ? '<span class="emp-badge emp-badge-green">1.5</span>' : '<span style="color:var(--emp-ink-soft)">0</span>' ?></td>
							<?php endforeach; ?>
							<td><span class="emp-badge emp-badge-purple"><?= $congesParAnnee[$annee] ?></span></td>
						</tr>
					<?php endfor; ?>
				</tbody>
			</table>
		</div>
	</div>

</main>
