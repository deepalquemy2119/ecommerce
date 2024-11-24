<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar sesión activa
$session_id = session_id();
$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM cuentas_sesiones WHERE usuario_id = :usuario_id AND session_id = :session_id AND fecha_expiracion > NOW()");
$stmt->execute(['usuario_id' => $usuario_id, 'session_id' => $session_id]);
$sesion_activa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sesion_activa) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}



// Verificar si el usuario ha agregado un producto al carrito
if (isset($_GET['agregar_al_carrito'])) {
    $producto_id = $_GET['agregar_al_carrito'];
    $cantidad = 1; // Puedes hacer esto dinámico si quieres que el usuario elija la cantidad

    // Verificar si el producto ya está en el carrito de la base de datos
    $stmt = $pdo->prepare("SELECT * FROM carrito WHERE usuario_id = :usuario_id AND producto_id = :producto_id");
    $stmt->execute(['usuario_id' => $usuario_id, 'producto_id' => $producto_id]);
    $carrito_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($carrito_existente) {
        // Si el producto ya está en el carrito, actualizamos la cantidad
        $nueva_cantidad = $carrito_existente['cantidad'] + $cantidad;
        $update_stmt = $pdo->prepare("UPDATE carrito SET cantidad = :cantidad WHERE usuario_id = :usuario_id AND producto_id = :producto_id");
        $update_stmt->execute([
            'cantidad' => $nueva_cantidad,
            'usuario_id' => $usuario_id,
            'producto_id' => $producto_id
        ]);
    } else {
        // Si el producto no está en el carrito, lo insertamos
        $insert_stmt = $pdo->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (:usuario_id, :producto_id, :cantidad)");
        $insert_stmt->execute([
            'usuario_id' => $usuario_id,
            'producto_id' => $producto_id,
            'cantidad' => $cantidad
        ]);
    }

    // Redirigir para evitar reenvíos del formulario
    header('Location: mi-cuenta.php');
    exit;
}


// Obtener productos del carrito desde la base de datos
$stmt = $pdo->prepare("SELECT c.*, p.nombre, p.precio, p.imagen FROM carrito c
                       JOIN productos p ON c.producto_id = p.id
                       WHERE c.usuario_id = :usuario_id");
$stmt->execute(['usuario_id' => $usuario_id]);
$productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mostrar los productos del carrito
if ($productos_carrito) {
    echo '<h2>Tu carrito:</h2>';
    echo '<ul>';
    foreach ($productos_carrito as $producto) {
        echo '<li>';
        echo '<img src="./images/' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre']) . '" style="width: 50px;">';
        echo '<p>' . htmlspecialchars($producto['nombre']) . ' x ' . $producto['cantidad'] . ' - $' . number_format($producto['precio'], 2) . '</p>';
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<p>No tienes productos en tu carrito.</p>';
}





// Obtener productos del carrito
// $stmt = $pdo->prepare("SELECT SUM(cantidad) as total_items FROM carrito WHERE usuario_id = :usuario_id");
// $stmt->execute(['usuario_id' => $usuario_id]);
// $carrito_info = $stmt->fetch(PDO::FETCH_ASSOC);
// $total_items = $carrito_info['total_items'] ?: 0;



// Verificar si el carrito existe en la sesión
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    echo '<h2>Tu carrito:</h2>';
    echo '<ul>';
    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        // Obtener información del producto
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :producto_id");
        $stmt->execute(['producto_id' => $producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            echo '<li>';
            echo '<img src="./images/' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre']) . '" style="width: 50px;">';
            echo '<p>' . htmlspecialchars($producto['nombre']) . ' x ' . $cantidad . ' - $' . number_format($producto['precio'], 2) . '</p>';
            echo '</li>';
        }
    }
    echo '</ul>';
} else {
    echo '<p>No tienes productos en tu carrito.</p>';
}






// Obtener todos los productos disponibles
$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="./public/css/dashboard.css">
</head>
<body>

<header>
    <h1>Bienvenido a tu cuenta, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
</header>

<div class="dashboard">
    <!-- Carrito de compras -->
    <div class="carrito">
        <h2>Carrito de Compras</h2>
        <p>Productos en tu carrito: <span><?php echo $total_items; ?></span></p>
        <a href="carrito.php" class="btn">Ver carrito</a>
    </div>

    <!-- Productos disponibles -->
    <div class="productos-disponibles">
        <h2>Productos disponibles para compra:</h2>
        <div class="productos-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <img src="./images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>" />
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>

                    <!-- Botón de agregar al carrito -->
                    <a href="mi-cuenta.php?agregar_al_carrito=<?php echo $producto['id']; ?>" class="btn agregar-carrito">Agregar al carrito</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<a href="logout.php" class="btn-logout">Cerrar sesión</a>

</body>
</html>
