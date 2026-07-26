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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="div-round-profile mb-3">
                                <img onerror="this.src='./images/default-image.jpeg'" src="./images/resourceshumaines/<?= $resourcehumaine->getPhoto() ?>" alt="<?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?>">
                            </div>
                            <h3 class="mb-0"><?= $resourcehumaine->getFirstName() . " " . $resourcehumaine->getLastName() ?></h3>
                            <span class="text-secondary"><?= $resourcehumaine->getFunction() ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ajouter un fichier</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12">
                                <p class="text-danger">Certains fichiers requis (contrat de travail, règlement interne, copie CIN, Offre d'emploi, Accord de confidentialité) !</p>
                            </div>
                        </div>
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

    });
</script>