<?php

if (isset($task) && !empty($task)) {
    switch ($task) {
        case 'addAgence':
            addAgence($_POST);
            break;
        case 'editAgence':
            editAgence($_POST);
            break;
        case 'deleteAgence':
            deleteAgence($_POST);
            break;
        case 'deleteAgences' :
            deleteAgences($_POST);
            break;
        case 'deleteLogo' :
            deleteLogo($_POST);
            break;
        case 'deleteSignature' :
            deleteSignature($_POST);
            break;
    }
}

function addAgence($data)
{
    $indices = array("nom","color");
    if (fieldCheck($data, $indices)) {
        if (buildAgence($data)->add() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function editAgence($data)
{
    $indices = array("id","nom","color");
    if (fieldCheck($data, $indices)) {
        if (buildAgence($data, $data['id'])->edit() == 1) {
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteAgence($data)
{
    $indices = array("id");
    if (fieldCheck($data, $indices))
    {
        $id = $data["id"];
        $agence = agence::find($id, $_SESSION["langue"]);
        if ($agence->delete() == 1) {
            if (file_exists("../../../images/agences/" . $agence->getLogo())) {
                @unlink("../../../images/agences/" . $agence->getLogo());
            }
            if (file_exists("../../../images/agences/" . $agence->getSignature())) {
                @unlink("../../../images/agences/" . $agence->getSignature());
            }
            echo "1";
        } else {
            echo "2";
        }
    } else {
        echo "0";
    }
}

function deleteAgences($data)
{
    $indices = array("ids");
    if (fieldCheck($data, $indices)) {
        $logos = agence::findLogosName($data['ids']);
        $signatures = agence::findSignaturesName($data['ids']);
        if (agence::deleteMultiple($data) == 1) {
            if ($logos)
                foreach ($logos as $logo) {
                    if (file_exists("../../../images/agences/" . $logo)) {
                        @unlink("../../../images/agences/" . $logo);
                    }
                }
            if ($signatures)
                foreach ($signatures as $signature) {
                    if (file_exists("../../../images/agences/" . $signature)) {
                        @unlink("../../../images/agences/" . $signature);
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

/* -------------------------------- deleteLogo -------------------------------- */
function deleteLogo($data)
{
	global $db;
	$agence = agence::find($data['id'],$_SESSION['langue']);
	$oldPic = $agence->getLogo();
	$agence->setLogo("");
	if ($agence->edit() == 1) {
		@unlink("../../../images/agences/" . $oldPic);
		echo '1';
	} else
		echo '2';
}

/* -------------------------------- deleteSignature -------------------------------- */
function deleteSignature($data)
{
	global $db;
	$agence = agence::find($data['id'],$_SESSION['langue']);
	$oldPic = $agence->getSignature();
	$agence->setSignature("");
	if ($agence->edit() == 1) {
		@unlink("../../../images/agences/" . $oldPic);
		echo '1';
	} else
		echo '2';
}


function buildAgence($data, $id = null)
{
    global $db;
    $agence = new agence();
    $logo = array();
    $signature = array();

    if (isset($_FILES['logo']) && $_FILES['logo']['name'][0] != '') {
        $logo = uploadFiles('logo', '../../../images/agences',  array('jpg', 'jpeg', 'gif', 'png', 'webp', 'JPG', 'JPEG', 'GIF', 'PNG', 'WEBP'));
    }
    
    if (isset($_FILES['signature']) && $_FILES['signature']['name'][0] != '') {
        $signature = uploadFiles('signature', '../../../images/agences',  array('jpg', 'jpeg', 'gif', 'png', 'webp', 'JPG', 'JPEG', 'GIF', 'PNG', 'WEBP'));
    }

    if ($id) {
        $agence = agence::find($id,$_SESSION["langue"]);
        if (isset($logo[0])) {
            $agence->setLogo($logo[0]);
            if (file_exists("../../../images/agences/" . agence::find($id, $_SESSION['langue'])->getLogo())) {
                @unlink("../../../images/agences/" . agence::find($id, $_SESSION['langue'])->getLogo());
            }
        } else {
            $agence->setLogo(agence::find($id, $_SESSION['langue'])->getLogo());
        }
        if (isset($signature[0])) {
            $agence->setSignature($signature[0]);
            if (file_exists("../../../images/agences/" . agence::find($id, $_SESSION['langue'])->getSignature())) {
                @unlink("../../../images/agences/" . agence::find($id, $_SESSION['langue'])->getSignature());
            }
        } else {
            $agence->setSignature(agence::find($id, $_SESSION['langue'])->getSignature());
        }
    } else {
        if (isset($logo[0])) {
            $agence->setLogo($logo[0]);
        } else {
            $agence->setLogo(null);
        }
        if (isset($signature[0])) {
            $agence->setSignature($signature[0]);
        } else {
            $agence->setSignature(null);
        }
    }

    $agence->setNom($data['nom']);
	$agence->setEmail($data['email']);
    $agence->setRaisonSocial($data['raison_social']);
    $agence->setManager($data['manager']);
    $agence->setCin($data['cin']);
    $agence->setTel($data['tel']);
    $agence->setTel2($data['tel2']);
    $agence->setFax($data['fax']);
    $agence->setAdresse($data['adresse']);
    $agence->setVille($data['ville']);
    $agence->setFonction($data['fonction']);
    $agence->setConditionDePaiement($data['condition_de_paiement']);
    $agence->setNumeroIncrementFacture($data['numero_increment_facture']);
    $agence->setNumeroIncrementDevis($data['numero_increment_devis']);
    $agence->setTva($data['tva']);
    $agence->setWebsite($data['website']);
    $agence->setColor($data['color']);
    $agence->setIf($data['if']);
    $agence->setTp($data['tp']);
    $agence->setRc($data['rc']);
    $agence->setIce($data['ice']);
    $agence->setConditions($data['conditions']);
    $agence->setInformation($data['information']);
    $agence->setDateAdd(date("Y-m-d H:i:s"));
    $agence->setLastEdit(date("Y-m-d H:i:s"));
    $agence->setLangue($_SESSION['langue']);

    return $agence;
}