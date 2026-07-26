<?php
include "../../../config.php";
require_once('../../../instanceDb.php');
require_once('../../../includes/functions/functions.php');
session_start();

if (isset($_GET['task']) && !empty($_GET['task'])) {
    $task = $_GET['task'];
    switch ($task) {
        case 'installModule' :
            installModule($_POST);
            break;
        case 'addModule' :
            addModule($_POST);
            break;
        case 'editModule' :
            editModule($_POST);
            break;
        case 'desinstallModule' :
            desinstallModule($_POST);
            break;
        case 'deleteModule' :
            deleteModule($_POST);
            break;
        case 'enableModule' :
            enableModule($_POST);
            break;
        case 'disableModule' :
            disableModule($_POST);
            break;
        case 'migrateModule' :
            migrateModule($_POST);
            break;
        case 'createModule' :
            createModule($_POST);
            break;
    }
}

/* -------------------------------- installModule -------------------------------- */
function installModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        require_once "../../../components/" . $id_module . "/install.php";
        $xml = simplexml_load_file("../../../components/" . $id_module . "/config.xml");
        $insertSQL = sprintf("INSERT INTO " . __prefixe_db__ . "modules (id_module, enabled, installed, nom, classe, nom_table, translated, url, system, icon, positioned) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($id_module, "text"),
            GetSQLValueString(0, "int"),
            GetSQLValueString(1, "int"),
            GetSQLValueString($xml->nom . "", "text"),
            GetSQLValueString($xml->class . "", "text"),
            GetSQLValueString($xml->table . "", "text"),
            GetSQLValueString($xml->traduction, "int"),
            GetSQLValueString($xml->url, "int"),
            GetSQLValueString($xml->system, "int"),
            GetSQLValueString($xml->icon . "", "text"),
            GetSQLValueString($xml->position . "", "text")
        );
        $install = "install_" . $id_module;
        if (!$db->query($insertSQL) && $install() == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

/* -------------------------------- addModule -------------------------------- */
function addModule($data)
{
    global $db;

    if (isset($_FILES['module']) && $_FILES['module']['name'][0] != '') {

        $var = uploadFiles('module', '../../../../uploads/', array('zip', 'ZIP'));

        if (count($var) == 0) {
            echo "3";
            exit();
        }

        $from_path = '../../../../uploads/';
        $to_path = '../../../components/';

        $n = 0;

        for ($i = 0; $i < count($var); $i++) {
            $zip = new ZipArchive;
            $res = $zip->open($from_path . $var[$i]);
            if ($res === TRUE) {
                $zip->extractTo($to_path);
                $zip->close();
                if (file_exists($from_path . $var[$i])) {
                    @unlink($from_path . $var[$i]);
                }

                $id_module = explode(".", $var[$i])[0];
                $id_module = preg_replace('/[0-9]+/', '', $id_module);

                require_once "../../../components/" . $id_module . "/install.php";

                $xml = simplexml_load_file("../../../components/" . $id_module . "/config.xml");

                $insertSQL = sprintf("INSERT INTO " . __prefixe_db__ . "modules (id_module, enabled, installed, nom, classe, nom_table, translated, url, system, icon, positioned) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    GetSQLValueString($id_module, "text"),
                    GetSQLValueString(1, "int"),
                    GetSQLValueString(1, "int"),
                    GetSQLValueString($xml->nom . "", "text"),
                    GetSQLValueString($xml->class . "", "text"),
                    GetSQLValueString($xml->table . "", "text"),
                    GetSQLValueString($xml->traduction, "int"),
                    GetSQLValueString($xml->url, "int"),
                    GetSQLValueString($xml->system, "int"),
                    GetSQLValueString($xml->icon . "", "text"),
                    GetSQLValueString($xml->position . "", "text")
                );

                $install = "install_" . $id_module;

                if (!$db->query($insertSQL) && $install() == 1) {
                    $n++;
                } else {
                    $n--;
                }

            } else {
                if (file_exists($from_path . $var[$i])) {
                    @unlink($from_path . $var[$i]);
                }
                $n--;
            }
        }
        if ($n == count($var)) {
            echo '1';
        } else {
            echo '2';
        }
    } else {
        echo '0';
    }
}

/* -------------------------------- editModule -------------------------------- */
function editModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        $m = new module($id_module, $db);

        $traduction = $m->isTranslated() ? 1 : 0;
        $url = $m->isUrl() ? 1 : 0;
        $system = $m->isSystem() ? 1 : 0;

        $content = "<?xml version='1.0' encoding='UTF-8' ?>\n";
        $content .= "<" . "component>\n";
        $content .= "   <" . "id>" . $id_module . "</id>\n";
        $content .= "   <" . "nom>" . $data["nom"] . "</nom>\n";
        $content .= "   <" . "class>" . $data["classe"] . "</class>\n";
        $content .= "   <" . "table>" . $data["table"] . "</table>\n";
        $content .= "   <" . "traduction>" . $traduction . "</traduction>\n";
        $content .= "   <" . "url>" . $url . "</url>\n";
        $content .= "   <" . "icon>" . $data["icone"] . "</icon>\n";
        $content .= "   <" . "system>" . $system . "</system>\n";
        $content .= "   <" . "position>" . $data["position"] . "</position>\n";
        $content .= "<" . "/component>\n";

        $updateSQL = sprintf("UPDATE " . __prefixe_db__ . "modules SET nom=%s, classe=%s, nom_table=%s, icon=%s, positioned=%s WHERE id_module=%s ",
            GetSQLValueString($data['nom'], "text"),
            GetSQLValueString($data['classe'], "text"),
            GetSQLValueString($data['table'], "text"),
            GetSQLValueString($data['icone'], "text"),
            GetSQLValueString($data['position'], "text"),
            GetSQLValueString($id_module, "text")
        );

        if (!$db->query($updateSQL)) {
            $cf = "../../../components/" . $id_module . "/config.xml";
            $fp = fopen($cf, "w");
            @fputs($fp, $content);
            @fclose($fp);
            echo 1;
        } else {
            echo 2;
        }
    }
}

