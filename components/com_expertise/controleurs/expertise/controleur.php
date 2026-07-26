<?php
// print all erros
error_reporting(E_ALL);

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addExpertise':
            addExpertise($_POST);
            break;
        case 'editExpertise':
            editExpertise($_POST);
            break;
        case 'deleteExpertise':
            deleteExpertise($_POST);
            break;
        case "enableExpertise":
            enableExpertise($_POST);
            break;
        case 'deleteExpertises':
            deleteExpertises($_POST);
            break;
        case 'enableExpertises':
            enableExpertises($_POST);
            break;
    }
}

function addExpertise($data)
{
    try {
        $indices = array("titre");
        if (validateExpertise($data, $indices)) {
            if (buildExpertise($data)->add() == 1) {
                echo "1";
            } else {
                echo "2";
            }
        } else {
            echo "0";
        }
    } catch (Throwable $th) {
        echo $th->getMessage() . '\n' . $th->getTraceAsString();
    }
}

function editExpertise($data)
{
    $indices = array("id", "titre");
    if (validateExpertise($data, $indices)) {
        if (buildExpertise($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteExpertise($data)
{
    $indices = array("id");
    if (validateExpertise($data, $indices)) {
        $id = $data["id"];
        $expertise = expertise::find($id, $_SESSION["langue"]);
        if ($expertise->delete() == 1) {
            if (file_exists("../../../images/expertises/" . $expertise->getPhoto())) {
                @unlink("../../../images/expertises/" . $expertise->getPhoto());
            }


            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteExpertises($data)
{
    $indices = array("ids");
    if (validateExpertise($data, $indices)) {
        $photos = expertise::findPhotosName($data['ids']);
        if (expertise::deleteMultiple($data) == 1) {
            if ($photos)
                foreach ($photos as $photo) {
                    if (file_exists("../../../images/expertises/" . $photo)) {
                        @unlink("../../../images/expertises/" . $photo);
                    }
                }

            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableExpertise($data)
{
    $indices = array("id", "state");
    if (validateExpertise($data, $indices)) {
        $expertise = new expertise();
        $expertise->setId($data['id']);
        $expertise->setActive($data['state'] == "oui" ? 0 : 1);
        if ($expertise->enable() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableExpertises($data)
{
    $indices = array("ids", "active");
    if (validateExpertise($data, $indices)) {
        $res = expertise::enableMultiple($data);
        if ($res == 1)
            echo '1';
        else
            echo '2';
    } else
        echo '0';
}

function validateExpertise($data = array(), $indices = array())
{
    foreach ($indices as $indice) {
        if (!isset($data[$indice]) || (empty($data[$indice]) && $data[$indice] != 0)) {
            return false;
        }
    }
    return true;
}

function buildExpertise($data, $id = null)
{
    global $db;
    $expertise = new expertise();

    $photo = array();
    $photo_banniere = array();

    if (isset($_FILES['photo']) && $_FILES['photo']['name'][0] != '') {
        $photo = uploadFiles('photo', '../../../images/expertises/',  array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
    }

    if (!isset($data['slug']) || empty($data['slug'])) {
        $data['slug'] = '';
    }
if (!isset($data['texte']) || empty($data['texte'])) {
        $data['texte'] = '';
    }
    if (!isset($data['id_parent']) || empty($data['id_parent'])) {
        $data['id_parent'] = 0;
    }

    if ($id) {
        $expertise->setId($id);
        if (isset($photo[0])) {
            $expertise->setPhoto($photo[0]);
            if (file_exists("../../../images/expertises/" . expertise::find($id, $_SESSION['langue'])->getPhoto())) {
                @unlink("../../../images/expertises/" . expertise::find($id, $_SESSION['langue'])->getPhoto());
            }
        } else {
            $expertise->setPhoto(expertise::find($id, $_SESSION['langue'])->getPhoto());
        }
    } else {
        if (isset($photo[0])) {
            $expertise->setPhoto($photo[0]);
        } else {
            $expertise->setPhoto(null);
        }
    }

    $expertise->setSlug($data['slug']);
    $expertise->setParent(expertise::find($data["id_parent"], $_SESSION["langue"]));
    $expertise->setOrdre($data['ordre']);
    $expertise->setActive(isset($data['active']) ? 1 : 0);
    $expertise->setTitre($data['titre']);
    $expertise->setSousTitre($data['sous_titre']);
    $expertise->setExtrait($data['extrait']);
    $expertise->setTexte($data['texte']);
    $expertise->setDateAdd(date("Y-m-d"));
    $expertise->setLastEdit(date("Y-m-d"));
    $expertise->setLangue($_SESSION['langue']);

    return $expertise;
}
