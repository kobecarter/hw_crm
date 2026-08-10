<?php

// Moteur de matching du Rapprochement Bancaire : pour chaque ligne de relevé importée, tente
// d'associer automatiquement les crédits aux factures clients (montant exact + nom du client),
// et reconnaît les débits récurrents (fournisseurs connus) pour préparer une charge. Ne crée
// JAMAIS d'écriture en base ici (ni charge, ni paiement) : simulation pure - même un match de
// crédit non-ambigu (exactement une facture candidate) ne fait que proposer le statut
// "credit_rapproche" ; la matérialisation réelle (payment->add()/checkPayment()) n'a lieu qu'à
// la confirmation explicite de l'aperçu (confirmerReleve()), jamais pendant previewReleve().
class rapprochementMoteur
{
    // Fournisseurs récurrents reconnus sur un libellé de débit -> charge suggérée (titre + type).
    private static $fournisseursReconnus = array(
        'radeema' => array('titre' => 'Facture Radeema (eau/électricité)', 'type' => 'fixe'),
        'iam' => array('titre' => 'Facture IAM (téléphonie/internet)', 'type' => 'fixe'),
        'godaddy' => array('titre' => 'Abonnement GoDaddy', 'type' => 'fixe'),
        '2switch' => array('titre' => 'Hébergement 2Switch', 'type' => 'fixe'),
        'ovh' => array('titre' => 'Hébergement OVH', 'type' => 'fixe'),
        'genious' => array('titre' => 'Achat Genious', 'type' => 'variable'),
        'genius' => array('titre' => 'Achat Genious', 'type' => 'variable'),
        'cnss' => array('titre' => 'Cotisation CNSS', 'type' => 'fixe'),
        'axa' => array('titre' => 'Assurance AXA', 'type' => 'fixe'),
    );

    // Seuil de similarité (similar_text, en %) entre le libellé bancaire et le nom/raison sociale
    // du client - même seuil que le matching employé déjà utilisé pour la dropzone Charges cette
    // session (fuzzy, mais jamais deviné à l'aveugle : sous ce seuil, pas de candidat retenu).
    const SEUIL_SIMILARITE = 45;

    // Modifie $ligne en place (statut, id_payment, donnees_matching) - ne l'enregistre pas,
    // charge à l'appelant (controleur) de faire $ligne->add()/edit() après coup.
    public static function matcherCredit(releveLigne $ligne, $agence)
    {
        $montant = round((float) $ligne->getCredit(), 2);
        if ($montant <= 0) {
            $ligne->setStatut('a_valider');
            return;
        }

        $factures = facture::findAll(-1, false, false, false, false, false, $agence);
        $candidats = array();
        $libelleMin = mb_strtolower((string) $ligne->getLibelle(), 'UTF-8');

        foreach ($factures as $f) {
            $reste = round((float) $f->getReste(), 2);
            if (abs($reste - $montant) > 0.01) {
                continue;
            }
            $client = $f->getClient();
            $nomClient = '';
            if ($client) {
                $nomClient = trim((string) $client->getRaisonSocial()) !== ''
                    ? $client->getRaisonSocial()
                    : trim($client->getPrenom() . ' ' . $client->getNom());
            }
            // Un libellé bancaire mêle souvent le nom du client à du texte de service (VIR,
            // RECU, REGLEMENT, référence...) - un similar_text() brut sur toute la chaîne dilue
            // le score même quand le nom apparaît tel quel. On vérifie donc d'abord une
            // correspondance directe (sous-chaîne), et on ne retombe sur le score flou que si
            // le nom n'apparaît pas littéralement.
            $score = 0;
            if ($nomClient !== '') {
                if (mb_stripos($libelleMin, mb_strtolower($nomClient, 'UTF-8')) !== false) {
                    $score = 100;
                } else {
                    similar_text(mb_strtolower($nomClient, 'UTF-8'), $libelleMin, $score);
                }
            }
            if ($score >= self::SEUIL_SIMILARITE) {
                $candidats[] = array(
                    'id_facture' => $f->getId(),
                    'numero' => $f->getNumero(),
                    'client' => $nomClient,
                    'montant' => $reste,
                    'score' => round($score, 1)
                );
            }
        }

        if (count($candidats) === 1) {
            // Simulation uniquement : aucune écriture ici. C'est confirmerReleve() qui créera le
            // payment + appellera checkPayment() au moment de la validation de l'aperçu.
            $ligne->setStatut('matched_facture');
            $ligne->setDonneesMatchingArray(array('type' => 'credit_rapproche', 'facture' => $candidats[0]));
        } else {
            // 0 ou plusieurs candidats : jamais de choix automatique - validation manuelle requise.
            $ligne->setStatut('a_valider');
            $ligne->setDonneesMatchingArray(array('type' => 'credit_ambigu', 'candidats' => $candidats));
        }
    }

