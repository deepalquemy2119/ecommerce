<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Por defecto en XAMPP es root
$password = ""; // En XAMPP por defecto no tiene contraseña
$dbname = "ecommerce"; // El nombre de tu base de datos

try {
    // Crear la conexión usando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Establecer el modo de error de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los productos de la base de datos
    $stmt = $conn->prepare("SELECT * FROM productos");
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los productos

    // Lista de URLs de las imágenes (de Google Drive)
    $imagenes = [
        ['asus_32_i9_4060.jpg', 'https://drive.google.com/file/d/1KZdpSeJnCIpLaifrb52XcHIDuIW8L8PG/view?usp=sharing', 'Monitor Asus 32" con i9 y 4060', 1],
        ['conector_super_video.jpg', 'https://drive.google.com/file/d/13QtQfYypIyyjjCBV_mqc3Z8Z0chIFIpa/view?usp=drive_link', 'Conector Super Video para multimedia', 2],
        ['ram_8_ddr4.jpg', 'https://drive.google.com/file/d/11Sh6OnqeHEDkMPbQWPoleTir8UiOtEHM/view?usp=drive_link', 'Memoria RAM DDR4 8GB', 3],
        ['monitor_32_ref_160.jpg', 'https://drive.google.com/file/d/1wxVYRGVnVB4mrAK5D5Bcrmtpoj20oS_c/view?usp=drive_link', 'Monitor 32" referencia 160', 4],
        ['mouse_genius_ergon.jpg', 'https://drive.google.com/file/d/1PqmXyFH8yrrqWova8-K77ZT5I44l9k0U/view?usp=drive_link', 'Mouse Genius ergonómico', 5],
        ['pendrive_32.jpg', 'https://drive.google.com/file/d/10Qz7FNalasPRAVhjqx-Ardde_7K6mWxH/view?usp=drive_link', 'Pendrive de 32GB', 7],
        ['sombrero_descanso.jpg', 'https://drive.google.com/file/d/1_1NXihHDzYH07onVeIbEBhZGYQkSLA3w/view?usp=drive_link', 'Sombrero de descanso', 8],
        ['teclado_blanco_mec.jpg', 'https://drive.google.com/file/d/1fLxGPH44skABHWA3ZhUdF3LV4mNyOVee/view?usp=drive_link', 'Teclado mecánico blanco', 9]
    ];

    // Función para convertir la URL de Google Drive a la URL directa de la imagen
    function convertirUrlDrive($url_drive) {
        // Expresión regular para extraer el ID del archivo de la URL
        if (preg_match('/https:\/\/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)\//', $url_drive, $matches)) {
            $file_id = $matches[1];
            // Retornar la URL directa de la imagen
            return "https://drive.google.com/uc?export=view&id=" . $file_id;
        }
        return $url_drive; // Si no es una URL válida de Google Drive, retornar la URL original
    }

    // Función para extraer el nombre de la imagen sin la extensión (de una URL de Google Drive)
    function getImagenNombreSinExtension($url_imagen) {
        // Obtiene el nombre del archivo de la URL
        $filename = basename($url_imagen); // Obtiene el nombre de archivo con la extensión
        // Quita la extensión .jpg, .png, .jpeg, etc.
        $nombre_sin_extension = pathinfo($filename, PATHINFO_FILENAME);
        return $nombre_sin_extension;
    }

    // ------------------------
    // Insertar los productos primero
    $producto_ids = []; // Para almacenar los IDs de los productos insertados

    foreach ($imagenes as $url_imagen) {
        // Extraer la URL de la imagen (segundo valor del array)
        $url_imagen = $url_imagen[1];  // Aquí estamos extrayendo la URL (el segundo valor de cada sub-array)

        // Extraer el nombre de la imagen sin la extensión
        $nombre_imagen_sin_extension = getImagenNombreSinExtension($url_imagen);

        // Insertar el producto en la base de datos (si no existe)
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio) VALUES (?, ?, ?)");
        $descripcion = "Descripción para " . $nombre_imagen_sin_extension; // Descripción por defecto
        $precio = rand(50, 500); // Precio aleatorio (o lo que sea que necesites)
        $stmt->execute([$nombre_imagen_sin_extension, $descripcion, $precio]);
        
        // Obtener el id del producto recién insertado
        $producto_ids[] = $conn->lastInsertId(); // Guardar el ID del producto insertado
    }

    // ------------------------
    // Ahora, insertar las imágenes asociadas a los productos
    foreach ($imagenes as $index => $url_imagen) {
        // Asegúrate de usar el ID correcto del producto en la lista
        $producto_id = $producto_ids[$index];

        // Extraer la URL de la imagen (el segundo valor del array)
        $url_imagen = $url_imagen[1];  // Aquí estamos extrayendo la URL (el segundo valor de cada sub-array)

        // Convertir la URL de Google Drive a la URL directa de la imagen
        $url_imagen_directa = convertirUrlDrive($url_imagen);

        // Insertar la imagen en la base de datos
        $stmt = $conn->prepare("INSERT INTO imagenes (nombre_imagen, url_imagen, descripcion, producto_id) VALUES (?, ?, ?, ?)");
        $nombre_imagen = getImagenNombreSinExtension($url_imagen); // Usar el nombre de la imagen sin la extensión
        $descripcion_imagen = "Imagen de " . $nombre_imagen;
        $stmt->execute([$nombre_imagen, $url_imagen_directa, $descripcion_imagen, $producto_id]);
    }

    echo "Datos insertados correctamente.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    die(); // Si hay un error, se detiene la ejecución
}

// Cerrar la conexión
$conn = null;
?>

<!-- HTML para mostrar productos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce - Bienvenido</title>
    <link rel="stylesheet" href="./public/css/index.css">
</head>
<body>
    <header>
        <!-- Barra superior con botones de Login y Register -->
        <div class="top-bar">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Register</a>
        </div>
        <!-- Mensaje de bienvenida -->
        <h1>¡Bienvenido a nuestro E-commerce!</h1>
    </header>

    <main>
        <!-- Mostrar los productos -->
        <div class="product-list">
            <?php
            if (count($productos) > 0) {
                // Mostrar cada producto
                foreach ($productos as $producto) {
                    echo "<div class='product'>";
                    
                    // Verificar si la URL de la imagen existe
                    if (!empty($producto['url_imagen'])) {
                        // Convertir la URL de Google Drive a la URL directa de la imagen
                        $url_imagen = convertirUrlDrive($producto['url_imagen']);
                        echo "<img src='" . htmlspecialchars($url_imagen) . "' alt='" . htmlspecialchars($producto["nombre"]) . "' />";
                        
                        // Obtener el nombre de la imagen sin la extensión
                        $nombre_imagen_sin_extension = getImagenNombreSinExtension($producto['url_imagen']);
                        echo "<h3>" . htmlspecialchars($nombre_imagen_sin_extension) . "</h3>"; // Mostrar el nombre sin extensión
                    } else {
                        // Si no hay imagen, mostrar una imagen por defecto
                        echo "<img src='./images/default-product.jpg' alt='Imagen no disponible' />";
                    }
                    
                    echo "<h2>" . htmlspecialchars($producto["nombre"]) . "</h2>";
                    echo "<p>" . htmlspecialchars($producto["descripcion"]) . "</p>";
                    echo "<p>Precio: $ " . number_format($producto["precio"], 2) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No hay productos disponibles.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 E-commerce</p>
    </footer>
</body>
</html>

<?php
// Cerrar la conexión
$conn = null;
?>
