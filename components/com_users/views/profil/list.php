<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Utilisateurs</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_users">Utilisateurs</a></li>
						<li class="breadcrumb-item active">Profils</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_users')) :?>
    				<div class="col-auto">
    					<a href="index.php?option=com_users&task=addProfil" class="btn btn-success mr-1">
    						<i class="fas fa-plus"></i>
    					</a>
    				</div>
				<?php endif;?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des profils</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>Profil</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($profils as $profil): ?>
									<tr>
										<td><?php echo $profil->getProfil(); ?></td>
										<td class="text-right">
										    <?php if ($_SESSION['user']->hasDroit('add', 'com_users') || $_SESSION['user']->hasDroit('edit', 'com_users')) :?>
											    <a href="index.php?option=com_users&task=droits&id=<?= $profil->getId(); ?>" class="btn btn-sm btn-white text-info mr-2"><i class="fa fa-user-lock"></i></a>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_users')) :?>
											    <a href="index.php?option=com_users&task=editProfil&id=<?= $profil->getId(); ?>" class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit"></i></a>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_users')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-id="<?= $profil->getId(); ?>"><i class="far fa-trash-alt"></i></a>
											<?php endif;?>
										</td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>					
		</div>					
	</div>			
</div>
<!-- /Page Wrapper -->
<script type="text/javascript">
$(function () {
	
	var msgsucces = "Profil supprimé avec succès";
	
	$(".delete").click(function (event) {
		event.preventDefault();
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_users/controleurs/router.php?task=deleteProfil", order, function (theResponse) {
				if (parseInt(theResponse) == 1) {
					
					$btn.parent().parent().addClass("table-danger");
					setTimeout(function () {
						$btn.parent().parent().remove()
					}, 1000);
					
					$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
				else {
					$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			});
		}
	})
});
</script>