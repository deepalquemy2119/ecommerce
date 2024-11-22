<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si el usuario no está logueado, redirigir a login
    header('Location: login.php');
    exit;
}

// Verificar si la sesión sigue activa en la base de datos
$session_id = session_id();
$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM cuentas_sesiones WHERE usuario_id = :usuario_id AND session_id = :session_id AND fecha_expiracion > NOW()");
$stmt->execute(['usuario_id' => $usuario_id, 'session_id' => $session_id]);
$sesion_activa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sesion_activa) {
    // Si la sesión no es válida o ha expirado
    session_unset();
    session_destroy();
    header('Location: login.php'); // Redirigir a login
    exit;
}

// Lógica para agregar productos al carrito
if (isset($_GET['agregar_al_carrito'])) {
    $producto_id = $_GET['agregar_al_carrito'];

    // Verificar si el producto ya está en el carrito
    $stmt = $pdo->prepare("SELECT * FROM carrito WHERE usuario_id = :usuario_id AND producto_id = :producto_id");
    $stmt->execute(['usuario_id' => $usuario_id, 'producto_id' => $producto_id]);
    $producto_en_carrito = $stmt->fetch();

    if ($producto_en_carrito) {
        // Si el producto ya está en el carrito, solo actualizamos la cantidad
        $nueva_cantidad = $producto_en_carrito['cantidad'] + 1;
        $stmt = $pdo->prepare("UPDATE carrito SET cantidad = :cantidad WHERE id = :carrito_id");
        $stmt->execute(['cantidad' => $nueva_cantidad, 'carrito_id' => $producto_en_carrito['id']]);
    } else {
        // Si el producto no está en el carrito, lo insertamos
        $stmt = $pdo->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (:usuario_id, :producto_id, 1)");
        $stmt->execute(['usuario_id' => $usuario_id, 'producto_id' => $producto_id]);
    }

    // Redirigir para evitar que el formulario se envíe varias veces al actualizar
    header('Location: mi-cuenta.php');
    exit;
}

// Obtener todos los productos disponibles (no los que ya están en el carrito)
$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/dashboard.css">
    <title>Mi Cuenta</title>
</head>
<body>

<header>
    <h1>Bienvenido a tu cuenta, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
</header>

<div class="container">
    <h2>Productos disponibles para compra:</h2>
    <div class="productos-disponibles">
        <?php foreach ($productos as $producto): ?>
            <div class="producto">
                <img src="./images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>">
                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>

                <!-- Botón visible solo si el usuario está logueado -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="mi-cuenta.php?agregar_al_carrito=<?php echo $producto['id']; ?>" class="btn">Agregar al carrito</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<a href="logout.php" class="btn-logout">Cerrar sesión</a>

</body>
</html>
