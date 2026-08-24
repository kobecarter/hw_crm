<?php
class mysql extends dbfactory {

    protected $default_cfg = array(
        'host'   => 'localhost',
        'user'   => 'root',
        'passwd' => '',
        'name'   => 'test');

    // connection a la base
    protected function connect () {
		$this->_config['link'] = new mysqli($this->_config['host'], $this->_config['user'], $this->_config['passwd'], $this->_config['name']);
		
        /*$this->_config['link'] = @mysql_connect($this->_config['host'],
            $this->_config['user'],
            $this->_config['passwd']);*/
        if (!$this->_config['link'] ) {
            throw new Exception('Erreur lors de la connection vers : '.$this->_config['host'].'.');
        }

        /*$this->_config['base'] = @mysql_select_db($this->_config['name'], $this->_config['link']);
        if (!$this->_config['base'] ) {
            throw new Exception('Erreur lors de l\'ouverture de la base de donn�e : '.$this->_config['name'].'.');
            unset($this->_config);
        }*/
        //echo 'connection r�ussie avec '.__CLASS__;
    }

    // Fermeture de la base de donn�es au moment de la destruction de la classe.
    public function __destruct() {
        //mysql_close($this->_config['link']);
		mysqli_close ($this->_config['link']);
    }
	
	public function getLink() {
        //mysql_close($this->_config['link']);
		return $this->_config['link'];
    }

    // cr�ation d'une requete et historisation
    public function query ($sql, $desc=NULL) {
		$this->query = $this->_config['link']->query($sql);
        /*$this->query = @mysql_query ($sql, $this->_config['link'] );
        if (!$this->query) {
            throw new Exception (mysql_error() );
            return false;
        }*/

    }

    // creation de requette + resultat dans un tableau associatif
    public function queryS ($sql) {
        //$this->query = @mysql_query ($sql, $this->_config['link'] );
		$this->query = $this->_config['link']->query($sql);
        if ($this->query){
            $arrayResult = array();
            while ($row = mysqli_fetch_assoc ($this->query))
                $arrayResult[] = self::repererEtCorrigerMojibake($row);

            return $arrayResult;
        }
        else{
            throw new Exception (mysqli_error($this->_config['link']));
            return false;
        }

    }

    // r�cupere les r�sultats dans un tableau associatif
    public function fetch_assoc ($query=NULL) {
        if (isset($query)) {
            $this->query = $query;
        }
        return self::repererEtCorrigerMojibake($this->query->fetch_assoc());
    }

    // r�cupere les r�sultats dans un tableau normal
    public function fetch_row($query=NULL) {
        if (isset ($query)) {
            $this->query = $query;
        }
        return self::repererEtCorrigerMojibake(mysqli_fetch_row ($this->query));
    }

    // r�cupere les r�sultats dans un tableau associatif et/ou normal
    public function fetch_array($query=NULL) {
        if (isset ($query)) {
            $this->query = $query;
        }
		return self::repererEtCorrigerMojibake($this->query->fetch_array());
        //return mysql_fetch_array ($this->query);
    }

    // Une majorité des tables de cette base sont déclarées en charset "latin1" (héritage
    // historique) alors que la connexion mysqli négocie "utf8mb4" par défaut (aucun set_charset()
    // explicite n'est appelé) - MySQL "convertit" alors les octets déjà-UTF8 de ces colonnes
    // comme s'ils étaient du latin1, produisant du texte doublement encodé ("ComptabilitÃ©" au
    // lieu de "Comptabilité", "GÃ©rant" au lieu de "Gérant", etc.) sur TOUT le texte accentué
    // provenant de ces tables. Plutôt qu'une migration de charset risquée sur 58 tables en prod,
    // on répare le texte à la volée ici (seul point de passage commun à toute lecture) : l'inverse
    // du mojibake (UTF-8 -> ISO-8859-1) restaure les bons octets - et ne produit du texte UTF-8
    // valide QUE si la chaîne était effectivement doublement encodée, ce qui sert aussi de
    // détection : les quelques tables déjà correctement en utf8mb4 (ex: crm_releve_ligne) ne sont
    // jamais altérées, car leur texte, lui, n'est pas doublement encodé.
    private static function repererEtCorrigerMojibake($ligne)
    {
        if (!is_array($ligne)) {
            return $ligne;
        }
        foreach ($ligne as $cle => $valeur) {
            if (!is_string($valeur) || $valeur === '') {
                continue;
            }
            // Windows-1252 et non ISO-8859-1 : le charset "latin1" de MySQL est en réalité
            // du cp1252 (quirk MySQL connu), donc les octets 0x80-0x9F (guillemets typographiques,
            // tirets, €...) doivent être réinterprétés via cp1252 sous peine d'échouer le
            // round-trip (et donc de laisser passer le mojibake) sur tout texte qui les contient.
            $repare = @mb_convert_encoding($valeur, 'Windows-1252', 'UTF-8');
            if ($repare !== false && $repare !== $valeur && mb_check_encoding($repare, 'UTF-8') && mb_check_encoding($valeur, 'UTF-8')) {
                $ligne[$cle] = $repare;
            }
        }
        return $ligne;
    }

    // r�cupere le nombre d'enregistrement
    public function num_rows($query=NULL) {
        if (isset($query)){
            $this->query = $query;
        }
        return $this->query->num_rows;
    }

    // retourne le dernier id ins�rer
    public function last_id(){
        return mysqli_insert_id($this->_config['link']);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////:

    // insertion dans la base de donn�es Q = Quickly
    public function insertIntoQ ($table=NULL, $values=NULL) {
        if (isset($table) && isset($values)){
            $sql = "INSERT INTO ". $table . " VALUES (".$values.")";
            $this->query = @mysqli_query ($sql, $this->_config['link'] );
        }

        if (! $this->query) {
            throw new Exception (mysqli_error() );
            return false;
        }

    }

    // modification dans la table
    public function updateTable($table=NULL, $newValues=NULL, $where=NULL){
        if (isset($table) && isset($newValues)){
            if (!isset($where)){
                $where = 1;
            }
            $sql = "UPDATE ". $table . " SET ".$newValues." WHERE ".$where;
            $this->query = @mysqli_query ($sql, $this->_config['link'] );
        }

        if (! $this->query) {
            throw new Exception (mysqli_error() );
            return false;
        }
    }

    // suppression d'un enregistrement
    public function deleteTable($table=NULL, $where){
        if (isset($table) && isset($where)){
            $sql = "DELETE FROM ". $table . " WHERE ".$where;
            $this->query = @mysqli_query ($sql, $this->_config['link'] );
        }

        if (! $this->query) {
            throw new Exception (mysqli_error() );
            return false;
        }
    }
}