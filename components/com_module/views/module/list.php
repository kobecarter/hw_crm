<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Modules</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Modules</li>
					</ul>
				</div>
				<?php if($_SESSION['user']->hasDroit('add', 'com_module')) :?>
    				<div class="col-auto">
    					<a href="index.php?option=com_module&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter module">
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
						<h4 class="card-title">Liste des modules</h4>
					</div>
					<div class="card-body">
						<div class="col-sm-12 mt-3 msgbox"></div>
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Nom</th>
										<th>Icone</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$directory = "components/";
									$items = scandir($directory);
									foreach ($items as $item){
									if ($item == "." || $item == "..") {
										continue;
									}
									$item = $item . "/";
									if (is_dir($directory . $item)){
									if (preg_match("/^com_/", $item)) {
									$xml = simplexml_load_file($directory . $item . "config.xml");
									$m = module::findByModule($xml->id);
									//var_dump($m);	
									?>
									<tr>
										<td><?php echo $xml->id; ?></td>
										<td><?php echo $xml->nom; ?></td>
										<td><?php echo '<div class="fa fa-' . $xml->icon . '"></div>'; ?></td>
										<td class="text-right">
											<?php if (!$m->isInstalled() && !$m->isEnabled()): ?>
												<?php if ($_SESSION['user']->hasDroit('add', 'com_module')): ?>
													<a href="javascript:void(0)" data-id="<?php echo $xml->id; ?>" data-toggle="tooltip"
											   data-placement="top"
											   data-original-title="Installer"
											   class="btn btn-sm btn-white text-info mr-2 install"><i class="fa fa-inbox"></i></a>
												<?php endif; ?>
												<?php if ($_SESSION['user']->hasDroit('delete', 'com_module') && !$m->isSystem()): ?>
													<a href="javascript:void(0)" data-id="<?php echo $xml->id; ?>" data-toggle="tooltip"
													   data-placement="top"
													   data-original-title="Supprimer"
													   class="btn btn-sm btn-white text-danger mr-2 delete"><i class="fa fa-trash"></i></a>
												<?php endif; ?>
											<?php elseif ($m->isInstalled() && !$m->isEnabled()): ?>
												<?php if ($_SESSION['user']->hasDroit('edit', 'com_module')) : ?>
													<a href="javascript:void(0)" data-id="<?php echo $xml->id; ?>" data-toggle="tooltip"
													   data-placement="top"
													   data-original-title="Désactivé"
													   class="btn btn-sm btn-white text-warning mr-2 enable"><i class="fa fa-toggle-off"></i></a>
												<?php endif; ?>
											
												<?php if ($_SESSION['user']->hasDroit('delete', 'com_module') && !$m->isSystem()): ?>
													<a href="javascript:void(0)" data-id="<?php echo $xml->id; ?>"
													   data-toggle="tooltip"
													   data-placement="top"
													   data-original-title="Désinstallé"
													   class="btn btn-sm btn-white mr-2 desinstall"><i class="fa fa-archive"></i></a>
												<?php endif; ?>
												<?php if ($_SESSION['user']->hasDroit('delete', 'com_module') && !$m->isSystem()): ?>
													<a href="javascript:void(0)" data-id="<?php echo $xml->id; ?>" data-toggle="tooltip"
													   data-placement="top"
													   data-original-title="Supprimer"
													   class="btn btn-sm btn-white text-danger mr-2 delete"><i class="fa fa-trash"></i></a>
												<?php endif; ?>
											<?php elseif ($m->isInstalled() && $m->isEnabled()): ?>
												<?php if ($_SESSION['user']->hasDroit('edit', 'com_module') && !$m->isSystem()) { ?>
													<a href="javascript:void(0)" data-id="<?php echo $m->getIdModule(); ?>" data-toggle="tooltip"
													   data-placement="top"
													   data-original-title="Activer"
													   class="btn btn-sm btn-white text-success mr-2 disable"><i class="fa fa-toggle-on"></i></a>
												<?php } ?>
												<?php if ($m->isTranslated()) { ?>
													<a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
													   data-original-title="Traduction"
													   class="btn btn-sm btn-white text-info mr-2"><i
																class="fa fa-globe"></i></a>
												<?php } ?>
												<?php if ($m->isUrl()) { ?>
													<a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
													   data-original-title="URL"
													   class="btn btn-sm btn-white mr-2"><i
																class="fa fa-link"></i></a>
												<?php } ?>
												<?php if ($_SESSION['user']->hasDroit('edit', 'com_module') && !$m->isSystem()) { ?>
													<a href="index.php?option=com_module&task=edit&id_module=<?php echo $m->getIdModule(); ?>"
													   data-toggle="tooltip" data-placement="top"
													   data-original-title="Modifier"
													   class="btn btn-sm btn-white text-warning mr-2"><i class="fa fa-pencil-alt"></i></a>
												<?php } ?>
												<?php if ($m->isSystem()) { ?>
													<a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
													   data-original-title="Système"
													   class="btn btn-sm btn-white text-danger mr-2"><i
																class="fa fa-plug"></i></a>
												<?php } ?>
												<?php if ($_SESSION['user']->hasDroit('edit', 'com_module') && $m->getIdModule() != "com_login") { ?>
													<a href="index.php?option=<?php echo $m->getIdModule(); ?>"
													   data-toggle="tooltip" data-placement="top"
													   data-original-title="Consulter"
													   class="btn btn-sm btn-white mr-2"><i class="fa fa-eye"></i></a>
												<?php } ?>
												<?php if(file_exists($directory . $item . "migration.php")) { ?>
													<a href="javascript:void(0)"
													   data-toggle="tooltip" data-placement="top"
													   id="migrate-<?= $m->getIdModule();?>"
													   data-original-title="Migration"
													   class="btn btn-sm btn-white text-success mr-2 migrate"><i class="fa fa-database"></i></a>
												<?php } ?>
											<?php endif; ?>
										</td>
									</tr>
									<?php }}} ?>
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
	
	$(document).on( "click", ".delete", function() {
		var msgsucces = "Module supprimé avec succès";
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_module/controleurs/router.php?task=deleteModule", order, function (theResponse) {
				$("html, body").animate({ scrollTop: 0 }, "slow");
				
				if (parseInt(theResponse) === 1) {
					
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
	});
	

	$(document).on( "click", ".install", function() {
		var msgsucces = "Module installé avec succès";
		var $btn = $(this);
		var id = $(this).attr("data-id");
		var order = 'id=' + id;
		$.post("components/com_module/controleurs/router.php?task=installModule", order, function (theResponse) {
			$("html, body").animate({ scrollTop: 0 }, "slow");
			
			if (parseInt(theResponse) === 1) {

				$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				
				setTimeout(function () {
					document.location.reload();
				}, 1000);		
			}
			else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'installation<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});
	
	$(document).on( "click", ".desinstall", function() {
		var msgsucces = "Module désinstallé avec succès";
		var $btn = $(this);
		var id = $(this).attr("data-id");
		var order = 'id=' + id;
		$.post("components/com_module/controleurs/router.php?task=desinstallModule", order, function (theResponse) {
			$("html, body").animate({ scrollTop: 0 }, "slow");
			
			if (parseInt(theResponse) === 1) {

				$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				
				setTimeout(function () {
					document.location.reload();
				}, 1000);		
			}
			else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'installation<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});
	
	$(document).on( "click", ".disable", function() {
		var msgsucces = "Module désactivé avec succès";
		var $btn = $(this);
		var id = $(this).attr("data-id");
		var order = 'id=' + id;
		$.post("components/com_module/controleurs/router.php?task=disableModule", order, function (theResponse) {
			$("html, body").animate({ scrollTop: 0 }, "slow");
			
			if (parseInt(theResponse) === 1) {

				$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				
				setTimeout(function () {
					document.location.reload();
				}, 1000);		
			}
			else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'activation<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});
	
	$(document).on( "click", ".enable", function() {
		var msgsucces = "Module activé avec succès";
		var $btn = $(this);
		var id = $(this).attr("data-id");
		var order = 'id=' + id;
		$.post("components/com_module/controleurs/router.php?task=enableModule", order, function (theResponse) {
			$("html, body").animate({ scrollTop: 0 }, "slow");
			
			if (parseInt(theResponse) === 1) {

				$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				
				setTimeout(function () {
					document.location.reload();
				}, 1000);		
			}
			else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'activation<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});

});
</script>