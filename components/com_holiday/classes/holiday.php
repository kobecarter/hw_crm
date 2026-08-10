<?php

class holiday
{
    static $table =  __prefixe_db__ . "holiday";

    private $id;
    private $name;
    private $start_date;
    private $end_date;
    private $remarque;
    private $date_add;
    private $last_edit;

    public function __construct()
    {
        $this->id = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStartDate()
    {
        return $this->start_date;
    }

    public function getEndDate()
    {
        return $this->end_date;
    }

    public function getRemarque()
    {
        return $this->remarque;
    }

    public function getDateAdd()
    {
        return $this->date_add;
    }

    public function getLastEdit()
    {
        return $this->last_edit;
    }

    public function getColor()
    {
        $today = strtotime(date('Y-m-d'));
        $startDateBeforeFive = strtotime($this->start_date." -6 days");
        $start_date = strtotime($this->start_date);
        $end_date = strtotime($this->end_date);

        if($today >= $startDateBeforeFive && $today < $start_date){
            return "table-warning";
        }else if($today >= $start_date && $today <= $end_date){
            return "table-success";
        }else if($today > $end_date){
            return "table-danger";
        }
        return "";
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;
    }

    public function setRemarque($seo_titre)
    {
        $this->remarque = $seo_titre;
    }

    public function setDateAdd($date_add)
    {
        $this->date_add = $date_add;
    }

    public function setLastEdit($last_edit)
    {
        $this->last_edit = $last_edit;
    }

    public function add()
    {
        global $db;
        $SQLinsert = sprintf(
            "INSERT INTO " . static::$table . " (name, start_date, end_date, remarque, date_add, last_edit) VALUES (%s, %s, %s, %s, %s, %s)",
            GetSQLValueString($this->name, "text"),
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->end_date, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->date_add, "date"),
            GetSQLValueString($this->last_edit, "date")
        );
        //echo $SQLinsert;
        
        if (!$db->query($SQLinsert)) {
            return 1;
        } else {
            return 2;
        }
    }

    public function edit()
    {
        global $db;
        $SQLupdate = sprintf(
            "UPDATE " . static::$table . " SET  name = %s, start_date = %s, end_date = %s, remarque = %s, last_edit = %s WHERE id = %s",
            GetSQLValueString($this->name, "text"),
            GetSQLValueString($this->start_date, "date"),
            GetSQLValueString($this->end_date, "date"),
            GetSQLValueString($this->remarque, "text"),
            GetSQLValueString($this->last_edit, "date"),
            GetSQLValueString($this->getId(), "int")
        );
        if (!$db->query($SQLupdate)) {
            return 1;
        } else {
            return 2;
        }
    }

    public function delete()
    {
        global $db;
        $SQLdelete = sprintf(
            "DELETE FROM " . static::$table . " WHERE id = %s",
                GetSQLValueString($this->getId(), "int")
            );
        if (!$db->query($SQLdelete)) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function find($id)
    {
        global $db;
        $holiday = new holiday();
        $SQLselect = sprintf(
            "SELECT * FROM " . static::$table ." where id = %s",
            GetSQLValueString($id, "int")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $holiday = static::build($data);
        }
        return $holiday;
    }

    public static function findAll()
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." order by id desc");
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $holiday = static::build($data);
            array_push($items, $holiday);
        }
        return $items;
    }

    public static function findByDate($date)
    {
        global $db;
        $holiday = new holiday();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where %s >= start_date and %s <= end_date order by id desc",
            GetSQLValueString($date, "date"),
            GetSQLValueString($date, "date")
        );
        $result = $db->query($SQLselect);
        if ($db->num_rows($result) == 1) {
            $data = $db->fetch_assoc($result);
            $holiday = static::build($data);
        }
        return $holiday;
    }

    public static function findByYear($year)
    {
        global $db;
        $items = array();
        $SQLselect = sprintf("SELECT * FROM " . static::$table." where start_date like %s order by id desc",
            GetSQLValueString($year."%", "text"),
        ); 
        $result = $db->queryS($SQLselect);
        foreach ($result as $data) {
            $holiday = static::build($data);
            array_push($items, $holiday);
        }
        return $items;
    }

    public static function build($data)
    {
        $holiday = new holiday();
        $holiday->setId($data['id']);
        $holiday->setName($data['name']);
        $holiday->setStartDate($data['start_date']);
        $holiday->setEndDate($data['end_date']);
        $holiday->setRemarque($data['remarque']);
        $holiday->setDateAdd($data['date_add']);
        $holiday->setLastEdit($data['last_edit']);
        return $holiday;
    }

    public static function getLastId()
    {
        global $db;
        return $db->last_id();
    }

    // Convertit un holiday en événement FullCalendar. La couleur reprend la même
    // logique que getColor() (proche = orange, en cours = vert, passé = gris,
    // lointain = violet de la marque) pour rester cohérent avec la liste.
    public static function toCalendarEvent($holiday)
    {
        $colorMap = array(
            'table-warning' => '#f59e0b',
            'table-success' => '#10b981',
            'table-danger' => '#94a3b8',
            '' => '#6366f1'
        );
        $color = $colorMap[$holiday->getColor()];
        return array(
            'title' => $holiday->getName(),
            'start' => $holiday->getStartDate(),
            'end' => date('Y-m-d', strtotime($holiday->getEndDate() . ' +1 day')),
            'allDay' => true,
            'color' => $color,
            'textColor' => '#fff'
        );
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
}
