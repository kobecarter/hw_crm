<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Fournisseurs</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Fournisseurs</li>
					</ul>
				</div>
				<div class="col-auto">
				    <?php if ($_SESSION['user']->hasDroit('add', 'com_fournisseur')) :?>
    					<a href="index.php?option=com_fournisseur&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter fournisseur">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
					<?php if($_SESSION['user']->hasDroit('view', 'com_fournisseur')) :?>
					<a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
						<i class="fas fa-filter"></i>
					</a>
					<?php endif;?>
				</div>
			</div>
		</div>
		
		<!-- Search Filter -->
		<div id="filter_inputs" class="card filter-card">
			<div class="card-body pb-0">
				<form method="post" action="components/com_fournisseur/controleurs/router.php?task=filterFournisseur" id="filterFournisseur">
					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="form-group">
								<label>Année</label>
								<input type="text" class="form-control" required name="year" id="year" value="<?php if (isset($_GET['year'])) echo $_GET['year']; ?>">
							</div>
						</div>
						
						<div class="col-sm-6 col-md-3 mb-4 mb-sm-0 mt-0 mt-sm-4 pt-0 pt-sm-2">
							<button type="submit" name="" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>
							
							<a href="#0" class="btn btn-primary export"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
						</div>
					</div>
				</form>
			</div>
		</div>
		<!-- /Search Filter -->
		
		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Liste des fournisseurs</h4>
					</div>
					<div class="card-body p-3">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
						    <div class="card card-table">
                                <div>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-all-tab" data-toggle="tab" data-target="#nav-all" type="button" role="tab" aria-controls="nav-all" aria-selected="true">Voir tout</button>
                                        <?php $categories = getCategorieFournisseur(); ?>
                                        <?php $cpt = 0; ?>
                                        <?php foreach($categories as $key => $categorie): ?>
                                        <?php $cpt++; ?>
                                        <button class="nav-link" id="nav-<?php echo $key; ?>-tab" data-toggle="tab" data-target="#nav-<?php echo $key; ?>" type="button" role="tab" aria-controls="nav-<?php echo $key; ?>" aria-selected="true"><?php echo $categorie; ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="tab-content" id="nav-tabContent">
                                    <!-- Tableau tout les fournisseurs -->
                                    <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
                                        <!-- Tableau -->
                                        <table class="table table-stripped table-center table-hover datatable">
            								<thead class="thead-light">
            									<tr>
            										<th>ID</th>
            										<th>Source</th>
            										<th>Nom complet</th>
            										<th>Téléphone</th>
            										<th>Email</th>
            										<th>Raison social</th>
            										<th class="text-right">Actions</th>
            									</tr>
            								</thead>
            								<tbody>
            								    <?php $fournisseurs = fournisseur::findAll(); ?>
            									<?php foreach ($fournisseurs as $fournisseur): ?>
            									<tr>
            										<td><?php echo $fournisseur->getId(); ?></td>
            										<td><?php echo $fournisseur->getSource(); ?></td>
            										<td>
            											<?php $photoLink = $fournisseur->getPhoto() != '' ? "images/fournisseurs/" . $fournisseur->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
            											<h2 class="table-avatar">
            												<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Fournisseur Image"> <?php echo $fournisseur->getPrenom() . " " . $fournisseur->getNom(); ?></a>
            											</h2>
            										</td>
            										<td><?php echo $fournisseur->getTel(); ?></td>
            										<td><?php echo $fournisseur->getEmail(); ?></td>
            										<td><?php echo $fournisseur->getRaisonSocial(); ?></td>
            										
            										<td class="text-right">
            										    <?php if($fournisseur->isValide()): ?>
            										    <a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="Fournisseur validé"><i class="fa fa-check"></i></a>
            										    <?php endif; ?>
            										    
            										    <?php $photoLink = $fournisseur->getDoc() != '' ? "images/fournisseurs/" . $fournisseur->getDoc() : ''; ?>
            										    <?php if($photoLink != ''): ?>
            										    <a href="<?php echo $photoLink; ?>" class="btn btn-sm btn-white text-info mr-2" data-fancybox data-toggle="tooltip" data-placement="top" data-original-title="Document"><i class="fa fa-file"></i></a>
            										    <?php endif; ?>
            										    
            											<?php $state = $fournisseur->isActive() ? 'oui' : 'non'; ?>
            											<?php $title = $fournisseur->isActive() ? 'Actif' : 'Inactif'; ?>
            											<?php $color = $fournisseur->isActive() ? 'text-success' : 'text-danger'; ?>
            											<?php $ico = $fournisseur->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
            											<?php if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) :?>
            											    <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $fournisseur->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
            										    	<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a> 
            											<?php endif;?>
            											<?php if ($_SESSION['user']->hasDroit('delete', 'com_fournisseur')) :?>
            											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $fournisseur->getId(); ?>"><i class="far fa-trash-alt"></i></a>
            										    <?php endif;?>
            										</td>
            									</tr>
            									<?php endforeach; ?>
            								</tbody>
            							</table>
                                        <!-- Tableau -->
                                    </div>
                                    <!-- Fin Tableau tout les fournisseurs -->
                                    
                                    <?php $cpt = 0; ?>
                                    <?php foreach($categories as $key => $categorie): ?>
                                    <?php $cpt++; ?>
                                    <div class="tab-pane fade" id="nav-<?php echo $key; ?>" role="tabpanel" aria-labelledby="nav-<?php echo $key; ?>-tab">
                                        <!-- Tableau -->
                                        <table class="table table-stripped table-center table-hover datatable">
            								<thead class="thead-light">
            									<tr>
            										<th>ID</th>
            										<th>Source</th>
            										<th>Nom complet</th>
            										<th>Téléphone</th>
            										<th>Email</th>
            										<th>Raison social</th>
            										<th class="text-right">Actions</th>
            									</tr>
            								</thead>
            								<tbody>
            								    <?php $fournisseurs = fournisseur::findAllByCategory(true,$key); ?>
            									<?php foreach ($fournisseurs as $fournisseur): ?>
            									<tr>
            										<td><?php echo $fournisseur->getId(); ?></td>
            										<td><?php echo $fournisseur->getSource(); ?></td>
            										<td>
            											<?php $photoLink = $fournisseur->getPhoto() != '' ? "images/fournisseurs/" . $fournisseur->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
            											<h2 class="table-avatar">
            												<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Fournisseur Image"> <?php echo $fournisseur->getPrenom() . " " . $fournisseur->getNom(); ?></a>
            											</h2>
            										</td>
            										<td><?php echo $fournisseur->getTel(); ?></td>
            										<td><?php echo $fournisseur->getEmail(); ?></td>
            										<td><?php echo $fournisseur->getRaisonSocial(); ?></td>
            										
            										<td class="text-right">
            										    <?php if($fournisseur->isValide()): ?>
            										    <a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="Fournisseur validé"><i class="fa fa-check"></i></a>
            										    <?php endif; ?>
            										    
            										    <?php $photoLink = $fournisseur->getDoc() != '' ? "images/fournisseurs/" . $fournisseur->getDoc() : ''; ?>
            										    <?php if($photoLink != ''): ?>
            										    <a href="<?php echo $photoLink; ?>" class="btn btn-sm btn-white text-info mr-2" data-fancybox data-toggle="tooltip" data-placement="top" data-original-title="Document"><i class="fa fa-file"></i></a>
            										    <?php endif; ?>
            										    
            											<?php $state = $fournisseur->isActive() ? 'oui' : 'non'; ?>
            											<?php $title = $fournisseur->isActive() ? 'Actif' : 'Inactif'; ?>
            											<?php $color = $fournisseur->isActive() ? 'text-success' : 'text-danger'; ?>
            											<?php $ico = $fournisseur->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
            											<?php if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) :?>
            											    <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $fournisseur->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
            										    	<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a> 
            											<?php endif;?>
            											<?php if ($_SESSION['user']->hasDroit('delete', 'com_fournisseur')) :?>
            											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $fournisseur->getId(); ?>"><i class="far fa-trash-alt"></i></a>
            										    <?php endif;?>
            										</td>
            									</tr>
            									<?php endforeach; ?>
            								</tbody>
            							</table>
                                        <!-- Tableau -->
                                    </div>
                                    <?php endforeach; ?>
                                </div>    
						    </div>
						    
						    
						    
						    
							
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
	
	$(document).on( "click", ".export", function() {	
		event.preventDefault();
		window.open('components/com_fournisseur/controleurs/router.php?task=exportFournisseur&year=' + $("#year").val(), '_blank'); 
		
		/*var order = $("#filterFournisseur").serialize();
		$.post("", order, function (result) {
		});*/
	})
	
	// envoi du filtre en ajax
	$('form#filterFournisseur').ajaxForm({
		beforeSubmit: function() {
			$("#filterFournisseur .loading").css('display', 'inline-block');
		},
		success: function(theResponse) {
			$("#filterFournisseur .loading").fadeOut();
			$("html, body").animate({
				scrollTop: 0
			}, "slow");

			$(".list-box").html(theResponse)
		}
	});
	
	var msgsucces = "Fournisseur supprimé avec succès";
	
	$(document).on( "click", ".delete", function() {	
		event.preventDefault();
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_fournisseur/controleurs/router.php?task=deleteFournisseur", order, function (theResponse) {
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
	
	$(document).on( "click", ".enable", function() {	
		var btn = $(this);
		var id = btn.attr("data-id");
		var state = btn.attr("data-state");
		var order = 'id=' + id + "&state=" + state;
		$.post("components/com_fournisseur/controleurs/router.php?task=enableFournisseur", order, function (theResponse) {
			var error_msg = "Erreur lors de l'activation.";
			if (state === "oui") {
				error_msg = "Erreur lors de la désactivation.";
			}
			if (parseInt(theResponse) === 1) {
				if (state === "oui") {
					btn.attr("data-state", "non").removeClass("text-success").addClass("text-danger").attr("data-original-title", "Inactif").html("<i class='fa fa-toggle-off'>");
				} else {
					btn.attr("data-state", "oui").removeClass("text-danger").addClass("text-success").attr("data-original-title", "Actif").html("<i class='fa fa-toggle-on'>");
				}
			} else {
				$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> ' + error_msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}
		});
	});
});
</script>