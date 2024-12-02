<?php
session_start();

// Verificar si el usuario está logueado y es un administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: admin.php");
    exit();  // Asegurarse de que el script no siga ejecutándose
}

// Conectar a la base de datos usando PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=ecommerce", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Función para agregar un producto
function agregarProducto($nombre, $descripcion, $precio, $stock) {
    global $pdo;
    $sql = "INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $descripcion, $precio, $stock]);
}

// Función para eliminar un producto
function eliminarProducto($id_producto) {
    global $pdo;
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_producto]);
}

// Función para editar un producto
function editarProducto($id_producto, $nombre, $descripcion, $precio, $stock) {
    global $pdo;
    $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $descripcion, $precio, $stock, $id_producto]);
}

// Función para eliminar varios productos seleccionados
function eliminarProductosSeleccionados($ids) {
    global $pdo;
    $sql = "DELETE FROM productos WHERE id IN (" . implode(',', array_map('intval', $ids)) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

// Si se recibe una solicitud para agregar un producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion'])) {
    if ($_POST['accion'] == 'agregar') {
        agregarProducto($_POST['nombre'], $_POST['descripcion'], $_POST['precio'], $_POST['stock']);
    } elseif ($_POST['accion'] == 'eliminar') {
        if (isset($_POST['productos'])) {
            eliminarProductosSeleccionados($_POST['productos']);
        }
    } elseif ($_POST['accion'] == 'editar') {
        if (isset($_POST['id_producto'])) {
            editarProducto($_POST['id_producto'], $_POST['nombre'], $_POST['descripcion'], $_POST['precio'], $_POST['stock']);
        }
    }
}

// Obtener los productos desde la base de datos
$sql = "SELECT * FROM productos";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/index.css">
    <title>Panel de Administración</title>
</head>
<body>

<header>
    <div class="container">
        <div class="welcome-message">
            <h1>Panel de Administración</h1>
            <p>Bienvenido <?php echo $_SESSION['usuario_nombre']; ?>, estás en el panel de administración.</p>
        </div>
    </div>
</header>

<main>
    <!-- Formulario para agregar un nuevo producto -->
    <div class="admin-actions">
        <h2>Agregar un nuevo producto</h2>
        <form action="crudAdmin.php" method="POST">
            <input type="hidden" name="accion" value="agregar">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required>
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" required></textarea>
            <label for="precio">Precio:</label>
            <input type="number" name="precio" step="0.01" required>
            <label for="stock">Stock:</label>
            <input type="number" name="stock" required>
            <button type="submit">Agregar Producto</button>
        </form>
    </div>

    <!-- Opciones de administración -->
    <div class="admin-actions">
        <h2>Acciones Administrativas</h2>
        
        <!-- Formulario para elegir acción en lote -->
        <form action="crudAdmin.php" method="POST">
            <select name="accion">
                <option value="eliminar">Eliminar productos seleccionados</option>
                <option value="editar">Editar productos seleccionados</option>
            </select>
            <button type="submit">Realizar acción</button>

            <!-- Lista de productos con checkboxes -->
            <div class="product-list">
                <h3>Lista de Productos</h3>
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="seleccionar_todos"></th>
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
                                <td><input type="checkbox" name="productos[]" value="<?php echo $producto['id']; ?>"></td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo $producto['descripcion']; ?></td>
                                <td><?php echo $producto['precio']; ?></td>
                                <td><?php echo $producto['stock']; ?></td>
                                <td>
                                    <!-- Formulario de eliminación individual -->
                                    <form action="crudAdmin.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                        <button type="submit">Eliminar</button>
                                    </form>
                                    <!-- Formulario de edición individual -->
                                    <a href="#editar_producto_form_<?php echo $producto['id']; ?>" onclick="document.getElementById('editar_producto_form_<?php echo $producto['id']; ?>').style.display = 'block'">Editar</a>
                                </td>
                            </tr>

                            <!-- Formulario de edición (oculto por defecto) -->
                            <div id="editar_producto_form_<?php echo $producto['id']; ?>" style="display:none;">
                                <h3>Editar Producto</h3>
                                <form action="crudAdmin.php" method="POST">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                    <label for="nombre">Nombre:</label>
                                    <input type="text" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
                                    <label for="descripcion">Descripción:</label>
                                    <textarea name="descripcion" required><?php echo $producto['descripcion']; ?></textarea>
                                    <label for="precio">Precio:</label>
                                    <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                                    <label for="stock">Stock:</label>
                                    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                                    <button type="submit">Actualizar Producto</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</main>

<!-- Botón para cerrar sesión -->
<div class="logout">
    <a href="logout.php"><button class="logout-button">Cerrar sesión</button></a>
</div>

<script>
// Seleccionar/deseleccionar todos los checkboxes
document.getElementById('seleccionar_todos').addEventListener('click', function() {
    let checkboxes = document.querySelectorAll('input[name="productos[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = document.getElementById('seleccionar_todos').checked;
    });
});
</script>

</body>
</html>
