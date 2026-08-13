<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Ajouter un contrat</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_contract">Contrats</a></li>
						<li class="breadcrumb-item active">Ajouter un contrat</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card glass-page">
					<div class="card-body">

						<!-- Stepper -->
						<div class="contract-wizard-stepper d-flex justify-content-center mb-4">
							<div class="contract-wizard-step active" data-step="1">
								<span class="contract-wizard-step-num">1</span>
								<span class="contract-wizard-step-label">Informations</span>
							</div>
							<div class="contract-wizard-step-line"></div>
							<div class="contract-wizard-step" data-step="2">
								<span class="contract-wizard-step-num">2</span>
								<span class="contract-wizard-step-label">Contenu</span>
							</div>
							<div class="contract-wizard-step-line"></div>
							<div class="contract-wizard-step" data-step="3">
								<span class="contract-wizard-step-num">3</span>
								<span class="contract-wizard-step-label">Récapitulatif</span>
							</div>
						</div>

						<div class="msgbox"></div>

						<!-- ÉTAPE 1 : Informations -->
						<div class="contract-wizard-pane" id="wizardEtape1">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Devis <span class="text-danger">*</span></label>
										<select class="chosen-select form-control" id="wzIdDevis" required>
											<option value="">Choisir un devis...</option>
											<?php foreach ($devises as $d) :?>
												<?php $nomClient = trim((string) $d->getClient()->getRaisonSocial()) !== '' ? $d->getClient()->getRaisonSocial() : trim($d->getClient()->getPrenom() . ' ' . $d->getClient()->getNom()); ?>
											<option value="<?= $d->getId() ?>">N°<?= htmlspecialchars($d->getNumero()) ?> — <?= htmlspecialchars($nomClient) ?> (<?= number_format($d->getTotal(), 2, ',', ' ') ?> <?= htmlspecialchars($d->getDevise()) ?>)</option>
											<?php endforeach;?>
										</select>
										<p class="text-muted mt-1 mb-0" style="font-size:0.8rem;">Seuls les devis déjà acceptés et sans contrat existant apparaissent ici.</p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Titre</label>
										<input type="text" class="form-control" id="wzTitre" placeholder="Généré automatiquement si laissé vide">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Durée</label>
										<input type="text" class="form-control" id="wzDuration" placeholder="12 mois">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Garantie</label>
										<input type="text" class="form-control" id="wzGarantie">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Nombre de paiement</label>
										<input type="number" min="0" class="form-control" id="wzNombreDePaiement" value="1">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Ville</label>
										<input type="text" class="form-control" id="wzVille" placeholder="Marrakech">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Tribunal compétent</label>
										<input type="text" class="form-control" id="wzTribunal" placeholder="Tribunal de Commerce de Marrakech">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label>Date</label>
										<div class="cal-icon">
											<input type="text" class="form-control datetimepicker" id="wzDate" value="<?= date('d/m/Y') ?>">
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<label class="row form-group toggle-switch">
										<span class="col-12 toggle-switch-content ml-0">
											<span class="d-block text-dark">Afficher la signature</span>
										</span>
										<span class="col-12">
											<input type="checkbox" id="wzShowSignature" class="toggle-switch-input">
											<span class="toggle-switch-label mr-auto mt-2">
												<span class="toggle-switch-indicator"></span>
											</span>
										</span>
									</label>
								</div>
							</div>
							<div class="text-right mt-3">
								<button type="button" class="btn btn-primary" id="wzEtape1SuivantBtn"><i class="fa fa-magic mr-1"></i> Générer et continuer</button>
							</div>
						</div>

						<!-- ÉTAPE 2 : Contenu (WYSIWYG) -->
						<div class="contract-wizard-pane d-none" id="wizardEtape2">
							<p class="text-muted">Contenu généré automatiquement à partir des prestations du devis — modifiable librement ci-dessous.</p>
							<textarea id="wzCorpsEditeur"></textarea>
							<div class="d-flex justify-content-between mt-3">
								<button type="button" class="btn btn-white" id="wzEtape2PrecedentBtn"><i class="fa fa-arrow-left mr-1"></i> Précédent</button>
								<button type="button" class="btn btn-primary" id="wzEtape2SuivantBtn">Suivant <i class="fa fa-arrow-right ml-1"></i></button>
							</div>
						</div>

						<!-- ÉTAPE 3 : Récapitulatif -->
						<div class="contract-wizard-pane d-none" id="wizardEtape3">
							<div class="contract-wizard-recap">
								<h5 class="mb-3">Récapitulatif</h5>
								<table class="table table-borderless mb-0">
									<tr><td class="text-muted" style="width:200px;">Devis</td><td id="recapDevis">—</td></tr>
									<tr><td class="text-muted">Client</td><td id="recapClient">—</td></tr>
									<tr><td class="text-muted">Titre</td><td id="recapTitre">—</td></tr>
									<tr><td class="text-muted">Durée</td><td id="recapDuration">—</td></tr>
									<tr><td class="text-muted">Ville / Tribunal</td><td id="recapVilleTribunal">—</td></tr>
								</table>
							</div>
							<p class="text-muted mt-4 mb-2">Que souhaitez-vous faire de ce contrat ?</p>
							<div class="d-flex flex-wrap" style="gap:10px;">
								<button type="button" class="btn btn-success" id="wzEnvoyerEmailBtn"><i class="fa fa-envelope mr-1"></i> Envoyer au client par email pour signature</button>
								<button type="button" class="btn btn-primary" id="wzEnvoyerSlackBtn"><i class="fab fa-slack mr-1"></i> Envoyer par Slack pour validation</button>
								<button type="button" class="btn btn-white" id="wzBrouillonBtn"><i class="fa fa-save mr-1"></i> Enregistrer comme brouillon</button>
							</div>
							<div class="mt-3">
								<button type="button" class="btn btn-white" id="wzEtape3PrecedentBtn"><i class="fa fa-arrow-left mr-1"></i> Précédent</button>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->

