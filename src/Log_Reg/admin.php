
<?php
session_start();
include_once '../conexion/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}





if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    // Si no es admin, redirigir a la página principal
    header('Location: ../../index.php');

    exit;
}


$stmt = $conn->prepare("SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, i.imagen 
                        FROM productos p
                        LEFT JOIN imagenes i ON p.id = i.producto_id");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si es admin, redirige a crudAdmin.php
if ($_SESSION['user_type'] == 'admin') {
    header("Location: crudAdmin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>

    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>

    <!-- Mostrar productos en formato de tarjetas -->
    <div class="products-container">
        <?php foreach ($productos as $producto): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" width="150">
                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <p>Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
                <p>Stock: <?php echo htmlspecialchars($producto['stock']); ?></p>
                <?php if ($_SESSION['user_type'] == 'cliente'): ?>
                    <form action="comprar.php" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        <button type="submit" class="btn btn-primary">Comprar</button>
                    </form>
                <?php else: ?>
                    <button disabled class="btn btn-secondary">No disponible para admins</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>

