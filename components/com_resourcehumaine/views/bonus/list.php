<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Bonus</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Bonus : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                        <button type="button" id="btnRecalculateBonus" class="btn btn-info mr-1" data-id="<?= $resourcehumaine->getId(); ?>" data-toggle="tooltip" data-placement="top" data-original-title="Recalculer le bonus à partir des bulletins de paie">
                            <i class="fas fa-calculator mr-1"></i> Recalculer bonus
                        </button>
                    <?php endif; ?>
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) : ?>
                        <a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter Bonus">
                            <i class="fas fa-plus"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php include("components/com_resourcehumaine/views/resourcehumaine/_profile_header.php"); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?= isset($bonus) ? 'Modifier le bonus' : 'Ajouter un bonus' ?></h4>
                    </div>
                    <div class="card-body">
                        <?php include("form.php"); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include "components/com_resourcehumaine/views/bonus/bonuses.php"; ?>

    </div>
</div>
<!-- /Page Wrapper -->

<!-- Choix de la période avant recalcul du bonus (voir style .tva-confirm-modal établi) -->
<div id="recalc-bonus-modal" class="modal custom-modal tva-confirm-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="tva-confirm-icon"><i class="fa fa-calculator"></i></div>
                <h5 class="modal-title mt-3">Recalculer le bonus</h5>
            </div>
            <div class="modal-body text-center" id="recalc-bonus-modal-body">
                <p class="mb-3">Le montant net de chaque bulletin de paie est comparé au salaire déclaré sur la fiche employé. L'écart devient le bonus.</p>
                <p class="mb-1"><strong>Sur quelle période voulez-vous calculer le bonus ?</strong></p>
                <div class="d-flex justify-content-center flex-wrap" style="gap:10px;margin-top:16px;">
                    <button type="button" class="btn btn-outline-primary recalc-bonus-scope" data-scope="all"><i class="fa fa-infinity mr-1"></i> Depuis le début</button>
                    <button type="button" class="btn btn-primary recalc-bonus-scope" data-scope="year"><i class="fa fa-calendar mr-1"></i> Année <?= date('Y') ?> en cours</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Icône "en cours" animée pendant le recalcul (appels IA séquentiels, peut prendre du
       temps sur beaucoup de bulletins) - anneau qui tourne autour de l'icône calculatrice. */
    .recalc-bonus-spinner {
        display: inline-block;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        border: 3px solid rgba(99, 102, 241, 0.2);
        border-top-color: #6366f1;
        animation: recalcBonusSpin 0.9s linear infinite;
        margin: 0 auto 12px;
    }
    @keyframes recalcBonusSpin {
        to { transform: rotate(360deg); }
    }
</style>

<script type="text/javascript">
    $(function() {
        $("#btnRecalculateBonus").on("click", function() {
            $("#recalc-bonus-modal").data("id", $(this).data("id"));
            $("#recalc-bonus-modal-body").html(
                '<p class="mb-3">Le montant net de chaque bulletin de paie est comparé au salaire déclaré sur la fiche employé. L\'écart devient le bonus.</p>' +
                '<p class="mb-1"><strong>Sur quelle période voulez-vous calculer le bonus ?</strong></p>' +
                '<div class="d-flex justify-content-center flex-wrap" style="gap:10px;margin-top:16px;">' +
                '<button type="button" class="btn btn-outline-primary recalc-bonus-scope" data-scope="all"><i class="fa fa-infinity mr-1"></i> Depuis le début</button>' +
                '<button type="button" class="btn btn-primary recalc-bonus-scope" data-scope="year"><i class="fa fa-calendar mr-1"></i> Année <?= date('Y') ?> en cours</button>' +
                '</div>'
            );
            $("#recalc-bonus-modal").modal("show");
        });

        $(document).on("click", ".recalc-bonus-scope", function() {
            var scope = $(this).data("scope");
            var id = $("#recalc-bonus-modal").data("id");

            $("#recalc-bonus-modal-body").html(
                '<div class="recalc-bonus-spinner"></div>' +
                '<p class="mb-1"><strong>Calcul en cours...</strong></p>' +
                '<p class="text-muted mb-0" style="font-size:0.85rem;">Lecture des bulletins de paie par IA - ça peut prendre une minute ou deux selon leur nombre.</p>'
            );

            $.post("components/com_resourcehumaine/controleurs/router.php?task=recalculateBonus", { id: id, scope: scope }, function(theResponse) {
                var data;
                try { data = JSON.parse(theResponse); } catch (e) { data = { success: 0 }; }

                if (data.success === 1) {
                    $("#recalc-bonus-modal-body").html(
                        '<div class="tva-confirm-icon" style="background:#e8f9f0;color:#16a34a;"><i class="fa fa-check"></i></div>' +
                        '<h5 class="mt-3 mb-2">Bonus recalculé</h5>' +
                        '<p class="mb-1">' + data.nb_bulletins + ' bulletin(s) pris en compte (' + data.nb_extraits + ' nouvellement lu(s) par IA).</p>' +
                        '<p class="mb-1">Total reçu : <strong>' + Number(data.total_recu).toLocaleString('fr-FR', {maximumFractionDigits: 2}) + ' MAD</strong></p>' +
                        '<p class="mb-1">Total déclaré : <strong>' + Number(data.total_declare).toLocaleString('fr-FR', {maximumFractionDigits: 2}) + ' MAD</strong></p>' +
                        '<p class="mb-0">Bonus : <strong class="text-success">' + Number(data.bonus).toLocaleString('fr-FR', {maximumFractionDigits: 2}) + ' MAD</strong></p>'
                    );
                    setTimeout(function() { location.reload(); }, 2200);
                } else {
                    $("#recalc-bonus-modal-body").html(
                        '<div class="tva-confirm-icon" style="background:#fdecea;color:#dc2626;"><i class="fa fa-times"></i></div>' +
                        '<h5 class="mt-3 mb-2">Échec du calcul</h5>' +
                        '<p class="mb-0 text-muted">' + (data.message || 'Une erreur est survenue.') + '</p>'
                    );
                }
            }).fail(function() {
                $("#recalc-bonus-modal-body").html(
                    '<div class="tva-confirm-icon" style="background:#fdecea;color:#dc2626;"><i class="fa fa-times"></i></div>' +
                    '<h5 class="mt-3 mb-2">Échec du calcul</h5>' +
                    '<p class="mb-0 text-muted">La requête a échoué ou a expiré.</p>'
                );
            });
        });

        var msgsucces = "Bonus supprimé avec succès";

        $(document).on("click", ".delete", function() {
            event.preventDefault();
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_resourcehumaine/controleurs/router.php?task=deleteBonusResourceHumaine", order, function(theResponse) {
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
        $('form#bonusResourceHumaineForm').ajaxForm({
            beforeSubmit: function() {
                $("#bonusResourceHumaineForm .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#bonusResourceHumaineForm .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                var msgsucces = "Bonus ajouté avec succès";
                if ($(".submit").attr("name") === "edit") {
                    msgsucces = "Bonus modifié avec succès";
                }
                if (parseInt(theResponse) === 1) {
                    $('#bonusResourceHumaineForm .msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');

                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (parseInt(theResponse) === 0) {
                    $('#bonusResourceHumaineForm .msgbox').html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention!</strong> Veuillez remplir les champs obligatoires<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('#bonusResourceHumaineForm .msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'execution de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            }
        });
    });
</script>