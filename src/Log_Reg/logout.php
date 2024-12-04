<?php
session_start();

// Destruir todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

//página de inicio de sesión
header("Location: ./src/Log_Reg/login.php");
exit();
?>
