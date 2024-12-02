<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "ecommerce";

try {
    // Crear la conexión usando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Array de imágenes en Google Drive
   //--------------- URLs de Drive -----------
$imagenes_drive = [ [$asus_32_i9_4060 = 'https://drive.google.com/file/d/1KZdpSeJnCIpLaifrb52XcHIDuIW8L8PG/view?usp=sharing'],

[$conector_super_video = 'https://drive.google.com/file/d/13QtQfYypIyyjjCBV_mqc3Z8Z0chIFIpa/view?usp=drive_link'],

[$ram_8_ddr4 = 'https://drive.google.com/file/d/11Sh6OnqeHEDkMPbQWPoleTir8UiOtEHM/view?usp=drive_link'],

[$monitor_32_ref_160 = 'https://drive.google.com/file/d/1wxVYRGVnVB4mrAK5D5Bcrmtpoj20oS_c/view?usp=drive_link'],

[$mouse_genius_ergon = 'https://drive.google.com/file/d/1PqmXyFH8yrrqWova8-K77ZT5I44l9k0U/view?usp=drive_link'],

[$on_404 = 'https://drive.google.com/file/d/1fXs6B6CC9w6x6nbsjuAxkWbaO7HKzBWf/view?usp=drive_link'],

[$pendrive_32 = 'https://drive.google.com/file/d/10Qz7FNalasPRAVhjqx-Ardde_7K6mWxH/view?usp=drive_link'],

[$sombrero_descanso = 'https://drive.google.com/file/d/1_1NXihHDzYH07onVeIbEBhZGYQkSLA3w/view?usp=drive_link'],

[$teclado_blanco_mec = 'https://drive.google.com/file/d/1fLxGPH44skABHWA3ZhUdF3LV4mNyOVee/view?usp=drive_link'],

];

    // Recorrer el array de imágenes y procesarlas
    foreach ($imagenes_drive as $imagen) {
        // Obtener el ID del archivo desde la URL de Google Drive
        preg_match('/d\/(.*?)\//', $imagen[0], $matches);
        $file_id = $matches[1];

        // Construir la URL de descarga directa
        $download_url = "https://drive.google.com/uc?export=download&id=" . $file_id;

        // Descargar la imagen desde Google Drive
        $archivo_imagen = file_get_contents($download_url);

        // Nombre de la imagen (opcional, basado en el ID o en el nombre de archivo original)
        $nombre_imagen = "imagen_" . $file_id . ".jpg";  // Puedes personalizar el nombre

        // ID del producto al que pertenece la imagen (este debe ser válido)
        $producto_id = 1;  // Este es un ejemplo, ajusta según tu necesidad

        // Verificar si el producto existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM productos WHERE id = :producto_id");
        $stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // El producto no existe, manejar el error o insertar un nuevo producto
            echo "El producto con ID $producto_id no existe, insertando nuevo producto.\n";
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) 
                                    VALUES ('Nuevo Producto', 'Descripción del producto', 100.00, 10)");
            $stmt->execute();
            // Obtener el ID del nuevo producto insertado
            $producto_id = $conn->lastInsertId();
        }

        // Llamar al procedimiento almacenado para insertar la imagen
        $stmt = $conn->prepare("CALL insertar_imagen(:nombre_imagen, :archivo_imagen, :producto_id)");
        $stmt->bindParam(':nombre_imagen', $nombre_imagen);
        $stmt->bindParam(':archivo_imagen', $archivo_imagen, PDO::PARAM_LOB);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
    }

    echo "Todas las imágenes han sido insertadas correctamente.";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$conn = null;
?>
