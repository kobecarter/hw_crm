<?php

use Mpdf\Tag\NewColumn;

if (isset($task) && !empty($task)) {
    switch ($task) {
        case "addFileResourceHumaine" :
            addFileResourceHumaine($_POST);
            break;
        case "editFileResourceHumaine" :
            editFileResourceHumaine($_POST);
            break;
        case "deleteFileResourceHumaine" :
            deleteFileResourceHumaine($_POST);
            break;
        case "addFileResourceHumaineSelf" :
            addFileResourceHumaineSelf($_POST);
            break;
        case "approveFileResourceHumaine" :
            approveFileResourceHumaine($_POST);
            break;
    }
}

// Upload self-service (espace employé, com_elogin) : jamais de hasDroit ici (voir router.php,
// gated isResourceHumaine() uniquement) - id_resourcehumaine forcé à la session, jamais au
// champ du formulaire, et document_type verrouillé sur la liste des documents encore
// manquants pour le statut de l'employé (whitelist stricte, pas de type libre). Toujours
// inséré non validé (validated = 0) : ne compte dans la checklist de conformité qu'après
// validation admin (voir fileresourcehumaine::documentTypesPresents()).
function addFileResourceHumaineSelf($data)
{
    if (!$_SESSION['user']->isResourceHumaine()) {
        echo "2";
        return;
    }
    $resourcehumaine = $_SESSION['user'];
    $documentsRequis = fileresourcehumaine::documentsRequis($resourcehumaine->getStatus());
    $documentType = isset($data['document_type']) ? $data['document_type'] : '';
    if (!isset($_FILES['file']) || $_FILES['file']['name'][0] == '' || !isset($documentsRequis[$documentType])) {
        echo "0";
        return;
    }

    $dossier = "../../../images/resourceshumaines/files";
    $files = uploadFiles('file', $dossier, array('PDF', 'pdf', 'jpg', 'jpeg', 'gif', 'png', 'webp', 'JPG', 'JPEG', 'GIF', 'PNG', 'WEBP'));

    $fileresourcehumaine = new fileresourcehumaine();
    $fileresourcehumaine->setResourcehumaine($resourcehumaine);
    $fileresourcehumaine->setTitle($documentsRequis[$documentType]);
    $fileresourcehumaine->setDocumentType($documentType);
    $fileresourcehumaine->setFile($files[0]);
    $fileresourcehumaine->setValidated(0);

    if ($fileresourcehumaine->add() == 1) {
        echo "1";
    } else {
        echo "2";
    }
}

// Validation admin d'un document déposé par l'employé - le fait basculer à "conforme" dans la
// checklist. Nom différent de validateFileResourceHumaine() (déjà pris par le helper de
// validation de formulaire ci-dessous) pour éviter toute confusion.
function approveFileResourceHumaine($data)
{
    if (!isset($data['id']) || empty($data['id'])) {
        echo "0";
        return;
    }
    $fileresourcehumaine = fileresourcehumaine::find($data['id']);
    $fileresourcehumaine->setValidated(1);
    if ($fileresourcehumaine->edit() == 1) {
        echo "1";
    } else {
        echo "2";
    }
}

function addFileResourceHumaine($data)
{
    $indices = array("id_resourcehumaine", "file","title");
    if (validateFileResourceHumaine($data, $indices)) {

        $dossier = "../../../images/resourceshumaines/files";
        $files = [];
        if(isset($_FILES['file']) && $_FILES['file']['name'][0]!=''){
            $files = uploadFiles('file', $dossier,  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
        }

        if(buildFileResourceHumaine($data, $files[0])->add() == 1)
            echo "1";
        else
            echo "2";
    } else {
        echo "0";
    }

}

function editFileResourceHumaine($data)
{
    $indices = array("id","id_resourcehumaine","title");
    if (validateFileResourceHumaine($data, $indices)) {
        $dossier = "../../../images/resourceshumaines/files";
        $files = [];
        if(isset($_FILES['file']) && $_FILES['file']['name'][0]!=''){
            $files = uploadFiles('file', $dossier,  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
        }

        if(buildFileResourceHumaine($data, $files , $data['id'])->edit() == 1)
            echo "1";
        else
            echo "2";
    } else {
        echo "0";
    }

}

function deleteFileResourceHumaine($data)
{
    $indices = array("id");
    if (validateFileResourceHumaine($data, $indices)) {
        $id = $data["id"];
        $fileresourcehumaine = fileresourcehumaine::find($id, $_SESSION["langue"]);
        if ($fileresourcehumaine->delete() == 1) {
            if (file_exists("../../../../images/resourceshumaines/files/" . $fileresourcehumaine->getFile())) {
                @unlink("../../../../images/resourceshumaines/files/" . $fileresourcehumaine->getFile());
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function validateFileResourceHumaine($data = array(), $indices = array())
{
    foreach ($indices as $indice) {
        if ($indice == "file") {
            if (!isset($_FILES["file"]) || $_FILES["file"]["name"][0] == "") {
                return false;
            }
        } else {
            if (!isset($data[$indice]) || empty($data[$indice])) {
                return false;
            }
        }
    }
    return true;
}

function buildFileResourceHumaine($data, $files = [], $id = null)
{
    $fileresourcehumaine = new fileresourcehumaine();
    if($id){
        $fileresourcehumaine = fileresourcehumaine::find($id);
        if(isset($files) && sizeof($files)>0) {
            $fileresourcehumaine->setFile($files[0]);
        }
    }else{
        if(isset($files)) {
            $fileresourcehumaine->setFile($files);
        }
    }
   
    $fileresourcehumaine->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
    $fileresourcehumaine->setTitle($data["title"]);
    $fileresourcehumaine->setDocumentType(isset($data["document_type"]) && $data["document_type"] != '' ? $data["document_type"] : null);

    return $fileresourcehumaine;
}