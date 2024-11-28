<?php

//--------------- URLs de Drive -----------
// $asus_32_i9_4060 = 'https://drive.google.com/file/d/1KZdpSeJnCIpLaifrb52XcHIDuIW8L8PG/view?usp=sharing';

// $conector_super_video = 'https://drive.google.com/file/d/13QtQfYypIyyjjCBV_mqc3Z8Z0chIFIpa/view?usp=drive_link';

// $ram_8_ddr4 = 'https://drive.google.com/file/d/11Sh6OnqeHEDkMPbQWPoleTir8UiOtEHM/view?usp=drive_link';

// $monitor_32_ref_160 = 'https://drive.google.com/file/d/1wxVYRGVnVB4mrAK5D5Bcrmtpoj20oS_c/view?usp=drive_link';

// $mouse_genius_ergon = 'https://drive.google.com/file/d/1PqmXyFH8yrrqWova8-K77ZT5I44l9k0U/view?usp=drive_link';

// $on_404 = 'https://drive.google.com/file/d/1fXs6B6CC9w6x6nbsjuAxkWbaO7HKzBWf/view?usp=drive_link';

// $pendrive_32 = 'https://drive.google.com/file/d/10Qz7FNalasPRAVhjqx-Ardde_7K6mWxH/view?usp=drive_link';

// $sombrero_descanso = 'https://drive.google.com/file/d/1_1NXihHDzYH07onVeIbEBhZGYQkSLA3w/view?usp=drive_link';

// $teclado_blanco_mec = 'https://drive.google.com/file/d/1fLxGPH44skABHWA3ZhUdF3LV4mNyOVee/view?usp=drive_link';



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
    
    // Obtener las imágenes de un producto específico
    $producto_id = 1; // Suponiendo que estamos obteniendo las imágenes del producto con ID = 1
    $sql = "SELECT nombre_imagen, url_imagen FROM imagenes WHERE producto_id = :producto_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Obtener todas las imágenes del producto
    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die(); // Si hay un error, se detiene la ejecución
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto</title>
</head>
<body>
    <h1>Imágenes del Producto</h1>
    <div class="imagenes">
        <?php
        if (count($imagenes) > 0) {
            foreach ($imagenes as $imagen) {
                echo "<div class='imagen'>";
                echo "<img src='" . htmlspecialchars($imagen['url_imagen']) . "' alt='" . htmlspecialchars($imagen['nombre_imagen']) . "' />";
                echo "</div>";
            }
        } else {
            echo "<p>No hay imágenes disponibles para este producto.</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn = null;
?>
