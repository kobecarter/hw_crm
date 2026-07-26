<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Modifier devis</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_devis">Devis</a></li>
						<li class="breadcrumb-item active">Modifier devis</li>
					</ul>
				</div>
				<div class="col-auto">
					<?php
					$facture = facture::findByDevis($devis->getId(),$_SESSION['agence']);
					if($devis->getStatu() == 4 && !$facture):
					?>
					<a href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>" class="btn btn-success mr-1 create-invoice" data-toggle="tooltip" data-placement="top" data-original-title="+ Facture">
						<i class="fa fa-file-invoice"></i>
					</a>
					<?php endif;?>
					<a href="components/com_devis/controleurs/router.php?task=pdfDevisTexte&id=<?php echo $devis->getId(); ?>" target="_blank" class="btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" data-original-title="PDF">
						<i class="far fa-file-pdf"></i>
					</a>
					<a href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>" target="_blank" class="btn btn-info mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Afficher">
						<i class="far fa-eye"></i>
					</a>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
					    <div class="mb-5">
					       <?php include("wizard_process.php"); ?>
					    </div>
						<?php include("form.php"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>