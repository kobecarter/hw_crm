<?php
// En-tête + barre d'onglets partagée par les 6 pages d'un employé (Profil/Fichiers/Absences/
// Demandes/Bonus/Bulletins de paie), pour naviguer entre elles sans repasser par la liste.
$currentTask = isset($_GET['task']) ? $_GET['task'] : '';
$empNomComplet = trim($resourcehumaine->getFirstName() . ' ' . $resourcehumaine->getLastName());
$empPhotoLink = $resourcehumaine->getPhoto() != '' ? "images/resourceshumaines/" . $resourcehumaine->getPhoto() : "assets/img/profiles/avatar-01.jpg";
$empActif = $resourcehumaine->isActive();
$ongletsEmploye = array(
    'show' => array('label' => 'Profil', 'icon' => 'fa-id-badge'),
    'file' => array('label' => 'Fichiers', 'icon' => 'fa-folder-open'),
    'absence' => array('label' => 'Absences', 'icon' => 'fa-calendar-times'),
    'request' => array('label' => 'Demandes', 'icon' => 'fa-envelope-open-text'),
    'bonus' => array('label' => 'Bonus', 'icon' => 'fa-money-bill-wave'),
    'parrainage' => array('label' => 'Parrainage', 'icon' => 'fa-handshake'),
    'payslip' => array('label' => 'Bulletins de paie', 'icon' => 'fa-file-invoice-dollar'),
    'joboffer' => array('label' => 'Offre d\'emploi', 'icon' => 'fa-file-signature'),
);
?>
<div class="employee-hero">
    <div class="employee-hero-cover"></div>
    <div class="employee-hero-body">
        <label class="avatar avatar-xxl employee-hero-avatar">
            <img class="avatar-img" src="<?php echo $empPhotoLink; ?>" onerror="this.src='assets/img/profiles/avatar-01.jpg'" alt="<?php echo htmlspecialchars($empNomComplet); ?>">
        </label>
        <div class="employee-hero-info">
            <h3><?php echo htmlspecialchars($empNomComplet); ?></h3>
            <p class="text-muted mb-2"><?php echo htmlspecialchars($resourcehumaine->getFunction()); ?></p>
            <span class="badge <?php echo $empActif ? 'bg-success-light' : 'bg-danger-light'; ?>"><?php echo $empActif ? 'Actif' : 'Inactif'; ?></span>
            <?php if ($resourcehumaine->getStatus() != '') : ?><span class="badge bg-primary-light"><?php echo htmlspecialchars($resourcehumaine->getStatus()); ?></span><?php endif; ?>
            <?php if ($resourcehumaine->getReference() != '') : ?><span class="badge bg-secondary-light">Matricule <?php echo htmlspecialchars($resourcehumaine->getReference()); ?></span><?php endif; ?>
        </div>
        <div class="employee-hero-actions">
            <?php if ($resourcehumaine->getPhone() != '') : ?>
                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $resourcehumaine->getPhone()); ?>" class="btn btn-white btn-sm mr-2" data-toggle="tooltip" title="Appeler"><i class="fa fa-phone"></i></a>
            <?php endif; ?>
            <?php if ($resourcehumaine->getEmail() != '') : ?>
                <a href="mailto:<?php echo $resourcehumaine->getEmail(); ?>" class="btn btn-white btn-sm mr-2" data-toggle="tooltip" title="Envoyer un email"><i class="fa fa-envelope"></i></a>
            <?php endif; ?>
            <?php if ($_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) : ?>
                <a href="index.php?option=com_resourcehumaine&task=edit&id=<?php echo $resourcehumaine->getId(); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil-alt mr-1"></i> Modifier</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="employee-hero-tabs">
        <?php foreach ($ongletsEmploye as $taskKey => $onglet) : ?>
            <a href="index.php?option=com_resourcehumaine&task=<?php echo $taskKey; ?>&id=<?php echo $resourcehumaine->getId(); ?>" class="employee-hero-tab <?php echo $currentTask === $taskKey ? 'active' : ''; ?>">
                <i class="fa <?php echo $onglet['icon']; ?>"></i> <?php echo $onglet['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
