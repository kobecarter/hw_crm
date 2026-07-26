<?php
if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addLang':
            addLang($_POST);
            break;
        case 'editLang':
            editLang($_POST);
            break;
        case 'deleteLang':
            deleteLang($_POST);
            break;
        case "enableLang":
            enableLang($_POST);
            break;
        case 'deleteLangs' :
            deleteLangs($_POST);
            break;
        case 'enableLangs' :
            enableLangs($_POST);
            break;
    }
}

function addLang($data)
{
    $indices = array("nom");
    if (fieldCheck($data, $indices)) {
        if (buildLang($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editLang($data)
{
    $indices = array("id", "nom");
    if (fieldCheck($data, $indices)) {
        if (buildLang($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteLang($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $langue = langue::find($id, $_SESSION["langue"]);
        if ($langue->delete() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function enableLang($data)
{
    $indices = array("id", "state");
    if (fieldCheck($data, $indices))
    {
        $langue = new langue();
        $langue = langue::find($data['id'],$_SESSION['langue']);
        $langue->setActif($data['state'] == "oui" ? 0 : 1);
        if ($langue->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function buildLang($data, $id = null)
{
    global $db;
    $langue = new langue();
	
	$flag = array();
    if(isset($_FILES['flag']) && $_FILES['flag']['name'][0]!=''){
        $flag = uploadFiles('flag','../../../../images/langues/',  array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG'));
    }

    if($id){
        $langue = langue::find($id);
    }
	
	if(isset($flag[0])) {
		$langue->setFlag($flag[0]);
	}
	
	$langue->setNom($data['nom']);
    $langue->setCode($data['code']);
    $langue->setActif(isset($data['active']) ? 1 : 0);
    $langue->setDefaut(isset($data['defaut']) ? 1 : 0);
    $langue->setDateAdd(date("Y-m-d H:i:s"));
    $langue->setLastEdit(date("Y-m-d H:i:s"));

    return $langue;
}