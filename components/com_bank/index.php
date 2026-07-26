<?php

@$task = $_GET['task'];
switch ($task)
{
    case 'add' :
        if ($_SESSION['user']->hasDroit('add', 'com_bank')) {
            $action = "components/com_bank/controleurs/router.php?task=addBank";
            $submitName = "add";
            $submitValue = "Ajouter bank";
            include_once("components/com_bank/views/bank/add.php");
        }
        break;
    case 'edit' :
        if ($_SESSION['user']->hasDroit('edit', 'com_bank')) {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = intval($_GET['id']);
                $bank = bank::find($id);
                $action = "components/com_bank/controleurs/router.php?task=editBank";
                $submitName = "edit";
                $submitValue = "Modifier bank";
                include_once("components/com_bank/views/bank/edit.php");
            }
        }
        break;
    default :
        if ($_SESSION['user']->hasDroit('view', 'com_bank')) {
            $banks = bank::findAll();
            include_once("components/com_bank/views/bank/list.php");
        }
        break;
}