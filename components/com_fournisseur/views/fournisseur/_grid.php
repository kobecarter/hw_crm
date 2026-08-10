<?php
// Grille de fournisseurs regroupée par catégorie - partagée entre l'affichage initial (list.php)
// et le fragment renvoyé par le filtre par année (controleur.php: filterFournisseur). Attend
// $fournisseurs (tableau d'objets fournisseur) dans le scope de l'appelant ; $messageVideGrille
// est optionnel (message affiché si $fournisseurs est vide).

$categoriesLabelsGrille = getCategorieFournisseur();
$groupes = array();
foreach ($fournisseurs as $f) {
    $cat = $f->getCategorie();
    $cle = ($cat && isset($categoriesLabelsGrille[$cat])) ? (int) $cat : 0;
    if (!isset($groupes[$cle])) {
        $groupes[$cle] = array();
    }
    $groupes[$cle][] = $f;
}
// Catégories les plus fournies en premier (même ordre que les puces de filtre) - "Sans catégorie"
// (0) toujours en dernier plutôt que mêlée aux autres selon son nombre d'éléments.
uksort($groupes, function ($a, $b) use ($groupes) {
    if ($a === 0) { return 1; }
    if ($b === 0) { return -1; }
    return count($groupes[$b]) <=> count($groupes[$a]);
});
?>
<div class="fournisseur-grid" id="fournisseurGrid">
	<?php foreach ($groupes as $catId => $items) :?>
	<div class="fournisseur-groupe" data-categorie-groupe="<?= $catId ?>">
		<h4 class="fournisseur-groupe-titre">
			<i class="fa <?= $catId ? getCategorieFournisseurIcone($catId) : 'fa-question-circle' ?>"></i>
			<?= htmlspecialchars($catId ? $categoriesLabelsGrille[$catId] : 'Sans catégorie') ?>
			<span class="fournisseur-groupe-count"><?= count($items) ?></span>
		</h4>
		<div class="fournisseur-groupe-grid">
			<?php foreach ($items as $fournisseur) :?>
				<?php include __DIR__ . '/_card.php'; ?>
			<?php endforeach;?>
		</div>
	</div>
	<?php endforeach;?>
</div>
<div class="fournisseur-grid-vide d-none" id="fournisseurGrilleVide">
	<i class="fa fa-search mb-2"></i>
	<p>Aucun fournisseur ne correspond à cette recherche.</p>
</div>
<?php if (empty($fournisseurs)) :?>
<div class="fournisseur-grid-vide">
	<i class="fa fa-truck mb-2"></i>
	<p><?= isset($messageVideGrille) ? htmlspecialchars($messageVideGrille) : 'Aucun fournisseur pour le moment.' ?></p>
</div>
<?php endif;?>
