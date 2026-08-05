<?php
$clientNomAffiche = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getTitre() . ' ' . $client->getNom() . ' ' . $client->getPrenom());
// Une entrée par plateforme (au plus) : index par plateforme pour un accès direct dans la boucle
// d'affichage ci-dessous, plutôt que de re-scanner $clientSocials à chaque itération.
$socialParPlateforme = array();
foreach ($clientSocials as $cs) {
    $socialParPlateforme[$cs->getPlateforme()] = $cs;
}
// Accès "autres" : toute entrée dont la plateforme n'est pas dans la liste fixe des 6 - permet
// d'enregistrer n'importe quel accès (Trello, Mailchimp, hébergeur...) sans limiter le formulaire
// aux seuls réseaux sociaux prévus au départ.
$customEntries = array();
foreach ($clientSocials as $cs) {
    if (!isset(clientsocial::$plateformes[$cs->getPlateforme()])) {
        $customEntries[] = $cs;
    }
}
$estSuperAdmin = $_SESSION['user']->isSuperUser();
?>
<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Réseaux sociaux — <?php echo htmlspecialchars($clientNomAffiche); ?></h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_client">Clients</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_client&task=showDetails&id=<?php echo $client->getId(); ?>"><?php echo htmlspecialchars($clientNomAffiche); ?></a></li>
						<li class="breadcrumb-item active">Réseaux sociaux</li>
					</ul>
				</div>
				<?php if ($estSuperAdmin) : ?>
				<div class="col-auto">
					<a href="javascript:void(0);" class="btn btn-primary" id="socialGenerateTokenBtn"><i class="fa fa-share-square mr-1"></i> Générer un accès temporaire</a>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="alert alert-warning social-security-note">
			<i class="fa fa-shield-alt mr-2"></i>
			Identifiants sensibles : les mots de passe sont chiffrés en base et restent masqués par défaut.
			<?php if ($estSuperAdmin) : ?>
				Cliquez sur <i class="fa fa-eye"></i> pour révéler un mot de passe ponctuellement.
			<?php else : ?>
				Seul un superadmin peut les révéler.
			<?php endif; ?>
		</div>

		<div class="row social-accounts-row">
			<?php foreach (clientsocial::$plateformes as $cle => $def) : ?>
				<?php $entry = isset($socialParPlateforme[$cle]) ? $socialParPlateforme[$cle] : null; ?>
				<div class="col-xl-4 col-md-6 col-12 d-flex">
					<div class="card flex-fill social-account-card" data-plateforme="<?php echo htmlspecialchars($cle); ?>">
						<div class="card-body">
							<div class="social-account-head">
								<span class="social-account-icon" style="background:<?php echo htmlspecialchars($def['couleur']); ?>;"><i class="fab <?php echo htmlspecialchars($def['icon']); ?>"></i></span>
								<h5 class="social-account-title"><?php echo htmlspecialchars($def['label']); ?></h5>
							</div>
							<?php if ($entry) : ?>
								<ul class="social-account-fields">
									<li>
										<span class="social-account-label">Identifiant</span>
										<span class="social-account-value"><?php echo $entry->getLogin() != '' ? htmlspecialchars($entry->getLogin()) : '—'; ?></span>
									</li>
									<li>
										<span class="social-account-label">Mot de passe</span>
										<span class="social-account-value social-account-password" data-id="<?php echo $entry->getId(); ?>">
											<span class="social-password-masked">••••••••</span>
											<span class="social-password-plain" style="display:none;"></span>
											<?php if ($estSuperAdmin) : ?>
												<a href="javascript:void(0);" class="social-password-reveal-btn" data-toggle="tooltip" title="Révéler"><i class="fa fa-eye"></i></a>
											<?php endif; ?>
										</span>
									</li>
									<li>
										<span class="social-account-label">Lien</span>
										<span class="social-account-value">
											<?php if ($entry->getLien() != '') : ?>
												<a href="<?php echo htmlspecialchars($entry->getLien()); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($entry->getLien()); ?></a>
											<?php else : ?>—<?php endif; ?>
										</span>
									</li>
									<li>
										<span class="social-account-label">Email de récupération</span>
										<span class="social-account-value"><?php echo $entry->getEmailRecuperation() != '' ? htmlspecialchars($entry->getEmailRecuperation()) : '—'; ?></span>
									</li>
									<li>
										<span class="social-account-label">Tél. de récupération</span>
										<span class="social-account-value"><?php echo $entry->getTelephoneRecuperation() != '' ? htmlspecialchars($entry->getTelephoneRecuperation()) : '—'; ?></span>
									</li>
									<?php if ($entry->getRemarque() != '') : ?>
									<li>
										<span class="social-account-label">Remarque</span>
										<span class="social-account-value"><?php echo nl2br(htmlspecialchars($entry->getRemarque())); ?></span>
									</li>
									<?php endif; ?>
								</ul>
								<div class="social-account-actions">
									<a href="javascript:void(0);" class="btn btn-white btn-sm social-account-edit-btn"
										data-id="<?php echo $entry->getId(); ?>"
										data-plateforme="<?php echo htmlspecialchars($cle); ?>"
										data-login="<?php echo htmlspecialchars($entry->getLogin()); ?>"
										data-lien="<?php echo htmlspecialchars($entry->getLien()); ?>"
										data-email="<?php echo htmlspecialchars($entry->getEmailRecuperation()); ?>"
										data-tel="<?php echo htmlspecialchars($entry->getTelephoneRecuperation()); ?>"
										data-remarque="<?php echo htmlspecialchars($entry->getRemarque()); ?>"
									><i class="fa fa-pencil-alt mr-1"></i> Modifier</a>
									<a href="javascript:void(0);" class="btn btn-white btn-sm text-danger social-account-delete-btn" data-id="<?php echo $entry->getId(); ?>"><i class="fa fa-trash mr-1"></i> Supprimer</a>
								</div>
							<?php else : ?>
								<p class="text-muted social-account-empty">Aucun identifiant enregistré.</p>
								<a href="javascript:void(0);" class="btn btn-primary btn-sm social-account-add-btn" data-plateforme="<?php echo htmlspecialchars($cle); ?>">
									<i class="fa fa-plus mr-1"></i> Ajouter
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<?php foreach ($customEntries as $entry) : ?>
				<div class="col-xl-4 col-md-6 col-12 d-flex">
					<div class="card flex-fill social-account-card" data-plateforme="<?php echo htmlspecialchars($entry->getPlateforme()); ?>">
						<div class="card-body">
							<div class="social-account-head">
								<span class="social-account-icon" style="background:#64748b;"><i class="fa fa-key"></i></span>
								<h5 class="social-account-title"><?php echo htmlspecialchars($entry->getPlateforme()); ?></h5>
							</div>
							<ul class="social-account-fields">
								<li>
									<span class="social-account-label">Identifiant</span>
									<span class="social-account-value"><?php echo $entry->getLogin() != '' ? htmlspecialchars($entry->getLogin()) : '—'; ?></span>
								</li>
								<li>
									<span class="social-account-label">Mot de passe</span>
									<span class="social-account-value social-account-password" data-id="<?php echo $entry->getId(); ?>">
										<span class="social-password-masked">••••••••</span>
										<span class="social-password-plain" style="display:none;"></span>
										<?php if ($estSuperAdmin) : ?>
											<a href="javascript:void(0);" class="social-password-reveal-btn" data-toggle="tooltip" title="Révéler"><i class="fa fa-eye"></i></a>
										<?php endif; ?>
									</span>
								</li>
								<li>
									<span class="social-account-label">Lien</span>
									<span class="social-account-value">
										<?php if ($entry->getLien() != '') : ?>
											<a href="<?php echo htmlspecialchars($entry->getLien()); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($entry->getLien()); ?></a>
										<?php else : ?>—<?php endif; ?>
									</span>
								</li>
								<li>
									<span class="social-account-label">Email de récupération</span>
									<span class="social-account-value"><?php echo $entry->getEmailRecuperation() != '' ? htmlspecialchars($entry->getEmailRecuperation()) : '—'; ?></span>
								</li>
								<li>
									<span class="social-account-label">Tél. de récupération</span>
									<span class="social-account-value"><?php echo $entry->getTelephoneRecuperation() != '' ? htmlspecialchars($entry->getTelephoneRecuperation()) : '—'; ?></span>
								</li>
								<?php if ($entry->getRemarque() != '') : ?>
								<li>
									<span class="social-account-label">Remarque</span>
									<span class="social-account-value"><?php echo nl2br(htmlspecialchars($entry->getRemarque())); ?></span>
								</li>
								<?php endif; ?>
							</ul>
							<div class="social-account-actions">
								<a href="javascript:void(0);" class="btn btn-white btn-sm social-account-edit-btn"
									data-id="<?php echo $entry->getId(); ?>"
									data-plateforme="<?php echo htmlspecialchars($entry->getPlateforme()); ?>"
									data-custom="1"
									data-login="<?php echo htmlspecialchars($entry->getLogin()); ?>"
									data-lien="<?php echo htmlspecialchars($entry->getLien()); ?>"
									data-email="<?php echo htmlspecialchars($entry->getEmailRecuperation()); ?>"
									data-tel="<?php echo htmlspecialchars($entry->getTelephoneRecuperation()); ?>"
									data-remarque="<?php echo htmlspecialchars($entry->getRemarque()); ?>"
								><i class="fa fa-pencil-alt mr-1"></i> Modifier</a>
								<a href="javascript:void(0);" class="btn btn-white btn-sm text-danger social-account-delete-btn" data-id="<?php echo $entry->getId(); ?>"><i class="fa fa-trash mr-1"></i> Supprimer</a>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<!-- Accès libre : n'importe quel autre service (hébergeur, outil interne, etc.), pas
			     limité aux 6 plateformes prévues au départ. -->
			<div class="col-xl-4 col-md-6 col-12 d-flex">
				<div class="card flex-fill social-account-card social-account-card-add-other">
					<div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
						<span class="social-account-icon mb-2" style="background:#64748b;"><i class="fa fa-plus"></i></span>
						<p class="text-muted mb-2">Un autre accès à enregistrer ?</p>
						<a href="javascript:void(0);" class="btn btn-primary btn-sm social-account-add-btn" data-plateforme="__custom__">
							<i class="fa fa-plus mr-1"></i> Ajouter un autre accès
						</a>
					</div>
				</div>
			</div>
		</div>

		<?php if ($estSuperAdmin && !empty($socialTokens)) : ?>
			<div class="card mt-4">
				<div class="card-header"><h5 class="card-title mb-0">Accès temporaires</h5></div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-hover mb-0">
							<thead class="thead-light">
								<tr>
									<th>Portée</th>
									<th>Créé le</th>
									<th>Expire le</th>
									<th>Dernière utilisation</th>
									<th>Statut</th>
									<th class="text-right">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($socialTokens as $tok) : ?>
									<tr>
										<td><?php echo $tok->getScopeType() === 'specific' ? htmlspecialchars($tok->getScopePlateforme()) : 'Tous les comptes'; ?></td>
										<td><?php echo normaldate($tok->getDateAdd()); ?></td>
										<td><?php echo date('d/m/Y H:i', strtotime($tok->getExpiresAt())); ?></td>
										<td><?php echo $tok->getLastUsedAt() ? date('d/m/Y H:i', strtotime($tok->getLastUsedAt())) : '—'; ?></td>
										<td>
											<?php if ($tok->isRevoked()) : ?><span class="badge bg-danger-light">Révoqué</span>
											<?php elseif ($tok->isExpired()) : ?><span class="badge bg-secondary-light">Expiré</span>
											<?php else : ?><span class="badge bg-success-light">Actif</span><?php endif; ?>
										</td>
										<td class="text-right">
											<?php if (!$tok->isRevoked() && !$tok->isExpired()) : ?>
												<a href="javascript:void(0);" class="btn btn-white btn-sm text-danger social-token-revoke-btn" data-id="<?php echo $tok->getId(); ?>">Révoquer</a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php endif; ?>

	</div>
