<?php
abstract class dbfactory {

    private static $instance;                   // Instance courante de la classe

    protected $_config;                         // Parametres de configuration base de donn�e.
    protected $query;                           // Ressource de query

    public $history = array();                  // Historique des requetes
    public $query_id = 0;                       // Compteur de requetes.


    // Initialise les variables de connections et active la connection a la base de donn�e.
    // Constructeur prot�g� permettant de n'avoir qu'une unique instance de la classe gr�ce a la m�thode singleton
    protected function __construct ($host=NULL, $user=NULL, $passwd=NULL, $name=NULL) {
        if ( !is_array($this->default_cfg) ) {
            throw new Exception('Vous devez remplir les parametres de la configuration par defaut de votre base de donn�e');
        }

        foreach ($this->default_cfg as $key=>$val ) {
            $this->_config[$key] = (isset($$key) ) ? $$key : $val;
        }

        unset($this->default_cfg); // Enleve les parametres par defaut pour �viter toute confusion possible.
        $this->connect();
    }

    // usinage : permet d'instancier la classe correcte en fonction de la db choisie
    public static function factory ($type, $host=NULL, $user=NULL, $passwd=NULL, $name=NULL) {
        if (class_exists ($type)) {
            $className = $type;
            return new $className ($host, $user, $passwd, $name);
        } else {
            throw new Exception ('Pas d\'impl�mentation disponible pour ' . $type);
        }
    }

    // n'autorise qu'une seule instance de la classe
    public static function singleton () {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    // on avertit le d�veloppeur qu'il n'a pas le droit de cloner l'objet instanci�
    public function __clone() {
        trigger_error('Le cl�nage n\'est pas autoris�.', E_USER_ERROR);
    }

    // m�thodes abstraites
    abstract protected function connect();
    abstract public function __destruct();
    abstract public function query($sql, $desc=NULL);
    abstract public function fetch_assoc($query=NULL);
    abstract public function num_rows($query=NULL);
    abstract public function fetch_row($query=NULL);
    abstract public function fetch_array($query=NULL);
    abstract public function insertIntoQ($table=NULL, $values=NULL);
    abstract public function updateTable($table=NULL, $newValues=NULL, $where=NULL);
    abstract public function deleteTable($table=NULL, $where);
    abstract public function queryS ($sql);
    abstract public function last_id();
}