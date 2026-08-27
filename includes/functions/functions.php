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
        27 => 'Hébergement web & noms de domaine',
        28 => 'Intelligence artificielle (IA)',
        29 => 'Outils & services Google',
        30 => 'Outils créatifs, multimédia & SEO',
        31 => 'Marketing, réseaux & gestion de projet',
        20 => 'Autres');
}

// Icône FontAwesome associée à chaque catégorie fournisseur - utilisé par les badges/puces de
// filtre de la page Fournisseurs (jamais de générique "tag" partout, chaque métier a son symbole).
function getCategorieFournisseurIcone($id){
    $icones = array(
        1 => 'fa-camera-retro',
        2 => 'fa-video',
        3 => 'fa-tools',
        4 => 'fa-walking',
        5 => 'fa-university',
        6 => 'fa-calculator',
        7 => 'fa-code',
        8 => 'fa-pen-nib',
        9 => 'fa-broom',
        10 => 'fa-shield-alt',
        11 => 'fa-landmark',
        12 => 'fa-file-invoice-dollar',
        13 => 'fa-umbrella',
        14 => 'fa-paperclip',
        15 => 'fa-print',
        16 => 'fa-car',
        17 => 'fa-wifi',
        18 => 'fa-truck',
        19 => 'fa-building',
        20 => 'fa-ellipsis-h',
        21 => 'fa-bullhorn',
        22 => 'fa-newspaper',
        23 => 'fa-microphone',
        24 => 'fa-calendar-alt',
        25 => 'fa-palette',
        26 => 'fa-hashtag',
        27 => 'fa-server',
        28 => 'fa-robot',
        29 => 'fa-cloud',
        30 => 'fa-magic',
        31 => 'fa-project-diagram',
    );
    return isset($icones[$id]) ? $icones[$id] : 'fa-briefcase';
}

// Icône, couleur d'accent et photo réelle (récupérée du site helloworld-agency.com, une par
// catégorie) associées à chaque catégorie de service (crm_categorie/crm_details_categorie) - même
// principe que getCategorieFournisseurIcone() ci-dessus, mais avec en plus une photo puisque la
// page Services affiche désormais une bannière par catégorie (grille façon page Fournisseurs).
function getServiceCategorieIcone($id){
    $icones = array(
        1 => 'fa-code',
        2 => 'fa-chart-line',
        3 => 'fa-palette',
        4 => 'fa-bullhorn',
        5 => 'fa-camera-retro',
    );
    return isset($icones[$id]) ? $icones[$id] : 'fa-briefcase';
}

function getServiceCategorieCouleur($id){
    $couleurs = array(
        1 => '#6366f1',
        2 => '#0d9488',
        3 => '#a855f7',
        4 => '#3b82f6',
        5 => '#22c55e',
    );
    return isset($couleurs[$id]) ? $couleurs[$id] : '#6366f1';
}

function getServiceCategoriePhoto($id){
    $photos = array(
        1 => '1-developpement.webp',
        2 => '2-seo.webp',
        3 => '3-design.webp',
        4 => '4-marketing.webp',
        5 => '5-shooting.webp',
    );
    return isset($photos[$id]) ? $photos[$id] : null;
}

