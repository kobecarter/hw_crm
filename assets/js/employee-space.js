/**
 * Espace Employé — micro-interactions (nav glissante, tilt 3D, jauge congés,
 * entrée GSAP). Chargé uniquement par includes/tpl/employee-top.php, jamais
 * sur les pages admin. Défensif comme modern-theme.js : si GSAP n'a pas pu
 * charger (CDN down), le reste (CSS, formulaires, nav) continue de marcher.
 */
(function () {
	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	ready(function () {
		var hasGsap = typeof gsap !== 'undefined';

		/* ---- Entrée : header -> hero -> nav -> cartes en stagger --------------
		   La classe .emp-anim (posée en tête de <head>, voir employee-top.php) masque ces
		   éléments (opacity:0) AVANT même le premier paint - sans ça, le navigateur peint le
		   contenu visible une frame ou deux avant que ce script (chargé après le HTML) n'ait
		   fini de s'exécuter, puis GSAP le repasse à 0 pour rejouer l'entrée : c'est le "ça
		   s'affiche puis disparaît" observé. Avec le masquage déjà fait en CSS, il n'y a plus
		   qu'à révéler - .fromTo() (jamais .from()) pour ne pas dépendre de l'opacité déjà à 0
		   posée par le CSS. Le reveal() de secours (catch + timeout) garantit que le contenu
		   redevient visible même si GSAP échoue à charger ou lève une erreur en cours de route. */
		var revealSelector = '.emp-header, .emp-nav-wrap, .emp-hero, .emp-main > .emp-grid > *, .emp-main > .emp-card:not(.emp-hero), .emp-main > .emp-page-header';
		var revealed = false;
		function reveal() {
			if (revealed) { return; }
			revealed = true;
			document.documentElement.classList.remove('emp-anim');
			document.querySelectorAll(revealSelector).forEach(function (el) {
				el.style.opacity = '';
			});
		}
		var safetyTimer = setTimeout(reveal, 2000);
		// Retire tout de suite la classe qui masquait via CSS (voir employee-top.php) - le
		// timeline GSAP ci-dessous pose sa propre opacité inline en même temps, donc rien ne
		// devient visible "par erreur" entre les deux (même tick JS, pas de paint entre les deux).
		document.documentElement.classList.remove('emp-anim');

		if (hasGsap) {
			try {
				var tl = gsap.timeline({ defaults: { ease: 'power2.out' }, onComplete: function () { clearTimeout(safetyTimer); revealed = true; } });
				var header = document.querySelector('.emp-header');
				var navWrap = document.querySelector('.emp-nav-wrap');
				var hero = document.querySelector('.emp-hero');
				var cards = document.querySelectorAll('.emp-main > .emp-grid > *, .emp-main > .emp-card:not(.emp-hero), .emp-main > .emp-page-header');

				if (header) { tl.fromTo(header, { opacity: 0, y: -20 }, { opacity: 1, y: 0, duration: 0.4 }); }
				if (navWrap) { tl.fromTo(navWrap, { opacity: 0, y: -10 }, { opacity: 1, y: 0, duration: 0.35 }, '-=0.2'); }
				if (hero) { tl.fromTo(hero, { opacity: 0, y: 16, scale: 0.98 }, { opacity: 1, y: 0, scale: 1, duration: 0.4 }, '-=0.15'); }
				if (cards.length) {
					tl.fromTo(cards, { opacity: 0, y: 18 }, {
						opacity: 1,
						y: 0,
						duration: 0.4,
						clearProps: 'opacity,transform',
						stagger: { amount: Math.min(0.4, cards.length * 0.06), from: 'start' }
					}, '-=0.15');
				}
			} catch (e) {
				clearTimeout(safetyTimer);
				reveal();
			}
		} else {
			clearTimeout(safetyTimer);
			reveal();
		}

		/* ---- Indicateur de nav glissant ---------------------------------- */
		var nav = document.querySelector('.emp-nav');
		var indicator = document.querySelector('.emp-nav-indicator');
		function placeIndicator(link, animate) {
			if (!nav || !indicator || !link) { return; }
			var navRect = nav.getBoundingClientRect();
			var linkRect = link.getBoundingClientRect();
			var left = linkRect.left - navRect.left + nav.scrollLeft;
			var vars = { left: left, width: linkRect.width };
			if (hasGsap && animate) {
				gsap.to(indicator, Object.assign({ duration: 0.5, ease: 'elastic.out(1, 0.8)' }, vars));
			} else {
				indicator.style.left = left + 'px';
				indicator.style.width = linkRect.width + 'px';
			}
		}
		if (nav && indicator) {
			var activeLink = nav.querySelector('.emp-nav-link.active');
			// Positionnement immédiat (sans anim) pour éviter un "saut" visible au chargement.
			placeIndicator(activeLink, false);
			window.addEventListener('resize', function () {
				placeIndicator(nav.querySelector('.emp-nav-link.active'), false);
			});
			nav.querySelectorAll('.emp-nav-link').forEach(function (link) {
				link.addEventListener('mouseenter', function () { placeIndicator(link, true); });
			});
			nav.addEventListener('mouseleave', function () {
				placeIndicator(nav.querySelector('.emp-nav-link.active'), true);
			});
		}

		/* ---- Tilt 3D au survol (cartes .emp-card-tilt) --------------------- */
		document.querySelectorAll('.emp-card-tilt').forEach(function (card) {
			card.addEventListener('mousemove', function (e) {
				var rect = card.getBoundingClientRect();
				var px = (e.clientX - rect.left) / rect.width - 0.5;
				var py = (e.clientY - rect.top) / rect.height - 0.5;
				card.style.transform = 'perspective(900px) rotateY(' + (px * 8) + 'deg) rotateX(' + (py * -8) + 'deg) translateZ(6px)';
			});
			card.addEventListener('mouseleave', function () {
				card.style.transform = 'perspective(900px) rotateY(0) rotateX(0) translateZ(0)';
			});
		});

		/* ---- Jauge congés (conic-gradient animée + compteur) --------------- */
		document.querySelectorAll('.emp-gauge').forEach(function (gauge) {
			var pct = parseFloat(gauge.getAttribute('data-percent')) || 0;
			var valueEl = gauge.querySelector('.emp-gauge-value');
			var rawValue = valueEl ? parseFloat(valueEl.getAttribute('data-value')) || 0 : 0;
			requestAnimationFrame(function () {
				gauge.style.setProperty('--pct', Math.max(0, Math.min(100, pct)));
			});
			if (hasGsap && valueEl) {
				var counter = { n: 0 };
				gsap.to(counter, {
					n: rawValue,
					duration: 1.3,
					ease: 'power2.out',
					onUpdate: function () {
						valueEl.textContent = (Number.isInteger(rawValue) ? Math.round(counter.n) : counter.n.toFixed(1));
					}
				});
			}
		});
	});
})();