    public static function matcherDebit(releveLigne $ligne, $agence)
    {
        $montant = round((float) $ligne->getDebit(), 2);
        if ($montant <= 0) {
            $ligne->setStatut('a_valider');
            return;
        }
        $libelleMin = mb_strtolower((string) $ligne->getLibelle(), 'UTF-8');

        // Paiement de TVA : ce cas revient à chaque dépôt (mensuel ou trimestriel) et ne doit
        // JAMAIS être rapproché tout seul (contrairement aux fournisseurs récurrents ci-dessous)
        // - la confirmation manuelle de la déclaration exacte est explicitement demandée. La
        // période est détectée à partir de la date de l'opération, avec le même calcul que le
        // widget d'échéance TVA (un paiement en août couvre la période de juillet, en mensuel).
        // Le portail marocain de paiement en ligne des impôts (SIMPL) libelle souvent l'opération
        // "PAIEMENT FACT TAXES EN LIGNE" sur le relevé bancaire, sans jamais écrire "TVA" - repéré
        // ici en confirmé sur un cas réel (relevé BMCE, 03/10/2025, 2605 DH = montant exact de la
        // déclaration TVA du mois correspondant, restée invisible côté relevés faute de ce trigger).
        if (mb_strpos($libelleMin, 'tva') !== false || mb_strpos($libelleMin, 'taxes en ligne') !== false) {
            $agenceObjet = agence::find($agence, isset($_SESSION['langue']) ? $_SESSION['langue'] : 'fr');
            $periodicite = $agenceObjet->getTvaPeriodicite() === 'trimestriel' ? 'trimestriel' : 'mensuel';
            $periode = tva::periodeReference($periodicite, new DateTime($ligne->getDateOperation()), -1);

            $anneePeriode = (int) $periode['debut']->format('Y');
            $declarationsProches = array_merge(
                tva::findByYear($agence, $anneePeriode - 1),
                tva::findByYear($agence, $anneePeriode)
            );

            $candidats = array();
            foreach ($declarationsProches as $decl) {
                $dateDecl = new DateTime($decl->getDate());
                $detectee = ($dateDecl >= $periode['debut'] && $dateDecl <= $periode['fin']);
                $candidats[] = array(
                    'id' => $decl->getId(),
                    'periode_libelle' => tva::libellePeriode($dateDecl, 'mensuel'),
                    'montant' => (float) $decl->getAmount(),
                    'deja_depose' => $decl->getStatus() == 1,
                    'detecte' => $detectee
                );
            }
            // Les plus proches de la période détectée en premier, pour une liste lisible dans la modale.
            usort($candidats, function ($a, $b) {
                return $b['detecte'] <=> $a['detecte'];
            });

            $ligne->setStatut('a_valider');
            $ligne->setDonneesMatchingArray(array(
                'type' => 'debit_tva',
                'periode_detectee' => $periode['libelle'],
                'candidats' => $candidats
            ));
            return;
        }

        // Commission bancaire : ce cas se répète à chaque relevé et se règle toujours de la même
        // façon (une charge agrégée avec le relevé lui-même comme justificatif, TVA récupérable
        // recalculée) - repéré ici en amont de toute autre recherche pour que ces lignes soient
        // isolées et regroupées par previewReleve() plutôt que traitées une par une. Certaines
        // banques scindent la commission en deux lignes du relevé (la commission HT, puis sa TVA
        // sur une ligne "TAXE SUR VALEUR AJOUTEE" séparée, souvent sans accent) plutôt qu'un
        // montant TTC unique - un petit montant sur ce libellé rejoint donc le même agrégat plutôt
        // que de rester sans justificatif (une vraie déclaration trimestrielle, elle, est repérée
        // juste au-dessus via le mot "tva" et n'atteint jamais ce point avec un montant aussi bas).
        $libelleSansAccents = strtr($libelleMin, array('é' => 'e', 'è' => 'e', 'ê' => 'e', 'à' => 'a', 'ô' => 'o', 'û' => 'u'));
        $estTvaDeCommissionScindee = mb_strpos($libelleSansAccents, 'taxe sur valeur ajoutee') !== false && $montant < 500;
        if (mb_strpos($libelleMin, 'commission') !== false || $estTvaDeCommissionScindee) {
            $ligne->setStatut('a_valider');
            $ligne->setDonneesMatchingArray(array('type' => 'debit_commission', 'montant' => $montant));
            return;
        }

        // Avant de suggérer une NOUVELLE charge (fournisseur reconnu) ou de crier au débit sans
        // justificatif, on regarde si une charge existe déjà (saisie manuellement, ou par une
        // autre voie) pour ce montant à une date proche - le justificatif existe peut-être déjà
        // dans la liste des charges, il suffit de le lier plutôt que d'en créer un doublon.
        // Jamais de lien automatique ici non plus : toujours une validation manuelle.
        $dateOperation = new DateTime($ligne->getDateOperation());
        $bornesDebut = (clone $dateOperation)->modify('-5 days')->format('Y-m-d');
        $bornesFin = (clone $dateOperation)->modify('+5 days')->format('Y-m-d');
        $idsDejaLies = releveLigne::findAllIdChargeLies();

        // charge::findAllByDate() renvoie des lignes associatives brutes (pas des objets charge)
        // - accès par clé plutôt que par getter, en conséquence.
        $candidatsCharges = array();
        foreach (charge::findAllByDate($bornesDebut, $bornesFin, $agence) as $c) {
            if ($c['devise'] != 'DH' || in_array($c['id'], $idsDejaLies)) {
                continue;
            }
            if (abs(round((float) $c['total'], 2) - $montant) > 0.01) {
                continue;
            }
            $candidatsCharges[] = array(
                'id' => $c['id'],
                'titre' => $c['titre'],
                'montant' => (float) $c['total'],
                'date' => $c['date_charge']
            );
        }
        if (!empty($candidatsCharges)) {
            $ligne->setStatut('a_valider');
            $ligne->setDonneesMatchingArray(array('type' => 'debit_charge_existante', 'candidats' => $candidatsCharges));
            return;
        }

        foreach (self::$fournisseursReconnus as $motCle => $suggestion) {
            if (mb_strpos($libelleMin, $motCle) !== false) {
                $ligne->setStatut('a_valider');
                $ligne->setDonneesMatchingArray(array(
                    'type' => 'debit_reconnu',
                    'titre' => $suggestion['titre'],
                    'charge_type' => $suggestion['type'],
                    'montant' => $montant,
                    'date_charge' => $ligne->getDateOperation()
                ));
                return;
            }
        }

        // Débit non reconnu, et aucune charge existante ne correspond : l'alerte rouge du spec
        // ("Débit détecté de X DH le [Date] - Facture manquante") - aucune création, l'utilisateur
        // doit fournir le justificatif. Un virement vers un particulier est souvent un salaire :
        // on suggère un employé (jamais lié automatiquement) pour accélérer la fenêtre de
        // justificatif manuel, qui proposera alors de créer un bulletin de paie plutôt qu'une
        // charge générique.
        $ligne->setStatut('sans_justificatif');
        $ligne->setDonneesMatchingArray(array('type' => 'debit_inconnu', 'employe_suggere' => self::matcherEmploye($ligne->getLibelle())));
    }

