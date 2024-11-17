<?php
include 'conexion.php';

$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="./public/css/index.css">

    <title>Tienda</title>
    
</head>
<body>

    <header>
        <h3>Welcome to B2B E-Commerce </h3>
    </header>

    <div class="nav-btns">
        <!-- Botones de Login y Registro -->
        <a href="login.php">Iniciar sesión</a>
        <a href="registro.php">Crear cuenta</a>
    </div>


</body>
</html>
