<?php

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

//---------------- Imagen a BLOB(bytes) -----------------
/*  // Ruta al archivo de imagen
$archivoImagen = './images/404/404.png';

// Leer el archivo en formato binario
$contenidoImagen = file_get_contents($archivoImagen);

// Crear un Blob en base a los datos binarios
$blob = $contenidoImagen;

// Mostrar la longitud del Blob (opcional)
echo 'Tamaño del Blob: ' . strlen($blob) . ' bytes';

// Aquí, $blob ahora contiene los datos binarios de la imagen     */

// file_get_contents(): Esta función lee el archivo de imagen y devuelve su contenido como una cadena de texto binaria.
// Blob: En PHP, un Blob sería simplemente una cadena de bytes binarios, que es exactamente lo que obtienes con file_get_contents().

// Guardar el Blob como un archivo en el servidor

// Una vez que hayas creado el Blob (ya sea desde un archivo o desde Base64), puedes guardarlo en el servidor usando file_put_contents():


// // Guardar el Blob (en este caso, contenido binario) en un nuevo archivo
// $nombreArchivo = 'imagen_guardada.jpg';
// file_put_contents($nombreArchivo, $blob);

// // Confirmación
// echo "La imagen ha sido guardada como '$nombreArchivo'.";


// Resumen de los pasos:

//     Leer el archivo de imagen: Usa file_get_contents() para obtener la imagen como datos binarios.
//     Convertir Base64 a binario: Usa base64_decode() para convertir cadenas Base64 en datos binarios.
//     Guardar el Blob: Utiliza file_put_contents() para almacenar el contenido binario en un archivo.

//---------------------------------------------------------


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
    $sql = "SELECT nombre, imagen, producto_id FROM imagenes WHERE producto_id = :producto_id";
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
    <h1>Enlaces de las Imágenes</h1>
    <ul>
        <?php
        // Recorrer el array y mostrar los enlaces
        foreach ($imagenes_drive as $imagen) {
            // Obtener la URL directamente del array
            echo "<li><a href='" . htmlspecialchars($imagen[0]) . "' target='_blank'>Ver Imagen</a></li>";
        }
        ?>
    </ul>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn = null;
?>
