<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Modifier profil</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_users">Utilisateurs</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_users&task=profil">Profils</a></li>
						<li class="breadcrumb-item active">Modifier profil</li>
					</ul>
				</div>
				<div class="col-auto">
					<a href="index.php?option=com_users&task=droits&id=<?php echo $profil->getId(); ?>" class="btn btn-info mr-1">
						<i class="fa fa-user-lock"></i>
					</a>
				</div>
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