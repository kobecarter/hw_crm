<?php

class marketingPlan
{
    static $table = __prefixe_db__ . "marketing_plan";

    const STATUT_BROUILLON = 0;
    const STATUT_SOUMIS = 1;

    private $id;
    private $client;
    private $description;
    private $briefJson;
    private $statut;
    private $dateAdd;
    private $dateSoumis;

    public function __construct()
    {
        $this->id = 0;
        $this->statut = self::STATUT_BROUILLON;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getClient()
    {
        return $this->client;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getBriefJson()
    {
        return $this->briefJson;
    }
    public function getStatut()
    {
        return $this->statut;
    }
    public function getDateAdd()
    {
        return $this->dateAdd;
    }
    public function getDateSoumis()
    {
        return $this->dateSoumis;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setClient($client)
    {
        $this->client = $client;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setBriefJson($briefJson)
    {
        $this->briefJson = $briefJson;
    }
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;
    }
    public function setDateSoumis($dateSoumis)
    {
        $this->dateSoumis = $dateSoumis;
    }

    public function add()
    {
        global $db;
        $sql = sprintf(
            "INSERT INTO " . static::$table . " (id_client, description, brief_json, statut, date_add) VALUES (%s, %s, %s, %s, %s)",
            GetSQLValueString($this->client->getId(), "int"),
            GetSQLValueString($this->description, "text"),
            GetSQLValueString($this->briefJson, "text"),
            GetSQLValueString($this->statut, "int"),
            GetSQLValueString(date('Y-m-d H:i:s'), "text")
        );
        $db->query($sql);
        $this->setId($db->last_id());
        return $this->id;
    }

    public function markSubmitted()
    {
        global $db;
        $sql = sprintf(
            "UPDATE " . static::$table . " SET statut = %s, date_soumis = %s WHERE id = %s",
            GetSQLValueString(self::STATUT_SOUMIS, "int"),
            GetSQLValueString(date('Y-m-d H:i:s'), "text"),
            GetSQLValueString($this->id, "int")
        );
        $db->query($sql);
    }

    public static function find($id, $clientId = null)
    {
        global $db;
        $plan = null;
        $sql = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($id, "int")
        );
        if ($clientId !== null) {
            $sql .= sprintf(" AND id_client = %s", GetSQLValueString($clientId, "int"));
        }
        $result = $db->query($sql);
        if ($db->num_rows($result) == 1) {
            $plan = static::build($db->fetch_assoc($result));
        }
        return $plan;
    }

    public static function findAllByClient($clientId)
    {
        global $db;
        $items = array();
        $sql = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id_client = %s ORDER BY date_add DESC",
            GetSQLValueString($clientId, "int")
        );
        $result = $db->queryS($sql);
        foreach ($result as $data) {
            $items[] = static::build($data);
        }
        return $items;
    }

    public static function build($data)
    {
        $plan = new marketingPlan();
        $plan->setId($data['id']);
        $plan->setClient(client::find($data['id_client'], $_SESSION['agence'] ?? 1));
        $plan->setDescription($data['description']);
        $plan->setBriefJson($data['brief_json']);
        $plan->setStatut($data['statut']);
        $plan->setDateAdd($data['date_add']);
        $plan->setDateSoumis(isset($data['date_soumis']) ? $data['date_soumis'] : null);
        return $plan;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'description' => $this->description,
            'brief' => $this->briefJson ? json_decode($this->briefJson, true) : null,
            'statut' => (int) $this->statut,
            'date_add' => $this->dateAdd,
            'date_soumis' => $this->dateSoumis,
        );
    }
}
