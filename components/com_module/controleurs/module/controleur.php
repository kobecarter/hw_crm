<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'installModule':
            installModule($_POST);
            break;
        case 'desinstallModule':
            desinstallModule($_POST);
            break;
        case 'deleteModule':
            deleteModule($_POST);
            break;
        case "enableModule":
            enableModule($_POST);
            break;
		case "disableModule":
            disableModule($_POST);
            break;	
    }
}

function installModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        require_once "../../../components/" . $id_module . "/install.php";
        $xml = simplexml_load_file("../../../components/" . $id_module . "/config.xml");
		
		$module = new module();
		$module->setIdModule($id_module);
		$module->setEnabled(0);
		$module->setInstalled(1);
		$module->setNom($xml->nom . "");
		$module->setClasse($xml->class . "");
		$module->setNomTable($xml->table . "");
		$module->setTranslated($xml->traduction);
		$module->setURL($xml->url);
		$module->setSysteme($xml->system);
		$module->setIcon($xml->icon . "");
		$module->setPosition($xml->position . "");
		
        $install = "install_" . $id_module;
        if ($module->add() == 1 && $install() == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

function desinstallModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        require_once "../../../components/" . $id_module . "/install.php";
        $SQLdelete = "DELETE FROM " . __prefixe_db__ . "modules WHERE id_module = '" . $id_module . "'";
        $desinstall = "desinstall_" . $id_module;
        if (!$db->query($SQLdelete) && $desinstall() == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

function deleteModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
		$module = module::findByModule($id_module);
        require_once "../../../components/" . $id_module . "/install.php";
        $desinstall = "desinstall_" . $id_module;
        if ($module->delete() == 1 && $desinstall() == 1) {
            rmdir_recursive("../../../components/" . $id_module);
            echo 1;
        } else {
            echo 2;
        }
    }
}

function enableModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
		$module = module::findByModule($id_module);
		$module->setEnabled(1);
        if ($module->edit() == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

function disableModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
		$module = module::findByModule($id_module);
		$module->setEnabled(0);
        if ($module->edit() == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }
}


function buildLocalisation($data, $id = null)
{
    $localisation = new localisation();

    if($id){
        $localisation->setId($id);
    }

    $localisation->setActive(isset($data['active']) ? 1 : 0);
    $localisation->setOrdre($data['ordre']);
    $localisation->setTitre($data['titre']);
    $localisation->setDateAdd(date("Y-m-d"));
    $localisation->setLastEdit(date("Y-m-d"));
    $localisation->setLangue($_SESSION['langue']);

    return $localisation;
}