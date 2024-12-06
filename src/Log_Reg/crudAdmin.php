<?php

// evito cache a la pagina
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Pragma: no-cache");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");




session_start();

// usuario logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

// sumar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["imagen"])) {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $imagen = $_FILES["imagen"]["name"];
    $imagen_tmp = $_FILES["imagen"]["tmp_name"];
    $upload_dir = "./public/images/";

    move_uploaded_file($imagen_tmp, $upload_dir . $imagen);

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (:nombre, :descripcion, :precio, :stock)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':stock', $stock);
    $stmt->execute();

    $producto_id = $conn->lastInsertId();

    $stmt = $conn->prepare("INSERT INTO imagenes (nombre, producto_id) VALUES (:nombre, :producto_id)");
    $stmt->bindParam(':nombre', $imagen);
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->execute();
}

// ver productos
$stmt = $conn->prepare("SELECT * FROM productos");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
    <h1>Bienvenido al Panel de Administración</h1>
    <p>¡Hola, <?php echo $_SESSION['usuario_nombre']; ?>!</p>

    <!-- Form agregar productos -->
    <h2>Agregar Producto</h2>
    <form action="crudAdmin.php" method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre del Producto:</label>
        <input type="text" name="nombre" required><br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" required></textarea><br><br>

        <label for="precio">Precio:</label>
        <input type="number" name="precio" step="0.01" required><br><br>

        <label for="stock">Stock:</label>
        <input type="number" name="stock" required><br><br>

        <label for="imagen">Imagen del Producto:</label>
        <input type="file" name="imagen" accept="image/*" required><br><br>

        <button type="submit">Agregar Producto</button>
    </form>

    <h2>Lista de Productos</h2>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($productos as $producto): ?>
        <tr>
            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
            <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($producto['precio']); ?> €</td>
            <td><?php echo htmlspecialchars($producto['stock']); ?></td>
            <td>
                <a href="editar_producto.php?id=<?php echo $producto['id']; ?>">Editar</a>
                <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <!-- cerrar sesión -->
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
