/**
 * Repère et met en évidence une ligne précise d'une liste .datatable à partir du paramètre d'URL
 * ?highlight=ID - posé par le Centre d'alertes (includes/functions/functions.php,
 * getAlertesUrgentes()) sur les liens Rappels/Relances, qui pointaient jusqu'ici vers la liste
 * entière sans jamais désigner l'élément réellement cliqué. Générique : cherche
 * tr[data-highlight-id] plutôt qu'un attribut par composant, réutilisable par toute future liste.
 * Indépendant de GSAP (contrairement à modern-theme.js) - le repérage ne doit jamais dépendre du
 * chargement réussi d'un CDN d'animation. Doit se charger APRES script.js (qui initialise les
 * .datatable) pour que l'instance DataTables existe déjà ici.
 */
(function ($) {
	if (!$) {
		return;
	}

	$(function () {
		var params = new URLSearchParams(window.location.search);
		var highlightId = params.get('highlight');
		if (!highlightId) {
			return;
		}

		var $row = $('tr[data-highlight-id="' + highlightId + '"]');
		if (!$row.length) {
			return;
		}

		var $table = $row.closest('table.datatable');
		if ($table.length && $.fn.DataTable && $.fn.DataTable.isDataTable($table[0])) {
			var table = $table.DataTable();
			// Ordre/filtre RÉELLEMENT appliqués (tri, recherche...) : la ligne peut être sur
			// n'importe quelle page une fois triée/filtrée, pas forcément à sa position d'origine.
			var nodes = table.rows({ search: 'applied', order: 'applied' }).nodes();
			var position = -1;
			for (var i = 0; i < nodes.length; i++) {
				if (nodes[i] === $row[0]) {
					position = i;
					break;
				}
			}
			var pageLength = table.page.len();
			if (position > -1 && pageLength > 0) {
				table.page(Math.floor(position / pageLength)).draw(false);
			}
		}

		// Après le draw() (repagine de façon synchrone mais laisse retomber le DOM) : on scrolle
		// et on marque la ligne comme "sélectionnée". Contrairement à un simple flash, la classe
		// n'est JAMAIS retirée ensuite - la ligne reste visuellement sélectionnée tant que la page
		// n'est pas rechargée/quittée (demande explicite : "le client sur lequel j'ai cliqué soit
		// sélectionné", pas juste brièvement mis en évidence).
		setTimeout(function () {
			var el = document.querySelector('tr[data-highlight-id="' + highlightId + '"]');
			if (!el) {
				return;
			}
			el.scrollIntoView({ behavior: 'smooth', block: 'center' });
			el.classList.add('row-highlight-pulse');
		}, 150);
	});
})(window.jQuery);
