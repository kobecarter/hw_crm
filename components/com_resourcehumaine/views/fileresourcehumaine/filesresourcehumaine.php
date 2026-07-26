<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Liste des fichiers</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <?php if(!$_SESSION['user']->isResourceHumaine() ) :?>
                        <div class="col-12">
                            <p class="text-danger">Certains fichiers requis (contrat de travail, règlement interne, copie CIN, Offre d'emploi, Accord de confidentialité) !</p>
                        </div>
                    <?php endif;?>
                </div>
                <div class="row">
                    <div class="col-sm-12 msgbox"></div>

                    <div class="col-sm-12">
                        <div class="div-file">
                            <ul class="sortable grid ul-files">
                                <?php
                                foreach ($files as $file) {
                                ?>
                                    <li class="d-flex justify-content-between">
                                        <a href="./images/resourceshumaines/files/<?php echo $file->getFile(); ?>" target="_blank">
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-file-pdf icon" alt="<?= $file->getTitle() ?>" title="Cliquer pour voir le fichier"></i>
                                                <h3><?= $file->getTitle() ?></h3>
                                            </div>
                                        </a>
                                        <div>
                                            <?php if($_SESSION['user']->hasDroit('view', 'com_resourcehumaine') || $_SESSION['user']->isResourceHumaine() ) :?>
                                                <a href="./images/resourceshumaines/files/<?php echo $file->getFile(); ?>" target="_blank" class="btn btn-primary btn-sm editFile"><i class="fa fa-eye"></i></a>
                                            <?php endif;?>
                                            <?php if($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) :?>
                                                <a href="index.php?option=com_resourcehumaine&task=file&id=<?= $resourcehumaine->getId() ?>&id_file=<?= $file->getId() ?>" class="btn btn-info btn-sm editFile"><i class="fa fa-edit"></i></a>
                                            <?php endif;?>
                                            <?php if($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) :?>
                                                <a href="javascript:void(0)" id="delete_<?php echo $file->getId(); ?>" class="btn btn-danger btn-sm deleteFile"><i class="fa fa-trash"></i></a>
                                            <?php endif;?>

                                        </div>

                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                            <?php
                            if (sizeof($files) <= 0) :
                            ?>
                                <p class="text-center">Il n'y a pas de fichiers</p>
                            <?php
                            endif;
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>