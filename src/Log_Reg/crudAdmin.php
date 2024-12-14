<?php
session_start();

include_once '../conexion/conexion.php';

// Usuario está logueado y si es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Mostrar productos
try {
    $stmt = $pdo->prepare("SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock FROM productos p");
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productos = [];
    echo "Error al obtener productos: " . $e->getMessage();
}

// CRUD para productos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        // Insertar producto
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        // Procedimiento almacenado para insertar un producto
        $stmt = $pdo->prepare("CALL insertar_producto(?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock]);

        // Después de agregar un producto, vamos a:
        header("Location: crudAdmin.php");
        exit();
    }

    if (isset($_POST['delete_product'])) {
        // Eliminar producto
        $producto_id = $_POST['producto_id'];
        $stmt = $pdo->prepare("CALL eliminar_producto(?)");
        $stmt->execute([$producto_id]);

        // Después de eliminar un producto, vamos a:
        header("Location: crudAdmin.php");
        exit();
    }

    if (isset($_POST['edit_product'])) {
        // Editar producto
        $producto_id = $_POST['producto_id'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        // Procedimiento almacenado para actualizar un producto
        $stmt = $pdo->prepare("CALL actualizar_producto(?, ?, ?, ?, ?)");
        $stmt->execute([$producto_id, $nombre, $descripcion, $precio, $stock]);

        // Después de editar un producto, vamos a:
        header("Location: crudAdmin.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        // Cerrar sesión
        session_unset();
        session_destroy();
        header("Location: ../../index.php");
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

    <!-- ------------------ Local CSS --------------- -->
    <link rel="stylesheet" href="public/css/crudAdmin.css.css">
</head>
<body>

<div class="container mt-5">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>

        <!-- Botón de cierre de sesión -->
        <form method="POST" action="crudAdmin.php" style="display:inline-block;">
            <button type="submit" name="logout" class="btn btn-danger">Cerrar sesión</button>
        </form>

        <!-- Formulario para agregar un producto -->
        <h2>Agregar Producto</h2>
        <form method="POST" action="crudAdmin.php" enctype="multipart/form-data">
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
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Producto</label>
                <input type="file" name="imagen" id="imagen" class="form-control">
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
                                <!-- Formulario para editar producto -->
                                <button class="m-3 btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $producto['id']; ?>">Editar</button>
                                <!-- Formulario para eliminar producto -->
                                <form method="POST" action="crudAdmin.php" style="display:inline-block;">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" name="delete_product" class=" btn btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal para editar producto -->
                        <div class="modal fade" id="editModal<?php echo $producto['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $producto['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $producto['id']; ?>">Editar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="crudAdmin.php" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                            <div class="mb-3">
                                                <label for="nombre" class="form-label">Nombre del Producto</label>
                                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="descripcion" class="form-label">Descripción</label>
                                                <textarea name="descripcion" class="form-control" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="precio" class="form-label">Precio</label>
                                                <input type="number" name="precio" class="form-control" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="stock" class="form-label">Stock</label>
                                                <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="imagen" class="form-label">Imagen del Producto (opcional)</label>
                                                <input type="file" name="imagen" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" name="edit_product" class="btn btn-primary">Actualizar Producto</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay productos registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>
