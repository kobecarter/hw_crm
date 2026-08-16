<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addBonusResourceHumaine':
            addBonusResourceHumaine($_POST);
            break;
        case 'editBonusResourceHumaine':
            editBonusResourceHumaine($_POST);
            break;
        case 'deleteBonusResourceHumaine':
            deleteBonusResourceHumaine($_POST);
            break;
        case 'recalculateBonus':
            recalculateBonus($_POST);
            break;
    }
}

// Marqueur stable en tête du champ "remark" du bonus auto-calculé, un par scope ('all' ou une
// année en cours au moment du calcul) - permet de retrouver/mettre à jour CE bonus précis lors
// d'un recalcul suivant plutôt que d'empiler un nouveau bonus à chaque clic.
function marqueurBonusAuto($scope)
{
    return '[BONUS_AUTO:' . $scope . ']';
}

// Compare le total réellement reçu (net à payer extrait par IA de chaque bulletin de paie) au
// salaire déclaré sur la fiche employé (salaire_actuel, seule référence disponible - il n'y a
// pas d'historique de salaire dans ce CRM, donc le même salaire déclaré sert de référence pour
// tous les mois du scope). L'écart positif (reçu > déclaré) est enregistré comme un bonus ;
// jamais négatif (un salaire reçu inférieur au déclaré est une anomalie de paie à traiter
// manuellement, pas un "bonus négatif").
function recalculateBonus($data)
{
    if (!isset($data['id']) || empty($data['id']) || !$_SESSION['user']->hasDroit('edit', 'com_resourcehumaine')) {
        echo json_encode(array('success' => 0, 'message' => 'Accès refusé.'));
        return;
    }

    $resourcehumaine = resourcehumaine::find($data['id']);
    if ($resourcehumaine->getId() == 0 || !$resourcehumaine->getAgency() || $resourcehumaine->getAgency()->getId() != $_SESSION['agence']) {
        echo json_encode(array('success' => 0, 'message' => 'Employé introuvable.'));
        return;
    }

    $scope = (isset($data['scope']) && $data['scope'] === 'year') ? 'year' : 'all';
    $anneeEnCours = date('Y');

    $payslips = payslip::findAllByResourcehumaine($resourcehumaine->getId());
    if ($scope === 'year') {
        $payslips = array_values(array_filter($payslips, function ($p) use ($anneeEnCours) {
            return $p->getDate() && date('Y', strtotime($p->getDate())) == $anneeEnCours;
        }));
    }

    if (empty($payslips)) {
        echo json_encode(array('success' => 0, 'message' => 'Aucun bulletin de paie trouvé pour cette période.'));
        return;
    }

    // Beaucoup de bulletins jamais encore extraits = beaucoup d'appels IA séquentiels (quelques
    // secondes chacun) - le défaut de 30s de PHP couperait le calcul en plein milieu.
    set_time_limit(300);

    $dossierPayslips = __DIR__ . '/../../../../images/resourceshumaines/payslips/';
    $totalRecu = 0;
    $nbExtraits = 0;
    $nbEchecs = 0;
    foreach ($payslips as $unBulletin) {
        if ($unBulletin->getAmount() === null) {
            $cheminFichier = $dossierPayslips . $unBulletin->getFile();
            if (!file_exists($cheminFichier)) {
                $nbEchecs++;
                continue;
            }
            try {
                $extension = strtolower(pathinfo($cheminFichier, PATHINFO_EXTENSION));
                $extrait = aiExtractor::extractPayslipAmount($cheminFichier, $extension);
                $montant = isset($extrait['montant']) ? floatval(str_replace(array(' ', ','), array('', '.'), $extrait['montant'])) : 0;
                if ($montant > 0) {
                    $unBulletin->setAmount($montant);
                    $unBulletin->edit();
                    $nbExtraits++;
                } else {
                    $nbEchecs++;
                    continue;
                }
            } catch (Exception $e) {
                error_log('recalculateBonus - échec extraction bulletin #' . $unBulletin->getId() . ': ' . $e->getMessage());
                $nbEchecs++;
                continue;
            }
        }
        $totalRecu += $unBulletin->getAmount();
    }

    $nbBulletinsComptes = count($payslips) - $nbEchecs;
    if ($nbBulletinsComptes <= 0) {
        echo json_encode(array('success' => 0, 'message' => 'Aucun montant n\'a pu être extrait des bulletins de paie de cette période.'));
        return;
    }
    $salaireDeclare = floatval($resourcehumaine->getSalaireActuel());
    if ($salaireDeclare <= 0) {
        echo json_encode(array('success' => 0, 'message' => 'Aucun salaire actuel déclaré sur la fiche employé - impossible de comparer.'));
        return;
    }
    $totalDeclare = $salaireDeclare * $nbBulletinsComptes;
    $bonusCalcule = max(0, round($totalRecu - $totalDeclare, 2));

    // Upsert : cherche un bonus déjà marqué pour ce même scope (posé par un recalcul
    // précédent) et le met à jour, plutôt que d'empiler un nouveau bonus à chaque clic.
    global $db;
    $marqueur = marqueurBonusAuto($scope);
    $SQLselect = sprintf("SELECT id FROM " . __prefixe_db__ . "bonus WHERE id_resourcehumaine = %s AND remark LIKE %s LIMIT 1",
        GetSQLValueString($resourcehumaine->getId(), "int"),
        GetSQLValueString($marqueur . '%', "text")
    );
    $result = $db->queryS($SQLselect);

    $bonus = !empty($result) ? bonus::find($result[0]['id']) : new bonus();
    $bonus->setResourcehumaine($resourcehumaine);
    $bonus->setAmount($bonusCalcule);
    $bonus->setDate(date('Y-m-d'));
    if ($bonus->getStatus() === null) {
        $bonus->setStatus(0);
    }
    $libelleScope = $scope === 'year' ? 'année ' . $anneeEnCours : 'depuis le début';
    $bonus->setRemark($marqueur . ' Calculé automatiquement (' . $libelleScope . ') à partir de ' . $nbBulletinsComptes . ' bulletin(s) de paie : ' . number_format($totalRecu, 2, ',', ' ') . ' MAD reçus vs ' . number_format($totalDeclare, 2, ',', ' ') . ' MAD déclarés (salaire actuel × nombre de bulletins).');
    $bonus->setDateAdd(date('Y-m-d'));
    $bonus->setLastEdit(date('Y-m-d'));
    $bonus->getId() ? $bonus->edit() : $bonus->add();

    echo json_encode(array(
        'success' => 1,
        'scope' => $scope,
        'nb_bulletins' => $nbBulletinsComptes,
        'nb_extraits' => $nbExtraits,
        'nb_echecs' => $nbEchecs,
        'total_recu' => $totalRecu,
        'total_declare' => $totalDeclare,
        'bonus' => $bonusCalcule,
    ));
}

