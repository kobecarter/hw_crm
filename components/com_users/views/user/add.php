<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h3 class="page-title">Ajouter utilisateur</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_users">Utilisateurs</a></li>
						<li class="breadcrumb-item active">Ajouter utilisateur</li>
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
							<a href="settings.html" class="nav-link active">
								<i class="far fa-user"></i> <span>Profile Settings</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="preferences.html" class="nav-link">
								<i class="fas fa-cog"></i> <span>Preferences</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="tax-types.html" class="nav-link">
								<i class="far fa-check-square"></i> <span>Tax Types</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="expense-category.html" class="nav-link">
								<i class="far fa-list-alt"></i> <span>Expense Category</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="notifications.html" class="nav-link">
								<i class="far fa-bell"></i> <span>Notifications</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="change-password.html" class="nav-link">
								<i class="fas fa-unlock-alt"></i> <span>Change Password</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="delete-account.html" class="nav-link">
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
						<h5 class="card-title">Informations</h5>
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