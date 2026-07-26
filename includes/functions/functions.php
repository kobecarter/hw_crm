<?php
 require_once __DIR__.'./../../vendor/autoload.php';
 use Firebase\JWT\JWT;
 use Firebase\JWT\Key;

function getCategorieFournisseur(){
    return array(
        1 => 'Photographe',
        2 => 'Vidéaste',
        3 => 'Maintenance / Entretien',
        4 => 'Mannequin',
        5 => 'Banque',
        6 => 'Cabinet comptable',
        7 => 'Développeur',
        8 => 'Designer / Graphiste',
        9 => 'Ménage / Service de nettoyage',
        10 => 'CNSS (Organisme social)',
        11 => 'Impôts (Administration fiscale)',
        12 => 'Factures d\'achat (Flux fournisseurs)',
        13 => 'Assurance',
        14 => 'Fournitures de bureau',
        15 => 'Imprimeur',
        16 => 'Habillage véhicule / Marquage publicitaire',
        17 => 'Téléphonie & Internet',
        18 => 'Service de livraison / Coursier',
        19 => 'Location d’espaces (Studios, salles de conférence)',
        21 => 'Service RP',
        22 => 'Presse contact',
        23 => 'Animateur',
        24 => 'Agence événementielle',
        25 => 'Artiste',
        26 => 'Influenceur',
        20 => 'Autres');
}

function fileExtension($s) {
  // strrpos() function returns the position 
  // of the last occurrence of a string inside 
  // another string.
  $n = strrpos($s,".");
  
  // The substr() function returns a part of a string.
  if($n===false)  
    return "";
  else 
    return substr($s,$n+1);
}

function getDaysInMonth($month = null, $year = null)
{

    if ($month == null) {

        $month = date("n", time());

    }

    if ($year = null) {

        $year = date("Y", time());

    }

    $dim = date("t", mktime(0, 0, 0, $month, 1, $year));

    return $dim;

}

function fieldCheck($data = array(), $indices = array()){
    foreach($indices as $indice){
        if(!isset($data[$indice]) || empty($data[$indice])){
            return false;
        }
    }
    return true;
}

function url_rewriting($str)
{
    $str = str_replace('&', 'et', $str);

    // On convertit la cha�ne en UTF-8 si besoin est.
    if ($str !== mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')) {
        $str = mb_convert_encoding($str, 'UTF-8');
    }

    $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');

    // Quelques entit�s � remplacer par les lettres correspondantes.
    $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '$1', $str);

    $str = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $str);
    return strtolower(trim($str, '-'));
}

function getTitleOfComponent($option)
{
    global $db;
    $titre = "component";
    $ids_modules = module::findAll();
    foreach ($ids_modules as $id_module) {
        $m = new module($id_module, $db);
        if ($m->getIdModule() == $option) {
            $titre = $m->getNom();
        }
    }
    return $titre;
}

function monthNumToName($mois)
{
    $tableau = Array("", "Janvier", "F&eacute;vrier",
        "Mars", "Avril", "Mai", "Juin", "Juillet",
        "A&ocirc;ut", "Septembre", "Octobre", "Novembre", "D&eacute;cembre");

    return (intval($mois) > 0 && intval($mois)
        < 13) ? $tableau[intval($mois)] : "Ind�fini";
}

function monthNumToShortName($mois)
{
    $tableau = Array("", "Janv", "F&eacute;vr",
        "Mars", "Avr", "Mai", "Juin", "Juill",
        "A&ocirc;ut", "Sept", "Oct", "Nov", "D&eacute;c");

    return (intval($mois) > 0 && intval($mois)
        < 13) ? $tableau[intval($mois)] : "Ind�fini";
}

