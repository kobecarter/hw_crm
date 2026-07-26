<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>CRM ADMINISTRATION VERSE CONCEPT</title>

	<meta name="spradm" content="<?= $_SESSION['user']->isSuperUser() == false ? 'false' : 'true' ?>">>

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

	<!-- FancyBox-->

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


	<!-- Datepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">

	<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->

	<!-- jQuery -->
	<script src="assets/js/jquery-3.5.1.min.js"></script>
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
			<!-- Logo -->
			<div class="header-left">
				<a href="index.php" class="logo">
					<img src="<?php echo $logoPath; ?>" alt="<?php echo $config->getNom(); ?>">
				</a>
				<a href="index.php" class="logo logo-small">
					<img src="<?php echo $logoPath; ?>" alt="<?php echo $config->getNom(); ?>" width="30" height="30">
				</a>
			</div>
			<!-- /Logo -->

			<!-- Sidebar Toggle -->
			<a href="javascript:void(0);" id="toggle_btn">
				<i class="fas fa-bars"></i>
			</a>
			<!-- /Sidebar Toggle -->
			<?php if (!$_SESSION['user']->isResourceHumaine()) : ?>
				<!-- Search -->
				<div class="top-nav-search">
					<form>
						<input type="text" class="form-control" placeholder="Search here">
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
					<!-- Navigation -->
					<li class="nav-item dropdown has-arrow">
						<?php $currentlang = langue::findByCode($_SESSION['langue']); ?>
						<a class="nav-link dropdown-toggle px-0 px-sm-2" data-toggle="dropdown" href="#" role="button">
							<span class="btn btn-sm d-inline-block text-info"><i class="fa fa-bars"></i></span> <span>Navigation</span>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<?php
							$modules = module::findAllPosition("param");
							foreach ($modules as $module) {
								$active = isset($_GET['option']) && $_GET['option'] == $module->getIdModule() ? "active" : "";
								if ($_SESSION['user']->hasDroit('view', $module->getIdModule())) { ?>

									<a class="dropdown-item" href="index.php?option=<?php echo $module->getIdModule(); ?>"><i class=" mr-2 fa fa-<?= $module->getIcon(); ?>"></i> <?php echo $module->getNom(); ?></a>
							<?php }
							} ?>
						</div>
					</li>
					<!-- /Navigation -->

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
								padding: 0 10px;
							}

							.user-menu.nav>li>a>i {
								font-size: 1rem;
							}

							.dropdown-menu.show {
								right: 0;
								left: auto !important;
								transform: translate3d(40px, 60px, 0px) !important;
								max-width: 300px;
							}

						}
					</style>
					<!-- Company -->
					<li class="nav-item dropdown has-arrow company-nav">
						<a class="nav-link dropdown-toggle px-0 px-sm-2" data-toggle="dropdown" href="#" role="button">
							<span class="btn btn-sm d-inline-block text-info"><i class="fa fa-building"></i></span> <span><?= $currentAgence->getNom() ?></span>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<?php $agencesMenu = agence::findAll($_SESSION['langue']); ?>
							<?php foreach ($agencesMenu as $value): ?>
								<a href="javascript:void(0);" class="dropdown-item switch-agence" data-agence="<?= $value->getId() ?>">
									<?= $value->getNom(); ?>
								</a>
							<?php endforeach ?>
						</div>
					</li>
					<!-- /Company -->

					<!-- Flag -->
					<li class="nav-item dropdown has-arrow">
						<?php $currentlang = langue::findByCode($_SESSION['langue']); ?>
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button">
							<img src="assets/img/flags/<?php echo $currentlang->getCode(); ?>.png" alt="" height="20"> <span><?php echo $currentlang->getNom(); ?></span>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
							<?php $langues = langue::findAll(); ?>
							<?php foreach ($langues as $langue): ?>
								<a href="javascript:void(0);" class="dropdown-item switch-lang" data-lang="<?= $langue->getCode() ?>">
									<img src="assets/img/flags/<?php echo $langue->getCode(); ?>.png" alt="" height="16"> <?php echo $langue->getNom(); ?>
								</a>
							<?php endforeach ?>
						</div>
					</li>
					<!-- /Flag -->
				<?php endif; ?>

				<!-- Notifications -->
				<?php include("notification.php"); ?>
				<!-- /Notifications -->

				<!-- User Menu -->
				<li class="nav-item dropdown has-arrow main-drop">
					<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
						<span class="user-img">
							<?php $avatar = $_SESSION['user']->getPhoto() != '' ? ($_SESSION['user']->isResourceHumaine() ? 'images/resourceshumaines/' : 'images/users/') . $_SESSION['user']->getPhoto() : 'assets/img/profiles/avatar-01.jpg'; ?>
							<img src="<?php echo $avatar; ?>" alt="<?php echo $_SESSION['user']->getName(); ?>">
							<span class="status online"></span>
						</span>
						<span><?php echo $_SESSION['user']->getName(); ?></span>
					</a>
					<div class="dropdown-menu">
						<?php if ($_SESSION['user']->hasDroit('view', 'com_user')) : ?>
							<a class="dropdown-item" href="index.php?option=com_users&task=edit&id=<?php echo $_SESSION['user']->getId(); ?>"><i data-feather="user" class="mr-1"></i> Profile</a>
						<?php endif; ?>
						<?php if ($_SESSION['user']->hasDroit('view', 'com_config')) : ?>
							<a class="dropdown-item" href="index.php?option=com_config"><i data-feather="settings" class="mr-1"></i> Paramètres</a>
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