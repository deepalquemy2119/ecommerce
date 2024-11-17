<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado y si tiene el rol de admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: index.php'); // Si no es admin, redirigir al inicio
    exit;
} elseif // Procesar el formulario para cargar productos 
    ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    
    // Subir imagen
    $imagen = '';
    if (isset($_FILES['imagen'])) {
        $imagen = 'uploads/' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
    }

    // Insertar producto en la base de datos
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria, imagen) 
                           VALUES (:nombre, :descripcion, :precio, :categoria, :imagen)");
    $stmt->execute([
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => $precio,
        'categoria' => $categoria,
        'imagen' => $imagen
    ]);
    
    // Redirigir después de cargar el producto
    header('Location: cargar_producto.php');
    exit;
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
        <input type="file" name="imagen"><br>

        <button type="submit">Cargar Producto</button>
    </form>
</body>
</html>
