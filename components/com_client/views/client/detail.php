<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Détail client</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_client">Clients</a></li>
						<li class="breadcrumb-item active">Détail client</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<?php
		$clientNomAffiche = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getTitre() . ' ' . $client->getNom() . ' ' . $client->getPrenom());
		$photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-02.jpg";
		$rappelsActifs = 0;
		foreach ($rappels as $rp) {
			if ($rp->getDaysLeft() >= 0) {
				$rappelsActifs++;
			}
		}
		$relanceEnCours = false;
		foreach ($relances as $rl) {
			if (!$rl->isTraite()) {
				$relanceEnCours = true;
				break;
			}
		}
		$siteLogoUrl = '';
		$siteLogoFallbackUrl = '';
		if ($client->getSiteWeb() != '') {
			$domaineClient = preg_replace('#^https?://#i', '', $client->getSiteWeb());
			$domaineClient = preg_replace('#^www\.#i', '', $domaineClient);
			$domaineClient = explode('/', $domaineClient)[0];
			if ($domaineClient != '') {
				// Clearbit ne couvre que les entreprises indexées dans sa base ; pour les petits
				// sites non répertoriés (404 silencieux), on retombe sur le favicon du site via
				// Google, qui fonctionne pour quasiment n'importe quel domaine.
				$siteLogoUrl = 'https://logo.clearbit.com/' . $domaineClient;
				$siteLogoFallbackUrl = 'https://www.google.com/s2/favicons?domain=' . $domaineClient . '&sz=128';
			}
		}
		$caParDevise = $client->getChiffreAffaireParDevise();

		// Devise principale = celle qui pèse le plus dans le CA encaissé ; c'est celle-là qu'on
		// trace sous forme de courbe (les autres devises restent affichées en petits montants).
		$devisePrincipale = null;
		$totalDevisePrincipale = -1;
		foreach ($caParDevise as $dv => $mt) {
			if ($mt > $totalDevisePrincipale) {
				$totalDevisePrincipale = $mt;
				$devisePrincipale = $dv;
			}
		}

		$courbeEncaissement = array();
		$premierPaiementDate = null;
		if ($devisePrincipale !== null) {
			$cumul = 0;
			foreach ($client->getPaymentsChronologiques() as $paiement) {
				if ($paiement['devise'] !== $devisePrincipale) {
					continue;
				}
				$cumul += $paiement['montant'];
				$courbeEncaissement[] = array('date' => $paiement['date'], 'cumul' => $cumul);
				if ($premierPaiementDate === null) {
					$premierPaiementDate = $paiement['date'];
				}
			}
		}
		?>

		<!-- Profil client -->
		<div class="client-hero">
			<div class="client-hero-cover" style="background:linear-gradient(135deg, <?= $currentAgence->getColor(); ?>, #4f46e5);"></div>
			<div class="client-hero-body">
				<label class="avatar avatar-xxl client-hero-avatar">
					<img class="avatar-img" src="<?php echo $photoLink; ?>" onerror="this.src=`assets/img/profiles/avatar-02.jpg`" alt="Image de profile">
				</label>
				<?php if ($siteLogoUrl != '') : ?>
					<img class="client-hero-site-logo" src="<?php echo htmlspecialchars($siteLogoUrl); ?>" data-fallback="<?php echo htmlspecialchars($siteLogoFallbackUrl); ?>" alt="Logo <?php echo htmlspecialchars($client->getSiteWeb()); ?>" onerror="if(!this.dataset.fallbackTried){this.dataset.fallbackTried='1';this.src=this.dataset.fallback;}else{this.style.display='none';}">
				<?php endif; ?>
				<div class="client-hero-info">
					<h3><?php echo htmlspecialchars($clientNomAffiche); ?></h3>
					<p class="text-muted mb-2"><?php echo htmlspecialchars(trim($client->getFonction())); ?></p>
					<span class="badge <?php echo $client->isActive() ? 'bg-success-light' : 'bg-danger-light'; ?>"><?php echo $client->isActive() ? 'Actif' : 'Inactif'; ?></span>
					<?php if ($client->isArchived()) : ?><span class="badge bg-warning-light"><i class="fas fa-archive mr-1"></i> Archivé</span><?php endif; ?>
					<?php if ($client->getICE() != '') : ?><span class="badge bg-primary-light">ICE <?php echo htmlspecialchars($client->getICE()); ?></span><?php endif; ?>
					<?php if ($relanceEnCours) : ?><span class="badge bg-danger-light relance-badge-pulse"><i class="fa fa-bell mr-1"></i> Relance en cours</span><?php endif; ?>
				</div>
				<div class="client-hero-actions">
					<?php if ($client->getTel() != '') : ?>
						<a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $client->getTel()); ?>" class="btn btn-white btn-sm mr-2" data-toggle="tooltip" title="Appeler"><i class="fa fa-phone"></i></a>
					<?php endif; ?>
					<?php if ($client->getEmail() != '') : ?>
						<a href="mailto:<?php echo $client->getEmail(); ?>" class="btn btn-white btn-sm mr-2" data-toggle="tooltip" title="Envoyer un email"><i class="fa fa-envelope"></i></a>
					<?php endif; ?>
					<?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) : ?>
						<a href="index.php?option=com_client&task=socialAccounts&id=<?php echo $client->getId(); ?>" class="btn btn-white btn-sm mr-2" data-toggle="tooltip" title="Réseaux sociaux (identifiants)"><i class="fa fa-share-alt"></i></a>
					<?php endif; ?>
					<?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) : ?>
						<?php if ($client->isArchived()) : ?>
							<a href="javascript:void(0);" class="btn btn-white btn-sm mr-2 text-success retablirClientDetail" data-id="<?php echo $client->getId(); ?>" data-toggle="tooltip" title="Rétablir"><i class="fa fa-undo mr-1"></i> Rétablir</a>
						<?php else : ?>
							<a href="javascript:void(0);" class="btn btn-white btn-sm mr-2 text-secondary archiveClientDetail" data-id="<?php echo $client->getId(); ?>" data-toggle="tooltip" title="Archiver"><i class="fas fa-archive mr-1"></i> Archiver</a>
						<?php endif; ?>
						<a href="index.php?option=com_client&task=edit&id=<?php echo $client->getId(); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil-alt mr-1"></i> Modifier</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<!-- /Profil client -->

		<!-- Chiffre d'affaires encaissé : courbe d'évolution réelle, pas une case de couleur -->
		<?php if (!empty($caParDevise)) : ?>
		<div class="client-revenue-card money-sensitive">
			<div class="client-revenue-info">
				<span class="client-revenue-eyebrow">Encaissé à ce jour</span>
				<div class="client-revenue-figure" data-value="<?php echo (float) $totalDevisePrincipale; ?>" data-devise="<?php echo htmlspecialchars($devisePrincipale); ?>">0 <?php echo htmlspecialchars($devisePrincipale); ?></div>
				<?php if ($premierPaiementDate) : ?>
					<span class="client-revenue-since">depuis le <?php echo normaldate($premierPaiementDate); ?></span>
				<?php endif; ?>
				<?php if (count($caParDevise) > 1) : ?>
					<div class="client-revenue-chips">
						<?php foreach ($caParDevise as $devise => $montant) : ?>
							<?php if ($devise === $devisePrincipale) continue; ?>
							<span class="client-revenue-chip"><?php echo number_format($montant, 2, ',', ' ') . ' ' . htmlspecialchars($devise); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if (count($courbeEncaissement) >= 2) : ?>
				<div class="client-revenue-chart">
					<canvas id="clientRevenueCanvas"></canvas>
					<div class="client-revenue-chart-axis">
						<span><?php echo normaldate($premierPaiementDate); ?></span>
						<span><?php echo normaldate(date('Y-m-d')); ?> (aujourd'hui)</span>
					</div>
				</div>
				<script type="application/json" id="clientRevenueData"><?php echo json_encode(array(
					'points' => $courbeEncaissement,
					'debut' => $premierPaiementDate,
					'aujourdhui' => date('Y-m-d')
				)); ?></script>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<!-- /Chiffre d'affaires encaissé -->

		<!-- Statistiques rapides -->
		<div class="row client-stats-row">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-1"><i class="far fa-file"></i></span>
							<div class="dash-count">
								<div class="dash-title">Devis</div>
								<div class="dash-counts"><p><?php echo sizeof($deviss); ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-2"><i class="fa fa-file-alt"></i></span>
							<div class="dash-count">
								<div class="dash-title">Factures</div>
								<div class="dash-counts"><p><?php echo sizeof($factures); ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-3"><i class="fa fa-bell"></i></span>
							<div class="dash-count">
								<div class="dash-title">Rappels actifs</div>
								<div class="dash-counts"><p><?php echo $rappelsActifs; ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-body">
						<div class="dash-widget-header">
							<span class="dash-widget-icon bg-4"><i class="fa fa-comments"></i></span>
							<div class="dash-count">
								<div class="dash-title">Relances</div>
								<div class="dash-counts"><p><?php echo sizeof($relances); ?></p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Statistiques rapides -->

		<!-- Coordonnées -->
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Coordonnées</h4>
					</div>
					<div class="card-body">
						<div class="row client-info-grid">
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-id-card"></i>
								<div><span>Nom complet</span><p><?php echo htmlspecialchars(trim($client->getTitre() . ' ' . $client->getNom() . ' ' . $client->getPrenom())); ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-building"></i>
								<div><span>Raison sociale</span><p><?php echo htmlspecialchars($client->getRaisonSocial()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-briefcase"></i>
								<div><span>Fonction</span><p><?php echo htmlspecialchars($client->getFonction()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-file-invoice"></i>
								<div><span>ICE</span><p><?php echo htmlspecialchars($client->getICE()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-file-signature"></i>
								<div><span>RC</span><p><?php echo htmlspecialchars($client->getRc()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-phone"></i>
								<div><span>Tél</span><p><?php echo htmlspecialchars(trim($client->getTel() . ' / ' . $client->getTel2() . ' / ' . $client->getTel3(), ' /')) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-envelope"></i>
								<div><span>E-mail</span><p><?php echo htmlspecialchars($client->getEmail()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-map-marker-alt"></i>
								<div><span>Adresse</span><p><?php echo htmlspecialchars(trim($client->getAdresse() . ' ' . $client->getAdresse2())) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-city"></i>
								<div><span>Ville / Région</span><p><?php echo htmlspecialchars(trim($client->getVille() . ' / ' . $client->getRegion(), ' /')) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-globe"></i>
								<div><span>Pays</span><p><?php echo htmlspecialchars($client->getPays()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-mail-bulk"></i>
								<div><span>Code postal</span><p><?php echo htmlspecialchars($client->getCP()) ?: '—'; ?></p></div>
							</div>
							<div class="col-md-6 col-lg-4 client-info-item">
								<i class="fa fa-globe-europe"></i>
								<div><span>Site</span><p>
									<?php if ($client->getSiteWeb() != '') : ?>
										<a href="<?php echo htmlspecialchars($client->getSiteWeb()); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($client->getSiteWeb()); ?></a>
									<?php else : ?>
										—
									<?php endif; ?>
								</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Coordonnées -->

		<!-- Récapitulatif IA des collaborations -->
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="card-title mb-0">Récapitulatif des collaborations</h4>
						<button type="button" class="btn btn-sm btn-outline-primary" id="btnGenerateIaRecap" data-client="<?php echo $client->getId(); ?>">
							<span class="spinner-border spinner-border-sm mr-1 loading" style="display:none;"></span>
							<i class="fa fa-magic mr-1"></i> <?php echo $client->getIaRecap() ? 'Régénérer' : 'Générer'; ?>
						</button>
					</div>
					<div class="card-body">
						<div id="iaRecapContent">
							<?php if ($client->getIaRecap()) : ?>
								<p style="white-space:pre-line;"><?php echo htmlspecialchars($client->getIaRecap()); ?></p>
								<small class="text-muted">Généré le <?php echo normaldate(substr($client->getIaRecapDate(), 0, 10)); ?></small>
							<?php else : ?>
								<p class="text-muted mb-0">Pas encore généré — clique sur "Générer" pour obtenir une synthèse de l'historique de ce client (devis, factures, services, paiements).</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Récapitulatif IA -->

		<!-- Onglets dynamiques : devis / factures / paiements / rappels / relances -->
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header client-tabs-header">
						<ul class="nav nav-tabs client-tabs" role="tablist">
							<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-devis">Devis <span class="badge badge-pill ml-1"><?php echo sizeof($deviss); ?></span></a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-factures">Factures <span class="badge badge-pill ml-1"><?php echo sizeof($factures); ?></span></a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-payments">Paiements</a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-rappels">Domaines &amp; Hébergement <span class="badge badge-pill ml-1"><?php echo sizeof($rappels); ?></span></a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-relances">Relances <span class="badge badge-pill ml-1 <?php echo $relanceEnCours ? 'relance-badge-pulse' : ''; ?>"><?php echo sizeof($relances); ?></span></a></li>
							<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-attestations">Attestations <span class="badge badge-pill ml-1"><?php echo sizeof($attestations); ?></span></a></li>
						</ul>
					</div>
					<div class="card-body">
						<div class="tab-content client-tab-content">
							<div class="tab-pane fade show active" id="tab-devis">
								<?php include("devis.php"); ?>
							</div>
							<div class="tab-pane fade" id="tab-factures">
								<?php include("factures.php"); ?>
							</div>
							<div class="tab-pane fade" id="tab-payments">
								<?php include("payments.php"); ?>
							</div>
							<div class="tab-pane fade" id="tab-rappels">
								<?php include("rappels.php"); ?>
							</div>
							<div class="tab-pane fade" id="tab-relances">
								<?php include("relances.php"); ?>
							</div>
							<div class="tab-pane fade" id="tab-attestations">
								<?php include("attestations.php"); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Onglets -->

	</div>
</div>
<!-- /Page Wrapper -->

<script>
	$(function () {
		$(document).on("click", ".archiveClientDetail", function () {
			var $btn = $(this);
			if (confirm("Archiver ce client ? Il ne sera plus visible dans la liste principale, mais reste accessible depuis \"Clients archivés\".")) {
				var order = 'id=' + $btn.attr("data-id");
				$.post("components/com_client/controleurs/router.php?task=archiveClient", order, function (theResponse) {
					if (parseInt(theResponse) == 1) {
						location.reload();
					} else {
						alert("Erreur lors de l'archivage.");
					}
				});
			}
		});
		$(document).on("click", ".retablirClientDetail", function () {
			var $btn = $(this);
			if (confirm("Rétablir ce client dans la liste principale ?")) {
				var order = 'id=' + $btn.attr("data-id");
				$.post("components/com_client/controleurs/router.php?task=retablirClient", order, function (theResponse) {
					if (parseInt(theResponse) == 1) {
						location.reload();
					} else {
						alert("Erreur lors du rétablissement.");
					}
				});
			}
		});

		// Carte "Encaissé à ce jour" : entrée douce, comptage progressif du montant, et courbe
		// d'évolution réelle des paiements dessinée en Canvas (dégrade proprement si GSAP est
		// indisponible ou s'il y a moins de 2 points, même convention que modern-theme.js).
		var $revenueCard = $('.client-revenue-card');
		if ($revenueCard.length) {
			if (typeof gsap !== 'undefined') {
				gsap.from($revenueCard[0], { opacity: 0, y: 16, duration: 0.5, ease: 'power2.out' });
			}

			var $figure = $('.client-revenue-figure');
			var valeurFinale = parseFloat($figure.attr('data-value')) || 0;
			var devisePrincipale = $figure.attr('data-devise');
			if (typeof gsap !== 'undefined') {
				var compteur = { valeur: 0 };
				gsap.to(compteur, {
					valeur: valeurFinale,
					duration: 1.2,
					ease: 'power1.out',
					onUpdate: function () {
						$figure.text(compteur.valeur.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + devisePrincipale);
					}
				});
			} else {
				$figure.text(valeurFinale.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + devisePrincipale);
			}

			var dataEl = document.getElementById('clientRevenueData');
			var canvas = document.getElementById('clientRevenueCanvas');
			if (dataEl && canvas) {
				var payload = JSON.parse(dataEl.textContent || '{}');
				var points = payload.points || [];
				if (points.length >= 2) {
					var ctx = canvas.getContext('2d');
					var dpr = window.devicePixelRatio || 1;
					var cssWidth = canvas.clientWidth;
					var cssHeight = canvas.clientHeight;
					canvas.width = cssWidth * dpr;
					canvas.height = cssHeight * dpr;
					ctx.scale(dpr, dpr);

					var maxVal = points[points.length - 1].cumul || 1;
					var pad = 10;

					// Échelle horizontale = le temps réel, pas l'index des points : l'axe couvre
					// [date du premier paiement → aujourd'hui], mais la courbe elle-même s'arrête
					// à la position réelle du dernier paiement (donc avant le bord droit si le
					// dernier paiement ne date pas d'aujourd'hui).
					var tDebut = new Date(payload.debut).getTime();
					var tFin = new Date(payload.aujourdhui).getTime();
					var echelleTotale = Math.max(tFin - tDebut, 1);

					function xForDate(dateStr) {
						var t = new Date(dateStr).getTime();
						return pad + ((t - tDebut) / echelleTotale) * (cssWidth - pad * 2);
					}
					function yAt(v) { return cssHeight - pad - (v / maxVal) * (cssHeight - pad * 2); }

					function draw(progress) {
						ctx.clearRect(0, 0, cssWidth, cssHeight);
						var pos = progress * (points.length - 1);
						var full = Math.floor(pos);
						var partial = pos - full;

						var coords = [];
						for (var i = 0; i <= full; i++) {
							coords.push([xForDate(points[i].date), yAt(points[i].cumul)]);
						}
						if (full < points.length - 1) {
							var x0 = xForDate(points[full].date), y0 = yAt(points[full].cumul);
							var x1 = xForDate(points[full + 1].date), y1 = yAt(points[full + 1].cumul);
							coords.push([x0 + (x1 - x0) * partial, y0 + (y1 - y0) * partial]);
						}
						if (coords.length < 2) {
							return;
						}

						var grad = ctx.createLinearGradient(0, 0, 0, cssHeight);
						grad.addColorStop(0, 'rgba(109, 40, 217, 0.20)');
						grad.addColorStop(1, 'rgba(109, 40, 217, 0)');
						ctx.beginPath();
						ctx.moveTo(coords[0][0], cssHeight - pad);
						coords.forEach(function (c) { ctx.lineTo(c[0], c[1]); });
						ctx.lineTo(coords[coords.length - 1][0], cssHeight - pad);
						ctx.closePath();
						ctx.fillStyle = grad;
						ctx.fill();

						ctx.beginPath();
						coords.forEach(function (c, i) { i === 0 ? ctx.moveTo(c[0], c[1]) : ctx.lineTo(c[0], c[1]); });
						ctx.strokeStyle = '#6d28d9';
						ctx.lineWidth = 2.5;
						ctx.lineJoin = 'round';
						ctx.lineCap = 'round';
						ctx.stroke();

						var last = coords[coords.length - 1];
						ctx.beginPath();
						ctx.arc(last[0], last[1], 4.5, 0, Math.PI * 2);
						ctx.fillStyle = '#6d28d9';
						ctx.fill();
						ctx.beginPath();
						ctx.arc(last[0], last[1], 8, 0, Math.PI * 2);
						ctx.strokeStyle = 'rgba(109,40,217,0.35)';
						ctx.lineWidth = 1.5;
						ctx.stroke();

						// Date du dernier paiement affichée sous son point, une fois la courbe
						// arrivée à ce point réel (pas pendant une position interpolée).
						if (full >= points.length - 1) {
							var partiesDate = points[points.length - 1].date.split('-');
							var texteDate = partiesDate[2] + '/' + partiesDate[1] + '/' + partiesDate[0];
							ctx.font = '600 11px -apple-system, Segoe UI, sans-serif';
							ctx.fillStyle = '#6d28d9';
							ctx.textAlign = 'center';
							ctx.textBaseline = 'top';
							var largeurTexte = ctx.measureText(texteDate).width;
							var xTexte = Math.min(Math.max(last[0], pad + largeurTexte / 2), cssWidth - pad - largeurTexte / 2);
							ctx.fillText(texteDate, xTexte, last[1] + 12);
						}
					}

					draw(0);
					if (typeof gsap !== 'undefined') {
						var etat = { p: 0 };
						gsap.to(etat, { p: 1, duration: 1.4, ease: 'power2.out', delay: 0.15, onUpdate: function () { draw(etat.p); } });
					} else {
						draw(1);
					}
				}
			}
		}

		$('#btnGenerateIaRecap').on('click', function () {
			var $btn = $(this);
			$btn.prop('disabled', true);
			$btn.find('.loading').show();
			$.post('components/com_ia/controleurs/router.php?task=generateClientSummary', { client: $btn.data('client') }, function (response) {
				$btn.find('.loading').hide();
				$btn.prop('disabled', false);
				if (response && response.icon === 'success') {
					$('#iaRecapContent').html('<p style="white-space:pre-line;">' + $('<div>').text(response.recap).html() + '</p><small class="text-muted">Généré à l\'instant</small>');
					$btn.html('<i class="fa fa-magic mr-1"></i> Régénérer');
				} else {
					$('#iaRecapContent').html('<p class="text-danger mb-0">Erreur lors de la génération : ' + (response && response.message ? response.message : 'inconnue') + '</p>');
				}
			}, 'json').fail(function () {
				$btn.find('.loading').hide();
				$btn.prop('disabled', false);
				$('#iaRecapContent').html('<p class="text-danger mb-0">Erreur de connexion, merci de réessayer.</p>');
			});
		});

		$(document).on("click", ".send-email-devis", function () {
			var $btn = $(this);
			var id = $btn.data("id");
			$btn.prop("disabled", true);
			$.post("components/com_devis/controleurs/router.php?task=sendEmailDevis", { id: id }, function (response) {
				$btn.prop("disabled", false);
				if (response.success) {
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Devis envoyé par email avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> ' + (response.message || "Erreur lors de l'envoi de l'email") + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}, "json").fail(function () {
				$btn.prop("disabled", false);
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur réseau lors de l\'envoi de l\'email<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			});
		});
	});
</script>
