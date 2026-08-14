<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Parrainages</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Parrainages : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php include("components/com_resourcehumaine/views/resourcehumaine/_profile_header.php"); ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">Commission de parrainage : <?= number_format($resourcehumaine->getCommissionParrainage(), 2, ',', ' ') ?> MAD par client validé — <a href="index.php?option=com_resourcehumaine&task=edit&id=<?= $resourcehumaine->getId() ?>">modifier</a></h4>
                    </div>
                    <div class="card-body">
                        <div class="col msgbox mt-3"></div>
                        <div class="table-responsive list-box">
                            <table class="table table-stripped table-center table-hover datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Contact</th>
                                        <th>Raison sociale</th>
                                        <th>Correspondance CRM</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parrainages as $unParrainage) : ?>
                                        <tr>
                                            <td><?= $unParrainage->getId(); ?></td>
                                            <td><?= htmlspecialchars($unParrainage->getPrenom() . ' ' . $unParrainage->getNom()) ?><?php if ($unParrainage->getEmail()) : ?><br><span class="text-muted"><?= htmlspecialchars($unParrainage->getEmail()) ?></span><?php endif; ?></td>
                                            <td><?= htmlspecialchars($unParrainage->getRaisonSocial()) ?></td>
                                            <td>
                                                <?php if ($unParrainage->getClient()) : ?>
                                                    <span class="badge bg-success-light"><i class="fa fa-check mr-1"></i><?= htmlspecialchars($unParrainage->getClient()->getRaisonSocial()) ?></span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary-light">Aucune</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $unParrainage->getMontantCommission() ? number_format($unParrainage->getMontantCommission(), 2, ',', ' ') . ' MAD' : '—' ?></td>
                                            <td>
                                                <?php if ($unParrainage->getStatut() == parrainage::STATUT_EN_ATTENTE) : ?>
                                                    <span class="badge badge-warning text-white">En attente</span>
                                                <?php elseif ($unParrainage->getStatut() == parrainage::STATUT_VALIDE) : ?>
                                                    <span class="badge badge-success">Validé</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">Refusé</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php if ($unParrainage->getStatut() == parrainage::STATUT_EN_ATTENTE && $_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2 validateParrainage" data-id="<?= $unParrainage->getId(); ?>" data-toggle="tooltip" title="Valider"><i class="fa fa-check"></i></a>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 rejectParrainage" data-id="<?= $unParrainage->getId(); ?>" data-toggle="tooltip" title="Refuser"><i class="fa fa-times"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (empty($parrainages)) : ?>
                                <p class="text-center">Aucun parrainage soumis par cet employé</p>
                            <?php endif; ?>
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
        $(document).on("click", ".validateParrainage, .rejectParrainage", function() {
            var $btn = $(this);
            var task = $btn.hasClass('validateParrainage') ? 'validateParrainage' : 'rejectParrainage';
            var confirmMsg = task === 'validateParrainage' ? "Valider ce parrainage et créditer la commission ?" : "Refuser ce parrainage ?";
            if (confirm(confirmMsg)) {
                var id = $btn.attr("data-id");
                $.post("components/com_resourcehumaine/controleurs/router.php?task=" + task, { id: id }, function(theResponse) {
                    if (parseInt(theResponse) === 1) {
                        location.reload();
                    } else {
                        $(".msgbox").html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-times'></i> <strong>Erreur! </strong>Échec de l'opération.</div>").slideDown();
                    }
                });
            }
        });
    });
</script>
