<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>CRM ADMINISTRATION VERSE CONCEPT</title>

	<meta name="spradm" content="<?= $_SESSION['user']->isSuperUser() == false ? 'false' : 'true' ?>">>

	<!-- Thème sombre + mode confidentialité : appliqués avant tout CSS pour éviter un flash
	     (thème clair, ou montants visibles un instant) au chargement -->
	<script>
		(function () {
			try {
				if (localStorage.getItem('crmTheme') === 'dark') {
					document.documentElement.setAttribute('data-theme', 'dark');
				}
				if (localStorage.getItem('crmPrivacy') === 'on') {
					document.documentElement.classList.add('privacy-mode');
				}
			} catch (e) {}
		})();
	</script>

	<!-- Favicon -->
	<link rel="shortcut icon" href="assets/img/favicon.png">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
	<link rel="stylesheet" href="assets/plugins/chosen/chosen.css">

	<!-- Datatables CSS -->
	<link rel="stylesheet" href="assets/plugins/datatables/datatables.min.css">

	<?php if (!isset($_GET['option']) || in_array($_GET['option'], array('com_dashboard', 'com_holiday'))): ?>
	<!-- FullCalendar (calendrier des jours fériés) -->
	<link rel="stylesheet" href="assets/plugins/fullcalendar/fullcalendar.min.css">
	<?php endif; ?>

	<!-- FancyBox-->

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


	<!-- Datepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="<?= assetVersion('assets/css/style.css') ?>">

	<!-- Modern Theme (surcouche additive : ne modifie aucune structure/fichier de vue) -->
	<link rel="stylesheet" href="<?= assetVersion('assets/css/modern-theme.css') ?>">

	<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

	<!-- jQuery -->
	<script src="assets/js/jquery-3.5.1.min.js"></script>
	<script>var currentSessionAgence = <?php echo intval($_SESSION['agence']); ?>;</script>
	<script>
		// Options FullCalendar en français, réutilisées par le widget dashboard et la
		// page com_holiday (le plugin bundled n'a pas de fichier de locale fr). Défini
		// ici (avant le contenu de la page) pour être disponible dès que ces vues
		// initialisent leur calendrier.
		window.fullCalendarFrDefaults = {
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			monthNamesShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
			dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
			dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
			buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', day: 'Jour', list: 'Liste' },
			firstDay: 1
		};
	</script>
	<!-- Ajax form JS -->
	<script src="assets/js/jquery.form.js"></script>
	<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
	<?php $currentAgence = agence::find($_SESSION['agence'], $_SESSION['langue']); ?>
	<style>
		.sidebar-menu li.active>a {
			background-color: <?= $currentAgence->getColor() . "30"; ?>;
			color: <?= $currentAgence->getColor(); ?>;
			position: relative;
		}

		.sidebar-menu li a:hover {
			color: <?= $currentAgence->getColor(); ?>;
		}

		.sidebar-menu>ul>li>a:hover {
			background-color: <?= $currentAgence->getColor() . "30"; ?>;
			color: <?= $currentAgence->getColor(); ?>;
		}
	</style>
</head>

