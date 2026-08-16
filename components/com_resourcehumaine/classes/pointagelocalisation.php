<?php

// Localisation du bureau (une seule ligne, id=1, même motif "singleton" que horairereference/
// crm_pointage_rappel) - remplace le contrôle Wi-Fi/IP (pointageIpAutorisee(), qui exige une IP
// publique fixe, payante chez Maroc Telecom) par un contrôle de position GPS/Wi-Fi fourni par le
// navigateur au moment du pointage (navigator.geolocation) : gratuit, insensible aux changements
// d'IP, vérifié côté serveur (distance réelle à vol d'oiseau, pas une simple confiance au client).
class pointagelocalisation
{
    static $table = __prefixe_db__ . "pointage_localisation";

    private $id;
    private $latitude;
    private $longitude;
    private $rayon_metres;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId(){ return $this->id; }
    public function getLatitude(){ return $this->latitude; }
    public function getLongitude(){ return $this->longitude; }
    public function getRayonMetres(){ return $this->rayon_metres; }
    public function getLastEdit(){ return $this->last_edit; }

    public function setId($id){ $this->id = $id; }
    public function setLatitude($v){ $this->latitude = $v; }
    public function setLongitude($v){ $this->longitude = $v; }
    public function setRayonMetres($v){ $this->rayon_metres = $v; }
    public function setLastEdit($v){ $this->last_edit = $v; }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf("UPDATE " . static::$table . " SET latitude = %s, longitude = %s, rayon_metres = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->latitude, "double"),
            GetSQLValueString($this->longitude, "double"),
            GetSQLValueString($this->rayon_metres, "int"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    // Toujours la ligne id=1 (seedée par la migration) - pas de add()/delete(), ce singleton
    // existe toujours.
    public static function find()
    {
        global $db;
        $item = new pointagelocalisation();
        $SQLselect = "SELECT * FROM " . static::$table . " WHERE id = 1";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $item = static::build($db->fetch_assoc($result));
        }
        return $item;
    }

    public static function build($data)
    {
        $item = new pointagelocalisation();
        $item->setId($data['id']);
        $item->setLatitude($data['latitude']);
        $item->setLongitude($data['longitude']);
        $item->setRayonMetres($data['rayon_metres']);
        $item->setLastEdit($data['last_edit']);
        return $item;
    }

    // Distance à vol d'oiseau entre deux points GPS (formule de Haversine), en mètres.
    public static function distanceMetres($lat1, $lng1, $lat2, $lng2)
    {
        $rayonTerreMetres = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $rayonTerreMetres * $c;
    }

    // "Cette position (envoyée par le navigateur de l'employé) est-elle à portée du bureau" -
    // utilisé par pointerWeb() en alternative au contrôle IP.
    public static function estDansLeRayon($latitude, $longitude)
    {
        $reference = self::find();
        if (!$reference || !$reference->getId()) {
            return false;
        }
        $distance = self::distanceMetres($latitude, $longitude, $reference->getLatitude(), $reference->getLongitude());
        return $distance <= $reference->getRayonMetres();
    }
}
