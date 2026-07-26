<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=isset($bonus) ? $action2 : $action1?>" id="bonusResourceHumaineForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
        <div class="col-md-4">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="month" class="form-control" name="date" value="<?php if (isset($bonus)){ echo date('Y-m',strtotime($bonus->getDate()));} ?>" required>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Montant<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="amount" min="0" step="any" value="<?php if(isset($bonus)) echo $bonus->getAmount(); ?>" required>
			</div>
		</div>

        <div class="col-md-4">
			<div class="form-group">
				<label>Statut<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" id="status1" value="1"  <?= isset($bonus) ? ($bonus->getStatus() == 1 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="status1">Pris</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" value="0" id="status2" <?= isset($bonus) ? ($bonus->getStatus() == 0 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="status2">Pas pris</label>
                    </div>
                </div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remark"><?php if (isset($bonus)){ echo $bonus->getRemark(); }?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('remark', {
                        allowedContent: true,
                        //allowedContent: 'p b i ul li tr th h2 h1 h3 h4 h5 h6 a; a[!href]; table[border,cellpadding,cellspacing]; td{height,width}; div(conditions,contentConditions,contentConditions2)',
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>	
		
		<!-- Toggle Switch -->

        <input type="hidden" name="id_resourcehumaine" value="<?= $resourcehumaine->getId() ?>" />
				
		<?php if(isset($bonus)): ?>
			<input type="hidden" name="id_bonus" value="<?php echo $bonus->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?=isset($bonus) ? 'Modifier' : 'Ajouter'?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($bonus) ? 'Modifier' : 'Ajouter'?> </button>
	</div>
</form>
