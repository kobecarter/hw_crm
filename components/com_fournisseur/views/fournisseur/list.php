<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Fournisseurs</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Fournisseurs</li>
					</ul>
				</div>
				<div class="col-auto">
				    <?php if ($_SESSION['user']->hasDroit('add', 'com_fournisseur')) :?>
    					<a href="index.php?option=com_fournisseur&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter fournisseur">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
					<?php if($_SESSION['user']->hasDroit('view', 'com_fournisseur')) :?>
					<a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer par année / Exporter">
						<i class="fas fa-filter"></i>
					</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<?php
		// $fournisseurs est calculé une seule fois ici et réutilisé par la grille de cartes et les
		// KPI/puces de catégorie, sans requête SQL supplémentaire.
		$fournisseurs = fournisseur::findAll();

		$nbTotal = count($fournisseurs);
		$nbActifs = 0;
		$nbEnAttente = 0;
		$nbNouveauxCeMois = 0;
		$moisCourant = (int) date('n');
		$anneeCourante = (int) date('Y');
		$compteursCategories = array();

		foreach ($fournisseurs as $f) {
			if ($f->isActive()) {
				$nbActifs++;
			}
			if (!$f->isValide()) {
				$nbEnAttente++;
			}
			$dateAjout = $f->getDateAdd();
			if ($dateAjout && (int) date('n', strtotime($dateAjout)) === $moisCourant && (int) date('Y', strtotime($dateAjout)) === $anneeCourante) {
				$nbNouveauxCeMois++;
			}
			$cat = $f->getCategorie();
			if ($cat) {
				$compteursCategories[$cat] = isset($compteursCategories[$cat]) ? $compteursCategories[$cat] + 1 : 1;
			}
		}

		arsort($compteursCategories);
		$categoriesLabels = getCategorieFournisseur();
		?>

		<div class="row mb-4">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="fa fa-truck"></i></span>
							<div class="dash-count">
								<div class="dash-title">Total fournisseurs</div>
								<div class="dash-counts"><p><?= $nbTotal ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-check-circle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Actifs</div>
								<div class="dash-counts"><p><?= $nbActifs ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0 <?= $nbEnAttente > 0 ? 'kpi-blink' : '' ?>">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1"><i class="fa fa-hourglass-half"></i></span>
							<div class="dash-count">
								<div class="dash-title">En attente de validation</div>
								<div class="dash-counts"><p><?= $nbEnAttente ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-4"><i class="fa fa-star"></i></span>
							<div class="dash-count">
								<div class="dash-title">Nouveaux ce mois</div>
								<div class="dash-counts"><p><?= $nbNouveauxCeMois ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Filtre année / export - repliée par défaut, ouverte par le bouton "entonnoir" de l'en-tête
		     (mécanique déjà partagée par toute l'app, cf. assets/js/script.js #filter_search). -->
		<div id="filter_inputs" class="card filter-card">
			<div class="card-body pb-0">
				<form method="post" action="components/com_fournisseur/controleurs/router.php?task=filterFournisseur" id="filterFournisseur">
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label>Année</label>
								<input type="text" class="form-control" required name="year" id="year" value="<?php if (isset($_GET['year'])) echo $_GET['year']; ?>">
							</div>
						</div>
						<div class="col-sm-6 col-md-3 mb-4 mb-sm-0 mt-0 mt-sm-4 pt-0 pt-sm-2">
							<button type="submit" name="" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>
							<a href="#0" class="btn btn-primary export"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /Filtre année / export -->

		<div class="row">
			<div class="col-sm-12">
				<div class="card fournisseur-toolbar mb-3">
					<div class="card-body">
						<div class="fournisseur-toolbar-row">
							<div class="fournisseur-recherche">
								<i class="fa fa-search"></i>
								<input type="text" id="fournisseurRecherche" placeholder="Rechercher un fournisseur (nom, société, téléphone, email)...">
							</div>
							<div class="fournisseur-compteur-resultats"><span id="fournisseurCompteurVisible"><?= $nbTotal ?></span> fournisseur<?= $nbTotal > 1 ? 's' : '' ?></div>
							<div class="fournisseur-vue-switch" role="group" aria-label="Mode d'affichage">
								<button type="button" class="fournisseur-vue-btn active" data-vue="boite" data-toggle="tooltip" title="Vue boîtes"><i class="fa fa-th-large"></i></button>
								<button type="button" class="fournisseur-vue-btn" data-vue="liste" data-toggle="tooltip" title="Vue liste"><i class="fa fa-list"></i></button>
							</div>
						</div>
						<div class="fournisseur-pills">
							<button type="button" class="fournisseur-pill active" data-categorie="tous">
								<i class="fa fa-layer-group"></i> Tous <span class="fournisseur-pill-count"><?= $nbTotal ?></span>
							</button>
							<?php foreach ($compteursCategories as $catId => $nb) :?>
								<?php if (!isset($categoriesLabels[$catId])) { continue; } ?>
								<button type="button" class="fournisseur-pill" data-categorie="<?= (int) $catId ?>">
									<i class="fa <?= getCategorieFournisseurIcone($catId) ?>"></i> <?= htmlspecialchars($categoriesLabels[$catId]) ?> <span class="fournisseur-pill-count"><?= $nb ?></span>
								</button>
							<?php endforeach;?>
						</div>
					</div>
				</div>

				<div class="col msgbox mt-0 mb-3 px-0"></div>

				<div class="list-box">
					<?php include __DIR__ . '/_grid.php'; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->

<script type="text/javascript">
$(function () {

	// ---- Entrée en cascade + effet "3D" au survol (perspective + rotation selon la position du
	// curseur) - jamais lourd : transform CSS uniquement, désactivé si l'utilisateur préfère moins
	// de mouvement (prefers-reduced-motion) via la classe posée sur <html> ailleurs dans l'app. ---
	if (typeof gsap !== 'undefined') {
		// stagger en "amount" (budget total réparti sur toutes les cartes) plutôt qu'un délai fixe
		// par carte : avec un délai fixe (ex: 0.04s/carte), la cascade dépassait déjà 4-5 secondes
		// à 100+ fournisseurs - ici l'entrée entière reste sous la seconde quel que soit leur nombre.
		gsap.from('.fournisseur-card', { opacity: 0, y: 16, scale: 0.97, duration: 0.35, stagger: { amount: 0.5, from: 'start' }, ease: 'power2.out', clearProps: 'all' });
	}

	var reduitMouvement = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (!reduitMouvement) {
		$(document).on('mousemove', '.fournisseur-card-tilt', function (e) {
			var rect = this.getBoundingClientRect();
			var px = (e.clientX - rect.left) / rect.width - 0.5;
			var py = (e.clientY - rect.top) / rect.height - 0.5;
			this.style.transform = 'perspective(700px) rotateY(' + (px * 8).toFixed(2) + 'deg) rotateX(' + (py * -8).toFixed(2) + 'deg) translateZ(6px)';
		});
		$(document).on('mouseleave', '.fournisseur-card-tilt', function () {
			this.style.transform = '';
		});
	}

	// ---- Vue boîtes / vue liste : purement CSS (même gabarit de carte, `.list-box` porte juste
	// la classe qui bascule la mise en page) - le choix est mémorisé pour la prochaine visite. ----
	function appliquerVue(vue) {
		$('.list-box').toggleClass('fournisseur-vue-liste', vue === 'liste');
		$('.fournisseur-vue-btn').removeClass('active').filter('[data-vue="' + vue + '"]').addClass('active');
	}
	appliquerVue(localStorage.getItem('fournisseurVue') || 'boite');
	$(document).on('click', '.fournisseur-vue-btn', function () {
		var vue = $(this).data('vue');
		localStorage.setItem('fournisseurVue', vue);
		appliquerVue(vue);
	});

	// ---- Recherche + filtre catégorie : purement côté client, instantané (les données sont déjà
	// dans le DOM) - pas d'aller-retour serveur pour une simple recherche par nom/téléphone/email. ----
	var categorieActive = 'tous';

	function appliquerFiltres() {
		var terme = $('#fournisseurRecherche').val().toLowerCase().trim();
		var visibles = 0;
		// Regroupé par catégorie : une puce de catégorie masque le groupe entier (pas seulement
		// ses cartes), la recherche ne masque que les cartes qui ne correspondent pas - un groupe
		// qui se retrouve sans aucune carte visible après recherche disparaît lui aussi.
		$('#fournisseurGrid > .fournisseur-groupe').each(function () {
			var $groupe = $(this);
			var okCategorieGroupe = categorieActive === 'tous' || $groupe.data('categorie-groupe') == categorieActive;
			var visiblesDansGroupe = 0;
			$groupe.find('.fournisseur-card').each(function () {
				var $carte = $(this);
				var okRecherche = terme === '' || ($carte.data('recherche') || '').toString().indexOf(terme) !== -1;
				var visible = okCategorieGroupe && okRecherche;
				$carte.toggleClass('d-none', !visible);
				if (visible) { visiblesDansGroupe++; }
			});
			$groupe.toggleClass('d-none', visiblesDansGroupe === 0);
			visibles += visiblesDansGroupe;
		});
		$('#fournisseurCompteurVisible').text(visibles);
		$('#fournisseurGrilleVide').toggleClass('d-none', visibles !== 0);
	}

	$(document).on('input', '#fournisseurRecherche', appliquerFiltres);

	$(document).on('click', '.fournisseur-pill', function () {
		$('.fournisseur-pill').removeClass('active');
		$(this).addClass('active');
		categorieActive = $(this).data('categorie');
		appliquerFiltres();
	});

	// ---- Activer / désactiver directement depuis la carte (bascule visuelle immédiate, pas de
	// rechargement de page) ------------------------------------------------------------------
	$(document).on('click', '.fournisseur-toggle-actif', function () {
		var $btn = $(this);
		var id = $btn.data('id');
		var state = $btn.data('state');
		$.post('components/com_fournisseur/controleurs/router.php?task=enableFournisseur', { id: id, state: state }, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				var $carte = $btn.closest('.fournisseur-card');
				if (state === 'oui') {
					$btn.attr('data-state', 'non').attr('data-original-title', 'Inactif — cliquer pour activer');
					$carte.addClass('is-inactive').attr('data-actif', 0);
				} else {
					$btn.attr('data-state', 'oui').attr('data-original-title', 'Actif — cliquer pour désactiver');
					$carte.removeClass('is-inactive').attr('data-actif', 1);
				}
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> Échec de la mise à jour du statut.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			}
		});
	});

	$(document).on('click', '.fournisseur-supprimer', function () {
		var $btn = $(this);
		if (!confirm('Supprimer définitivement ce fournisseur ?')) { return; }
		var id = $btn.data('id');
		$.post('components/com_fournisseur/controleurs/router.php?task=deleteFournisseur', { id: id }, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				var $carte = $btn.closest('.fournisseur-card');
				if (typeof gsap !== 'undefined') {
					gsap.to($carte[0], { opacity: 0, scale: 0.9, duration: 0.25, onComplete: function () { $carte.remove(); appliquerFiltres(); } });
				} else {
					$carte.remove();
					appliquerFiltres();
				}
				$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès !</strong> Fournisseur supprimé.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> Échec de la suppression.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			}
		});
	});

	$(document).on('click', '.export', function (e) {
		e.preventDefault();
		window.open('components/com_fournisseur/controleurs/router.php?task=exportFournisseur&year=' + $('#year').val(), '_blank');
	});

	// Le filtre par année recharge la grille de cartes (même gabarit _card.php côté serveur) -
	// les puces catégorie/recherche continuent de fonctionner sur le nouveau contenu.
	$('form#filterFournisseur').ajaxForm({
		beforeSubmit: function () {
			$('#filterFournisseur .loading').css('display', 'inline-block');
		},
		success: function (theResponse) {
			$('#filterFournisseur .loading').fadeOut();
			$('html, body').animate({ scrollTop: 0 }, 'slow');
			$('.list-box').html(theResponse);
			categorieActive = 'tous';
			$('#fournisseurRecherche').val('');
			appliquerFiltres();
		}
	});
});
</script>
