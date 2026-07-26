<!-- Page Wrapper -->
<div class="page-wrapper">
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

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link <?= (isset($_GET['task']) && $_GET['task'] == "client") || !isset($_GET['task']) ? 'active' : null ?>" id="nav-clients-tab" data-toggle="tab" data-target="#nav-clients" type="button" role="tab" aria-controls="nav-clients" aria-selected="true">Clients</button>
                            <button class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "devis" ? 'active' : null ?>" id="nav-devis-tab" data-toggle="tab" data-target="#nav-devis" type="button" role="tab" aria-controls="nav-devis" aria-selected="false">Devis</button>
                            <button class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "contract" ? 'active' : null ?>" id="nav-contrats-tab" data-toggle="tab" data-target="#nav-contrats" type="button" role="tab" aria-controls="nav-contrats" aria-selected="false">Contrats</button>
                            <button class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "facture" ? 'active' : null ?>" id="nav-factures-tab" data-toggle="tab" data-target="#nav-factures" type="button" role="tab" aria-controls="nav-factures" aria-selected="false">Factures</button>
                            <button class="nav-link <?= isset($_GET['task']) && $_GET['task'] == "paiement" ? 'active' : null ?>" id="nav-paiements-tab" data-toggle="tab" data-target="#nav-paiements" type="button" role="tab" aria-controls="nav-paiements" aria-selected="false">Paiements</button>
                        </div>
                    </div>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade <?= (isset($_GET['task']) && $_GET['task'] == "client") || !isset($_GET['task']) ? 'show active' : null ?>" id="nav-clients" role="tabpanel" aria-labelledby="nav-clients-tab">
                            <div class="card-header d-flex">
                                <h4 class="card-title">Liste des clients</h4>
                                <div class="w-fit ml-auto">
                				    <?php if ($_SESSION['user']->hasDroit('add', 'com_client')) :?>
                    					<a href="index.php?option=com_client&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter client">
                    						<i class="fas fa-plus"></i>
                    					</a>
                					<?php endif;?>
                					<?php if ($_SESSION['user']->hasDroit('view', 'com_client')) :?>
                    					<a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
                    						<i class="fas fa-filter"></i>
                    					</a>
                    					<!-- me add -->
                    					<a href="#" class="btn btn-primary exportEmail" ><span class="fa fa-download spinner-border-sm" data-original-title="ExportEmails"></span> </a>
                    					<!-- me add -->
                					<?php endif;?>
                				</div>
                            </div>
                            <div class="card-body">
                                <div class="col msgbox mt-3"></div>
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Search Filter -->
                                        <div id="filter_inputs_client" class="card filter-card px-4">
                                            <div class="card-body pb-0">
                                                <form method="post" action="components/com_client/controleurs/router.php?task=filterClient" id="filterClient">
                                                    <div class="row">
                                                        <div class="col-sm-6 col-md-3">
                                                            <div class="form-group">
                                                                <label>Année</label>
                                                                <input type="text" class="form-control" required name="year" id="year" value="<?php if (isset($_GET['year'])) echo $_GET['year']; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="col-sm-8 col-md-8 mb-4 mb-sm-0 mt-0 mt-sm-4 pt-0 pt-sm-2">
                                                            <button type="submit" name="" class="btn btn-primary submit"><span class="spinner-border spinner-border-sm mr-2 loading"></span> Filtrer</button>

                                                            <a href="#0" class="btn btn-primary exportClient"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter</a>
                                                            <a href="#0" class="btn btn-primary exportClientWithFilter"><span class="fa fa-download spinner-border-sm mr-2"></span> Exporter les clients</a>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- /Search Filter -->
                                    </div>
                                </div>
                                <div class="table-responsive list-box">
                                    <table class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom complet</th>
                                                <th>Téléphone</th>
                                                <th>Email</th>
                                                <th>Raison social</th>
                                                <th></th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($clients as $client): ?>
                                                <?php $activity = "Ajouté par " . $client->getUserAdded()->getNom() . " | Modifié par " . $client->getUserEdited()->getNom(); ?>
                                                <tr>
                                                    <td><?php echo $client->getId(); ?></td>
                                                    <td>
                                                        <?php $photoLink = $client->getPhoto() != '' ? "images/clients/" . $client->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                                        <h2 class="table-avatar">
                                                            <a href="index.php?option=com_client&task=showDetails&id=<?= $client->getId(); ?>"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $client->getPrenom() . " " . $client->getNom(); ?></a>
                                                        </h2>
                                                    </td>
                                                    <td><?php echo $client->getTel(); ?></td>
                                                    <td><?php echo $client->getEmail(); ?></td>
                                                    <td><?php echo $client->getRaisonSocial(); ?></td>
                                                    <td><a href="javascript:void(0);" class="btn btn-sm btn-white text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $activity; ?>"><i class="fa fa-user"></i></a> </td>
                                                    <td class="text-right">
                                                        <?php $state = $client->isActive() ? 'oui' : 'non'; ?>
                                                        <?php $title = $client->isActive() ? 'Actif' : 'Inactif'; ?>
                                                        <?php $color = $client->isActive() ? 'text-success' : 'text-danger'; ?>
                                                        <?php $ico = $client->isActive() ? 'fa fa-toggle-on' : 'fa fa-toggle-off'; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_client')) : ?>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-white <?php echo $color; ?> mr-2 enableClient" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $title; ?>" data-id="<?= $client->getId(); ?>" data-state="<?php echo $state; ?>"><i class="<?php echo $ico; ?>"></i></a>
                                                            <a href="index.php?option=com_client&task=edit&id=<?= $client->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('delete', 'com_client')) : ?>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-white text-danger mr-2 deleteClient" data-toggle="tooltip" data-placement="top" data-original-title="Supprimer" data-id="<?= $client->getId(); ?>"><i class="far fa-trash-alt"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade <?= isset($_GET['task']) && $_GET['task'] == "devis" ? 'show active' : null ?>" id="nav-devis" role="tabpanel" aria-labelledby="nav-devis-tab">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">Liste des devis</h4>
                                <div>
                                    <?php if ($_SESSION['user']->hasDroit('add', 'com_devis')) : ?>
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
                                                    <td><?php echo number_format($devis->getTotal(), 2, ',', ' ') . ' ' . $devis->getDevise(); ?></td>
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
                                    <table class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Devis</th>
                                                <th>Client</th>
                                                <th>Titre</th>
                                                <th>Duration</th>
                                                <th>Garantie</th>
                                                <th>Ville</th>
                                                <th>Nombre de paiement</th>
                                                <th>Date</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($contracts as $contract): ?>
                                                <tr>
                                                    <td><?php echo $contract->getId(); ?></td>
                                                    <td><?php echo $contract->getDevis()->getNumero(); ?></td>
                                                    <td>
                                                        <?php $photoLink = $contract->getDevis()->getClient()->getPhoto() != '' ? "images/clients/" . $contract->getDevis()->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
                                                        <h2 class="table-avatar">
                                                            <a href="#0"><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt="Client Image"> <?php echo $contract->getDevis()->getClient()->getRaisonSocial(); ?></a>
                                                        </h2>
                                                    </td>
                                                    <td><?php echo $contract->getTitre(); ?></td>
                                                    <td><?php echo $contract->getDuration(); ?></td>
                                                    <td><?php echo $contract->getGarantie(); ?></td>
                                                    <td><?php echo $contract->getVille(); ?></td>
                                                    <td><b><?php echo $contract->getNombreDePaiement(); ?></b></td>
                                                    <td data-sort="<?= strtotime($contract->getDate()) ?>"><?php echo normaldate($contract->getDate()); ?></td>
                                                    <td class="text-right">
                                                        <?php if ($_SESSION['user']->hasDroit('edit', 'com_contract')) : ?>
                                                            <a href="index.php?option=com_contract&task=edit&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-warning mr-2" data-toggle="tooltip" data-placement="top" data-original-title="Modifier"><i class="fa fa-pencil-alt"></i></a>
                                                        <?php endif; ?>
                                                        <?php if ($_SESSION['user']->hasDroit('view', 'com_contract')) : ?>
                                                            <a href="components/com_contract/controleurs/router.php?task=pdfContract&id=<?= $contract->getId(); ?>" class="btn btn-sm btn-white text-primary mr-2" data-toggle="tooltip" data-placement="top" data-original-title="PDF"><i class="fa fa-file"></i></a>
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
                                        <a href="index.php?option=com_facture&task=unpaid" class="btn btn-danger mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Factures impayées">
                                            <i class="fa fa-file-invoice"></i>
                                        </a>
                                    <?php endif ?>
                                    <?php if ($_SESSION['user']->hasDroit('add', 'com_facture')) : ?>
                                        <!--<a href="index.php?option=com_facture&task=add" class="btn btn-success mr-1" data-toggle="tooltip" data-placement="top" data-original-title="Ajouter facture">
                                    <i class="fas fa-plus"></i>
                                </a>-->
                                    <?php endif; ?>
                                    <?php if ($_SESSION['user']->hasDroit('view', 'com_facture')) : ?>
                                        <a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search" data-toggle="tooltip" data-placement="top" data-original-title="Filtrer">
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
                                                            <a href="<?php echo $photoLink; ?>" data-fancybox><img class="avatar avatar-sm mr-2 avatar-img rounded-circle" src="<?php echo $photoLink; ?>" alt=""> <?php echo $nom; ?></a>
                                                        </h2>
                                                    </td>
                                                    <td><?php echo $facture->isAvoir() ? '<span class="badge bg-info-light">avoir</span>' : ''; ?></td>
                                                    <td data-sort="<?= strtotime($facture->getDateFacture()) ?>"><?php echo normaldate($facture->getDateFacture()); ?></td>
                                                    <td><?php echo number_format($facture->getTotal(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
                                                    <td><?php echo number_format($facture->getReste(), 2, ',', ' ') . ' ' . $facture->getDevise(); ?></td>
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
                                <div class="div-information px-4">
                                    <hr />
                                    <div class="row">
                                        <div class="col-12">
                                            <h3 class="page-title">Informations</h3>
                                        </div>
                                        <div class="col-12">
                                            <div class="col-12 p-4">
                                                <?= $agence->getInformation(); ?>
                                            </div>
                                        </div>
                                    </div>
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
                                                    <td><?php echo number_format($payment->getMontant(), 2, ',', ' ') . ' ' . $payment->getFacture()->getDevise(); ?></td>
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

<script type="text/javascript">
    $(function() {
        /*
            ******************************************************************
            For invoice
            ******************************************************************
        */

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

                $(".list-box").html(theResponse)
            }
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

        /*
            ******************************************************************
            For client
            ******************************************************************
        */
        $(document).on("click", ".exportClient", function() {
            event.preventDefault();
            window.open('components/com_client/controleurs/router.php?task=exportClient&year=' + $("#year").val(), '_blank');
        })

        $(document).on("click", ".exportClientWithFilter", function() {
            event.preventDefault();
            window.open('components/com_client/controleurs/router.php?task=exportClientWithFilter&year=' + $("#year").val(), '_blank');
        })
        /*  export emails */
        $(document).on("click", ".exportEmailClient", function() {
            event.preventDefault();
            window.open('components/com_client/controleurs/router.php?task=exportEmail', '_blank');
        })

        // envoi du filtre en ajax
        $('form#filterClient').ajaxForm({
            beforeSubmit: function() {
                $("#filterClient .loading").css('display', 'inline-block');
            },
            success: function(theResponse) {
                console.log(theResponse)
                $("#filterClient .loading").fadeOut();
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");
                $(".list-box").html(theResponse)
            }
        });

        $(document).on("click", ".deleteClient", function() {
            event.preventDefault();
            var msgsucces = "Client supprimé avec succès";
            var $btn = $(this);
            if (confirm("Etes-vous sure !")) {
                var id = $(this).attr("data-id");
                var order = 'id=' + id;
                $.post("components/com_client/controleurs/router.php?task=deleteClient", order, function(theResponse) {
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

        $(document).on("click", ".enableClient", function() {
            var btn = $(this);
            var id = btn.attr("data-id");
            var state = btn.attr("data-state");
            var order = 'id=' + id + "&state=" + state;
            $.post("components/com_client/controleurs/router.php?task=enableClient", order, function(theResponse) {
                console.log(theResponse);
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