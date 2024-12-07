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

//conexion ddbb
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

//usando PDO para la conexion
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

// Función para insertar un producto desde procedure
function insertarProducto($nombre, $descripcion, $precio, $stock) {
    global $pdo;
    $stmt = $pdo->prepare("CALL insertar_producto(?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio, $stock]);
}


// Función para obtener un producto por ID desde procedure
function obtenerProducto($id) {
    global $pdo;
    $stmt = $pdo->prepare("CALL obtener_producto(?)");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve el producto
}


// Función para actualizar un producto desde procedure
function actualizarProducto($id, $nombre, $descripcion, $precio, $stock) {
    global $pdo;
    $stmt = $pdo->prepare("CALL actualizar_producto(?, ?, ?, ?, ?)");
    $stmt->execute([$id, $nombre, $descripcion, $precio, $stock]);
}

// Función para eliminar un producto desde procedure
function eliminarProducto($id) {
    global $pdo;
    $stmt = $pdo->prepare("CALL eliminar_producto(?)");
    $stmt->execute([$id]);
}


// uso de los procedures para ejecutar un crud

// procedimiento para insertar un producto
insertarProducto("Camiseta Roja", "Camiseta de algodón roja", 20.99, 50);

// procedimiento para obtener un producto
$producto = obtenerProducto(1);
echo "Producto: " . $producto['nombre'] . ", Precio: " . $producto['precio'];

// procedimiento para actualizar un producto
actualizarProducto(1, "Camiseta Roja", "Camiseta de algodón roja con logo", 25.99, 60);

// uso procedimiento para eliminar un producto
eliminarProducto(1);


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
