<?php

class releveLigne
{
    static $table = __prefixe_db__ . "releve_ligne";

    private $id;
    private $agence;
    private $bank;
    private $lot_import;
    private $date_operation;
    private $libelle;
    private $debit;
    private $credit;
    private $statut;
    private $id_payment;
    private $id_charge;
    private $id_tva;
    private $donnees_matching;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAgence()
    {
        return $this->agence;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function getLotImport()
    {
        return $this->lot_import;
    }

    public function getDateOperation()
    {
        return $this->date_operation;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function getDebit()
    {
        return $this->debit;
    }

    public function getCredit()
    {
        return $this->credit;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function getIdPayment()
    {
        return $this->id_payment;
    }

    public function getIdCharge()
    {
        return $this->id_charge;
    }

    public function getIdTva()
    {
        return $this->id_tva;
    }

    public function getDonneesMatching()
    {
        return $this->donnees_matching;
    }

    // Décodage pratique du JSON stocké (candidats factures ambigus pour un crédit, ou champs de
    // charge suggérés pour un débit reconnu) - jamais manipulé en JSON brut en dehors de ce fichier
    // et du moteur de matching.
    public function getDonneesMatchingArray()
    {
        $decoded = json_decode((string) $this->donnees_matching, true);
        return is_array($decoded) ? $decoded : array();
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAgence($agence)
    {
        $this->agence = $agence;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function setLotImport($lot_import)
    {
        $this->lot_import = $lot_import;
    }

    public function setDateOperation($date_operation)
    {
        $this->date_operation = $date_operation;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    public function setDebit($debit)
    {
        $this->debit = $debit;
    }

    public function setCredit($credit)
    {
        $this->credit = $credit;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function setIdPayment($id_payment)
    {
        $this->id_payment = $id_payment;
    }

    public function setIdCharge($id_charge)
    {
        $this->id_charge = $id_charge;
    }

    public function setIdTva($id_tva)
    {
        $this->id_tva = $id_tva;
    }

    public function setDonneesMatching($donnees_matching)
    {
        $this->donnees_matching = $donnees_matching;
    }

    public function setDonneesMatchingArray($tableau)
    {
        $this->donnees_matching = json_encode($tableau);
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence, id_bank, lot_import, date_operation, libelle, debit, credit, statut, id_payment, id_charge, id_tva, donnees_matching, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence ? $this->agence->getId() : null, "int"),
            GetSQLValueString($this->bank ? $this->bank->getId() : null, "int"),
            GetSQLValueString($this->lot_import, "text"),
            GetSQLValueString($this->date_operation, "date"),
            GetSQLValueString($this->libelle, "text"),
            GetSQLValueString($this->debit, "double"),
            GetSQLValueString($this->credit, "double"),
            GetSQLValueString($this->statut, "text"),
            GetSQLValueString($this->id_payment, "int"),
            GetSQLValueString($this->id_charge, "int"),
            GetSQLValueString($this->id_tva, "int"),
            GetSQLValueString($this->donnees_matching, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)) {
            return 1;
        }
        return 0;
    }

    // Seuls les champs qui évoluent après l'import (au moment de la validation/ignorance d'une
    // ligne) sont mis à jour - les champs importés (date/libellé/montants/compte) sont immuables.
    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET statut = %s, id_payment = %s, id_charge = %s, id_tva = %s, donnees_matching = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->statut, "text"),
            GetSQLValueString($this->id_payment, "int"),
            GetSQLValueString($this->id_charge, "int"),
            GetSQLValueString($this->id_tva, "int"),
            GetSQLValueString($this->donnees_matching, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        }
        return 0;
    }

    public static function find($id)
    {
        global $db;
        $ligne = new releveLigne();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $ligne = static::build($data);
        }
        return $ligne;
    }

    public static function findAll($agence, $lot = null)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id_agence = %s",
            GetSQLValueString($agence, "int")
        );
        if ($lot !== null) {
            $SQLselect .= sprintf(" AND lot_import = %s", GetSQLValueString($lot, "text"));
        }
        $SQLselect .= " ORDER BY date_operation DESC, id DESC";
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    // Compteurs par statut pour un lot donné - alimente le résumé affiché sur chaque carte de la
    // liste des lots (une ligne SQL groupée plutôt que de charger tout le lot pour compter en PHP).
    public static function compterParLot($lotImport)
    {
        global $db;
        $compteurs = array('matched_facture' => 0, 'matched_charge' => 0, 'matched_tva' => 0, 'a_valider' => 0, 'sans_justificatif' => 0, 'ignore' => 0);
        $SQLselect = sprintf("SELECT statut, COUNT(*) as nb FROM " . static::$table . " WHERE lot_import = %s GROUP BY statut",
            GetSQLValueString($lotImport, "text")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $row) {
            $compteurs[$row['statut']] = (int) $row['nb'];
        }
        return $compteurs;
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLdelete)) {
            return 1;
        }
        return 0;
    }

    // Supprime toutes les lignes d'un lot en une seule requête - utilisé par la suppression d'un
    // import entier (annulation complète), après que l'appelant a déjà défait les écritures liées
    // (paiements, charges, TVA) ligne par ligne.
    public static function deleteByLot($lotImport)
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE lot_import = %s",
            GetSQLValueString($lotImport, "text")
        );
        return $db->query($SQLdelete);
    }

    // Charges déjà liées à une ligne de relevé (peu importe l'agence) - utilisé par le moteur de
    // matching pour ne jamais proposer deux fois la même charge existante comme justificatif
    // d'un débit (même patron que payslip::findAllIdChargeLies() déjà utilisé côté Charges).
    public static function findAllIdChargeLies()
    {
        global $db;
        $ids = array();
        $result = $db->queryS("SELECT id_charge FROM " . static::$table . " WHERE id_charge IS NOT NULL");
        foreach ($result as $row) {
            $ids[] = $row['id_charge'];
        }
        return $ids;
    }

    // Sécurité anti-doublon (réaffectation) : cette charge est-elle déjà rattachée à une AUTRE
    // ligne de relevé ? Utilisé avant de lier une charge/un bulletin existant à une nouvelle ligne
    // (validerLigne(), creerJustificatifManuel()) pour bloquer et demander confirmation plutôt que
    // de laisser une même charge se retrouver rattachée à deux virements différents en silence.
    public static function findLigneParCharge($idCharge, $excludeLigneId = null)
    {
        global $db;
        if (empty($idCharge)) {
            return null;
        }
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_charge = %s AND statut = 'matched_charge'",
            GetSQLValueString($idCharge, "int")
        );
        if (!empty($excludeLigneId)) {
            $SQLselect .= sprintf(" AND id != %s", GetSQLValueString($excludeLigneId, "int"));
        }
        $SQLselect .= " LIMIT 1";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return static::build($data);
        }
        return null;
    }

    // Règlements déjà liés à une ligne de relevé (peu importe l'agence) - même patron que
    // findAllIdChargeLies() ci-dessus, pour ne jamais proposer deux fois le même règlement
    // existant du client comme justificatif d'un crédit (fenêtre "Choisir la facture").
    public static function findAllIdPaymentLies()
    {
        global $db;
        $ids = array();
        $result = $db->queryS("SELECT id_payment FROM " . static::$table . " WHERE id_payment IS NOT NULL");
        foreach ($result as $row) {
            $ids[] = $row['id_payment'];
        }
        return $ids;
    }

    // Sécurité anti-doublon (réaffectation) : ce règlement est-il déjà rattaché à une AUTRE ligne
    // de relevé ? Même patron que findLigneParCharge() ci-dessus, utilisé avant de lier un
    // règlement existant du client à une nouvelle ligne (validerLigne()).
    public static function findLigneParPayment($idPayment, $excludeLigneId = null)
    {
        global $db;
        if (empty($idPayment)) {
            return null;
        }
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_payment = %s AND statut = 'matched_facture'",
            GetSQLValueString($idPayment, "int")
        );
        if (!empty($excludeLigneId)) {
            $SQLselect .= sprintf(" AND id != %s", GetSQLValueString($excludeLigneId, "int"));
        }
        $SQLselect .= " LIMIT 1";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return static::build($data);
        }
        return null;
    }

    // Identifie le lot le plus récemment importé pour une agence - utilisé pour afficher par
    // défaut le résultat du dernier import à l'ouverture de la page.
    public static function findDernierLot($agence)
    {
        global $db;
        $SQLselect = sprintf("SELECT lot_import FROM " . static::$table . " WHERE id_agence = %s ORDER BY id DESC LIMIT 1",
            GetSQLValueString($agence, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data['lot_import'];
        }
        return null;
    }

    // Trouve les lignes strictement identiques (même compte, même date, même libellé, même
    // débit/crédit) déjà importées dans un lot précédent - protège previewReleve()/
    // confirmerReleve() contre la ré-importation d'un relevé déjà traité (rien n'empêchait
    // jusqu'ici de redéposer deux fois le même fichier : lot_import est un uniqid() généré à
    // chaque aperçu, il ne peut jamais collisionner avec un import antérieur). Retourne les objets
    // complets (pas juste un booléen) car confirmerReleve() en a besoin pour les défaire proprement
    // quand l'utilisateur choisit d'écraser l'ancien import plutôt que de le garder.
    public static function trouverDoublons($idBank, $dateOperation, $debit, $credit, $libelle)
    {
        global $db;
        $items = array();
        if (empty($idBank) || empty($dateOperation)) {
            return $items;
        }
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_bank = %s AND date_operation = %s AND libelle = %s AND IFNULL(debit,0) = %s AND IFNULL(credit,0) = %s",
            GetSQLValueString($idBank, "int"),
            GetSQLValueString($dateOperation, "date"),
            GetSQLValueString($libelle, "text"),
            GetSQLValueString(!empty($debit) ? $debit : 0, "double"),
            GetSQLValueString(!empty($credit) ? $credit : 0, "double")
        );
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    // Utilisé par le "check" affiché sous la dropzone : ce compte a-t-il déjà reçu au moins un
    // relevé importé (peu importe le lot) ? Sert uniquement à colorer la checklist en vert/rouge.
    public static function existePourBank($idBank)
    {
        global $db;
        $SQLselect = sprintf("SELECT id FROM " . static::$table . " WHERE id_bank = %s LIMIT 1",
            GetSQLValueString($idBank, "int")
        );
        $result = $db->query($SQLselect);
        return $db->num_rows($result) > 0;
    }

    public static function build($data)
    {
        $ligne = new releveLigne();
        $ligne->setId($data['id']);
        if (!empty($data['id_agence'])) {
            $ligne->setAgence(agence::find($data['id_agence'], isset($_SESSION['langue']) ? $_SESSION['langue'] : 'fr'));
        }
        if (!empty($data['id_bank'])) {
            $ligne->setBank(bank::find($data['id_bank']));
        }
        $ligne->setLotImport($data['lot_import']);
        $ligne->setDateOperation($data['date_operation']);
        $ligne->setLibelle($data['libelle']);
        $ligne->setDebit($data['debit']);
        $ligne->setCredit($data['credit']);
        $ligne->setStatut($data['statut']);
        $ligne->setIdPayment(isset($data['id_payment']) ? $data['id_payment'] : null);
        $ligne->setIdCharge(isset($data['id_charge']) ? $data['id_charge'] : null);
        $ligne->setIdTva(isset($data['id_tva']) ? $data['id_tva'] : null);
        $ligne->setDonneesMatching(isset($data['donnees_matching']) ? $data['donnees_matching'] : null);
        $ligne->setDateAdd($data['date_add']);
        $ligne->setLastEdit($data['last_edit']);
        return $ligne;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }
}
