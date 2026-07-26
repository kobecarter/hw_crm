<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<!-- Page Header -->
		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Détail client</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item"><a href="index.php?option=com_client">Clients</a></li>
						<li class="breadcrumb-item active">Détail client</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Page Header -->

		<div class="profile-cover">
			<div class="profile-cover-wrap" style="background:<?= $currentAgence->getColor(); ?>;">
			</div>
		</div>

		<div class="text-center mb-5">
			<label class="avatar avatar-xxl profile-cover-avatar" style="background:#FFF;box-shadow:rgba(0,0,0,.3) 0 0 5px inset;">
				<?php $photoLink = isset($client) && $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-02.jpg"; ?>
				<img class="avatar-img" src="<?php echo $photoLink; ?>" onerror="this.src=`assets/img/profiles/avatar-02.jpg`" alt="Image de profile" id="blah">
			</label>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body row">
						<div class="col-sm-6">
							<table class="lable table-show-detail">
								<tbody>
									<tr>
										<th>Nom complete</th>
										<td><span class="text-secondary"><?php echo $client->getTitre() . ' ' . $client->getNom() . ' ' . $client->getPrenom(); ?></span></td>
									</tr>
									<tr>
										<th>Raison sociale</th>
										<td><span class="text-secondary"><?php echo $client->getRaisonSocial(); ?></span></td>
									</tr>
									<tr>
										<th>Fonction</th>
										<td><span class="text-secondary"><?php echo $client->getFonction(); ?></span></td>
									</tr>
									<tr>
										<th>ICE</th>
										<td><span class="text-secondary"><?php echo $client->getICE(); ?></span></td>
									</tr>
									<tr>
										<th>RC</th>
										<td><span class="text-secondary"><?php echo $client->getRc(); ?></span></td>
									</tr>
									<tr>
										<th>Tél</th>
										<td><span class="text-secondary"><?php echo $client->getTel(); ?> / <?php echo $client->getTel2(); ?> / <?php echo $client->getTel3(); ?></span></td>
									</tr>
									<tr>
										<th>E-mail</th>
										<td><span class="text-secondary"><?php echo $client->getEmail(); ?></span></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-sm-6">
							<table class="lable table-show-detail">
								<tbody>
									<tr>
										<th>Adresse</th>
										<td><span class="text-secondary"><?php echo $client->getAdresse(); ?></span></td>
									</tr>
									<tr>
										<th>Adresse 2</th>
										<td><span class="text-secondary"><?php echo $client->getAdresse2(); ?></span></td>
									</tr>
									<tr>
										<th>Pays</th>
										<td><span class="text-secondary"><?php echo $client->getPays(); ?></span></td>
									</tr>
									<tr>
										<th>Région</th>
										<td><span class="text-secondary"><?php echo $client->getRegion(); ?></span></td>
									</tr>
									<tr>
										<th>Ville</th>
										<td><span class="text-secondary"><?php echo $client->getVille(); ?></span></td>
									</tr>
									<tr>
										<th>Code postal</th>
										<td><span class="text-secondary"><?php echo $client->getCP(); ?></span></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php include("rappels.php"); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php include("relances.php"); ?>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<?php include("devis.php"); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php include("factures.php"); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php include("payments.php"); ?>
			</div>
		</div>

		
	</div>
</div>