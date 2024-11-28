<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener los productos
$stmt = $conn->prepare("SELECT * FROM productos");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agregar producto al carrito
if (isset($_GET['agregar'])) {
    $producto_id = $_GET['agregar'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verificar si el producto ya está en el carrito
    $stmt = $conn->prepare("SELECT * FROM carrito WHERE usuario_id = :usuario_id AND producto_id = :producto_id");
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // Si no está en el carrito, agregarlo
        $stmt = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id) VALUES (:usuario_id, :producto_id)");
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
    }
}

// Mostrar productos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
</head>
<body>
    <h1>Productos</h1>
    <ul>
        <?php foreach ($productos as $producto): ?>
            <li>
                <img src="public/images/<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>" width="100">
                <h3><?= $producto['nombre'] ?></h3>
                <p><?= $producto['descripcion'] ?></p>
                <p>Precio: $<?= $producto['precio'] ?></p>
                <a href="products.php?agregar=<?= $producto['id'] ?>">Agregar al carrito</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
