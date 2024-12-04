<?php
session_start();

// usuario está logueado o NO es un administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    // no es administrador, a admin.php
    header("Location: admin.php");
    exit();
} 

//las imágenes
$image_dir = './public/images/';

// los archivos de imagen del directorio
$imagenes = array_diff(scandir($image_dir), array('..', '.'));


$mensaje_bienvenida = "¡Panel de Administración!";

// imagen 404
$error_image = './public/images/404/404.png';

// description por cada imagen
$descripciones = [
    'asus_32_i9_4060' => 'Pantalla ASUS de 32" con procesador i9 y tarjeta gráfica RTX 4060.',
    'conector_super_video' => 'Conector de video de alta definición para dispositivos multimedia.',
    'ram_8_ddr4' => 'Memoria RAM DDR4 de 8GB para un rendimiento superior.',
    'monitor_32_ref_160' => 'Monitor de 32" con resolución 4K para un rendimiento increíble.',
    'mouse_genius_ergon' => 'Mouse ergonómico Genius, ideal para largas sesiones de trabajo.',
    'on_404' => 'Imagen no disponible.',
    'pendrive_32' => 'Pendrive de 32GB, rápido y confiable para tus datos.',
    'sombrero_descanso' => 'Sombrero cómodo para descansar en el sol.',
    'teclado_blanco_mec' => 'Teclado mecánico blanco con retroiluminación RGB.'
];






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
        <!-- mensaje de bienvenida al administrador -->
        <div class="welcome-message">
            <h1><?php echo $mensaje_bienvenida; ?></h1>
            <p>Bienvenido <?php echo $_SESSION['usuario_nombre']; ?>. Estás en el panel de administración.</p>
        </div>
</header>

<main>
<!-- Opciones de administración (agregar, eliminar productos, etc.) -->
         <div class="admin-actions">
            <h2>Acciones Administrativas</h2>
            
                <ul><a href="crudAdmin.php">Agregar nuevo producto</a></ul>
                <ul><a href="crudAdmin.php">Eliminar producto</a></ul>
                <ul><a href="crudAdmin.php">Editar producto</a></ul>
                <!-- Otras acciones que pueda necesitar el administrador -->
            
        </div>

        <!-- Botón para cerrar sesión -->
        <div class="logout">
            <a href="logout.php"><button class="logout-button">Cerrar sesión</button></a>
        </div>
    </div>
        <!-- cards imágenes -->
        <div class="gallery">
            <?php foreach ($imagenes as $imagen): ?>
                <div class="card">
                    <!-- ruta imagen con lógica de 404 si no carga -->
                    <img src="<?php echo $image_dir . $imagen; ?>" 
                         alt="Imagen producto" 
                         onerror="this.src='<?php echo $error_image; ?>';">

                    <div class="card-content">
                        <!-- todo de imagen -->
                        <h3><?php echo ucfirst(str_replace('_', ' ', pathinfo($imagen, PATHINFO_FILENAME))); ?></h3>
                        <p><?php echo isset($descripciones[pathinfo($imagen, PATHINFO_FILENAME)]) ? $descripciones[pathinfo($imagen, PATHINFO_FILENAME)] : 'Descripción no disponible.'; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </main>

        <footer>

        </footer>
       

</body>
</html>
