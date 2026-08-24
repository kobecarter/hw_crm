<?php

class temoignage
{
    static $table =  __prefixe_db__ . "temoignage";
    static $table2 =  __prefixe_db__ . "details_temoignage";

    private $id;
    private $id_client;
    private $photo;
    private $active;
    private $note;
    private $ordre;
    private $nom;
    private $fonction;
    private $email;
    private $sujet;
    private $temoignage;
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

    public function getPhoto()
    {
        return $this->photo;
    }

    public function isActive()
    {
        return $this->active ? 1 : 0;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getOrdre()
    {
        return $this->ordre;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getFonction()
    {
        return $this->fonction;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSujet()
    {
        return $this->sujet;
    }

    public function getTemoignage()
    {
        return $this->temoignage;
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

    public function getIdClient()
    {
        return $this->id_client;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setIdClient($id_client)
    {
        $this->id_client = $id_client;
    }

    public function setNote($note)
    {
        $this->note = $note;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setFonction($fonction)
    {
        $this->fonction = $fonction;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setSujet($sujet)
    {
        $this->sujet = $sujet;
    }

    public function setTemoignage($temoignage)
    {
        $this->temoignage = $temoignage;
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

        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (photo, active, ordre, date_add, last_edit) VALUES (%s, %s, %s, %s, %s)",
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );

        if (!$db->query($SQLinsert)) {
            $id_temoignage = $db->last_id();
            $SQLinsert2 = sprintf(
                "INSERT INTO " . static::$table2 . " (id_temoignage, nom, fonction, email, sujet, temoignage, langue) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_temoignage, "int"),
                GetSQLValueString($this->nom, "text"),
                GetSQLValueString($this->fonction, "text"),
                GetSQLValueString($this->email, "text"),
                GetSQLValueString($this->sujet, "text"),
                GetSQLValueString($this->temoignage, "text"),
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  photo = %s, active = %s, ordre = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->id, "int")
        );

        if (!$db->query($SQLupdate)) {
            $SQLselect = sprintf(
                "SELECT * FROM " . static::$table2 . " WHERE id_temoignage = %s AND langue = %s",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->langue, "text")
            );
            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 0) {
                $SQLupdate = sprintf(
                    "INSERT INTO " . static::$table2 . " (id_temoignage, nom, fonction, email, sujet, temoignage, langue) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($this->id, "int"),
                    GetSQLValueString($this->nom, "text"),
                    GetSQLValueString($this->fonction, "text"),
                    GetSQLValueString($this->email, "text"),
                    GetSQLValueString($this->sujet, "text"),
                    GetSQLValueString($this->temoignage, "text"),
                    GetSQLValueString($this->langue, "text")
                );
            } else {
                $SQLupdate = sprintf(
                    "UPDATE " . static::$table2 . " SET nom = %s, fonction = %s, email = %s, sujet = %s, temoignage = %s WHERE id_temoignage = %s AND langue = %s",
                    GetSQLValueString($this->nom, "text"),
                    GetSQLValueString($this->fonction, "text"),
                    GetSQLValueString($this->email, "text"),
                    GetSQLValueString($this->sujet, "text"),
                    GetSQLValueString($this->temoignage, "text"),
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

        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->id, "int")
        );
        $SQLdelete2 = sprintf(
            "DELETE FROM " . static::$table2 . " WHERE id_temoignage = %s",
            GetSQLValueString($this->id, "int")
        );

        if (!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
            @unlink("../../../images/temoignages/" . $this->getPhoto());
            return 1;
        } else {
            return 0;
        }
    }

    public function enable()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET active = %s WHERE id = %s",
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->id, "int")
        );

        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getLink()
    {
        global $siteURL;
        if ($this->nom != "") {
            if (langue::isLangueDefault($this->langue)) {
                return $siteURL . __CLASS__ . "/" . url_rewriting($this->nom) . "/" . $this->id . "/";
            } else {
                return $siteURL . $this->langue . "/" . __CLASS__ . "/" . url_rewriting($this->nom) . "/" . $this->id . "/";
            }
        } else
            return "index.php?option=com_" . __CLASS__ . "&id=" . $this->id;
    }

    public static function getSeo()
    {
        $url = "";
        $url .= "RewriteRule ^" . __CLASS__ . "/([a-zA-Z0-9_-]+)/([0-9]+)/$ index.php?option=com_" . __CLASS__ . "&task=showDetails&id=$2 [NC,L]
        RewriteRule ^([a-z]+)/" . __CLASS__ . "/([a-zA-Z0-9_-]+)/([0-9]+)/$ index.php?option=com_" . __CLASS__ . "&task=showDetails&l=$1&id=$3 [NC,L]
        ";
        return $url;
    }

    public function getItems($elements, $lang)
    {
        $items = [];
        foreach ($elements as $element) {
            $items = $element::findAllByTemoignage($lang, $this->id);
            if (count($items)) {
                array_push($items, $element);
                break;
            }
        }
        return $items;
    }

    public static function find($id, $langue = 'fr')
    {
        global $db;
        $temoignage = new temoignage();
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_temoignage AND langue = %s WHERE A.id = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id, "int")
        );

        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $temoignage = static::build($data);
        }
        return $temoignage;
    }

