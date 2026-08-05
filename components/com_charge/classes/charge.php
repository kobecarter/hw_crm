<?php

class charge
{
    static $table =  __prefixe_db__ . "charge";
    static $tableAgence =  __prefixe_db__ . "agence";

    private $id;
    private $agence;
    private $paid_by;
    private $user;
	private $type;
    private $titre;
    private $description;
    private $total;
	private $devise;
	private $tva_taux;
	private $tva_deductible;
    private $paid;
	private $facture;
	private $refunded;
    private $date_charge;
	private $date_payment;
	private $mode_payment;
	private $photo;
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
    
    public function getPaidBy()
    {
        return $this->paid_by;
    }

    public function getUser()
    {
        return $this->user;
    }
	
	public function getType()
    {
        return $this->type;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getTotal()
    {
        return $this->total;
    }
	
	public function getDevise()
    {
        return $this->devise;
    }

	public function getTvaTaux()
    {
        return $this->tva_taux;
    }

	public function getTvaDeductible()
    {
        return $this->tva_deductible;
    }

	public function isTvaDeductible()
    {
        return $this->tva_deductible == 1;
    }

    public function getPaid()
    {
        return $this->paid;
    }
	
	public function getFacture()
    {
        return $this->facture;
    }
    
    public function getRefunded()
    {
        return $this->refunded;
    }
	
	public function isPaid()
    {
        return $this->paid == 1 ? true : false;
    }
    
    public function isRefunded()
    {
        return $this->refunded == 1 ? true : false;
    }
	
	public function hasFacture()
    {
        return $this->facture == 1 ? true : false;
    }

    public function getDatePayment()
    {
        return $this->date_payment;
    }
	
	public function getModePayment()
    {
        return $this->mode_payment;
    }
	
	public function getDateCharge()
    {
        return $this->date_charge;
    }
	
	public function getPhoto()
    {
        return $this->photo;
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
    
    public function setPaidBy($paid_by)
    {
        $this->paid_by = $paid_by;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
	
	public function setType($type)
    {
        $this->type = $type;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }
	
	public function setDevise($devise)
    {
        $this->devise = $devise;
    }

	public function setTvaTaux($tva_taux)
    {
        $this->tva_taux = $tva_taux;
    }

	public function setTvaDeductible($tva_deductible)
    {
        $this->tva_deductible = $tva_deductible;
    }

    public function setPaid($paid)
    {
        $this->paid = $paid;
    }
	
	public function setFacture($facture)
    {
        $this->facture = $facture;
    }
    
    public function setRefunded($refunded)
    {
        $this->refunded = $refunded;
    }

    public function setDatePayment($date_payment)
    {
        $this->date_payment = $date_payment;
    }
	
	public function setModePayment($mode_payment)
    {
        $this->mode_payment = $mode_payment;
    }
	
	public function setDateCharge($date_charge)
    {
        $this->date_charge = $date_charge;
    }
	
	public function setPhoto($photo)
    {
        $this->photo = $photo;
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
        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_agence,paid_by,id_user,type, titre, description, total, devise, tva_taux, tva_deductible, paid, facture, refunded, date_charge, date_payment, mode_payment, photo, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->paid_by->getId(), "int"),
            GetSQLValueString($this->user->getId(), "int"),
            GetSQLValueString($this->type, "text"),
			GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->total, "double"),
			GetSQLValueString($this->devise, "text"),
			GetSQLValueString($this->tva_taux, "double"),
			GetSQLValueString($this->tva_deductible, "int"),
			GetSQLValueString($this->paid, "int"),
			GetSQLValueString($this->facture, "int"),
			GetSQLValueString($this->refunded, "int"),
			GetSQLValueString($this->date_charge, "date"),
			GetSQLValueString($this->date_payment, "date"),	
			GetSQLValueString($this->mode_payment, "text"),	
			GetSQLValueString($this->photo, "text"),					 
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        if (!$db->query($SQLinsert)) {
			return 1;
        } else {
            return 2;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET id_agence = %s, paid_by = %s, id_user = %s, type = %s, titre = %s, description = %s, total = %s, devise = %s, tva_taux = %s, tva_deductible = %s, paid = %s, facture = %s, refunded = %s, date_charge = %s, date_payment = %s, mode_payment = %s, photo = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->agence->getId(), "int"),
            GetSQLValueString($this->paid_by->getId(), "int"),
            GetSQLValueString($this->user->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->titre, "text"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->total, "double"),
			GetSQLValueString($this->devise, "text"),
			GetSQLValueString($this->tva_taux, "double"),
			GetSQLValueString($this->tva_deductible, "int"),
			GetSQLValueString($this->paid, "int"),
			GetSQLValueString($this->facture, "int"),
			GetSQLValueString($this->refunded, "int"),
			GetSQLValueString($this->date_charge, "date"),				 
			GetSQLValueString($this->date_payment, "date"),
			GetSQLValueString($this->mode_payment, "text"),
			GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
			return 1;
        } else {
            return 0;
        }
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf("DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id,$agence=1)
    {
        global $db;
        $charge = new charge();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id WHERE B.id = %s and A.ID = %s",
            GetSQLValueString($agence, "int"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $charge = static::build($data);
        }
        return $charge;
    }

    public static function findAll($ordre = false,$agence=1)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id where B.id = %s",
            GetSQLValueString($agence, "int")
        );

        if($ordre){
            $SQLselect .= " ORDER BY A.date_charge DESC";
        }
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $charge = static::build($data);
            array_push($items, $charge);
        }
        return $items;
    }

    // Recherche globale (bandeau de recherche du bandeau haut) : titre de charge uniquement -
    // utilisé par com_search, jamais par le listing standard des charges.
    public static function search($terme, $agence = false)
    {
        global $db;
        $items = array();
        $like = GetSQLValueString('%' . $terme . '%', 'text');
        $SQLselect = "SELECT id AS ID, " . static::$table . ".* FROM " . static::$table . " WHERE 1=1" . ($agence ? " AND id_agence = " . intval($agence) : "") . " AND titre LIKE $like ORDER BY id DESC LIMIT 8";
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function build($data){
        $charge = new charge();
        $charge->setId($data['ID']);
        $charge->setAgence(agence::find($data['id_agence'],$_SESSION['langue']));
        $charge->setPaidBy(user::find($data['paid_by']));
        $charge->setUser(user::find($data['id_user']));
		$charge->setType($data['type']);
        $charge->setTitre($data['titre']);
        $charge->setDescription($data['description']);
        $charge->setTotal($data['total']);
		$charge->setDevise($data['devise']);
		$charge->setTvaTaux(isset($data['tva_taux']) ? $data['tva_taux'] : null);
		$charge->setTvaDeductible(isset($data['tva_deductible']) ? $data['tva_deductible'] : null);
        $charge->setPaid($data['paid']);
		$charge->setFacture($data['facture']);
		$charge->setRefunded($data['refunded']);
        $charge->setDateCharge($data['date_charge']);
		$charge->setDatePayment($data['date_payment']);
		$charge->setModePayment($data['mode_payment']);
		$charge->setPhoto($data['photo']);
        $charge->setDateAdd($data['date_add']);
        $charge->setLastEdit($data['last_edit']);
        return $charge;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    public static function count($agence = 1){
        global $db;
        $SQLcount = "SELECT count(A.id) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE B.id = $agence";
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }
	
	public static function total($year = false, $month = false, $agence = 1)
    {
        global $db;
        $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE B.id = $agence AND A.paid = 1";

        if ($year) {
            $SQLcount .= " AND YEAR(A.date_payment) = " . intval($year);
        }

        if ($month) {
            $SQLcount .= " AND MONTH(A.date_payment) = " . intval($month);
        }
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return intval($data["c"]);
        }
        return 0;
    }
    
    public static function getCharge($from = false, $to = false,$agence = 1, $devise = false)
    {
        global $db;
        if($agence)
            $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE B.id = $agence AND A.paid = 1";
        else
            $SQLcount = "SELECT SUM(A.total) as c FROM " . static::$table . " A inner join " . static::$tableAgence . " B on A.id_agence = B.id WHERE A.paid = 1";

        if ($from) {
            $SQLcount .= " AND A.date_payment >= '$from'";
        }

        if ($to) {
            $SQLcount .= " AND A.date_payment <= '$to'";
        }
        
        if ($devise) {
            $SQLcount .= " AND A.devise = '$devise'";
        }

        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return intval($data["c"]);
        }
        return 0;
    }

    // Montant de TVA récupérable sur les charges payées et marquées déductibles pour la
    // période : chaque charge peut avoir un taux différent (7/10/20), donc pas sommable en
    // un seul SUM SQL — on additionne "total - total/(1+taux/100)" ligne par ligne en PHP.
    public static function montantTvaDeductible($from, $to, $agence = 1, $devise = 'DH')
    {
        global $db;
        $SQLselect = "SELECT A.total, A.tva_taux FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id
            WHERE B.id = " . GetSQLValueString($agence, "int") . " AND A.paid = 1 AND A.tva_deductible = 1 AND A.devise = " . GetSQLValueString($devise, "text") . "
            AND A.tva_taux IS NOT NULL AND A.tva_taux > 0";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND A.id_user = " . intval($_SESSION['user']->getId());
        }
        if ($from) {
            $SQLselect .= " AND A.date_payment >= " . GetSQLValueString($from, "date");
        }
        if ($to) {
            $SQLselect .= " AND A.date_payment <= " . GetSQLValueString($to, "date");
        }
        $result = $db->queryS($SQLselect);
        $montant = 0;
        foreach ($result as $row) {
            $taux = (float) $row['tva_taux'];
            $total = (float) $row['total'];
            $montant += $total - ($total / (1 + $taux / 100));
        }
        return $montant;
    }

    // Détail ligne par ligne des charges prises en compte dans la TVA déductible (mêmes
    // critères exacts que montantTvaDeductible() ci-dessus), pour l'export comptable Excel.
    public static function detailTvaDeductible($from, $to, $agence = 1, $devise = 'DH')
    {
        global $db;
        $SQLselect = "SELECT A.id AS ID, A.titre, A.type, A.date_charge, A.date_payment, A.mode_payment, A.total, A.tva_taux, A.photo FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id
            WHERE B.id = " . GetSQLValueString($agence, "int") . " AND A.paid = 1 AND A.tva_deductible = 1 AND A.devise = " . GetSQLValueString($devise, "text") . "
            AND A.tva_taux IS NOT NULL AND A.tva_taux > 0";
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND A.id_user = " . intval($_SESSION['user']->getId());
        }
        if ($from) {
            $SQLselect .= " AND A.date_payment >= " . GetSQLValueString($from, "date");
        }
        if ($to) {
            $SQLselect .= " AND A.date_payment <= " . GetSQLValueString($to, "date");
        }
        $SQLselect .= " ORDER BY A.date_payment ASC";

        $result = $db->queryS($SQLselect);
        $lignes = array();
        foreach ($result as $row) {
            $taux = (float) $row['tva_taux'];
            $total = (float) $row['total'];
            $montantHT = $total / (1 + $taux / 100);
            $lignes[] = array(
                'id' => $row['ID'],
                'photo' => $row['photo'],
                'titre' => $row['titre'],
                'type' => $row['type'],
                'date_charge' => $row['date_charge'],
                'date_paiement' => $row['date_payment'],
                'mode_paiement' => $row['mode_payment'],
                'taux_tva' => $taux,
                'montant_ttc' => $total,
                'montant_ht' => $montantHT,
                'montant_tva' => $total - $montantHT,
            );
        }
        return $lignes;
    }

    // Toutes les charges de la période, indépendamment de leur déductibilité TVA (contrairement
    // à detailTvaDeductible() ci-dessus) — pour l'export comptable "Tous les achats ajoutés",
    // vue d'ensemble/traçabilité complète pour le comptable. Filtre sur date_charge, comme
    // facture::findAll() filtre sur date_facture côté ventes.
    public static function findAllByDate($from = false, $to = false, $agence = 1)
    {
        global $db;
        $SQLselect = "SELECT A.id, A.titre, A.type, A.date_charge, A.date_payment, A.devise, A.total, A.paid, A.tva_taux, A.tva_deductible
            FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id
            WHERE B.id = " . GetSQLValueString($agence, "int");
        if ($_SESSION['user']->isSuperUser() == false) {
            $SQLselect .= " AND A.id_user = " . intval($_SESSION['user']->getId());
        }
        if ($from) {
            $SQLselect .= " AND A.date_charge >= " . GetSQLValueString($from, "date");
        }
        if ($to) {
            $SQLselect .= " AND A.date_charge <= " . GetSQLValueString($to, "date");
        }
        $SQLselect .= " ORDER BY A.date_charge ASC";

        return $db->queryS($SQLselect);
    }

    // Top N charges (regroupées par titre - une même charge récurrente, ex. "Hébergement
    // serveur", revient sur plusieurs mois) sur une période, toutes devises converties en
    // DH-équivalent. Mêmes filtres (paid = 1, date_payment) que getCharge()/total() ci-dessus,
    // pour que ce classement explique bien le chiffre "Charges" affiché à côté sur la page.
    public static function topCharges($from = false, $to = false, $agence = 1, $limit = 10)
    {
        global $db;
        $devises = array('DH', '€', '£', '$', 'AED');
        $totaux = array();
        foreach ($devises as $devise) {
            $taux = tauxConversionDH($devise);
            $SQLselect = "SELECT A.titre, A.type, SUM(A.total) as total
                FROM " . static::$table . " A INNER JOIN " . static::$tableAgence . " B ON A.id_agence = B.id
                WHERE B.id = " . GetSQLValueString($agence, "int") . "
                AND A.paid = 1
                AND A.devise = " . GetSQLValueString($devise, "text");
            if ($from) {
                $SQLselect .= " AND A.date_payment >= " . GetSQLValueString($from, "date");
            }
            if ($to) {
                $SQLselect .= " AND A.date_payment <= " . GetSQLValueString($to, "date");
            }
            $SQLselect .= " GROUP BY A.titre";
            $result = $db->queryS($SQLselect);
            foreach ($result as $row) {
                $cle = mb_strtolower(trim($row['titre']));
                if (!isset($totaux[$cle])) {
                    $totaux[$cle] = array('titre' => $row['titre'], 'type' => $row['type'], 'total' => 0);
                }
                $totaux[$cle]['total'] += floatval($row['total']) * $taux;
            }
        }
        uasort($totaux, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        return array_slice(array_values($totaux), 0, $limit);
    }
}