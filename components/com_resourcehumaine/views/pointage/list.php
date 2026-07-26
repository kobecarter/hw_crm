<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Pointage</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Pointage</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
						<h4 class="card-title">Importer le pointage</h4>
					</div>
                    <div class="card-body">
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
						<h4 class="card-title">Statistic de pointage</h4>
					</div>
                    <div class="card-body">
                        <div class="row justify-content-end">
                            <div class="col-12">
                                <form method="post" action="" id="pointageFilterForm">
                                    <div class="form-group">
                                        <input type="month" class="form-control ml-auto" value="<?=date('Y-m')?>" name="month" style="width:300px">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive table-pointage mt-5">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<script type="text/javascript">
    function filterPointage(month){
        let order = "date="+month
        $.post("components/com_resourcehumaine/controleurs/router.php?task=filterPointage", order, function(theResponse) {
            console.log(theResponse);
            $(".table-pointage").html(theResponse)
        });
    }
    $(function() {
        // envoi du formulaire en ajax
        $('form#importPointageForm').ajaxForm({
            beforeSubmit: function() {
                $("#importPointageForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#importPointageForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Pointage importé avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Pointage importé avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#importPointageForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#importPointageForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#importPointageForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });

        $(document).on("change", "input[name='month']", function() {
            event.preventDefault();
            var value = $(this).val();
            filterPointage(value)
        })
        filterPointage($('input[name="month"]').val())
    });
</script>