<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado y si tiene el rol de admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: index.php'); // Si no es admin, redirigir al inicio
    exit;
}

// Procesar el formulario para cargar productos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $imagen = '';

    // Validación de precio
    if (!is_numeric($precio) || $precio <= 0) {
        $error = 'El precio debe ser un número positivo.';
    }

    // Subir imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Verificar el tipo de imagen (por ejemplo, jpg, png, jpeg)
        $imagen_tipo = mime_content_type($_FILES['imagen']['tmp_name']);
        if (in_array($imagen_tipo, ['image/jpeg', 'image/png', 'image/gif'])) {
            $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen)) {
                $error = 'Error al cargar la imagen.';
            }
        } else {
            $error = 'Solo se permiten imágenes en formato JPG, PNG o GIF.';
        }
    } else {
        $error = 'Por favor, sube una imagen.';
    }

    // Si no hay errores, insertar el producto en la base de datos
    if (!isset($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, imagen) 
                                   VALUES (:nombre, :descripcion, :precio, :categoria, :imagen)");
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'categoria' => $categoria,
                'imagen' => $imagen
            ]);
            // Redirigir a la página de productos después de cargar el producto
            header('Location: productos.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error al guardar el producto: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Producto</title>
</head>
<body>

<h2>Cargar Nuevo Producto</h2>

<!-- Mostrar mensajes de error si existen -->
<?php if (isset($error)) { ?>
    <div style="color: red;"><?php echo $error; ?></div>
<?php } ?>

<form action="cargar_producto.php" method="POST" enctype="multipart/form-data">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required><br>
    
    <label for="descripcion">Descripción:</label>
    <textarea name="descripcion" required></textarea><br>
    
    <label for="precio">Precio:</label>
    <input type="number" name="precio" required><br>
    
    <label for="categoria">Categoría:</label>
    <input type="text" name="categoria" required><br>
    
    <label for="imagen">Imagen:</label>
    <input type="file" name="imagen" required><br>

    <button type="submit">Cargar Producto</button>
</form>

</body>
</html>
