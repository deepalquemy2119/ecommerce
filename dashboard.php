<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'];

// Obtener los productos en el carrito del usuario
$stmt = $pdo->prepare("SELECT carrito.id AS carrito_id, productos.*, carrito.cantidad 
    FROM carrito
    JOIN productos ON carrito.producto_id = productos.id
    WHERE carrito.usuario_id = :usuario_id");
$stmt->execute(['usuario_id' => $usuario_id]);
$productos_en_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos disponibles (los que no están en el carrito)
$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Eliminar producto del carrito
if (isset($_GET['eliminar_del_carrito'])) {
    $carrito_id = $_GET['eliminar_del_carrito'];
    $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = :carrito_id");
    $stmt->execute(['carrito_id' => $carrito_id]);
    header('Location: dashboard.php');  // Redirigir para actualizar la vista
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="./public/css/dashboard.css">

    <title>Dashboard</title>
    
</head>
<body>

    <header>
        <h1>Bienvenido...  a su carrito: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
    </header>

    <div class="container">
        <?php if (empty($productos_en_carrito)): ?>
            <p>No tienes productos en el carrito.</p>
            <h2>Productos disponibles:</h2>
            <div class="productos-disponibles">
                <?php foreach ($productos as $producto): ?>
                    <div class="producto">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                        <a href="carrito.php?id=<?php echo $producto['id']; ?>" class="btn">Agregar al carrito</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2>Tu carrito:</h2>
            <div class="productos-en-carrito">
                <?php foreach ($productos_en_carrito as $producto): ?>
                    <div class="producto">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                        <p>Cantidad: <?php echo $producto['cantidad']; ?></p>
                        <a href="dashboard.php?eliminar_del_carrito=<?php echo $producto['carrito_id']; ?>" class="btn-eliminar">Eliminar del carrito</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botón para cerrar sesión -->
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>

</body>
</html>

<?php 

/*  logica si el carrito está vacío:

    vacío (empty($productos_en_carrito)), se muestra mensaje que no hay productos en el carrito y se muestra la lista de productos disponibles.
    Si hay productos en el carrito (!empty($productos_en_carrito)), se muestran esos productos con la opción de eliminarlos.

    productos disponibles:

    Los productos que están disponibles (es decir, no están en el carrito del usuario) se cargan desde la base de datos y se muestran en la sección debajo de "No tienes productos en el carrito".

    boton eliminar del carrito:

    Si el usuario tiene productos en el carrito, cada producto tiene un enlace para eliminarlo. Este enlace pasa el carrito_id como parámetro en la URL, lo que activa la lógica de eliminación.

    al eliminar un producto del carrito, lleva de nuevo al dashboard.php para actualizar la vista.     */


?>