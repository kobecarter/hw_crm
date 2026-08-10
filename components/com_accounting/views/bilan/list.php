<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Bilan</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Bilan </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_accounting')) : ?>
                        <a href="index.php?option=com_accounting&task=bilan" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter un bilan" target="_blank">
                            <i class="fas fa-plus"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0 <?= $kpiMontantDu > 0 ? 'kpi-blink' : '' ?>">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-1"><i class="fa fa-exclamation-circle"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Montant en attente de dépôt</div>
                                <div class="dash-counts"><p><?= number_format($kpiMontantDu, 0, ',', ' ') ?> MAD</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-3"><i class="fa fa-check-circle"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Total déposé</div>
                                <div class="dash-counts"><p><?= number_format($kpiMontantDepose, 0, ',', ' ') ?> MAD</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-2"><i class="fa fa-calendar-check"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Dernier bilan déposé</div>
                                <div class="dash-counts"><p><?= $kpiDerniereAnnee !== null ? $kpiDerniereAnnee : '—' ?></p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card <?= !empty($anneesManquantes) ? 'tva-manquants-card kpi-blink' : '' ?> mb-0" id="bilan-manquants-card">
                    <div class="card-body">
                        <?php if (!empty($anneesManquantes)) : ?>
                            <div class="d-flex align-items-start">
                                <span class="dash-widget-icon bg-9 mr-3"><i class="fa fa-exclamation-triangle"></i></span>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 text-danger">
                                        <span id="bilan-manquants-count"><?= count($anneesManquantes) ?></span> année(s) sans bilan déposé depuis la création de l'agence (<?= $anneeCreationAgence ?>)
                                    </h5>
                                    <p class="text-muted mb-2" style="font-size:0.85rem;">
                                        Cliquez sur une année pour l'ajouter rapidement (à compléter ensuite).
                                    </p>
                                    <?php foreach ($anneesManquantes as $anneeM) : ?>
                                        <a href="javascript:void(0)" class="badge bg-danger-light bilan-missing-year mr-1 mb-1"
                                           data-annee="<?= $anneeM ?>"
                                           data-toggle="tooltip" data-placement="top" data-original-title="Cliquez pour ajouter le bilan de <?= $anneeM ?>">
                                            <?= $anneeM ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="d-flex align-items-center">
                                <span class="dash-widget-icon bg-3 mr-3"><i class="fa fa-check"></i></span>
                                <div>
                                    <strong class="text-success">Aucune année manquante</strong>
                                    <span class="text-muted"> — tous les bilans depuis la création de l'agence sont enregistrés.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card" id="bilan-add-form-card">
                    <div class="card-header">
						<h4 class="card-title"><?=isset($bilan) ? 'Modifier le bilan' : 'Ajouter un bilan'?></h4>
					</div>
                    <div class="card-body">
                        <div id="bilan-missing-reminder" class="alert alert-warning d-none" style="font-size:0.9rem;">
                            <i class="fa fa-info-circle mr-1"></i>
                            Vous ajoutez le bilan de <strong id="bilan-missing-reminder-annee"></strong> — saisissez le montant puis cliquez sur « Ajouter ».
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
						<h4 class="card-title">Liste des bilan</h4>
					</div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Date de dépot</th>
                                        <th>Année</th>
                                        <th>Montant</th>
                                        <th>Majoration</th>
                                        <th>Statut</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bilans as $bilan): ?>
                                        <tr>
                                            <td><?php echo $bilan->getId(); ?></td>
                                            <td><?php echo date("d/m/Y",strtotime($bilan->getDateOfDepot())); ?></td>
                                            <td><b><?php echo $bilan->getYear(); ?></b></td>
                                            <td><b><?php echo floatval($bilan->getAmount()); ?> MAD</b></td>
                                            <td><b><?php echo floatval($bilan->getIncreasion()); ?> MAD</b></td>
                                            
                                            <td>
                                                <?php if ($bilan->getStatus() == 1) : ?>
                                                    <span class="badge badge-success">Déposé</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">Non déposé</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($bilan->getDoc() != '') : ?>
                                                    <a href="images/bilan/<?php echo $bilan->getDoc(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Voir document" data-fancybox=""><i class="fa fa-file"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) : ?>
                                                    <a href="index.php?option=com_accounting&task=bilan&id_bilan=<?= $bilan->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) : ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $bilan->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

<!-- Popup "Ajouter le bilan d'une année manquante" -->
<div id="bilan-missing-modal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="tva-confirm-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                <h5 class="modal-title mt-3">Bilan manquant</h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Voulez-vous ajouter le bilan de <strong id="bilan-missing-modal-annee">—</strong> ?</p>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    Vous allez être amené(e) au formulaire ci-dessous avec cette année déjà sélectionnée —
                    <strong>il faudra ensuite saisir le montant et cliquer sur "Ajouter" pour l'enregistrer réellement.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                <button type="button" id="bilan-missing-modal-confirm" class="btn btn-primary"><i class="fa fa-arrow-down mr-1"></i> Remplir le formulaire</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        // Ajout depuis l'alerte "années manquantes" : clic sur une année -> popup de confirmation
        // -> à la confirmation, ouvre le vrai formulaire "Ajouter un bilan" avec l'année déjà
        // sélectionnée - même principe que les mois manquants de la page TVA/CNSS, à la maille
        // annuelle.
        var $bilanCibleManquante = null;

        $(document).on("click", ".bilan-missing-year", function() {
            var $badge = $(this);
            $bilanCibleManquante = { annee: $badge.attr("data-annee"), badge: $badge };
            $('#bilan-missing-modal-annee').text($bilanCibleManquante.annee);
            $('#bilan-missing-modal').modal('show');
        });

        $(document).on("click", "#bilan-missing-modal-confirm", function() {
            if (!$bilanCibleManquante) return;
            $('#bilan-missing-modal').modal('hide');

            $('select[name=year]').val($bilanCibleManquante.annee).trigger('change');

            $('#bilan-missing-reminder-annee').text($bilanCibleManquante.annee);
            $('#bilan-missing-reminder').removeClass('d-none');

            setTimeout(function() {
                var $formCard = $('#bilan-add-form-card');
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
                $.post("components/com_accounting/controleurs/router.php?task=deleteDocBilan", order, function(theResponse) {
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

        var msgsucces = "Bilan supprimé avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteBilan", order, function(theResponse) {
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
        $('form#bilanForm').ajaxForm({
            beforeSubmit: function() {
                $("#bilanForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#bilanForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Bilan ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Bilan modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#bilanForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    // Si cet ajout correspond à l'année visée depuis l'alerte "années manquantes",
                    // on fait passer son badge au vert avant le rechargement de page.
                    var submittedYear = $('#bilanForm select[name=year]').val();
                    if ($bilanCibleManquante && submittedYear === $bilanCibleManquante.annee) {
                        $bilanCibleManquante.badge.removeClass("bg-danger-light").addClass("bg-success-light");
                        $bilanCibleManquante.badge.html('<i class="fa fa-check mr-1"></i>' + $bilanCibleManquante.annee);
                    }
                    $bilanCibleManquante = null;

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#bilanForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#bilanForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>