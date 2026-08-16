<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Gestion des fichiers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="index.php?option=com_resourcehumaine">Resources humaines</a></li>
                        <li class="breadcrumb-item active">Fichiers : <?php echo $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></li>
                    </ul>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['user']->hasDroit('add', 'com_resourcehumaine')) : ?>
                        <a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId(); ?>" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter un fichier">
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
                        <h4 class="card-title">Ajouter un fichier</h4>
                    </div>
                    <div class="card-body">
                        <?php include("form.php"); ?>
                    </div>

                </div>
            </div>
        </div>

        <?php include "components/com_resourcehumaine/views/fileresourcehumaine/filesresourcehumaine.php"; ?>
        
    </div>
</div>


<script src="js/jquery.sortable.js"></script>
<script type="text/javascript">
    $(function() {
        $(".deleteFile").click(function() {
            if (confirm("Voulez vous supprimer cet fichier ?")) {
                var btn = $(this);
                var t = btn.attr("id").split("_");
                var id = t[1];
                var order = "id=" + id;
                $.post("<?php echo $action3; ?>", order, function(theResponse) {
                    var success_msg = "Fichier supprimé avec succès.";
                    var error_msg = "Erreur lors de la suppression.";
                    if (parseInt(theResponse) === 1) {
                        self.parents('li').addClass('bg-danger-light')
                        setTimeout(function() {
                            self.parents('li').remove()
                            if($('ul.ul-files').find('li').length<=0){
                                console.log("count",$('ul.ul-files').find('li').length)
                                console.log("count",$('.div-file').length)
                                $('.div-file').html('<p class="text-center">Il n\'y a pas de fichier</p>')
                            }
                        }, 1000);
                    } else {
                        $(".msgbox").html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-times'></i> <strong>Erreur! </strong>" + error_msg + "</div>").slideDown();
                    }
                });
            }
        });

        $(document).on("click", ".approveFile", function() {
            var $btn = $(this);
            var id = $btn.attr("data-id");
            $.post("components/com_resourcehumaine/controleurs/router.php?task=approveFileResourceHumaine", { id: id }, function(theResponse) {
                if (parseInt(theResponse) === 1) {
                    location.reload();
                } else {
                    $(".msgbox").html("<div class='alert alert-danger alert-dismissable'><i class='fa fa-times'></i> <strong>Erreur! </strong>Échec de la validation.</div>").slideDown();
                }
            });
        });

    });
</script>