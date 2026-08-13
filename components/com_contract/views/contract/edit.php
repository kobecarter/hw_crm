<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Modifier contrat</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_contract">Contrats</a></li>
						<li class="breadcrumb-item active">Modifier contrat</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('delete', 'com_contract')) :?>
				<div class="col-auto">
					<button type="button" class="btn btn-white text-danger" id="supprimerContratEditBtn"><i class="far fa-trash-alt mr-1"></i> Supprimer le contrat</button>
				</div>
				<?php endif;?>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<?php include("form.php"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if ($_SESSION['user']->hasDroit('delete', 'com_contract')) :?>
<!-- Popup "Supprimer ce contrat" — même habillage que les autres popups de confirmation
     destructive de l'app (.tva-confirm-modal + variante rouge "charge-doublon-icon"). -->
<div id="supprimerContratEditModal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<div class="charge-doublon-icon"><i class="fa fa-trash"></i></div>
				<h5 class="modal-title mt-3">Supprimer ce contrat ?</h5>
			</div>
			<div class="modal-body">
				<p class="text-center mb-0" style="font-size:0.9rem;">Cette action est irréversible. Le devis retrouvera la possibilité d'en générer un nouveau.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
				<button type="button" class="btn btn-danger" id="supprimerContratEditConfirmerBtn"><i class="fa fa-trash mr-1"></i> Supprimer</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function () {
	$('#supprimerContratEditBtn').on('click', function () {
		$('#supprimerContratEditModal').modal('show');
	});
	$('#supprimerContratEditConfirmerBtn').on('click', function () {
		var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Suppression...');
		$.post('components/com_contract/controleurs/router.php?task=deleteContract', {
			id: <?= $contract->getId() ?>
		}, function (response) {
			if (response == '1') {
				window.location.href = 'index.php?option=com_contract';
			} else {
				$btn.prop('disabled', false).html('<i class="fa fa-trash mr-1"></i> Supprimer');
				$('#supprimerContratEditModal').modal('hide');
			}
		});
	});
});
</script>
<?php endif;?>