<body>

	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<!-- Header -->
		<div class="header">
			<?php $logoPath = $currentAgence->getLogo()  ? 'images/agences/' . $currentAgence->getLogo() : 'assets/img/logo.png'; ?>
			<!-- Logo (burger juste à côté, même chip "verre" que le reste du bandeau) -->
			<div class="header-left">
				<a href="javascript:void(0);" id="toggle_btn">
					<i class="fas fa-bars"></i>
				</a>
				<a href="index.php" class="logo">
					<img src="<?php echo $logoPath; ?>" alt="<?php echo $config->getNom(); ?>">
				</a>
				<a href="index.php" class="logo logo-small">
					<img src="<?php echo $logoPath; ?>" alt="<?php echo $config->getNom(); ?>" width="30" height="30">
				</a>
			</div>
			<!-- /Logo -->

			<?php if (!$_SESSION['user']->isResourceHumaine()) : ?>
				<?php
					$prenomUtilisateur = htmlspecialchars(ucfirst(strtolower($_SESSION['user']->getPrenom())));
					// Comparé sur l'email/login plutôt que le prénom seul (plus fiable - un prénom
					// peut être vide/mal saisi) : "hiba@helloworld..." couvre le login réel qu'il
					// s'agisse de l'adresse complète ou d'un préfixe.
					$identifiantUtilisateur = strtolower(trim($_SESSION['user']->getEmail() . ' ' . $_SESSION['user']->getLogin() . ' ' . $_SESSION['user']->getPrenom()));
					$estHiba = strpos($identifiantUtilisateur, 'hiba@helloworld') !== false || strpos($identifiantUtilisateur, 'hiba') === 0;
					$placeholderRecherche = "Hey $prenomUtilisateur, un nom, une facture, une référence... on s'occupe du reste ✨";
				?>
				<!-- Search - centrée façon Gemini (voir .top-nav-search en position absolute dans
				     modern-theme.css, indépendant de la mise en page en float du reste du header).
				     La salutation ("Hey [Prénom]...") vit désormais DANS le placeholder de la
				     recherche plutôt que dans un bloc de texte séparé à côté - un seul élément au
				     lieu de deux qui se disputaient la place sur les écrans moyens. -->
				<div class="top-nav-search<?= $estHiba ? ' top-nav-search-hiba' : '' ?>">
					<form>
						<input type="text" class="form-control" placeholder="<?= htmlspecialchars($placeholderRecherche) ?>">
						<button class="btn" type="submit"><i class="fas fa-search"></i></button>
					</form>
				</div>
				<!-- /Search -->
			<?php endif; ?>

			<!-- Mobile Menu Toggle -->
			<a class="mobile_btn" id="mobile_btn">
				<i class="fas fa-bars"></i>
			</a>
			<!-- /Mobile Menu Toggle -->

			<!-- Header Menu -->
			<ul class="nav user-menu">
				<?php if (!$_SESSION['user']->isResourceHumaine()) : ?>
					<style>
						@media (max-width: 767.98px) {

							.user-menu.nav>li.company-nav>a>span.btn {
								display: none;
							}

							.header-left .logo.logo-small {
								position: absolute;
								left: 50px;
							}

							.header .header-left .logo img {
								max-height: 30px;
							}

							.user-menu.nav>li>a {
								padding: 0;
							}

							.user-menu.nav>li>a>i {
								font-size: 1rem;
							}

							/* Ancien calcul "right:0 + translate3d(40px, 60px, 0)" supposait implicitement
							   que le menu ouvert était toujours le MÊME <li> (le plus à droite) - correct
							   pour l'avatar, mais pour company-nav/langue (les premiers <li> du groupe,
							   donc les plus à GAUCHE) "right:0" les ancre déjà à leur PROPRE bord droit
							   (proche du bord gauche de l'écran), et +40px ne suffit pas à compenser : le
							   menu partait hors écran (jusqu'à -154px), recouvrant le titre de page en
							   dessous. Remplacé par un positionnement fixe par rapport au VIEWPORT (pas
							   au déclencheur) - un seul calcul, jamais hors écran quel que soit le <li>
							   qui l'a ouvert.

							   Ce bloc CSS ne suffit cependant pas seul : Popper.js (moteur de
							   positionnement des dropdowns Bootstrap) pose son PROPRE positionnement en
							   style inline ("position:absolute; transform:translate3d(Xpx,Ypx,0)"), et
							   quand ce transform se retrouve en désaccord avec le "position:fixed" imposé
							   ici, Popper le recalcule en boucle sur la position déjà faussée par le
							   transform précédent - le menu finit à un x aléatoire selon le moment
							   observé, jamais x=12 attendu, MÊME si getComputedStyle().transform répond
							   bien "none" (la cascade CSS gagne "sur le papier", mais Popper réécrit son
							   inline juste après). Fix réel : "data-display=static" sur chaque déclencheur
							   data-toggle="dropdown" (top.php + notification.php) - dit à Bootstrap de ne
							   PAS invoquer Popper du tout, seul ce CSS positionne alors le menu. */
							.dropdown-menu.show {
								position: fixed !important;
								/* 66px (.header, même hauteur qu'en desktop - les icônes défilent
								   horizontalement sur une ligne au lieu de s'enrouler, voir .user-menu
								   dans assets/css/style.css) + 8px de marge. */
								top: 74px !important;
								left: 12px !important;
								right: 12px !important;
								transform: none !important;
								/* "width:auto" ne s'étire PAS jusqu'à combler l'espace entre left/right
								   ici (constaté : reste à 200px, la valeur de ".user-menu .dropdown-menu
								   {min-width:200px}" - .notifications déborde alors hors écran) - calc()
								   explicite plutôt que de compter sur cette résolution automatique. */
								width: calc(100vw - 24px) !important;
								max-width: none !important;
							}

						}
					</style>
					<!-- Company - icône drapeau (Maroc/Émirats) plutôt qu'un bâtiment générique : aucun
					     champ pays sur l'agence, cf. agenceFlagEmoji() dans functions.php. -->
					<li class="nav-item dropdown has-arrow company-nav">
						<a class="nav-link dropdown-toggle px-0 px-sm-2" data-toggle="dropdown" data-display="static" href="#" role="button">
							<span class="btn btn-sm d-inline-block flag-emoji-btn"><?= agenceFlagEmoji($currentAgence->getId()) ?></span> <span><?= $currentAgence->getNom() ?></span>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<?php $agencesMenu = agence::findAll($_SESSION['langue']); ?>
							<?php foreach ($agencesMenu as $value): ?>
								<a href="javascript:void(0);" class="dropdown-item switch-agence" data-agence="<?= $value->getId() ?>">
									<span class="flag-emoji-item"><?= agenceFlagEmoji($value->getId()) ?></span> <?= $value->getNom(); ?>
								</a>
							<?php endforeach ?>
						</div>
					</li>
					<!-- /Company -->

					<!-- Flag - emoji plutôt que les images images/langues/*.png (inexistantes sur le
					     disque, chemin relatif erroné - 404 sur chaque page) - cf. langueFlagEmoji(). -->
					<li class="nav-item dropdown has-arrow">
						<?php $currentlang = langue::findByCode($_SESSION['langue']); ?>
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" data-display="static" href="#" role="button">
							<span class="flag-emoji-btn"><?= langueFlagEmoji($currentlang->getCode()) ?></span> <span><?php echo $currentlang->getNom(); ?></span>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<?php $langues = langue::findAll(); ?>
							<?php foreach ($langues as $langue): ?>
								<a href="javascript:void(0);" class="dropdown-item switch-lang" data-lang="<?= $langue->getCode() ?>">
									<span class="flag-emoji-item"><?= langueFlagEmoji($langue->getCode()) ?></span> <?php echo $langue->getNom(); ?>
								</a>
							<?php endforeach ?>
						</div>
					</li>
					<!-- /Flag -->
				<?php endif; ?>

				<!-- Confidentialité : masque les montants (CA, dettes, recettes, etc. marqués .money-sensitive)
				     partout dans l'app - utile pour un partage d'écran. État mémorisé (localStorage), pas
				     de secret côté serveur : c'est un flou visuel, pas un contrôle d'accès. -->
				<li class="nav-item">
					<a href="javascript:void(0);" class="nav-link" id="privacyToggleBtn" data-toggle="tooltip" title="Masquer les montants">
						<i class="fa fa-eye" id="privacyToggleIcon"></i>
					</a>
				</li>
				<!-- /Confidentialité -->

				<!-- Thème sombre -->
				<li class="nav-item">
					<a href="javascript:void(0);" class="nav-link" id="themeToggleBtn" data-toggle="tooltip" title="Thème sombre">
						<i class="fa fa-moon" id="themeToggleIcon"></i>
					</a>
				</li>
				<!-- /Thème sombre -->

				<!-- Notifications -->
				<?php include("notification.php"); ?>
				<!-- /Notifications -->

				<!-- User Menu -->
				<li class="nav-item dropdown has-arrow main-drop">
					<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" data-display="static">
						<span class="user-img">
							<?php $avatar = $_SESSION['user']->getPhoto() != '' ? ($_SESSION['user']->isResourceHumaine() ? 'images/resourceshumaines/' : 'images/users/') . $_SESSION['user']->getPhoto() : 'assets/img/profiles/avatar-01.jpg'; ?>
							<img src="<?php echo $avatar; ?>" alt="<?php echo $_SESSION['user']->getName(); ?>">
							<span class="status online"></span>
						</span>
						<span><?php echo $_SESSION['user']->getName(); ?></span>
					</a>
					<div class="dropdown-menu">
						<?php if (!$_SESSION['user']->isResourceHumaine()) : ?>
							<a class="dropdown-item" href="index.php?option=com_users&task=myProfile"><i data-feather="user" class="mr-1"></i> Profile</a>
						<?php endif; ?>
						<?php if ($_SESSION['user']->hasDroit('view', 'com_config')) : ?>
							<a class="dropdown-item" href="index.php?option=com_config"><i data-feather="settings" class="mr-1"></i> Paramètres</a>
						<?php endif; ?>
						<?php
						// Modules "param" (langues, modules, utilisateurs, expertises, réalisations,
						// témoignages) - anciennement leur propre dropdown "Navigation" dans le bandeau,
						// regroupés ici dans le profil pour désencombrer la barre du haut.
						$modulesConfig = array_values(array_filter(module::findAllPosition("param"), function ($m) {
							// com_config est déjà le lien "Paramètres" juste au-dessus (même page) -
							// jamais listé une seconde fois ici.
							return $m->getIdModule() !== 'com_config' && $_SESSION['user']->hasDroit('view', $m->getIdModule());
						}));
						?>
						<?php if (!empty($modulesConfig)) : ?>
							<div class="dropdown-divider"></div>
							<h6 class="dropdown-header">Configuration</h6>
							<?php foreach ($modulesConfig as $indexModuleConfig => $moduleConfig) : ?>
								<a class="dropdown-item config-menu-item config-menu-item-<?= ($indexModuleConfig % 6) + 1 ?>" href="index.php?option=<?= $moduleConfig->getIdModule(); ?>"><i class="fa fa-<?= $moduleConfig->getIcon(); ?>"></i> <?= $moduleConfig->getNom(); ?></a>
							<?php endforeach; ?>
							<div class="dropdown-divider"></div>
						<?php endif; ?>
						<a class="dropdown-item <?=$_SESSION['user']->isResourceHumaine() ? 'elogout' : 'logout'?>" href="javascript:void(0);"><i data-feather="log-out" class="mr-1"></i> Déconnexion</a>
					</div>
				</li>
				<!-- /User Menu -->

			</ul>
			<!-- /Header Menu -->
			<div style="width:100%;height:100%;<?= "background:" . $currentAgence->getColor() . "30" ?>"></div>
		</div>
		<!-- /Header -->

		<?php if (!$_SESSION['user']->isResourceHumaine()) : ?>
		<!-- Recherche mobile : .top-nav-search disparaît sous 992px (pas la place dans le bandeau
		     réduit aux icônes) - sans équivalent, la recherche était totalement inaccessible au
		     tactile. Ancrée en bas de l'écran (position:fixed, cf. modern-theme.css) pour rester
		     facilement atteignable au pouce. DOIT rester un FRÈRE de .header, jamais un enfant : le
		     "backdrop-filter" du header (glassmorphism) crée un nouveau bloc de confinement pour
		     tout descendant position:fixed (même piège que backdrop-filter/transform/filter/
		     will-change ailleurs dans ce fichier/modern-theme.css) - à l'intérieur de .header, ce
		     bloc ne se positionnait plus par rapport au VIEWPORT mais par rapport aux 66px du
		     header lui-même, recouvrant tout le bandeau (hamburger, drapeaux...) et rendant tout
		     inatteignable au clic. Même classe .top-nav-search que la version desktop (juste un
		     modificateur -mobile en plus) : assets/js/global-search.js s'attache à CHAQUE
		     .top-nav-search de la page, pas seulement au premier trouvé - un seul script pour les
		     deux. -->
		<div class="top-nav-search top-nav-search-mobile">
			<form>
				<input type="text" class="form-control" placeholder="<?= htmlspecialchars($placeholderRecherche) ?>">
				<button class="btn" type="submit"><i class="fas fa-search"></i></button>
			</form>
		</div>
		<!-- /Recherche mobile -->
		<?php endif; ?>