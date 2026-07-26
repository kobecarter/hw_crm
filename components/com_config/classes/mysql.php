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
                $arrayResult[] = $row;

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
        return  $this->query->fetch_assoc();
    }

    // r�cupere les r�sultats dans un tableau normal
    public function fetch_row($query=NULL) {
        if (isset ($query)) {
            $this->query = $query;
        }
        return mysqli_fetch_row ($this->query);
    }

    // r�cupere les r�sultats dans un tableau associatif et/ou normal
    public function fetch_array($query=NULL) {
        if (isset ($query)) {
            $this->query = $query;
        }
		return  $this->query->fetch_array();
        //return mysql_fetch_array ($this->query);
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