    // Suggestion d'employé à partir d'un libellé bancaire bruyant ("VIR. INSTANTANE EN FAVEUR DE
    // BEN DEBBANE HIBAT ALLAH REF ... DU ..."). Un simple stripos("prénom nom") rate la plupart des
    // virements réels : la banque écrit souvent "Nom Prénom" (ordre inverse du dossier RH), et les
    // noms composés tantôt en un seul mot côté RH ("BENDEBBANE") tantôt avec espace/tiret côté
    // relevé ("BEN DEBBANE") - on compare donc les deux noms réduits à leurs seules lettres, dans
    // les deux ordres de concaténation possibles, plutôt qu'une sous-chaîne figée. Le score flou
    // (similar_text) ne sert plus que de filet pour les fautes de frappe : sur un libellé long, son
    // pourcentage reste structurellement bas même face à un nom présent en clair, donc ne pas s'y
    // fier seul serait passer à côté de la quasi-totalité des cas réels. Ne renvoie qu'une
    // SUGGESTION - jamais de liaison automatique.
    public static function matcherEmploye($libelle)
    {
        $libelleNormalise = self::normaliserPourComparaisonNom($libelle);
        $libelleMin = mb_strtolower((string) $libelle, 'UTF-8');
        $seuilSuggestion = 65;
        $meilleurScore = 0;
        $meilleurEmploye = null;

        foreach (resourcehumaine::findAll() as $employe) {
            $prenom = trim((string) $employe->getFirstName());
            $nom = trim((string) $employe->getLastName());
            if ($prenom === '' && $nom === '') {
                continue;
            }

            $concat1 = self::normaliserPourComparaisonNom($prenom . $nom);
            $concat2 = self::normaliserPourComparaisonNom($nom . $prenom);
            if ($libelleNormalise !== '' && (
                (mb_strlen($concat1) >= 4 && mb_strpos($libelleNormalise, $concat1) !== false)
                || (mb_strlen($concat2) >= 4 && mb_strpos($libelleNormalise, $concat2) !== false)
            )) {
                return array('id' => $employe->getId(), 'nom_complet' => $employe->getFullName(), 'score' => 100.0);
            }

            $nomComplet = trim(mb_strtolower($prenom . ' ' . $nom, 'UTF-8'));
            $score = 0;
            similar_text($nomComplet, $libelleMin, $score);
            if ($score > $meilleurScore) {
                $meilleurScore = $score;
                $meilleurEmploye = $employe;
            }
        }

        if ($meilleurEmploye && $meilleurScore >= $seuilSuggestion) {
            return array('id' => $meilleurEmploye->getId(), 'nom_complet' => $meilleurEmploye->getFullName(), 'score' => round($meilleurScore, 1));
        }
        return null;
    }

