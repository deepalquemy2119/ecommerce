
<?php


include_once '../conexion/conexion.php'; 




// Verifica si el usuario está logueado y si es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Mostrar productos
try {
    $stmt = $conn->prepare("SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock FROM productos p");
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si hay un error con la consulta, muestra un mensaje de error
    $productos = [];
    echo "Error al obtener productos: " . $e->getMessage();
}

// Operaciones de CRUD
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        // Insertar producto
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        // Preparar la inserción en la base de datos
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock]);

        // Redirigir después de agregar un producto
        header("Location: crudAdmin.php");
        exit();
    }

    if (isset($_POST['delete_product'])) {
        // Eliminar producto
        $producto_id = $_POST['producto_id'];
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);

        // Redirigir después de eliminar un producto
        header("Location: crudAdmin.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>

        <!-- Formulario para agregar un producto -->
        <h2>Agregar Producto</h2>
        <form method="POST" action="crudAdmin.php">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Producto</label>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre del producto" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Descripción del producto" required></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" name="precio" id="precio" class="form-control" placeholder="Precio del producto" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" placeholder="Cantidad en stock" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Agregar Producto</button>
        </form>

        <!-- Mostrar productos en tabla para editar o eliminar -->
        <h2 class="mt-5">Lista de Productos</h2>
        <?php if (count($productos) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                            <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                            <td>
                                <form method="POST" action="crudAdmin.php">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" name="delete_product" class="btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay productos registrados.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
