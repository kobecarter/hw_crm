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
    
    return $fileresourcehumaine;
}