<?php

class expertise
{
    static $table =  __prefixe_db__ . "expertise";
    static $table2 =  __prefixe_db__ . "details_expertise";

    private $id;
    private $slug;
    private $parent;
    private $photo;

    private $ordre;
    private $active;
    private $titre;
    private $sous_titre;

    private $extrait;
    private $texte;
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

    public function getSlug()
    {
        return $this->slug;
    }

    public function getParent()
    {
        return $this->parent;
    }


    public function isActive()
    {
        return $this->active ? 1 : 0;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getPhoto()
    {
        return $this->photo;
    }


    public function getOrdre()
    {
        return $this->ordre;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getSousTitre()
    {
        return $this->sous_titre;
    }


    public function getTexte()
    {
        return $this->texte;
    }



    public function getExtrait()
    {
        return $this->extrait;
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

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }



    public function setActive($active)
    {
        $this->active = $active;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }



    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setSousTitre($sous_titre)
    {
        $this->sous_titre = $sous_titre;
    }


    public function setTexte($texte)
    {
        $this->texte = $texte;
    }

    public function setExtrait($extrait)
    {
        $this->extrait = $extrait;
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
            "INSERT INTO " . static::$table . " (slug, id_parent, photo,ordre, active, date_add, last_edit) VALUES (%s, %s, %s,  %s, %s, %s, %s)",
            GetSQLValueString($this->slug, "text"),
            GetSQLValueString($this->parent->getId(), "int"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );

        if (!$db->query($SQLinsert)) {
            $id_expertise = $db->last_id();
            $SQLinsert2 = sprintf(
                "INSERT INTO " . static::$table2 . " (id_expertise, titre, sous_titre,  extrait, texte, langue) VALUES (%s, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_expertise, "int"),
                GetSQLValueString($this->titre, "text"),
                GetSQLValueString($this->sous_titre, "text"),
                GetSQLValueString($this->extrait, "text"),
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
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  slug = %s, id_parent = %s, photo = %s, ordre = %s, active = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->slug, "text"),
            GetSQLValueString($this->parent->getId(), "int"),
            GetSQLValueString($this->photo, "text"),
            GetSQLValueString($this->ordre, "int"),
            GetSQLValueString($this->active, "int"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->id, "int")
        );
        if (!$db->query($SQLupdate)) {
            $SQLselect = sprintf(
                "SELECT * FROM " . static::$table2 . " WHERE id_expertise = %s AND langue = %s",
                GetSQLValueString($this->id, "int"),
                GetSQLValueString($this->langue, "text")
            );

            $result = $db->query($SQLselect);
            if ($db->num_rows($result) == 0) {
                $SQLupdate = sprintf(
                    "INSERT INTO " . static::$table2 . " (id_expertise, titre, sous_titre,extrait, texte, langue) VALUES (%s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($this->id, "int"),
                    GetSQLValueString($this->titre, "text"),
                    GetSQLValueString($this->sous_titre, "text"),
                    GetSQLValueString($this->extrait, "text"),
                    GetSQLValueString($this->texte, "text"),
                    GetSQLValueString($this->langue, "text")
                );
            } else {
                $SQLupdate = sprintf(
                    "UPDATE " . static::$table2 . " SET titre = %s, sous_titre = %s,  extrait = %s, texte = %s WHERE id_expertise = %s AND langue = %s",
                    GetSQLValueString($this->titre, "text"),
                    GetSQLValueString($this->sous_titre, "text"),
                    GetSQLValueString($this->extrait, "text"),
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
        if ($this->hasChildren()) {
            $children = $this->getChildren($_SESSION["langue"]);
            foreach ($children as $child) {
                $parent = new expertise();
                $child->setParent($parent);
                $child->editParent();
            }
        }
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
            GetSQLValueString($this->getId(), "int")
        );
        $SQLdelete2 = sprintf(
            "DELETE FROM " . static::$table2 . " WHERE id_expertise = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
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
            GetSQLValueString($this->getActive(), "int"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function editParent()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET id_parent = %s WHERE id = %s",
            GetSQLValueString($this->getParent()->getId(), "int"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function hasChildren($active = false)
    {
        global $db;
        $SQLcount = sprintf(
            "SELECT count(id) as c FROM " . static::$table . " WHERE id_parent = %s",
            GetSQLValueString($this->getId(), "int")
        );
        if ($active) {
            $SQLcount .= " AND active = 1";
        }
        $result = $db->query($SQLcount);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            return $data["c"] > 0 ? true : false;
        }
        return false;
    }

    public function getChildren($langue, $active = false, $ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_expertise AND langue = %s WHERE 1 = 1 AND id_parent = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($this->id, "int")
        );
        if ($active) {
            $SQLselect .= " AND active = 1";
        }

        if ($ordre) {
            $SQLselect .= " ORDER BY ordre ASC";
        }

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $expertise = static::build($data);
            array_push($items, $expertise);
        }
        return $items;
    }

    public function getLink()
    {
        global $siteURL;
        if ($this->titre != "") {
            if (langue::isLangueDefault($this->langue)) {
                return $siteURL . __CLASS__ . "/" . url_rewriting($this->titre) . "/" . $this->id . "/";
            } else {
                return $siteURL . $this->getLangue() . "/" . __CLASS__ . "/" . url_rewriting($this->getTitre()) . "/" . $this->id . "/";
            }
        } else
            return "index.php?option=com_" . __CLASS__ . "&id=" . $this->id;
    }

    public function getThankYouPageLink()
    {
        global $siteURL;
        if ($this->titre != "") {
            if (langue::isLangueDefault($this->langue)) {
                return $siteURL . __CLASS__ . "/" . url_rewriting($this->titre) . "/confirm/" . $this->id . "/";
            } else {
                return $siteURL . $this->getLangue() . "/" . __CLASS__ . "/" . url_rewriting($this->getTitre()) . "/confirm/" . $this->id . "/";
            }
        } else
            return "index.php?option=com_" . __CLASS__ . "&task=thankYou&id=" . $this->id;
    }



    public function getItems($elements, $lang)
    {
        $items = [];
        foreach ($elements as $element) {
            $items = $element::findAllByExpertise($lang, $this->id);
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
        $expertise = new expertise();
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_expertise AND langue = %s WHERE A.id = %s",
            GetSQLValueString($langue, "text"),
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $expertise = static::build($data);
        }
        return $expertise;
    }

    public static function findAll($langue, $active = false, $parent = false, $ordre = false)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf(
            "SELECT A.id as ID, A.*, B.* FROM " . static::$table . " A LEFT JOIN " . static::$table2 . " B ON A.id = B.id_expertise AND langue = %s WHERE 1 = 1",
            GetSQLValueString($langue, "text")
        );
        if ($active) {
            $SQLselect .= " AND active = 1";
        }
        if ($parent) {
            $SQLselect .= " AND (id_parent = 0 || id_parent = NULL)";
        }
        if ($ordre) {
            $SQLselect .= " ORDER BY ordre ASC";
        }

        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $expertise = static::build($data);
            array_push($items, $expertise);
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

            foreach ($result as $data) {
                $photos[] = $data["photo"];
            }
            return $photos;
        }
    }

    public static function build($data)
    {
        global $db;
        $expertise = new expertise();

        $expertise->setId($data['ID']);
        $expertise->setSlug($data['slug']);
        $expertise->setParent(expertise::find($data['id_parent'], $data["langue"]));
        $expertise->setPhoto($data['photo']);
        $expertise->setOrdre($data['ordre']);
        $expertise->setActive($data['active']);
        $expertise->setTitre($data['titre']);
        $expertise->setSousTitre($data['sous_titre']);
        $expertise->setExtrait($data['extrait']);
        $expertise->setTexte($data['texte']);
        $expertise->setDateAdd($data['date_add']);
        $expertise->setLastEdit($data['last_edit']);
        $expertise->setLangue($data['langue']);
        return $expertise;
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
        if (isset($data['ids']) && !empty($data['ids']) && isset($data['active']) && $data['active'] != '') {
            extract($data);

            $SQLupdate = sprintf("UPDATE " . static::$table . " SET active = $active WHERE id in$ids");

            if (!$db->query($SQLupdate))
                return 1;
            else
                return 2;
        } else
            return 0;
    }

    public static function deleteMultiple($data)
    {

        global $db;
        if (isset($data['ids']) && !empty($data['ids'])) {
            extract($data);
            $SQLdelete = "DELETE FROM " . static::$table . " WHERE id in $ids";
            $SQLdelete2 = "DELETE FROM " . static::$table2 . " WHERE id_expertise in $ids";
            if (!$db->query($SQLdelete) && !$db->query($SQLdelete2)) {
                //seo();
                return 1;
            } else
                return 2;
        } else
            return 0;
    }
    public function toArray()
    {
        global $siteURL;
        $data = array(
            'id' => $this->getId(),
            'titre' => $this->getTitre(),
            'extrait' => $this->getExtrait(),
            'texte' => $this->getTexte(),
            'image' => $siteURL . 'images/expertises/' . $this->getPhoto(),
            'ordre' => $this->getOrdre(),
            'active' => $this->getActive(),
            'langue' => $this->getLangue()
        );
        return $data;
    }
}
