<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Utilisateurs</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Utilisateurs</li>
					</ul>
				</div>
				<div class="col-auto">
				    <?php if ($_SESSION['user']->hasDroit('add', 'com_users')) :?>
    					<a href="index.php?option=com_users&task=add" class="btn btn-success mr-1">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
					<?php if ($_SESSION['user']->hasDroit('view', 'com_users')) :?>
    					<a class="btn btn-primary filter-btn" href="index.php?option=com_users&task=profil">
    						<i class="fas fa-users"></i>
    					</a>
					<?php endif;?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des utilisateurs</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>Nom</th>
										<th>Login</th>
										<th>Date ajout</th>
										<th>Profil</th>
										<th>Status</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($users as $user): ?>
									<tr>
										<td>
											<?php $photoLink = $user->getPhoto() != '' ? "images/users/" . $user->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
											<h2 class="table-avatar">
												<a href="profile.html"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="User Image"> <?php echo $user->getName(); ?></a>
											</h2>
										</td>
										<td><?php echo $user->getLogin(); ?></td>
										<td data-sort="<?= strtotime($user->getDateAdd())?>"><?php echo $user->getDateAdd(); ?></td>
										<td><span class="text-success"><?php echo $user->getProfil()->getProfil(); ?></span></td>
										<td>
											<?php if($user->isActif()): ?>
											<span class="badge badge-pill bg-success-light">Actif</span>
											<?php else: ?>
											<span class="badge badge-pill bg-danger-light">Inactif</span>
											<?php endif; ?>
										</td>
										<td class="text-right">
										    <?php if ($_SESSION['user']->hasDroit('edit', 'com_users')) :?>
											    <a href="index.php?option=com_users&task=edit&id=<?= $user->getId(); ?>" class="btn btn-sm btn-white text-success mr-2"><i class="far fa-edit"></i></a> 
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_users')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-id="<?= $user->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
	
	var msgsucces = "Utilisateur supprimé avec succès";
	
	$(".delete").click(function (event) {
		event.preventDefault();
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_users/controleurs/router.php?task=deleteUser", order, function (theResponse) {
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