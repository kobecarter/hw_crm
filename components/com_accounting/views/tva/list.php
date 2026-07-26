<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Tva</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Tva </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_accounting')) : ?>
                        <a href="index.php?option=com_accounting&task=tva" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une tva" target="_blank">
                            <i class="fas fa-plus"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
						<h4 class="card-title">Statistique des tva</h4>
					</div>
                    <div class="card-body">
                        <div class="table-responsive ">
                            <table class="table mb-0 text-center">
                                <thead>
                                    <tr>
                                        <th class="text-secondary">Année</th>
                                        <?php foreach(months() as $month) :?>
                                            <th><?=$month['name']?></th>
                                        <?php endforeach;?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $total_amount = 0;
                                    $total_amount_paid = 0;
                                    $total_amount_unpaid = 0;
                                    for ($i=$start_year; $i <= $end_year; $i++) :
                                        $tva_by_year = tva::findByYear($_SESSION['agence'],$i);
                                        if(sizeof($tva_by_year) > 0 || $i == date('Y',strtotime(date('Y-m-d')))) :
                                        $total_amount_yearly = 0;
                                    ?>

                                        <tr>
                                            <td><?=$i?></td>
                                            <?php foreach(months() as $month) :
                                                $tva_by_date = tva::findByDate($_SESSION['agence'],$i.'-'.$month['number']);
                                                $total_amount_monthly = 0;
                                                ?>
                                                
                                                <th>
                                                    <?php 
                                                        foreach ($tva_by_date as $key => $value) {
                                                            $total_amount_monthly += $value->getAmount();
                                                            $total_amount_yearly += $value->getAmount();
                                                            $total_amount += $value->getAmount();
                                                            if($value->getStatus() == 0){
                                                                $total_amount_unpaid += $value->getAmount();
                                                            }
                                                            if($value->getStatus() == 1){
                                                                $total_amount_paid += $value->getAmount();
                                                            }
                                                        }
                                                        if($total_amount_monthly>0){
                                                            echo '<span class="text-info">'.$total_amount_monthly.' MAD</span>';
                                                        }else{
                                                            echo '0 MAD';
                                                        }
                                                        
                                                    ?>
                                                </th>
                                            <?php endforeach;?>
                                            <th><span class="text-primary"><?=$total_amount_yearly." MAD"?></span></th>
                                        </tr>
                                    <?php 
                                        endif; 
                                        endfor;
                                    ?>
                                </tbody>
                                <tfooter>
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Total : <span class="text-warning"><?=$total_amount?> MAD</span></th>
                                    </tr>
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Total payé : <span class="text-success"><?=$total_amount_paid?> MAD</span></th>
                                    </tr>
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Total resté : <span class="text-danger"><?=$total_amount_unpaid?> MAD</span></th>
                                    </tr>
                                </tfooter>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
						<h4 class="card-title"><?=isset($tva) ? 'Modifier la tva' : 'Ajouter une tva'?></h4>
					</div>
                    <div class="card-body">
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                <div class="card-header">
						<h4 class="card-title">Liste des tva</h4>
					</div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Montant</th>
                                        <th>Majoration</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tvaes as $tva): ?>
                                        <tr>
                                            <td><?php echo $tva->getId(); ?></td>
                                            <td><b><?php echo floatval($tva->getAmount()); ?> MAD</b></td>
                                            <td><b><?php echo floatval($tva->getIncreasion()); ?> MAD</b></td>
                                            <td><?php echo date("m/Y",strtotime($tva->getDate())); ?></td>
                                            <td>
                                                <?php if ($tva->getStatus() == 1) : ?>
                                                    <span class="badge badge-success">Poussé</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">No poussé</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($tva->getDoc() != '') : ?>
                                                    <a href="images/tva/<?php echo $tva->getDoc(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox=""><i class="fa fa-file"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) : ?>
                                                    <a href="index.php?option=com_accounting&task=tva&id_tva=<?= $tva->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) : ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $tva->getId(); ?>"><i class="far fa-trash-alt"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<script type="text/javascript">
    $(function() {
        
        $(document).on("click", ".deleteDoc", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteDocTva", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        setTimeout(function() {
                            $btn.parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Document supprimé avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        var msgsucces = "Tva supprimé avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteTva", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        // envoi du formulaire en ajax
        $('form#tvaForm').ajaxForm({
            beforeSubmit: function() {
                $("#tvaForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#tvaForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Tva ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Tva modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#tvaForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#tvaForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#tvaForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>