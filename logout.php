<?php
// logout.php
session_start();
include 'conexion.php';

// Eliminar la sesión de la base de datos
$session_id = session_id();
$stmt = $pdo->prepare("DELETE FROM cuentas_sesiones WHERE session_id = :session_id");
$stmt->execute(['session_id' => $session_id]);

// Cerrar sesión del servidor
session_unset();
session_destroy();

// Redirigir al login
header('Location: login.php');
exit;

?>
