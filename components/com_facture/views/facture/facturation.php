<!-- Page Wrapper -->
<div class="page-wrapper glass-page">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Facturation</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Facturation</li>
                    </ul>
                </div>
            </div>
        </div>

        <?php $facturationRevenueTasks = ['devis', 'contract', 'facture', 'paiement']; ?>
        <div id="kpi-client-block" class="<?= (isset($_GET['task']) && in_array($_GET['task'], $facturationRevenueTasks)) ? 'd-none' : '' ?>">
            <?php include(__DIR__ . "/../../../com_client/views/client/list-kpi.php"); ?>
        </div>
        <div id="kpi-revenue-block" class="<?= (isset($_GET['task']) && in_array($_GET['task'], $facturationRevenueTasks)) ? '' : 'd-none' ?>">
            <?php include(__DIR__ . "/facturation-kpi.php"); ?>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div>
                        <div class="nav nav-tabs pill-tabs" id="nav-tab" role="tablist">
                            <a class="nav-link <?= (isset($_GET['task']) && $_GET['task'] == "client") || !isset($_GET['task']) ? 'active' : null ?>" id="nav-clients-tab" href="index.php?option=com_facture&task=client" data-toggle="tab" data-target="#nav-clients" data-kpi="kpi-client-block" role="tab" aria-controls="nav-clients" aria-selected="true">Clients <span class="badge badge-pill ml-1"><?php echo sizeof($clients); ?></span></a>
                            <a class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "devis" ? 'active' : null ?>" id="nav-devis-tab" href="index.php?option=com_facture&task=devis" data-toggle="tab" data-target="#nav-devis" data-kpi="kpi-revenue-block" role="tab" aria-controls="nav-devis" aria-selected="false">Devis <span class="badge badge-pill ml-1"><?php echo sizeof($deviss); ?></span></a>
                            <a class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "contract" ? 'active' : null ?>" id="nav-contrats-tab" href="index.php?option=com_facture&task=contract" data-toggle="tab" data-target="#nav-contrats" data-kpi="kpi-revenue-block" role="tab" aria-controls="nav-contrats" aria-selected="false">Contrats <span class="badge badge-pill ml-1"><?php echo sizeof($contracts); ?></span></a>
                            <a class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "facture" ? 'active' : null ?>" id="nav-factures-tab" href="index.php?option=com_facture&task=facture" data-toggle="tab" data-target="#nav-factures" data-kpi="kpi-revenue-block" role="tab" aria-controls="nav-factures" aria-selected="false">Factures <span class="badge badge-pill ml-1"><?php echo sizeof($factures); ?></span></a>
                            <a class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "paiement" ? 'active' : null ?>" id="nav-paiements-tab" href="index.php?option=com_facture&task=paiement" data-toggle="tab" data-target="#nav-paiements" data-kpi="kpi-revenue-block" role="tab" aria-controls="nav-paiements" aria-selected="false">Paiements <span class="badge badge-pill ml-1"><?php echo sizeof($payments); ?></span></a>
                        </div>
                    </div>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade <?= (isset($_GET['task']) && $_GET['task'] == "client") || !isset($_GET['task']) ? 'show active' : null ?>" id="nav-clients" role="tabpanel" aria-labelledby="nav-clients-tab">
                            <?php include(__DIR__ . "/../../../com_client/views/client/list-content.php"); ?>
                        </div>
                        <div class="tab-pane fade <?= isset($_GET['task']) && $_GET['task'] == "devis" ? 'show active' : null ?>" id="nav-devis" role="tabpanel" aria-labelledby="nav-devis-tab">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">Liste des devis</h4>
                                <div>
                                    <?php if ($_SESSION['user']->hasDroit('add', 'com_devis')) : ?>
                                        <button type="button" class="btn btn-outline-primary mr-1" data-toggle="modal" data-target="#dialog-ia-devis-chat" data-original-title="Assistant IA">
                                            <i class="fa fa-magic"></i> Assistant IA
                                        </button>
                                        <a href="index.php?option=com_devis&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter devis">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col msgbox mt-3"></div>
                                <div class="table-responsive">
                                    <table class="table table-stripped table-center table-hover datatable" data-order="[]">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Numéro</th>
                                                <th>Client</th>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Contrat</th>
                                                <th>Facture</th>
                                                <th>Status</th>
                                                <th>Envoyer Mail</th>
                                                <th></th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($deviss as $devis): ?>
                                                <?php
                                                switch($devis->getStatu()){
                                                    case '1' : $statu = '<span class="badge bg-success-light">Envoyé</span>'; break;
                                                    case '2' : $statu = '<span class="badge bg-success-light">Accepté</span>'; break;
                                                    case '3' : $statu = '<span class="badge bg-primary-light">Contrat en attente de signature</span>'; break;
                                                    case '4' : $statu = '<span class="badge bg-success text-white">Paiement effectué</span>'; break;
                                                    case '5' : $statu = '<span class="badge bg-danger text-white">Devis Refusé</span>'; break;
                                                    default : $statu = '<span class="badge bg-warning-light">Brouillon</span>'; break;	
                                                }
                                                $activity = "Ajouté par " . $devis->getUserAdded()->getNom() . " | Modifié par " . $devis->getUserEdited()->getNom();
                                                ?>
                                                <tr>
                                                    <td><?php echo $devis->getId(); ?></td>
                                                    <td><a href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>">#<?php echo $devis->getNumero(); ?></a></td>
                                                    <td>
                                                        <?php $photoLink = $devis->getClient()->getPhoto() != '' ? "images/clients/" . $devis->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                                        <h2 class="table-avatar">
                                                            <a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $devis->getClient()->getRaisonSocial(); ?></a>
                                                        </h2>
                                                    </td>
                                                    <td data-sort="<?= strtotime($devis->getDateDevis()) ?>"><?php echo normaldate($devis->getDateDevis()); ?></td>
                                                    <td class="money-sensitive"><?php echo number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise(); ?></td>
                                                    <td>
                                                        <?php
                                                        $contract = contract::findByDevis($devis->getId(), $_SESSION['agence'], $devis->getLangue());
                                                        if ($contract->getId() != 0): ?>
                                                            <a href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?php echo $contract->getId(); ?>" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="Contract" target="_blank"><i class="fa fa-file"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($devis->getFacture()->getId() != 0): ?>
                                                            <a href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $devis->getFacture()->getId(); ?>" class="btn btn-sm btn-white text-info" data-toggle="tooltip" data-placement="top" data-original-title="Facture" target="_blank"><i class="fa fa-file-alt"></i></a>
                                                        <?php elseif ($devis->getFacture()->getId() == 0 && in_array($devis->getStatu(), [4])): ?>
                                                            <a class="dropdown-item text-success create-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-file-invoice mr-2"></i>+</a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $statu; ?></td>
                                                    <td><a onclick="return confirm('Êtes-vous sûr de vouloir envoyer ce fichier?')" href="components/com_devis/controleurs/router.php?task=sendViaMailDevis&id=<?php echo $devis->getId(); ?>" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi de devis via Mail" data-id="<?= $devis->getId(); ?>" target="_blank"><i class="far fa-paper-plane"></i></td>
                                                    <td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
                                                    <td class="text-right">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_devis')) : ?>
                                                                    <a class="dropdown-item text-warning" href="index.php?option=com_devis&task=edit&id=<?php echo $devis->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) : ?>
                                                                    <a class="dropdown-item text-info" href="index.php?option=com_devis&task=show&id=<?php echo $devis->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_devis')) : ?>
                                                                    <a class="dropdown-item text-danger deleteDevis" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) : ?>
                                                                    <!-- <a class="dropdown-item text-danger" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $devis->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a> -->
                                                                    <a class="dropdown-item text-danger" href="components/com_devis/controleurs/router.php?task=pdfDevisTexte&id=<?php echo $devis->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('add', 'com_facture') || $_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
                                                                    <?php
                                                                    $facture = facture::findByDevis($devis->getId(), $_SESSION['agence']);
                                                                    if (in_array($devis->getStatu(), [1, 3]) && !$facture):
                                                                    ?>
                                                                        <a class="dropdown-item text-success create-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-file-invoice mr-2"></i>+ Facture</a>
                                                                    <?php endif; ?>
                                                                    <a class="dropdown-item text-info duplicate-invoice" href="javascript:void(0);" data-id="<?php echo $devis->getId(); ?>"><i class="fa fa-copy mr-2"></i>Dupliquer</a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade <?= isset($_GET['task']) && $_GET['task'] == "contract" ? 'show active' : null ?>" id="nav-contrats" role="tabpanel" aria-labelledby="nav-contrats-tab">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">Liste des contrats</h4>
                                <div>
                                    <?php if ($_SESSION['user']->hasDroit('add', 'com_contract')) : ?>
                                        <a href="index.php?option=com_contract&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter contrat">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-sm-12 mt-3 msgbox"></div>
                                <div class="table-responsive">
                                    <table id="contracts-table" class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Devis</th>
                                                <th>Client</th>
                                                <th>Titre</th>
                                                <th>Durée / Garantie</th>
                                                <th>Ville</th>
                                                <th>Paiements</th>
                                                <th>Date</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($contracts as $contract): ?>
                                                <tr>
                                                    <td><?php echo $contract->getDevis()->getNumero(); ?></td>
                                                    <td>
                                                        <?php $photoLink = $contract->getDevis()->getClient()->getPhoto() != '' ? "images/clients/" . $contract->getDevis()->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                                        <h2 class="table-avatar">
                                                            <a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $contract->getDevis()->getClient()->getRaisonSocial(); ?></a>
                                                        </h2>
                                                    </td>
                                                    <td><?php echo $contract->getTitre(); ?></td>
                                                    <td><?php echo $contract->getDuration(); ?> / <?php echo $contract->getGarantie(); ?></td>
                                                    <td><?php echo $contract->getVille(); ?></td>
                                                    <td><b><?php echo $contract->getNombreDePaiement(); ?></b></td>
                                                    <td data-sort="<?= strtotime($contract->getDate()) ?>"><?php echo normaldate($contract->getDate()); ?></td>
                                                    <td class="text-right">
                                                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_contract')) : ?>
                                                            <a href="index.php?option=com_contract&task=edit&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('view', 'com_contract')) : ?>
                                                            <a href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-primary mr-2" data-toggle="tooltip" data-placement="top" data-original-title="PDF"><i class="fa fa-file"></i></a>
                                                            <a href="components/com_contract/controleurs/router.php?task=docxContract&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-info mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Télécharger en Word"><i class="fa fa-file-word"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('delete', 'com_contract')) : ?>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 deleteContract" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $contract->getId(); ?>"><i class="far fa-trash-alt"></i></a>
                                                        <?php endif ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade <?= isset($_GET['task']) && $_GET['task'] == "facture" ? 'show active' : null ?>" id="nav-factures" role="tabpanel" aria-labelledby="nav-factures-tab">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">Liste des factures</h4>
                                <div>
                                    <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
                                        <a href="index.php?option=com_facture&task=archive" class="btn btn-warning text-white mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Archive">
                                            <i class="fas fa-archive"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-danger mr-1 quick-filter-unpaid" data-toggle="tooltip" data-placement="top" data-original-title="Factures impayées">
                                            <i class="fa fa-file-invoice"></i>
                                        </a>
                                    <?php endif ?>
                                    <?php if ($_SESSION['user']->hasDroit('add', 'com_facture')) : ?>
                                        <!--<a href="index.php?option=com_facture&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter facture">
                                    <i class="fas fa-plus"></i>
                                </a>-->
                                    <?php endif; ?>
                                    <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
                                        <a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search_facture" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
                                            <i class="fas fa-filter"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body facture-filter">
                                <div class="col msgbox mt-3"></div>
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Search Filter -->
                                        <div id="filter_inputs_facture" class="card filter-card px-4">
                                            <div class="card-body pb-0">
                                                <form method="post" action="components/com_facture/controleurs/router.php?task=filterFacture" id="filterFacture">
                                                    <div class="row">
                                                        <div class="col-sm-6 col-md-2">
                                                            <div class="form-group">
                                                                <label>Date début</label>
                                                                <div class="cal-icon">
                                                                    <input type="text" class="form-control datetimepicker" required name="from" id="from" value="<?php if (isset($_GET['from'])) echo normaldate($_GET['from']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-2">
                                                            <div class="form-group">
                                                                <label>Date fin</label>
                                                                <div class="cal-icon">
                                                                    <input type="text" class="form-control datetimepicker" required name="to" id="to" value="<?php if (isset($_GET['to'])) echo normaldate($_GET['to']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Devise</label>
                                                                <select class="select" name="devise">
                                                                    <option value="">Toutes devises</option>
                                                                    <option value="DH">MAD (DH)</option>
                                                                    <option value="€">Euro (€)</option>
                                                                    <option value="$">Dollar ($)</option>
                                                                    <option value="£">Pound (£)</option>
                                                                    <option value="AED">AED (DH)</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Statut</label>
                                                                <select class="select" name="status">
                                                                    <option value="">Touts les statuts</option>
                                                                    <option value="litige">Litige</option>
                                                                    <option value="unpaid">Impayée</option>
                                                                    <option value="paid">Payée</option>
                                                                    <option value="partial">Payée partialement</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-4">
                                                            <button type="submit" name="" class="btn btn-primary submit" style="margin-top: 32px;"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>
                                                            <a href="javascript:void(0)" class="btn btn-primary exportFacture" style="margin-top: 32px;"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
                                                            <a href="javascript:void(0)" class="btn btn-primary exportFacturePDFS" style="margin-top: 32px;"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter PDF(s)</a>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- /Search Filter -->
                                    </div>
                                </div>
                                <div class="table-responsive list-box">
                                    <table class="table table-stripped table-center table-hover datatable datatable-invoice" data-order="[]">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Numéro</th>
                                                <th>Client</th>
                                                <th></th>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Reste</th>
                                                <th>Statut</th>
                                                <th>Send Mail</th>
                                                <th></th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($factures as $facture) : ?>
                                                <?php
                                                $payments_by_invoice = payment::findAll($facture->getId());
                                                $nbrPayment = sizeof($payments_by_invoice);

                                                if($facture->getStatu() == 3){
        										    $statu = '<span class="badge bg-primary-light">Litige ('.$nbrPayment.')</span>';
        										}else {
        										    if($facture->getTotal() == $facture->getReste()){
            											$statu = '<span class="badge bg-danger-light">Impayée ('.$nbrPayment.')</span>';
            										}elseif($facture->getTotal() > $facture->getReste() && $facture->getReste() > 0){	
            											$statu = '<span class="badge bg-warning-light">Payée partialement ('.$nbrPayment.')</span>';
            										}elseif($facture->getReste() <= 0){
            											$statu = '<span class="badge bg-success-light">Payée ('.$nbrPayment.')</span>';
            										}
        										}

                                                $nom = $facture->getClient()->getRaisonSocial() != '' ? $facture->getClient()->getRaisonSocial() : $facture->getClient()->getNom() . ' ' . $facture->getClient()->getPrenom();
                                                $activity = "Ajouté par " . $facture->getUserAdded()->getNom() . " | Modifié par " . $facture->getUserEdited()->getNom();
                                                ?>
                                                <tr>
                                                    <td><?php echo $facture->getId(); ?></td>
                                                    <td><a href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>">#<?php echo $facture->getNumero(); ?></a></td>
                                                    <td>
                                                        <?php $photoLink = $facture->getClient()->getPhoto() != '' ? "images/clients/" . $facture->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                                        <h2 class="table-avatar">
                                                            <a href="index.php?option=com_client&task=showDetails&id=<?php echo $facture->getClient()->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $nom; ?></a>
                                                        </h2>
                                                    </td>
                                                    <td><?php echo $facture->isAvoir() ? '<span class="badge bg-info-light">avoir</span>' : ''; ?></td>
                                                    <td data-sort="<?= strtotime($facture->getDateFacture()) ?>"><?php echo normaldate($facture->getDateFacture()); ?></td>
                                                    <td class="money-sensitive"><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                                    <td class="money-sensitive"><?php echo number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                                    <td><?php echo $statu; ?></td>
                                                    <td><a onclick="return confirm('Êtes-vous sûr de vouloir envoyer ce fichier?')" href="components/com_facture/controleurs/router.php?task=sendViaMailFacture&id=<?php echo $facture->getId(); ?>" class="btn btn-sm btn-white text-info mr-2 sendMail" data-toggle="tooltip" data-placement="top" data-original-title="Envoi de facture via Mail" data-id="<?= $facture->getId(); ?>" target="_blank"><i class="far fa-paper-plane"></i></td>
                                                    <td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
                                                    <td class="text-right">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <?php if ($_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
                                                                    <a class="dropdown-item text-warning" href="index.php?option=com_facture&task=edit&id=<?php echo $facture->getId(); ?>"><i class="far fa-edit mr-2"></i>Modifier</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('add', 'com_facture') || $_SESSION['user']->hasDroit('edit', 'com_facture')) : ?>
                                                                    <a class="dropdown-item text-secondary" href="index.php?option=com_facture&task=avoir&id=<?php echo $facture->getId(); ?>"><i class="fa fa-file-invoice-dollar mr-3"></i>Avoir</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
                                                                    <a class="dropdown-item text-info" href="index.php?option=com_facture&task=show&id=<?php echo $facture->getId(); ?>"><i class="far fa-eye mr-2"></i>Afficher</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('delete', 'com_facture')) : ?>
                                                                    <a class="dropdown-item text-danger deleteFacture" href="javascript:void(0);" data-id="<?php echo $facture->getId(); ?>"><i class="far fa-trash-alt mr-2"></i>Supprimer</a>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
                                                                    <a class="dropdown-item text-success" href="index.php?option=com_facture&task=payment&id=<?php echo $facture->getId(); ?>"><i class="far fa-money-bill-alt mr-2"></i>Reglement</a>
                                                                    <?php if($facture->getReste() <= 0): ?>
                                                                        <a class="dropdown-item text-danger"  href="components/com_facture/controleurs/router.php?task=pdfFacture&id=<?php echo $facture->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>PDF</a>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                                <?php if ($_SESSION['user']->hasDroit('view', 'com_devis')) : ?>
                                                                    <?php if ($facture->getDevis()->getId() != 0): ?>
                                                                        <a class="dropdown-item text-info" href="components/com_devis/controleurs/router.php?task=pdfDevis&id=<?php echo $facture->getDevis()->getId(); ?>" target="_blank"><i class="far fa-file-pdf mr-2"></i>Devis</a>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane fade <?= isset($_GET['task']) && $_GET['task'] == "paiement" ? 'show active' : null ?>" id="nav-paiements" role="tabpanel" aria-labelledby="nav-paiements-tab">
                            <div class="card-header">
                                <h4 class="card-title">Liste des paiements</h4>
                            </div>
                            <div class="card-body">
                                <div class="col msgbox mt-3"></div>
                                <div class="table-responsive">
                                    <table class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Facture</th>
                                                <th>Client</th>
                                                <th>Montant</th>
                                                <th>Méthode</th>
                                                <th>Date réception</th>
                                                <th>Date validation</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($payments as $key => $payment) : ?>
                                                <tr>
                                                    <td><?php echo $payment->getId(); ?></td>
                                                    <td><?php echo $payment->getFacture()->getNumero(); ?></td>
                                                    <td><?php
                                                        $nom = $payment->getFacture()->getClient()->getRaisonSocial() != '' ? $payment->getFacture()->getClient()->getRaisonSocial() : $payment->getFacture()->getClient()->getNom() . ' ' . $payment->getFacture()->getClient()->getPrenom();
                                                        echo $nom;
                                                        ?></td>
                                                    <td class="money-sensitive"><?php echo number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise(); ?></td>
                                                    <td><?php echo $payment->getMethodePayment(); ?></td>
                                                    <td data-sort="<?= strtotime($payment->getDatePayment()) ?>"><?php echo normaldate($payment->getDatePayment()); ?></td>
                                                    <td data-sort="<?= strtotime($payment->getDateValidation()) ?>"><?php echo normaldate($payment->getDateValidation()); ?></td>
                                                    <td class="text-right">
                                                        <?php if ($payment->getRegImg() != ''): ?>
                                                            <a href="images/reglements/<?php echo $payment->getRegImg(); ?>" data-fancybox class="btn btn-sm btn-white text-success mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Reglement"><i class="fa fa-file-alt"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
                                                            <a class="btn btn-sm btn-white text-success mr-2" href="components/com_facture/controleurs/router.php?task=pdfPayment&id=<?php echo $payment->getId(); ?>&index=<?php echo $key + 1; ?>" target="_blank"><i class="far fa-file-pdf"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('deletePayment', 'com_facture')) : ?>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 delete" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $payment->getId(); ?>"><i class="far fa-trash-alt"></i></a>
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
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Assistant IA - Nouveau devis (même modale/JS que components/com_devis/views/devis/list.php,
     "la page référence" : accessible ici aussi depuis l'onglet Devis de la page Facturation, pour
     qu'on la retrouve peu importe par où on arrive sur les devis). -->
<div id="dialog-ia-devis-chat" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-magic mr-1"></i> Assistant IA — Nouveau devis</h5>
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<div class="modal-body">

				<div id="iaDevisChatEtape1">
					<p class="text-muted">Décrivez le projet ou la prestation demandée par le client (son nom, ce qu'il souhaite, un budget si connu...). L'assistant identifie le client et propose les prestations correspondantes.</p>
					<div class="form-group">
						<textarea class="form-control" id="iaDevisChatMessage" rows="5" placeholder="Ex : Le client ABC Corp souhaite un site e-commerce avec un pack SEO, budget autour de 25000 MAD, à livrer avant fin du mois."></textarea>
					</div>
					<div id="iaDevisChatErreur" class="alert alert-danger" style="display:none;"></div>
					<button type="button" class="btn btn-primary" id="iaDevisChatAnalyser">
						<span class="spinner-border spinner-border-sm mr-1" id="iaDevisChatLoading" style="display:none;"></span>
						Analyser la demande
					</button>
				</div>

				<div id="iaDevisChatEtape2" style="display:none;">
					<h6 class="form-section-title"><i class="fa fa-user-tie mr-1"></i> Étape 1 — Confirmez le client</h6>
					<div id="iaDevisChatClientMatches"></div>

					<div class="form-group mt-3">
						<label class="small text-muted mb-1">Ce n'est pas le bon client ? Cherchez-en un autre :</label>
						<input type="text" class="form-control" id="iaDevisChatClientRecherche" placeholder="Nom du client...">
						<div id="iaDevisChatClientRechercheResultats" class="mt-2"></div>
					</div>

					<div class="mt-3">
						<button type="button" class="btn btn-outline-secondary btn-sm" id="iaDevisChatNouveauClient"><i class="fa fa-plus mr-1"></i> Aucun client trouvé — en créer un nouveau</button>
						<button type="button" class="btn btn-link btn-sm" id="iaDevisChatRetour">&larr; Revenir à la description</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<!-- Modale "Ajouter client" (même partiel que la page Ajouter/Modifier devis, ia-client-modal.js
     l'utilise indifféremment) : permet de créer le client identifié par l'assistant SANS quitter
     ce flux - la fiche s'ouvre pré-remplie avec ce que l'IA a détecté, et une fois créée, reprend
     exactement le même chemin que si un client existant avait été confirmé. -->
<div id="dialog-client" class="modal client-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-scrollable" role="document" style="max-width: 800px;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Ajouter client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>
<!-- /Add Client Modal -->

<script type="text/javascript">
$(function () {

	function escHtmlIaDevisChat(s) {
		return $('<div>').text(s === undefined || s === null ? '' : s).html();
	}

	function rendreClientMatchesIaDevisChat(matches) {
		var $zone = $('#iaDevisChatClientMatches').empty();
		if (!matches || !matches.length) {
			$zone.html('<div class="alert alert-warning mb-0">Aucun client existant ne correspond au nom mentionné. Cherchez-en un manuellement ci-dessous, ou créez un nouveau client.</div>');
			return;
		}
		matches.forEach(function (m) {
			var sousTitre = [m.email, m.tel].filter(function (v) { return v; }).join(' · ');
			var $card = $(
				'<div class="card mb-2"><div class="card-body d-flex justify-content-between align-items-center py-2 flex-wrap">' +
				'<div class="mr-2"><strong>' + escHtmlIaDevisChat(m.nom) + '</strong>' +
				(m.agence_nom ? ' <span class="badge badge-light">' + escHtmlIaDevisChat(m.agence_nom) + '</span>' : '') +
				'<br><span class="text-muted small">' + escHtmlIaDevisChat(sousTitre) + '</span></div>' +
				'<button type="button" class="btn btn-sm btn-success confirmer-client-ia-devis-chat">Confirmer ce client</button>' +
				'</div></div>'
			);
			$card.find('.confirmer-client-ia-devis-chat').on('click', function () {
				confirmerClientEtOuvrirDevis(m.id, m.agence_id);
			});
			$zone.append($card);
		});
	}

	function confirmerClientEtOuvrirDevis(idClient, agenceClient) {
		var extracted = window.iaDevisChatExtracted || {};
		sessionStorage.setItem('ia_services', JSON.stringify(extracted.services || []));
		sessionStorage.setItem('ia_conditions', JSON.stringify(extracted.conditions || []));
		sessionStorage.setItem('ia_client_pays', (extracted.client && extracted.client.pays) || '');
		ensureSessionAgence(agenceClient, function () {
			document.location = 'index.php?option=com_devis&task=add&id_client=' + idClient;
		});
	}

	$('#iaDevisChatAnalyser').on('click', function () {
		var message = $('#iaDevisChatMessage').val().trim();
		if (!message) {
			return;
		}
		var $btn = $(this);
		$('#iaDevisChatErreur').hide();
		$('#iaDevisChatLoading').show();
		$btn.prop('disabled', true);
		$.post('components/com_ia/controleurs/router.php?task=extractChatDevis', { message: message }, function (response) {
			$('#iaDevisChatLoading').hide();
			$btn.prop('disabled', false);
			if (!response.success) {
				$('#iaDevisChatErreur').text(response.message || "Erreur lors de l'analyse.").show();
				return;
			}
			window.iaDevisChatExtracted = response.extracted;
			rendreClientMatchesIaDevisChat(response.client_matches || []);
			$('#iaDevisChatEtape1').hide();
			$('#iaDevisChatEtape2').show();
		}, 'json').fail(function () {
			$('#iaDevisChatLoading').hide();
			$btn.prop('disabled', false);
			$('#iaDevisChatErreur').text('Erreur réseau, merci de réessayer.').show();
		});
	});

	$('#iaDevisChatRetour').on('click', function () {
		$('#iaDevisChatEtape2').hide();
		$('#iaDevisChatEtape1').show();
	});

	// "Aucun client trouvé" : création SANS quitter ce flux - même modale/JS que celle utilisée
	// pour "+ client" sur la page Ajouter devis (ia-client-modal.js), pré-remplie avec ce que
	// l'IA a détecté (mêmes clés prenom/nom/raison_social/fonction/tel/email/adresse/ville que
	// components/com_client/views/client/form.php attend). Une fois le client créé, on reprend
	// exactement le même chemin que la confirmation d'un client existant.
	$('#iaDevisChatNouveauClient').on('click', function () {
		var extracted = window.iaDevisChatExtracted || {};
		openIaClientModal({
			modalSelector: '#dialog-client',
			prefillClient: extracted.client || {},
			onCreated: function (newClientId, newClientAgence) {
				$('#dialog-client').modal('hide');
				confirmerClientEtOuvrirDevis(newClientId, newClientAgence);
			}
		});
	});

	var timerRechercheClientIaDevisChat = null;
	$('#iaDevisChatClientRecherche').on('input', function () {
		var terme = $(this).val().trim();
		clearTimeout(timerRechercheClientIaDevisChat);
		if (terme.length < 2) {
			$('#iaDevisChatClientRechercheResultats').empty();
			return;
		}
		timerRechercheClientIaDevisChat = setTimeout(function () {
			$.get('components/com_ia/controleurs/router.php', { task: 'rechercheClientChatDevis', q: terme }, function (resp) {
				rendreClientMatchesIaDevisChat((resp && resp.matches) || []);
			}, 'json');
		}, 350);
	});

	$('#dialog-ia-devis-chat').on('hidden.bs.modal', function () {
		$('#iaDevisChatEtape2').hide();
		$('#iaDevisChatEtape1').show();
		$('#iaDevisChatMessage').val('');
		$('#iaDevisChatErreur').hide();
		$('#iaDevisChatClientRecherche').val('');
		$('#iaDevisChatClientRechercheResultats').empty();
	});
});
</script>

<script type="text/javascript">
    $(function() {
        /*
            ******************************************************************
            For invoice
            ******************************************************************
        */

        // Bascule des onglets Clients/Devis/Contrats/Factures/Paiements : switch
        // 100% client (bootstrap tab.js, tout le contenu est déjà rendu dans le
        // DOM), donc les KPI affichés au-dessus doivent être basculés en même
        // temps sans recharger la page — on affiche juste le bloc KPI qui
        // correspond à l'onglet cliqué (voir attribut data-kpi) et on met à jour
        // l'URL (historique only, pas de navigation) pour qu'un rechargement
        // manuel retombe sur le bon onglet.
        $('#nav-tab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var kpiId = $(e.target).data('kpi');
            $('#kpi-client-block, #kpi-revenue-block').addClass('d-none');
            $('#' + kpiId).removeClass('d-none');
            if (window.history && window.history.pushState) {
                window.history.pushState(null, '', $(e.target).attr('href'));
            }
        });

        // Bouton "Filtrer" de l'onglet Factures : id dédié (filter_inputs_facture)
        // pour ne pas entrer en conflit avec le bouton/panneau générique
        // #filter_search / #filter_inputs de l'onglet Clients (même page, même
        // toggle géré ailleurs par script.js), qui restait sinon toujours ouvert
        // faute de gestionnaire propre.
        $(document).on("click", "#filter_search_facture", function() {
            $('#filter_inputs_facture').slideToggle("slow");
        });

        // Export résultat filtre
        $(document).on("click", ".exportFacture", function() {
            event.preventDefault();
            window.open('components/com_facture/controleurs/router.php?task=exportFactureTest&from=' + $("#from").val() + '&to=' + $("#to").val()+ '&devise=' + $("select[name='devise']").val()+ '&status=' + $("select[name='status']").val(), '_blank');
        })

        // Export résultat filtre
        $(document).on("click", ".exportFacturePDFS", function() {
            event.preventDefault();
            if($("#from").val() == '' || $("#to").val() == '') {
                $(".facture-filter").find(".msgbox").html('<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Attention !</strong> Sélectionnez date de debut et de fin<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>')
            }else {
                window.open('components/com_facture/controleurs/router.php?task=pdfFactures&from=' + $("#from").val() + '&to=' + $("#to").val()+ '&devise=' + $("select[name='devise']").val()+ '&status=' + $("select[name='status']").val(), '_blank');
            }
        })

        // envoi du filtre en ajax
        $('form#filterFacture').ajaxForm({
            beforeSubmit: function() {
                $("#filterFacture .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                $("#filterFacture .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                // ciblé sur l'onglet Factures : .list-box existe aussi dans
                // l'onglet Clients (même page), un sélecteur global écraserait
                // le mauvais tableau.
                $("#nav-factures .list-box").html(theResponse)
            }
        });

        // Bouton "Factures impayées" : filtre directement le tableau de l'onglet
        // Factures (statut = unpaid, sans restriction de dates) au lieu de
        // charger la page dédiée index.php?option=com_facture&task=unpaid.
        $(document).on("click", ".quick-filter-unpaid", function(e) {
            e.preventDefault();
            $("#from").val("01/01/2000");
            $("#to").val("<?php echo date('d/m/Y'); ?>");
            $("select[name='devise']").val('');
            $("select[name='status']").val('unpaid');
            $("#filterFacture").ajaxSubmit({
                beforeSubmit: function() {
                    $("#filterFacture .loading").css('display', 'inline-block');
                },
                success: function(theResponse) {
                    $("#filterFacture .loading").fadeOut();
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                    $("#nav-factures .list-box").html(theResponse);
                }
            });
        });

        var msgsucces = "Facture supprimée avec succès";

        $(document).on("click", ".deleteFacture", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_facture/controleurs/router.php?task=deleteFacture", order, function(theResponse) {
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        $(".enableFacture").click(function() {
            var btn = $(this);
            var id = btn.attr("data-id");
            var state = btn.attr("data-state");
            var order = 'id=' + id + "&state=" + state;
            $.post("components/com_facture/controleurs/router.php?task=enableClient", order, function(theResponse) {
                var error_msg = "Erreur lors de l'activation.";
                if (state === "oui") {
                    error_msg = "Erreur lors de la désactivation.";
                }
                if (parseInt(theResponse) === 1) {
                    if (state === "oui") {
                        btn.attr("data-state", "non").removeClass("text-success").addClass("text-danger").attr("data-original-title", "Inactif").html("<i class='fa fa-toggle-off'>");
                    } else {
                        btn.attr("data-state", "oui").removeClass("text-danger").addClass("text-success").attr("data-original-title", "Actif").html("<i class='fa fa-toggle-on'>");
                    }
                } else {
                    $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> ' + error_msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                }
            });
        });

        /* Pour les clients (recherche, filtres, export, activer/désactiver,
           supprimer) : géré par le script inclus dans
           components/com_client/views/client/list-content.php, partagé avec
           la page index.php?option=com_client. */

        /*
            ******************************************************************
            For devis
            ******************************************************************
        */

        $(document).on("click", ".deleteDevis", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_devis/controleurs/router.php?task=deleteDevis", order, function(theResponse) {
                    console.log(theResponse)
                    let msgsucces = "Devis supprimé avec succès"
                    if (parseInt(theResponse) == 1) {

                        $btn.parent().parent().parent().parent().addClass("table-danger");
                        setTimeout(function() {
                            $btn.parent().parent().parent().parent().remove()
                        }, 1000);

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        $(document).on("click", ".create-invoice", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_devis/controleurs/router.php?task=createInvoice", order, function(theResponse) {
                    console.log(theResponse)
                    let msgsucces = "Facture créée avec succès";
                    if (parseInt(theResponse) == 1) {
                        
                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                        setTimeout(function() {
                            location.href = "index.php?option=com_facture&task=facture"
                        }, 1000)
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        $(document).on("click", ".duplicate-invoice", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_devis/controleurs/router.php?task=duplicateDevis", order, function(theResponse) {
                    let msgsucces = "Devis dupliqué avec succès";
                    if (parseInt(theResponse) == 1) {

                        $('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                        $(document).reload();
                    } else {
                        $('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de l\'opération<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
                    }
                });
            }
        })

        /*
            ******************************************************************
            For contract
            ******************************************************************
        */

        $(document).on("click", ".deleteContract", function() {
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                var msgsucces = "Contrat supprimé avec succès";
                $.post("components/com_contract/controleurs/router.php?task=deleteContract", order, function(theResponse) {
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

        /*
            ******************************************************************
            For payments
            ******************************************************************
        */

        $(document).on("click", ".deletePayment", function() {
			var $btn = $(this);
			if (confirm("Etes-vous sure !")) {
				var id = $(this).attr("data-id");
				var order = 'id=' + id;
                var msgsucces = "Paiement supprimé avec succès";
				$.post("components/com_facture/controleurs/router.php?task=deletePayment", order, function(theResponse) {
					if (parseInt(theResponse) == 1) {

						$btn.parent().parent().parent().parent().addClass("table-danger");
						setTimeout(function() {
							$btn.parent().parent().parent().parent().remove()
						}, 1000);

						$('.msgbox').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> ' + msgsucces + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					} else {
						$('.msgbox').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> Erreur lors de la suppression<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
					}
				});
			}
		})

    });
</script>