<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=isset($tva) ? $action2 : $action1?>" id="tvaForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
        <div class="col-md-4">
			<div class="form-group">
				<label>Date<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="month" id="month-input" class="form-control" onclick="document.getElementById('month-input').click();" name="date" value="<?php if (isset($tva)){ echo date('Y-m',strtotime($tva->getDate()));} ?>" required>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Montant<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="amount" min="0" step="any" value="<?php if(isset($tva)) echo $tva->getAmount(); ?>" required>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Majoration<span class="text-danger d-none"> * </span></label>
				<input type="number" class="form-control" name="increasion" min="0" step="any" value="<?php if(isset($tva)) echo $tva->getIncreasion(); ?>">
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="form-group">
				<label>Document</label>
				<input type="file" class="form-control" name="doc[]">
			</div>
		</div>
		
		<?php if(isset($tva) && $tva->getDoc() != ''): ?>
		<div class="col-md-2">
		    <a href="images/tva/<?php echo $tva->getDoc(); ?>" class="btn btn-success" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox="" style="margin-top: 32px;">
                <i class="fas fa-file"></i>
            </a>
            <a href="" class="btn btn-danger deleteDoc" data-id="<?php echo $tva->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer document" style="margin-top: 32px;">
                <i class="fas fa-trash"></i>
            </a>
		</div>
		<?php endif; ?>

        <div class="col-md-4">
			<div class="form-group">
				<label>Statut<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" id="status1" value="1"  <?= isset($tva) ? ($tva->getStatus() == 1 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="status1">Poussé</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" value="0" id="status2" <?= isset($tva) ? ($tva->getStatus() == 0 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="status2">Non poussé</label>
                    </div>
                </div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remark"><?php if (isset($tva)){ echo $tva->getRemark(); }?></textarea>
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

        <input type="hidden" name="id_agence" value="<?= isset($tva) ? $tva->getAgence()->getId() : $_SESSION['agence'] ?>">
				
		<?php if(isset($tva)): ?>
			<input type="hidden" name="id_tva" value="<?php echo $tva->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?=isset($tva) ? 'Modifier' : 'Ajouter'?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($tva) ? 'Modifier' : 'Ajouter'?> </button>
	</div>
</form>