// Années sans déclaration enregistrée, depuis la création de l'agence jusqu'à l'année en cours
// incluse - même principe que le calcul "mois manquants" de la page TVA/CNSS, mais à la maille
// annuelle pour les déclarations qui n'ont lieu qu'une fois par an (Bilan, Impôts, Taxe
// professionnelle). Partagé entre les 3 pages plutôt que dupliqué, car $classe change mais la
// logique (findByYear($agenceId, $annee) vide = manquante) est strictement identique.
function comAccountingAnneesManquantes($classe, $agenceObj)
{
    $anneeCourante = (int) date('Y');
    $anneeCreation = $agenceObj && $agenceObj->getDateAdd() ? (int) date('Y', strtotime($agenceObj->getDateAdd())) : 2022;
    $manquantes = array();
    for ($y = $anneeCreation; $y <= $anneeCourante; $y++) {
        $lignes = $classe::findByYear($agenceObj->getId(), (string) $y);
        if (empty($lignes)) {
            $manquantes[] = $y;
        }
    }
    return array('annees' => $manquantes, 'anneeCreation' => $anneeCreation);
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

				// macOS encode les accents en Unicode décomposé (ex: "é" = "e" + accent combinant U+0301) :
				// htmlentities() ne convertit pas ces marques combinantes (pas d'entité nommée), elles
				// restaient donc telles quelles dans le nom de fichier et faisaient planter l'INSERT SQL
				// ("Incorrect string value") sur les noms du type "Capture d'écran...png".
				$nom_fichier = preg_replace('/[\x{0300}-\x{036F}]/u', '', $nom_fichier);

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

        // Beaucoup de colonnes legacy (ex: id_user_added) sont nullable et null
        // arrive couramment ici (ex: user::find(null)). mysqli_real_escape_string()
        // n'accepte plus null depuis PHP 8.1 (deprecated), et le handler d'erreurs
        // strict de Leaf transforme cette deprecation en exception fatale côté API
        // - alors qu'une valeur vide a toujours été le comportement voulu ici.
        if ($theValue === null) {
            $theValue = "";
        }

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

// Simule une session utilisateur pour un appelant automatisé sans navigateur (endpoint public
// appelé par un webhook ou un service de tâche planifiée externe) : plusieurs classes du modèle
// (client, facture, devis, relance...) lisent $_SESSION['user']/['agence']/['langue'] directement,
// exactement comme slackEventWebhook() le fait déjà pour $_SESSION['user'] seul.
// Emoji drapeau pour le sélecteur d'agence du bandeau haut - aucun champ "pays" sur l'agence,
// seule l'agence 2 (HELLOWORLDLABEL - FZCO, Dubaï) est aux Émirats, toutes les autres facturent
// depuis le Maroc (même distinction que le "groupeMaroc" de com_rapprochement).
function agenceFlagEmoji($idAgence)
{
    return intval($idAgence) === 2 ? '🇦🇪' : '🇲🇦';
}

// Lien "espace client" public utilisé dans les emails automatiques - même distinction
// Maroc/Dubaï qu'agenceFlagEmoji() (l'agence 2 facture depuis Dubaï, toutes les autres
// depuis le Maroc), chaque pays ayant son propre nom de domaine pour l'espace client.
function espaceClientLink($idAgence)
{
    return intval($idAgence) === 2
        ? 'https://www.helloworldlabel.ae/client-space/'
        : 'https://www.helloworld-agency.com/client-space/';
}

// Emoji drapeau pour le sélecteur de langue du bandeau haut - remplace les images
// images/langues/*.png qui n'existent pas sur le disque (référencées avec un chemin relatif
// erroné "../images/langues/", générant un 404 sur toutes les pages).
function langueFlagEmoji($code)
{
    $drapeaux = array('fr' => '🇫🇷', 'en' => '🇬🇧', 'ar' => '🇲🇦');
    return isset($drapeaux[$code]) ? $drapeaux[$code] : '🏳️';
}

function bootstrapSystemSession($userId, $agenceId, $langue = 'fr')
{
    $_SESSION['user'] = user::find($userId);
    $_SESSION['agence'] = $agenceId;
    $_SESSION['langue'] = $langue;
}

// Agrège les alertes urgentes de plusieurs modules (rappels clients, factures à échéance,
// fournisseurs en attente de validation, lignes BANK STATEMENT encore à traiter) en une seule
// structure groupée, cliquable vers la bonne page - alimente le centre "Rappels(Urgent)" du
// bandeau haut (includes/tpl/notification.php + bottom.php) à la place des mini-dropdowns
// dispersés. Chaque groupe n'apparaît que si l'utilisateur a le droit de voir le module
// correspondant ET qu'il y a au moins une alerte - jamais une section vide.
function getAlertesUrgentes($agence)
{
    $user = $_SESSION['user'];
    $groupes = array();

    if ($user->hasDroit('view', 'com_rappel')) {
        $items = array();
        foreach (rappel::findAll(false, $agence) as $rappel) {
            if ($rappel->getDaysLeft() < 30) {
                $items[] = array(
                    'titre' => trim($rappel->getDomaine() . ' ' . $rappel->getType()),
                    'sous_titre' => 'Expire dans ' . $rappel->getDaysLeft() . ' jour(s)',
                    'url' => 'index.php?option=com_rappel&highlight=' . $rappel->getId(),
                    'urgence' => $rappel->getDaysLeft() <= 7 ? 'danger' : 'warning',
                    'date_add' => $rappel->getDateAdd()
                );
            }
        }
        if (!empty($items)) {
            $groupes['rappels'] = array('label' => 'Hosting / Domaines / Renouvellements Rappel', 'icon' => 'fa-bell', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_facture')) {
        $items = array();
        foreach (facture::findAll(false, false, false, true, false, false, $agence) as $f) {
            if ($f->getDaysLeft() < 30 && $f->getDateFin() != null && ($f->getTotal() == $f->getReste() || ($f->getTotal() > $f->getReste() && $f->getReste() > 0))) {
                $items[] = array(
                    'titre' => 'Facture N°' . $f->getNumero(),
                    'sous_titre' => 'Expire dans ' . $f->getDaysLeft() . ' jour(s)',
                    'url' => 'index.php?option=com_facture&task=show&id=' . $f->getId(),
                    'urgence' => $f->getDaysLeft() <= 7 ? 'danger' : 'warning',
                    'date_add' => $f->getDateAdd()
                );
            }
        }
        if (!empty($items)) {
            $groupes['factures'] = array('label' => 'Factures à échéance', 'icon' => 'fa-file-invoice', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_fournisseur')) {
        $items = array();
        foreach (fournisseur::findAll() as $f) {
            if (!$f->isValide()) {
                $nom = trim((string) $f->getRaisonSocial()) !== '' ? $f->getRaisonSocial() : trim($f->getPrenom() . ' ' . $f->getNom());
                $items[] = array(
                    'titre' => $nom !== '' ? $nom : '(sans nom)',
                    'sous_titre' => 'En attente de validation',
                    'url' => 'index.php?option=com_fournisseur&task=edit&id=' . $f->getId(),
                    'urgence' => 'warning',
                    'date_add' => $f->getDateAdd()
                );
            }
        }
        if (!empty($items)) {
            $groupes['fournisseurs'] = array('label' => 'Fournisseurs en attente', 'icon' => 'fa-truck', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_rapprochement')) {
        $items = array();
        foreach (releveLot::findAll($agence) as $lot) {
            $compteurs = releveLigne::compterParLot($lot->getLotImport());
            $nbATraiter = $compteurs['a_valider'] + $compteurs['sans_justificatif'];
            if ($nbATraiter > 0) {
                $bank = $lot->getBank();
                $nomCompte = '—';
                if ($bank) {
                    $nomCompte = $bank->getLabel() !== null && $bank->getLabel() !== '' ? $bank->getLabel()
                        : ($bank->getRaisonSociale() !== null && $bank->getRaisonSociale() !== '' ? $bank->getRaisonSociale() : $bank->getBanque());
                }
                $items[] = array(
                    'titre' => $nomCompte . ' — ' . $lot->getPeriodeLibelle(),
                    'sous_titre' => $nbATraiter . ' ligne(s) à traiter' . ($compteurs['sans_justificatif'] > 0 ? ' (dont ' . $compteurs['sans_justificatif'] . ' sans justificatif)' : ''),
                    'url' => 'index.php?option=com_rapprochement',
                    'urgence' => $compteurs['sans_justificatif'] > 0 ? 'danger' : 'warning',
                    'date_add' => $lot->getDateAdd()
                );
            }
        }
        if (!empty($items)) {
            $groupes['rapprochement'] = array('label' => 'BANK STATEMENT à traiter', 'icon' => 'fa-exchange-alt', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_relance')) {
        $items = array();
        foreach (relance::findAllNonTraite($agence) as $r) {
            $client = $r->getClient();
            $nomClient = $client ? (trim((string) $client->getRaisonSocial()) !== '' ? $client->getRaisonSocial() : trim($client->getPrenom() . ' ' . $client->getNom())) : '';
            $facture = $r->getFacture();
            $joursRestants = $r->getDaysLeft();
            $items[] = array(
                'titre' => $nomClient . ($facture ? ' — Facture N°' . $facture->getNumero() : ''),
                'sous_titre' => $joursRestants < 0 ? 'En retard de ' . abs($joursRestants) . ' jour(s)' : 'Relance ' . $r->getEtape() . ' dans ' . $joursRestants . ' jour(s)',
                'url' => 'index.php?option=com_relance&highlight=' . $r->getId(),
                'urgence' => $joursRestants < 0 ? 'danger' : 'warning',
                'date_add' => $r->getDateAdd()
            );
        }
        if (!empty($items)) {
            $groupes['relances'] = array('label' => 'Relances de paiement', 'icon' => 'fa-hand-holding-usd', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_accounting')) {
        $agenceObjet = agence::find($agence, isset($_SESSION['langue']) ? $_SESSION['langue'] : 'fr');
        $periodicite = $agenceObjet->getTvaPeriodicite() === 'trimestriel' ? 'trimestriel' : 'mensuel';
        $periodeCloturee = tva::periodeReference($periodicite, null, -1);
        $periodeDeclaree = true;
        $curseurPeriode = clone $periodeCloturee['debut'];
        while ($curseurPeriode <= $periodeCloturee['fin']) {
            if (empty(tva::findByDate($agence, $curseurPeriode->format('Y-m')))) {
                $periodeDeclaree = false;
                break;
            }
            $curseurPeriode->modify('+1 month');
        }
        if (!$periodeDeclaree) {
            $joursAvantEcheance = (int) (new DateTime('today'))->diff($periodeCloturee['limite'])->format('%r%a');
            $items = array(array(
                'titre' => 'Déclaration TVA — ' . $periodeCloturee['libelle'],
                'sous_titre' => $joursAvantEcheance < 0 ? 'En retard de ' . abs($joursAvantEcheance) . ' jour(s)' : 'À déposer dans ' . $joursAvantEcheance . ' jour(s)',
                'url' => 'index.php?option=com_accounting&task=tva',
                'urgence' => $joursAvantEcheance < 0 ? 'danger' : 'warning',
                // Item unique de ce groupe (jamais plusieurs déclarations en attente à la fois) - pas
                // de vraie "date d'ajout" pertinente ici, le tri secondaire par date ne joue de toute
                // façon aucun rôle sur un groupe à un seul item.
                'date_add' => date('Y-m-d')
            ));
            $groupes['tva'] = array('label' => 'TVA à déclarer', 'icon' => 'fa-file-invoice-dollar', 'items' => $items);
        }
    }

    if ($user->hasDroit('view', 'com_resourcehumaine')) {
        $items = array();
        foreach (resourcehumaine::findAll() as $employe) {
            if (!$employe->getAgency() || $employe->getAgency()->getId() != $agence) {
                continue;
            }
            foreach (request::findAllByResourcehumaine($employe->getId()) as $demande) {
                if ($demande->getStatus() == 0) {
                    $items[] = array(
                        'titre' => $employe->getFirstName() . ' ' . $employe->getLastName() . ' — ' . $demande->getTitle(),
                        'sous_titre' => 'Demande en attente depuis le ' . date('d/m/Y', strtotime($demande->getDateAdd())),
                        'url' => 'index.php?option=com_resourcehumaine&task=request&id=' . $employe->getId(),
                        'urgence' => 'warning',
                        'date_add' => $demande->getDateAdd()
                    );
                }
            }
        }
        if (!empty($items)) {
            $groupes['demandes_rh'] = array('label' => 'Demandes employés en attente', 'icon' => 'fa-user-clock', 'items' => $items);
        }
    }

    // Réseaux sociaux à revérifier tous les 3 mois (90 jours) : un identifiant peut avoir changé
    // (mot de passe renouvelé, compte perdu) sans que personne ne le remarque avant d'en avoir
    // besoin - cf. components/com_client/classes/clientsocial.php::findStaleByAgence(). Un simple
    // ré-enregistrement (même sans changer les valeurs) via la page ou un accès temporaire suffit
    // à repousser l'échéance de 3 mois, pas besoin d'un bouton "vérifié" dédié.
    if ($user->hasDroit('edit', 'com_client')) {
        $items = array();
        foreach (clientsocial::findStaleByAgence($agence, 90) as $stale) {
            $clientStale = client::findAny($stale['id_client']);
            if ($clientStale->getId() == 0) {
                continue;
            }
            $nomStale = $clientStale->getRaisonSocial() != '' ? $clientStale->getRaisonSocial() : trim($clientStale->getTitre() . ' ' . $clientStale->getNom() . ' ' . $clientStale->getPrenom());
            $joursDepuis = $stale['derniere_verif'] ? (int) round((time() - strtotime($stale['derniere_verif'])) / 86400) : null;
            $items[] = array(
                'titre' => $nomStale,
                'sous_titre' => $joursDepuis !== null ? 'Non revérifié depuis ' . $joursDepuis . ' jour(s)' : 'Jamais vérifié',
                'url' => 'index.php?option=com_client&task=socialAccounts&id=' . $stale['id_client'],
                'urgence' => ($joursDepuis === null || $joursDepuis >= 120) ? 'danger' : 'warning',
                // Pas de vraie "date d'ajout" disponible ici (findStaleByAgence() renvoie un agrégat,
                // pas un objet clientsocial) - la dernière vérification est le meilleur proxy dispo.
                'date_add' => $stale['derniere_verif']
            );
        }
        if (!empty($items)) {
            $groupes['social_a_verifier'] = array('label' => 'Réseaux sociaux à vérifier', 'icon' => 'fa-share-alt', 'items' => $items);
        }
    }

    return $groupes;
}

// ---- Statistiques globales (com_dashboard, task=globalStats) : conversion devises + agrégats
// comptables réutilisables, centralisés ici pour ne plus dupliquer les mêmes taux/calculs dans
// chaque fonction du contrôleur (l'ancienne version de la page les répétait 3 fois). --------------

// Taux de conversion approximatifs vers le DH - mêmes valeurs déjà utilisées historiquement par
// cette page (aucun module "taux de change" réel dans ce CRM, ce sont les valeurs métier
// existantes) : centralisées ici en un seul endroit plutôt que recopiées à chaque calcul.
function tauxConversionDH($devise)
{
    $taux = array('DH' => 1, '€' => 10, '£' => 12, '$' => 9, 'AED' => 2.5);
    return isset($taux[$devise]) ? $taux[$devise] : 1;
}

// Chiffre d'affaires encaissé (dashboard, card "Chiffre d'affaire" du profil Commercial) et
// commission qui en découle, pour une année et une agence données. Basé sur payment::total()
// - pas payment::getReglementbyDate(), qui filtre sur A.id_user_added, une colonne qui n'existe
// pas sur crm_payment - déjà scopé sur l'utilisateur connecté par le filtrage id_user_added
// (sur crm_facture, via jointure) intégré à payment::total() pour tout non-superuser.
function commercialCAEtCommission($user, $year, $agence)
{
    $devises = array('DH', '€', '£', '$', 'AED');
    $parDevise = array();
    $totalDH = 0;
    foreach ($devises as $devise) {
        $montant = payment::total($year, false, $devise, $agence);
        $parDevise[$devise] = $montant;
        $totalDH += $montant * tauxConversionDH($devise);
    }
    $taux = $user->getTauxCommission();
    return array(
        'par_devise' => $parDevise,
        'total_dh' => $totalDH,
        'taux_commission' => $taux,
        'commission_dh' => $totalDH * ($taux / 100),
    );
}

// Agrégats comptables d'une période (toutes devises confondues, converties en DH) pour une agence
// donnée ou toutes agences (false) : chiffre d'affaires, encaissé, charges, créances (impayé),
// marge et les deux taux qui donnent vraiment un sens de pilotage à ces chiffres (taux de marge,
// taux de recouvrement) plutôt que de simples totaux isolés.
function statsGlobalesPeriode($from, $to, $agence = false)
{
    $devises = array('DH', '€', '£', '$', 'AED');
    $ca = 0;
    $encaissements = 0;
    $charges = 0;
    $creances = 0;
    foreach ($devises as $d) {
        $taux = tauxConversionDH($d);
        $ca += facture::getCAbyDate(false, $from, $to, $d, $agence) * $taux;
        $encaissements += payment::getReglementbyDate($from, $to, $d, $agence) * $taux;
        $charges += charge::getCharge($from, $to, $agence, $d) * $taux;
        $creances += facture::getCreanceByDate($from, $to, $d, $agence) * $taux;
    }
    $marge = $ca - $charges;
    return array(
        'ca' => $ca,
        'encaissements' => $encaissements,
        'charges' => $charges,
        'creances' => $creances,
        'marge' => $marge,
        'tauxMarge' => $ca > 0 ? ($marge / $ca) * 100 : 0,
        'tauxRecouvrement' => $ca > 0 ? ($encaissements / $ca) * 100 : 0,
    );
}

// Variation en % entre deux valeurs (pour les puces de tendance des KPI) - null si la valeur de
// référence est nulle (pas de "vs 0" qui donnerait un pourcentage absurde style +99900%).
function variationPourcent($actuel, $precedent)
{
    if (!$precedent) {
        return null;
    }
    return (($actuel - $precedent) / abs($precedent)) * 100;
}

// Chiffrement des identifiants réseaux sociaux client (com_client, onglet "Réseaux sociaux") -
// AES-256-CBC réversible (PAS un hash : ces mots de passe doivent pouvoir être réaffichés/copiés
// par un superadmin pour se connecter aux comptes réels du client, contrairement aux mots de passe
// de connexion à ce CRM qui sont hashés en bcrypt, cf. audit sécurité). Clé dans
// config.secrets.php (SOCIAL_CREDENTIALS_KEY, non versionné). IV aléatoire à chaque chiffrement,
// stocké à côté du texte chiffré (l'IV n'a pas besoin d'être secret, seule la clé l'est).
function encryptSocialCredential($plain)
{
    if ($plain === null || $plain === '') {
        return '';
    }
    $key = hex2bin(SOCIAL_CREDENTIALS_KEY);
    $iv = random_bytes(16);
    $cipher = openssl_encrypt($plain, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv) . ':' . base64_encode($cipher);
}

function decryptSocialCredential($encoded)
{
    if ($encoded === null || $encoded === '') {
        return '';
    }
    $parts = explode(':', $encoded, 2);
    if (count($parts) !== 2) {
        return '';
    }
    $key = hex2bin(SOCIAL_CREDENTIALS_KEY);
    $iv = base64_decode($parts[0]);
    $cipher = base64_decode($parts[1]);
    $plain = openssl_decrypt($cipher, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return $plain === false ? '' : $plain;
}

// Contrôle d'accès de la page "Réseaux sociaux" d'un client : soit un admin normalement connecté
// (session CRM habituelle), soit une session d'accès temporaire valide ($_SESSION['socialAccess'],
// posée par components/com_client/controleurs/socialaccess/router.php après vérification
// token+code - voir clientsocialtoken::verify()). Centralisé ici pour que le contrôleur
// (add/edit/delete) ET la page publique appellent exactement la même règle - jamais dupliquée.
function socialAccessAllowed($idClient, $plateforme = null)
{
    if (isset($_SESSION['user']) && $_SESSION['user']->hasDroit('edit', 'com_client')) {
        return true;
    }
    return socialAccessTokenAllows($idClient, $plateforme);
}

// Le déchiffrement/affichage du mot de passe est réservé au superadmin côté session normale (audit
// sécurité 2026-08-04) - mais un accès temporaire valide doit aussi pouvoir révéler, sa légitimité
// venant du couple token+code plutôt que du statut superadmin.
function socialRevealAllowed($idClient, $plateforme = null)
{
    if (isset($_SESSION['user']) && $_SESSION['user']->isSuperUser()) {
        return true;
    }
    return socialAccessTokenAllows($idClient, $plateforme);
}

function socialAccessTokenAllows($idClient, $plateforme = null)
{
    if (empty($_SESSION['socialAccessTokenId'])) {
        return false;
    }
    $t = clientsocialtoken::find($_SESSION['socialAccessTokenId']);
    if (!$t->isUsable()) {
        return false;
    }
    if (!$t->getClient() || $t->getClient()->getId() != intval($idClient)) {
        return false;
    }
    if ($t->getScopeType() === 'specific' && $plateforme !== null && $t->getScopePlateforme() !== $plateforme) {
        return false;
    }
    return true;
}

// Rappel bimestriel des documents manquants (dossier employé incomplet - CIN, contrat, engagement
// de confidentialité...) : appelée à chaque chargement de la liste RH (com_resourcehumaine/
// index.php, cas par défaut). Pas de vrai cron sur cet environnement (même principe que le rappel
// 90 jours des réseaux sociaux, cf. getAlertesUrgentes() ci-dessus) : un email n'est réellement
// renvoyé à un employé donné que si last_document_reminder est vide ou vieux de 60 jours ou plus,
// jamais à chaque visite de la page.
function verifierRappelsDocumentsManquantsRH()
{
    if (!defined('SMTP_HOST') || SMTP_HOST == '') {
        return;
    }
    $seuil = strtotime('-60 days');
    foreach (resourcehumaine::findAll() as $employe) {
        if ($employe->getActive() != 1 || !$employe->getEmail()) {
            continue;
        }
        $dernierRappel = $employe->getLastDocumentReminder();
        if ($dernierRappel && strtotime($dernierRappel) > $seuil) {
            continue;
        }
        $files = fileresourcehumaine::findAllByResourcehumaine($employe->getId());
        $manquants = fileresourcehumaine::documentsManquants($employe->getStatus(), $files);
        if (empty($manquants)) {
            continue;
        }
        if (envoyerEmailDocumentsManquantsRH($employe, $manquants)) {
            resourcehumaine::updateLastDocumentReminder($employe->getId(), date('Y-m-d'));
        }
    }
}

function envoyerEmailDocumentsManquantsRH($employe, $manquants)
{
    require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->CharSet = 'UTF-8';

        // Expéditeur explicitement direction@ (et non sales@/SMTP_USERNAME, l'identité d'envoi
        // partagée par défaut pour le reste de l'app) - demandé pour que l'employé voie ces
        // relances comme venant de la direction. setFrom() ne dépend pas de SMTP_USERNAME/
        // SMTP_PASSWORD (qui restent l'identité AUTHENTIFIÉE côté serveur SMTP) : la plupart des
        // fournisseurs acceptent un From différent du compte authentifié tant qu'il est sur le
        // même domaine ("send as"), ce qui est le cas ici.
        $mail->setFrom('direction@helloworld-agency.com', 'Hello World - Direction');
        $mail->addAddress($employe->getEmail(), trim($employe->getFirstName() . ' ' . $employe->getLastName()));
        // La direction doit voir passer ces relances (personnel ET stagiaires) dans sa propre
        // boîte - être l'expéditeur (From, ci-dessus) ne garantit pas en soi une copie reçue.
        $mail->addCC('direction@helloworld-agency.com');
        $mail->isHTML(true);

        $listeHtml = '';
        foreach ($manquants as $libelle) {
            $listeHtml .= '<li>' . htmlspecialchars($libelle) . '</li>';
        }
        $mail->Subject = "Documents manquants à votre dossier";
        $mail->Body = "Bonjour " . htmlspecialchars($employe->getFirstName()) . ",<br><br>"
            . "Merci de bien vouloir ajouter le(s) document(s) suivant(s), encore manquant(s) à votre dossier :<br>"
            . "<ul>" . $listeHtml . "</ul>"
            . "Merci de les transmettre à l'administration dès que possible — tant que ces documents ne sont pas fournis, "
            . "certaines demandes (congé notamment) resteront indisponibles.<br><br>"
            . "Cordialement,<br>L'équipe Hello World";
        $mail->AltBody = "Documents manquants à votre dossier : " . implode(', ', $manquants) . ". Merci de les transmettre à l'administration.";

        $sent = $mail->send();
        if ($sent) {
            copierEmailEnvoyeVersDossierEnvoyes($mail->getSentMIMEMessage());
        }
        return $sent;
    } catch (\Exception $e) {
        return false;
    }
}

// Identité d'envoi selon l'agence du client destinataire : id_agence=2
// (HELLOWORLDLABEL - FZCO, Dubai) envoie depuis sales@helloworldlabel.ae,
// toute autre agence (Maroc) garde l'identité sales@helloworld-agency.com
// par défaut. Jamais les mêmes identifiants SMTP entre les deux -- voir
// config.secrets.php (SMTP_*_DUBAI). Utilisé pour tout email adressé à un
// CLIENT (devis, facture, réclamation, rappel, relance, contrat) ; les
// emails internes (employés, notifications agence) restent sur l'identité
// par défaut, non concernés par cette fonction.
function getMailCredentialsForAgence($idAgence)
{
    if ((int) $idAgence === 2 && defined('SMTP_HOST_DUBAI') && SMTP_HOST_DUBAI !== '') {
        return array(
            'host' => SMTP_HOST_DUBAI,
            'port' => defined('SMTP_PORT_DUBAI') ? SMTP_PORT_DUBAI : 587,
            'username' => SMTP_USERNAME_DUBAI,
            'password' => SMTP_PASSWORD_DUBAI,
        );
    }
    return array(
        'host' => defined('SMTP_HOST') ? SMTP_HOST : null,
        'port' => defined('SMTP_PORT') ? SMTP_PORT : 587,
        'username' => defined('SMTP_USERNAME') ? SMTP_USERNAME : null,
        'password' => defined('SMTP_PASSWORD') ? SMTP_PASSWORD : null,
    );
}

// Les emails envoyés via mail()/SMTP n'atterrissent jamais tout seuls dans le dossier "Envoyés"
// de la boîte d'envoi - seul un client mail (Outlook, webmail) fait ça, en copiant lui-même le
// message envoyé vers ce dossier IMAP après coup. Un script PHP qui envoie n'est pas un "client
// mail" au sens IMAP : sans cet appel, ces envois (devis, factures) restent invisibles à la fois
// dans le webmail et dans Outlook (si Outlook est configuré en IMAP sur ce compte - en POP3 il ne
// verrait de toute façon jamais ce dossier). Volontairement best-effort : un échec ici ne doit
// jamais faire échouer l'envoi réel du mail au client, seulement priver l'expéditeur de sa copie
// de courtoisie. $host/$username/$password optionnels : par défaut la boîte sales@helloworld-
// agency.com (comportement historique, tous les appels existants), sinon la boîte fournie
// (ex. sales@helloworldlabel.ae pour un client Dubai, voir getMailCredentialsForAgence()).
function copierEmailEnvoyeVersDossierEnvoyes($rawMimeMessage, $host = null, $username = null, $password = null)
{
    if (!function_exists('imap_open')) {
        error_log('copierEmailEnvoyeVersDossierEnvoyes: extension imap absente');
        return false;
    }
    if (trim((string) $rawMimeMessage) === '') {
        error_log('copierEmailEnvoyeVersDossierEnvoyes: message MIME vide (getSentMIMEMessage)');
        return false;
    }
    $host = $host ?: (defined('SMTP_HOST') ? SMTP_HOST : null);
    $username = $username ?: (defined('SMTP_USERNAME') ? SMTP_USERNAME : null);
    $password = $password ?: (defined('SMTP_PASSWORD') ? SMTP_PASSWORD : null);
    if (!$host || !$username) {
        error_log('copierEmailEnvoyeVersDossierEnvoyes: identifiants IMAP manquants');
        return false;
    }
    foreach (array(
        '{' . $host . ':993/imap/ssl}',
        '{' . $host . ':993/imap/ssl/novalidate-cert}',
    ) as $mailbox) {
        $conn = @imap_open($mailbox . 'INBOX.Sent', $username, $password);
        if ($conn) {
            $ok = imap_append($conn, $mailbox . 'INBOX.Sent', $rawMimeMessage, '\\Seen');
            if (!$ok) {
                error_log('copierEmailEnvoyeVersDossierEnvoyes: imap_append a échoué sur ' . $mailbox . ' - ' . imap_last_error());
            }
            imap_close($conn);
            return $ok;
        }
        error_log('copierEmailEnvoyeVersDossierEnvoyes: imap_open a échoué sur ' . $mailbox . ' - ' . imap_last_error());
    }
    return false;
}

// Pointage web self-service (com_resourcehumaine) : l'employé ne peut pointer que depuis le
// Wi-Fi du bureau (config.secrets.php, POINTAGE_ALLOWED_IPS, CSV). Repli sûr par défaut : si la
// liste est vide (pas encore configurée) ou l'adresse du visiteur est vide, refuse - jamais
// permissif par accident. Compare REMOTE_ADDR et, s'il existe, le premier maillon de
// X-Forwarded-For (hébergement mutualisé/proxy en prod) : ce dernier est falsifiable par le
// client, mais l'enjeu ici est une politique de présence RH, pas une frontière de sécurité
// sensible - la même tolérance que le reste de l'app pour ce type de contrôle.
function pointageIpAutorisee()
{
    if (!defined('POINTAGE_ALLOWED_IPS') || trim(POINTAGE_ALLOWED_IPS) === '') {
        return false;
    }
    $autorisees = array_map('trim', explode(',', POINTAGE_ALLOWED_IPS));

    $ipsAVerifier = array();
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ipsAVerifier[] = $_SERVER['REMOTE_ADDR'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $premier = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        if ($premier !== '') {
            $ipsAVerifier[] = $premier;
        }
    }

    foreach ($ipsAVerifier as $ip) {
        if (in_array($ip, $autorisees, true)) {
            return true;
        }
    }
    return false;
}

// Ajoute "?v=<date de modification du fichier>" à un chemin d'asset local (CSS/JS sous assets/) -
// aucun des <link>/<script> de includes/tpl/top.php|bottom.php n'a de cache-buster, et il n'y a
// pas de .htaccess définissant de Cache-Control pour ces fichiers : un navigateur mobile (Safari
// iOS en particulier, plus agressif que desktop sur le cache heuristique) peut continuer à servir
// une version CSS/JS obsolète en cache pendant longtemps après une modification côté serveur, sans
// aucun moyen pour l'utilisateur de le savoir - un simple correctif qui "ne marche pas" peut donc
// n'être qu'un problème de cache, pas de code. $relatif est le même chemin que celui déjà écrit en
// dur dans les balises (ex: "assets/css/style.css"), relatif à la racine du projet.
function assetVersion($relatif)
{
    $absolu = __DIR__ . '/../../' . $relatif;
    return $relatif . (file_exists($absolu) ? '?v=' . filemtime($absolu) : '');
}