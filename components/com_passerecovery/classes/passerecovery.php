<?php

class passerecovery
{
    static $table =  __prefixe_db__ . "passerecovery";
    static $table2 =  __prefixe_db__ . "client";


    private $id;
    private $id_client;
    private $code;
    private const CODE_SALT = "{@2012-22HW/A";
    private const EXPIRE_DURATION_SEC = 600;
    private const CODE_LEGNTH = 8;
    private $date_add;
    private $last_edit;



    public function __construct($id_client = 0)
    {
        $this->id_client = $id_client;
    }

    public function __destruct()
    {
    }



    public function getId()
    {
        return $this->id;
    }

    public function getClientId()
    {
        return $this->id_client;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getAddDate()
    {
        return $this->date_add;
    }

    public function getUpdateDate()
    {
        return $this->last_edit;
    }

    public function getExpireTimestamp()
    {
        return date_timestamp_get(date_create($this->getAddDate())) + self::EXPIRE_DURATION_SEC;
    }

    public function isExpired()
    {
        if ($this->getExpireTimestamp() <= time()) {
            $this->delete();
            return true;
        } else {
            return false;
        }
    }

    public function isValid()
    {
        return !$this->isExpired();
    }


    public function setId($id)
    {
        $this->id = $id;
    }
    public function setIdClient($id_client)
    {
        $this->id_client = $id_client;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setAddDate($date_add)
    {
        $this->date_add = $date_add;
    }


    public function setUpdateDate($last_edit)
    {
        $this->last_edit = $last_edit;
    }


    public function  generateRawCode()
    {
        for ($i = 0; $i < self::CODE_LEGNTH; $i++) {
            $this->code .= rand(0, 9);
        }
        return $this->code;
    }


    public function generateCode()
    {
        $this->code = hash('sha256', $this->code . self::CODE_SALT);
    }

    private static function hashCode($code)
    {
        return hash('sha256', $code . self::CODE_SALT);
    }

    public function save()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, code, date_add, last_edit) VALUES (%s, %s, %s, %s)",
            GetSQLValueString($this->id_client, "int"),
            GetSQLValueString($this->code, "text"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date"),
        );

        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE id = %d",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {

            $data = $db->fetch_assoc($result);
            $passerecovery = new passerecovery();
            $passerecovery->setId($data['id']);
            $passerecovery->setIdClient($data['id_client']);
            $passerecovery->setCode($data['code']);
            $passerecovery->setAddDate($data['date_add']);
            $passerecovery->setUpdateDate($data['last_edit']);

            return $passerecovery;
        }
        return null;
    }
    public static function findByCode($code)
    {
        global $db;
        $code = self::hashCode($code);
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table . " WHERE code = %s",
            GetSQLValueString($code, "text")
        );
        $result = $db->query($SQLselect);

        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $passerecovery = new passerecovery();
            $passerecovery->setId($data['id']);
            $passerecovery->setIdClient($data['id_client']);
            $passerecovery->setCode($data['code']);
            $passerecovery->setAddDate($data['date_add']);
            return $passerecovery;
        }
        return null;
    }


    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET id_client = %d, code = '%s', last_edit = '%s' WHERE id_client = %d",
            GetSQLValueString($this->id, "int"),
            GetSQLValueString($this->id_client, "int"),
            GetSQLValueString($this->code, "text"),
            GetSQLValueString(date('Y-m-d H:i:s'), "date"),
            GetSQLValueString($this->id_client, "int")

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
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %d",
            GetSQLValueString($this->id, "int")
        );

        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getClient()
    {

        global $db;
        $SQLselect = "SELECT * FROM " . self::$table2 . " WHERE id_client = '" . $this->id_client . "'";
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $client =  client::find($data['id_client']);
            return $client;
        }
        return null;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'id_client' => $this->id_client,
            'code' => $this->code,
            'date_add' => $this->date_add,
            'last_edit' => $this->last_edit,
        );
    }
}
