<?php
/**
 * Édition du profil — espace employé (com_dashboard task=myProfileEdit). Exclusif à ce
 * contexte (aucune autre vue ne l'inclut). Champs volontairement limités à photo/téléphone/
 * adresse/ville (voir buildResourceHumaineSelf() côté contrôleur) - matricule/CIN/salaire/
 * statut/profil/dates restent réservés à l'admin.
 */
$empPhotoEdit = $resourcehumaine->getPhoto() ? "./images/resourceshumaines/" . $resourcehumaine->getPhoto() : "./images/default-image.jpeg";
?>
<main class="emp-main">

	<div class="emp-page-header">
		<h1 class="emp-page-title"><i class="fa fa-user-edit mr-2"></i>Modifier mon profil</h1>
		<p class="emp-page-subtitle">Photo, téléphone et adresse — les autres informations sont gérées par les ressources humaines</p>
	</div>

	<div class="emp-card">
		<form method="post" action="components/com_resourcehumaine/controleurs/router.php?task=editMyResourceHumaine" enctype="multipart/form-data" id="editMyRHForm">
			<div class="emp-msgbox msgbox"></div>

			<div style="display:flex;align-items:center;gap:18px;margin-bottom:24px;">
				<div class="emp-hero-avatar-frame" style="width:80px;height:80px;">
					<img id="editMyRHAvatarPreview" src="<?= htmlspecialchars($empPhotoEdit) ?>" onerror="this.src='./images/default-image.jpeg'" alt="">
				</div>
				<div>
					<label class="emp-form-label" style="margin-bottom:6px;">Photo de profil</label>
					<input type="file" name="photo[]" id="editMyRHPhotoInput" accept=".jpg,.jpeg,.png,.gif" class="emp-input">
				</div>
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Téléphone</label>
				<input type="text" class="emp-input" name="phone" value="<?= htmlspecialchars($resourcehumaine->getPhone()) ?>">
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Téléphone secondaire</label>
				<input type="text" class="emp-input" name="second_phone" value="<?= htmlspecialchars($resourcehumaine->getSecondPhone()) ?>">
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Adresse</label>
				<input type="text" class="emp-input" name="address" value="<?= htmlspecialchars($resourcehumaine->getAddress()) ?>">
			</div>

			<div class="emp-form-group">
				<label class="emp-form-label">Ville</label>
				<input type="text" class="emp-input" name="city" value="<?= htmlspecialchars($resourcehumaine->getCity()) ?>">
			</div>

			<input type="hidden" name="id" value="<?= $resourcehumaine->getId() ?>">

			<button type="submit" class="emp-submit-btn">
				<span class="spinner-border spinner-border-sm loading" style="display:none;"></span>
				<span>Enregistrer</span>
			</button>
			<a href="index.php" class="emp-btn-mini" style="width:auto;padding:0 16px;height:44px;margin-left:8px;">Annuler</a>
		</form>
	</div>

</main>

<script type="text/javascript">
	$(function() {
		$('#editMyRHPhotoInput').on('change', function(e) {
			if (e.target.files && e.target.files[0]) {
				var reader = new FileReader();
				reader.onload = function(ev) { $('#editMyRHAvatarPreview').attr('src', ev.target.result); };
				reader.readAsDataURL(e.target.files[0]);
			}
		});

		$('form#editMyRHForm').ajaxForm({
			beforeSubmit: function() {
				$('#editMyRHForm .loading').show();
			},
			success: function(theResponse) {
				$('#editMyRHForm .loading').hide();
				if (parseInt(theResponse) === 1) {
					$('#editMyRHForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Succès !</strong> Profil mis à jour.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					setTimeout(function() { document.location = 'index.php'; }, 1200);
				} else {
					$('#editMyRHForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Erreur !</strong> Une erreur est survenue.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				}
			}
		});
	});
</script>
