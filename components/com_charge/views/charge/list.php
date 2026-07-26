<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Charges</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Charges</li>
					</ul>
				</div>
				<?php if ($_SESSION['user']->hasDroit('add', 'com_charge')) :?>
				<div class="col-auto">
					<a href="index.php?option=com_charge&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter charge">
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
						<h4 class="card-title">Liste des Charges</h4>
					</div>
					<div class="card-body">
					    <style>
					        .shortcut{
					            display:table;
					            margin:auto;
					            padding:0;
					        }
					        .shortcut li{
					            display:inline-block;
					            margin: 0 15px;
					        }
					        .shortcut li img{
					            height:84px;
					        }
					    </style>
					    <ul class="shortcut">
					        <li><a href="https://clients.genious.net/login" target="_blank"><img src="images/logo-genious.png" alt="Genious"></a></li>
					        <li><a href="https://sso.godaddy.com/?realm=idp&app=venture-redirector&path=/" target="_blank"><img src="images/logo-godaddy.png" alt="GoDaddy"></a></li>
					        <li><a href="https://www.ovh.com/auth/" target="_blank"><img src="images/logo-ovh.png" alt="OVH"></a></li>
					        <li><a href="https://clients.o2switch.fr/auth/connexion" target="_blank"><img src="images/logo-o2switch.png" alt="O2switch"></a></li>
					        <li><a href="https://themeforest.net/" target="_blank"><img src="images/logo-themeforest.png" alt="Themeforest"></a></li>
					        <li><a href="https://www.adobe.com/" target="_blank"><img src="images/logo-adobe.png" alt="Adobe"></a></li>
					        <li><a href="https://www.link-assistant.com/" target="_blank"><img src="images/logo-powersuite.png" alt="PowerSuite"></a></li>
					        <li><a href="https://leonardo.ai/" target="_blank"><img src="images/logo-leonardo.png" alt="leonardo"></a></li>
					    </ul>
						<div class="col-sm-12 mt-3 msgbox"></div>
						<!-- Search Filter -->
                        <div id="filter_inputs_charge" class="card filter-card px-4">
                            <div class="card-body pb-0">
                                <form method="post" action="" id="filterCharges">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-2">
                                            <div class="form-group">
                                                <label>Date début</label>
                                                <div class="cal-icon">
                                                    <input type="text" class="form-control datetimepicker" required name="from" id="from" value="<?php if (isset($_GET['from'])) echo normaldate($_GET['from']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-2">
                                            <div class="form-group">
                                                <label>Date fin</label>
                                                <div class="cal-icon">
                                                    <input type="text" class="form-control datetimepicker" required name="to" id="to" value="<?php if (isset($_GET['to'])) echo normaldate($_GET['to']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <a href="javascript:void(0)" class="btn btn-primary exportCharges" style="margin-top: 32px;"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- /Search Filter -->
						<div class="table-responsive">
							<table class="table table-stripped table-center table-hover datatable" data-order="[]">
								<thead class="thead-light">
									<tr>
										<th>ID</th>
										<th>Payé par</th>
										<th>Charge</th>
										<th>Type</th>
										<th>Montant</th>
										<th>Rembourssé</th>
										<th>Date charge</th>
										<th>Date paiement</th>
										<th>Description</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($charges as $charge): ?>
									<?php 
									switch($charge->getType()){
										case 'fixe' : $trClass = "table-success"; break;
										case 'variable' : $trClass = "table-warning"; break;
										case 'hors_hw' : $trClass = "table-secondary"; break;	
										default : $trClass = "";
									}
									?>
									<tr class="<?php echo $trClass; ?>">
										<td><?php echo $charge->getId(); ?></td>
										<td><?php echo $charge->getPaidBy()->getId() == 0 ? $charge->getAgence()->getNom() : $charge->getPaidBy()->getNom()." ".$charge->getPaidBy()->getPrenom();?></td>
										<td><?php echo $charge->getTitre(); ?></td>
										<td><?php echo $charge->getType(); ?></td>
										<td><?php echo number_format($charge->getTotal(), 2, ',', ' '). ' ' . $charge->getDevise(); ?></td>
										<td>
										    <?php if($charge->getPaidBy()->getId() != 0): ?>
										    <?php if($charge->isRefunded()) : ?>
										        <span class="badge badge-success">Rembourssé</span>
										    <?php else : ?>
										        <span class="badge badge-danger">Non Rembourssé</span>
										    <?php endif; ?>
										    <?php endif; ?>
										</td>
										<td data-sort="<?= strtotime($charge->getDateCharge())?>"><?php echo normaldate($charge->getDateCharge()); ?></td>
										<td data-sort="<?= strtotime($charge->getDatePayment())?>"><?php echo normaldate($charge->getDatePayment()); ?></td>
										<td><?php echo $charge->getDescription(); ?></td>
										<td class="text-right">
										    <?php if ($_SESSION['user']->hasDroit('view', 'com_charge')) :?>
    											<?php if($charge->getPhoto() != ''): ?>
    											<a href="images/charges/<?php echo $charge->getPhoto(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Photo" data-fancybox><i class="fa fa-image"></i></a> 
    											<?php endif; ?>
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_charge')) :?>
												<?php $state = $charge->isPaid() ? 'oui' : 'non'; ?>
    											<?php $title = $charge->isPaid() ? 'Payée' : 'Non payée'; ?>
    											<?php $color = $charge->isPaid() ? 'text-success' : 'text-danger'; ?>
    											<?php $ico = $charge->isPaid() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
    											<a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $charge->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
    											<a href="index.php?option=com_charge&task=edit&id=<?= $charge->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a> 
											<?php endif;?>
											<?php if ($_SESSION['user']->hasDroit('delete', 'com_charge')) :?>
											    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $charge->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
	
	var msgsucces = "Charge supprimée avec succès";
	
	$(document).on( "click", ".delete", function() {
		var $btn = $(this);
		if (confirm("Etes-vous sure !")) {
			var id = $(this).attr("data-id");
			var order = 'id=' + id;
			$.post("components/com_charge/controleurs/router.php?task=deleteCharge", order, function (theResponse) {
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
		$.post("components/com_charge/controleurs/router.php?task=enableCharge", order, function (theResponse) {
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
	
	// Export résultat filtre
    $(document).on("click", ".exportCharges", function() {
        event.preventDefault();
        window.open('components/com_charge/controleurs/router.php?task=exportCharges&from=' + $("#from").val() + '&to=' + $("#to").val(), '_blank');
    })
});
</script>