<?php
/**
 * Pointage — espace employé (com_dashboard task=pointageweb). Exclusif à ce contexte. La position
 * (GPS/Wi-Fi du navigateur) est demandée à chaque clic et REVÉRIFIÉE côté serveur (voir
 * pointerWeb() dans pointageweb/controleur.php, distance réelle à la localisation du bureau) —
 * impossible de la connaître au chargement de la page (l'API navigator.geolocation n'existe qu'en
 * JS), donc plus de blocage dur des boutons ici comme avec l'ancien contrôle IP : on tente
 * toujours le pointage, le serveur tranche.
 */
$etapesLabels = array(
	'arrivee' => array('Arrivée', 'fa-sign-in-alt'),
	'depart_pause' => array('Départ pause', 'fa-mug-hot'),
	'retour_pause' => array('Retour pause', 'fa-utensils'),
	'depart' => array('Départ', 'fa-sign-out-alt'),
);
$getters = array(
	'arrivee' => 'getHeureArrivee',
	'depart_pause' => 'getHeureDepartPause',
	'retour_pause' => 'getHeureRetourPause',
	'depart' => 'getHeureDepart',
);
$prochaineEtape = $pointageDuJour->getProchaineEtape();
$minutesTravaillees = $pointageDuJour->getMinutesTravaillees();
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-clock mr-2"></i>Pointage</h1>
		<p class="emp-page-subtitle">Pointez vos horaires depuis le bureau — votre position est vérifiée à chaque clic</p>
	</div>

	<?php if ($blocagePointage === 'ferie') : ?>
		<div class="emp-card">
			<div class="emp-punch-alert">
				<i class="fa fa-umbrella-beach"></i>
				<span>Aujourd'hui est un jour férié — le pointage n'est pas disponible. Bonne journée !</span>
			</div>
		</div>
	<?php elseif ($blocagePointage === 'conge') : ?>
		<div class="emp-card">
			<div class="emp-punch-alert">
				<i class="fa fa-plane-departure"></i>
				<span>Vous êtes en congé aujourd'hui — le pointage n'est pas disponible. Bon repos !</span>
			</div>
		</div>
	<?php else : ?>
		<div class="emp-card emp-card-tilt">
			<div class="emp-punch-geoloc-hint" id="empGeolocHint">
				<i class="fa fa-location-crosshairs"></i>
				<span>Nous vérifions votre position au moment du pointage — autorisez l'accès à la localisation si votre navigateur le demande.</span>
			</div>

			<div class="emp-punch-clock">
				<div class="emp-punch-clock-time" id="empPunchClock">--:--:--</div>
				<div class="emp-punch-clock-date"><?= letterDay(date('N')) . ' ' . html_entity_decode(normaldate2(date('Y-m-d'))) ?></div>
			</div>

			<div class="emp-punch-grid" id="empPunchGrid">
				<?php foreach ($etapesLabels as $cle => $info) :
					$heure = $pointageDuJour->{$getters[$cle]}();
					$estFait = (bool) $heure;
					$estPret = ($prochaineEtape === $cle);
				?>
					<button type="button" class="emp-punch-btn <?= $estFait ? 'is-done' : ($estPret ? 'is-ready' : '') ?>" data-etape="<?= $cle ?>" <?= (!$estPret) ? 'disabled' : '' ?>>
						<i class="fa <?= $estFait ? 'fa-check-circle' : $info[1] ?>"></i>
						<span><?= $info[0] ?></span>
						<span class="emp-punch-btn-heure"><?= $estFait ? substr($heure, 0, 5) : ($estPret ? 'Cliquez ici' : '—') ?></span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="emp-msgbox msgbox mt-3"></div>

			<div class="emp-punch-summary">
				<div class="emp-punch-summary-item">
					<div class="emp-punch-summary-value" id="empPunchWorked"><?= floor($minutesTravaillees / 60) ?>h<?= str_pad($minutesTravaillees % 60, 2, '0', STR_PAD_LEFT) ?></div>
					<div class="emp-punch-summary-label">Travaillé aujourd'hui</div>
				</div>
				<div class="emp-punch-summary-item">
					<div class="emp-punch-summary-value">
						<?php if ($prochaineEtape === null) : ?>
							<span class="emp-badge emp-badge-green">Terminé</span>
						<?php else : ?>
							<span class="emp-badge emp-badge-amber"><?= $etapesLabels[$prochaineEtape][0] ?></span>
						<?php endif; ?>
					</div>
					<div class="emp-punch-summary-label">Prochaine étape</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

</main>

<script type="text/javascript">
	$(function() {
		// Horloge en direct (juste l'affichage - l'heure enregistrée au clic est toujours celle du serveur).
		function tickClock() {
			var maintenant = new Date();
			var pad = function(n) { return n < 10 ? '0' + n : n; };
			$('#empPunchClock').text(pad(maintenant.getHours()) + ':' + pad(maintenant.getMinutes()) + ':' + pad(maintenant.getSeconds()));
		}
		tickClock();
		setInterval(tickClock, 1000);

		// Position GPS/Wi-Fi fraîche à chaque pointage (jamais réutilisée d'un pointage à l'autre)
		// - envoyée au serveur qui calcule la vraie distance au bureau (pointagelocalisation::
		// estDansLeRayon()). Si le navigateur refuse/échoue, on poste quand même sans coordonnées :
		// le serveur retombe alors sur le contrôle IP (pointageIpAutorisee()) et répond avec un
		// message clair si aucun des deux ne passe - jamais un blocage silencieux côté client.
		function obtenirPositionPuis(callback) {
			if (!navigator.geolocation) {
				callback(null);
				return;
			}
			navigator.geolocation.getCurrentPosition(
				function(position) {
					callback({ latitude: position.coords.latitude, longitude: position.coords.longitude });
				},
				function() { callback(null); },
				{ enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
			);
		}

		$(document).on('click', '.emp-punch-btn.is-ready', function() {
			var $btn = $(this);
			var etape = $btn.data('etape');
			$btn.prop('disabled', true);
			$('#empGeolocHint').addClass('is-locating');

			obtenirPositionPuis(function(position) {
				var order = { etape: etape };
				if (position) {
					order.latitude = position.latitude;
					order.longitude = position.longitude;
				}

				$.post('components/com_resourcehumaine/controleurs/router.php?task=pointerWeb', order, function(theResponse) {
					var data;
					try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }

					if (data.success === 1) {
						$btn.removeClass('is-ready').addClass('is-done').prop('disabled', true);
						$btn.find('i').removeClass().addClass('fa fa-check-circle');
						$btn.find('.emp-punch-btn-heure').text(data.heure.substring(0, 5));

						if (data.prochaine_etape) {
							$('.emp-punch-btn[data-etape="' + data.prochaine_etape + '"]').removeAttr('disabled').addClass('is-ready').find('.emp-punch-btn-heure').text('Cliquez ici');
						}

						var h = Math.floor(data.minutes_travaillees / 60);
						var m = data.minutes_travaillees % 60;
						$('#empPunchWorked').text(h + 'h' + (m < 10 ? '0' + m : m));

						$('.emp-msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Pointé !</strong> ' + data.heure.substring(0, 5) + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

						if (!data.prochaine_etape) {
							setTimeout(function() { location.reload(); }, 1500);
						}
					} else {
						$btn.prop('disabled', false);
						$('.emp-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> ' + (data.message || 'Une erreur est survenue.') + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					}
				}).fail(function() {
					$btn.prop('disabled', false);
					$('.emp-msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> La requête a échoué.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				}).always(function() {
					$('#empGeolocHint').removeClass('is-locating');
				});
			});
		});
	});
</script>
