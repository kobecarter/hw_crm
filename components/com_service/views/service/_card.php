<?php
// Une carte service - partagée entre les deux sections (Services / Packs) de list.php, même
// principe que components/com_fournisseur/views/fournisseur/_card.php (un seul gabarit de carte,
// jamais deux à maintenir en parallèle). Attend $service dans le scope de l'appelant.

$categorieObj = $service->getCategorie();
$categorieId = $categorieObj ? $categorieObj->getId() : 0;
$categorieLabel = $categorieObj ? $categorieObj->getTitre() : null;
$couleurAccent = getServiceCategorieCouleur($categorieId);
$iconeCategorie = getServiceCategorieIcone($categorieId);
$actif = $service->isActive();
$pack = $service->isPack();
$recherche = mb_strtolower(trim($service->getTitre()), 'UTF-8');
?>
<div class="service-card<?= $actif ? '' : ' is-inactive' ?>" data-service-id="<?= $service->getId() ?>" data-categorie="<?= (int) $categorieId ?>" data-recherche="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>">
	<div class="service-card-tilt" style="--service-accent: <?= $couleurAccent ?>;">
		<?php if ($pack) : ?><span class="service-card-pack-ribbon">PACK</span><?php endif; ?>
		<div class="service-card-top">
			<div class="service-card-icone"><i class="fa <?= $iconeCategorie ?>"></i></div>
			<?php if ($_SESSION['user']->hasDroit('edit', 'com_service')) : ?>
			<button type="button" class="fournisseur-card-switch service-toggle-actif" data-id="<?= $service->getId() ?>" data-state="<?= $actif ? 'oui' : 'non' ?>" data-toggle="tooltip" title="<?= $actif ? 'Actif — cliquer pour désactiver' : 'Inactif — cliquer pour activer' ?>">
				<span class="fournisseur-card-switch-thumb"></span>
			</button>
			<?php endif; ?>
		</div>

		<h3 class="service-card-titre" title="<?= htmlspecialchars($service->getTitre()) ?>"><?= htmlspecialchars($service->getTitre()) ?></h3>
		<?php if ($categorieLabel) : ?><div class="service-card-categorie"><?= htmlspecialchars($categorieLabel) ?></div><?php endif; ?>

		<div class="service-card-prix">
			<strong><?= number_format((float) $service->getPrix(), 0, ',', ' ') ?> MAD</strong>
			<span>par <?= htmlspecialchars($service->getUnite()) ?></span>
		</div>

		<div class="service-card-badges">
			<span class="service-card-badge"><i class="fa fa-<?= $service->getFacturation() === 'periodique' ? 'sync-alt' : 'circle' ?>"></i> <?= $service->getFacturation() === 'periodique' ? 'Périodique' : 'Unique' ?></span>
			<?php if ($service->getOrdre()) : ?><span class="service-card-badge"><i class="fa fa-sort-numeric-down"></i> Ordre <?= (int) $service->getOrdre() ?></span><?php endif; ?>
		</div>

		<div class="service-card-footer">
			<?php if ($_SESSION['user']->hasDroit('edit', 'com_service')) : ?>
			<a href="index.php?option=com_service&task=edit&id=<?= $service->getId() ?>" class="fournisseur-card-action" data-toggle="tooltip" title="Modifier"><i class="fa fa-pencil-alt"></i></a>
			<?php endif; ?>
			<?php if ($_SESSION['user']->hasDroit('delete', 'com_service')) : ?>
			<a href="javascript:void(0);" class="fournisseur-card-action fournisseur-card-action-danger service-supprimer" data-id="<?= $service->getId() ?>" data-toggle="tooltip" title="Supprimer"><i class="far fa-trash-alt"></i></a>
			<?php endif; ?>
		</div>
	</div>
</div>
