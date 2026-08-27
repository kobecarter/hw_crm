<?php

class bank
{
    static $table =  __prefixe_db__ . "bank";

    private $id;
    private $raison_sociale;
    private $siege_social;
    private $numero_registre_commerce;
    private $ice;
    private $rib;
    private $code_swift;
    private $banque;
    private $label;
    private $exclu_rapprochement;
    private $iban_number;
    private $currency;
    private $agence;
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

    public function getRaisonSociale()
    {
        return $this->raison_sociale;
    }
	
	public function getSiegeSocial()
    {
        return $this->siege_social;
    }

    public function getNumeroRegistreCommerce()
    {
        return $this->numero_registre_commerce;
    }

	public function getIce()
    {
        return $this->ice;
    }

    public function getRib()
    {
        return $this->rib;
    }

    public function getCodeSwift()
    {
        return $this->code_swift;
    }
    
    public function getBanque()
    {
        return $this->banque;
    }

    // Nom d'affichage du compte pour le Rapprochement Bancaire (ex: "BCP Verse Concept",
    // "BMCE Convertible DH") - distinct de "banque" (nom de la banque elle-même), car une même
    // banque peut avoir plusieurs comptes ici (courant vs convertible).
    public function getLabel()
    {
        return $this->label;
    }

    // Compte personnel (ex: remboursement de frais), pas un compte de l'entreprise - à ne jamais
    // afficher/proposer dans la checklist ou la détection automatique de BANK STATEMENT.
    public function getExcluRapprochement()
    {
        return $this->exclu_rapprochement;
    }

    public function getIbanNumber()
    {
        return $this->iban_number;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }

