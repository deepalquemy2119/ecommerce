<?php
// Conectar a la base de datos
include_once '../conexion/conexion.php';

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["imagen"])) {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    
    // Obtener la imagen cargada
    $imagen = $_FILES['imagen']['name'];
    $imagen_tmp = $_FILES['imagen']['tmp_name'];
    
    // Convertir la imagen a binario
    $imagen_binaria = file_get_contents($imagen_tmp);

    // Insertar el producto en la base de datos
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (:nombre, :descripcion, :precio, :stock)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':stock', $stock);
    $stmt->execute();
    
    // Obtener el ID del producto insertado
    $producto_id = $pdo->lastInsertId();

    // Insertar la imagen en la tabla 'imagenes' (como LONGBLOB)
    $stmt = $pdo->prepare("INSERT INTO imagenes (nombre, imagen, producto_id) VALUES (:nombre, :imagen, :producto_id)");
    $stmt->bindParam(':nombre', $imagen);
    $stmt->bindParam(':imagen', $imagen_binaria, PDO::PARAM_LOB);
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->execute();
    
    //echo "Producto e imagen agregados con éxito!";
    header("Location: productos.php");
    exit;

}

// Mostrar productos e imágenes desde la base de datos
$stmt = $pdo->prepare("SELECT DISTINCT p.nombre AS producto_nombre, i.nombre AS imagen_nombre, i.imagen 
                        FROM productos p 
                        LEFT JOIN imagenes i ON p.id = i.producto_id");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/productos.css">

    <title>Productos</title>
</head>
<body>

    <!-- Formulario para agregar productos -->
    <h2>Agregar Producto</h2>
    <form action="productos.php" method="POST" enctype="multipart/form-data">
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

    <hr>
    <br><br><br><br>
    <!-- Mostrar productos e imágenes -->
    <h2>Lista de Productos</h2>
    <?php foreach ($productos as $producto): ?>
        <div>
            <h3><?php echo htmlspecialchars($producto['producto_nombre']); ?></h3>
            <?php if ($producto['imagen']): ?>
                <img src="data:image/jpg;base64,<?php echo base64_encode($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['imagen_nombre']); ?>" width="150">
            <?php else: ?>
                <p>Este producto no tiene imagen.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</body>
</html>


<?php

/* subida productos e imágenes:

    formulario:
        nombre del producto,
        descripción,
        precio,
        stock
        imagen.
        La propiedad enctype="multipart/form-data" es necesaria para permitir la subida de archivos.

    procesamiento del formulario:

    es enviado ($_SERVER["REQUEST_METHOD"] == "POST"), se procesan los datos: el nombre, descripción, precio, stock, y la imagen.
    se usa file_get_contents() para convertir la imagen a formato binario (BLOB) y se inserta en la base de datos junto con el producto.

    recuperar y mostrar productos e imágenes:

    los productos y las imágenes asociadas
     con una consulta LEFT JOIN entre las
     tablas productos e imagenes.
    Se muestra la imagen en formato base64 para que se pueda visualizar en el navegador.  */


?>