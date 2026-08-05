<?php
// Une carte fournisseur - partagée entre l'affichage initial (list.php) et le fragment renvoyé par
// le filtre par année (controleur.php: filterFournisseur), pour ne jamais avoir deux gabarits de
// carte à maintenir en parallèle. Attend $fournisseur dans le scope de l'appelant.

$nomAffiche = trim((string) $fournisseur->getRaisonSocial()) !== ''
    ? $fournisseur->getRaisonSocial()
    : trim($fournisseur->getPrenom() . ' ' . $fournisseur->getNom());
$sousNom = trim((string) $fournisseur->getRaisonSocial()) !== ''
    ? trim($fournisseur->getPrenom() . ' ' . $fournisseur->getNom())
    : '';
$aPhoto = $fournisseur->getPhoto() != '';
$logoInfo = $aPhoto ? null : fournisseur::getLogoInfo($nomAffiche);
$categorieId = $fournisseur->getCategorie();
$categoriesLabelsCard = getCategorieFournisseur();
$categorieLabel = isset($categoriesLabelsCard[$categorieId]) ? $categoriesLabelsCard[$categorieId] : null;
$categorieIcone = getCategorieFournisseurIcone($categorieId);
$docLink = $fournisseur->getDoc() != '' ? 'images/fournisseurs/' . $fournisseur->getDoc() : '';
$actif = $fournisseur->isActive();
$recherche = mb_strtolower(trim($nomAffiche . ' ' . $sousNom . ' ' . $fournisseur->getEmail() . ' ' . $fournisseur->getTel()), 'UTF-8');
?>
<div class="fournisseur-card<?= $actif ? '' : ' is-inactive' ?>" data-fournisseur-id="<?= $fournisseur->getId() ?>" data-categorie="<?= (int) $categorieId ?>" data-recherche="<?= htmlspecialchars($recherche, ENT_QUOTES, 'UTF-8') ?>" data-actif="<?= $actif ? 1 : 0 ?>">
	<div class="fournisseur-card-tilt">
		<div class="fournisseur-card-top">
			<div class="fournisseur-card-avatar" <?= $aPhoto ? '' : 'style="background:' . $logoInfo['bg'] . ';"' ?>>
				<?php if ($aPhoto) :?>
					<img src="images/fournisseurs/<?= $fournisseur->getPhoto() ?>" alt="<?= htmlspecialchars($nomAffiche) ?>">
				<?php else :?>
					<span><?= $logoInfo['initials'] ?></span>
				<?php endif;?>
			</div>
			<?php if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) :?>
			<button type="button" class="fournisseur-card-switch fournisseur-toggle-actif" data-id="<?= $fournisseur->getId() ?>" data-state="<?= $actif ? 'oui' : 'non' ?>" data-toggle="tooltip" title="<?= $actif ? 'Actif — cliquer pour désactiver' : 'Inactif — cliquer pour activer' ?>">
				<span class="fournisseur-card-switch-thumb"></span>
			</button>
			<?php endif;?>
		</div>

		<!-- Bloc info regroupé (nom + badges + contacts) : un seul enfant flex en vue liste, pour
		     que le CSS puisse basculer d'une carte verticale à une ligne horizontale sans toucher
		     à ce gabarit. -->
		<div class="fournisseur-card-info">
			<div class="fournisseur-card-identite">
				<h3 class="fournisseur-card-nom" title="<?= htmlspecialchars($nomAffiche) ?>"><?= htmlspecialchars($nomAffiche) ?></h3>
				<?php if ($sousNom !== '') :?><div class="fournisseur-card-sousnom"><?= htmlspecialchars($sousNom) ?></div><?php endif;?>
			</div>

			<div class="fournisseur-card-badges">
				<?php if ($categorieLabel) :?>
				<span class="fournisseur-card-badge-categorie"><i class="fa <?= $categorieIcone ?>"></i> <?= htmlspecialchars($categorieLabel) ?></span>
				<?php endif;?>
				<?php if ($fournisseur->isValide()) :?>
				<span class="fournisseur-card-badge-valide" data-toggle="tooltip" title="Fournisseur validé"><i class="fa fa-check-circle"></i></span>
				<?php endif;?>
			</div>

			<div class="fournisseur-card-contacts">
				<?php if ($fournisseur->getTel()) :?>
				<a href="tel:<?= htmlspecialchars($fournisseur->getTel()) ?>" class="fournisseur-card-contact"><i class="fa fa-phone"></i><span><?= htmlspecialchars($fournisseur->getTel()) ?></span></a>
				<?php endif;?>
				<?php if ($fournisseur->getEmail()) :?>
				<a href="mailto:<?= htmlspecialchars($fournisseur->getEmail()) ?>" class="fournisseur-card-contact"><i class="fa fa-envelope"></i><span><?= htmlspecialchars($fournisseur->getEmail()) ?></span></a>
				<?php endif;?>
				<?php if (!$fournisseur->getTel() && !$fournisseur->getEmail()) :?>
				<span class="fournisseur-card-contact fournisseur-card-contact-vide">Aucun contact renseigné</span>
				<?php endif;?>
			</div>
		</div>

		<div class="fournisseur-card-footer">
			<?php if ($docLink !== '') :?>
			<a href="<?= $docLink ?>" data-fancybox data-toggle="tooltip" title="Voir le document" class="fournisseur-card-action"><i class="fa fa-file-alt"></i></a>
			<?php endif;?>
			<?php if ($_SESSION['user']->hasDroit('edit', 'com_fournisseur')) :?>
			<a href="index.php?option=com_fournisseur&task=edit&id=<?= $fournisseur->getId() ?>" data-toggle="tooltip" title="Modifier" class="fournisseur-card-action"><i class="fa fa-pencil-alt"></i></a>
			<?php endif;?>
			<?php if ($_SESSION['user']->hasDroit('delete', 'com_fournisseur')) :?>
			<a href="javascript:void(0);" class="fournisseur-card-action fournisseur-card-action-danger fournisseur-supprimer" data-id="<?= $fournisseur->getId() ?>" data-toggle="tooltip" title="Supprimer"><i class="far fa-trash-alt"></i></a>
			<?php endif;?>
		</div>
	</div>
</div>
