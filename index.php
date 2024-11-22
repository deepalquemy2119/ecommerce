<?php
// index.php
session_start();
include_once 'conexion.php';

// Verificar si el usuario está logueado
$is_logged_in = isset($_SESSION['usuario_id']) ? true : false;

// Consultar productos de la base de datos
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
    <h3>Bienvenido a nuestra Tienda Online</h3>
</header>

<!-- Navegación y botones -->
<div class="nav-btns">
    <?php if (!$is_logged_in): ?>
        <!-- Mostrar los botones de login/registro si no está logueado -->
        <div id="login-acciones">
            <p>Para comprar, <a href="login.php">Inicia sesión</a> o <a href="registro.php">Crea una cuenta</a>.</p>
        </div>
    <?php else: ?>
        <!-- Si está logueado, mostrar los enlaces a cuenta y cerrar sesión -->
        <a href="logout.php">Cerrar sesión</a>
        <a href="mi-cuenta.php">Mi cuenta</a>
    <?php endif; ?>
</div>

<!-- Mostrar los productos en tarjetas -->
<div class="productos">
    <?php foreach ($productos as $producto): ?>
        <div class="producto-card">
            <img src="./images/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" onError="this.onerror=null;this.src='./images/default.jpg';">
            <h4><?php echo $producto['nombre']; ?></h4>
            <p><?php echo $producto['descripcion']; ?></p>
            <p>$<?php echo $producto['precio']; ?></p>

            <!-- Solo mostrar los botones de agregar al carrito si está logueado -->
            <?php if ($is_logged_in): ?>
                <a href="mi-cuenta.php?agregar_al_carrito=<?php echo $producto['id']; ?>" class="btn-agregar">Agregar al carrito</a>
            <?php else: ?>
                <p><a href="login.php" class="btn-login">Inicia sesión</a> o <a href="registro.php" class="btn-registro">Crea una cuenta</a> para comprar.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