    public function getAgence()
    {
        return $this->agence;
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

	
    public function setRaisonSociale($raison_sociale)
    {
        $this->raison_sociale = $raison_sociale;
    }
	
	public function setSiegeSocial($siege_social)
    {
        $this->siege_social = $siege_social;
    }

    public function setNumeroRegistreCommerce($numero_registre_commerce)
    {
        $this->numero_registre_commerce = $numero_registre_commerce;
    }

	public function setIce($ice)
    {
        $this->ice = $ice;
    }

    public function setRib($rib)
    {
        $this->rib = $rib;
    }

    public function setCodeSwift($code_swift)
    {
        $this->code_swift = $code_swift;
    }
    
    public function setBanque($banque)
    {
        $this->banque = $banque;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function setExcluRapprochement($exclu_rapprochement)
    {
        $this->exclu_rapprochement = $exclu_rapprochement ? 1 : 0;
    }

    public function setIbanNumber($iban_number)
    {
        $this->iban_number = $iban_number;
    }
    
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function setAgence($agence)
    {
        $this->agence = $agence;
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

        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (raison_sociale, siege_social, numero_registre_commerce, ice, rib, code_swift, banque, label, exclu_rapprochement, iban_number, currency, id_agence, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($this->raison_sociale, "text"),
            GetSQLValueString($this->siege_social, "text"),
            GetSQLValueString($this->numero_registre_commerce, "text"),
            GetSQLValueString($this->ice, "text"),
            GetSQLValueString($this->rib, "text"),
            GetSQLValueString($this->code_swift, "text"),
            GetSQLValueString($this->banque, "text"),
            GetSQLValueString($this->label, "text"),
            GetSQLValueString($this->exclu_rapprochement, "int"),
            GetSQLValueString($this->iban_number, "text"),
            GetSQLValueString($this->currency, "text"),
            GetSQLValueString($this->agence ? $this->agence->getId() : null, "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );

        if (!$db->query($SQLinsert)) 
        {
            return 1;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  raison_sociale = %s, siege_social = %s, numero_registre_commerce = %s, ice = %s, rib = %s, code_swift = %s, banque = %s, label = %s, exclu_rapprochement = %s, iban_number = %s, currency = %s, id_agence = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->raison_sociale, "text"),
            GetSQLValueString($this->siege_social, "text"),
            GetSQLValueString($this->numero_registre_commerce, "text"),
            GetSQLValueString($this->ice, "text"),
            GetSQLValueString($this->rib, "text"),
            GetSQLValueString($this->code_swift, "text"),
            GetSQLValueString($this->banque, "text"),
            GetSQLValueString($this->label, "text"),
            GetSQLValueString($this->exclu_rapprochement, "int"),
            GetSQLValueString($this->iban_number, "text"),
            GetSQLValueString($this->currency, "text"),
            GetSQLValueString($this->agence ? $this->agence->getId() : null, "int"),
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

    public static function find($id)
    {
        global $db;
        $bank = new bank();
        $SQLselect = sprintf("SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $bank = static::build($data);
        }
        return $bank;
    }

    // Recherche globale (bandeau de recherche du bandeau haut) : nom de banque, raison sociale,
    // RIB/IBAN ou ICE - utilisé par com_search pour la recherche "intelligente" par mot-clé
    // institutionnel (ex: "BMCE") ou par numéro de compte.
    public static function search($terme)
    {
        global $db;
        $items = array();
        $like = GetSQLValueString('%' . $terme . '%', 'text');
        $SQLselect = "SELECT * FROM " . static::$table
            . " WHERE banque LIKE $like OR raison_sociale LIKE $like OR rib LIKE $like OR iban_number LIKE $like OR ice LIKE $like"
            . " ORDER BY id DESC LIMIT 8";
        foreach ($db->queryS($SQLselect) as $data) {
            array_push($items, static::build($data));
        }
        return $items;
    }

    public static function findAll($id_agence = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table);
        if (is_array($id_agence) && !empty($id_agence)) {
            $ids = array_map('intval', $id_agence);
            $SQLselect .= " WHERE id_agence IN (" . implode(',', $ids) . ")";
        } elseif ($id_agence) {
            $SQLselect .= sprintf(" WHERE id_agence = %s", GetSQLValueString($id_agence, "int"));
        }

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $bank = static::build($data);
            array_push($items, $bank);
        }
        return $items;
    }

    // Liste des comptes utilisables pour une agence donnée sur un formulaire devis/facture - mêmes
    // règles strictes par agence que com_bank/controleurs/bank/controleur.php::getBanksByAgence()
    // (le rafraîchissement AJAX déclenché au changement de client), pour que la liste affichée dès
    // le premier chargement de la page soit identique à celle après rafraîchissement. Avant ce
    // partage, com_devis/index.php et com_facture/index.php utilisaient un simple
    // bank::findAll($agence) qui ne connaissait pas ces règles : un compte perso (Hamid/Zakaria)
    // ou pooled assigné à un devis/une facture d'une agence différente de celle du compte
    // n'apparaissait alors PAS dans la liste au premier chargement - aucune option ne portait donc
    // "selected", et enregistrer sans toucher au champ effaçait silencieusement le compte déjà
    // choisi.
    public static function findAllPourFormulaire($idAgence)
    {
        $reglesParAgence = array(
            3 => array(1, 11, 7),
            1 => array(6, 12, 10, 11, 7),
            // 13 = XNBD PERSONAL HAMID DUBAI : compte perso ("PERSONAL" dans la raison
            // sociale, cf. estPerso plus bas / com_bank/controleurs/bank/controleur.php),
            // donc déjà automatiquement restreint aux devis/factures proforma par
            // assets/js/ia-bank-filter.js dès qu'il est sélectionné.
            2 => array(2, 13),
        );

        if (isset($reglesParAgence[$idAgence])) {
            $idsAutorises = $reglesParAgence[$idAgence];
            $banks = array();
            foreach (self::findAll(false) as $b) {
                if (in_array($b->getId(), $idsAutorises)) {
                    $banks[] = $b;
                }
            }
            usort($banks, function ($a, $b) use ($idsAutorises) {
                return array_search($a->getId(), $idsAutorises) <=> array_search($b->getId(), $idsAutorises);
            });
            return $banks;
        }

        $groupeMaroc = array(1, 3, 25);
        $agencesARechercher = in_array($idAgence, $groupeMaroc) ? $groupeMaroc : $idAgence;
        return $idAgence ? self::findAll($agencesARechercher) : array();
    }

    public static function build($data){
        global $db;
        $bank = new bank();
        
        $bank->setId($data['id']);
        $bank->setRaisonSociale($data['raison_sociale']);
        $bank->setSiegeSocial($data['siege_social']);
		$bank->setNumeroRegistreCommerce($data['numero_registre_commerce']);
        $bank->setIce($data['ice']);
        $bank->setRib($data['rib']);
        $bank->setCodeSwift($data['code_swift']);
        $bank->setBanque($data['banque']);
        $bank->setLabel(isset($data['label']) ? $data['label'] : null);
        $bank->setExcluRapprochement(isset($data['exclu_rapprochement']) ? $data['exclu_rapprochement'] : 0);
        $bank->setIbanNumber($data['iban_number']);
        $bank->setCurrency($data['currency']);
        if (isset($data['id_agence']) && !empty($data['id_agence'])) {
            $bank->setAgence(agence::find($data['id_agence'], isset($_SESSION['langue']) ? $_SESSION['langue'] : 'fr'));
        }
        $bank->setDateAdd($data['date_add']);
        $bank->setLastEdit($data['last_edit']);
        return $bank;
    }
    

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    /* Détecte un "logo" (pastille d'initiales + couleur de marque approximative)
       à partir du nom de la banque (champ "banque", ex: "Banque Populaire",
       "BMCE", "Mashreq bank") : aucune image de logo réelle n'est stockée dans
       le projet, donc on retombe sur des initiales colorées façon avatar,
       cohérentes d'une page à l'autre puisque basées uniquement sur le nom. */
    public static function getLogoInfo($nomBanque)
    {
        $name = mb_strtolower(trim((string) $nomBanque), 'UTF-8');

        $known = array(
            'populaire'        => array('initials' => 'BP',  'bg' => '#003d6a'),
            'bmce'             => array('initials' => 'BOA', 'bg' => '#00954d'),
            'bank of africa'   => array('initials' => 'BOA', 'bg' => '#00954d'),
            'mashreq'          => array('initials' => 'MB',  'bg' => '#8a6d1e'),
            'attijari'         => array('initials' => 'AWB', 'bg' => '#e2001a'),
            'cih'              => array('initials' => 'CIH', 'bg' => '#f47b20'),
            'societe generale' => array('initials' => 'SG',  'bg' => '#e60028'),
            'société générale' => array('initials' => 'SG',  'bg' => '#e60028'),
            'credit du maroc'  => array('initials' => 'CDM', 'bg' => '#005baa'),
            'crédit du maroc'  => array('initials' => 'CDM', 'bg' => '#005baa'),
            'bank al maghrib'  => array('initials' => 'BAM', 'bg' => '#7a1f2b'),
        );
        foreach ($known as $needle => $info) {
            if ($name !== '' && mb_strpos($name, $needle) !== false) {
                return $info;
            }
        }

        // repli générique : initiales des deux premiers mots du nom
        $words = preg_split('/\s+/', trim((string) $nomBanque));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            if ($w !== '') {
                $initials .= mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'), 'UTF-8');
            }
        }
        return array('initials' => $initials !== '' ? $initials : '?', 'bg' => '#6366f1');
    }

    public static function count(){
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function deleteMultiple($data){
        
        global $db;	
        if(isset($data['ids']) && !empty($data['ids'])){
            extract($data);
            $SQLdelete = "DELETE FROM ". static::$table ." WHERE id in $ids";
            if(!$db->query($SQLdelete)) {
                //seo();
                return 1;
            }else
                return 2;
        }
        else
            return 0;
    }

    public static function buildApi($data){
        $bank = [
            'id' => $data['id'],
            'raison_sociale' => $data['raison_sociale'],
            'siege_social' => $data['siege_social'],
            'numero_registre_commerce' => $data['numero_registre_commerce'],
            'ice' => $data['ice'],
            'rib' => $data['rib'],
            'code_swift' => $data['code_swift'],
            'banque' => $data['banque'],
            'iban_number' => $data['iban_number'],
            'currency' => $data['currency'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit']
        ];
        return $bank;
    }

    public static function ApiFindById($id)
    {
        if(getToken()){
            global $db;
            $bank = new bank();
            $SQLselect = sprintf("SELECT * FROM " . static::$table . " where id = %s",
                GetSQLValueString($id, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $bank = static::buildApi($data);
            }
            return $bank;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }
}