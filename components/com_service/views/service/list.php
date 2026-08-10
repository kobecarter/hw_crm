<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Services</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Services</li>
					</ul>
				</div>
				<div class="col-auto">
					<a href="index.php?option=com_service<?= $tousLesServices ? '' : '&tous=1' ?>" class="btn btn-white mr-1" data-toggle="tooltip" data-placement="top" data-original-title="<?= $tousLesServices ? 'Masquer les services inactifs' : 'Afficher aussi les services inactifs' ?>">
						<i class="fa fa-<?= $tousLesServices ? 'eye-slash' : 'eye' ?>"></i> <?= $tousLesServices ? 'Masquer les inactifs' : 'Voir les inactifs' ?>
					</a>
					<?php if ($_SESSION['user']->hasDroit('add', 'com_service')) :?>
						<a href="index.php?option=com_service&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter service">
							<i class="fas fa-plus"></i>
						</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<!-- KPIs -->
		<div class="row">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="fa fa-check-circle"></i></span>
							<div class="dash-count">
								<div class="dash-title">Services actifs</div>
								<div class="dash-counts"><p><?= $kpiServicesActifs ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-4"><i class="fa fa-box-open"></i></span>
							<div class="dash-count">
								<div class="dash-title">Packs actifs</div>
								<div class="dash-counts"><p><?= $kpiPacksActifs ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-th-large"></i></span>
							<div class="dash-count">
								<div class="dash-title">Catégories représentées</div>
								<div class="dash-counts"><p><?= count($kpiCategoriesRepresentees) ?> / <?= count($categoriesService) ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill mb-0">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-9"><i class="fa fa-file-invoice-dollar"></i></span>
							<div class="dash-count">
								<div class="dash-title">CA en devis (12 derniers mois)</div>
								<div class="dash-counts"><p><?= number_format($kpiCaDevis, 0, ',', ' ') ?> MAD</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Barre de recherche + filtre par catégorie -->
		<div class="row">
			<div class="col-md-12">
				<div class="card fournisseur-toolbar">
					<div class="card-body">
						<div class="fournisseur-toolbar-row">
							<div class="fournisseur-recherche">
								<i class="fa fa-search"></i>
								<input type="text" id="serviceRecherche" placeholder="Rechercher un service ou un pack...">
							</div>
							<div class="fournisseur-compteur-resultats"><span id="serviceCompteur"><?= count($services) ?></span> résultat(s)</div>
						</div>
						<div class="fournisseur-pills" id="servicePills">
							<button type="button" class="fournisseur-pill active" data-categorie="tous">
								<i class="fa fa-border-all"></i> Toutes catégories <span class="fournisseur-pill-count"><?= count($services) ?></span>
							</button>
							<?php foreach ($categoriesService as $cat) :
								$nb = 0;
								foreach ($services as $s) { if ($s->getCategorie() && $s->getCategorie()->getId() == $cat->getId()) { $nb++; } }
								if ($nb == 0) { continue; }
							?>
								<button type="button" class="fournisseur-pill" data-categorie="<?= (int) $cat->getId() ?>">
									<?= htmlspecialchars($cat->getTitre()) ?> <span class="fournisseur-pill-count"><?= $nb ?></span>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		$servicesActifsSimples = array();
		$servicesPacks = array();
		foreach ($services as $s) {
			if ($s->isPack()) {
				$servicesPacks[] = $s;
			} else {
				$servicesActifsSimples[] = $s;
			}
		}
		?>

		<div class="row">
			<div class="col-sm-12">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title"><i class="fa fa-box-open mr-2"></i>Packs (<?= count($servicesPacks) ?>)</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div id="servicePacksGrid">
							<?php $listeServices = $servicesPacks; $messageVideGrille = 'Aucun pack dans ce groupe.'; include __DIR__ . '/_grid.php'; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title"><i class="fa fa-list mr-2"></i>Services (<?= count($servicesActifsSimples) ?>)</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div id="serviceActifsGrid">
							<?php $listeServices = $servicesActifsSimples; $messageVideGrille = 'Aucun service dans ce groupe.'; include __DIR__ . '/_grid.php'; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<!-- /Page Wrapper -->
