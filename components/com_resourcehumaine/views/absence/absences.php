<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-header">
                <h4 class="card-title">Liste des absences et conges</h4>
            </div>
            <div class="card-body">
                <div class="col msgbox mt-3"></div>
                <div class="table-responsive list-box">
                    <table class="table table-stripped table-center table-hover datatable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Date du début</th>
                                <th>Date de fin</th>
                                <th>Date de retour</th>
                                <th>Nombre des jours</th>
                                <th>Nature d'absence</th>
                                <th>Statut</th>
                                <th>Remarque</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absences as $absence): ?>
                                <tr>
                                    <td><?php echo $absence->getId(); ?></td>
                                    <td><?php echo normaldate($absence->getStartDate()); ?></td>
                                    <td><?php echo normaldate($absence->getEndDate()); ?></td>
                                    <td><?php echo normaldate($absence->getBackDate()); ?></td>
                                    <td><b><?php echo $absence->getNumberOfDays(); ?></b></td>
                                    <td>
                                        <?php if ($absence->getNatureOfAbsence() == 1) : ?>
                                            <span class="badge badge-secondary">Congé</span>
                                        <?php elseif ($absence->getNatureOfAbsence() == 2) : ?>
                                            <span class="badge badge-info">Justifié</span>
                                        <?php elseif ($absence->getNatureOfAbsence() == 3) : ?>
                                            <span class="badge badge-primary">Non justifié</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($absence->getNatureOfAbsence() != 1) : ?>
                                            <?php if ($absence->getStatus() == 1) : ?>
                                                <span class="badge badge-success">Déductible</span>
                                            <?php else : ?>
                                                <span class="badge badge-danger">Non déductible</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><span title="<?= strip_tags($absence->getRemark()) ?>"><?php echo substr($absence->getRemark(), 0, 30); ?> <?php echo strlen($absence->getRemark()) > 30 ? '[...]' : null ?> </span></td>
                                    <td class="text-right">
                                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                                            <a href="index.php?option=com_resourcehumaine&task=absence&id=<?= $resourcehumaine->getId(); ?>&id_absence=<?= $absence->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $absence->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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