<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addTva':
            addTva($_POST);
            break;
        case 'editTva':
            editTva($_POST);
            break;
        case 'deleteTva':
            deleteTva($_POST);
            break;
        case 'deleteDocTva':
            deleteDocTva($_POST);
            break;
        case 'exportTvaComptable':
            exportTvaComptable($_GET);
            break;
    }
}

// Style d'une ligne d'en-tête de tableau (fond coloré, texte blanc gras, centré) — factorisé
// car réutilisé identiquement sur les 4 onglets de détail de l'export comptable.
function tvaExportStyleEnTete($sheet, $range, $fillColor)
{
    $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
    $sheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($fillColor);
    $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
}

// Écrit un tableau organisé (bandeau titre, en-tête colorée, données zébrées, bordures,
// ligne de totaux optionnelle, gel + filtre auto sur l'en-tête) dans une feuille — factorisé
// car les 4 onglets de détail de l'export comptable partagent exactement cette présentation,
// seuls les colonnes/lignes/couleur changent.
function tvaExportEcrireTableau($sheet, $titre, $headers, $rows, $totaux, $colonnesMontant, $fillColor)
{
    $nbCols = count($headers);
    $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($nbCols);

    $sheet->setCellValue('A1', $titre);
    $sheet->mergeCells('A1:' . $lastCol . '1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
    $sheet->getRowDimension(1)->setRowHeight(28);

    $headerRow = 3;
    foreach ($headers as $i => $h) {
        $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
        $sheet->setCellValue($col . $headerRow, $h);
        $sheet->getColumnDimension($col)->setWidth(max(16, strlen($h) + 4));
    }
    tvaExportStyleEnTete($sheet, 'A' . $headerRow . ':' . $lastCol . $headerRow, $fillColor);
    $sheet->getRowDimension($headerRow)->setRowHeight(30);

    $row = $headerRow + 1;
    foreach ($rows as $data) {
        foreach (array_values($data) as $i => $value) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $row, $value);
            if (in_array($i, $colonnesMontant)) {
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
        }
        if (($row - $headerRow) % 2 == 0) {
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F5F3FF');
        }
        $row++;
    }
    $lastDataRow = $row - 1;

    if ($lastDataRow > $headerRow) {
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $lastDataRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setARGB('DDDDDD');
        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':' . $lastCol . $lastDataRow);
    } else {
        $sheet->setCellValue('A' . $row, 'Aucune donnée sur cette période.');
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->getColor()->setARGB('888888');
    }

    if ($totaux !== null && $lastDataRow > $headerRow) {
        $row++;
        foreach (array_values($totaux) as $i => $value) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $row, $value);
            if (in_array($i, $colonnesMontant)) {
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
        }
        $range = 'A' . $row . ':' . $lastCol . $row;
        $sheet->getStyle($range)->getFont()->setBold(true);
        $sheet->getStyle($range)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $sheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('EDE9FE');
    }
}