<script type="text/javascript">
$(function () {

	// ---- Entrée en cascade + effet "3D" au survol - même mécanique que la page Fournisseurs
	// (stagger en "amount" plutôt qu'un délai fixe par carte, désactivé si prefers-reduced-motion). ----
	if (typeof gsap !== 'undefined') {
		gsap.from('.service-card', { opacity: 0, y: 16, scale: 0.97, duration: 0.35, stagger: { amount: 0.5, from: 'start' }, ease: 'power2.out', clearProps: 'all' });
	}
	var reduitMouvementService = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (!reduitMouvementService) {
		$(document).on('mousemove', '.service-card-tilt', function (e) {
			var rect = this.getBoundingClientRect();
			var px = (e.clientX - rect.left) / rect.width - 0.5;
			var py = (e.clientY - rect.top) / rect.height - 0.5;
			this.style.transform = 'perspective(700px) rotateY(' + (px * 8).toFixed(2) + 'deg) rotateX(' + (py * -8).toFixed(2) + 'deg) translateZ(6px)';
		});
		$(document).on('mouseleave', '.service-card-tilt', function () {
			this.style.transform = '';
		});
	}

	// Recherche + filtre par catégorie, purement côté client - même idiome que la page
	// Fournisseurs : une puce masque le groupe entier, la recherche ne masque que les cartes qui
	// ne correspondent pas, et un groupe sans carte visible disparaît lui aussi. Les deux grilles
	// (Packs / Services) sont filtrées ensemble par la même barre de recherche.
	var categorieActive = 'tous';
	function appliquerFiltres() {
		var terme = $('#serviceRecherche').val().toLowerCase().trim();
		var visibles = 0;
		$('#servicePacksGrid .service-groupe, #serviceActifsGrid .service-groupe').each(function () {
			var $groupe = $(this);
			var okCategorieGroupe = categorieActive === 'tous' || $groupe.data('categorie-groupe') == categorieActive;
			var visiblesDansGroupe = 0;
			$groupe.find('.service-card').each(function () {
				var $carte = $(this);
				var okRecherche = terme === '' || ($carte.data('recherche') || '').toString().indexOf(terme) !== -1;
				var visible = okCategorieGroupe && okRecherche;
				$carte.toggleClass('d-none', !visible);
				if (visible) { visiblesDansGroupe++; }
			});
			$groupe.toggleClass('d-none', visiblesDansGroupe === 0);
			visibles += visiblesDansGroupe;
		});
		$('#serviceCompteur').text(visibles);
	}
	$('#serviceRecherche').on('input', appliquerFiltres);
	$(document).on('click', '#servicePills .fournisseur-pill', function () {
		$('#servicePills .fournisseur-pill').removeClass('active');
		$(this).addClass('active');
		categorieActive = $(this).data('categorie');
		appliquerFiltres();
	});

	// ---- Activer / désactiver directement depuis la carte, sans rechargement de page ----------
	$(document).on('click', '.service-toggle-actif', function () {
		var $btn = $(this);
		var id = $btn.data('id');
		var state = $btn.data('state');
		$.post('components/com_service/controleurs/router.php?task=enableService', { id: id, state: state }, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				var $carte = $btn.closest('.service-card');
				if (state === 'oui') {
					$btn.attr('data-state', 'non').attr('data-original-title', 'Inactif — cliquer pour activer');
					$carte.addClass('is-inactive');
				} else {
					$btn.attr('data-state', 'oui').attr('data-original-title', 'Actif — cliquer pour désactiver');
					$carte.removeClass('is-inactive');
				}
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la mise à jour du statut<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});

	$(document).on('click', '.service-supprimer', function () {
		var $btn = $(this);
		if (!confirm('Etes-vous sure !')) { return; }
		var id = $btn.data('id');
		$.post('components/com_service/controleurs/router.php?task=deleteService', { id: id }, function (theResponse) {
			if (parseInt(theResponse) === 1) {
				var $carte = $btn.closest('.service-card');
				if (typeof gsap !== 'undefined') {
					gsap.to($carte[0], { opacity: 0, scale: 0.9, duration: 0.25, onComplete: function () { $carte.remove(); appliquerFiltres(); } });
				} else {
					$carte.remove();
					appliquerFiltres();
				}
				$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Service supprimé avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});
});
</script>
