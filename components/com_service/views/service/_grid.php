<?php
// Grille de services regroupée par catégorie, avec une bannière photo réelle par catégorie -
// même principe que components/com_fournisseur/views/fournisseur/_grid.php. Attend $listeServices
// (tableau d'objets service) et $messageVideGrille (texte si vide) dans le scope de l'appelant.

$groupesService = array();
foreach ($listeServices as $s) {
    $cat = $s->getCategorie();
    $cle = $cat ? (int) $cat->getId() : 0;
    if (!isset($groupesService[$cle])) {
        $groupesService[$cle] = array();
    }
    $groupesService[$cle][] = $s;
}
// Catégories les plus fournies en premier, cohérent avec l'ordre des puces de filtre.
uksort($groupesService, function ($a, $b) use ($groupesService) {
    if ($a === 0) { return 1; }
    if ($b === 0) { return -1; }
    return count($groupesService[$b]) <=> count($groupesService[$a]);
});
?>
<?php if (empty($listeServices)) : ?>
	<div class="service-grid-vide">
		<i class="fa fa-box-open mb-2"></i>
		<p><?= isset($messageVideGrille) ? htmlspecialchars($messageVideGrille) : 'Aucun service dans ce groupe.' ?></p>
	</div>
<?php else : ?>
	<?php foreach ($groupesService as $catId => $items) : ?>
		<?php $photoCategorie = $catId ? getServiceCategoriePhoto($catId) : null; ?>
		<div class="service-groupe" data-categorie-groupe="<?= $catId ?>">
			<?php if ($photoCategorie) : ?>
				<div class="service-groupe-banner">
					<img src="images/service/categories/<?= htmlspecialchars($photoCategorie) ?>" alt="<?= htmlspecialchars($items[0]->getCategorie()->getTitre()) ?>" loading="lazy">
					<div class="service-groupe-banner-titre">
						<i class="fa <?= getServiceCategorieIcone($catId) ?>"></i>
						<?= htmlspecialchars($items[0]->getCategorie()->getTitre()) ?>
						<span class="service-groupe-banner-count"><?= count($items) ?></span>
					</div>
				</div>
			<?php else : ?>
				<h4 class="fournisseur-groupe-titre">
					<i class="fa fa-question-circle"></i> Sans catégorie
					<span class="fournisseur-groupe-count"><?= count($items) ?></span>
				</h4>
			<?php endif; ?>
			<div class="service-groupe-grid">
				<?php foreach ($items as $service) : ?>
					<?php include __DIR__ . '/_card.php'; ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
