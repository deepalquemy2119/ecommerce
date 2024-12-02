<?php
session_start();  // Iniciar sesión para destruirla

// Destruir todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir a la página de inicio de sesión
header("Location: ./src/Log_Reg/login.php");
exit();
?>
