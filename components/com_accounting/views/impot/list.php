<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Impôts (IS / IR)</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Impôts </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_accounting')) : ?>
                        <a href="index.php?option=com_accounting&task=impot" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une déclaration" target="_blank">
                            <i class="fas fa-plus"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0 <?= $kpiMontantDu > 0 ? 'kpi-blink' : '' ?>">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-1"><i class="fa fa-exclamation-circle"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Total dû (<?= $anneeCourante ?>)</div>
                                <div class="dash-counts"><p><?= number_format($kpiMontantDu, 0, ',', ' ') ?> MAD</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-3"><i class="fa fa-check-circle"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Déposé (<?= $anneeCourante ?>)</div>
                                <div class="dash-counts"><p><?= number_format($kpiMontantDepose, 0, ',', ' ') ?> MAD</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0 <?= $kpiDeclarationsEnRetard > 0 ? 'kpi-blink' : '' ?>">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-9"><i class="fa fa-hourglass-half"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Déclarations en retard</div>
                                <div class="dash-counts"><p><?= $kpiDeclarationsEnRetard ?></p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-2"><i class="fa fa-calendar-alt"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Échéance IS indicative</div>
                                <div class="dash-counts"><p style="font-size: 15px;">31 mars <?= $anneeCourante ?></p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-muted small mb-4">Échéance IS indicative pour le solde de l'impôt sur les sociétés de l'exercice précédent - à confirmer chaque année avec le comptable, les dates de dépôt IS/IR pouvant varier selon le régime fiscal de l'agence.</p>

        <div class="row">
            <div class="col-md-12">
                <div class="card <?= !empty($anneesManquantes) ? 'tva-manquants-card kpi-blink' : '' ?> mb-0" id="impot-manquants-card">
                    <div class="card-body">
                        <?php if (!empty($anneesManquantes)) : ?>
                            <div class="d-flex align-items-start">
                                <span class="dash-widget-icon bg-9 mr-3"><i class="fa fa-exclamation-triangle"></i></span>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 text-danger">
                                        <span id="impot-manquants-count"><?= count($anneesManquantes) ?></span> déclaration(s) IS/IR manquante(s) depuis la création de l'agence (<?= $anneeCreationAgence ?>)
                                    </h5>
                                    <p class="text-muted mb-2" style="font-size:0.85rem;">
                                        Cliquez pour ajouter rapidement la déclaration manquante (à compléter ensuite).
                                    </p>
                                    <?php foreach ($anneesManquantes as $manquante) : ?>
                                        <a href="javascript:void(0)" class="badge bg-danger-light impot-missing-item mr-1 mb-1"
                                           data-annee="<?= $manquante['annee'] ?>" data-type="<?= $manquante['type'] ?>"
                                           data-toggle="tooltip" data-placement="top" data-original-title="Cliquez pour ajouter l'<?= $manquante['type'] ?> de <?= $manquante['annee'] ?>">
                                            <?= $manquante['annee'] ?> - <?= $manquante['type'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="d-flex align-items-center">
                                <span class="dash-widget-icon bg-3 mr-3"><i class="fa fa-check"></i></span>
                                <div>
                                    <strong class="text-success">Aucune déclaration manquante</strong>
                                    <span class="text-muted"> — l'IS et l'IR de chaque année depuis la création de l'agence sont enregistrés.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card" id="impot-add-form-card">
                    <div class="card-header">
						<h4 class="card-title"><?=isset($impot) ? 'Modifier la déclaration' : 'Ajouter une déclaration'?></h4>
					</div>
                    <div class="card-body">
                        <div id="impot-missing-reminder" class="alert alert-warning d-none" style="font-size:0.9rem;">
                            <i class="fa fa-info-circle mr-1"></i>
                            Vous ajoutez la déclaration <strong id="impot-missing-reminder-item"></strong> — saisissez le montant puis cliquez sur « Ajouter ».
                        </div>
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                <div class="card-header">
						<h4 class="card-title">Liste des déclarations</h4>
					</div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Type</th>
                                        <th>Date de dépot</th>
                                        <th>Année</th>
                                        <th>Montant</th>
                                        <th>Majoration</th>
                                        <th>Statut</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($impots as $impotItem): ?>
                                        <tr>
                                            <td><?php echo $impotItem->getId(); ?></td>
                                            <td><span class="badge badge-info"><?php echo $impotItem->getType(); ?></span></td>
                                            <td><?php echo date("d/m/Y",strtotime($impotItem->getDateOfDepot())); ?></td>
                                            <td><b><?php echo $impotItem->getYear(); ?></b></td>
                                            <td><b><?php echo floatval($impotItem->getAmount()); ?> MAD</b></td>
                                            <td><b><?php echo floatval($impotItem->getIncreasion()); ?> MAD</b></td>

                                            <td>
                                                <?php if ($impotItem->getStatus() == 1) : ?>
                                                    <span class="badge badge-success">Déposé</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">Pas déposé</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($impotItem->getDoc() != '') : ?>
                                                    <a href="images/impot/<?php echo $impotItem->getDoc(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox=""><i class="fa fa-file"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) : ?>
                                                    <a href="index.php?option=com_accounting&task=impot&id_impot=<?= $impotItem->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) : ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $impotItem->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

<!-- Popup "Ajouter une déclaration manquante" -->
<div id="impot-missing-modal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="tva-confirm-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                <h5 class="modal-title mt-3">Déclaration manquante</h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Voulez-vous ajouter la déclaration <strong id="impot-missing-modal-item">—</strong> ?</p>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    Vous allez être amené(e) au formulaire ci-dessous avec le type et l'année déjà sélectionnés —
                    <strong>il faudra ensuite saisir le montant et cliquer sur "Ajouter" pour l'enregistrer réellement.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                <button type="button" id="impot-missing-modal-confirm" class="btn btn-primary"><i class="fa fa-arrow-down mr-1"></i> Remplir le formulaire</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        var $impotCibleManquante = null;

        $(document).on("click", ".impot-missing-item", function() {
            var $badge = $(this);
            $impotCibleManquante = { annee: $badge.attr("data-annee"), type: $badge.attr("data-type"), badge: $badge };
            $('#impot-missing-modal-item').text($impotCibleManquante.annee + ' - ' + $impotCibleManquante.type);
            $('#impot-missing-modal').modal('show');
        });

        $(document).on("click", "#impot-missing-modal-confirm", function() {
            if (!$impotCibleManquante) return;
            $('#impot-missing-modal').modal('hide');

            $('select[name=type]').val($impotCibleManquante.type).trigger('change');
            $('select[name=year]').val($impotCibleManquante.annee).trigger('change');

            $('#impot-missing-reminder-item').text($impotCibleManquante.annee + ' - ' + $impotCibleManquante.type);
            $('#impot-missing-reminder').removeClass('d-none');

            setTimeout(function() {
                var $formCard = $('#impot-add-form-card');
                $('html, body').animate({ scrollTop: $formCard.offset().top - 80 }, 'slow');
                $formCard.addClass('tva-form-highlight');
                setTimeout(function() { $formCard.removeClass('tva-form-highlight'); }, 1800);
            }, 350);
        });

        $(document).on("click", ".deleteDoc", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteDocImpot", order, function(theResponse) {
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

        var msgsucces = "Déclaration supprimée avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteImpot", order, function(theResponse) {
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
        $('form#impotForm').ajaxForm({
            beforeSubmit: function() {
                $("#impotForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                $("#impotForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Déclaration ajoutée avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Déclaration modifiée avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#impotForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    var submittedYear = $('#impotForm select[name=year]').val();
                    var submittedType = $('#impotForm select[name=type]').val();
                    if ($impotCibleManquante && submittedYear === $impotCibleManquante.annee && submittedType === $impotCibleManquante.type) {
                        $impotCibleManquante.badge.removeClass("bg-danger-light").addClass("bg-success-light");
                        $impotCibleManquante.badge.html('<i class="fa fa-check mr-1"></i>' + $impotCibleManquante.annee + ' - ' + $impotCibleManquante.type);
                    }
                    $impotCibleManquante = null;

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#impotForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#impotForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>
