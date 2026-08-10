<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Cotisations</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Cotisation </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_accounting')) : ?>
                        <a href="index.php?option=com_accounting&task=cnss" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter une cotisation" target="_blank">
                            <i class="fas fa-plus"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0 <?= $kpiMontantAPayer > 0 ? 'kpi-blink' : '' ?>">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-1"><i class="fa fa-exclamation-circle"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Montant à payer (<?= $anneeCourante ?>)</div>
                                <div class="dash-counts"><p><?= number_format($kpiMontantAPayer, 0, ',', ' ') ?> MAD</p></div>
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
                <div class="card flex-fill mb-0">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-2"><i class="fa fa-users"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Effectifs déclarés</div>
                                <div class="dash-counts"><p><?= $kpiEffectifsDeclares ?></p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card flex-fill mb-0 <?= $kpiMoisManquants > 0 ? 'kpi-blink' : '' ?>">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon bg-9"><i class="fa fa-hourglass-half"></i></span>
                            <div class="dash-count">
                                <div class="dash-title">Échéances en attente</div>
                                <div class="dash-counts"><p><?= $kpiMoisManquants ?></p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card <?= $kpiMoisManquants > 0 ? 'tva-manquants-card kpi-blink' : '' ?> mb-0" id="cnss-manquants-card">
                    <div class="card-body">
                        <?php if ($kpiMoisManquants > 0) : ?>
                            <div class="d-flex align-items-start" id="cnss-manquants-content">
                                <span class="dash-widget-icon bg-9 mr-3"><i class="fa fa-exclamation-triangle"></i></span>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 text-danger">
                                        <span id="cnss-manquants-count"><?= $kpiMoisManquants ?></span> mois sans CNSS déposée depuis la création de l'agence (<?= $anneeCreationAgence ?>)
                                    </h5>
                                    <p class="text-muted mb-2" style="font-size:0.85rem;">
                                        Aucune cotisation trouvée pour ces mois ci-dessous — cliquez sur un mois pour l'ajouter rapidement (à compléter ensuite).
                                    </p>
                                    <?php foreach ($manquantsParAnneeCnss as $anneeM => $listeMois) : ?>
                                        <div class="mb-1 cnss-manquants-annee" id="cnss-manquants-annee-<?= $anneeM ?>">
                                            <strong><?= $anneeM ?></strong>
                                            (<span class="cnss-manquants-annee-count"><?= count($listeMois) ?></span>) :
                                            <?php foreach ($listeMois as $moisInfo) : ?>
                                                <a href="javascript:void(0)" class="badge bg-danger-light cnss-missing-month mr-1 mb-1"
                                                   data-annee="<?= $anneeM ?>" data-mois="<?= $moisInfo['num'] ?>" data-mois-nom="<?= $moisInfo['nom'] ?>"
                                                   data-toggle="tooltip" data-placement="top" data-original-title="Cliquez pour ajouter la CNSS de <?= $moisInfo['nom'] ?> <?= $anneeM ?>">
                                                    <?= $moisInfo['nom'] ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="d-flex align-items-center">
                                <span class="dash-widget-icon bg-3 mr-3"><i class="fa fa-check"></i></span>
                                <div>
                                    <strong class="text-success">Aucun mois manquant</strong>
                                    <span class="text-muted"> — toutes les cotisations CNSS depuis la création de l'agence sont enregistrées.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
						<h4 class="card-title">Statistique des cotisations</h4>
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
                                        $cnss_by_year = cnss::findByYear($_SESSION['agence'],$i);
                                        if(sizeof($cnss_by_year) > 0 || $i == date('Y',strtotime(date('Y-m-d')))) :
                                        $total_amount_yearly = 0;
                                    ?>

                                        <tr>
                                            <td><?=$i?></td>
                                            <?php foreach(months() as $month) :
                                                $cnss_by_date = cnss::findByDate($_SESSION['agence'],$i.'-'.$month['number']);
                                                $total_amount_monthly = 0;
                                                ?>
                                                
                                                <th>
                                                    <?php 
                                                        foreach ($cnss_by_date as $key => $value) {
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
                                        <th colspan="4">Total déposé : <span class="text-success"><?=$total_amount_paid?> MAD</span></th>
                                    </tr>
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Total non déposé : <span class="text-danger"><?=$total_amount_unpaid?> MAD</span></th>
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
                <div class="card" id="cnss-add-form-card">
                    <div class="card-header">
						<h4 class="card-title"><?=isset($cnss) ? 'Modifier la cotisation' : 'Ajouter une cotisation'?></h4>
					</div>
                    <div class="card-body">
                        <div id="cnss-missing-reminder" class="alert alert-warning d-none" style="font-size:0.9rem;">
                            <i class="fa fa-info-circle mr-1"></i>
                            Vous ajoutez la CNSS de <strong id="cnss-missing-reminder-mois"></strong> — saisissez le montant puis cliquez sur « Ajouter ».
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
						<h4 class="card-title">Liste des cotisations</h4>
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
                                        <th>Justification</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cnsses as $cnss): ?>
                                        <tr>
                                            <td><?php echo $cnss->getId(); ?></td>
                                            <td><b><?php echo floatval($cnss->getAmount()); ?> MAD</b></td>
                                            <td><b><?php echo floatval($cnss->getIncreasion()); ?> MAD</b></td>
                                            <td><?php echo date("m/Y",strtotime($cnss->getDate())); ?></td>
                                            <td>
                                                <?php if ($cnss->getStatus() == 1) : ?>
                                                    <span class="badge badge-success">Déposé</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">Pas déposé</span>
                                                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) : ?>
                                                        <a href="javascript:void(0);" class="cnss-marquer-depose ml-1" data-id="<?= $cnss->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Marquer comme déposé"><i class="fa fa-check-circle text-success"></i></a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(isset($cnss) && $cnss->getJustification()) :?>
                                                    <a href="./images/accounting/cnss/<?php echo $cnss->getJustification();?>" class="btn btn-success ml-2" target="_blank"><i class="fa fa-file-alt mt-2"></i></a>
                                                <?php endif;?>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_accounting')) : ?>
                                                    <a href="index.php?option=com_accounting&task=cnss&id_cnss=<?= $cnss->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif; ?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_accounting')) : ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $cnss->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