// Export Excel multi-onglets pour le comptable : détail des ventes/paiements comptés dans la
// TVA collectée, détail des achats/charges comptés dans la TVA déductible, et — comme demandé
// explicitement — la liste complète de toutes les ventes et de tous les achats/charges
// ajoutés sur la période (toutes devises), pour une traçabilité totale au-delà du seul
// périmètre de la TVA (qui reste, lui, limité au DH — cf. onglet "Synthèse").
function exportTvaComptable($data)
{
    if (!$_SESSION['user']->hasDroit('view', 'com_accounting')) {
        return;
    }

    $agence = agence::find($_SESSION['agence'], $_SESSION['langue']);
    $periodiciteTva = $agence->getTvaPeriodicite() === 'trimestriel' ? 'trimestriel' : 'mensuel';
    if (isset($data['from']) && !empty($data['from']) && isset($data['to']) && !empty($data['to'])) {
        $from = dateBD($data['from']);
        $to = dateBD($data['to']);
    } else {
        // Sans dates fournies (appel direct), la période par défaut respecte la périodicité de
        // l'agence : un mois pour une déclarante mensuelle, tout le trimestre civil en cours pour
        // une déclarante trimestrielle.
        $periodeParDefaut = tva::periodeReference($periodiciteTva, null, 0);
        $from = $periodeParDefaut['debut']->format('Y-m-d');
        $to = $periodeParDefaut['fin']->format('Y-m-d');
    }

    $ventesTva = tvaSimulateur::detailVentesTvaCollectee($from, $to, $_SESSION['agence']);
    $achatsTva = tvaSimulateur::detailAchatsTvaDeductible($from, $to, $_SESSION['agence']);
    $ventesToutes = tvaSimulateur::detailVentesAjoutees($from, $to, $_SESSION['agence']);
    $achatsToutes = charge::findAllByDate($from, $to, $_SESSION['agence']);

    $totalCollecteeTTC = array_sum(array_column($ventesTva, 'montant_ttc'));
    $totalCollecteeHT = array_sum(array_column($ventesTva, 'montant_ht'));
    $totalCollecteeTVA = array_sum(array_column($ventesTva, 'montant_tva'));
    $totalDeductibleTTC = array_sum(array_column($achatsTva, 'montant_ttc'));
    $totalDeductibleHT = array_sum(array_column($achatsTva, 'montant_ht'));
    $totalDeductibleTVA = array_sum(array_column($achatsTva, 'montant_tva'));

    $typeLabels = array('fixe' => 'Charge fixe', 'variable' => 'Charge variable', 'hors_hw' => 'Hors Hello World');
    $modeLabels = array('espece' => 'Espèce', 'cheque' => 'Chèque', 'virement' => 'Virement', 'paiement_en_ligne' => 'Paiement en ligne');
    $periodeTexte = normalDate($from) . ' au ' . normalDate($to);

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->removeSheetByIndex(0);
    $spreadsheet->getProperties()->setTitle('Tableau TVA - ' . $agence->getNom())->setCreator('HW CRM');

    // Onglet 1 : Synthèse
    $sheetSynthese = $spreadsheet->createSheet();
    $sheetSynthese->setTitle('Synthèse');
    $sheetSynthese->setCellValue('A1', 'Tableau TVA — ' . $agence->getNom());
    $sheetSynthese->mergeCells('A1:E1');
    $sheetSynthese->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheetSynthese->setCellValue('A2', 'Période du ' . $periodeTexte);
    $sheetSynthese->mergeCells('A2:E2');
    $sheetSynthese->getStyle('A2')->getFont()->setItalic(true)->setSize(11);
    $sheetSynthese->setCellValue('A3', 'Généré le ' . date('d/m/Y à H:i'));
    $sheetSynthese->mergeCells('A3:E3');
    $sheetSynthese->getStyle('A3')->getFont()->setSize(9)->getColor()->setARGB('888888');

    $r = 5;
    $infos = array(
        array('Raison sociale', $agence->getRaisonSocial() != '' ? $agence->getRaisonSocial() : $agence->getNom()),
        array('ICE', $agence->getIce()),
        array('Identifiant Fiscal (IF)', $agence->getIf()),
        array('Taux de TVA appliqué', $agence->getTva() . ' %'),
    );
    foreach ($infos as $info) {
        $sheetSynthese->setCellValue('A' . $r, $info[0]);
        $sheetSynthese->setCellValue('B' . $r, $info[1]);
        $sheetSynthese->getStyle('A' . $r)->getFont()->setBold(true);
        $r++;
    }

    $r += 1;
    $sheetSynthese->setCellValue('A' . $r, 'Montants de la période (dirhams uniquement)');
    $sheetSynthese->mergeCells('A' . $r . ':E' . $r);
    $sheetSynthese->getStyle('A' . $r)->getFont()->setBold(true)->setSize(12);
    tvaExportStyleEnTete($sheetSynthese, 'A' . $r . ':E' . $r, '6d28d9');
    $r++;

    $kpis = array(
        array('TVA collectée (ventes encaissées)', $totalCollecteeTVA, false),
        array('TVA déductible (achats payés)', $totalDeductibleTVA, false),
        array('TVA nette de la période (collectée − déductible)', $totalCollecteeTVA - $totalDeductibleTVA, true),
    );
    foreach ($kpis as $kpi) {
        $sheetSynthese->setCellValue('A' . $r, $kpi[0]);
        $sheetSynthese->mergeCells('A' . $r . ':C' . $r);
        $sheetSynthese->setCellValue('D' . $r, $kpi[1]);
        $sheetSynthese->mergeCells('D' . $r . ':E' . $r);
        $sheetSynthese->getStyle('D' . $r)->getNumberFormat()->setFormatCode('#,##0.00 "DH"');
        $sheetSynthese->getStyle('D' . $r)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        if ($kpi[2]) {
            $sheetSynthese->getStyle('A' . $r . ':E' . $r)->getFont()->setBold(true);
            $sheetSynthese->getStyle('A' . $r . ':E' . $r)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
        }
        $r++;
    }

    $r += 1;
    $sheetSynthese->setCellValue('A' . $r, 'À lire avant de transmettre ce tableau');
    $sheetSynthese->getStyle('A' . $r)->getFont()->setBold(true);
    $r++;
    $note = "Cette estimation est calculée automatiquement par le CRM à partir des règlements clients encaissés et des charges payées marquées déductibles sur la période choisie. Elle ne remplace pas la déclaration officielle établie par le comptable — elle sert uniquement d'aide à l'anticipation.\n\n"
        . "• Les factures proforma ne sont jamais comptées : elles ne portent pas de TVA réellement due.\n"
        . "• Seuls les documents en dirhams (DH) sont inclus dans les montants ci-dessus. Les paiements reçus en devise étrangère (ex. compte convertible) ne sont pas pris en compte dans le calcul de la TVA.\n"
        . "• Les onglets \"Toutes les ventes\" et \"Tous les achats-charges\" listent en revanche l'intégralité des documents de la période, toutes devises confondues, à titre de traçabilité complète pour le comptable.";
    $sheetSynthese->setCellValue('A' . $r, $note);
    $sheetSynthese->mergeCells('A' . $r . ':E' . ($r + 6));
    $sheetSynthese->getStyle('A' . $r)->getAlignment()->setWrapText(true)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
    $sheetSynthese->getRowDimension($r)->setRowHeight(120);

    foreach (array('A', 'B', 'C', 'D', 'E') as $col) {
        $sheetSynthese->getColumnDimension($col)->setWidth(26);
    }

    // Onglet 2 : Ventes prises en compte dans la TVA collectée
    $sheetVentesTva = $spreadsheet->createSheet();
    $sheetVentesTva->setTitle('Ventes - TVA collectee');
    $rows = array();
    foreach ($ventesTva as $l) {
        $rows[] = array(
            $l['numero_facture'],
            $l['client'],
            $l['date_facture'] ? date('d/m/Y', strtotime($l['date_facture'])) : '',
            $l['date_paiement'] ? date('d/m/Y', strtotime($l['date_paiement'])) : '',
            $l['methode_paiement'],
            round($l['montant_ttc'], 2),
            $l['taux_tva'],
            round($l['montant_ht'], 2),
            round($l['montant_tva'], 2),
        );
    }
    $totaux = count($rows) > 0 ? array('', '', '', '', 'TOTAL', round($totalCollecteeTTC, 2), '', round($totalCollecteeHT, 2), round($totalCollecteeTVA, 2)) : null;
    tvaExportEcrireTableau(
        $sheetVentesTva,
        'Ventes prises en compte dans la TVA collectée — ' . $periodeTexte . ' (DH uniquement)',
        array('N° Facture', 'Client', 'Date facture', 'Date encaissement', 'Mode paiement', 'Montant encaissé TTC', 'Taux TVA (%)', 'Montant HT', 'Montant TVA collectée'),
        $rows,
        $totaux,
        array(5, 7, 8),
        '6d28d9'
    );

    // Onglet 3 : Achats/charges pris en compte dans la TVA déductible
    $sheetAchatsTva = $spreadsheet->createSheet();
    $sheetAchatsTva->setTitle('Achats - TVA deductible');
    $rows = array();
    foreach ($achatsTva as $l) {
        $rows[] = array(
            $l['titre'],
            $l['date_charge'] ? tva::libellePeriode($l['date_charge'], $periodiciteTva) : '',
            isset($typeLabels[$l['type']]) ? $typeLabels[$l['type']] : $l['type'],
            $l['date_charge'] ? date('d/m/Y', strtotime($l['date_charge'])) : '',
            $l['date_paiement'] ? date('d/m/Y', strtotime($l['date_paiement'])) : '',
            isset($modeLabels[$l['mode_paiement']]) ? $modeLabels[$l['mode_paiement']] : $l['mode_paiement'],
            round($l['montant_ttc'], 2),
            $l['taux_tva'],
            round($l['montant_ht'], 2),
            round($l['montant_tva'], 2),
        );
    }
    $totaux = count($rows) > 0 ? array('', '', '', '', '', 'TOTAL', round($totalDeductibleTTC, 2), '', round($totalDeductibleHT, 2), round($totalDeductibleTVA, 2)) : null;
    tvaExportEcrireTableau(
        $sheetAchatsTva,
        'Achats/charges pris en compte dans la TVA déductible — ' . $periodeTexte . ' (DH uniquement)',
        array('Libellé / Fournisseur', 'Période TVA (' . ($periodiciteTva == 'trimestriel' ? 'trimestrielle' : 'mensuelle') . ')', 'Type', 'Date charge', 'Date paiement', 'Mode paiement', 'Montant payé TTC', 'Taux TVA (%)', 'Montant HT', 'Montant TVA déductible'),
        $rows,
        $totaux,
        array(6, 8, 9),
        '4f46e5'
    );

    // Onglet 4 : Toutes les ventes ajoutées (toutes devises, transparence complète)
    $sheetVentesToutes = $spreadsheet->createSheet();
    $sheetVentesToutes->setTitle('Toutes les ventes');
    $rows = array();
    foreach ($ventesToutes as $f) {
        $type = $f['avoir'] == 1 ? 'Avoir' : ($f['proforma'] == 1 ? 'Proforma' : 'Facture');
        $reste = $f['proforma'] == 1 ? '' : round((float) $f['total'] - (float) $f['montant_regle'], 2);
        $rows[] = array(
            $f['numero'],
            trim($f['raison_social']) !== '' ? $f['raison_social'] : trim($f['prenom'] . ' ' . $f['nom']),
            $f['date_facture'] ? date('d/m/Y', strtotime($f['date_facture'])) : '',
            $f['devise'],
            $type,
            round((float) $f['total'], 2),
            round((float) $f['montant_regle'], 2),
            $reste,
        );
    }
    tvaExportEcrireTableau(
        $sheetVentesToutes,
        'Toutes les ventes ajoutées — ' . $periodeTexte . ' (toutes devises)',
        array('N° Facture', 'Client', 'Date facture', 'Devise', 'Type', 'Montant total', 'Montant réglé', 'Reste à payer'),
        $rows,
        null,
        array(5, 6, 7),
        'f59e0b'
    );

    // Onglet 5 : Tous les achats/charges ajoutés (toutes devises, transparence complète)
    $sheetAchatsToutes = $spreadsheet->createSheet();
    $sheetAchatsToutes->setTitle('Tous les achats-charges');
    $rows = array();
    foreach ($achatsToutes as $c) {
        $rows[] = array(
            $c['titre'],
            isset($typeLabels[$c['type']]) ? $typeLabels[$c['type']] : $c['type'],
            $c['date_charge'] ? date('d/m/Y', strtotime($c['date_charge'])) : '',
            $c['date_payment'] ? date('d/m/Y', strtotime($c['date_payment'])) : '',
            $c['devise'],
            round((float) $c['total'], 2),
            $c['paid'] == 1 ? 'Oui' : 'Non',
            $c['tva_taux'] !== null ? $c['tva_taux'] : '',
            $c['tva_deductible'] == 1 ? 'Oui' : 'Non',
        );
    }
    tvaExportEcrireTableau(
        $sheetAchatsToutes,
        'Tous les achats/charges ajoutés — ' . $periodeTexte . ' (toutes devises)',
        array('Libellé', 'Type', 'Date charge', 'Date paiement', 'Devise', 'Montant', 'Payée', 'Taux TVA (%)', 'Déductible TVA'),
        $rows,
        null,
        array(5),
        '10b981'
    );

    $spreadsheet->setActiveSheetIndex(0);

    $nomBase = 'TVA_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $agence->getNom()) . '_' . $from . '_au_' . $to;

    $dirTmp = __DIR__ . '/../../../../images/tmp';
    if (!is_dir($dirTmp)) {
        @mkdir($dirTmp, 0775, true);
    }

    // Le tableau Excel est d'abord écrit sur disque : PhpSpreadsheet ne sait pas écrire
    // directement dans un flux ZipArchive.
    $excelTempPath = $dirTmp . '/' . uniqid('tva_') . '.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($excelTempPath);

    // Comme demandé : le ZIP embarque aussi les pièces justificatives des lignes réellement
    // comptées dans le tableau (pas celles des onglets "toutes les ventes/achats", qui listent
    // volontairement plus large que le périmètre TVA) — les factures PDF (une seule par
    // facture même si elle a plusieurs règlements dans l'onglet détail) et les photos/scans
    // des charges déductibles qui en ont une.
    $idsFactureUniques = array_values(array_unique(array_filter(array_column($ventesTva, 'id_facture'))));
    $facturesObjets = array();
    foreach ($idsFactureUniques as $idF) {
        $f = facture::find($idF, $_SESSION['agence']);
        if ($f && $f->getId()) {
            $facturesObjets[] = $f;
        }
    }

    $zipTempPath = $dirTmp . '/' . uniqid('tva_zip_') . '.zip';
    $zip = new ZipArchive();
    $zip->open($zipTempPath, ZipArchive::CREATE);
    $zip->addFile($excelTempPath, $nomBase . '.xlsx');

    $tempPdfFiles = array();
    if (!empty($facturesObjets)) {
        $tempPdfFiles = facture::pdfFactures($facturesObjets, $zip);
    }

    foreach ($achatsTva as $l) {
        if (empty($l['photo'])) {
            continue;
        }
        $cheminPhoto = '../../../images/charges/' . $l['photo'];
        if (file_exists($cheminPhoto)) {
            $zip->addFile($cheminPhoto, 'Justificatifs achats/' . $l['id'] . ' - ' . $l['photo']);
        }
    }

    $zip->close();

    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment;filename="' . $nomBase . '.zip"');
    header('Cache-Control: max-age=0');
    readfile($zipTempPath);

    @unlink($zipTempPath);
    @unlink($excelTempPath);
    foreach ($tempPdfFiles as $tempPdf) {
        @unlink($tempPdf);
    }
    exit;
}

