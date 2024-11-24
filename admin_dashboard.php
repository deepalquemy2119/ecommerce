<?php
// admin_dashboard.php
session_start(); // Iniciar sesión

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php'); // Redirigir al login si no es admin
    exit();
}

echo "<h1>Bienvenido, " . $_SESSION['usuario_nombre'] . " (Administrador)</h1>";
// Aquí puedes agregar enlaces o botones para el CRUD de productos
echo "<a href='crud_producto.php'>Gestionar Productos</a><br>";
echo "<a href='logout.php'>Cerrar sesión</a>";
?>
