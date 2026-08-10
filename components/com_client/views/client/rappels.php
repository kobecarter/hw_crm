<div class="card card-table">
	<div class="card-header">
		<h4 class="card-title">Domaines &amp; Hébergement</h4>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3"></div>
		<div class="table-responsive list-box">
		<table class="table table-stripped table-center table-hover datatable">
			<thead class="thead-light">
				<tr>
					<th>ID</th>
					<th>Type</th>
					<th>Domaine</th>
					<th>Date expiration</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rappels as $rappel) : ?>
					<?php
					$rowClass = '';
					if ($rappel->getDaysLeft() < 30) {
						$rowClass = 'table-warning';
					}
					if ($rappel->getDaysLeft() < 10) {
						$rowClass = 'table-danger';
					}
					if ($rappel->getDaysLeft() < 0) {
						$rowClass = 'table-expired-recently';
					}
					if ($rappel->getDaysLeft() < -30) {
						$rowClass = 'table-expired';
					}
					?>
					<tr class="<?php echo $rowClass; ?>">
						<td><?php echo $rappel->getId(); ?></td>
						<td><?php echo $rappel->getType(); ?></td>
						<td><?php echo $rappel->getDomaine(); ?></td>
						<td data-sort="<?= strtotime($rappel->getDateExpir())?>"><?php echo normaldate($rappel->getDateExpir()); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		</div>
	</div>
</div>