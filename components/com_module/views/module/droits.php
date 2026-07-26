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
						<li class="breadcrumb-item"><a href="index.php?option=com_users&task=profil">Profils</a></li>
						<li class="breadcrumb-item active">Drois d'accès : <?php echo $profil->getProfil(); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Drois d'accès : <?php echo $profil->getProfil(); ?></h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>Module</th>
										<th><i class="far fa-eye"></i> Consulter</th>
										<th><i class="fas fa-plus"></i> Ajouter</th>
										<th><i class="fa fa-pencil-alt"></i> Modifier</th>
										<th><i class="far fa-trash-alt"></i> Supprimer</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($modules as $module): ?>
									<tr>
										<td><?php echo $module->getNom(); ?></td>
										<?php foreach($actions as $action): ?>
										<td>
											<?php if($profil->hasDroit($action, $module->getIdModule())): ?>
											<a href="javascript:void(0);" class="btn btn-sm btn-success mr-2 droits" name="<?php echo 'disable:' .$profil->getId() . ':' . $module->getIdModule() . ':' . $action; ?>"><i class="fa fa-check"></i></a>
											<?php else: ?>
											<a href="javascript:void(0);" class="btn btn-sm btn-danger mr-2 droits" name="<?php echo 'enable:' .$profil->getId() . ':' . $module->getIdModule() . ':' . $action; ?>"><i class="fa fa-times"></i></a>
											<?php endif; ?>
										</td>
										<?php endforeach; ?>
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
	
	$(".droits").click(function (event) {
		event.preventDefault();
		var $bt = $(this);
		var t = $(this).attr("name").split(":");
		var order = 'task=' + t[0] + '&profil=' + t[1] + '&module=' + t[2] + '&action=' + t[3];
		$.post("components/com_users/controleurs/router.php?task=setDroit", order, function (theResponse) {
			if (parseInt(theResponse) == 1) {
				if (t[0] == 'enable') {
					$bt.attr("name", "disable:" + t[1] + ":" + t[2] + ":" + t[3]);
					$bt.addClass("btn-success").removeClass("btn-danger");
					$bt.html('<i class="fa fa-check"></i>');
				}

				if (t[0] == 'disable') {
					$bt.attr("name", "enable:" + t[1] + ":" + t[2] + ":" + t[3]);
					$bt.addClass("btn-danger").removeClass("btn-success");
					$bt.html('<i class="fa fa-times"></i>');
				}
			}
		});
	})
});
</script>