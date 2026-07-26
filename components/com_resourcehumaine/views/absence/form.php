<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=isset($absence) ? $action2 : $action1?>" id="absenceResourceHumaineForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
        <div class="col-md-4">
			<div class="form-group">
				<label>Date du départ<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="start_date" value="<?php if (isset($absence)){ echo normaldate($absence->getStartDate());} ?>" required>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Date de fin<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="end_date" value="<?php if (isset($absence)){ echo normaldate($absence->getEndDate());}?>" required>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Date de retour<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="text" class="form-control datetimepicker" name="back_date" value="<?php if (isset($absence)){ echo normaldate($absence->getBackDate());}?>" required>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label>Nombre des jours<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="number_of_days" min="0" step="0.5" value="<?php if(isset($absence)) echo $absence->getNumberOfDays(); ?>" required>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label>Justification</label>
                <div class="d-flex">
                    <input type="file" name="justification[]" id="edit_img" class="form-control">
                    <?php if(isset($absence) && $absence->getJustification()) :?>
                        <a href="./images/resourceshumaines/absences/<?php echo $absence->getJustification();?>" class="btn btn-success ml-2"><i class="fa fa-file-alt mt-2"></i></a>
                    <?php endif;?>
                </div>
				
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="form-group">
				<label>Nature d'absence<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="nature_of_absence" class="form-check-input" value="1" id="nature_of_absence1" <?= isset($absence) ? ($absence->getNatureOfAbsence() == 1 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="nature_of_absence1">Congé</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="nature_of_absence" class="form-check-input" value="2" id="nature_of_absence2" <?= isset($absence) ? ($absence->getNatureOfAbsence() == 2 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="nature_of_absence2">Justifié</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="nature_of_absence" class="form-check-input" value="3" id="nature_of_absence3" <?= isset($absence) ? ($absence->getNatureOfAbsence() == 3 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="nature_of_absence3">Non justifié</label>
                    </div>
                </div>
			</div>
		</div>

        <div class="col-md-6">
			<div class="form-group">
				<label>Statut<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" id="status1" value="1"  <?= isset($absence) ? ($absence->getStatus() == 1 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="status1">Déductible</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" value="0" id="status2" <?= isset($absence) ? ($absence->getStatus() == 0 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="status2">Non déductible</label>
                    </div>
                </div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remark"><?php if (isset($absence)){ echo $absence->getRemark(); }?></textarea>
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
				
		<?php if(isset($absence)): ?>
			<input type="hidden" name="id_absence" value="<?php echo $absence->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?=isset($absence) ? 'Modifier' : 'Ajouter'?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($absence) ? 'Modifier' : 'Ajouter'?> </button>
	</div>
</form>
