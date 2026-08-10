/**
 * Thème sombre + mode confidentialité (flou des montants). Fichier autonome (pas de
 * dépendance GSAP/jQuery) : ces deux actions doivent rester utilisables même si un CDN externe
 * tombe - même raison que assets/js/global-search.js.
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
		// --- Thème sombre ---
		var themeBtn = document.getElementById('themeToggleBtn');
		var themeIcon = document.getElementById('themeToggleIcon');

		function applyThemeIcon() {
			if (!themeIcon) { return; }
			var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
			themeIcon.classList.toggle('fa-moon', !isDark);
			themeIcon.classList.toggle('fa-sun', isDark);
			if (themeBtn) {
				themeBtn.setAttribute('title', isDark ? 'Thème clair' : 'Thème sombre');
			}
		}
		applyThemeIcon();

		if (themeBtn) {
			themeBtn.addEventListener('click', function () {
				var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
				if (isDark) {
					document.documentElement.removeAttribute('data-theme');
					try { localStorage.setItem('crmTheme', 'light'); } catch (e) {}
				} else {
	document.documentElement.setAttribute('data-theme', 'dark');
					try { localStorage.setItem('crmTheme', 'dark'); } catch (e) {}
				}
				applyThemeIcon();
				// Les graphiques ApexCharts déjà rendus gardent la largeur de leur premier rendu -
				// un simple changement de classe CSS ne les fait pas se redimensionner. La plupart
				// des libs de graphiques (dont ApexCharts) écoutent déjà "resize" pour se redessiner
				// responsive - un évènement synthétique suffit, pas besoin de connaître chaque
				// instance de graphique par son nom.
				window.dispatchEvent(new Event('resize'));
			});
		}

		// --- Mode confidentialité (flou des montants) ---
		var privacyBtn = document.getElementById('privacyToggleBtn');
		var privacyIcon = document.getElementById('privacyToggleIcon');

		function applyPrivacyIcon() {
			if (!privacyIcon) { return; }
			var isHidden = document.documentElement.classList.contains('privacy-mode');
			privacyIcon.classList.toggle('fa-eye', !isHidden);
			privacyIcon.classList.toggle('fa-eye-slash', isHidden);
			if (privacyBtn) {
				privacyBtn.setAttribute('title', isHidden ? 'Afficher les montants' : 'Masquer les montants');
			}
		}

		try {
			if (localStorage.getItem('crmPrivacy') === 'on') {
				document.documentElement.classList.add('privacy-mode');
			}
		} catch (e) {}
		applyPrivacyIcon();

		if (privacyBtn) {
			privacyBtn.addEventListener('click', function () {
				var isHidden = document.documentElement.classList.toggle('privacy-mode');
				try { localStorage.setItem('crmPrivacy', isHidden ? 'on' : 'off'); } catch (e) {}
				applyPrivacyIcon();
			});
		}
	});
})();
