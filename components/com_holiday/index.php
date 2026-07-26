<?php

@$task = $_GET['task'];
switch ($task) {
    case 'add':
        if ($_SESSION['user']->hasDroit('add', 'com_holiday')) {
            $action = "components/com_holiday/controleurs/router.php?task=addHoliday";
            $submitName = "add";
            $submitValue = "Ajouter holiday";
            include_once("components/com_holiday/views/holiday/add.php");
        }
        break;
    case 'edit':
        if ($_SESSION['user']->hasDroit('edit', 'com_holiday')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $holiday = holiday::find($id);
                $action = "components/com_holiday/controleurs/router.php?task=editHoliday";
                $submitName = "edit";
                $submitValue = "Modifier holiday";
                include_once("components/com_holiday/views/holiday/edit.php");
            }
        }
        break;
    default:
        if ($_SESSION['user']->hasDroit('view', 'com_holiday')) {
            $holidays = holiday::findAll();
            include_once("components/com_holiday/views/holiday/list.php");
        }
        break;
}
