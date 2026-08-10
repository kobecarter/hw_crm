<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=isset($impot) ? $action2 : $action1?>" id="impotForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Type<span class="text-danger"> * </span></label>
				<select class="select" name="type" required>
					<option value="IS" <?php if(isset($impot) && $impot->getType() == 'IS') echo "selected"; ?>>IS</option>
					<option value="IR" <?php if(isset($impot) && $impot->getType() == 'IR') echo "selected"; ?>>IR</option>
				</select>
			</div>
		</div>

        <div class="col-md-3">
			<div class="form-group">
				<label>Date de dépot<span class="text-danger"> * </span></label>
				<div class="cal-icon">
					<input type="date" id="month-input-impot" class="form-control" onclick="document.getElementById('month-input-impot').click();" name="date_of_depot" value="<?php if (isset($impot)){ echo $impot->getDateOfDepot();} ?>" required>
				</div>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Année<span class="text-danger"> * </span></label>
				<select class="select" name="year" required>
					<option value="" disabled selected>Sélectionner</option>
					<?php for ($i=date('Y'); $i >=2000 ; $i--) :?>
						<option value="<?=$i?>" <?php if(isset($impot) && $impot->getYear() == $i) echo "selected"; ?>><?=$i?></option>
					<?php endfor;?>
			</select>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Montant<span class="text-danger"> * </span></label>
				<input type="number" class="form-control" name="amount" min="0" step="any" value="<?php if(isset($impot)) echo $impot->getAmount(); ?>" required>
			</div>
		</div>

		<div class="col-md-2">
			<div class="form-group">
				<label>Majoration<span class="text-danger d-none"> * </span></label>
				<input type="number" class="form-control" name="increasion" min="0" step="any" value="<?php if(isset($impot)) echo $impot->getIncreasion(); ?>">
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label>Document</label>
				<input type="file" class="form-control" name="doc[]">
			</div>
		</div>

		<?php if(isset($impot) && $impot->getDoc() != ''): ?>
		<div class="col-md-2">
		    <a href="images/impot/<?php echo $impot->getDoc(); ?>" class="btn btn-success" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox="" style="margin-top: 32px;">
                <i class="fas fa-file"></i>
            </a>
            <a href="" class="btn btn-danger deleteDoc" data-id="<?php echo $impot->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer document" style="margin-top: 32px;">
                <i class="fas fa-trash"></i>
            </a>
		</div>
		<?php endif; ?>

        <div class="col-md-4">
			<div class="form-group">
				<label>Statut<span class="text-danger"> * </span></label>
                <div class="form-checks">
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" id="statusImpot1" value="1"  <?= isset($impot) ? ($impot->getStatus() == 1 ? 'checked' : '') : '' ?>>
                        <label class="form-check-label" for="statusImpot1">Déposé</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="radio" name="status" class="form-check-input" value="0" id="statusImpot2" <?= isset($impot) ? ($impot->getStatus() == 0 ? 'checked' : '') : 'checked' ?>>
                        <label class="form-check-label" for="statusImpot2">Pas déposé</label>
                    </div>
                </div>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label>Remarque</label>
				<textarea name="remark" id="remarkImpot"><?php if (isset($impot)){ echo $impot->getRemark(); }?></textarea>
                <script type="text/javascript">
                    CKEDITOR.replace('remarkImpot', {
                        allowedContent: true,
                        filebrowserBrowseUrl: 'ckeditor/plugins/ckfinder/ckfinder.html'
                    });
                </script>
			</div>
		</div>

        <input type="hidden" name="id_agence" value="<?= isset($impot) ? $impot->getAgence()->getId() : $_SESSION['agence'] ?>">

		<?php if(isset($impot)): ?>
			<input type="hidden" name="id_impot" value="<?php echo $impot->getId(); ?>">
		<?php endif; ?>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="<?=isset($impot) ? 'Modifier' : 'Ajouter'?>" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> <?=isset($impot) ? 'Modifier' : 'Ajouter'?> </button>
	</div>
</form>
