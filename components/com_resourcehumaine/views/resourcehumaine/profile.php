<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Tableau de bord</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active d-flex align-items-center"><i class="fa fa-home mr-2"></i> Tableau de bord</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="div-round-profile mb-3">
                                <img src="./images/resourceshumaines/<?= $resourcehumaine->getPhoto() ?>" onerror="this.src='./images/default-image.jpeg'" alt="<?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?>">
                            </div>
                            <h3 class="mb-0"><?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></h3>
                            <span class="text-secondary"><?= $resourcehumaine->getFunction() ?></span>
                        </div>

                        <div class="div-table-information customer-details-group mt-5">
                            <div class="row justify-content-center">
                                <div class="col-7">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Matricule</h6>
                                                        <p><?= $resourcehumaine->getReference() ? $resourcehumaine->getReference() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Référence pointage</h6>
                                                        <p><?= $resourcehumaine->getReferencePointage() ? $resourcehumaine->getReferencePointage() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>CIN</h6>
                                                        <p><?= $resourcehumaine->getCin() ? $resourcehumaine->getCin() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Prénom</h6>
                                                        <p><?= $resourcehumaine->getFirstName() ? $resourcehumaine->getFirstName() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Nom</h6>
                                                        <p><?= $resourcehumaine->getLastName() ? $resourcehumaine->getLastName() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Email</h6>
                                                        <p><?= $resourcehumaine->getEmail() ? $resourcehumaine->getEmail() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Téléphone</h6>
                                                        <p><?= $resourcehumaine->getPhone() ? $resourcehumaine->getPhone() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Adresse</h6>
                                                        <p><?= $resourcehumaine->getAddress() ? $resourcehumaine->getAddress() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Ville</h6>
                                                        <p><?= $resourcehumaine->getCity() ? $resourcehumaine->getCity() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Source de prospection</h6>
                                                        <p><?= $resourcehumaine->getProspectingSource() ? $resourcehumaine->getProspectingSource() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Numéro de CNSS</h6>
                                                        <p><?= $resourcehumaine->getCnssNumber() ? $resourcehumaine->getCnssNumber() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Fonction</h6>
                                                        <p><?= $resourcehumaine->getFunction() ? $resourcehumaine->getFunction() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Statut</h6>
                                                        <p><?= $resourcehumaine->getStatus() ? $resourcehumaine->getStatus() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Date début</h6>
                                                        <p><?= normaldate($resourcehumaine->getStartDate()) ? $resourcehumaine->getStartDate() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Date de signature de contrat</h6>
                                                        <p><?= normaldate($resourcehumaine->getContractSigningDate()) ? $resourcehumaine->getContractSigningDate() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Date fin</h6>
                                                        <p><?= normaldate($resourcehumaine->getEndDate()) ? $resourcehumaine->getEndDate() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="customer-details">
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-details-cont">
                                                        <h6>Remarque</h6>
                                                        <p><?= normaldate($resourcehumaine->getRemark()) ? $resourcehumaine->getRemark() : '<span class="text-danger">Indéfinie</span>' ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-5">
                            <table class="table mb-0 text-center">
                                <thead>
                                    <tr>
                                        <th class="text-secondary">Année</th>
                                        <?php foreach (months() as $month) : ?>
                                            <th><?= $month['name'] ?></th>
                                        <?php endforeach; ?>
                                        <th>Congé total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $start_date = $resourcehumaine->getStartDate();
                                    $end_date = $resourcehumaine->getEndDate() ? $resourcehumaine->getEndDate() : date("Y-m-d");
                                    $total_holidays = 0;
                                    for ($i = date("Y", strtotime($start_date)); $i <= date("Y", strtotime($end_date)); $i++) :
                                        $total_holidays_per_year = 0;
                                    ?>

                                        <tr>
                                            <td><?= $i ?></td>
                                            <?php foreach (months() as $month) : ?>
                                                <th>
                                                    <?php
                                                    $month_date = date("Y-m-t", strtotime($i . "-" . $month['number'] . "-1"));
                                                    $start_month_date = date("Y-m-d", strtotime($start_date));
                                                    $end_month_date = date("Y-m-d", strtotime($end_date));

                                                    if ($month_date >= $start_month_date && $month_date <= $end_month_date) {
                                                        $total_holidays_per_year += 1.5;
                                                        $total_holidays += 1.5;
                                                        echo '<span class="text-success">1.5</span>';
                                                    } else {
                                                        echo '0';
                                                    }
                                                    ?>
                                                </th>
                                            <?php endforeach; ?>
                                            <th><span class="text-success"><?= $total_holidays_per_year ?></span></th>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                                <tfoot">
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Congé total : <span class="text-success"><?= $total_holidays ?> Jour(s)</span></th>
                                    </tr>
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Congé consommé : <span class="text-danger">
                                                <?php
                                                $total_holidays_consumed = 0;
                                                foreach ($absences as $value) :
                                                    $total_holidays_consumed += ($value->getNatureOfAbsence() == 1 ? $value->getNumberOfDays() : 0);
                                                endforeach;
                                                echo $total_holidays_consumed . " Jour(s)";
                                                ?>
                                            </span></th>
                                    </tr>
                                    <tr class="text-left">
                                        <th colspan="10"></th>
                                        <th colspan="4">Congé resté : <span class="text-warning"><?= $total_holidays - $total_holidays_consumed ?> Jour(s)</span></th>
                                    </tr>
                                    </tfoot>
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

        var msgsucces = "Absence supprimé avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_resourcehumaine/controleurs/router.php?task=deleteAbsenceResourceHumaine", order, function(theResponse) {
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
        $('form#absenceResourceHumaineForm').ajaxForm({
            beforeSubmit: function() {
                $("#absenceResourceHumaineForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#absenceResourceHumaineForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Absence ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Absence modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#absenceResourceHumaineForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#absenceResourceHumaineForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#absenceResourceHumaineForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>