<?php
session_start();

$host = 'localhost';
$dbname = 'ecommerce';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

// Verificar si el carrito está vacío
if (empty($_SESSION['carrito'])) {
    echo "<h2>El carrito está vacío.</h2>";
    exit;
}

// Obtener los productos del carrito
$productos_carrito = $_SESSION['carrito'];

$total = 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h1>Tu Carrito de Compras</h1>

        <form method="POST" action="carrito.php">
            <?php foreach ($productos_carrito as $producto_id => $cantidad): ?>
                <?php
                // Obtener el producto desde la base de datos
                $sql = "SELECT p.id, p.nombre, p.precio FROM productos p WHERE p.id = :producto_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':producto_id', $producto_id);
                $stmt->execute();
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$producto) {
                    // Si el producto no existe, lo eliminamos del carrito
                    unset($_SESSION['carrito'][$producto_id]);
                    continue;
                }

                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;
                ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?> (<?php echo $cantidad; ?>)</h5>
                        <p class="card-text">Precio unitario: $<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="card-text">Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                        
                        <!-- Botón para eliminar el producto -->
                        <button type="submit" name="eliminar" value="<?php echo $producto_id; ?>" class="btn btn-danger">Eliminar</button>
                    </div>
                </div>
            <?php endforeach; ?>

            <h3 class="mt-3">Total: $<?php echo number_format($total, 2); ?></h3>

            <!-- Botones para ir al inicio o cerrar sesión -->
            <div class="mt-4">
                <a href="../../index.php" class="btn btn-primary">Volver a la Tienda</a>
                <a href="../../index.php" class="btn btn-warning">Cerrar Sesión</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Eliminar producto del carrito
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $producto_id = $_POST['eliminar'];
    unset($_SESSION['carrito'][$producto_id]); // Eliminar el producto del carrito
    header('Location: carrito.php'); // Redirigir de nuevo a carrito.php
    exit;
}
?>
