<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Gestion des temoignages</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Temoignages</li>
                    </ul>
                </div>
                <?php if ($_SESSION['user']->hasDroit('add', 'com_temoignage')) :?>
                    <div class="col-auto">
                        <a href="index.php?option=com_temoignage&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter temoignage">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                <?php endif;?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Liste des temoignages</h4>
                    </div>
                    <div class="card-body">
                        <div class="col-sm-12 mt-3 msgbox"></div>

                        <div class="table-responsive">
                            <table class="table table-stripped table-center table-hover datatable">
                                <caption style="text-align: left; margin-left: 25px; margin-top: 20px; ">
                                    <i class="fa fa-level-down-alt fa-rotate-180" style="font-size: 12pt;"></i>
                                    <label for="" style="margin-left: 20px; ">Avec la sélection :</label>
                                    <div style="margin-left: 20px; display: inline-block;">


                                        <a href="#0" id="enable_multiple" class="btn btn-sm btn-white text-success disable" data-toggle="tooltip" data-original-title="Activer">
                                            <i class="fa fa-toggle-on"></i>
                                        </a>
                                        <a href="#0" id="disable_multiple" class="btn btn-sm btn-white text-warning disable" data-toggle="tooltip" data-original-title="Désactiver">
                                            <i class="fa fa-toggle-off"></i>
                                        </a>
                                        <a href="#0" id="delete_multiple" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" class="btn btn-sm btn-white text-danger"><i class="far fa-trash-alt"></i></a>


                                    </div>

                                </caption>
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            <input type="hidden" id="idVal">
                                            <input type="checkbox" name="" id="checkAll" data-toggle="tooltip" data-placement="top" data-original-title="Sélectionner tout">
                                        </th>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Fonction</th>
                                        <th>E-mail</th>
                                        <th>Sujet</th>
                                        <th>Ordre</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($temoignages as $temoignage) : ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="checkElement" value="<?php echo $temoignage->getId(); ?>">
                                            </td>
                                            <td><?php echo $temoignage->getId(); ?></td>
                                            <td><?= $temoignage->getNom(); ?></td>
                                            <td><?= $temoignage->getFonction(); ?></td>
                                            <td><?= $temoignage->getEmail(); ?></td>
                                            <td><?= $temoignage->getsujet(); ?></td>
                                            <td><?= $temoignage->getOrdre(); ?></td>
                                            <td class="text-right">
                                                <?php $state = $temoignage->isActive() ? 'oui' : 'non'; ?>
                                                <?php $title = $temoignage->isActive() ? 'Actif' : 'Inactif'; ?>
                                                <?php $color = $temoignage->isActive() ? 'text-success' : 'text-danger'; ?>
                                                <?php $ico = $temoignage->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_temoignage')) :?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enable" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $temoignage->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
                                                    <a href="index.php?option=com_temoignage&task=edit&id=<?= $temoignage->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                <?php endif;?>
                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_temoignage')) :?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $temoignage->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

        var msgsucces = "Temoignage supprimé avec succès";

        $(document).on("click", ".delete", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_temoignage/controleurs/router.php?task=deleteTemoignage", order, function(theResponse) {
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

        $(".enable").click(function() {
            var btn = $(this);
            var id = btn.attr("data-id");
            var state = btn.attr("data-state");
            var order = 'id=' + id + "&state=" + state;
            $.post("components/com_temoignage/controleurs/router.php?task=enableTemoignage", order, function(theResponse) {
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



    $(document).ready(function() {

        $('#enable_multiple, #disable_multiple').click(function() {
            var active = ($(this).attr('id') == "enable_multiple") ? "oui" : "non";

            var activationTxt = ($(this).attr('id') == "enable_multiple") ? "activer" : "désactiver";

            var test = false;
            var idsT = [];
            $(".checkElement").each(function() {
                if ($(this).is(':checked')) {
                    test = true;
                    idsT.push($(this).val());
                }

            });


            if (test) {
                if (confirm("Voulez vous " + activationTxt + " les temoignages sélectionnés ??")) {
                    var ids = "(" + idsT + ")";

                    var order = 'ids=' + ids + '&active=' + active;
                    $.post(
                        "components/com_temoignage/controleurs/router.php?task=enableTemoignages",
                        order,
                        function(theResponse) {
                            if (parseInt(theResponse) == 1) {
                                var iconActive = "";
                                $("#checkAll").prop("checked", false);
                                $("#checkAll").change();
                                if (active == "oui") {
                                    iconActive += '<a href="#0" data-toggle="tooltip" data-placement="top" ';
                                    iconActive += 'data-original-title="Active" class="btn btn-success btn-xs">';
                                    iconActive += '<i class="fa fa-toggle-on"></i></a>';
                                } else {
                                    iconActive += '<a href="#0" data-toggle="tooltip" data-placement="top" ';
                                    iconActive += 'data-original-title="Active" class="btn btn-warning btn-xs">';
                                    iconActive += '<i class="fa fa-toggle-off"></i></a>';
                                }

                                $(idsT).each(function(i, id) {
                                    $("#row_" + id).children().eq($("#row_" + id).children().length - 1).children().eq(0).remove();
                                    $("#row_" + id).children().eq($("#row_" + id).children().length - 1).prepend(iconActive);
                                });


                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succ&egrave;s</strong> les temoignages sont ' + activationTxt.substr(0, activationTxt.length - 2) + 'és avec succ&egrave;s</div>');
                                $('.msgbox').slideDown();


                                setTimeout(function() {
                                    document.location = "index.php?option=com_temoignage";
                                }, 2000);

                            } else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de ' + ((active == "oui") ? "l\'activation" : "la désactivation") + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                                $('.msgbox').slideDown();
                            }
                        }
                    );
                }
            } else {
                $('.msgbox').html('<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Avertissement!</strong> Aucune ligne n\'a été sélectionnée</div>');
                $('.msgbox').slideDown();
            }
        });

        $('#delete_multiple').click(function() {
            var test = false;
            var idsT = [];
            $(".checkElement").each(function() {
                if ($(this).is(':checked')) {
                    test = true;
                    idsT.push($(this).val());
                }

            });


            if (test) {
                if (confirm("Voulez vous supprimer les temoignages sélectionnés ??")) {
                    var ids = "(" + idsT + ")";

                    var order = 'ids=' + ids;
                    $.post(
                        "components/com_temoignage/controleurs/router.php?task=deleteTemoignages",
                        order,
                        function(theResponse) {
                            if (parseInt(theResponse) == 1) {

                                $('.msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>Succès</strong> les temoignages supprim&eacute;s avec succ&egrave;s</div>');
                                $('.msgbox').slideDown();
                                for (var i = 0; i < idsT.length; i++) {
                                    $("#row_" + idsT[i]).addClass("danger");
                                }
                                setTimeout(function() {
                                    for (var i = 0; i < idsT.length; i++) {
                                        $("#row_" + idsT[i]).remove();
                                    }
                                }, 300);

                                setTimeout(function() {
                                    location = "";
                                }, 2000);


                                // setTimeout(function(){$("#row_"+id).remove()},300);
                            } else {
                                $('.msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>Erreur!</strong> Erreur lors de la suppression</div>');
                                $('.msgbox').slideDown();
                            }
                        }
                    );
                }
            } else {
                $('.msgbox').html('<div class="alert alert-warning alert-dismissable"><i class="icon-remove-sign"></i> <strong>Avertissement!</strong> Aucune ligne n\'a été sélectionnée</div>');
                $('.msgbox').slideDown();
            }
        });




        $("#idVal").val($('.checkElement').eq(0).val());

        $('#checkAll').change(function() {
            var status = $('#checkAll').is(':checked');
            $('.checkElement').each(function() {
                $(this).prop('checked', status);
            });
        });

        $('.checkElement').each(function() {
            $(this).click(function() {
                if ($(this).is(':checked') == false) {
                    $('#checkAll').prop('checked', false);
                } else {
                    var status = true;
                    $('.checkElement').each(function() {
                        if ($(this).is(':checked') == false) {
                            status = false;
                        }
                    });

                    $('#checkAll').prop('checked', status);

                }
            });
        });

        $('body').eq(0).click(function(event) {

            if ($("#idVal").val() != $('.checkElement').eq(0).val()) {
                $("#idVal").val($('.checkElement').eq(0).val());

                $('#checkAll').prop('checked', false);
                $('.checkElement').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

    });
</script>