function deleteDocTva($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices)) {
        $tva = tva::find($data['id'],$_SESSION['agence']);
        $tva->setDoc("");
        if($tva->edit() == 1){
            @unlink("../../../images/tva/" . $tva->getDoc());
            echo "1";
        }
    } else {
        echo "0";
    }
}

function addTva($data)
{
    // fieldCheck() utilise empty() : un montant "0" (mois à crédit, TVA due nulle) serait
    // rejeté à tort, donc on valide id_agence/amount/date "à la main" ici plutôt que via
    // fieldCheck() pour ce champ précis (piège déjà rencontré cette session).
    if (!isset($data['id_agence']) || $data['id_agence'] === '' || !isset($data['amount']) || $data['amount'] === '') {
        echo "0";
        return;
    }

    $periodicite = isset($data['periodicite']) && $data['periodicite'] === 'trimestriel' ? 'trimestriel' : 'mensuel';

    if ($periodicite === 'trimestriel') {
        if (empty($data['trimestre']) || empty($data['annee'])) {
            echo "0";
            return;
        }
        echo ajouterTvaTrimestrielle($data) ? "1" : "2";
        return;
    }

    if (!isset($data['date']) || $data['date'] === '') {
        echo "0";
        return;
    }

    $tva = buildTva($data);
    if ($tva->add() != 1) {
        echo "2";
        return;
    }
    // Capturé immédiatement après l'insertion : creerChargeDepuisPreuvePaiement() ci-dessous
    // fait sa propre INSERT (dans crm_charge), ce qui écraserait LAST_INSERT_ID() si on
    // appelait tva::getLastId() après coup (bug réel rencontré en testant : la charge se
    // créait bien, mais l'UPDATE de liaison ciblait alors l'id de la charge, pas celui de
    // la TVA, laissant id_charge NULL sur la vraie ligne).
    $idTva = tva::getLastId();

    // Preuve de paiement fournie -> pousse automatiquement une charge fixe liée (voir
    // creerChargeDepuisPreuvePaiement()) ; sinon la TVA reste "pas encore poussée".
    $montantCharge = floatval($data['amount']) + (isset($data['increasion']) && $data['increasion'] !== '' ? floatval($data['increasion']) : 0);
    $titreCharge = 'TVA ' . nomMoisTva((int) date('n', strtotime($tva->getDate()))) . ' ' . date('Y', strtotime($tva->getDate()));
    $idCharge = creerChargeDepuisPreuvePaiement($data, $montantCharge, $titreCharge, $data['id_agence']);
    if ($idCharge) {
        $tva->setId($idTva);
        $tva->setIdCharge($idCharge);
        $tva->edit();
    }

    echo "1";
}

