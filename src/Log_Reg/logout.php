<?php
session_start();
//todas las variables de sesión
$_SESSION = array();

// destruir la sesión , borro cookie de la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]);
}

// destruir la sesión
session_destroy();

//al login
header("Location: ../../index.php");
exit;
?>
