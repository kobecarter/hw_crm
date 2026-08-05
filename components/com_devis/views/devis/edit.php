<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Modifier devis</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_devis">Devis</a></li>
						<li class="breadcrumb-item active">Modifier devis</li>
					</ul>
				</div>
				<div class="col-auto">
					<?php
					$facture = facture::findByDevis($devis->getId(),$_SESSION['agence']);
					if($devis->getStatu() == 4 && !$facture):
					?>
					<a href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>" class="btn btn-success mr-1 create-invoice" data-toggle="tooltip" data-placement="top" data-original-title="+ Facture">
						<i class="fa fa-file-invoice"></i>
					</a>
					<?php endif;?>
					<a href="components/com_devis/controleurs/router.php?task=pdfDevisTexte&id=<?php echo $devis->getId(); ?>" target="_blank" class="btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" data-original-title="PDF">
						<i class="far fa-file-pdf"></i>
					</a>
					<a href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>" target="_blank" class="btn btn-info mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Afficher">
						<i class="far fa-eye"></i>
					</a>
					<?php
						$espaceClientLink = $siteURL . "index.php?option=com_elogin";
						$messageDevis = "Bonjour " . $devis->getClient()->getPrenom() . ",\n\n"
							. "Nous espérons que vous allez bien ! ✨\n\n"
							. "C'est avec grand plaisir que nous vous transmettons votre devis N°" . $devis->getNumero() . ", préparé avec la plus grande attention pour répondre au mieux à vos besoins.\n\n"
							. "Vous pouvez à tout moment retrouver ce devis, ainsi que l'ensemble de vos devis et factures, directement depuis votre espace client :\n" . $espaceClientLink . "\n(également accessible depuis notre application mobile).\n\n"
							. "N'hésitez pas à revenir vers nous pour la moindre question, nous sommes toujours ravis de vous accompagner.\n\n"
							. "Belle journée à vous,\nL'équipe Hello World";
						$telClient = preg_replace('/[^0-9+]/', '', $devis->getClient()->getTel());
					?>
					<button type="button" class="btn btn-secondary mr-1 send-email-devis" data-id="<?php echo $devis->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Envoyer par email (PDF joint)" style="position:relative;">
						<i class="far fa-envelope email-devis-icon"></i>
						<span class="email-reminder-badge" style="display:none;position:absolute;top:-3px;right:-3px;width:11px;height:11px;border-radius:50%;background:#dc3545;border:2px solid #fff;"></span>
					</button>
					<a href="https://wa.me/<?php echo $telClient; ?>?text=<?php echo rawurlencode($messageDevis); ?>" target="_blank" class="btn btn-success mr-1 whatsapp-send-devis" data-devis-id="<?php echo $devis->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Envoyer par WhatsApp" style="position:relative;">
						<i class="fab fa-whatsapp whatsapp-devis-icon"></i>
						<span class="whatsapp-reminder-badge" style="display:none;position:absolute;top:-3px;right:-3px;width:11px;height:11px;border-radius:50%;background:#dc3545;border:2px solid #fff;"></span>
					</a>
					<button type="button" class="btn btn-dark mr-1 send-slack-devis" data-id="<?php echo $devis->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Envoyer sur Slack pour validation">
						<i class="fab fa-slack"></i>
					</button>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<style>
			@keyframes devisIconBlink {
				0%, 100% { opacity: 1; }
				50% { opacity: 0.15; }
			}
			.devis-icon-blink {
				animation: devisIconBlink 1s infinite;
			}
		</style>

		<!-- Popup de confirmation envoi email / WhatsApp -->
		<div id="dialog-devis-notif" class="modal custom-modal fade" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body text-center py-4">
						<h3 id="devis-notif-icon" style="font-size:2.5rem;"></h3>
						<p id="devis-notif-text" class="mb-0" style="font-size:1.05rem;"></p>
					</div>
					<div class="modal-footer justify-content-center border-0 pt-0">
						<button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
					</div>
				</div>
			</div>
		</div>
		<!-- /Popup de confirmation -->

		<script>
			// Feedback visuel bref sur le bouton (vert = succès, rouge = erreur)
			function flashDevisActionButton($btn, ok) {
				var originalClass = $btn.data('original-class') || $btn.attr('class');
				$btn.data('original-class', originalClass);
				$btn.removeClass('btn-secondary btn-dark').addClass(ok ? 'btn-success' : 'btn-danger');
				setTimeout(function () {
					$btn.attr('class', originalClass);
				}, 2000);
			}

			// Popup qui s'affiche pour confirmer l'envoi (email / WhatsApp), avec emoji + auto-fermeture
			function showDevisActionPopup(icon, text) {
				$('#devis-notif-icon').text(icon);
				$('#devis-notif-text').html(text);
				$('#dialog-devis-notif').modal('show');
				clearTimeout(window._devisNotifTimeout);
				window._devisNotifTimeout = setTimeout(function () {
					$('#dialog-devis-notif').modal('hide');
				}, 4000);
			}

			var devisId = <?php echo $devis->getId(); ?>;
			var devisEmailSentKey = 'email_sent_devis_' + devisId;
			var devisWaSentKey = 'wa_sent_devis_' + devisId;

			// Icône clignotante + petit cercle rouge tant que l'action (email / WhatsApp) n'a pas été faite pour ce devis
			function refreshDevisIconBlink() {
				var emailDone = !!localStorage.getItem(devisEmailSentKey);
				var waDone = !!localStorage.getItem(devisWaSentKey);
				$('.email-devis-icon').toggleClass('devis-icon-blink', !emailDone);
				$('.whatsapp-devis-icon').toggleClass('devis-icon-blink', !waDone);
				$('.email-reminder-badge').toggle(!emailDone);
				$('.whatsapp-reminder-badge').toggle(!waDone);
			}
			refreshDevisIconBlink();

			$(document).on('click', '.send-email-devis', function () {
				var $btn = $(this);
				var id = $btn.data('id');
				$btn.prop('disabled', true);
				$.post("components/com_devis/controleurs/router.php?task=sendEmailDevis", { id: id }, function (response) {
					$btn.prop('disabled', false);
					flashDevisActionButton($btn, !!response.success);
					if (response.success) {
						if (typeof response.statu !== 'undefined' && window.updateDevisStatusBadge) {
							$("#status-select").val(response.statu);
							updateDevisStatusBadge(response.statu);
						}
						localStorage.setItem(devisEmailSentKey, '1');
						refreshDevisIconBlink();
						if (!localStorage.getItem(devisWaSentKey)) {
							showDevisActionPopup('📧✅', '<strong>Email envoyé au client !</strong><br><br>😠 N\'oubliez pas : la confirmation par <strong>WhatsApp est obligatoire</strong> !');
						} else {
							showDevisActionPopup('📧✅', '<strong>Email envoyé au client !</strong> 🎉');
						}
					} else {
						showDevisActionPopup('❌', 'Erreur lors de l\'envoi de l\'email : ' + response.message);
					}
				}, 'json').fail(function () {
					$btn.prop('disabled', false);
					flashDevisActionButton($btn, false);
					showDevisActionPopup('❌', 'Erreur réseau lors de l\'envoi de l\'email.');
				});
			});

			$(document).on('click', '.send-slack-devis', function () {
				var $btn = $(this);
				var id = $btn.data('id');
				$btn.prop('disabled', true);
				$.post("components/com_devis/controleurs/router.php?task=sendSlackDevis", { id: id }, function (response) {
					$btn.prop('disabled', false);
					flashDevisActionButton($btn, !!response.success);
					if (response.success) {
						if (typeof response.statu !== 'undefined' && window.updateDevisStatusBadge) {
							$("#status-select").val(response.statu);
							updateDevisStatusBadge(response.statu);
						}
					} else {
						console.error("Erreur envoi Slack devis :", response.message);
					}
				}, 'json').fail(function () {
					$btn.prop('disabled', false);
					flashDevisActionButton($btn, false);
					console.error("Erreur réseau lors de l'envoi vers Slack.");
				});
			});

			// Popup de confirmation au clic sur WhatsApp
			(function () {
				var $waBtn = $('.whatsapp-send-devis');
				$waBtn.on('click', function () {
					localStorage.setItem(devisWaSentKey, '1');
					refreshDevisIconBlink();
					if (localStorage.getItem(devisEmailSentKey)) {
						showDevisActionPopup('🎉😄', '<strong>Bravo !</strong> Vous avez complété les méthodes de confirmation (email + WhatsApp) !');
					} else {
						showDevisActionPopup('💬✅', '<strong>Message WhatsApp envoyé !</strong>');
					}
				});
			})();
		</script>

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
					    <div class="mb-5">
					       <?php include("wizard_process.php"); ?>
					    </div>
						<?php include("form.php"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>