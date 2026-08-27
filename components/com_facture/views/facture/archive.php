<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Archive</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_facture">Factures</a></li>
                        <li class="breadcrumb-item active">Archive</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
                        <a href="index.php?option=com_facture&task=archive" class="btn btn-warning text-white mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Archive">
                            <i class="fas fa-archive"></i>
                        </a>
                        <a href="index.php?option=com_facture&task=unpaid" class="btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Factures impayées">
                            <i class="fa fa-file-invoice"></i>
                        </a>
                        <a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
                            <i class="fas fa-filter"></i>
                        </a>
                    <?php endif;?>
                </div>
            </div>
        </div>

        <!-- Search Filter -->
        <div id="filter_inputs" class="card filter-card">
            <div class="card-body pb-0">
                <form method="post" action="components/com_facture/controleurs/router.php?task=filterFacture" id="filterFacture">
                    <div class="row">
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label>Date début</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control datetimepicker" required name="from" value="<?php if (isset($_GET['from'])) echo normaldate($_GET['from']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label>Date fin</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control datetimepicker" required name="to" value="<?php if (isset($_GET['to'])) echo normaldate($_GET['to']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <button type="submit" name="" class="btn btn-primary submit" style="margin-top: 32px;"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Search Filter -->

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Liste des factures</h4>
                    </div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable" data-order="[]">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Numéro</th>
                                        <th>Client</th>
                                        <th></th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Reste</th>
                                        <th>Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($factures as $facture) : ?>
                                        <?php
                                        switch ($facture->getStatu()) {
                                            case '1':
                                                $statu = '<span class="badge bg-success-light">Payée</span>';
                                                break;
                                            case '2':
                                                $statu = '<span class="badge bg-warning-light">Payée partialement</span>';
                                                break;
                                            default:
                                                $statu = '<span class="badge bg-danger-light">Impayée</span>';
                                                break;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $facture->getId(); ?></td>
                                            <td><a href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>">#<?php echo $facture->getNumero(); ?></a></td>
                                            <td>
                                                <?php $photoLink = $facture->getClient()->getPhoto() != '' ? "images/clients/" . $facture->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                                <h2 class="table-avatar">
                                                    <a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $facture->getClient()->getRaisonSocial(); ?></a>
                                                </h2>
                                            </td>
                                            <td><?php echo $facture->isAvoir() ? '<span class="badge bg-info-light">avoir</span>' : ''; ?></td>
                                            <td data-sort="<?= strtotime($facture->getDateFacture())?>"><?php echo normaldate($facture->getDateFacture()); ?></td>
                                            <td><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                            <td><?php echo number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                            <td><?php echo $statu; ?></td>
                                            <td class="text-right">
                                                <div class="dropdown dropdown-action">
                                                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <?php if($_SESSION['user']->hasDroit('edit', 'com_facture')) :?>
                                                            <a class="dropdown-item text-warning" href="index.php?option=com_facture&task=edit&id=<?php echo $facture->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
                                                        <?php endif;?>
                                                        <?php if($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
                                                            <a class="dropdown-item text-info" href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
                                                        <?php endif;?>
                                                        <?php if($_SESSION['user']->hasDroit('delete', 'com_facture')) :?>
                                                            <a class="dropdown-item text-info retablir" href="javascript:void(0);" data-id="<?php echo $facture->getId(); ?>"><i class="fa fa-undo mr-2"></i>Rétablir</a>
                                                        <?php endif;?>
                                                        <?php if($_SESSION['user']->hasDroit('delete', 'com_facture')) :?>
                                                        <a class="dropdown-item text-danger delete" href="javascript:void(0);" data-id="<?php echo $facture->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
                                                        <?php endif;?>
                                                        <?php if($_SESSION['user']->hasDroit('view', 'com_facture')) :?>
                                                            <a class="dropdown-item text-success" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>"><i class="far fa-money-bill-alt mr-2"></i>Reglement</a>
                                                            <?php if($facture->isGlobalPdfAllowed()): ?>
                                                            <a class="dropdown-item text-danger" href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
                                                            <?php endif; ?>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
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

        // envoi du filtre en ajax
        $('form#filterFacture').ajaxForm({
            beforeSubmit: function() {
                $("#filterFacture .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                $("#filterFacture .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                $(".list-box").html(theResponse)
            }
        });

        var msgsucces = "Facture supprimée avec succès";

        $(document).on("click", ".delete", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_facture/controleurs/router.php?task=deleteFacture", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })
        
        $(document).on("click", ".retablir", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_facture/controleurs/router.php?task=retablirFacture", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Facture rétablie avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        $(".enable").click(function() {
            var btn = $(this);
            var id = btn.attr("data-id");
            var state = btn.attr("data-state");
            var order = 'id=' + id + "&state=" + state;
            $.post("components/com_facture/controleurs/router.php?task=enableClient", order, function(theResponse) {
                var error_msg = "Erreur lors de l'activation.";
                if (state === "oui") {
                    error_msg = "Erreur lors de la désactivation.";
                }
                if (parseInt(theResponse) === 1) {
                    if (state === "oui") {
                        btn.attr("data-state", "non").removeClass("text-success").addClass("text-danger").attr("data-original-title", "Inactif").html("<i class='fa fa-toggle-off'>");
                    } else {
                        btn.attr("data-state", "oui").removeClass("text-danger").addClass("text-success").attr("data-original-title", "Actif").html("<i class='fa fa-toggle-on'>");
                    }
                } else {
                    $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> ' + error_msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            });
        });
    });
</script>