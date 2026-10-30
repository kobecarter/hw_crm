<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?php echo $action; ?>" id="relanceForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-user"></i> Client &amp; facture concernés</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Client<span class="text-danger"> * </span></label>
				<select class="chosen-select" name="client" id="client_select" required>
					<option value="">Sélectionner</option>
					<?php foreach ($clients as $value) : ?>
						<?php 
							$sl = null;
							if((isset($relance) && $relance->getClient()->getId() == $value->getId()) || (isset($client) && $client->getId() == $value->getId()))
							{
								$sl = "selected";
							} ?>
						<option value="<?php echo $value->getId() ?>" <?php echo $sl; ?>><?php echo $value->getNom() . ' ' . $value->getPrenom() . ' - ' . $value->getRaisonSocial(); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Catégorie</label>
				<?php
					$categoriesRelance = array(
						'facture' => 'Facture',
						'paiement' => 'Paiement',
						'expiration_domaine' => 'Expiration de domaine',
						'hebergement' => 'Hébergement',
						'autre_service' => 'Autre service',
					);
				?>
				<select class="chosen-select" name="categorie" id="categorie_select">
					<option value="">Sélectionner</option>
					<?php foreach ($categoriesRelance as $valeur => $libelle) :?>
					<option value="<?php echo $valeur; ?>" <?php echo isset($relance) && $relance->getCategorie() == $valeur ? 'selected' : ''; ?>><?php echo $libelle; ?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group" id="liste_selon_categorie">
				<?php /* Rempli dynamiquement selon client+catégorie, voir getListeSelonCategorie() - facture/règlement/domaine selon la catégorie choisie, vide pour hébergement/autre service. */ ?>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-info-circle"></i> Détails de la relance</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Type<span class="text-danger"> * </span></label>
				<?php
					$selected1 = null;
					if(isset($relance) && $relance->getType() == "Téléphone"){
						$selected1 = "selected";
					}
					$selected2 = null;
					if(isset($relance) && $relance->getType() == "Email"){
						$selected2 = "selected";
					}
					$selected3 = null;
					if(isset($relance) && $relance->getType() == "directement"){
						$selected3 = "selected";
					}
				?>
				<select class="select" name="type" required>
					<option value="">Sélectionner</option>
					<option value="Téléphone" <?php echo $selected1;?>>Téléphone</option>
					<option value="Email" <?php echo $selected2;?>>Email</option>
					<option value="directement" <?php echo $selected3;?>>directement</option>
				</select>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
					<input type="date" class="form-control" name="date" id="date_relance" value="<?php if (isset($relance)) echo $relance->getDate(); ?>" required>
			</div>
		</div>

		<!-- Toggle Switch -->
		<div class="col-md-3">
			<label class="row form-group toggle-switch">
				<span class="col-4 col-sm-4 toggle-switch-content ml-0">
					<span class="d-block text-dark">Traitée</span>
				</span>
				<span class="col-8 col-sm-8">
					<input type="checkbox" name="traite" class="toggle-switch-input" <?php if(isset($relance) && $relance->isTraite()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto" style="float: left;">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>	
		<!-- /Toggle Switch -->

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-robot"></i> Email de relance (généré par IA)</div>
		</div>
		<div class="col-md-12">
			<button type="button" class="btn btn-outline-primary mb-3" id="genererEmailRelanceBtn">
				<span class="spinner-border spinner-border-sm mr-2" id="genererEmailRelanceSpinner" style="display:none;"></span>
				<i class="fa fa-magic mr-1"></i> Générer l'email avec l'IA
			</button>
			<div id="emailRelanceMsgbox"></div>
			<div id="emailRelanceSection" style="display:none;">
				<div class="form-group">
					<label>Sujet</label>
					<input type="text" class="form-control" id="email_sujet_input">
				</div>
				<div class="form-group">
					<label>Corps de l'email</label>
					<textarea id="email_corps_textarea"></textarea>
					<script type="text/javascript">
						var emailRelanceEditor = CKEDITOR.replace('email_corps_textarea', { allowedContent: true });
					</script>
				</div>
				<div class="text-right">
					<button type="button" class="btn btn-success" id="programmerRelanceBtn"><i class="fa fa-clock mr-1"></i> Programmer l'envoi</button>
					<button type="button" class="btn btn-primary" id="envoyerRelanceMaintenantBtn"><i class="fa fa-paper-plane mr-1"></i> Envoyer maintenant</button>
				</div>
			</div>
		</div>

		<div class="col-12">
			<div class="form-section-title"><i class="fa fa-comment-alt"></i> Remarque &amp; justificatif</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque<span class="text-danger"> * </span></label>
				<textarea name="remarque" class="form-control" required><?php if (isset($relance)) echo $relance->getRemarque(); ?></textarea>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label for="photo" class="col-sm-3 col-form-label input-label">Photo</label>
				<div class="col-sm-9">
					<div class="d-flex align-items-center">
						<label class="avatar avatar-xl profile-cover-avatar m-0" for="edit_img">
							<?php $photoLink = isset($relance) && $relance->getPhoto() != '' ? "images/relances/" . $relance->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
							<img id="avatarImg" class="avatar-img" src="<?php echo $photoLink; ?>" alt="Profile Image">
							<input type="file" name="photo[]" id="edit_img">
							<span class="avatar-edit">
								<i data-feather="edit-2" class="avatar-uploader-icon shadow-soft"></i>
							</span>
						</label>
					</div>
				</div>
			</div>
		</div>

		<?php if (isset($relance)) : ?>
			<input type="hidden" name="id" value="<?php echo $relance->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	// Relance existante rouverte pour modification : ses choix (facture/règlement/domaine liés,
	// email déjà généré) à restaurer une fois la page chargée - voir appliquerEtatInitial()
	// ci-dessous. null en création.
	<?php if (isset($relance)) : ?>
	var relanceInitiale = {
		idFacture: <?php echo json_encode($relance->getFacture() && $relance->getFacture()->getId() ? $relance->getFacture()->getId() : null); ?>,
		idRappel: <?php echo json_encode($relance->getRappel() && $relance->getRappel()->getId() ? $relance->getRappel()->getId() : null); ?>,
		emailSujet: <?php echo json_encode($relance->getEmailSujet()); ?>,
		emailCorps: <?php echo json_encode($relance->getEmailCorps()); ?>
	};
	<?php else : ?>
	var relanceInitiale = null;
	<?php endif; ?>

	$(function() {

		// Liste dynamique (facture/règlement/domaine) selon le couple client+catégorie - voir
		// getListeSelonCategorie(). Se redéclenche sur un changement de l'un OU l'autre champ,
		// puisque la liste dépend des deux à la fois. "preselection" (optionnel) : utilisé
		// uniquement au chargement initial d'une relance existante, pour retrouver la facture/
		// règlement/domaine déjà liés plutôt que de rouvrir la liste vierge - un changement manuel
		// de client/catégorie par l'utilisateur ne doit jamais re-forcer cet ancien choix.
		function rechargerListeSelonCategorie(preselection) {
			var idClient = $("select[name='client']").val();
			var categorie = $("#categorie_select").val();
			if (!idClient || !categorie) {
				$("#liste_selon_categorie").html('');
				return;
			}
			var payload = { id: idClient, categorie: categorie };
			if (preselection) {
				payload.id_facture = preselection.idFacture || '';
				payload.id_rappel = preselection.idRappel || '';
			}
			$.post("components/com_relance/controleurs/router.php?task=getListeSelonCategorie", payload, function(theResponse) {
				$("#liste_selon_categorie").html(theResponse);
				$("#liste_selon_categorie .chosen-select").select2();
			});
		}
		$(document).on("change", "#client_select, #categorie_select", function() {
			rechargerListeSelonCategorie();
		});

		// Restauration de l'état d'une relance existante : liste dépendante (avec son option déjà
		// liée présélectionnée) et email déjà généré/relu, affichés directement sans que
		// l'utilisateur ait besoin de tout refaire pour simplement consulter/corriger.
		if (relanceInitiale) {
			if ($("#categorie_select").val()) {
				rechargerListeSelonCategorie({ idFacture: relanceInitiale.idFacture, idRappel: relanceInitiale.idRappel });
			}
			if (relanceInitiale.emailCorps) {
				$("#email_sujet_input").val(relanceInitiale.emailSujet);
				emailRelanceEditor.setData(relanceInitiale.emailCorps);
				$("#emailRelanceSection").show();
			}
		}

		// Catégorie "paiement" : la table relance ne relie qu'à une facture (pas à un règlement
		// précis) - en choisissant un règlement, on reporte sa facture liée dans le champ caché
		// "facture" (même champ que la catégorie "facture" utilise directement), pour que
		// l'enregistrement/la génération IA n'aient besoin de lire qu'un seul champ "facture"
		// quel que soit le chemin suivi pour y arriver.
		$(document).on("change", "select[name='payment_ref']", function() {
			var factureId = $(this).find(':selected').data('facture') || '';
			$("input[name='facture']").val(factureId);
		});

		// envoi du formulaire en ajax
		$('form#relanceForm').ajaxForm({
			beforeSubmit: function() {
				$("#relanceForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse)
				$("#relanceForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Rappel ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Rappel modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#relanceForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						let client_id = $("select[name='client']").val()
						document.location = "index.php?option=com_relance&task=client&id="+client_id;
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#relanceForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#relanceForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});

		// Génère le corps de l'email via l'IA pour la catégorie choisie (sujet déterministe,
		// voir aiRelanceEmail::libelleCategorie()) et révèle la section édition/envoi.
		$(document).on("click", "#genererEmailRelanceBtn", function() {
			var idClient = $("select[name='client']").val();
			var categorie = $("#categorie_select").val();
			$("#emailRelanceMsgbox").html('');
			if (!idClient || !categorie) {
				$("#emailRelanceMsgbox").html('<div class="alert alert-warning">Choisissez un client et une catégorie avant de générer l\'email.</div>');
				return;
			}
			var $btn = $(this);
			$btn.prop('disabled', true);
			$("#genererEmailRelanceSpinner").css('display', 'inline-block');
			var $domaineOption = $("select[name='rappel_domaine']").find(':selected');
			$.post("components/com_relance/controleurs/router.php?task=genererEmailRelance", {
				client: idClient,
				facture: $("[name='facture']").val(),
				categorie: categorie,
				domaine: $domaineOption.data('domaine') || '',
				date_expiration: $domaineOption.data('expiration') || ''
			}, function(response) {
				$btn.prop('disabled', false);
				$("#genererEmailRelanceSpinner").css('display', 'none');
				if (!response.success) {
					$("#emailRelanceMsgbox").html('<div class="alert alert-danger">' + response.message + '</div>');
					return;
				}
				$("#email_sujet_input").val(response.sujet);
				emailRelanceEditor.setData(response.corps);
				$("#emailRelanceSection").show();
			}).fail(function() {
				$btn.prop('disabled', false);
				$("#genererEmailRelanceSpinner").css('display', 'none');
				$("#emailRelanceMsgbox").html('<div class="alert alert-danger">Erreur réseau lors de la génération.</div>');
			});
		});

		function envoyerOuProgrammerRelance(mode, $btn) {
			var idClient = $("select[name='client']").val();
			var categorie = $("#categorie_select").val();
			var date = $("#date_relance").val();
			if (!idClient || !categorie || !date) {
				$("#emailRelanceMsgbox").html('<div class="alert alert-warning">Client, catégorie et date sont requis.</div>');
				return;
			}
			var payload = {
				id: $("input[name='id']").val() || '',
				client: idClient,
				facture: $("[name='facture']").val(),
				rappel_domaine: $("select[name='rappel_domaine']").val() || '',
				categorie: categorie,
				date: date,
				remarque: $("textarea[name='remarque']").val(),
				email_sujet: $("#email_sujet_input").val(),
				email_corps: emailRelanceEditor.getData(),
				mode: mode
			};
			$btn.prop('disabled', true);
			$.post("components/com_relance/controleurs/router.php?task=enregistrerRelanceEmail", payload, function(response) {
				$btn.prop('disabled', false);
				var parts = String(response).split('|');
				if (parts[0] === '1' && parts[1] === 'envoye') {
					$("#emailRelanceMsgbox").html('<div class="alert alert-success">Relance créée et email envoyé avec succès.</div>');
					setTimeout(function() { document.location = "index.php?option=com_relance&task=client&id=" + idClient; }, 1500);
				} else if (parts[0] === '1' && parts[1] === 'programme') {
					$("#emailRelanceMsgbox").html('<div class="alert alert-success">Relance programmée pour le ' + date + '.</div>');
					setTimeout(function() { document.location = "index.php?option=com_relance&task=client&id=" + idClient; }, 1500);
				} else if (parts[0] === '1' && parts[1] === 'erreur_envoi') {
					$("#emailRelanceMsgbox").html('<div class="alert alert-warning">La relance a été créée mais l\'email n\'a pas pu être envoyé. Réessayez.</div>');
				} else if (parts[0] === '0') {
					$("#emailRelanceMsgbox").html('<div class="alert alert-warning">Veuillez remplir les champs obligatoires.</div>');
				} else {
					$("#emailRelanceMsgbox").html('<div class="alert alert-danger">Erreur lors de l\'enregistrement.</div>');
				}
			}).fail(function() {
				$btn.prop('disabled', false);
				$("#emailRelanceMsgbox").html('<div class="alert alert-danger">Erreur réseau.</div>');
			});
		}

		$(document).on("click", "#envoyerRelanceMaintenantBtn", function() {
			envoyerOuProgrammerRelance('immediat', $(this));
		});
		$(document).on("click", "#programmerRelanceBtn", function() {
			envoyerOuProgrammerRelance('programme', $(this));
		});
	})
</script>