<style>
.contract-wizard-stepper { align-items: center; }
.contract-wizard-step { display: flex; flex-direction: column; align-items: center; opacity: 0.4; }
.contract-wizard-step.active, .contract-wizard-step.done { opacity: 1; }
.contract-wizard-step-num { width: 36px; height: 36px; border-radius: 50%; background: var(--brand-grad, #6a35f0); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; }
.contract-wizard-step:not(.active):not(.done) .contract-wizard-step-num { background: #cbd5e1; }
.contract-wizard-step-label { font-size: 0.78rem; margin-top: 4px; font-weight: 600; }
.contract-wizard-step-line { width: 60px; height: 2px; background: #cbd5e1; margin: 0 10px 20px; }
.contract-wizard-recap { background: #f8f8fc; border-radius: 12px; padding: 1.2rem; }
</style>

<script type="text/javascript">
$(function () {
	var idContractCourant = null;
	var recapCourant = null;

	function allerEtape(numero) {
		$('.contract-wizard-pane').addClass('d-none');
		$('#wizardEtape' + numero).removeClass('d-none');
		$('.contract-wizard-step').each(function () {
			var s = parseInt($(this).data('step'), 10);
			$(this).toggleClass('active', s === numero).toggleClass('done', s < numero);
		});
	}

	function afficherErreur(message) {
		$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show p-2" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
	}
	function afficherSucces(message) {
		$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show p-2" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
	}

	$('#wzEtape1SuivantBtn').on('click', function () {
		if (!$('#wzIdDevis').val()) {
			afficherErreur('Choisissez un devis.');
			return;
		}
		var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Génération...');
		$.post('components/com_contract/controleurs/router.php?task=genererContratDepuisDevis', {
			id_devis: $('#wzIdDevis').val(),
			titre_contract: $('#wzTitre').val(),
			duration_contract: $('#wzDuration').val(),
			garantie_contract: $('#wzGarantie').val(),
			ville_contract: $('#wzVille').val(),
			tribunal_contract: $('#wzTribunal').val(),
			date_contract: $('#wzDate').val(),
			nombre_de_paiement_contract: $('#wzNombreDePaiement').val(),
			show_signature_contract: $('#wzShowSignature').is(':checked') ? 1 : 0
		}, function (response) {
			$btn.prop('disabled', false).html('<i class="fa fa-magic mr-1"></i> Générer et continuer');
			if (!response.success) {
				afficherErreur(response.message || 'Erreur lors de la génération');
				return;
			}
			idContractCourant = response.id_contract;
			recapCourant = response.recap;
			if (!CKEDITOR.instances.wzCorpsEditeur) {
				$('#wzCorpsEditeur').val(response.corps_genere);
				CKEDITOR.replace('wzCorpsEditeur', { allowedContent: true, height: 500 });
			} else {
				CKEDITOR.instances.wzCorpsEditeur.setData(response.corps_genere);
			}
			allerEtape(2);
		}, 'json').fail(function () {
			$btn.prop('disabled', false).html('<i class="fa fa-magic mr-1"></i> Générer et continuer');
			afficherErreur('Erreur réseau lors de la génération');
		});
	});

	$('#wzEtape2PrecedentBtn').on('click', function () { allerEtape(1); });

	$('#wzEtape2SuivantBtn').on('click', function () {
		var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Enregistrement...');
		$.post('components/com_contract/controleurs/router.php?task=enregistrerCorpsContrat', {
			id: idContractCourant,
			corps_genere: CKEDITOR.instances.wzCorpsEditeur.getData()
		}, function (response) {
			$btn.prop('disabled', false).html('Suivant <i class="fa fa-arrow-right ml-1"></i>');
			if (!response.success) {
				afficherErreur(response.message || "Erreur lors de l'enregistrement");
				return;
			}
			$('#recapDevis').text('N°' + recapCourant.devis_numero);
			$('#recapClient').text(recapCourant.client);
			$('#recapTitre').text(recapCourant.titre);
			$('#recapDuration').text(recapCourant.duration);
			$('#recapVilleTribunal').text(recapCourant.ville + ' / ' + recapCourant.tribunal);
			allerEtape(3);
		}, 'json');
	});

	$('#wzEtape3PrecedentBtn').on('click', function () { allerEtape(2); });

	$('#wzEnvoyerEmailBtn, #wzEnvoyerSlackBtn, #wzBrouillonBtn').on('click', function () {
		var $btn = $(this);
		var task = $btn.attr('id') === 'wzEnvoyerEmailBtn' ? 'envoyerContratEmailSignature'
			: ($btn.attr('id') === 'wzEnvoyerSlackBtn' ? 'envoyerContratSlack' : null);

		if (!task) {
			// Enregistrer comme brouillon : déjà sauvegardé à l'étape 2, il ne reste qu'à sortir de l'assistant.
			window.location.href = 'index.php?option=com_contract&task=editeur&id=' + idContractCourant;
			return;
		}

		var libelleInitial = $btn.html();
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Envoi...');
		$.post('components/com_contract/controleurs/router.php?task=' + task, {
			id: idContractCourant
		}, function (response) {
			$btn.prop('disabled', false).html(libelleInitial);
			if (!response.success) {
				afficherErreur(response.message || "Erreur lors de l'envoi");
				return;
			}
			afficherSucces('Contrat envoyé avec succès.');
			setTimeout(function () {
				window.location.href = 'index.php?option=com_contract&task=editeur&id=' + idContractCourant;
			}, 1200);
		}, 'json');
	});
});
</script>