<!-- Popup "Ajouter la CNSS d'un mois manquant" -->
<div id="cnss-missing-modal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="tva-confirm-icon"><i class="fa fa-shield-alt"></i></div>
                <h5 class="modal-title mt-3">Cotisation CNSS manquante</h5>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Voulez-vous ajouter la CNSS de <strong id="cnss-missing-modal-mois">—</strong> ?</p>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    Vous allez être amené(e) au formulaire ci-dessous avec ce mois déjà sélectionné —
                    <strong>il faudra ensuite saisir le montant et cliquer sur "Ajouter" pour l'enregistrer réellement.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Annuler</button>
                <button type="button" id="cnss-missing-modal-confirm" class="btn btn-primary"><i class="fa fa-arrow-down mr-1"></i> Remplir le formulaire</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {

        // Ajout depuis l'alerte "mois manquants" : clic sur un mois -> popup de confirmation ->
        // à la confirmation, ouvre le vrai formulaire "Ajouter une cotisation" avec le mois/année
        // déjà sélectionnés. $cnssCibleManquante garde la trace du badge visé pour le faire passer
        // au vert quand ce même mois est effectivement soumis - même principe que la page TVA.
        var $cnssCibleManquante = null;

        $(document).on("click", ".cnss-missing-month", function() {
            var $badge = $(this);
            $cnssCibleManquante = {
                annee: $badge.attr("data-annee"),
                mois: $badge.attr("data-mois"),
                moisNom: $badge.attr("data-mois-nom"),
                badge: $badge
            };

            $('#cnss-missing-modal-mois').text($cnssCibleManquante.moisNom + ' ' + $cnssCibleManquante.annee);
            $('#cnss-missing-modal').modal('show');
        });

        $(document).on("click", "#cnss-missing-modal-confirm", function() {
            if (!$cnssCibleManquante) return;
            $('#cnss-missing-modal').modal('hide');

            var moisPadded = ("0" + $cnssCibleManquante.mois).slice(-2);
            $('#month-input').val($cnssCibleManquante.annee + '-' + moisPadded);

            $('#cnss-missing-reminder-mois').text($cnssCibleManquante.moisNom + ' ' + $cnssCibleManquante.annee);
            $('#cnss-missing-reminder').removeClass('d-none');

            setTimeout(function() {
                var $formCard = $('#cnss-add-form-card');
                $('html, body').animate({ scrollTop: $formCard.offset().top - 80 }, 'slow');
                $formCard.addClass('tva-form-highlight');
                setTimeout(function() { $formCard.removeClass('tva-form-highlight'); }, 1800);
            }, 350);
        });

        var msgsucces = "Cotisation supprimé avec succès";

        $(document).on("click", ".cnss-marquer-depose", function() {
            var $lien = $(this);
            var $badgeCell = $lien.closest('td');
            $.post("components/com_accounting/controleurs/router.php?task=toggleCnssStatus", { id: $lien.attr("data-id") }, function(theResponse) {
                if (parseInt(theResponse) === 1) {
                    $badgeCell.html('<span class="badge badge-success">Déposé</span>');
                } else {
                    $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la mise à jour du statut<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            });
        });

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_accounting/controleurs/router.php?task=deleteCnss", order, function(theResponse) {
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
        $('form#cnssForm').ajaxForm({
            beforeSubmit: function() {
                $("#cnssForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#cnssForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Cotisation ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Cotisation modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#cnssForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    // Si cet ajout correspond au mois visé depuis l'alerte "mois manquants" (popup
                    // -> formulaire pré-rempli), on fait passer son badge au vert avant le
                    // rechargement de page (qui, lui, le retirera pour de bon puisqu'il ne sera
                    // plus manquant).
                    var submittedDate = $('#cnssForm input[name=date]').val();
                    if ($cnssCibleManquante) {
                        var moisPaddedSubmit = ("0" + $cnssCibleManquante.mois).slice(-2);
                        if (submittedDate === ($cnssCibleManquante.annee + '-' + moisPaddedSubmit)) {
                            $cnssCibleManquante.badge.removeClass("bg-danger-light").addClass("bg-success-light");
                            $cnssCibleManquante.badge.html('<i class="fa fa-check mr-1"></i>' + $cnssCibleManquante.moisNom);
                        }
                        $cnssCibleManquante = null;
                    }

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#cnssForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#cnssForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>