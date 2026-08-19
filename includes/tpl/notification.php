<?php $rappels = rappel::findAll(false, $_SESSION['agence']);
$notifications = [];
if ($_SESSION['user']->isResourceHumaine()) {
	$notifications = notification::findAll('resourcehumaine', $_SESSION['user']->getId());
} else {
	$notifications = notification::findAll('user', false);
}
?>
<?php
// Centre d'alertes unifié (rappels clients + factures à échéance + fournisseurs en attente +
// BANK STATEMENT à traiter) - calculé avant le <li> puisque le badge "danger" du groupe influe sur
// la classe du <li> lui-même. La modale correspondante est rendue dans includes/tpl/bottom.php (en
// dehors du <ul> de ce fichier, une modale ne pouvant pas être un enfant direct de <ul>). Voir
// includes/functions/functions.php: getAlertesUrgentes().
$afficherAlertes = !$_SESSION['user']->isResourceHumaine();
if ($afficherAlertes) {
	$GLOBALS['alertesUrgentes'] = getAlertesUrgentes($_SESSION['agence']);
	$nbAlertesUrgentes = 0;
	$aUneAlerteDanger = false;
	foreach ($GLOBALS['alertesUrgentes'] as $groupe) {
		$nbAlertesUrgentes += count($groupe['items']);
		foreach ($groupe['items'] as $item) {
			if ($item['urgence'] === 'danger') { $aUneAlerteDanger = true; }
		}
	}
}
?>
<!-- GlassEffectContainer : chat + Rappels(Urgent) partagent UNE seule pile de verre (.glass-icon-group)
     au lieu de deux puces isolées - même <li> (garde l'inline .user-menu.nav>li>a de top.php intacte),
     deux <a class="nav-link"> flex côte à côte. Le bell garde sa classe "danger" au niveau du <li>
     pour teinter tout le groupe quand une alerte est urgente. -->
<li class="nav-item dropdown glass-icon-group<?= ($afficherAlertes && $nbAlertesUrgentes > 0) ? ($aUneAlerteDanger ? ' bell-urgent-li bell-urgent-li-danger' : ' bell-urgent-li') : '' ?>">

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
	<a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown" data-display="static">
		<i class="fa fa-comments"></i> <?php if (sizeof($notifications) > 0) : ?> <span class="badge badge-pill"><?php echo $cpt; ?></span> <?php endif; ?>
	</a>
	<?php if ($afficherAlertes) : ?>
	<a href="javascript:void(0)" class="nav-link" data-toggle="modal" data-target="#alertCenterModal" data-original-title="Rappels(Urgent)" title="Rappels(Urgent)">
		<i class="fa fa-bell<?= $aUneAlerteDanger ? ' bell-urgent-blink' : '' ?>"></i> <?php if ($nbAlertesUrgentes > 0) : ?> <span class="badge badge-pill"><?php echo $nbAlertesUrgentes; ?></span> <?php endif; ?>
	</a>
	<?php endif; ?>
</li>