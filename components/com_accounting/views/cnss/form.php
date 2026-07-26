<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=isset($cnss) ? $action2 : $action1?>" id="cnssForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
        <div class="col-md-4">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="month" id="month-input" class="form-control" onclick="document.getElementById('month-input').click();" name="date" value="<?php if (isset($cnss)){ echo date('Y-m',strtotime($cnss->getDate()));} ?>" required>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Montant<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="amount" min="0" step="any" value="<?php if(isset($cnss)) echo $cnss->getAmount(); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Majoration<span class="text-danger d-none"> * </span></label>
				<input type="number" class="form-control" name="increasion" min="0" step="any" value="<?php if(isset($cnss)) echo $cnss->getIncreasion(); ?>">
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Justification</label>
                <div class="d-flex">
                    <input type="file" name="justification[]" id="edit_img" class="form-control">
                    <?php if(isset($cnss) && $cnss->getJustification()) :?>
                        <a href="./images/accounting/cnss/<?php echo $cnss->getJustification();?>" class="btn btn-success ml-2"><i class="fa fa-file-alt mt-2"></i></a>
                    <?php endif;?>
                </div>
				
			</div>
		</div>

        <div class="col-md-4">
			<div class="form-group">
				<label>Statut<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" id="status1" value="1"  <?= isset($cnss) ? ($cnss->getStatus() == 1 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="status1">Payé</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" value="0" id="status2" <?= isset($cnss) ? ($cnss->getStatus() == 0 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="status2">Impayé</label>
                    </div>
                </div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remark"><?php if (isset($cnss)){ echo $cnss->getRemark(); }?></textarea>
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

        <input type="hidden" name="id_agence" value="<?= isset($cnss) ? $cnss->getAgence()->getId() : $_SESSION['agence'] ?>">
				
		<?php if(isset($cnss)): ?>
			<input type="hidden" name="id_cnss" value="<?php echo $cnss->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?=isset($cnss) ? 'Modifier' : 'Ajouter'?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($cnss) ? 'Modifier' : 'Ajouter'?> </button>
	</div>
</form>
