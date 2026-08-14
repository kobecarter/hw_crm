<?php
$documentsRequisFiche = fileresourcehumaine::documentsRequis($resourcehumaine->getStatus());
$documentsManquantsFiche = fileresourcehumaine::documentsManquants($resourcehumaine->getStatus(), $files);
$documentsPresentsFiche = fileresourcehumaine::documentTypesPresents($files);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card doc-compliance-card <?= !empty($documentsManquantsFiche) ? 'doc-compliance-incomplete' : '' ?>">
            <div class="card-header">
                <h4 class="card-title">Conformité du dossier — <?= htmlspecialchars($resourcehumaine->getStatus()) ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($documentsManquantsFiche)) : ?>
                    <div class="doc-compliance-alert">
                        <i class="fa fa-exclamation-triangle mr-2"></i>
                        Profil non finalisé — il manque <?= count($documentsManquantsFiche) ?> document(s) obligatoire(s) : <?= htmlspecialchars(implode(', ', $documentsManquantsFiche)) ?>.
                    </div>
                <?php else : ?>
                    <div class="doc-compliance-ok">
                        <i class="fa fa-check-circle mr-2"></i> Dossier complet — tous les documents requis sont fournis.
                    </div>
                <?php endif; ?>
                <div class="doc-checklist">
                    <?php foreach ($documentsRequisFiche as $cle => $libelle) : ?>
                        <div class="doc-checklist-item <?= isset($documentsPresentsFiche[$cle]) ? 'doc-checklist-ok' : 'doc-checklist-missing' ?>">
                            <i class="fa <?= isset($documentsPresentsFiche[$cle]) ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                            <span><?= htmlspecialchars($libelle) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Liste des fichiers</h4>
            </div>
            <div class="card-body">
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
                                                <h3><?= $file->getTitle() ?><?php if ($file->getDocumentType() && isset($documentsRequisFiche[$file->getDocumentType()])) : ?> <span class="badge bg-success-light doc-type-badge"><?= htmlspecialchars($documentsRequisFiche[$file->getDocumentType()]) ?></span><?php endif; ?><?php if (!$file->getValidated()) : ?> <span class="badge badge-warning text-white">En attente de validation</span><?php endif; ?></h3>
                                            </div>
                                        </a>
                                        <div>
                                            <?php if($_SESSION['user']->hasDroit('view', 'com_resourcehumaine') || $_SESSION['user']->isResourceHumaine() ) :?>
                                                <a href="./images/resourceshumaines/files/<?php echo $file->getFile(); ?>" target="_blank" class="btn btn-primary btn-sm editFile"><i class="fa fa-eye"></i></a>
                                            <?php endif;?>
                                            <?php if(!$file->getValidated() && $_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) :?>
                                                <a href="javascript:void(0)" data-id="<?php echo $file->getId(); ?>" class="btn btn-success btn-sm approveFile" title="Valider ce document déposé par l'employé"><i class="fa fa-check"></i></a>
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