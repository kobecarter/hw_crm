<?php

class installation
{
    private $nom_table;
    private $attributs;
    private $id_module;
    private $valeurs;
    private $fichiers;

    public function __construct()
    {
        $this->nom_table = "";
        $this->attributs = array();
        $this->id_module = "";
        $this->valeurs = array();
        $this->fichiers = array();
        return $this;
    }

    public function init()
    {
        $this->nom_table = "";
        $this->attributs = array();
        $this->id_module = "";
        $this->valeurs = array();
        $this->fichiers = array();
        return $this;
    }

    public function table($nom_table)
    {
        $this->nom_table = $nom_table;
        return $this;
    }

    public function column($colonne, $definition)
    {
        $param = array(
            "colonne" => $colonne,
            "definition" => $definition,
        );
        array_push($this->attributs, $param);
        return $this;
    }

    public function module($id_module)
    {
        $this->id_module = $id_module;
        return $this;
    }

    public function value($colonne, $type, $valeur)
    {
        $param = array(
            "colonne" => $colonne,
            "type" => $type,
            "valeur" => $valeur,
        );
        array_push($this->valeurs, $param);
        return $this;
    }

    public function create()
    {
        if (count($this->attributs)) {
            global $db;
            $sql = "CREATE TABLE IF NOT EXISTS " . __prefixe_db__ . $this->nom_table . " (";
            $sql .= "id INT NOT NULL auto_increment, ";
            foreach ($this->attributs as $attribut) {
                $sql .= $attribut["colonne"] . " " . $attribut["definition"] . ", ";
            }
            $sql .= " PRIMARY KEY(id) )";
            if (!$db->query($sql)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function drop()
    {
        if ($this->nom_table) {
            global $db;
            $sql = "DROP TABLE IF EXISTS " .  __prefixe_db__ . $this->nom_table;
            if (!$db->query($sql)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function insert()
    {
        if ($this->nom_table) {
            global $db;
            $sql = "INSERT INTO " . __prefixe_db__ . $this->nom_table;
            $colonnes = "(";
            $valeurs = "(";
            foreach ($this->valeurs as $valeur) {
                $semicolon = end($this->valeurs) == $valeur ? ")" : ", ";
                $colonnes .=  $valeur["colonne"];
                $colonnes .= $semicolon;
                $valeurs .= GetSQLValueString($valeur["valeur"], $valeur["type"]);
                $valeurs .= $semicolon;
            }
            $sql = $sql . " " . $colonnes . " VALUES " . $valeurs;
            if (!$db->query($sql)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function addPermissions()
    {
        if ($this->id_module) {
            global $db;
            $permessions = array("view", "add", "edit", "delete");
            $sql = "INSERT INTO " . __prefixe_db__ . "droits (id_profil, module, action) VALUES ";
            foreach ($permessions as $permession) {
                $sql .= "(1, '" . $this->id_module . "', '" . $permession . "')";
                $semicolon = end($permessions) == $permession ? ";" : ", ";
                $sql .= $semicolon;
            }
            if (!$db->query($sql)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function revokePermissions()
    {
        if ($this->id_module) {
            global $db;
            $sql = "DELETE FROM " . __prefixe_db__ . "droits WHERE module = '" . $this->id_module . "'";
            if (!$db->query($sql)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function file($file_name, $type)
    {
        $param = array(
            "file_name" => $file_name,
            "type" => $type
        );
        array_push($this->fichiers, $param);
        return $this;
    }

    public function fileCreate()
    {
        if (count($this->fichiers)) {
            foreach ($this->fichiers as $fichier) {
                switch ($fichier["type"]) {
                    case "images":
                        if (!file_exists("../../../images/" . $fichier["file_name"])) {
                            mkdir("../../../images/" . $fichier["file_name"]);
                        }
                        break;
                    case "images-sys":
                        if (!file_exists("../images/" . $fichier["file_name"])) {
                            mkdir("../images/" . $fichier["file_name"]);
                        }
                        break;
                    case "module":
                        if (!file_exists("../../../../components/" . $fichier["file_name"] . "/" . $fichier["file_name"])) {
                            copy_recursive("../../../components/" . $fichier["file_name"] . "/" . $fichier["file_name"], "../../../../components/" . $fichier["file_name"]);
                            rmdir_recursive("../../../components/" . $fichier["file_name"] . "/" . $fichier["file_name"]);
                        }
                        break;
                }
            }
        }
    }

    public function fileRemove()
    {
        if (count($this->fichiers)) {
            foreach ($this->fichiers as $fichier) {
                switch ($fichier["type"]) {
                    case "images":
                    case "images-sys":
                        if (file_exists("../../../../images/" . $fichier["file_name"])) {
                            rmdir_recursive("../../../../images/" . $fichier["file_name"]);
                        }
                        break;
                    case "module":
                        if (file_exists("../../../../components/" . $fichier["file_name"])) {
                            copy_recursive("../../../../components/" . $fichier["file_name"], "../../../components/" . $fichier["file_name"] . "/" . $fichier["file_name"]);
                            rmdir_recursive("../../../../components/" . $fichier["file_name"]);
                        }
                        break;
                }
            }
        }
    }
}
