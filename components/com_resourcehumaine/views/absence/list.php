<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Absences</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Absences : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) : ?>
                        <a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter Absence">
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
                        <h4 class="card-title">Statistic d'absences et congés</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="div-round-profile mb-3">
                                <img onerror="this.src='./images/default-image.jpeg'" src="./images/resourceshumaines/<?= $resourcehumaine->getPhoto() ?>" onerror="this.src='./images/default-image.jpeg'" alt="<?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?>">
                            </div>
                            <h3 class="mb-0"><?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></h3>
                            <span class="text-secondary"><?= $resourcehumaine->getFunction() ?></span>
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
                                <tfooter>
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
                        <h4 class="card-title"><?= isset($absence) ? 'Modifier l\'absence' : 'Ajouter une absence' ?></h4>
                    </div>
                    <div class="card-body">
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include "components/com_resourcehumaine/views/absence/absences.php";?>
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