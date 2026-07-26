<!--<h4 class="card-title">Basic Info</h4>-->
<form method="post" action="<?=$action1?>" id="importPointageForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12 msgbox"></div>
		
		<div class="col-md-12">
			<div class="form-group">
				<label>Fichier de pointage</label>
                <div class="d-flex">
                    <input type="file" name="pointage[]" id="edit_img" class="form-control">
                </div>
				
			</div>
		</div>
	</div>
	<div class="text-right mt-4">
		<button type="submit" name="Importer" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Importer </button>
	</div>
</form>
	