/* -------------------------------- desinstallModule -------------------------------- */
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

/* -------------------------------- deleteModule -------------------------------- */
function deleteModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        require_once "../../../components/" . $id_module . "/install.php";
        $SQLdelete = "DELETE FROM " . __prefixe_db__ . "modules WHERE id_module = '" . $id_module . "'";
        $desinstall = "desinstall_" . $id_module;
        if (!$db->query($SQLdelete) && $desinstall() == 1) {
            rmdir_recursive("../../../components/" . $id_module);
            echo 1;
        } else {
            echo 2;
        }
    }
}

/* -------------------------------- enableModule -------------------------------- */
function enableModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        $SQLupdate = sprintf("UPDATE " . __prefixe_db__ . "modules SET enabled=%s WHERE id_module=%s",
            GetSQLValueString(1, "int"),
            GetSQLValueString($id_module, "text"));
        if (!$db->query($SQLupdate)) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

/* -------------------------------- disableModule -------------------------------- */
function disableModule($data)
{
    global $db;
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        $SQLupdate = sprintf("UPDATE " . __prefixe_db__ . "modules SET enabled=%s WHERE id_module=%s",
            GetSQLValueString(0, "int"),
            GetSQLValueString($id_module, "text"));
        if (!$db->query($SQLupdate)) {
            echo 1;
        } else {
            echo 2;
        }
    }
}

/* -------------------------------- migrateModule -------------------------------- */
function migrateModule($data)
{
    if (isset($data['id']) && !empty($data['id'])) {

        $id_module = $data['id'];
        require_once "../../../components/" . $id_module . "/migration.php";
        $migrate = "migrate_" . $id_module;
        if ($migrate() == 1) {
            if (file_exists("../../../components/" . $id_module . "/migration.php")) {
                @rename(
                    "../../../components/" . $id_module . "/migration.php",
                    "../../../components/" . $id_module . "/migration_" . date('YmdHis') . ".php"
                );
            }
            echo 1;
        } else {
            echo 2;
        }
    }
}

?>
