<?php

class agence
{
    static $table =  __prefixe_db__ . "agence";
    static $table2 =  __prefixe_db__ . "details_agence";

    private $id;
    private $logo;
    private $signature;
    private $raison_social;
    private $manager;
    private $cin;
    private $nom;
    private $email;
    private $tel;
    private $tel2;
    private $fax;
    private $adresse;
    private $ville;
    private $fonction;
    private $condition_de_paiement;
    private $numero_increment_facture;
    private $numero_increment_devis;
    private $tva;
    private $tva_seuil_alerte;
    private $tva_periodicite;
    private $tva_slack_derniere_alerte;
    private $website;
    private $color;
    private $if;
    private $tp;
    private $rc;
    private $ice;
    private $conditions;
    private $information;
    private $date_add;
    private $last_edit;
    private $langue;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }
	
	public function getlogo()
    {
        return $this->logo;
    }
    
    public function getSignature()
    {
        return $this->signature;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getRaisonSocial()
    {
        return $this->raison_social;
    }
    
    public function getManager()
    {
        return $this->manager;
    }

    public function getCin()
    {
        return $this->cin;
    }

	public function getEmail()
    {
        return $this->email;
    }

    public function getTel()
    {
        return $this->tel;
    }

    public function getTel2()
    {
        return $this->tel2;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function getFonction()
    {
        return $this->fonction;
    }

    public function getConditionDePaiement()
    {
        return $this->condition_de_paiement;
    }

    public function getNumeroIncrementFacture()
    {
        return $this->numero_increment_facture;
    }

    public function getNumeroIncrementDevis()
    {
        return $this->numero_increment_devis;
    }

    public function getTva()
    {
        return $this->tva;
    }

    public function getTvaSeuilAlerte()
    {
        return $this->tva_seuil_alerte;
    }

    public function getTvaPeriodicite()
    {
        return $this->tva_periodicite;
    }

    public function getTvaSlackDerniereAlerte()
    {
        return $this->tva_slack_derniere_alerte;
    }

    public function getWebsite()
    {
        return $this->website;
    }
    
    public function getColor()
    {
        return $this->color;
    }

    public function getIf()
    {
        return $this->if;
    }

    public function getTp()
    {
        return $this->tp;
    }

    public function getRc()
    {
        return $this->rc;
    }

    public function getIce()
    {
        return $this->ice;
    }

    public function getConditions()
    {
        return $this->conditions;
    }
    
    public function getInformation()
    {
        return $this->information;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function getLangue()
    {
        return $this->langue;
    }

	
    public function setId($id)
    {
        $this->id = $id;
    }
	
	public function setLogo($logo)
    {
        $this->logo = $logo;
    }
    
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setRaisonSocial($raison_social)
    {
        $this->raison_social = $raison_social;
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
    }


    public function setCin($cin)
    {
        $this->cin = $cin;
    }

	public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    public function setTel2($tel2)
    {
        $this->tel2 = $tel2;
    }

    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    public function setFonction($fonction)
    {
        $this->fonction = $fonction;
    }

    public function setConditionDePaiement($condition_de_paiement)
    {
        $this->condition_de_paiement = $condition_de_paiement;
    }

    public function setNumeroIncrementFacture($numero_increment_facture)
    {
        $this->numero_increment_facture = $numero_increment_facture;
    }

    public function setNumeroIncrementDevis($numero_increment_devis)
    {
        $this->numero_increment_devis = $numero_increment_devis;
    }

    public function setTva($tva)
    {
        $this->tva = $tva;
    }

    public function setTvaSeuilAlerte($tva_seuil_alerte)
    {
        $this->tva_seuil_alerte = $tva_seuil_alerte;
    }

    public function setTvaPeriodicite($tva_periodicite)
    {
        $this->tva_periodicite = $tva_periodicite === 'trimestriel' ? 'trimestriel' : 'mensuel';
    }

    public function setWebsite($website)
    {
        $this->website = $website;
    }
    
    public function setColor($color)
    {
        $this->color = $color;
    }

    public function setIf($if)
    {
        $this->if = $if;
    }

    public function setTp($tp)
    {
        $this->tp = $tp;
    }

    public function setRc($rc)
    {
        $this->rc = $rc;
    }

    public function setIce($ice)
    {
        $this->ice = $ice;
    }

    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }
    
    public function setInformation($information)
    {
        $this->information = $information;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function setLangue($langue)
    {
        $this->langue = $langue;
    }

    public function add()
    {
        global $db;

        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (logo, signature, email, raison_social, manager, cin, tel, tel2, fax, numero_increment_facture, numero_increment_devis, tva, tva_seuil_alerte, tva_periodicite, website, color, `if`, tp, rc, ice, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($this->logo, "text"),
			GetSQLValueString($this->signature, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->raison_social, "text"),
            GetSQLValueString($this->manager, "text"),
            GetSQLValueString($this->cin, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->tel, "text"),
            GetSQLValueString($this->tel2, "text"),
            GetSQLValueString($this->fax, "text"),
            GetSQLValueString($this->numero_increment_facture, "int"),
            GetSQLValueString($this->numero_increment_devis, "int"),
            GetSQLValueString($this->tva, "int"),
            GetSQLValueString($this->tva_seuil_alerte, "double"),
            GetSQLValueString($this->tva_periodicite, "text"),
            GetSQLValueString($this->website, "text"),
            GetSQLValueString($this->color, "text"),
            GetSQLValueString($this->if, "text"),
            GetSQLValueString($this->tp, "text"),
            GetSQLValueString($this->rc, "text"),
            GetSQLValueString($this->ice, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );

        if (!$db->query($SQLinsert)) 
        {
            $id_agence = $db->last_id();
            $SQLinsert2 = sprintf("INSERT INTO " . static::$table2 . " (id_agence, nom, adresse, ville, fonction, condition_de_paiement, conditions, information, langue) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_agence, "int"),
                GetSQLValueString($this->nom, "text"),
                GetSQLValueString($this->adresse, "text"),
                GetSQLValueString($this->ville, "text"),
                GetSQLValueString($this->fonction, "text"),
                GetSQLValueString($this->condition_de_paiement, "text"),
                GetSQLValueString($this->conditions, "text"),
                GetSQLValueString($this->information, "text"),
                GetSQLValueString($this->langue, "text")
            );
            if (!$db->query($SQLinsert2)) {
                return 1;
            }
            return 2;
        } else {
            return 0;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  logo = %s, signature = %s, email = %s, raison_social = %s, manager = %s, cin = %s,  tel = %s, tel2 = %s, fax = %s, numero_increment_facture = %s, numero_increment_devis = %s, tva = %s, tva_seuil_alerte = %s, tva_periodicite = %s, website = %s, color = %s, `if` = %s, tp = %s, rc = %s, ice = %s, last_edit = %s WHERE id = %s",
			GetSQLValueString($this->logo, "text"),
			GetSQLValueString($this->signature, "text"),
            GetSQLValueString($this->email, "text"),
            GetSQLValueString($this->raison_social, "text"),
            GetSQLValueString($this->manager, "text"),
            GetSQLValueString($this->cin, "text"),
            GetSQLValueString($this->tel, "text"),
            GetSQLValueString($this->tel2, "text"),
            GetSQLValueString($this->fax, "text"),
            GetSQLValueString($this->numero_increment_facture, "text"),
            GetSQLValueString($this->numero_increment_devis, "text"),
            GetSQLValueString($this->tva, "int"),
            GetSQLValueString($this->tva_seuil_alerte, "double"),
            GetSQLValueString($this->tva_periodicite, "text"),
            GetSQLValueString($this->website, "text"),
            GetSQLValueString($this->color, "text"),
            GetSQLValueString($this->if, "text"),
            GetSQLValueString($this->tp, "text"),
            GetSQLValueString($this->rc, "text"),
            GetSQLValueString($this->ice, "text"),
            GetSQLValueString($this->last_edit, "date"), 
            GetSQLValueString($this->id, "int")
        );

        if (!$db->query($SQLupdate)) {
            $SQLselect = sprintf("SELECT * FROM " . static::$table2 . " WHERE id_agence = %s AND langue = %s",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->langue, "text")
            );
            
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 0) {
                $SQLupdate = sprintf("INSERT INTO " . static::$table2 . " (id_agence, nom, adresse, ville, fonction, condition_de_paiement, conditions, information, langue) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->nom, "text"),
                GetSQLValueString($this->adresse, "text"),
                GetSQLValueString($this->ville, "text"),
                GetSQLValueString($this->fonction, "text"),
                GetSQLValueString($this->condition_de_paiement, "text"),
                GetSQLValueString($this->conditions, "text"),
                GetSQLValueString($this->information, "text"),
                GetSQLValueString($this->langue, "text")
                );
            } else {
                $SQLupdate = sprintf("UPDATE " . static::$table2 . " SET nom = %s, adresse = %s, ville = %s, fonction = %s, condition_de_paiement = %s, conditions = %s, information = %s  WHERE id_agence = %s AND langue = %s",
					GetSQLValueString($this->nom, "text"),
					GetSQLValueString($this->adresse, "text"),
                    GetSQLValueString($this->ville, "text"),
                    GetSQLValueString($this->fonction, "text"),
					GetSQLValueString($this->condition_de_paiement, "text"),
                    GetSQLValueString($this->conditions, "text"),
                    GetSQLValueString($this->information, "text"),
                    GetSQLValueString($this->id, "int"),
                    GetSQLValueString($this->langue, "text")
                );
            }
            if (!$db->query($SQLupdate)) {
                return 1;
            } else {
                return 2;
            }
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
        $SQLdelete2 = sprintf("DELETE FROM " . static::$table2 . " WHERE id_agence = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete) && !$db->query($SQLdelete2)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id, $langue = 'fr')
    {
        global $db;
        $agence = new agence();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_agence AND langue = %s WHERE A.id = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $agence = static::build($data);
        }
        return $agence;
    }

    public static function findAll($langue, $active = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_agence AND langue = %s WHERE 1 = 1",
            GetSQLValueString($langue, "text")
        );
        if($active){
            $SQLselect .= " AND active = 1";
        }
		
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $agence = static::build($data);
            array_push($items, $agence);
        }
        return $items;
    }

    public static function findLogosName($data)
    {
        global $db;
        $logos = [];
        if (isset($data['ids']) && !empty($data['ids'])) {
            $SQLselect = sprintf(
                "SELECT photo FROM " . static::$table . " WHERE id in%s",
                GetSQLValueString($data['ids'], "text")
            );

            $result = $db->queryS($SQLselect);

            foreach ($result as $data) {
                $logos[] = $data["logo"];
            }
            return $logos;
        }
    }
    
    public static function findSignaturesName($data)
    {
        global $db;
        $signatures = [];
        if (isset($data['ids']) && !empty($data['ids'])) {
            $SQLselect = sprintf(
                "SELECT signature FROM " . static::$table . " WHERE id in%s",
                GetSQLValueString($data['ids'], "text")
            );

            $result = $db->queryS($SQLselect);

            foreach ($result as $data) {
                $signatures[] = $data["signature"];
            }
            return $signatures;
        }
    }

    public static function build($data){
        global $db;
        $agence = new agence();
        
        $agence->setId($data['ID']);
        $agence->setLogo($data['logo']);
        $agence->setSignature($data['signature']);
        $agence->setNom($data['nom']);
		$agence->setEmail($data['email']);
        $agence->setRaisonSocial($data['raison_social']);
        $agence->setManager($data['manager']);
        $agence->setCin($data['cin']);
        $agence->setTel($data['tel']);
        $agence->setTel2($data['tel2']);
        $agence->setFax($data['fax']);
        $agence->setAdresse($data['adresse']);
        $agence->setVille($data['ville']);
        $agence->setFonction($data['fonction']);
        $agence->setConditionDePaiement($data['condition_de_paiement']);
        $agence->setNumeroIncrementFacture($data['numero_increment_facture']);
        $agence->setNumeroIncrementDevis($data['numero_increment_devis']);
        $agence->setTva($data['tva']);
        $agence->setTvaSeuilAlerte(isset($data['tva_seuil_alerte']) ? $data['tva_seuil_alerte'] : null);
        $agence->setTvaPeriodicite(isset($data['tva_periodicite']) ? $data['tva_periodicite'] : 'mensuel');
        $agence->tva_slack_derniere_alerte = isset($data['tva_slack_derniere_alerte']) ? $data['tva_slack_derniere_alerte'] : null;
        $agence->setWebsite($data['website']);
        $agence->setColor($data['color']);
        $agence->setIf($data['if']);
        $agence->setTp($data['tp']);
        $agence->setRc($data['rc']);
        $agence->setIce($data['ice']);
        $agence->setConditions($data['conditions']);
        $agence->setInformation($data['information']);
        $agence->setDateAdd($data['date_add']);
        $agence->setLastEdit($data['last_edit']);
        $agence->setLangue($data['langue']);
        return $agence;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
    }

    // Marque qu'une alerte Slack "échéance TVA proche" a déjà été envoyée aujourd'hui pour cette
    // agence — évite les envois en double si le cron externe (cronCheckEcheanceTva) est appelé
    // plusieurs fois le même jour. Simple UPDATE ciblé plutôt qu'un aller-retour add()/edit()
    // complet : ce champ est purement opérationnel, jamais affiché/modifié via le formulaire Agence.
    public static function marquerAlerteTvaEnvoyee($idAgence)
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET tva_slack_derniere_alerte = %s WHERE id = %s",
            GetSQLValueString(date('Y-m-d'), "date"),
            GetSQLValueString($idAgence, "int")
        );
        $db->query($SQLupdate);
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
            $SQLdelete2 = "DELETE FROM ". static::$table2 ." WHERE id_agence in $ids";
            if(!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
                //seo();
                return 1;
            }else
                return 2;
        }
        else
            return 0;
    }

    // API

    public static function buildApi($data){
        $agence = array(
            'id' => $data['ID'],
            'logo' => $data['logo'],
            'signature' => $data['signature'],
            'nom' => $data['nom'],
            'email' => $data['email'],
            'raison_social' => $data['raison_social'],
            'manager' => $data['manager'],
            'cin' => $data['cin'],
            'tel' => $data['tel'],
            'tel2' => $data['tel2'],
            'fax' => $data['fax'],
            'adresse' => $data['adresse'],
            'ville' => $data['ville'],
            'fonction' => $data['fonction'],
            'condition_de_paiement' => $data['condition_de_paiement'],
            'numero_increment_facture' => $data['numero_increment_facture'],
            'numero_increment_devis' => $data['numero_increment_devis'],
            'tva' => $data['tva'],
            'website' => $data['website'],
            'color' => $data['color'],
            'if' => $data['if'],
            'tp' => $data['tp'],
            'rc' => $data['rc'],
            'ice' => $data['ice'],
            'conditions' => $data['conditions'],
            'information' => $data['information'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
            'langue' => $data['langue'],
        );
        return $agence;
    }

    public static function ApiFindById($id, $langue = 'fr')
    {
        if(getToken()){
            global $db;
            $agence = new agence();
            $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_agence AND langue = %s WHERE A.id = %s",
                GetSQLValueString($langue, "text"),
                GetSQLValueString($id, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $agence = static::buildApi($data);
            }
            return $agence;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

}