function addBonusResourceHumaine($data)
{
    // echo json_encode($data);
    // die();
    $indices = array("id_resourcehumaine", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildBonusResourceHumaine($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editBonusResourceHumaine($data)
{
    $indices = array("id_bonus", "id_resourcehumaine", "amount", "date");
    if (fieldCheck($data, $indices)) {
        if (buildBonusResourceHumaine($data, $data['id_bonus'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteBonusResourceHumaine($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        // find() + vérification manuelle de l'agence via l'employé lié (bonus::find($id) ne prend
        // pas d'agence en paramètre) - sans ce contrôle, n'importe quel id de prime valide, même
        // d'une autre agence, se faisait supprimer (IDOR).
        $bonus = bonus::find($data['id']);
        $employeBonus = $bonus->getResourcehumaine();
        if ($bonus->getId() == 0 || !$employeBonus || !$employeBonus->getAgency() || $employeBonus->getAgency()->getId() != $_SESSION['agence']) {
            echo "2";
            return;
        }
        if ($bonus->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildBonusResourceHumaine($data, $id = null)
{
    $bonus = new bonus();

    if($id){
        $bonus = bonus::find($id);
    }

	$bonus->setResourcehumaine(resourcehumaine::find($data['id_resourcehumaine']));
    $bonus->setAmount($data['amount']);
    $bonus->setDate(date("Y-m-t",strtotime($data['date']."-1")));
    $bonus->setStatus($data['status']);
	$bonus->setRemark($data['remark']);
    $bonus->setDateAdd(date("Y-m-d"));
    $bonus->setLastEdit(date("Y-m-d"));

    return $bonus;
}