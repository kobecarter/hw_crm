/**
 * Recherche globale du bandeau haut (.top-nav-search) : requête debouncée vers
 * components/com_search/controleurs/router.php, résultats groupés par module affichés dans un
 * panneau sous le champ. Fonctionne indépendamment de GSAP (contrairement à modern-theme.js) -
 * la recherche ne doit jamais dépendre du chargement réussi d'un CDN d'animation.
 */
(function ($) {
	if (!$) {
		return;
	}

	function escHtml(s) {
		return $('<div>').text(s === undefined || s === null ? '' : s).html();
	}

	$(function () {
		var $input = $('.top-nav-search input').first();
		if (!$input.length) {
			return;
		}
		var $wrap = $input.closest('.top-nav-search');
		$wrap.css('position', 'relative');
		$input.closest('form').on('submit', function (e) { e.preventDefault(); });

		var $panel = $('<div class="global-search-panel"></div>').appendTo($wrap);
		var timer = null;
		var xhr = null;

		function fermer() {
			$panel.removeClass('show');
		}

		function lancerRecherche(terme, toutesAgences) {
			if (xhr) {
				xhr.abort();
			}
			xhr = $.get('components/com_search/controleurs/router.php', { task: 'rechercheGlobale', q: terme, toutesAgences: toutesAgences ? 1 : 0 }, function (response) {
				if (response && response.success) {
					rendre(response.groupes, terme, toutesAgences, !!response.aucunResultatExact);
				}
			}, 'json');
		}

		function rendre(groupes, terme, toutesAgences, aucunResultatExact) {
			var cles = Object.keys(groupes || {});
			if (!cles.length) {
				if (!toutesAgences) {
					$panel.html('<div class="global-search-vide">Aucun résultat pour « ' + escHtml(terme) + ' » dans cette agence.</div>'
						+ '<button type="button" class="global-search-autres-agences"><i class="fa fa-globe"></i> Chercher aussi dans les autres agences</button>').addClass('show');
					$panel.find('.global-search-autres-agences').on('click', function () {
						$panel.html('<div class="global-search-vide"><i class="fa fa-spinner fa-spin"></i> Recherche dans toutes les agences…</div>').addClass('show');
						lancerRecherche(terme, true);
					});
				} else {
					$panel.html('<div class="global-search-vide">Aucun résultat pour « ' + escHtml(terme) + ' », même dans les autres agences.</div>').addClass('show');
				}
				return;
			}
			var html = '';
			if (toutesAgences) {
				html += '<div class="global-search-toutes-agences-note"><i class="fa fa-globe"></i> Résultats de toutes les agences</div>';
			} else if (aucunResultatExact) {
				html += '<div class="global-search-toutes-agences-note"><i class="fa fa-lightbulb"></i> Aucun résultat exact pour « ' + escHtml(terme) + ' » - suggestions ci-dessous</div>';
			}
			cles.forEach(function (cle) {
				var groupe = groupes[cle];
				var classeGroupe = 'global-search-groupe' + (cle === 'suggestions' ? ' global-search-groupe-suggestions' : '');
				html += '<div class="' + classeGroupe + '"><div class="global-search-groupe-titre"><i class="fa ' + groupe.icon + '"></i>' + escHtml(groupe.label) + '</div>';
				(groupe.items || []).forEach(function (item) {
					var attrAgenceId = item.agence_id ? ' data-agence-id="' + escHtml(item.agence_id) + '"' : '';
					html += '<a href="' + escHtml(item.url) + '" class="global-search-item"' + attrAgenceId + '>'
						+ '<span class="global-search-item-titre">' + escHtml(item.titre) + '</span>'
						+ (item.sous_titre ? '<span class="global-search-item-sous">' + escHtml(item.sous_titre) + '</span>' : '')
						+ (item.agence ? '<span class="global-search-item-agence"><i class="fa fa-map-marker-alt"></i>' + escHtml(item.agence) + '</span>' : '')
						+ (typeof item.actif !== 'undefined' ? '<span class="global-search-item-statut global-search-item-statut-' + (item.actif ? 'actif' : 'inactif') + '"><i class="fa fa-circle"></i> ' + (item.actif ? 'Actif' : 'Inactif') + '</span>' : '')
						+ (item.ca ? '<span class="global-search-item-ca"><i class="fa fa-coins"></i> CA : ' + escHtml(item.ca) + '</span>' : '')
						+ '</a>';
				});
				html += '</div>';
			});
			if (aucunResultatExact && !toutesAgences) {
				html += '<button type="button" class="global-search-autres-agences"><i class="fa fa-globe"></i> Chercher aussi dans les autres agences</button>';
			}
			$panel.html(html).addClass('show');
			// Un résultat "autre agence" (client/facture/devis/charge) pointe vers une fiche
			// filtrée côté serveur par l'agence de session : sans bascule préalable, la page
			// s'ouvre vide malgré un id valide. On bascule donc la session sur la bonne agence
			// (no-op si elle y est déjà, cf. ensureSessionAgence) avant de suivre le lien.
			$panel.find('.global-search-item[data-agence-id]').on('click', function (e) {
				var $lien = $(this);
				var agenceId = $lien.data('agence-id');
				if (typeof ensureSessionAgence !== 'function' || parseInt(agenceId) === parseInt(window.currentSessionAgence)) {
					return;
				}
				e.preventDefault();
				var urlCible = $lien.attr('href');
				$lien.css('opacity', 0.6);
				ensureSessionAgence(agenceId, function () {
					document.location = urlCible;
				});
			});
			$panel.find('.global-search-autres-agences').on('click', function () {
				$panel.html('<div class="global-search-vide"><i class="fa fa-spinner fa-spin"></i> Recherche dans toutes les agences…</div>').addClass('show');
				lancerRecherche(terme, true);
			});
			if (typeof gsap !== 'undefined') {
				gsap.from($panel.find('.global-search-item'), { opacity: 0, y: -4, duration: 0.2, stagger: 0.02, ease: 'power1.out', clearProps: 'all' });
			}
		}

		$input.on('input', function () {
			var terme = $input.val().trim();
			clearTimeout(timer);
			if (terme.length < 2) {
				fermer();
				return;
			}
			timer = setTimeout(function () {
				lancerRecherche(terme, false);
			}, 300);
		});

		$input.on('focus', function () {
			if ($panel.children().length) {
				$panel.addClass('show');
			}
		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest('.top-nav-search').length) {
				fermer();
			}
		});
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape') {
				fermer();
			}
		});
	});
})(window.jQuery);
