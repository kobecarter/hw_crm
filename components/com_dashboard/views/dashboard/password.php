<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content container-fluid">
		<div class="row justify-content-center">
			<div class="col-xl-7 d-flex">
				<div class="card flex-fill">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5 class="card-title">Écrivez le code PIN</h5>
						</div>
					</div>
					<div class="card-body">
					    <form method="post" action="components/com_dashboard/controleurs/router.php?task=validateCodePin" id="codePinForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12">
                                    <div class="msgbox"></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="password" placeholder="Code PIN" class="form-control" name="code_pin" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0 text-center">
                                        <button type="submit" class="btn btn-primary"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Valider</button>
                                    </div>
                                </div>
                            </div>                
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Page Wrapper -->

<script>
    $(document).ready(function(){
        // envoi du formulaire en ajax
		$('form#codePinForm').ajaxForm({
			beforeSubmit: function() {
				$("#codePinForm .loading").css('display', 'inline-block');
			},
			success: function(theResponse) {
				console.log(theResponse)
				// console.log(theResponse);
				$("#codePinForm .loading").fadeOut();
				$("html, body").animate({
					scrollTop: 0
				}, "slow");

				var msgsucces = "Le code PIN est correct";
				if (parseInt(theResponse) === 1) {
					$('#codePinForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    setTimeout(function() {
						location.reload()
					}, 1500)
				} else if (parseInt(theResponse) === 0) {
					$('#codePinForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir Le champ de code PIN<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}else if(parseInt(theResponse) === 2){
                    $('#codePinForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Le code PIN est incorrect<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
					$('#codePinForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				}
			}
		});
    })
</script>