// retourn le nom du jour
function getDayBynumber($n)
{
    $jours = array('Ind�fini', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
    $days = array('Ind�fini', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

    return ($_SESSION['langue'] == 'en') ? $jours[$n] : $days[$n];
}

// converti une date sous forme de jj/mm/aaaa en mktime

function dateToMktime($date)
{

    if ($date != "") {

        $tDate = explode("/", $date);

        $mktime = mktime(0, 0, 0, $tDate[1], $tDate[0], $tDate[2]);

    } else {

        $mktime = "";

    }

    return $mktime;

}

// converti un mktimeen une date au format jj/mm/aaaa	

function mktimeToDate($mktime)
{

    if ($mktime != "") {

        $date = date("d/m/Y", $mktime);

    } else {

        $date = "";

    }

    return $date;

}

// converti une date au format date sql
function dateBD($date)
{ 
    $restult = null;
    if(isset($date) && !empty($date)){
        $d = explode("/", $date);
        $restult = $d[2] . '-' . $d[1] . '-' . $d[0];
    }
    return $restult;
}

// converti une date en une date jj/mm/an
function normalDate($date)
{
    if ($date != "") {
        $d = explode("-", $date);
        return $d[2] . '/' . $d[1] . '/' . $d[0];
    } else
        return "";
}


function normaldate2($date, $lang = "fr")
{
    $date = explode('-', $date);
    switch ($date[1]) {
        case '01':
            $mois = $lang == 'fr' ? 'Janvier' : 'January';
            break;
        case '02':
            $mois = $lang == 'fr' ? 'F&eacute;vrier' : 'February';
            break;
        case '03':
            $mois = $lang == 'fr' ? 'Mars' : 'March';
            break;
        case '04':
            $mois = $lang == 'fr' ? 'Avril' : 'April';
            break;
        case '05':
            $mois = $lang == 'fr' ? 'Mai' : 'May';
            break;
        case '06':
            $mois = $lang == 'fr' ? 'Juin' : 'June';
            break;
        case '07':
            $mois = $lang == 'fr' ? 'Juillet' : 'July';
            break;
        case '08':
            $mois = $lang == 'fr' ? 'Ao&ucirc;t' : 'August';
            break;
        case '09':
            $mois = $lang == 'fr' ? 'Septembre' : 'September';
            break;
        case '10':
            $mois = $lang == 'fr' ? 'Octobre' : 'October';
            break;
        case '11':
            $mois = $lang == 'fr' ? 'Novembre' : 'November';
            break;
        case '12':
            $mois = $lang == 'fr' ? 'D&eacute;cembre' : 'December';
            break;
    }
    $result = $date[2] . ' ' . $mois . ' ' . $date[0];
    return $result;
}

function letterDay($day)
{
    switch ($day) {
        case '1':
            $letter = 'Lundi';
            break;
        case '2':
            $letter = 'Mardi';
            break;
        case '3':
            $letter = 'Mercredi';
            break;
        case '4':
            $letter = 'Jeudi';
            break;
        case '5':
            $letter = 'Vendredi';
            break;
        case '6':
            $letter = 'Samedi';
            break;
        case '7':
            $letter = 'Dimanche';
            break;
    }
    return $letter;
}

function letterMonth($month)
{
    switch ($month) {
        case '01':
            $letter = 'Janvier';
            break;
        case '02':
            $letter = 'F&eacute;vrier';
            break;
        case '03':
            $letter = 'Mars';
            break;
        case '04':
            $letter = 'Avril';
            break;
        case '05':
            $letter = 'Mai';
            break;
        case '06':
            $letter = 'Juin';
            break;
        case '07':
            $letter = 'Juillet';
            break;
        case '08':
            $letter = 'Ao&ucirc;t';
            break;
        case '09':
            $letter = 'Septembre';
            break;
        case '10':
            $letter = 'Octobre';
            break;
        case '11':
            $letter = 'Novembre';
            break;
        case '12':
            $letter = 'D&eacute;cembre';
            break;
    }
    return $letter;
}

// retourn un tableau avec les noms des images upload�es 

function uploadFiles($nomChampTxt, $uploadTo, $extensions = NULL)
{

	global $filesNotUploaded;

	

	$nomChampTxt = str_replace("[]","",$nomChampTxt);

	if($_FILES) {

		$racine = $uploadTo;

		// Pour chaque input

		for($i=0;$i<sizeof($_FILES[$nomChampTxt]["name"]);$i++) {

	

			// Si l'input est vide, on passe

			if(!$_FILES[$nomChampTxt]["name"][$i]) continue;

	

			$name = $_FILES[$nomChampTxt]["name"][$i];

			$ext = substr($name, strrpos($name, ".") + 1); 

				if (in_array($ext, $extensions) || $extensions == NULL){

				$nom_fichier=basename($name,".".$ext);
				
				// Pour éviter d'écraser l'ancien en cas de doublon
				$nom_fichier = str_replace(" ","_",$nom_fichier);
				$nom_fichier = str_replace("’","",$nom_fichier);	
				
				$accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig);/';
				$string_encoded = htmlentities($nom_fichier,ENT_NOQUOTES,'UTF-8');
				$nom_fichier = preg_replace($accents,'$1',$string_encoded); 	

				$nom_fichier = strtolower($nom_fichier);
				
				$n="";

				while(file_exists("$racine/$nom_fichier$n.$ext")) $n++;

				$nom_fichier="$nom_fichier$n.$ext";

				$fichiers[] = $nom_fichier;

				// Fin de l'upload

					if (@move_uploaded_file($_FILES[$nomChampTxt]["tmp_name"][$i], "$racine/$nom_fichier")){

						@chmod("$racine/$nom_fichier", 0755);

					} else {

						echo "Erreur, impossible d'envoyer le fichier <i>$racine/$nom_fichier</i><br>\n";

					}

				}

			}

	

		}

	return @$fichiers;

}

//G�n�rer une chaine de caract�re unique et al�atoire

function random($car)
{
    $string = "";
    $chaine = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    srand((double)microtime() * 1000000);
    for ($i = 0; $i < $car; $i++) {
        $string .= $chaine[rand() % strlen($chaine)];
    }
    return $string;
}


// redimentionnement d'images

function redimage($img_src, $img_dest, $dst_w, $dst_h)
{

    // Lit les dimensions de l'image

    $size = GetImageSize($img_src);

    $src_w = $size[0];
    $src_h = $size[1];

    // Teste les dimensions tenant dans la zone

    $test_h = round(($dst_w / $src_w) * $src_h);

    $test_w = round(($dst_h / $src_h) * $src_w);

    // Si Height final non pr�cis� (0)

    if (!$dst_h) $dst_h = $test_h;

    // Sinon si Width final non pr�cis� (0)

    elseif (!$dst_w) $dst_w = $test_w;

    // Sinon teste quel redimensionnement tient dans la zone

    elseif ($test_h > $dst_h) $dst_w = $test_w;

    else $dst_h = $test_h;


    // La vignette existe ?

    $test = (file_exists($img_dest));

    // L'original a �t� modifi� ?

    if ($test)

        $test = (filemtime($img_dest) > filemtime($img_src));

    // Les dimensions de la vignette sont correctes ?

    if ($test) {

        $size2 = GetImageSize($img_dest);

        $test = ($size2[0] == $dst_w);

        $test = ($size2[1] == $dst_h);

    }


    // Cr�er la vignette ?

    if (!$test) {

        // Cr�e une image vierge aux bonnes dimensions

        // $dst_im = ImageCreate($dst_w,$dst_h);

        $dst_im = ImageCreateTrueColor($dst_w, $dst_h);

        // Copie dedans l'image initiale redimensionn�e

        $src_im = ImageCreateFromJpeg($img_src);

        // ImageCopyResized($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);

        ImageCopyResampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

        // Sauve la nouvelle image

        ImageJpeg($dst_im, $img_dest);

        // D�truis les tampons

        ImageDestroy($dst_im);

        ImageDestroy($src_im);

    }


    // Affiche le descritif de la vignette echo "SRC='".$img_dest."?t=".time()."' WIDTH=".$dst_w." HEIGHT=".$dst_h;

}


// retourn les valeur necessaire pour la pagination

function pagination($req, $nb_elemPage, $pageActu)
{

    global $db;

    $result = $db->query($req);

    $nbr_elem = $db->num_rows($result);

    $page = ceil($nbr_elem / $nb_elemPage);

    $n = ($pageActu + 1) * $nb_elemPage; //nombre des element depuis la 1er page jusqu'a la page actuel

    if ($nbr_elem > $n)

        $val[2] = $pageActu + 1; // bouton suivant

    if ($pageActu > 0)

        $val[3] = $pageActu - 1; // bouton pr�c�dent

    $val[0] = $page; //nombre de page

    $val[1] = $nbr_elem; // nombre d'element retourn� par la requette


    return $val;

}

// teste d'unicit�
function unique($table, $champ, $val)
{
    global $db;
    $SQLselect = "select * from " . $table . " where " . $champ . "='" . $val . "'";
    $n = $db->num_rows($db->query($SQLselect));
    if ($n == 0)
        return true;
    else
        return false;
}

// rajoute la fonction GetSQLValueString pour les requettes au cas ou elle n'est pas d�finie

if (!function_exists("GetSQLValueString")) {

    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")

    {
        global $db;
        //$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;


        $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($db->getLink(), $theValue) : mysqli_escape_string($db->getLink(), $theValue);


        switch ($theType) {

            case "text":

                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";

                break;

            case "long":

            case "int":

                $theValue = ($theValue != "") ? intval($theValue) : "NULL";

                break;

            case "double":

                $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";

                break;

            case "date":

                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";

                break;

            case "defined":

                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;

                break;

        }

        return $theValue;

    }

}

// Export base de donn�es
function dumpMySQL($serveur, $login, $password, $base, $mode)
{
    $connexion = mysql_connect($serveur, $login, $password);
    mysql_select_db($base, $connexion);

    $entete = "-- ----------------------\n";
    $entete .= "-- dump de la base " . $base . " au " . date("d-M-Y") . "\n";
    $entete .= "-- ----------------------\n\n\n";
    $creations = "";
    $insertions = "\n\n";

    $listeTables = mysql_query("show tables", $connexion);
    while ($table = mysql_fetch_array($listeTables)) {
        // si l'utilisateur a demand� la structure ou la totale
        if ($mode == 1 || $mode == 3) {
            $creations .= "-- -----------------------------\n";
            $creations .= "-- creation de la table " . $table[0] . "\n";
            $creations .= "-- -----------------------------\n";
            $listeCreationsTables = mysql_query("show create table " . $table[0], $connexion);
            while ($creationTable = mysql_fetch_array($listeCreationsTables)) {
                $creations .= $creationTable[1] . ";\n\n";
            }
        }
        // si l'utilisateur a demand� les donn�es ou la totale
        if ($mode > 1) {
            $donnees = mysql_query("SELECT * FROM " . $table[0]);
            $insertions .= "-- -----------------------------\n";
            $insertions .= "-- insertions dans la table " . $table[0] . "\n";
            $insertions .= "-- -----------------------------\n";
            while ($nuplet = mysql_fetch_array($donnees)) {
                $insertions .= "INSERT INTO " . $table[0] . " VALUES(";
                for ($i = 0; $i < mysql_num_fields($donnees); $i++) {
                    if ($i != 0)
                        $insertions .= ", ";
                    if (mysql_field_type($donnees, $i) == "string" || mysql_field_type($donnees, $i) == "blob")
                        $insertions .= "'";
                    $insertions .= addslashes($nuplet[$i]);
                    if (mysql_field_type($donnees, $i) == "string" || mysql_field_type($donnees, $i) == "blob")
                        $insertions .= "'";
                }
                $insertions .= ");\n";
            }
            $insertions .= "\n";
        }
    }

    mysql_close($connexion);

    $fichierDump = fopen("dump/dump_" . date('d-m-Y') . ".sql", "wb");
    fwrite($fichierDump, $entete);
    fwrite($fichierDump, $creations);
    fwrite($fichierDump, $insertions);
    fclose($fichierDump);

    echo '<div class="alert success">
        <span class="icon"></span>
        <strong>Succ&egrave;s! </strong>Sauvegarde r&eacute;alis&eacute;e avec succ&egrave;s !!
    </div>';
    echo "";
}

function show404Error($val)
{
    include_once($val . ".html");
}


// Supprimer récurssivement un dossier
function rmdir_recursive($dir)
{
    foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
}

// Copier récurssivement un dossier
function copy_recursive($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_recursive($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function RedimensionnerImage($source, $type_value = "W", $new_value,  $compression = 70, $sortie = "") {
    /*
      Récupération des dimensions de l'image afin de vérifier
      que ce fichier correspond bel et bien à un fichier image.
      Stockage dans deux variables le cas échéant.
    */
    if( !( list($source_largeur, $source_hauteur) = @getimagesize($source) ) ) {
        return false;
    }
    /*
      Calcul de la valeur dynamique en fonction des dimensions actuelles
      de l'image et de la dimension fixe que nous avons précisée en argument.
    */
    if( $type_value == "H" ) {
        $nouv_hauteur = $new_value;
        $nouv_largeur = ($new_value / $source_hauteur) * $source_largeur;
    } else {
        $nouv_largeur = $new_value;
        $nouv_hauteur = ($new_value / $source_largeur) * $source_hauteur;
    }
    /*
   Création du conteneur, c'est-à-dire l'image qui va contenir la version
    redimensionnée. Elle aura donc les nouvelles dimensions.
    */
    $image = imagecreatetruecolor($nouv_largeur, $nouv_hauteur);
    /*
      Importation de l'image source. Stockage dans une variable pour pouvoir
      effectuer certaines actions.
    */
    $source_image = imagecreatefromstring(file_get_contents($source));
    /*
      Copie de l'image dans le nouveau conteneur en la rééchantillonant. Ceci
      permet de ne pas perdre de qualité.
    */
    imagecopyresampled($image, $source_image, 0, 0, 0, 0, $nouv_largeur, $nouv_hauteur, $source_largeur, $source_hauteur);
    /*
      Si nous avons spécifié une sortie et qu'il s'agit d'un chemin valide (accessible
      par le script)
    */
    if(strlen($sortie) > 0 and @touch($sortie)) {
        /*
         Enregistrement de l'image et affichage d'une notification à l'utilisateur.
        */
        imagejpeg($image, $sortie, $compression);
        //echo "Fichier sauvegardé.";
        /*
          Sinon...
        */
    } else {
        /*
         ...Nous indiquons au navigateur que nous affichons une image en définissant le
         header et nous affichons l'image.
        */
        header("Content-Type: image/jpeg");
        imagejpeg($image, NULL, $compression);
    }
    /*
      Libération de la mémoire allouée aux deux images (sources et nouvelle).
    */
    imagedestroy($image);
    imagedestroy($source_image);
}

function getToken(){
    $headers = getallheaders();
    if (isset($headers['Authorization']) && !empty($headers['Authorization'])) {
        $authorization = str_replace('Bearer ','',$headers['Authorization']);
        // Explicitly cast the result to stdClass
        try {
            $userData = JWT::decode($authorization, new Key("client_secret_key", 'HS256'));
            return $userData;
        } catch (\Throwable $th) {
            return null;
        }
    }
    return null;
}
function setToken($client){
    $payload = array(
        'isd' => "localhost",
        'aud' => "localhost",
        'email' => $client['email'],
        'id' => $client['id'],
    );
    $encode = JWT::encode($payload, "client_secret_key", 'HS256');
    return $encode;
}

function getInfoFromToken($token){
    if (isset($token) && !empty($token)) {
        // Explicitly cast the result to stdClass
        try {
            $userData = JWT::decode($token, new Key("client_secret_key", 'HS256'));
            return $userData;
        } catch (\Throwable $th) {
            return $th;
        }
    }
    return null;
}

function isValidDate($dateString, $format = 'Y-m-d') {
    // Create a DateTime object from the date string
    $date = DateTime::createFromFormat($format, $dateString);
    
    // Check if the date is valid and matches the expected format
    return $date && $date->format($format) === $dateString;
}

function months(){
    return [
        ["number"=>"01","name"=>"Janvier"],
        ["number"=>"02","name"=>"Février"],
        ["number"=>"03","name"=>"Mars"],
        ["number"=>"04","name"=>"Avril"],
        ["number"=>"05","name"=>"Mai"],
        ["number"=>"06","name"=>"Juin"],
        ["number"=>"07","name"=>"Juillet"],
        ["number"=>"08","name"=>"Août"],
        ["number"=>"09","name"=>"Septembre"],
        ["number"=>"10","name"=>"Octobre"],
        ["number"=>"11","name"=>"Novembre"],
        ["number"=>"12","name"=>"Décembre"]
    ];
}

function getUnities(){
    return [
        "en"=>[
            "month"=>"Month",
            "day_man"=>"Day/Man",
            "day"=>"Day",
            "week"=>"Week",
            "year"=>"Year",
            "pers"=>"Person"
        ],
        "fr"=>[
            "month"=>"Mois",
            "day_man"=>"Jour/hommes",
            "day"=>"Jour",
            "week"=>"Semaine",
            "year"=>"An",
            "pers"=>"Personne"
        ],
        "ar"=>[
            "month"=>"Month",
            "day_man"=>"Day/Man",
            "day"=>"Day",
            "week"=>"Week",
            "year"=>"Year",
            "pers"=>"Person"
        ]
    ];
}

function getUnit($unit,$langue){
    $unites = [
        "en"=>[
            "month"=>"Month",
            "day_man"=>"Day/Man",
            "day"=>"Day",
            "year"=>"Year"
        ],
        "fr"=>[
            "month"=>"Mois",
            "day_man"=>"Jour/hommes",
            "day"=>"Jour",
            "year"=>"An"
        ],
        "ar"=>[
            "month"=>"Month",
            "day_man"=>"Day/Man",
            "day"=>"Day",
            "year"=>"Year"
        ]
    ];
    return $unites[$langue][$unit];
}

function getDatesFromRange($startDate, $endDate) {
    $dates = [];

    // Create a DateTime object for start and end dates
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    
    // Add 1 day to the end date to include the end date itself
    $end = $end->modify('+1 day'); 

    // Create a DateInterval of 1 day
    $interval = new DateInterval('P1D');

    // Create a DatePeriod instance with the start, interval, and end
    $dateRange = new DatePeriod($start, $interval, $end);

    // Loop through each date in the period and add to the array
    foreach ($dateRange as $date) {
        $dates[] = $date->format('Y-m-d'); // Format as 'Y-m-d', can change as needed
    }

    return $dates;
}

function chargesTitles(){
    return [
        "Frais de telecommunications orange",
        "Frais de telecommunications (Orange)",
        "Frais de telecommunications (IAM)", 
        "Frais Electricite", 
        "Frais Eau",
        "Frais de ménage",
        "Gasoil",
        "Fournitures de bureau",
        "Achat matériel",
        "Amenagement du bureau",
        "Frais de restauration",
        "Travaux de bureau",
        "Frais de service",
        "Loyer",
        "Credit du bureau",
        "Frais syndique",
        "Frais comptable",
        "Frais d'avocat",
        "Frais de déplacement",
        "Services bancaires",
        "Produit de menage",
        "Achat du consommable",
        "Frais outils de fonctionnement", 
        "Achat Genious",
        "Cotisation CNSS",
        "Régularisation TVA",
        "Indemenité de période d'essai",
        "Indemenité de stage",
    ];
}