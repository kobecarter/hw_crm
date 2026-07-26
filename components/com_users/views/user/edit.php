<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h3 class="page-title">Modifier utilisateur</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_users">Utilisateurs</a></li>
						<li class="breadcrumb-item active">Modifier utilisateur</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-3 col-md-4 mb-4 mb-sm-0">

				<!-- Settings Menu -->
				<div class="widget settings-menu">
					<ul>
						<li class="nav-item">
							<a href="index.php?option=com_users&task=edit&id=<?= $user->getId(); ?>" class="nav-link active">
								<i class="far fa-user"></i> <span>Profile Settings</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="index.php?option=com_users&task=editPass&id=<?= $user->getId(); ?>" class="nav-link">
								<i class="fas fa-cog"></i> <span>Change Password</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="#0" class="nav-link Delete" data-id =<?=  $user->getId(); ?>>
								<i class="fas fa-ban"></i> <span>Delete Account</span>
							</a>
						</li>
					</ul>
				</div>
				<!-- /Settings Menu -->

			</div>

			<div class="col-xl-9 col-md-8">

				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Basic information</h5>
					</div>
					<div class="card-body">

						<!-- Form -->
						<?php include('form.php'); ?>
						<!-- /Form -->

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--  /Page Wrapper-->
<script type="text/javascript">
	$(function () {
			var msgsucces = "Utilisateur supprimé avec succès";
			$(".Delete").click(function (event) {
				event.preventDefault();
				var $btn = $(this);
				if (confirm("Etes-vous sure !")) {
					var id = $(this).attr("data-id");
					var order = 'id=' + id;
					$.post("components/com_users/controleurs/router.php?task=deleteUser", order, function (theResponse) {
						if (parseInt(theResponse) == 1) {
							
							
							
							$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
							setTimeout(() => {
								document.location = "index.php?option=com_users";
							}, 500);
						}
						else {
							$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
						}
					});
				}
			})
	});
</script>