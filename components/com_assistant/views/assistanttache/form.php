<?php
// Type + fiche actuellement liés (édition) ou pré-sélectionnés via l'URL (ex: widget "+ Ajouter"
// depuis la fiche d'un client) - utilisés pour présélectionner les deux select ci-dessous.
$relTypeCourant = isset($tache) ? $tache->getTypeRelation() : (isset($preselectTypeRelation) ? $preselectTypeRelation : null);
$relIdCourant = isset($tache) ? $tache->getIdRelation() : (isset($preselectIdRelation) ? $preselectIdRelation : null);
?>
<form method="post" action="<?php echo $action; ?>" id="assistantTacheForm">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Type<span class="text-danger"> * </span></label>
				<select class="select" name="type" required>
					<?php $typeCourant = isset($tache) ? $tache->getType() : (isset($preselectType) ? $preselectType : null); ?>
					<option value="tache" <?= ($typeCourant == 'tache') ? 'selected' : '' ?>>Tâche</option>
					<option value="rendez_vous" <?= ($typeCourant == 'rendez_vous') ? 'selected' : '' ?>>Rendez-vous / Meeting</option>
					<option value="suivi_client" <?= ($typeCourant == 'suivi_client') ? 'selected' : '' ?>>Suivi client</option>
				</select>
			</div>
		</div>

		<div class="col-md-5">
			<div class="form-group">
				<label>Titre<span class="text-danger"> * </span></label>
				<input type="text" class="form-control" name="titre" value="<?php if (isset($tache)) echo $tache->getTitre(); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Date / heure</label>
				<input type="datetime-local" class="form-control" name="date_tache" value="<?php if (isset($tache) && $tache->getDateTache()) echo date('Y-m-d\TH:i', strtotime($tache->getDateTache())); ?>">
			</div>
		</div>

		<!-- Lié à : client / fournisseur / employé / réclamation / banque - chacun avec sa propre
		     fiche à choisir - ou réunion, une catégorie sans fiche associée (pas de module dédié),
		     on la coche simplement. "Aucun" = pas de relation du tout. Un seul <select> visible à
		     la fois (assistant-relation.js gère l'affichage), tous partagent name="id_relation" -
		     seul celui qui est actif (non disabled) est réellement soumis avec le formulaire. -->
		<div class="col-md-4">
			<div class="form-group">
				<label>Lié à <small class="text-muted">(optionnel)</small></label>
				<select class="select" name="type_relation" id="assistantTypeRelation">
					<option value="">Aucun</option>
					<?php foreach (assistanttache::$relationTypes as $cle => $infos) : ?>
						<option value="<?= $cle ?>" <?= ($relTypeCourant == $cle) ? 'selected' : '' ?>><?= $infos['label'] ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="col-md-8" id="assistantRelationRecordWrap">
			<div class="form-group assistant-relation-record" data-relation-type="client" style="display:none;">
				<label>Client</label>
				<select class="chosen-select" name="id_relation" disabled>
					<option value="">Sélectionner</option>
					<?php foreach ($clients as $c) : ?>
						<option value="<?= $c->getId() ?>" <?= ($relTypeCourant == 'client' && $relIdCourant == $c->getId()) ? 'selected' : '' ?>><?= trim($c->getRaisonSocial()) !== '' ? $c->getRaisonSocial() : trim($c->getNom() . ' ' . $c->getPrenom()) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group assistant-relation-record" data-relation-type="fournisseur" style="display:none;">
				<label>Fournisseur</label>
				<select class="chosen-select" name="id_relation" disabled>
					<option value="">Sélectionner</option>
					<?php foreach ($fournisseurs as $f) : ?>
						<option value="<?= $f->getId() ?>" <?= ($relTypeCourant == 'fournisseur' && $relIdCourant == $f->getId()) ? 'selected' : '' ?>><?= trim($f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getNom() . ' ' . $f->getPrenom()) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group assistant-relation-record" data-relation-type="rh" style="display:none;">
				<label>Employé</label>
				<select class="chosen-select" name="id_relation" disabled>
					<option value="">Sélectionner</option>
					<?php foreach ($employes as $e) : ?>
						<option value="<?= $e->getId() ?>" <?= ($relTypeCourant == 'rh' && $relIdCourant == $e->getId()) ? 'selected' : '' ?>><?= $e->getFirstName() . ' ' . $e->getLastName() ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group assistant-relation-record" data-relation-type="reclamation" style="display:none;">
				<label>Réclamation</label>
				<select class="chosen-select" name="id_relation" disabled>
					<option value="">Sélectionner</option>
					<?php foreach ($reclamations as $r) : ?>
						<option value="<?= $r->getId() ?>" <?= ($relTypeCourant == 'reclamation' && $relIdCourant == $r->getId()) ? 'selected' : '' ?>><?= $r->getSujet() ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group assistant-relation-record" data-relation-type="banque" style="display:none;">
				<label>Banque</label>
				<select class="chosen-select" name="id_relation" disabled>
					<option value="">Sélectionner</option>
					<?php foreach ($banks as $b) : ?>
						<?php $labelBanque = $b->getLabel() !== null && $b->getLabel() !== '' ? $b->getLabel() : ($b->getRaisonSociale() !== null && $b->getRaisonSociale() !== '' ? $b->getRaisonSociale() : $b->getBanque()); ?>
						<option value="<?= $b->getId() ?>" <?= ($relTypeCourant == 'banque' && $relIdCourant == $b->getId()) ? 'selected' : '' ?>><?= $labelBanque ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<p class="assistant-relation-none text-muted mb-0" style="display:none;">Aucune fiche à choisir pour ce type - il suffit de l'avoir sélectionné ci-contre.</p>
		</div>

		<div class="col-md-4">
			<label class="row form-group toggle-switch">
				<span class="col-8 col-sm-8 toggle-switch-content ml-0">
					<span class="d-block text-dark">Terminé</span>
				</span>
				<span class="col-4 col-sm-4">
					<input type="checkbox" name="termine" class="toggle-switch-input" <?php if (isset($tache) && $tache->isTermine()) echo "checked"; ?>>
					<span class="toggle-switch-label ml-auto">
						<span class="toggle-switch-indicator"></span>
					</span>
				</span>
			</label>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Notes</label>
				<textarea name="remarque" class="form-control" rows="3"><?php if (isset($tache)) echo $tache->getRemarque(); ?></textarea>
			</div>
		</div>

		<?php if (isset($tache)) : ?>
			<input type="hidden" name="id" value="<?php echo $tache->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?= $submitName; ?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?php echo $submitValue; ?></button>
	</div>
</form>

<script>
	$(function() {

		// "Lié à" : un seul select de fiche visible/actif à la fois selon le type choisi (aucun
		// pour "Aucun"/"Réunion" - ce dernier n'a pas de fiche, juste une catégorie qu'on coche).
		function assistantAppliquerTypeRelation() {
			var type = $('#assistantTypeRelation').val();
			$('.assistant-relation-record').hide().find('select').prop('disabled', true);
			$('.assistant-relation-none').hide();
			if (!type) {
				return;
			}
			var $zone = $('.assistant-relation-record[data-relation-type="' + type + '"]');
			if ($zone.length) {
				$zone.show().find('select').prop('disabled', false).trigger('chosen:updated');
			} else {
				// "reunion" (ou tout futur type sans fiche) : rien à choisir, juste le type coché.
				$('.assistant-relation-none').show();
			}
		}
		$('#assistantTypeRelation').on('change', assistantAppliquerTypeRelation);
		assistantAppliquerTypeRelation();

		// envoi du formulaire en ajax
		$('form#assistantTacheForm').ajaxForm({
			beforeSubmit: function() {
				$("#assistantTacheForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				$("#assistantTacheForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Élément ajouté avec succès";
				if ($(".submit").attr("name") === "edit") {
					msgsucces = "Élément modifié avec succès";
				}
				if (parseInt(theResponse) === 1) {
					$('#assistantTacheForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

					setTimeout(function() {
						document.location = "index.php?option=com_assistant";
					}, 1500)

				} else if (parseInt(theResponse) === 0) {
					$('#assistantTacheForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				} else {
					$('#assistantTacheForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
	})
</script>
