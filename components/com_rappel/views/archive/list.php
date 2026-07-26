<!-- Page Wrapper -->
<style>
    .table-expired-recently {
        background-color: rgba(220, 53, 69, 0.5);
    }

    .table-expired-recently:hover {
        background-color: rgba(220, 53, 69, 0.6) !important;
    }

    .table-expired {
        background-color: rgba(220, 53, 69, 0.7);
    }

    .table-expired:hover {
        background-color: rgba(220, 53, 69, 0.8) !important;
    }
</style>
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Archive</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_rappel">Rappels</a></li>
                        <li class="breadcrumb-item active">Rappels Archivés</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Liste des Rappels Archivés</h4>
                    </div>
                    <div class="card-body">
                        <div class="col-sm-12 mt-3 msgbox"></div>
                        <div class="table-responsive">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Domaine</th>
                                        <th>Date expiration</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rappels as $rappel) : ?>
                                        <?php
                                        $rowClass = '';
                                        // if ($rappel->getDaysLeft() < 30) {
                                        //     $rowClass = 'table-warning';
                                        // }
                                        // if ($rappel->getDaysLeft() < 10) {
                                        //     $rowClass = 'table-danger';
                                        // }
                                        // if ($rappel->getDaysLeft() < 0) {
                                        //     $rowClass = 'table-expired-recently';
                                        // }
                                        // if ($rappel->getDaysLeft() < -30) {
                                        //     $rowClass = 'table-expired';
                                        // }
                                        ?>
                                        <tr class="<?php echo $rowClass; ?>">
                                            <td><?php echo $rappel->getId(); ?></td>
                                            <td><?php echo $rappel->getType(); ?></td>
                                            <td><?php echo $rappel->getDomaine(); ?></td>
                                            <td data-sort="<?= strtotime($rappel->getDateExpir())?>"><?php echo normaldate($rappel->getDateExpir()); ?></td>
                                            <td class="text-right">
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) :?>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2 desarchive" data-toggle="tooltip" data-placement="top" data-original-title="Désarchiver" data-id="<?= $rappel->getId(); ?>"><i class="fa fa-undo"></i></a>
                                                <?php endif;?>
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_rappel')) :?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $rappel->getId(); ?>"><i class="far fa-trash-alt"></i></a>
                                                <?php endif;?>
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

        var msgsucces = "Rappel supprimé avec succès";
        $(document).on("click", ".delete", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_rappel/controleurs/router.php?task=deleteRappel", order, function(theResponse) {
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

        var msgsucces = "Rappel désarchivé avec succès";
        $(document).on("click", ".desarchive", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_rappel/controleurs/router.php?task=desarchiveRappel", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la désarchivation<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })
    });
</script>