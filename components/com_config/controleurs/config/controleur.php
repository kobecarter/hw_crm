<?php
if (isset($task) && !empty($task)) {
	switch ($task) {
		case 'updateConfig':
			updateConfig($_POST);
			break;

		case 'deleteLogo':
			deleteLogo($_POST);
			break;

		case 'switchLang':
			switchLang($_POST);
			break;
		case 'switchAgence':
			switchAgence($_POST);
			break;
	}
}

function updateConfig($data)
{
	global $db;
	$indices = array("nom");
	if (fieldCheck($data, $indices)) {
		$config = new config($db, $_SESSION['langue']);

		$photo = array();
		if (isset($_FILES['logo']) && $_FILES['logo']['name'][0] != '') {
			$photo = uploadFiles('logo', '../../../images/config/',  array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'));
			$config->setLogo($photo[0]);
		}

		$config->setNom($data['nom']);
		$config->setTitre($data['titre']);
		$config->setDescription($data['description']);
		$config->setEmail($data['email']);
		$config->setTel($data['tel']);
		$config->setTel2($data['tel2']);
		$config->setFax($data['fax']);
		$config->setAdresse($data['adresse']);
		$config->setFacebook($data['facebook']);
		$config->setTwitter($data['twitter']);
		$config->setYoutube($data['youtube']);
		$config->setInstagram($data['instagram']);
		$config->setPinterest($data['pinterest']);
		$config->setLinkedin($data['linkedin']);
		$config->setSnapchat($data['snapshat']);
		$config->setTumblr($data['tumblr']);
		$config->setViadeo($data['viadeo']);
		$config->setAnalytic($data['analytic']);
		$config->setConditionPaiment($data['condition_paiment']);

		if ($config->edit() == 1) {
			echo "1";
		} else {
			echo "2";
		}
	} else {
		echo "0";
	}
}

/* -------------------------------- deleteLogo -------------------------------- */
function deleteLogo()
{
	global $db;

	$config = new config($db, $_SESSION['langue']);
	$oldPic = $config->getLogo();
	$config->setLogo("");
	if ($config->edit() == 1) {
		@unlink("../../../images/config/" . $oldPic);
		echo '1';
	} else
		echo '2';
}

function switchLang($data)
{
	global $db;
	if (isset($data['lang']) && !empty($data['lang'])) {
		$_SESSION['langue'] = $data['lang'];
		echo '1';
	}
	// echo $_SESSION['langue'];
}

function switchAgence($data)
{
	global $db;
	if (isset($data['agence']) && !empty($data['agence'])) {
		$_SESSION['agence'] = $data['agence'];
		if($data['agence'] == 2){
		    $_SESSION['langue'] = 'en';
		}else {
		    $_SESSION['langue'] = 'fr';
		}
		echo '1';
	}
	// echo $_SESSION['langue'];
}
