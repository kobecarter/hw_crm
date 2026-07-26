<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Liste des Bulletins de paie </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 msgbox"></div>

                    <div class="col-sm-12">
                        <div class="div-file">
                            <ul class="sortable grid ul-payslip">
                                <?php
                                foreach ($payslips as $file) {
                                ?>
                                    <li class="d-flex justify-content-between">
                                        <a href="./images/resourceshumaines/payslips/<?php echo $file->getFile(); ?>" target="_blank">
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-file-pdf icon" alt="<?= $file->getTitle() ?>" title="Cliquer pour voir le bulletin de pay"></i>
                                                <h3><?= $file->getTitle() ?></h3>
                                            </div>
                                        </a>
                                        <div>
                                            <a href="index.php?option=com_resourcehumaine&task=payslip&id=<?= $resourcehumaine->getId() ?>&id_payslip=<?= $file->getId() ?>" class="btn btn-info btn-sm editPayslip"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0)" id="delete_<?php echo $file->getId(); ?>" class="btn btn-danger btn-sm deletePayslip"><i class="fa fa-trash"></i></a>
                                        </div>

                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                            <?php
                            if (sizeof($payslips) <= 0) :
                            ?>
                                <p class="text-center">Il n'y a pas de bulletin de paie</p>
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