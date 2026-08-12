<?php
// Widgets flottants globaux (jours fériés + Assistant) - déplacés hors de com_dashboard/views/
// dashboard/list.php (2026-08-04) pour apparaître sur TOUTES les pages de l'app, pas seulement le
// tableau de bord. Inclus une seule fois depuis includes/tpl/bottom.php, jamais sur la page de
// login (bottom-login.php est un fichier séparé qui n'inclut pas celui-ci). Même garde qu'avant :
// rien de tout ça pour un profil Ressource Humaine (qui a son propre "profile.php" à la place du
// dashboard classique et n'a pas accès à ces modules).
if (!$_SESSION['user']->isResourceHumaine()) :
?>

<?php
	// Widget flottant "jours fériés" (collé à droite de l'écran, visible par tous les utilisateurs) :
	// construit la liste des jours à marquer sur le mini-calendrier + les prochaines échéances. La coloration des
	// jours reprend holiday::getColor() (proche/en cours/passé/lointain), mais le
	// déclenchement de la notification "un jour férié arrive" suit une règle différente et
	// plus précise : dans les 5 prochains jours OUVRABLES (on saute samedi/dimanche).
	$holidayWidgetDays = array();
	$upcomingHolidays = array();
	$todayTs = strtotime(date('Y-m-d'));

	$fiveBusinessDays = new DateTime('today');
	$businessDaysCounted = 0;
	while ($businessDaysCounted < 5) {
		$fiveBusinessDays->modify('+1 day');
		if ((int) $fiveBusinessDays->format('N') < 6) {
			$businessDaysCounted++;
		}
	}
	$fiveBusinessDaysTs = $fiveBusinessDays->getTimestamp();

	$hasUrgentHoliday = false;
	foreach (holiday::findAll() as $h) {
		$statusClass = $h->getColor();
		$status = 'future';
		if ($statusClass == 'table-warning') {
			$status = 'soon';
		} elseif ($statusClass == 'table-success') {
			$status = 'ongoing';
		} elseif ($statusClass == 'table-danger') {
			$status = 'past';
		}

		$startTs = strtotime($h->getStartDate());
		if ($startTs >= $todayTs && $startTs <= $fiveBusinessDaysTs) {
			$hasUrgentHoliday = true;
		}

		$cursor = $startTs;
		$endTs = strtotime($h->getEndDate());
		while ($cursor <= $endTs) {
			$holidayWidgetDays[date('Y-m-d', $cursor)] = array('name' => $h->getName(), 'status' => $status);
			$cursor = strtotime('+1 day', $cursor);
		}

		if ($endTs >= $todayTs) {
			$upcomingHolidays[] = array('name' => $h->getName(), 'start' => $h->getStartDate(), 'status' => $status);
		}
	}
	usort($upcomingHolidays, function ($a, $b) {
		return strtotime($a['start']) - strtotime($b['start']);
	});
	$upcomingHolidays = array_slice($upcomingHolidays, 0, 4);
	?>
	<!-- Widget flottant : jours fériés (collé à droite, s'ouvre automatiquement si un jour férié approche) -->
	<div id="holiday-widget" class="holiday-widget">
		<button type="button" class="holiday-widget-tab" id="holiday-widget-tab" data-toggle="tooltip" data-placement="left" title="Calendrier des jours fériés à venir">
			<i class="fas fa-calendar-alt"></i>
		</button>
		<div class="holiday-widget-panel" id="holiday-widget-panel">
			<div class="holiday-widget-header">
				<button type="button" class="holiday-cal-nav" id="holiday-cal-prev"><i class="fas fa-chevron-left"></i></button>
				<span id="holiday-cal-title"></span>
				<button type="button" class="holiday-cal-nav" id="holiday-cal-next"><i class="fas fa-chevron-right"></i></button>
			</div>
			<div class="holiday-widget-dow">
				<span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span>
			</div>
			<div class="holiday-widget-days" id="holiday-cal-days"></div>
			<div class="holiday-widget-scheduled">
				<div class="holiday-widget-scheduled-header">
					<h6>Jours fériés</h6>
					<a href="index.php?option=com_holiday">Voir tout</a>
				</div>
				<?php foreach ($upcomingHolidays as $uh) : ?>
					<div class="holiday-widget-item">
						<span class="holiday-widget-tag holiday-tag-<?= $uh['status'] ?>">Jour férié</span>
						<div class="holiday-widget-item-name"><?= htmlspecialchars($uh['name']) ?></div>
						<div class="holiday-widget-item-date"><?= normaldate($uh['start']) ?></div>
					</div>
				<?php endforeach; ?>
				<?php if (empty($upcomingHolidays)) : ?>
					<div class="text-muted small">Aucun jour férié à venir</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="holiday-widget-toast" id="holiday-widget-toast">
		<button type="button" class="holiday-widget-toast-close" id="holiday-widget-toast-close">&times;</button>
		<span>😈 Un jour férié approche... le dictateur ne sera pas content si tu l'as pas noté ! 🤡 Va vite jeter un œil au calendrier avant qu'il n'explose 😂</span>
	</div>
	<script>
		<?php
		// JSON_INVALID_UTF8_SUBSTITUTE : un nom de jour férié mal encodé en base (arrive sur
		// la prod, jamais vu en local) fait échouer json_encode() silencieusement (retour
		// false, donc "var holidayWidgetDays = ;" -> SyntaxError JS qui casse tout le
		// widget) sans ce flag ; avec, les octets invalides sont juste remplacés.
		$holidayWidgetDaysJson = json_encode($holidayWidgetDays, JSON_INVALID_UTF8_SUBSTITUTE) ?: '{}';
		?>
		var holidayWidgetDays = <?php echo $holidayWidgetDaysJson; ?>;
		var holidayWidgetHasUrgent = <?php echo $hasUrgentHoliday ? 'true' : 'false'; ?>;
	</script>

<?php if ($_SESSION['user']->hasDroit('view', 'com_assistant')) : ?>
	<?php
	// Widget flottant "Assistant" (collé à droite, même patron que .holiday-widget ci-dessus) :
	// les tâches/rendez-vous/suivis à faire, triées par échéance, + un ajout rapide directement
	// dans le panneau sans changer de page.
	$assistantWidgetTaches = assistanttache::findAll($_SESSION['agence'], false);
	$assistantWidgetEnRetard = 0;
	foreach ($assistantWidgetTaches as $awt) {
		$j = $awt->getDaysLeft();
		if ($j !== null && $j < 0) {
			$assistantWidgetEnRetard++;
		}
	}
	$assistantWidgetApercu = array_slice($assistantWidgetTaches, 0, 6);
	// Listes pour le sélecteur "Lié à" du mini-formulaire du widget - même besoin que
	// components/com_assistant/index.php::assistantChargerListesRelation(), dupliqué ici
	// (léger, 5 requêtes) plutôt que factorisé, ce fichier n'étant jamais routé via
	// com_assistant/index.php pour pouvoir réutiliser sa fonction directement.
	if ($_SESSION['user']->hasDroit('add', 'com_assistant')) {
		$assistantWidgetClients = client::findAll(true, false, $_SESSION['agence']);
		$assistantWidgetFournisseurs = fournisseur::findAll(false, $_SESSION['agence']);
		$assistantWidgetEmployes = array_values(array_filter(resourcehumaine::findAll(), function ($e) {
			return $e->getAgency() && $e->getAgency()->getId() == $_SESSION['agence'];
		}));
		$assistantWidgetReclamations = reclamation::findAll(false, false, $_SESSION['agence']);
		$assistantWidgetBanks = bank::findAll($_SESSION['agence']);
	}
	?>
	<!-- Widget flottant : Assistant (tâches/rendez-vous/suivis) - même patron que .holiday-widget
	     ci-dessus (onglet collé à droite, panneau qui se déploie au clic), avec un ajout rapide
	     directement dans le panneau. -->
	<div id="assistant-widget" class="assistant-float-widget">
		<button type="button" class="assistant-float-widget-tab" id="assistant-widget-tab" data-toggle="tooltip" data-placement="left" title="Assistant — tâches, rendez-vous &amp; suivis">
			<i class="fa fa-tasks"></i>
			<?php if ($assistantWidgetEnRetard > 0) : ?><span class="assistant-float-widget-badge"><?= $assistantWidgetEnRetard ?></span><?php endif; ?>
		</button>
		<div class="assistant-float-widget-panel" id="assistant-widget-panel">
			<div class="holiday-widget-scheduled-header">
				<h6>Assistant</h6>
				<a href="index.php?option=com_assistant">Voir tout</a>
			</div>

			<div class="assistant-float-widget-list">
				<?php foreach ($assistantWidgetApercu as $awt) : ?>
					<?php
					$awtJours = $awt->getDaysLeft();
					$awtRetard = $awtJours !== null && $awtJours < 0;
					?>
					<a href="index.php?option=com_assistant&highlight=<?= $awt->getId() ?>" class="assistant-float-widget-item <?= $awtRetard ? 'assistant-float-widget-item-late' : '' ?>">
						<span class="assistant-float-widget-item-dot"></span>
						<span class="assistant-float-widget-item-body">
							<span class="assistant-float-widget-item-title"><?= htmlspecialchars($awt->getTitre()) ?></span>
							<span class="assistant-float-widget-item-date">
								<?= $awt->getDateTache() ? date('d/m/Y H:i', strtotime($awt->getDateTache())) : 'Sans date' ?>
							</span>
						</span>
					</a>
				<?php endforeach; ?>
				<?php if (empty($assistantWidgetApercu)) : ?>
					<div class="text-muted small">Rien à faire pour le moment ✨</div>
				<?php endif; ?>
			</div>

			<?php if ($_SESSION['user']->hasDroit('add', 'com_assistant')) : ?>
				<div class="assistant-float-widget-quickadd">
					<div class="col-md-12 msgbox"></div>
					<!-- Mêmes champs et même logique "Lié à" polymorphe que la page complète
					     (index.php?option=com_assistant&task=add / views/assistanttache/form.php)
					     - juste compacté dans le panneau, sans quitter la page en cours. -->
					<form id="assistantWidgetQuickForm">
						<input type="text" class="form-control form-control-sm mb-2" name="titre" placeholder="Nouvelle tâche..." required>
						<div class="d-flex mb-2" style="gap:6px;">
							<select class="form-control form-control-sm" name="type" style="flex:1;">
								<option value="tache">Tâche</option>
								<option value="rendez_vous">RDV</option>
								<option value="suivi_client">Suivi</option>
							</select>
							<input type="datetime-local" class="form-control form-control-sm" name="date_tache" style="flex:1;">
						</div>

						<select class="form-control form-control-sm mb-2" name="type_relation" id="assistantWidgetTypeRelation">
							<option value="">Lié à... (optionnel)</option>
							<?php foreach (assistanttache::$relationTypes as $cle => $infos) : ?>
								<option value="<?= $cle ?>"><?= $infos['label'] ?></option>
							<?php endforeach; ?>
						</select>
						<div id="assistantWidgetRelationRecordWrap" class="mb-2">
							<select class="form-control form-control-sm assistant-widget-relation-record" data-relation-type="client" style="display:none;" disabled>
								<option value="">Sélectionner un client</option>
								<?php foreach ($assistantWidgetClients as $c) : ?>
									<option value="<?= $c->getId() ?>"><?= trim($c->getRaisonSocial()) !== '' ? $c->getRaisonSocial() : trim($c->getNom() . ' ' . $c->getPrenom()) ?></option>
								<?php endforeach; ?>
							</select>
							<select class="form-control form-control-sm assistant-widget-relation-record" data-relation-type="fournisseur" style="display:none;" disabled>
								<option value="">Sélectionner un fournisseur</option>
								<?php foreach ($assistantWidgetFournisseurs as $f) : ?>
									<option value="<?= $f->getId() ?>"><?= trim($f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getNom() . ' ' . $f->getPrenom()) ?></option>
								<?php endforeach; ?>
							</select>
							<select class="form-control form-control-sm assistant-widget-relation-record" data-relation-type="rh" style="display:none;" disabled>
								<option value="">Sélectionner un employé</option>
								<?php foreach ($assistantWidgetEmployes as $e) : ?>
									<option value="<?= $e->getId() ?>"><?= $e->getFirstName() . ' ' . $e->getLastName() ?></option>
								<?php endforeach; ?>
							</select>
							<select class="form-control form-control-sm assistant-widget-relation-record" data-relation-type="reclamation" style="display:none;" disabled>
								<option value="">Sélectionner une réclamation</option>
								<?php foreach ($assistantWidgetReclamations as $r) : ?>
									<option value="<?= $r->getId() ?>"><?= $r->getSujet() ?></option>
								<?php endforeach; ?>
							</select>
							<select class="form-control form-control-sm assistant-widget-relation-record" data-relation-type="banque" style="display:none;" disabled>
								<option value="">Sélectionner une banque</option>
								<?php foreach ($assistantWidgetBanks as $b) : ?>
									<?php $labelBanque = $b->getLabel() !== null && $b->getLabel() !== '' ? $b->getLabel() : ($b->getRaisonSociale() !== null && $b->getRaisonSociale() !== '' ? $b->getRaisonSociale() : $b->getBanque()); ?>
									<option value="<?= $b->getId() ?>"><?= $labelBanque ?></option>
								<?php endforeach; ?>
							</select>
							<p class="assistant-widget-relation-none text-muted small mb-0" style="display:none;">Aucune fiche à choisir pour ce type.</p>
						</div>

						<textarea name="remarque" class="form-control form-control-sm mb-2" rows="2" placeholder="Notes..."></textarea>

						<button type="submit" class="btn btn-primary btn-sm btn-block mt-2"><span class="spinner-border spinner-border-sm mr-1 loading" style="display:none;"></span>Ajouter</button>
					</form>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<script>
		$(function () {
			var $widget = $('#assistant-widget');
			var $widgetTab = $('#assistant-widget-tab');
			// Le tooltip Bootstrap (title au survol) reste actif même une fois le panneau ouvert
			// (le bouton est toujours sous le curseur) - on le masque et on le désactive pendant
			// que le panneau est ouvert pour ne pas le voir se superposer au contenu déployé.
			$(document).on('click', '#assistant-widget-tab', function () {
				var opening = !$widget.hasClass('open');
				$widget.toggleClass('open', opening);
				if (opening) {
					$widgetTab.tooltip('hide').tooltip('disable');
				} else {
					$widgetTab.tooltip('enable');
				}
			});
			$(document).on('click', function (e) {
				if (!$(e.target).closest('#assistant-widget').length && $widget.hasClass('open')) {
					$widget.removeClass('open');
					$widgetTab.tooltip('enable');
				}
			});
			// "Lié à" du widget : même logique que le formulaire complet (un seul select de
			// fiche actif à la fois selon le type choisi).
			function assistantWidgetAppliquerTypeRelation() {
				var type = $('#assistantWidgetTypeRelation').val();
				$('.assistant-widget-relation-record').hide().prop('disabled', true);
				$('.assistant-widget-relation-none').hide();
				if (!type) {
					return;
				}
				var $zone = $('.assistant-widget-relation-record[data-relation-type="' + type + '"]');
				if ($zone.length) {
					$zone.show().prop('disabled', false);
				} else {
					$('.assistant-widget-relation-none').show();
				}
			}
			$('#assistantWidgetTypeRelation').on('change', assistantWidgetAppliquerTypeRelation);

			$('#assistantWidgetQuickForm').on('submit', function (e) {
				e.preventDefault();
				var $form = $(this);
				var data = {
					titre: $form.find('[name=titre]').val(),
					type: $form.find('[name=type]').val(),
					date_tache: $form.find('[name=date_tache]').val(),
					type_relation: $form.find('[name=type_relation]').val(),
					id_relation: $form.find('.assistant-widget-relation-record:visible').val(),
					remarque: $form.find('[name=remarque]').val()
				};
				$form.find('.loading').show();
				$.post('components/com_assistant/controleurs/router.php?task=addAssistantTache', data, function (theResponse) {
					$form.find('.loading').hide();
					if (parseInt(theResponse) === 1) {
						document.location.reload();
					} else {
						$form.closest('.assistant-float-widget-panel').find('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show p-2 small" role="alert">Erreur lors de l\'ajout<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			});
		});
	</script>
<?php endif; ?>

<script>
	(function () {
		if (typeof holidayWidgetDays === 'undefined') {
			return;
		}

		var monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
		var now = new Date();
		var viewYear = now.getFullYear();
		var viewMonth = now.getMonth();

		function pad(n) {
			return n < 10 ? '0' + n : '' + n;
		}

		function renderCalendar() {
			$('#holiday-cal-title').text(monthNames[viewMonth] + ' ' + viewYear);

			var firstDay = new Date(viewYear, viewMonth, 1);
			var startOffset = (firstDay.getDay() + 6) % 7; // grille commence le lundi
			var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
			var todayStr = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());

			var html = '';
			for (var i = 0; i < startOffset; i++) {
				html += '<span class="holiday-cal-day holiday-cal-day-empty"></span>';
			}
			for (var d = 1; d <= daysInMonth; d++) {
				var dateStr = viewYear + '-' + pad(viewMonth + 1) + '-' + pad(d);
				var info = holidayWidgetDays[dateStr];
				var cls = 'holiday-cal-day';
				var title = '';
				if (dateStr === todayStr) {
					cls += ' holiday-cal-day-today';
				}
				if (info) {
					cls += ' holiday-cal-day-holiday holiday-cal-day-' + info.status;
					title = ' title="' + String(info.name).replace(/"/g, '&quot;') + '"';
				}
				html += '<span class="' + cls + '"' + title + '>' + d + '</span>';
			}
			$('#holiday-cal-days').html(html);
		}

		// Ouverture/fermeture pilotée uniquement par la classe .open (transition CSS déjà
		// définie sur .holiday-widget-panel) : ne pas mélanger avec une animation GSAP qui
		// poserait des styles inline (transform/opacity) et empêcherait la fermeture au
		// clic suivant, la classe CSS ne pouvant plus reprendre la main dessus.
		function openWidget() {
			$('#holiday-widget').addClass('open');
		}

		function closeWidget() {
			$('#holiday-widget').removeClass('open');
		}

		// Le message reste affiché tant que l'utilisateur ne le ferme pas lui-même
		// (pas de disparition automatique).
		function showHolidayToast() {
			$('#holiday-widget-toast').addClass('show');
		}

		function hideHolidayToast() {
			$('#holiday-widget-toast').removeClass('show');
		}

		$(document).on('click', '#holiday-cal-prev', function () {
			viewMonth--;
			if (viewMonth < 0) {
				viewMonth = 11;
				viewYear--;
			}
			renderCalendar();
		});
		$(document).on('click', '#holiday-cal-next', function () {
			viewMonth++;
			if (viewMonth > 11) {
				viewMonth = 0;
				viewYear++;
			}
			renderCalendar();
		});
		$(document).on('click', '#holiday-widget-tab', function () {
			if ($('#holiday-widget').hasClass('open')) {
				closeWidget();
			} else {
				openWidget();
			}
		});
		$(document).on('click', function (e) {
			if (!$(e.target).closest('#holiday-widget').length) {
				closeWidget();
			}
		});
		$(document).on('click', '#holiday-widget-toast-close', function () {
			hideHolidayToast();
		});

		renderCalendar();

		// Le calendrier ne s'ouvre plus tout seul : seul le message d'alerte apparaît
		// (et reste visible jusqu'à fermeture manuelle), l'ouverture du panneau reste
		// entièrement au clic de l'utilisateur sur l'icône.
		if (holidayWidgetHasUrgent) {
			setTimeout(showHolidayToast, 900);
		}
	})();
</script>

<?php endif; // !isResourceHumaine ?>