</div>
<!-- /Page Wrapper -->

<?php if ($estSuperAdmin) : ?>
<!-- Modale génération d'accès temporaire -->
<div id="socialTokenModal" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Générer un accès temporaire</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body" id="socialTokenStep1">
				<p class="text-muted small">Valable 24h, sans compte CRM. Le lien peut être envoyé sur Slack ; le code de sécurité doit être communiqué séparément, à titre personnel, au responsable social media.</p>
				<div class="form-group">
					<label>Portée</label>
					<select class="form-control" id="socialTokenScope">
						<option value="all">Vérifier tous les comptes</option>
						<option value="specific">Vérifier un compte spécifique</option>
					</select>
				</div>
				<div class="form-group" id="socialTokenPlateformeGroup" style="display:none;">
					<label>Plateforme</label>
					<select class="form-control" id="socialTokenPlateforme">
						<?php foreach ($clientSocials as $cs) : ?>
							<option value="<?php echo htmlspecialchars($cs->getPlateforme()); ?>"><?php echo isset(clientsocial::$plateformes[$cs->getPlateforme()]) ? htmlspecialchars(clientsocial::$plateformes[$cs->getPlateforme()]['label']) : htmlspecialchars($cs->getPlateforme()); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group form-check">
					<input type="checkbox" class="form-check-input" id="socialTokenSlack">
					<label class="form-check-label" for="socialTokenSlack">Envoyer le lien sur Slack (#family)</label>
				</div>
				<div class="msgbox"></div>
			</div>
			<div class="modal-body" id="socialTokenStep2" style="display:none;">
				<div class="alert alert-warning">
					<i class="fa fa-exclamation-triangle mr-1"></i> Ce code ne sera plus jamais affiché. Copiez-le et transmettez-le séparément du lien.
				</div>
				<div class="form-group">
					<label>Lien (peut être partagé sur Slack)</label>
					<div class="input-group">
						<input type="text" class="form-control" id="socialTokenResultUrl" readonly>
						<div class="input-group-append"><button class="btn btn-white social-token-copy-btn" type="button" data-target="socialTokenResultUrl">Copier</button></div>
					</div>
				</div>
				<div class="form-group">
					<label>Code de sécurité (à communiquer à part, en personne)</label>
					<div class="input-group">
						<input type="text" class="form-control text-center" style="font-size:1.3rem; letter-spacing:0.3rem;" id="socialTokenResultCode" readonly>
						<div class="input-group-append"><button class="btn btn-white social-token-copy-btn" type="button" data-target="socialTokenResultCode">Copier</button></div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Fermer</button>
				<button type="button" class="btn btn-primary" id="socialTokenGenerateBtn">Générer</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- Modal Ajout/Modification -->
<div id="socialAccountModal" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="socialAccountModalTitle">Identifiants</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="socialAccountForm">
				<div class="modal-body">
					<input type="hidden" name="id" id="socialFieldId" value="">
					<input type="hidden" name="id_client" value="<?php echo $client->getId(); ?>">
					<input type="hidden" name="plateforme" id="socialFieldPlateforme" value="">
					<div class="form-group" id="socialFieldPlateformeGroup" style="display:none;">
						<label>Nom de la plateforme</label>
						<input type="text" class="form-control" id="socialFieldPlateformeText" placeholder="ex. Trello, Mailchimp, hébergeur...">
					</div>
					<div class="form-group">
						<label>Identifiant / nom d'utilisateur</label>
						<input type="text" class="form-control" name="login" id="socialFieldLogin">
					</div>
					<div class="form-group">
						<label>Mot de passe</label>
						<input type="password" class="form-control" name="password" id="socialFieldPassword" autocomplete="new-password" placeholder="Laisser vide pour ne pas modifier">
					</div>
					<div class="form-group">
						<label>Lien</label>
						<input type="text" class="form-control" name="lien" id="socialFieldLien" placeholder="https://...">
					</div>
					<div class="form-group">
						<label>Email de récupération</label>
						<input type="text" class="form-control" name="email_recuperation" id="socialFieldEmail">
					</div>
					<div class="form-group">
						<label>Téléphone de récupération</label>
						<input type="text" class="form-control" name="telephone_recuperation" id="socialFieldTel">
					</div>
					<div class="form-group">
						<label>Remarque</label>
						<textarea class="form-control" name="remarque" id="socialFieldRemarque" rows="2"></textarea>
					</div>
					<div class="msgbox"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
					<button type="submit" class="btn btn-primary">Enregistrer</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(function () {
		var plateformeLabels = <?php echo json_encode(array_map(function ($d) {
			return $d['label'];
		}, clientsocial::$plateformes)); ?>;

		// Champ "Nom de la plateforme" visible uniquement pour un accès libre (__custom__ à
		// l'ajout, ou une plateforme qui n'est pas dans la liste fixe des 6 à la modification) -
		// sa valeur alimente le champ caché réellement soumis (#socialFieldPlateforme).
		function afficherChampPlateformeLibre(valeurInitiale) {
			$('#socialFieldPlateformeGroup').show();
			$('#socialFieldPlateformeText').val(valeurInitiale === '__custom__' ? '' : valeurInitiale);
			$('#socialFieldPlateforme').val(valeurInitiale === '__custom__' ? '' : valeurInitiale);
		}
		$('#socialFieldPlateformeText').on('input', function () {
			$('#socialFieldPlateforme').val($(this).val());
		});

		$('.social-account-add-btn').on('click', function () {
			var plateforme = $(this).data('plateforme');
			$('#socialAccountForm')[0].reset();
			$('#socialFieldId').val('');
			$('#socialFieldPlateformeGroup').hide();
			if (plateforme === '__custom__') {
				afficherChampPlateformeLibre('__custom__');
				$('#socialAccountModalTitle').text('Ajouter un accès');
			} else {
				$('#socialFieldPlateforme').val(plateforme);
				$('#socialAccountModalTitle').text('Ajouter — ' + plateformeLabels[plateforme]);
			}
			$('#socialAccountModal').modal('show');
		});

		$('.social-account-edit-btn').on('click', function () {
			var $btn = $(this);
			var plateforme = $btn.data('plateforme');
			var estLibre = $btn.data('custom') === 1 || $btn.data('custom') === '1';
			$('#socialAccountForm')[0].reset();
			$('#socialFieldId').val($btn.data('id'));
			$('#socialFieldPlateformeGroup').hide();
			if (estLibre) {
				afficherChampPlateformeLibre(plateforme);
				$('#socialAccountModalTitle').text('Modifier — ' + plateforme);
			} else {
				$('#socialFieldPlateforme').val(plateforme);
				$('#socialAccountModalTitle').text('Modifier — ' + plateformeLabels[plateforme]);
			}
			$('#socialFieldLogin').val($btn.data('login'));
			$('#socialFieldLien').val($btn.data('lien'));
			$('#socialFieldEmail').val($btn.data('email'));
			$('#socialFieldTel').val($btn.data('tel'));
			$('#socialFieldRemarque').val($btn.data('remarque'));
			$('#socialAccountModal').modal('show');
		});

		$('#socialAccountForm').on('submit', function (e) {
			e.preventDefault();
			var id = $('#socialFieldId').val();
			var task = id ? 'editClientSocial' : 'addClientSocial';
			var data = $(this).serialize();
			$.post('components/com_client/controleurs/router.php?task=' + task, data, function (resp) {
				if (parseInt(resp) === 1) {
					document.location.reload();
				} else {
					$('#socialAccountForm .msgbox').html('<div class="alert alert-danger p-2 small">Erreur lors de l\'enregistrement.</div>');
				}
			});
		});

		$('.social-account-delete-btn').on('click', function () {
			if (!confirm('Supprimer ces identifiants ?')) {
				return;
			}
			var id = $(this).data('id');
			$.post('components/com_client/controleurs/router.php?task=deleteClientSocial', { id: id }, function (resp) {
				if (parseInt(resp) === 1) {
					document.location.reload();
				} else {
					alert('Erreur lors de la suppression.');
				}
			});
		});

		$('.social-password-reveal-btn').on('click', function () {
			var $btn = $(this);
			var $wrap = $btn.closest('.social-account-password');
			var $masked = $wrap.find('.social-password-masked');
			var $plain = $wrap.find('.social-password-plain');
			if ($plain.is(':visible')) {
				$plain.hide();
				$masked.show();
				$btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
				return;
			}
			$.post('components/com_client/controleurs/router.php?task=revealClientSocialPassword', { id: $wrap.data('id') }, function (resp) {
				var data = typeof resp === 'string' ? JSON.parse(resp) : resp;
				if (data.error) {
					alert('Accès refusé ou identifiant introuvable.');
					return;
				}
				$plain.text(data.password !== '' ? data.password : '(vide)').show();
				$masked.hide();
				$btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
			}, 'json');
		});

		// --- Accès temporaire ---
		$('#socialGenerateTokenBtn').on('click', function () {
			$('#socialTokenStep1').show();
			$('#socialTokenStep2').hide();
			$('#socialTokenGenerateBtn').show();
			$('#socialTokenModal .msgbox').empty();
			$('#socialTokenModal').modal('show');
		});
		$('#socialTokenScope').on('change', function () {
			$('#socialTokenPlateformeGroup').toggle($(this).val() === 'specific');
		});
		$('#socialTokenGenerateBtn').on('click', function () {
			var $btn = $(this);
			var scope = $('#socialTokenScope').val();
			var data = {
				id_client: <?php echo $client->getId(); ?>,
				scope_type: scope,
				scope_plateforme: scope === 'specific' ? $('#socialTokenPlateforme').val() : '',
				send_slack: $('#socialTokenSlack').is(':checked') ? 1 : 0
			};
			$btn.prop('disabled', true);
			$.post('components/com_client/controleurs/router.php?task=generateClientSocialToken', data, function (resp) {
				$btn.prop('disabled', false);
				var result = typeof resp === 'string' ? JSON.parse(resp) : resp;
				if (result.error) {
					$('#socialTokenModal .msgbox').html('<div class="alert alert-danger p-2 small mt-2">Erreur : accès refusé ou champs manquants.</div>');
					return;
				}
				$('#socialTokenResultUrl').val(result.url);
				$('#socialTokenResultCode').val(result.code);
				$('#socialTokenStep1').hide();
				$('#socialTokenStep2').show();
				$btn.hide();
			}, 'json');
		});
		$(document).on('click', '.social-token-copy-btn', function () {
			var input = document.getElementById($(this).data('target'));
			input.select();
			document.execCommand('copy');
			$(this).text('Copié !');
		});
		$('.social-token-revoke-btn').on('click', function () {
			if (!confirm('Révoquer cet accès temporaire ? Il ne sera plus utilisable, même avec le bon code.')) {
				return;
			}
			var id = $(this).data('id');
			$.post('components/com_client/controleurs/router.php?task=revokeClientSocialToken', { id: id }, function (resp) {
				if (parseInt(resp) === 1) {
					document.location.reload();
				} else {
					alert('Erreur lors de la révocation.');
				}
			});
		});
	});
</script>
