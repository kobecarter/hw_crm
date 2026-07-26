<?php
// on utilise la fonction magique __autoload pour charger automatiquement tous les fichiers de classe lorsqu'un appel est demand&eacute;.

function my_autoloader($class_name)
{
    global $debug;
    $components_directory = $debug ? "companies-management/components/" : "hw-agences/components/";
    $directory = realpath((dirname(__DIR__))) . "/" .  $components_directory;
    $items = scandir($directory);
    foreach ($items as $item) {
        if ($item == "." || $item == "..") {
            continue;
        }
        if (is_dir($directory . $item)) {
            if (preg_match("/^com_/", $item)) {
                $class = $directory . $item . '/classes/' . $class_name . '.php';
                if (file_exists($class))
                    require_once($class);
            }
        }
    }

  
    
    $link = $_SERVER['REQUEST_URI'];
    if(strpos($link,"task")){
        require_once('PHPMailer-5.2-stable/PHPMailerAutoload.php');
    }
    
}
spl_autoload_register('my_autoloader');


try {
    // base de donn&eacute;es, serveur, login, pass, base de donn&eacute;es
    $db = dbfactory::factory($dbType, $host, $login, $password, $dataBaseName);
    global $db;
} catch (Exception $e) {
    die($e->getmessage());
}