    public static function findAll($langue, $active = false, $ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_temoignage AND langue = %s WHERE 1 = 1",
            GetSQLValueString($langue, "text")
        );

        if ($active) {
            $SQLselect .= " AND active = 1";
        }

        if ($ordre) {
            $SQLselect .= " ORDER BY ordre ASC";
        }

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $temoignage = static::build($data);
            array_push($items, $temoignage);
        }
        return $items;
    }

    public static function findPhotosName($data)
    {
        global $db;
        $photos = [];
        if (isset($data['ids']) && !empty($data['ids'])) {
            $SQLselect = sprintf(
                "SELECT photo FROM " . static::$table . " WHERE id in%s",
                GetSQLValueString($data['ids'], "text")
            );

            $result = $db->queryS($SQLselect);

            foreach ($result as $photo) {
                $photos[] = $photo;
            }
            return $photos;
        }
    }

    public static function build($data)
    {
        $temoignage = new temoignage();

        $temoignage->setId($data['ID']);
        $temoignage->setIdClient(isset($data['id_client']) ? $data['id_client'] : null);
        $temoignage->setPhoto($data['photo']);
        $temoignage->setActive($data['active']);
        $temoignage->setNote(isset($data['note']) ? $data['note'] : null);
        $temoignage->setOrdre($data['ordre']);
        $temoignage->setNom($data['nom']);
        $temoignage->setFonction($data['fonction']);
        $temoignage->setEmail($data['email']);
        $temoignage->setSujet($data['sujet']);
        $temoignage->setTemoignage($data['temoignage']);
        $temoignage->setDateAdd($data['date_add']);
        $temoignage->setLastEdit($data['last_edit']);
        $temoignage->setLangue($data['langue']);

        return $temoignage;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    public static function count()
    {
        global $db;
        $SQLcount = "SELECT count(id) as c FROM " . static::$table;
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"];
        }
        return 0;
    }

    public static function enableMultiple($data)
    {
        global $db;
        extract($data);
        $good = true;
        $ids = substr($ids, 1, strlen($ids) - 2);
        $ids = explode(",", $ids);
        foreach ($ids as $id) {
            $SQLupdate = sprintf("UPDATE " . static::$table . " SET active = $active WHERE id = $id");
            if ($db->query($SQLupdate)) {
                $good = false;
                break;
            }
        }
        if ($good) {
            return 1;
        } else
            return 0;
    }

    public static function deleteMultiple($data)
    {

        global $db;
        if (isset($data['ids']) && !empty($data['ids'])) {
            extract($data);
            $SQLdelete = "DELETE FROM " . static::$table . " WHERE id in $ids";
            $SQLdelete2 = "DELETE FROM " . static::$table2 . " WHERE id_temoignage in $ids";
            if (!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
                return 1;
            } else
                return 2;
        } else
            return 0;
    }

    public  function toArray()
    {

        global $siteURL;
        return array(
            'id' => $this->getId(),
            'photo' => $this->getPhoto() ? $siteURL . 'images/temoignages/' . $this->getPhoto() : null,
            'author' => $this->getNom(),
            'fonction' => $this->getFonction(),
            'testimonial' => $this->getTemoignage(),
            'note' => $this->getNote() !== null ? (int) $this->getNote() : null,
            'active' => (bool) $this->getActive(),
        );
    }

    // API - un client n'a jamais qu'un seul témoignage : crée s'il n'en a pas
    // encore, sinon édite le sien (repasse en modération à chaque modification).
    public static function findByClient($clientId, $langue = 'fr')
    {
        global $db;
        $temoignage = null;
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_temoignage AND langue = %s WHERE A.id_client = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($clientId, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $temoignage = static::build($data);
        }
        return $temoignage;
    }

    public static function createOrUpdateApi($client, $data)
    {
        global $db;
        $langue = 'fr';
        $existing = static::findByClient($client->getId(), $langue);
        $nom = trim($client->getPrenom() . ' ' . $client->getNom());
        if ($nom === '') {
            $nom = $client->getRaisonSocial();
        }

        if ($existing) {
            $SQLupdate = sprintf(
                "UPDATE " . static::$table . " SET note = %s, active = 0, last_edit = %s WHERE id = %s",
                GetSQLValueString($data['note'], "int"),
                GetSQLValueString(date('Y-m-d'), "date"),
                GetSQLValueString($existing->getId(), "int")
            );
            $db->query($SQLupdate);
            $SQLselectDetails = sprintf(
                "SELECT id FROM " . static::$table2 . " WHERE id_temoignage = %s AND langue = %s",
                GetSQLValueString($existing->getId(), "int"),
                GetSQLValueString($langue, "text")
            );
            $resultDetails = $db->query($SQLselectDetails);
            if ($db->num_rows($resultDetails) == 1) {
                $SQLupdateDetails = sprintf(
                    "UPDATE " . static::$table2 . " SET fonction = %s, sujet = %s, temoignage = %s WHERE id_temoignage = %s AND langue = %s",
                    GetSQLValueString($data['fonction'], "text"),
                    GetSQLValueString(isset($data['sujet']) ? $data['sujet'] : '', "text"),
                    GetSQLValueString($data['temoignage'], "text"),
                    GetSQLValueString($existing->getId(), "int"),
                    GetSQLValueString($langue, "text")
                );
                $db->query($SQLupdateDetails);
            } else {
                $SQLinsertDetails = sprintf(
                    "INSERT INTO " . static::$table2 . " (id_temoignage, nom, fonction, email, sujet, temoignage, langue) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($existing->getId(), "int"),
                    GetSQLValueString($nom, "text"),
                    GetSQLValueString($data['fonction'], "text"),
                    GetSQLValueString($client->getEmail(), "text"),
                    GetSQLValueString(isset($data['sujet']) ? $data['sujet'] : '', "text"),
                    GetSQLValueString($data['temoignage'], "text"),
                    GetSQLValueString($langue, "text")
                );
                $db->query($SQLinsertDetails);
            }
            return true;
        }

        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (id_client, active, note, ordre, date_add, last_edit) VALUES (%s, 0, %s, 0, %s, %s)",
            GetSQLValueString($client->getId(), "int"),
            GetSQLValueString($data['note'], "int"),
            GetSQLValueString(date('Y-m-d'), "date"),
            GetSQLValueString(date('Y-m-d'), "date")
        );
        // Note : la méthode query() de ce wrapper ne renvoie jamais rien (toujours NULL),
        // succès ou échec - un échec SQL réel remonte comme exception, pas comme valeur
        // de retour falsy. On ne peut donc pas tester son retour ici (cf. même piège dans
        // add()/edit() plus haut dans ce fichier, où le "else" est du code mort).
        $db->query($SQLinsert);
        $idTemoignage = $db->last_id();
        $SQLinsertDetails = sprintf(
            "INSERT INTO " . static::$table2 . " (id_temoignage, nom, fonction, email, sujet, temoignage, langue) VALUES (%s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($idTemoignage, "int"),
            GetSQLValueString($nom, "text"),
            GetSQLValueString($data['fonction'], "text"),
            GetSQLValueString($client->getEmail(), "text"),
            GetSQLValueString(isset($data['sujet']) ? $data['sujet'] : '', "text"),
            GetSQLValueString($data['temoignage'], "text"),
            GetSQLValueString($langue, "text")
        );
        $db->query($SQLinsertDetails);
        return true;
    }
}
