<?php $rappels = rappel::findAll(false, $_SESSION['agence']);
$notifications = [];
if ($_SESSION['user']->isResourceHumaine()) {
	$notifications = notification::findAll('resourcehumaine', $_SESSION['user']->getId());
} else {
	$notifications = notification::findAll('user', false);
}
?>
<li class="nav-item dropdown">

	<div class="dropdown-menu notifications">
		<div class="topnav-dropdown-header">
			<span class="notification-title">Notifications</span>
			<a href="javascript:void(0)" class="clear-noti"> Fermer</a>
		</div>
		<div class="noti-content">
			<ul class="notification-list">
				<?php $cpt = 0; ?>
				<?php
				if (sizeof($notifications) > 0) :
					foreach ($notifications as $notification): 
						$background = "";
						if($notification->getTypeMessage() == "success"){
							$background = "bg-success-light";
						}elseif($notification->getTypeMessage() == "warning"){
							$background = "bg-warning-light";
						}else if($notification->getTypeMessage() == "danger"){
							$background = "bg-danger-light";
						}
						?>
						
						<li class="notification-message <?=$background?>">
							<a href="javascript:void(0)" class="mark-as-read" data-url="<?= $notification->getUrl() ?>" data-id="<?= $notification->getId() ?>">
								<div class="notification-content media">
									<span class="avatar avatar-sm">
										<div class="circle-notification">
											<i class="fa fa-comments"></i>
										</div>
									</span>
									<div class="media-body">
										<p class="noti-details"><span class="noti-title"><?php echo $notification->getTitle(); ?></span></p>
										<p class="noti-time"><span class="notification-time"><?php echo $notification->getMessage(); ?></span></p>
									</div>
								</div>
							</a>
						</li>
					<?php $cpt++;
					endforeach;
				else :
					?>
					<li class="notification-message">
						<p class="noti-details text-center my-3"><span class="noti-title text-secondary">Aucune notification</span></p>
					</li>

				<?php
				endif;
				?>
			</ul>
		</div>
		<div class="topnav-dropdown-footer">
			<a href="javascript:void(0)">Voir tout les notifications</a>
		</div>
	</div>
	<a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">
		<i class="fa fa-comments"></i> <?php if (sizeof($notifications) > 0) : ?> <span class="badge badge-pill"><?php echo $cpt; ?></span> <?php endif; ?>
	</a>
</li>

<?php if (!$_SESSION['user']->isResourceHumaine()) : ?>

<?php
$cpt = 0;
foreach ($rappels as $rappel){
    if ($rappel->getDaysLeft() < 30) $cpt++;
}
?>
	<li class="nav-item dropdown" <?php if ($cpt > 0): ?> style="background:#EB462B" <?php endif; ?>>

		<div class="dropdown-menu notifications">
			<div class="topnav-dropdown-header">
				<span class="notification-title">Notifications</span>
				<a href="javascript:void(0)" class="clear-noti"> Fermer</a>
			</div>
			<div class="noti-content">
				<ul class="notification-list">
					<?php
					$cpt = 0;
					foreach ($rappels as $rappel): ?>
						<?php if ($rappel->getDaysLeft() < 30): ?>
							<?php $cpt++; ?>
							<?php $photo = $rappel->getClient()->getPhoto() != '' ? "images/clients/" . $rappel->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
							<li class="notification-message">
								<a href="index.php?option=com_rappel">
									<div class="media">
										<span class="avatar avatar-sm">

											<img class="avatar-img rounded-circle" alt="<?php echo $rappel->getClient()->getRaisonSocial(); ?>" src="<?php echo $photo; ?>">
										</span>
										<div class="media-body">
											<p class="noti-details"><span class="noti-title"><?php echo $rappel->getDomaine(); ?></span> <?php echo $rappel->getType(); ?></p>
											<p class="noti-time"><span class="notification-time">Expire dans <?php echo $rappel->getDaysLeft(); ?> jours</span></p>
										</div>
									</div>
								</a>
							</li>
					<?php endif;
					endforeach; ?>
				</ul>
			</div>
			<div class="topnav-dropdown-footer">
				<a href="index.php?option=com_rappel">Voir tout les rappels</a>
			</div>
		</div>
		<a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">
			<span style="top: -3px;position: relative;">Rappels(Urgent)</span>
			<i class="fa fa-bell"></i> <?php if ($cpt > 0) : ?> <span class="badge badge-pill" style="background-color:#2eba07"><?php echo $cpt; ?></span> <?php endif; ?>
		</a>
	</li>

	<?php $factures = facture::findAll(false, false, false, true, false, false, $_SESSION['agence']); ?>
	<li class="nav-item dropdown">

		<div class="dropdown-menu notifications">
			<div class="topnav-dropdown-header">
				<span class="notification-title">Notifications</span>
				<a href="javascript:void(0)" class="clear-noti"> Fermer</a>
			</div>
			<div class="noti-content">
				<ul class="notification-list">
					<?php $cptf = 0; ?>
					<?php foreach ($factures as $value): ?>
						<?php if ($value->getDaysLeft() < 30 && $value->getDateFin() != null && ($value->getTotal() == $value->getReste() || ($value->getTotal() > $value->getReste() && $value->getReste() > 0))): ?>
							<?php $cptf++; ?>
							<?php $photo = $value->getClient()->getPhoto() != '' ? "images/clients/" . $value->getClient()->getPhoto() : "assets/img/profiles/avatar-01.jpg"; ?>
							<li class="notification-message">
								<a href="index.php?option=com_facture&task=show&id=<?= $value->getId() ?>">
									<div class="media">
										<span class="avatar avatar-sm">

											<img class="avatar-img rounded-circle" alt="<?php echo $value->getClient()->getRaisonSocial(); ?>" src="<?php echo $photo; ?>">
										</span>
										<div class="media-body">
											<p class="noti-details"><span class="noti-title"><?php echo $value->getClient()->getNom() . " " . $value->getClient()->getPrenom(); ?></span></p>
											<p class="noti-time"><span class="notification-time">Expire dans <?php echo $value->getDaysLeft(); ?> jours</span></p>
										</div>
									</div>
								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="topnav-dropdown-footer">
				<a href="index.php?option=com_facture">Voir tout les factures</a>
			</div>
		</div>
		<a href="javascripti:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">
			<i class="fa fa-file-invoice"></i> <?php if (sizeof($factures) > 0 && $cptf > 0) : ?> <span class="badge badge-pill"><?php echo $cptf; ?></span> <?php endif; ?>
		</a>
	</li>

<?php endif; ?>