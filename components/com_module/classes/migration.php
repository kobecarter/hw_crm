<?php

class migration
{
    private $nom_table;
    private $attributs;

    public function __construct(){
        $this->nom_table = "";
        $this->attributs = array();
        return $this;
    }

    public function init(){
        $this->nom_table = "";
        $this->attributs = array();
        return $this;
    }

    public function table($nom_table){
        $this->nom_table = $nom_table;
        return $this;
    }

    public function addColumn($colonne, $definition){
        $param = array(
            "colonne" => $colonne,
            "definition" => $definition,
            "operation" => "add"
        );
        array_push($this->attributs, $param);
        return $this;
    }

    public function modifyColumn($colonne, $definition){
        $param = array(
            "colonne" => $colonne,
            "definition" => $definition,
            "operation" => "modify"
        );
        array_push($this->attributs, $param);
        return $this;
    }

    public function changeColumn($colonneOld, $colonneNew, $definition){
        $param = array(
            "colonneOld" => $colonneOld,
            "colonneNew" => $colonneNew,
            "definition" => $definition,
            "operation" => "change"
        );
        array_push($this->attributs, $param);
        return $this;
    }

    public function dropColumn($colonne){
        $param = array(
            "colonne" => $colonne,
            "operation" => "drop"
        );
        array_push($this->attributs, $param);
        return $this;
    }

    public function exec(){
        if(count($this->attributs)) {
            global $db;
            $sql = "ALTER TABLE " . __prefixe_db__ . $this->nom_table;
            foreach ($this->attributs as $attribut) {
                switch ($attribut["operation"]) {
                    case "add":
                        $sql .= " ADD " . $attribut["colonne"] . " " . $attribut["definition"];
                        $semicolon = end($this->attributs) == $attribut ? ";" : ",";
                        $sql .= $semicolon;
                        break;
                    case "modify":
                        $sql .= " MODIFY " . $attribut["colonne"] . " " . $attribut["definition"];
                        $semicolon = end($this->attributs) == $attribut ? ";" : ",";
                        $sql .= $semicolon;
                        break;
                    case "change":
                        $sql .= " CHANGE " . $attribut["colonneOld"] . " " . $attribut["colonneNew"] . " " . $attribut["definition"];
                        $semicolon = end($this->attributs) == $attribut ? ";" : ",";
                        $sql .= $semicolon;
                        break;
                    case "drop":
                        $sql .= " DROP " . $attribut["colonne"];
                        $semicolon = end($this->attributs) == $attribut ? ";" : ", ";
                        $sql .= $semicolon;
                        break;
                }
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

}