    // Lettres uniquement, en majuscules, sans accents - pour comparer un nom de la base RH à un
    // libellé bancaire sans se soucier des espaces, tirets ou accents qui diffèrent d'une source à
    // l'autre.
    private static function normaliserPourComparaisonNom($texte)
    {
        $texte = mb_strtoupper((string) $texte, 'UTF-8');
        $texte = strtr($texte, array('É' => 'E', 'È' => 'E', 'Ê' => 'E', 'À' => 'A', 'Ô' => 'O', 'Û' => 'U', 'Ç' => 'C'));
        return preg_replace('/[^A-Z]/', '', $texte);
    }

    // Regroupe les lignes "commission" détectées dans un lot en un seul agrégat HT/TVA/Total, sur
    // la base du taux de TVA général de l'agence - point de départ éditable côté UI avant
    // confirmation (le taux réel appliqué par la banque sur les commissions n'est pas forcément le
    // taux standard de l'agence, ex. 10% observé en pratique contre 20% standard - jamais imposé
    // silencieusement).
    public static function agregerCommissions($lignesCommission, $agence)
    {
        $total = 0;
        foreach ($lignesCommission as $ligne) {
            $total += round((float) $ligne->getDebit(), 2);
        }
        $total = round($total, 2);

        $agenceObjet = agence::find($agence, isset($_SESSION['langue']) ? $_SESSION['langue'] : 'fr');
        $taux = (float) $agenceObjet->getTva();
        if ($taux <= 0) {
            $taux = 20;
        }

        $ht = $taux > 0 ? round($total / (1 + $taux / 100), 2) : $total;
        $tva = round($total - $ht, 2);

        return array(
            'total' => $total,
            'ht' => $ht,
            'tva' => $tva,
            'taux' => $taux,
            'nb_lignes' => count($lignesCommission)
        );
    }
}
