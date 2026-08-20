/**
 * Modern Theme — micro-interactions GSAP, subtiles et génériques.
 * Ne cible que des classes déjà présentes sur toutes les pages : aucune vue
 * de composant n'a besoin d'être modifiée. Tout est défensif (vérifie que
 * gsap et les éléments existent) pour ne jamais casser une page.
 */
(function () {
	if (typeof gsap === 'undefined') {
		return;
	}

	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	ready(function () {
		// Fondu d'entrée doux de la page. clearProps est indispensable ici : sans
		// ça, GSAP laisse un "transform" et un "will-change" inline sur
		// .page-wrapper une fois l'animation finie (même retombés à des valeurs
		// neutres), ce qui crée un nouveau contexte d'empilement CSS et piège
		// tout ce qui est dessous — dont les modales Bootstrap (leur z-index ne
		// peut alors plus jamais dépasser le ".modal-backdrop", qui lui reste
		// hors de ce contexte : clics bloqués sur tout le formulaire modal).
		var $wrapper = document.querySelector('.page-wrapper');
		if ($wrapper) {
			gsap.from($wrapper, {
				opacity: 0,
				y: 8,
				duration: 0.45,
				ease: 'power2.out',
				clearProps: 'all'
			});
		}

		// Apparition en cascade légère des lignes de tableau (une seule fois par tableau,
		// limité aux premières lignes pour rester discret sur les grandes listes).
		// clearProps : les lignes contiennent souvent des menus déroulants
		// (.dropdown-action) dont le z-index doit pouvoir dépasser les lignes
		// voisines — un transform inline résiduel sur la <tr> le piégerait.
		document.querySelectorAll('.table tbody').forEach(function (tbody) {
			var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr')).slice(0, 25);
			if (rows.length) {
				gsap.from(rows, {
					opacity: 0,
					y: 6,
					duration: 0.35,
					stagger: 0.02,
					ease: 'power1.out',
					clearProps: 'all'
				});
			}
		});

		// Léger effet de "lift" au survol des cartes : uniquement l'ombre, pas de
		// translation. Un "y" animé en transform laisse presque toujours un
		// résidu (matrix(...) au lieu de "none") si l'animation est interrompue
		// par un survol suivant trop rapide (clearProps ne se déclenche qu'à la
		// fin d'un tween qui va au bout) — et un ".card" contient très souvent
		// une modale ou un menu déroulant, dont le z-index se retrouve alors
		// piégé sous tout ce qui est en dehors de la carte (ex: le backdrop
		// d'une modale Bootstrap, bloquant tout clic dans son formulaire).
		document.querySelectorAll('.card').forEach(function (card) {
			var enter = function () {
				gsap.to(card, { boxShadow: '0 12px 28px rgba(31,41,55,0.10)', duration: 0.25, ease: 'power2.out' });
			};
			var leave = function () {
				gsap.to(card, { boxShadow: '0 2px 8px rgba(31,41,55,0.06)', duration: 0.25, ease: 'power2.out', clearProps: 'boxShadow' });
			};
			card.addEventListener('mouseenter', enter);
			card.addEventListener('mouseleave', leave);
		});

		// Tuiles KPI du dashboard (cartes en verre dégradé) : entrée en cascade des
		// icônes + léger effet de brillance au survol, en plus du lift générique des cartes
		var kpiIcons = document.querySelectorAll('.dash-widget-icon');
		if (kpiIcons.length) {
			gsap.from(kpiIcons, {
				scale: 0.5,
				opacity: 0,
				duration: 0.5,
				stagger: 0.08,
				ease: 'back.out(1.7)',
				delay: 0.15,
				clearProps: 'all'
			});
			kpiIcons.forEach(function (icon) {
				var card = icon.closest('.card');
				if (!card) return;
				card.addEventListener('mouseenter', function () {
					gsap.to(icon, { scale: 1.12, duration: 0.25, ease: 'power2.out' });
				});
				card.addEventListener('mouseleave', function () {
					gsap.to(icon, { scale: 1, duration: 0.25, ease: 'power2.out', clearProps: 'transform' });
				});
			});
		}

		// Pages "verre liquide" (.glass-page - dashboard et au-delà) : léger tilt 3D des tuiles KPI
		// selon la position du curseur, même technique que les cartes fournisseurs
		// (.fournisseur-card-tilt) - purement décoratif, ne touche jamais le contenu ni sa
		// lisibilité (rotation plafonnée à 6deg). :has(.dash-widget-icon) plutôt qu'un nom de ligne
		// pour attraper les tuiles KPI de toutes les pages .glass-page, pas seulement le dashboard.
		document.querySelectorAll('.glass-page .card:has(.dash-widget-icon)').forEach(function (card) {
			card.addEventListener('mousemove', function (e) {
				var rect = card.getBoundingClientRect();
				var px = (e.clientX - rect.left) / rect.width - 0.5;
				var py = (e.clientY - rect.top) / rect.height - 0.5;
				card.style.transform = 'perspective(800px) rotateY(' + (px * 6).toFixed(2) + 'deg) rotateX(' + (py * -6).toFixed(2) + 'deg) translateY(-3px)';
			});
			card.addEventListener('mouseleave', function () {
				card.style.transform = '';
			});
		});

		// ---- Liquid Glass : reflet spéculaire qui suit le curseur (--mx/--my) --------------
		// Liste de sélecteurs partagée avec modern-theme.css (.glassEffect()) - modifier les
		// deux ensemble. getBoundingClientRect() à chaque mousemove est volontaire : ces
		// surfaces ne bougent jamais pendant le survol (pas de layout thrash réel), et ça
		// évite de garder un ResizeObserver actif juste pour ce détail cosmétique.
		var GLASS_SHINE_SELECTOR = '.header-left, .glass-page .card, .alert-center-modal .modal-content, .login-wrapper .loginbox, .btn-primary, .top-nav-search .btn, .sidebar-menu li a';
		document.addEventListener('mousemove', function (e) {
			var el = e.target.closest ? e.target.closest(GLASS_SHINE_SELECTOR) : null;
			if (!el) return;
			var rect = el.getBoundingClientRect();
			if (!rect.width || !rect.height) return;
			el.style.setProperty('--mx', (((e.clientX - rect.left) / rect.width) * 100).toFixed(1) + '%');
			el.style.setProperty('--my', (((e.clientY - rect.top) / rect.height) * 100).toFixed(1) + '%');
		}, { passive: true });

		// Petit effet "press" au clic des boutons
		document.querySelectorAll('.btn').forEach(function (btn) {
			btn.addEventListener('mousedown', function () {
				gsap.to(btn, { scale: 0.96, duration: 0.1, ease: 'power1.out' });
			});
			['mouseup', 'mouseleave'].forEach(function (evt) {
				btn.addEventListener(evt, function () {
					gsap.to(btn, { scale: 1, duration: 0.15, ease: 'power1.out', clearProps: 'transform' });
				});
			});
		});

		// Transition d'entrée douce des modals Bootstrap (en plus du fade natif)
		if (window.jQuery) {
			jQuery(document).on('show.bs.modal', '.modal', function () {
				// Une modale écrite en profondeur dans le HTML (ex: dans un formulaire com_devis,
				// lui-même dans .card > .content > .page-wrapper) reste à cet endroit du DOM même
				// une fois "affichée" - Bootstrap ne fait que basculer des classes, il ne déplace
				// jamais l'élément. Si UN SEUL ancêtre crée un nouveau contexte d'empilement CSS
				// (transform, filter, backdrop-filter, isolation, will-change, opacity<1...), le
				// z-index de la modale reste piégé sous tout ce qui est hors de cet ancêtre - dont
				// le ".modal-backdrop", lui ajouté directement à <body> par Bootstrap : overlay
				// visible, popup invisible ou injoignable au clic. Trois cas connus de ce piège ont
				// déjà été corrigés au cas par cas dans ce fichier/modern-theme.css
				// (.glass-page .card + backdrop-filter/isolation, .page-wrapper + will-change) ;
				// plutôt que d'en corriger un quatrième au cas par cas, on supprime la classe de
				// bug entière une fois pour toutes en sortant SYSTÉMATIQUEMENT toute modale de son
				// emplacement d'origine vers un enfant direct de <body> avant son ouverture - elle
				// ne peut alors plus jamais être piégée par un ancêtre, quel qu'il soit.
				if (this.parentNode !== document.body) {
					document.body.appendChild(this);
				}
				var content = this.querySelector('.modal-content');
				if (content) {
					gsap.fromTo(content, { opacity: 0, y: 12, scale: 0.98 }, { opacity: 1, y: 0, scale: 1, duration: 0.3, ease: 'power2.out', clearProps: 'all' });
				}
			});

			// ---- Centre d'alertes : recherche + puces de filtre + groupes repliables -----------
			// Tout est déjà dans le DOM (rendu PHP unique par chargement de page, voir
			// includes/tpl/bottom.php) - recherche/puces ne font QUE masquer/afficher via classe
			// .d-none, jamais retirer d'élément : aucune alerte ne peut disparaître par erreur.
			function alertCenterAppliquerFiltres($modal) {
				var texte = ($.trim($modal.find('#alertCenterSearch').val() || '')).toLowerCase();
				var filtreActif = $modal.find('.alert-center-chip.active').data('filtre') || 'tout';
				var totalVisible = 0;

				$modal.find('.alert-center-groupe').each(function () {
					var $groupe = $(this);
					var groupeCle = String($groupe.data('groupe'));
					var groupeCorrespondFiltre = (filtreActif === 'tout') || (filtreActif === 'urgent') || (filtreActif === groupeCle);
					var groupeAAuMoinsUnItemVisible = false;

					$groupe.find('.alert-center-item').each(function () {
						var $item = $(this);
						var estUrgent = $item.hasClass('alert-center-item-danger');
						var correspondTexte = !texte || (String($item.data('texte') || '')).indexOf(texte) !== -1;
						var correspondFiltre = groupeCorrespondFiltre && (filtreActif !== 'urgent' || estUrgent);
						var visible = correspondTexte && correspondFiltre;
						$item.toggleClass('d-none', !visible);
						if (visible) { groupeAAuMoinsUnItemVisible = true; totalVisible++; }
					});

					$groupe.toggleClass('d-none', !groupeAAuMoinsUnItemVisible);
				});

				$modal.find('#alertCenterEmptyFiltre').toggleClass('d-none', totalVisible > 0);
			}

			jQuery(document).on('input', '#alertCenterSearch', function () {
				alertCenterAppliquerFiltres(jQuery(this).closest('.modal'));
			});

			jQuery(document).on('click', '.alert-center-chip', function () {
				var $chip = jQuery(this);
				$chip.closest('.alert-center-chips').find('.alert-center-chip').removeClass('active');
				$chip.addClass('active');
				alertCenterAppliquerFiltres($chip.closest('.modal'));
			});

			// Un groupe déjà passé en revue peut être replié pour dégager de la place à l'écran -
			// jamais replié par défaut à l'ouverture (voir show.bs.modal ci-dessous) : la priorité
			// reste de tout montrer d'emblée, le repli n'est qu'un confort optionnel de l'utilisateur.
			jQuery(document).on('click', '[data-toggle-groupe]', function () {
				var $groupe = jQuery(this).closest('.alert-center-groupe');
				$groupe.toggleClass('alert-center-groupe-collapsed');
				$groupe.find('.alert-center-liste').stop(true, true).slideToggle(200);
			});

			jQuery(document).on('show.bs.modal', '#alertCenterModal', function () {
				var $modal = jQuery(this);
				$modal.find('#alertCenterSearch').val('');
				$modal.find('.alert-center-chip').removeClass('active');
				$modal.find('.alert-center-chip[data-filtre="tout"]').addClass('active');
				$modal.find('.alert-center-groupe').removeClass('alert-center-groupe-collapsed');
				$modal.find('.alert-center-liste').show();
				alertCenterAppliquerFiltres($modal);
			});

			// Entrée en cascade : les CARTES de groupe (unité visuelle désormais, plutôt que chaque
			// ligne individuellement - un panneau à plusieurs dizaines d'items au total resterait
			// lisible mais l'animation ligne-par-ligne serait superflue/plus lente à percevoir), puis
			// les puces de filtre juste avant, plus rapide et plus discret.
			jQuery(document).on('shown.bs.modal', '#alertCenterModal', function () {
				var groupes = this.querySelectorAll('.alert-center-groupe');
				if (groupes.length) {
					gsap.from(groupes, { opacity: 0, y: 10, duration: 0.35, stagger: { amount: 0.3, from: 'start' }, ease: 'power2.out', clearProps: 'all' });
				}
				var chips = this.querySelectorAll('.alert-center-chip');
				if (chips.length) {
					gsap.from(chips, { opacity: 0, scale: 0.85, duration: 0.25, stagger: { amount: 0.2, from: 'start' }, ease: 'back.out(1.7)', clearProps: 'all' });
				}
			});
		}

		// ---- Sidebar "Meniscus" : entrée en cascade + bead lumineux qui glisse -------------
		// jusqu'au lien actif/survolé. Le bead est créé ici (jamais dans sidebar.php) pour
		// rester fidèle au principe de ce fichier : aucune vue n'a besoin d'être modifiée.
		var sidebarMenu = document.getElementById('sidebar-menu');
		if (sidebarMenu) {
			var topLevelLinks = sidebarMenu.querySelectorAll('#sidebar-menu > ul > li > a');
			if (topLevelLinks.length) {
				gsap.from(topLevelLinks, {
					opacity: 0,
					x: -12,
					duration: 0.4,
					stagger: { amount: 0.4, from: 'start' },
					ease: 'power2.out',
					clearProps: 'all'
				});
			}

			var bead = document.createElement('div');
			bead.className = 'sidebar-bead';
			sidebarMenu.appendChild(bead);

			function sidebarActiveLink() {
				// Le lien actif le plus profond (sous-item ouvert) prime sur l'item de premier niveau.
				return sidebarMenu.querySelector('ul ul li.active > a')
					|| sidebarMenu.querySelector('#sidebar-menu > ul > li.active > a');
			}

			function positionBead(el, animate) {
				if (!el) {
					gsap.to(bead, { opacity: 0, duration: 0.2, overwrite: true });
					return;
				}
				var menuRect = sidebarMenu.getBoundingClientRect();
				var itemRect = el.getBoundingClientRect();
				var top = itemRect.top - menuRect.top + sidebarMenu.scrollTop;
				var vars = { top: top, height: itemRect.height, opacity: 1, overwrite: true };
				if (animate) {
					gsap.to(bead, Object.assign({ duration: 0.5, ease: 'elastic.out(1, 0.75)' }, vars));
				} else {
					gsap.set(bead, vars);
				}
			}

			positionBead(sidebarActiveLink(), false);

			sidebarMenu.querySelectorAll('a').forEach(function (a) {
				a.addEventListener('mouseenter', function () { positionBead(a, true); });
			});
			sidebarMenu.addEventListener('mouseleave', function () { positionBead(sidebarActiveLink(), true); });

			// L'ouverture/fermeture d'un sous-menu (slideDown/slideUp, script.js) déplace tout ce
			// qui suit - on repositionne une fois l'animation de hauteur terminée, et on fait
			// apparaître les sous-items en cascade plutôt que d'un bloc (script.js ne gère que
			// display/hauteur, jamais l'opacité).
			sidebarMenu.querySelectorAll('#sidebar-menu > ul > li.submenu > a').forEach(function (a) {
				a.addEventListener('click', function () {
					var submenuUl = a.nextElementSibling;
					setTimeout(function () {
						positionBead(sidebarActiveLink(), true);
						if (submenuUl && a.classList.contains('subdrop')) {
							gsap.from(submenuUl.querySelectorAll('li'), {
								opacity: 0,
								x: -8,
								duration: 0.3,
								stagger: 0.04,
								ease: 'power2.out',
								clearProps: 'all'
							});
						}
					}, 350);
				});
			});

			window.addEventListener('resize', function () {
				positionBead(sidebarActiveLink(), false);
			});
		}
	});
})();
