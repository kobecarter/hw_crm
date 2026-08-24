<?php
// Génère un PDF facture/devis dans un processus PHP isolé, appelé par
// PdfController (hw_crm/api/) via exec(). Nécessaire car hw_crm/api/vendor/
// (chargé par index.php avant toute route) et hw_crm/vendor/ (chargé par
// facture::pdfFacture()/devis::pdfDevis() en interne) déclarent tous les deux
// mpdf/psr-log-aware-trait contre des versions incompatibles de psr/log : les
// charger tous les deux dans le même process PHP fatale sur une redéclaration
// de méthode. Un process séparé ne charge jamais hw_crm/api/vendor/, donc pas
// de collision. Écrit le PDF dans uploads/ et imprime son nom de fichier sur
// stdout ; toute erreur (introuvable / accès refusé) va sur stderr avec un
// code de sortie non nul.

chdir(__DIR__);
require_once 'config.php';
require_once 'instanceDb.php';
require_once 'includes/functions/functions.php';

[, $type, $idArg, $clientIDArg] = $argv;
$id = (int) $idArg;
$clientID = (int) $clientIDArg;

bootstrapSystemSession(defined('SLACK_BOT_ACTING_USER_ID') ? SLACK_BOT_ACTING_USER_ID : 5, 1, 'fr');

if ($type === 'facture') {
    chdir(__DIR__ . '/components/com_facture/controleurs/');
    $facture = facture::find($id, $_SESSION['agence']);
    if (!$facture || !$facture->getClient() || $facture->getClient()->getId() != $clientID) {
        fwrite(STDERR, 'not_found');
        exit(1);
    }
    if (!$facture->getLangue()) {
        $facture->setLangue('fr');
    }
    $fileName = $facture->pdfFacture('save');
    echo $fileName;
    exit(0);
}

if ($type === 'devis') {
    chdir(__DIR__ . '/components/com_devis/controleurs/');
    $devis = devis::find($id, $_SESSION['agence']);
    if (!$devis || !$devis->getClient() || $devis->getClient()->getId() != $clientID) {
        fwrite(STDERR, 'not_found');
        exit(1);
    }
    if (!$devis->getLangue()) {
        $devis->setLangue('fr');
    }
    $fileName = $devis->pdfDevis('save');
    echo $fileName;
    exit(0);
}

fwrite(STDERR, 'bad_type');
exit(1);
