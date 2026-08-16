<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-header">
                <h4 class="card-title">Liste des demandes</h4>
            </div>
            <div class="card-body">
                <div class="col msgboxtable mt-3"></div>
                <div class="table-responsive list-box">
                    <table class="table table-stripped table-center table-hover datatable table-requests">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Réponse</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo $request->getId(); ?></td>
                                    <td><span class="badge badge-info"><?php echo $request->getTypeLabel(); ?></span></td>
                                    <td><?php echo $request->getTitle(); ?></td>
                                    <td><span title="<?= strip_tags($request->getDescription()) ?>"><?php echo substr($request->getDescription(), 0, 30); ?> <?php echo strlen($request->getDescription()) > 30 ? '[...]' : null ?> </span></td>
                                    <td class="response">
                                    <span title="<?= strip_tags($request->getResponse()) ?>"><?php echo substr($request->getResponse(), 0, 30); ?> <?php echo strlen($request->getResponse()) > 30 ? '[...]' : null ?> </span>
                                    </td>
                                    <td class="status">
                                        <?php if ($request->getStatus() == 0) : ?>
                                            <span class="badge badge-warning text-white">En attente</span>
                                        <?php elseif ($request->getStatus() == 1) : ?>
                                            <span class="badge badge-success">Apprové</span>
                                        <?php elseif ($request->getStatus() == 2) : ?>
                                            <span class="badge badge-danger">Refusé</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-success mr-2 changeStatusRequest approve <?= in_array($request->getStatus(), [0, 2]) ? '' : 'd-none' ?>" data-toggle="tooltip" data-placement="top" data-original-title="Approver" data-id="<?= $request->getId(); ?>" data-status="approve"><i class="fa fa-check"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 changeStatusRequest reject <?= in_array($request->getStatus(), [0, 1]) ? '' : 'd-none' ?>" data-toggle="tooltip" data-placement="top" data-original-title="Reject" data-id="<?= $request->getId(); ?>" data-status="reject"><i class="fa fa-times"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-primary mr-2 setResponseRequest" data-toggle="tooltip" data-placement="top" data-original-title="Réponder" data-id="<?= $request->getId(); ?>"><i class="fa fa-comments"></i></a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-white text-info mr-2 showRequest" data-toggle="tooltip" data-placement="top" data-original-title="Afficher" data-id="<?= $request->getId(); ?>"><i class="far fa-eye"></i></a>
                                        <?php if ($_SESSION['user']->isResourceHumaine() && $_SESSION['user']->getId() == $resourcehumaine->getId()) : ?>
                                            <a href="index.php?task=requests&id_request=<?= $request->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $request->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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

<!-- Add Category Modal -->
<div id="div-show-model" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mr-auto">Afficher la demande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="div-show-model-content">
                            <!-- Write here you table code -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Category Modal -->

<script type="text/javascript">
    $(function() {
        $(document).on("click", ".showRequest", function(event) {
            event.preventDefault();
            var $btn = $(this);
            var id = $(this).attr("data-id");
            var order = 'id=' + id;
            $(".div-show-model-content").html("");
            $.post("components/com_resourcehumaine/controleurs/router.php?task=showRequest", order, function(theResponse) {
                $("#div-show-model").find('.model-title').html("Afficher la demande");
                $(".div-show-model-content").html(theResponse);
                $("#div-show-model").modal('show');
            });
        })

        $(document).on("click", ".changeStatusRequest", function(event) {
            event.preventDefault();
            var $btn = $(this);
            var id = $(this).attr("data-id");
            var status = $(this).attr("data-status");
            var order = 'id=' + id + '&status=' + status;
            $.post("components/com_resourcehumaine/controleurs/router.php?task=changeStatusRequest", order, function(theResponse) {
                if (parseInt(theResponse) == 1) {
                    if (status == 'approve') {
                        $btn.parents('tr').find('td.status').html('<span class="badge badge-success text-white">Apprové</span>')
                        $btn.addClass("d-none")
                        $btn.siblings('.reject').removeClass("d-none")
                    } else if (status == 'reject') {
                        $btn.parents('tr').find('td.status').html('<span class="badge badge-danger text-white">Refusé</span>')
                        $btn.addClass("d-none")
                        $btn.siblings('.approve').removeClass("d-none")
                    }
                    $('.msgboxtable').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Instructions effacées avec succès<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                } else {
                    $('.msgboxtable').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            });
        })

        $(document).on("click", ".setResponseRequest", function(event) {
            event.preventDefault();
            var $btn = $(this);
            var id = $(this).attr("data-id");
            var order = 'id=' + id;
            $(".div-show-model-content").html("");
            $.post("components/com_resourcehumaine/controleurs/router.php?task=showSetResponseRequest", order, function(theResponse) {
                $("#div-show-model").find('.model-title').html("Modifier la réponse");
                $(".div-show-model-content").html(theResponse);
                $("#div-show-model").modal('show');
                $("#div-show-model").attr('data-tr',$btn.parents('tr').index())
            });
        })


    });
</script>