<div class="card card-table">
	<div class="card-header">
		<h4 class="card-title">Liste des relances</h4>
	</div>
	<div class="card-body">
		<div class="col msgbox mt-3"></div>
		<div class="table-responsive list-box">
			<table class="table table-stripped table-center table-hover datatable">
				<thead class="thead-light">
					<tr>
						<th>ID</th>
						<th>Type</th>
						<th>Remarque</th>
						<th>Date</th>
						<th class="text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($relances as $relance) : ?>
						<tr>
							<td><?php echo $relance->getId(); ?></td>
							<td><?php echo $relance->getType(); ?></td>
							<td><?php

								$remark = $relance->getRemarque();
								if(strlen($remark)>50){
									$remark = substr($remark, 0, 50);
									echo $remark . ' ...';
								}else{
									echo $remark;
								}
								
								?>
							</td>
							<td><?php
								$date=date_create($relance->getDate());
								echo date_format($date,"d/m/Y H:i"); 
								
								?></td>
							<td class="text-right">
								<?php if($_SESSION['user']->hasDroit('edit', 'com_relance')) :?>
									<a href="index.php?option=com_relance&task=edit&id=<?= $relance->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
								<?php endif;?>
								<?php if($_SESSION['user']->hasDroit('delete', 'com_relance')) :?>
									<a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $relance->getId(); ?>"><i class="far fa-trash-alt"></i></a>
								<?php endif;?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>