function editTva($data)
{
    if (!isset($data['id_tva']) || $data['id_tva'] === '' || !isset($data['id_agence']) || $data['id_agence'] === '' || !isset($data['amount']) || $data['amount'] === '' || !isset($data['date']) || $data['date'] === '') {
        echo "0";
        return;
    }

    $tva = buildTva($data, $data['id_tva']);
    // Si une charge est déjà liée, on ne la remplace pas silencieusement même si un nouveau
    // fichier "preuve de paiement" est soumis (pour éviter de créer une charge en double) —
    // modifier la charge existante se fait directement depuis le module Charges.
    $dejaPoussee = $tva->getIdCharge() != '' && $tva->getIdCharge() !== null;

    if ($tva->edit() != 1) {
        echo "2";
        return;
    }

    if (!$dejaPoussee) {
        $montantCharge = floatval($data['amount']) + (isset($data['increasion']) && $data['increasion'] !== '' ? floatval($data['increasion']) : 0);
        $titreCharge = 'TVA ' . nomMoisTva((int) date('n', strtotime($tva->getDate()))) . ' ' . date('Y', strtotime($tva->getDate()));
        $idCharge = creerChargeDepuisPreuvePaiement($data, $montantCharge, $titreCharge, $data['id_agence']);
        if ($idCharge) {
            $tva->setIdCharge($idCharge);
            $tva->edit();
        }
    }

    echo "1";
}

