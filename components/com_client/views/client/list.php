<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Clients</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Clients</li>
					</ul>
				</div>
				<div class="col-auto">
				    <?php if ($_SESSION['user']->hasDroit('add', 'com_client')) :?>
    					<a href="index.php?option=com_client&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter client">
    						<i class="fas fa-plus"></i>
    					</a>
					<?php endif;?>
					<?php if ($_SESSION['user']->hasDroit('view', 'com_client')) :?>
    					<a class="btn btn-primary filter-btn mr-1" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
    						<i class="fas fa-filter"></i>
    					</a>
    					<!-- me add -->
    					<a href="#" class="btn btn-primary exportEmail" ><span class="fa fa-download spinner-border-sm" data-original-title="ExportEmails"></span> </a>
    					<!-- me add -->
					<?php endif;?>
				</div>
			</div>
		</div>
		
		<!-- Search Filter -->
		<div id="filter_inputs" class="card filter-card">
			<div class="card-body pb-0">
				<form method="post" action="components/com_client/controleurs/router.php?task=filterClient" id="filterClient">
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
						<h4 class="card-title">Liste des clients</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Nom complet</th>
										<th>Téléphone</th>
										<th>Email</th>
										<th>Raison social</th>
										<th></th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($clients as $client): ?>
									<?php $activity = "Ajouté par " . $client->getUserAdded()->getNom() . " | Modifié par " . $client->getUserEdited()->getNom(); ?>	
									<tr>
										<td><?php echo $client->getId(); ?></td>
										<td>
											<?php $photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
											<h2 class="table-avatar">
												<a href="index.php?option=com_client&task=showDetails&id=<?= $client->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $client->getPrenom() . " " . $client->getNom(); ?></a>
											</h2>
										</td>
										<td><?php echo $client->getTel(); ?></td>
										<td><?php echo $client->getEmail(); ?></td>
										<td><?php echo $client->getRaisonSocial(); ?></td>
										<td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
										<td class="text-right">
											<?php $state = $client->isActive() ? 'oui' : 'non'; ?>
											<?php $title = $client->isActive() ? 'Actif' : 'Inactif'; ?>
											<?php $color = $client->isActive() ? 'text-success' : 'text-danger'; ?>
											<?php $ico = $client->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $client->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
											    <a href="index.php?option=com_client&task=edit&id=<?= $client->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a> 
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_client')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $client->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
	
	$(document).on( "click", ".export", function() {	
		event.preventDefault();
		window.open('components/com_client/controleurs/router.php?task=exportClient&year=' + $("#year").val(), '_blank'); 
	})
	
	/*  export emails */
	$(document).on( "click", ".exportEmail", function() {	
		event.preventDefault();
		
		
		window.open('components/com_client/controleurs/router.php?task=exportEmail', '_blank');  
		
		
	})

	// envoi du filtre en ajax
	$('form#filterClient').ajaxForm({
		beforeSubmit: function() {
			$("#filterClient .loading").css('display', 'inline-block');
		},
		success: function(theResponse) {
			$("#filterClient .loading").fadeOut();
			$("html, body").animate({
				scrollTop: 0
			}, "slow");

			$(".list-box").html(theResponse)
		}
	});
	
	var msgsucces = "Client supprimé avec succès";
	
	$(document).on( "click", ".delete", function() {	
		event.preventDefault();
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_client/controleurs/router.php?task=deleteClient", order, function (theResponse) {
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
		$.post("components/com_client/controleurs/router.php?task=enableClient", order, function (theResponse) {
			console.log(theResponse);
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