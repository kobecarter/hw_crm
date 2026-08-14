<?php
/**
 * Shell dédié à l'espace self-service employé (com_elogin). Remplace top.php +
 * sidebar.php uniquement quand $_SESSION['user'] est un resourcehumaine
 * (voir index.php racine) — n'affecte jamais le CRM admin. Ouvre le document
 * <html>...<body> et UN SEUL div non refermé ("main-wrapper", même
 * convention que top.php) : header et nav sont des blocs complets
 * auto-refermés, chaque vue employé (profile.php, profile_absences.php, ...)
 * ouvre et referme elle-même son propre <main class="emp-main">, exactement
 * comme les vues admin ouvrent/referment leur .page-wrapper. bottom.php
 * (commun aux deux espaces) referme ce main-wrapper.
 */
$resourcehumaine = $_SESSION['user'];
$currentTask = isset($_GET['task']) ? $_GET['task'] : 'dashboard';
$pendingRequestsCount = 0;
foreach (request::findAllByResourcehumaine($resourcehumaine->getId()) as $uneDemande) {
	if ($uneDemande->getStatus() == 0) {
		$pendingRequestsCount++;
	}
}
$empPhoto = $resourcehumaine->getPhoto() ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "images/default-image.jpeg";
// Logo de l'agence à laquelle l'employé est rattaché (même motif que includes/tpl/top.php pour
// l'admin) plutôt que le logo générique de l'app - repli sur ce dernier si l'agence n'a pas de
// logo personnalisé.
$empAgency = $resourcehumaine->getAgency();
$empLogoPath = ($empAgency && $empAgency->getLogo()) ? "images/agences/" . $empAgency->getLogo() : "assets/img/logo.png";
$empAgencyName = $empAgency ? ($empAgency->getNom() ?: $empAgency->getRaisonSocial()) : '';
$empNavItems = array(
	array('key' => 'dashboard', 'url' => 'index.php', 'label' => 'Tableau de bord', 'icon' => 'fa-home', 'badge' => 0),
	array('key' => 'pointageweb', 'url' => 'index.php?task=pointageweb', 'label' => 'Pointage', 'icon' => 'fa-clock', 'badge' => 0),
	array('key' => 'absences', 'url' => 'index.php?task=absences', 'label' => 'Absences', 'icon' => 'fa-umbrella-beach', 'badge' => 0),
	array('key' => 'files', 'url' => 'index.php?task=files', 'label' => 'Fichiers', 'icon' => 'fa-folder-open', 'badge' => 0),
	array('key' => 'payslips', 'url' => 'index.php?task=payslips', 'label' => 'Bulletins', 'icon' => 'fa-file-invoice-dollar', 'badge' => 0),
	array('key' => 'bonuses', 'url' => 'index.php?task=bonuses', 'label' => 'Bonus', 'icon' => 'fa-gift', 'badge' => 0),
	array('key' => 'requests', 'url' => 'index.php?task=requests', 'label' => 'Demandes', 'icon' => 'fa-comments', 'badge' => $pendingRequestsCount),
	array('key' => 'parrainage', 'url' => 'index.php?task=parrainage', 'label' => 'Parrainage', 'icon' => 'fa-handshake', 'badge' => 0),
);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>Espace Employé — <?= htmlspecialchars($resourcehumaine->getFirstName()) ?></title>

	<!-- Thème sombre : même mécanique anti-flash que top.php. Pose aussi .emp-anim (masque
	     l'entrée GSAP via CSS, voir assets/css/employee-space.css) avant le premier paint pour
	     qu'assets/js/employee-space.js n'ait plus qu'à révéler - sans ce class ajouté ici en
	     synchrone, le contenu s'affichait un instant avant que le script (chargé après le HTML)
	     ne le masque pour rejouer l'entrée, d'où l'effet "ça s'affiche puis disparaît". -->
	<script>
		(function () {
			document.documentElement.classList.add('emp-anim');
			try {
				if (localStorage.getItem('crmTheme') === 'dark') {
					document.documentElement.setAttribute('data-theme', 'dark');
				}
			} catch (e) {}
		})();
	</script>

	<link rel="shortcut icon" href="assets/img/favicon.png">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="assets/css/modern-theme.css">
	<link rel="stylesheet" href="assets/css/employee-space.css">

	<script src="assets/js/jquery-3.5.1.min.js"></script>
	<script src="assets/js/jquery.form.js"></script>
	<!-- employee-space.js s'exécute au DOMContentLoaded : par ce moment-là, gsap et
	     modern-theme.js (chargés en fin de includes/tpl/bottom.php, commun aux deux
	     espaces) ont déjà fini de s'exécuter puisque ce sont des <script> synchrones
	     du même document. -->
	<script src="assets/js/employee-space.js"></script>
</head>

<body>

	<div class="emp-bg" aria-hidden="true">
		<span class="emp-bg-blob emp-bg-blob-1"></span>
		<span class="emp-bg-blob emp-bg-blob-2"></span>
		<span class="emp-bg-blob emp-bg-blob-3"></span>
	</div>

	<div class="main-wrapper employee-space">

		<header class="emp-header">
			<div class="emp-header-inner">
				<a href="index.php" class="emp-brand">
					<img src="<?= htmlspecialchars($empLogoPath) ?>" alt="Logo" onerror="this.src='assets/img/logo.png'">
					<span class="emp-brand-label">Espace Employé</span>
					<?php if ($empAgencyName) : ?>
						<span class="emp-brand-badge"><?= htmlspecialchars($empAgencyName) ?></span>
					<?php endif; ?>
				</a>
				<div class="emp-header-actions">
					<button type="button" class="emp-icon-btn" id="themeToggleBtn" title="Thème sombre">
						<i class="fa fa-moon" id="themeToggleIcon"></i>
					</button>
					<?php /* com_users&task=myProfile est une page admin (attend un objet "user", pas
				"resourcehumaine") - le profil employé, c'est son propre tableau de bord. */ ?>
				<a href="index.php" class="emp-user-chip" title="Mon profil">
						<img class="emp-user-avatar" src="<?= htmlspecialchars($empPhoto) ?>" onerror="this.src='images/default-image.jpeg'" alt="">
						<span class="emp-user-name"><?= htmlspecialchars($resourcehumaine->getFirstName()) ?></span>
					</a>
					<button type="button" class="emp-icon-btn elogout" title="Déconnexion">
						<i class="fa fa-sign-out-alt"></i>
					</button>
				</div>
			</div>
		</header>

		<div class="emp-nav-wrap">
			<nav class="emp-nav">
				<span class="emp-nav-indicator"></span>
				<?php foreach ($empNavItems as $item) : ?>
					<a href="<?= $item['url'] ?>" class="emp-nav-link<?= $currentTask === $item['key'] ? ' active' : '' ?>">
						<i class="fa <?= $item['icon'] ?>"></i>
						<span><?= $item['label'] ?></span>
						<?php if ($item['badge'] > 0) : ?>
							<span class="emp-nav-badge"><?= $item['badge'] ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