function deleteTva($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find($id, $agence) plutôt que new+setId() : sans ça, un id valide d'une autre agence se
        // faisait supprimer sans aucune vérification d'appartenance (IDOR).
        $tva = tva::find($data['id'], $_SESSION['agence']);
        if ($tva->getId() == 0) {
            echo "2";
            return;
        }
        if ($tva->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildTva($data, $id = null)
{
    $tva = new tva();

    if($id){
        // Bug corrigé : tva::find($id) sans agence filtrait par défaut sur l'agence 1,
        // donc l'édition d'une TVA d'une autre agence (ex: Verse Concept) ne trouvait
        // aucune ligne et l'edit() suivant ne modifiait rien silencieusement.
        $tva = tva::find($id, isset($data['id_agence']) ? $data['id_agence'] : $_SESSION['agence']);
    }

    // Le document de déclaration n'était jusqu'ici jamais réellement sauvegardé (uniquement
    // utilisé côté client pour le pré-remplissage IA) — on l'attache maintenant comme les
    // autres pièces jointes de l'application (même pattern que buildCharge() pour "photo").
    $doc = array();
    if (isset($_FILES['doc']) && isset($_FILES['doc']['name'][0]) && $_FILES['doc']['name'][0] != '') {
        $doc = uploadFiles('doc', '../../../images/tva/', array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    }
    if (isset($doc[0])) {
        $tva->setDoc($doc[0]);
    }

	$tva->setAgence(agence::find($data['id_agence'], $_SESSION['langue']));
    $tva->setAmount($data['amount']);
    $tva->setIncreasion(isset($data['increasion']) && $data['increasion'] !== '' ? $data['increasion'] : 0);
    $tva->setDate(date("Y-m-t",strtotime($data['date']."-1")));
    $tva->setDatePayment(isset($data['date_payment']) && !empty($data['date_payment']) ? dateBD($data['date_payment']) : null);
    $tva->setStatus(isset($data['status']) ? $data['status'] : 0);
	$tva->setRemark(isset($data['remark']) ? $data['remark'] : '');
    $tva->setDateAdd(date("Y-m-d"));
    $tva->setLastEdit(date("Y-m-d"));

    return $tva;
}

// Nom du mois en français, pour le titre de la charge auto-créée (ex: "TVA Juin 2024").
function nomMoisTva($numero)
{
    $noms = array(1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre');
    return isset($noms[$numero]) ? $noms[$numero] : '';
}

// Certaines sociétés déclarent la TVA par trimestre (confirmé sur les vraies déclarations DGI
// de Verse Concept lues depuis Google Drive cette session : en-tête "Déclaration Trimestrielle",
// champ "Période" = numéro du trimestre, pas du mois). On répartit le montant/majoration saisis
// sur les 3 mois du trimestre — une ligne crm_tva par mois — pour que le reste de l'écran
// (cumul par année, alerte "mois manquants") continue de raisonner mois par mois sans code
// spécial. Les 3 lignes ne forment pas un "lot" : chacune reste éditable/supprimable seule.
function ajouterTvaTrimestrielle($data)
{
    $trimestre = intval($data['trimestre']);
    $annee = intval($data['annee']);
    $moisDebut = ($trimestre - 1) * 3 + 1;

    $montantTotal = floatval($data['amount']);
    $majorationTotal = isset($data['increasion']) && $data['increasion'] !== '' ? floatval($data['increasion']) : 0;
    $montantParMois = round($montantTotal / 3, 2);
    $majorationParMois = round($majorationTotal / 3, 2);

    $agenceObj = agence::find($data['id_agence'], $_SESSION['langue']);
    $remarkSaisi = isset($data['remark']) ? $data['remark'] : '';
    $remarkComplet = "Trimestre T$trimestre $annee (montant divisé sur 3 mois)." . ($remarkSaisi !== '' ? ' ' . $remarkSaisi : '');

    // Une seule charge pour le montant total du trimestre (un seul paiement réel en général),
    // partagée par les 3 lignes mensuelles via id_charge.
    $idCharge = creerChargeDepuisPreuvePaiement($data, $montantTotal + $majorationTotal, 'TVA T' . $trimestre . ' ' . $annee, $data['id_agence']);

    $doc = array();
    if (isset($_FILES['doc']) && isset($_FILES['doc']['name'][0]) && $_FILES['doc']['name'][0] != '') {
        $doc = uploadFiles('doc', '../../../images/tva/', array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    }
    $nomDoc = isset($doc[0]) ? $doc[0] : '';
    $datePaiement = isset($data['date_payment']) && !empty($data['date_payment']) ? dateBD($data['date_payment']) : null;
    $statut = isset($data['status']) ? $data['status'] : 0;

    $succes = true;
    for ($i = 0; $i < 3; $i++) {
        $mois = $moisDebut + $i;
        $tva = new tva();
        $tva->setAgence($agenceObj);
        $tva->setAmount($montantParMois);
        $tva->setIncreasion($majorationParMois);
        $tva->setDate(date('Y-m-t', strtotime(sprintf('%04d-%02d-01', $annee, $mois))));
        $tva->setDatePayment($datePaiement);
        $tva->setStatus($statut);
        $tva->setRemark($remarkComplet);
        $tva->setDoc($nomDoc);
        $tva->setIdCharge($idCharge);
        $tva->setDateAdd(date('Y-m-d'));
        $tva->setLastEdit(date('Y-m-d'));

        if ($tva->add() != 1) {
            $succes = false;
        }
    }

    return $succes;
}

// Crée automatiquement une charge fixe (com_charge) à partir de la preuve de paiement jointe
// au formulaire TVA — uniquement si un fichier est réellement fourni (sinon aucune charge
// n'est créée, comme demandé explicitement). tva_deductible=0 et tva_taux=null sont
// impératifs : payer la TVA à l'État n'est pas un achat ouvrant droit à déduction — l'inclure
// fausserait le simulateur TVA (double-comptage circulaire, cf. charge::montantTvaDeductible()).
// Retourne l'id de la charge créée, ou null si aucun fichier/échec.
function creerChargeDepuisPreuvePaiement($data, $montantTotal, $titre, $idAgence)
{
    if (!isset($_FILES['preuve_paiement']) || !isset($_FILES['preuve_paiement']['name'][0]) || $_FILES['preuve_paiement']['name'][0] == '') {
        return null;
    }

    $photo = uploadFiles('preuve_paiement', '../../../images/charges/', array('jpg','jpeg','gif','png','pdf','JPG','JPEG','GIF','PNG','PDF'));
    if (!isset($photo[0])) {
        return null;
    }

    $datePaiement = isset($data['date_payment']) && !empty($data['date_payment']) ? dateBD($data['date_payment']) : date('Y-m-d');

    $charge = new charge();
    $charge->setAgence(agence::find($idAgence, $_SESSION['langue']));
    $charge->setPaidBy($_SESSION['user']);
    $charge->setUser($_SESSION['user']);
    $charge->setType('fixe');
    $charge->setTitre($titre);
    $charge->setDescription('Charge créée automatiquement depuis le paiement de la TVA.');
    $charge->setTotal($montantTotal);
    $charge->setDevise('DH');
    $charge->setTvaTaux(null);
    $charge->setTvaDeductible(0);
    $charge->setPaid(1);
    $charge->setFacture(0);
    $charge->setRefunded(0);
    $charge->setDateCharge($datePaiement);
    $charge->setDatePayment($datePaiement);
    $charge->setModePayment('virement');
    $charge->setPhoto($photo[0]);
    $charge->setDateAdd(date('Y-m-d'));
    $charge->setLastEdit(date('Y-m-d'));

    if ($charge->add() == 1) {
        return charge::getLastId();
    }
    return null;
}