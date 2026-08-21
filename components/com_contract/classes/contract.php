<?php

class contract
{
    static $table =  __prefixe_db__ . "contract";
    static $table2 =  __prefixe_db__ . "details_contract";
    static $table3 =  __prefixe_db__ . "devis";
    static $table4 =  __prefixe_db__ . "client";

    private $id;
    private $type;
    private $devis;
    private $titre;
    private $duration;
    private $garantie;
    private $ville;
    private $date;
    private $date_fin;
    private $tribunal;
    private $nombre_de_paiement;
    private $texte;
    private $show_signature;
    private $status;
    private $contrat_pdf;
    private $corps_genere;
    private $signed_at;
    private $acceptance_token;
    private $signed_by_name;
    private $signed_ip;
    private $langue;
    private $date_add;
    private $last_edit;

    const STATUT_PREPARATION = 1;
    const STATUT_ATTENTE_SIGNATURE = 2;
    const STATUT_SIGNE = 3;
    const STATUT_REFUSE = 4;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getType()
    {
        return $this->type;
    }
	
	public function getDevis()
    {
        return $this->devis;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getGarantie()
    {
        return $this->garantie;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDateFin()
    {
        return $this->date_fin;
    }

    public function getTribunal()
    {
        return $this->tribunal;
    }

    public function getNombreDePaiement()
    {
        return $this->nombre_de_paiement;
    }

	public function getTexte()
    {
        return $this->texte;
    }

    public function getLangue()
    {
        return $this->langue;
    }
    
    public function getShowSignature()
    {
        return $this->show_signature;
    }
    
     public function isShowSignature()
    {
        return $this->show_signature == 1 ? true : false;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getContratPDF()
    {
        return $this->contrat_pdf;
    }

    // Corps HTML complet du contrat, figé au moment de "Générer le contrat" depuis un devis
    // (contractGenerator::genererCorpsContrat()) puis modifiable dans l'éditeur WYSIWYG - une fois
    // rempli, pdfContract()/docxContract() l'utilisent tel quel au lieu de régénérer à chaque appel
    // (sinon les modifications manuelles de l'utilisateur seraient perdues). Reste NULL pour les
    // anciens contrats (créés avant cette fonctionnalité) et pour ceux créés via l'ancien
    // formulaire manuel - ceux-là continuent de se régénérer dynamiquement à chaque export.
    public function getCorpsGenere()
    {
        return $this->corps_genere;
    }

    public function getSignedAt()
    {
        return $this->signed_at;
    }

    // Jeton de signature en ligne (cf. envoyerContratEmailSignature() et
    // components/com_contract/controleurs/contractaccept/router.php, même mécanisme que
    // crm_joboffer.acceptance_token) - null tant que le contrat n'a jamais été envoyé pour
    // signature en ligne.
    public function getAcceptanceToken()
    {
        return $this->acceptance_token;
    }

    // Nom tapé + IP capturés au moment de la signature en ligne (contractaccept/router.php) -
    // trace d'audit indépendante du fichier lui-même : reste disponible même quand le fichier
    // déposé (Word/PDF override) ne peut pas recevoir la mention de signature.
    public function getSignedByName()
    {
        return $this->signed_by_name;
    }

    public function getSignedIp()
    {
        return $this->signed_ip;
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
    
    public function setType($type)
    {
        $this->type = $type;
    }
	
	public function setDevis($devis)
    {
        $this->devis = $devis;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    public function setGarantie($garantie)
    {
        $this->garantie = $garantie;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setDateFin($date_fin)
    {
        $this->date_fin = $date_fin;
    }

    public function setTribunal($tribunal)
    {
        $this->tribunal = $tribunal;
    }

    public function setNombreDePaiement($nombre_de_paiement)
    {
        $this->nombre_de_paiement = $nombre_de_paiement;
    }
    
    public function setTexte($texte)
    {
        $this->texte = $texte;
    }

    public function setLangue($langue)
    {
        $this->langue = $langue;
    }
    
    public function setShowSignature($show_signature)
    {
        $this->show_signature = $show_signature;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setContratPDF($contrat_pdf)
    {
        $this->contrat_pdf = $contrat_pdf;
    }

    public function setCorpsGenere($corps_genere)
    {
        $this->corps_genere = $corps_genere;
    }

    public function setSignedAt($signed_at)
    {
        $this->signed_at = $signed_at;
    }

    public function setAcceptanceToken($acceptance_token)
    {
        $this->acceptance_token = $acceptance_token;
    }

    public function setSignedByName($signed_by_name)
    {
        $this->signed_by_name = $signed_by_name;
    }

    public function setSignedIp($signed_ip)
    {
        $this->signed_ip = $signed_ip;
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

        $SQLinsert = sprintf("INSERT INTO " . static::$table . " (id_devis, type, date, date_fin, nombre_de_paiement, show_signature, status, contrat_pdf, corps_genere, signed_at, acceptance_token, signed_by_name, signed_ip, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($this->devis->getId(), "id"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->date_fin, "date"),
            GetSQLValueString($this->nombre_de_paiement, "int"),
            GetSQLValueString($this->show_signature, "int"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->contrat_pdf, "text"),
            GetSQLValueString($this->corps_genere, "text"),
            GetSQLValueString($this->signed_at, "date"),
            GetSQLValueString($this->acceptance_token, "text"),
            GetSQLValueString($this->signed_by_name, "text"),
            GetSQLValueString($this->signed_ip, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
    
        //echo $SQLinsert;
        //echo 'test';
        if (!$db->query($SQLinsert)) 
        {
            $id_contract = $db->last_id();
            $SQLinsert2 = sprintf("INSERT INTO " . static::$table2 . " (id_contract, titre, duration, garantie, ville, tribunal, texte, langue) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_contract, "int"),
                GetSQLValueString($this->titre, "text"),
                GetSQLValueString($this->duration, "text"),
                GetSQLValueString($this->garantie, "text"),
                GetSQLValueString($this->ville, "text"),
                GetSQLValueString($this->tribunal, "text"),
                GetSQLValueString($this->texte, "text"),
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
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET  id_devis = %s, type = %s, date = %s, date_fin = %s, nombre_de_paiement = %s, show_signature = %s, status = %s, contrat_pdf = %s, corps_genere = %s, signed_at = %s, acceptance_token = %s, signed_by_name = %s, signed_ip = %s, last_edit = %s WHERE id = %s",
			GetSQLValueString($this->devis->getId(), "int"),
            GetSQLValueString($this->type, "text"),
            GetSQLValueString($this->date, "date"),
            GetSQLValueString($this->date_fin, "date"),
            GetSQLValueString($this->nombre_de_paiement, "int"),
            GetSQLValueString($this->show_signature, "int"),
            GetSQLValueString($this->status, "int"),
            GetSQLValueString($this->contrat_pdf, "text"),
            GetSQLValueString($this->corps_genere, "text"),
            GetSQLValueString($this->signed_at, "date"),
            GetSQLValueString($this->acceptance_token, "text"),
            GetSQLValueString($this->signed_by_name, "text"),
            GetSQLValueString($this->signed_ip, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->id, "int")
        );
       
        if (!$db->query($SQLupdate)) {
            $SQLselect = sprintf("SELECT * FROM " . static::$table2 . " WHERE id_contract = %s AND langue = %s",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->langue, "text")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 0) {
                $SQLupdate = sprintf("INSERT INTO " . static::$table2 . " (id_contract, titre, duration, garantie, ville, tribunal, texte, langue) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->titre, "text"),
                GetSQLValueString($this->duration, "text"),
                GetSQLValueString($this->garantie, "text"),
                GetSQLValueString($this->ville, "text"),
                GetSQLValueString($this->tribunal, "text"),
                GetSQLValueString($this->texte, "text"),
                GetSQLValueString($this->langue, "text")
                );
            } else {
                $SQLupdate = sprintf("UPDATE " . static::$table2 . " SET titre = %s, duration = %s, garantie = %s, ville = %s, tribunal = %s, texte = %s  WHERE id_contract = %s AND langue = %s",
                    GetSQLValueString($this->titre, "text"),
                    GetSQLValueString($this->duration, "text"),
                    GetSQLValueString($this->garantie, "text"),
                    GetSQLValueString($this->ville, "text"),
                    GetSQLValueString($this->tribunal, "text"),
					GetSQLValueString($this->texte, "text"),
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
        $SQLdelete2 = sprintf("DELETE FROM " . static::$table2 . " WHERE id_contract = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if(!$db->query($SQLdelete) && !$db->query($SQLdelete2)){
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id, $agence = 1, $langue = 'fr')
    {
        global $db;
        $contract = new contract();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM  " . static::$table . " A inner join " . static::$table3 ." C on A.id_devis = C.id inner join " . static::$table4 ." D on C.id_client = D.id LEFT JOIN " . static::$table2 . " B ON A.id = B.id_contract AND B.langue = %s WHERE A.id = %s and D.id_agence = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id, "int"),
            GetSQLValueString($agence, "int")

        );

        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (C.id_user_added = ".$_SESSION['user']->getId()." )";
        }

        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $contract = static::build($data);
        }
        return $contract;
    }

    public static function findByDevis($id_devis, $agence = 1, $langue = 'fr')
    {
        global $db;
        $contract = new contract();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM  " . static::$table . " A inner join " . static::$table3 ." C on A.id_devis = C.id  inner join " . static::$table4 ." D on C.id_client = D.id  LEFT JOIN " . static::$table2 . " B ON A.id = B.id_contract AND B.langue = %s WHERE A.id_devis = %s and D.id_agence = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id_devis, "int"),
            GetSQLValueString($agence, "int")
        );

        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (C.id_user_added = ".$_SESSION['user']->getId()." )";
        }

        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $contract = static::build($data);
        }
        return $contract;
    }

    public static function findAll($agence=1,$langue = "fr")
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM  " . static::$table . " A  inner join " . static::$table3 ." C on A.id_devis = C.id  inner join " . static::$table4 ." D on C.id_client = D.id  LEFT JOIN " . static::$table2 . " B ON A.id = B.id_contract AND B.langue = %s WHERE D.id_agence = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($agence, "int")
        );

        if($_SESSION['user']->isSuperUser() == false){
            $SQLselect .= " AND (C.id_user_added = ".$_SESSION['user']->getId()." )";
        }

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $contract = static::build($data);
            array_push($items, $contract);
        }
        return $items;
    }

    public static function build($data,$id = null){
        global $db;
        $contract = new contract();

        if($id){
            $contract = contract::find($id,$_SESSION['agence'],$_SESSION['langue']);
        }
        
        $contract->setId($data['ID']);
        $contract->setType($data['type']);
        $contract->setDevis(devis::find($data['id_devis'],$_SESSION['agence']));
        $contract->setTitre($data['titre']);
        $contract->setDuration($data['duration']);
        $contract->setGarantie($data['garantie']);
        $contract->setVille($data['ville']);
        $contract->setDate($data['date']);
        $contract->setDateFin($data['date_fin']);
        $contract->setTribunal($data['tribunal']);
        $contract->setNombreDePaiement($data['nombre_de_paiement']);
        $contract->setTexte($data['texte']);
        $contract->setShowSignature($data['show_signature']);
        $contract->setStatus($data['status']);
        $contract->setContratPDF($data['contrat_pdf']);
        $contract->setCorpsGenere(isset($data['corps_genere']) ? $data['corps_genere'] : null);
        $contract->setSignedAt(isset($data['signed_at']) ? $data['signed_at'] : null);
        $contract->setAcceptanceToken(isset($data['acceptance_token']) ? $data['acceptance_token'] : null);
        $contract->setSignedByName(isset($data['signed_by_name']) ? $data['signed_by_name'] : null);
        $contract->setSignedIp(isset($data['signed_ip']) ? $data['signed_ip'] : null);
        $contract->setDateAdd($data['date_add']);
        $contract->setLastEdit($data['last_edit']);
        $contract->setLangue($data['langue']);
        return $contract;
    }

    public static function getLastId(){
        global $db;
        return $db->last_id();
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
            $SQLdelete2 = "DELETE FROM ". static::$table2 ." WHERE id_contract in $ids";
            if(!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
                //seo();
                return 1;
            }else
                return 2;
        }
        else
            return 0;
    }

    public static function buildApi($data){
        $contract = array(
            'id' => $data['ID'],
            'devis' => devis::findByIdApi($data['id_devis']),
            'titre' => $data['titre'],
            'duration' => $data['duration'],
            'garantie' => $data['garantie'],
            'ville' => $data['ville'],
            'date' => $data['date'],
            'date_fin' => $data['date_fin'],
            'tribunal' => $data['tribunal'],
            'nombre_de_paiement' => $data['nombre_de_paiement'],
            'texte' => $data['texte'],
            'show_signature' => $data['show_signature'],
            'date_add' => $data['date_add'],
            'last_edit' => $data['last_edit'],
            'langue' => $data['langue'],
        );
        return $contract;
    }

    public static function ApiFindById($id , $agence=1, $langue = 'fr')
    {
        $token = getToken();
        if($token){
            global $db;
            $contract = new contract();
            // Un client ne doit voir que SES PROPRES contrats (id_client du devis
            // lié doit correspondre au token vérifié — pas seulement à l'agence).
            $SQLselect = sprintf("SELECT A.id as ID, A.*, B.* FROM  " . static::$table . " A inner join " . static::$table3 ." C on A.id_devis = C.id inner join " . static::$table4 ." D on C.id_client = D.id LEFT JOIN " . static::$table2 . " B ON A.id = B.id_contract AND B.langue = %s WHERE A.id = %s and D.id_agence = %s and C.id_client = %s",
                GetSQLValueString($langue, "text"),
                GetSQLValueString($id, "int"),
                GetSQLValueString($agence, "int"),
                GetSQLValueString((int) $token->id, "int")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 1) {
                $data = $db->fetch_assoc($result);
                $contract = static::buildApi($data);
            }
            return $contract;
        }else{
            return json_encode(array("icon"=>"error","message"=>"Unauthorized"));
        }
    }

}