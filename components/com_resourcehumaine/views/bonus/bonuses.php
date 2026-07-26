<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-header">
                <h4 class="card-title">Liste des primes</h4>
            </div>
            <div class="card-body">
                <div class="col msgbox mt-3"></div>
                <div class="table-responsive list-box">
                    <table class="table table-stripped table-center table-hover datatable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bonuses as $bonus): ?>
                                <tr>
                                    <td><?php echo $bonus->getId(); ?></td>
                                    <td><b><?php echo $bonus->getAmount(); ?> MAD</b></td>
                                    <td><?php echo date("m/Y", strtotime($bonus->getDate())); ?></td>
                                    <td>
                                        <?php if ($bonus->getStatus() == 1) : ?>
                                            <span class="badge badge-success">Pris</span>
                                        <?php else : ?>
                                            <span class="badge badge-danger">Pas pris</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                                            <a href="index.php?option=com_resourcehumaine&task=bonus&id=<?= $resourcehumaine->getId(); ?>&id_bonus=<?= $bonus->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['user']->hasDroit('delete', 'com_resourcehumaine')) : ?>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $bonus->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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