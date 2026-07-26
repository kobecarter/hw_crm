<?php

use Mpdf\Tag\NewColumn;

if (isset($task) && !empty($task)) {
    switch ($task) {
        case "addPayslip" :
            addPayslip($_POST);
            break;
        case "editPayslip" :
            editPayslip($_POST);
            break;
        case "deletePayslip" :
            deletePayslip($_POST);
            break;
    }
}

function addPayslip($data)
{
    $indices = array("id_resourcehumaine", "file","date","title");
    if (validatePayslip($data, $indices)) {

        $dossier = "../../../images/resourceshumaines/payslips";
        $files = [];
        if(isset($_FILES['file']) && $_FILES['file']['name'][0]!=''){
            $files = uploadFiles('file', $dossier,  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
        }

        if(buildPayslip($data, $files[0])->add() == 1)
            echo "1";
        else
            echo "2";
    } else {
        echo "0";
    }

}

function editPayslip($data)
{
    $indices = array("id","id_resourcehumaine","title","date");
    if (validatePayslip($data, $indices)) {
        $dossier = "../../../images/resourceshumaines/payslips";
        $files = [];
        if(isset($_FILES['file']) && $_FILES['file']['name'][0]!=''){
            $files = uploadFiles('file', $dossier,  array('PDF','pdf','jpg','jpeg','gif','png','webp','JPG','JPEG','GIF','PNG','WEBP'));
        }

        if(buildPayslip($data, $files , $data['id'])->edit() == 1)
            echo "1";
        else
            echo "2";
    } else {
        echo "0";
    }

}

function deletePayslip($data)
{
    $indices = array("id");
    if (validatePayslip($data, $indices)) {
        $id = $data["id"];
        $payslip = payslip::find($id, $_SESSION["langue"]);
        if ($payslip->delete() == 1) {
            if (file_exists("../../../../images/resourceshumaines/payslips/" . $payslip->getFile())) {
                @unlink("../../../../images/resourceshumaines/payslips/" . $payslip->getFile());
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function validatePayslip($data = array(), $indices = array())
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

function buildPayslip($data, $files = [], $id = null)
{
    $payslip = new payslip();
    if($id){
        $payslip = payslip::find($id);
        if(isset($files) && sizeof($files)>0) {
            $payslip->setFile($files[0]);
        }
    }else{
        if(isset($files)) {
            $payslip->setFile($files);
        }
    }
   
    $payslip->setResourcehumaine(resourcehumaine::find($data["id_resourcehumaine"]));
    $payslip->setTitle($data["title"]);
    $payslip->setDate($data["date"]."-1");
    
    return $payslip;
}