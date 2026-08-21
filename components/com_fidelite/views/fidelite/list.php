<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">

		<div class="page-header">
			<div class="row align-items-center">
				<div class="col">
					<h3 class="page-title">Espace Fidélité</h3>
					<ul class="breadcrumb">
						<li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
						<li class="breadcrumb-item active">Espace Fidélité</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">

				<div class="card card-table">
					<div class="card-header">
						<h4 class="card-title">Points de fidélité par client</h4>
					</div>
					<div class="card-body">
						<div class="col msgbox mt-3"></div>
						<div class="table-responsive list-box">
							<table class="table table-stripped table-center table-hover datatable">
								<thead class="thead-light">
									<tr>
										<th>Client</th>
										<th>Email</th>
										<th>Total points</th>
										<th class="text-right">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($items as $item):
										$c = $item['client']; ?>
									<tr>
										<td>
											<?php $photoLink = $c->getPhoto() != '' ? "images/clients/" . $c->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
											<h2 class="table-avatar">
												<a href="index.php?option=com_client&task=showDetails&id=<?php echo $c->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $c->getNom() . " " . $c->getPrenom(); ?></a>
											</h2>
										</td>
										<td><?php echo $c->getEmail(); ?></td>
										<td><b><?php echo (int) $item['total']; ?></b></td>
										<td class="text-right">
											<?php if ($_SESSION['user']->hasDroit('edit', 'com_fidelite')) : ?>
											<a href="index.php?option=com_fidelite&task=manage&id=<?= $c->getId(); ?>" class="btn btn-sm btn-white text-primary mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Gérer les points"><i class="fa fa-star"></i> Gérer</a>
											<?php endif; ?>
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
