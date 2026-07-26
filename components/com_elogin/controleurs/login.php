<?php

if(isset($_GET['task']) && !empty($_GET['task'])) {
    $task = $_GET['task'];
    switch ($task) {
        case 'login' :
            login($_POST);
            break; 
        case 'logout' :
            logout();
            break;
    }
}

/* -------------------------------- Login -------------------------------- */
function login($data) {
   // Handle the standard login functionality
    if (isset($data['email']) && !empty($data['email']) && isset($data['password']) && !empty($data['password'])) {
        // Check if the Email and password are corrected for the last project
        $email = addslashes($data['email']);
        $password = $data['password'];
        $resourcehumaine = resourcehumaine::login($email, $password);
        if (isset($resourcehumaine) && $resourcehumaine->getId() > 0) {
            if($resourcehumaine->isActive()){
                $_SESSION['user'] = $resourcehumaine;
                $resourcehumaine->setConnected(true);
                echo '1'; // Connection successful
            }else{
                echo '3';
            }
        } else {
            echo '2'; // Email and password are incorrect
        }
    } else {
        echo '0'; // Required fields not provided
    }
}

/* -------------------------------- logout -------------------------------- */
function logout(){
	if(isset($_SESSION['user'])){
		unset($_SESSION['user']);
		echo '1';
		// Unset or expire the cookies
		setcookie('login', '', time() - 3600, '/');
		setcookie('username', '', time() - 3600, '/');
		setcookie("login_global", "", time() + 31536000, "/");
	}
}
?>