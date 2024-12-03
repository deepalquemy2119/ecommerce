<?php
session_start();  // Iniciar la sesión para gestionar el carrito

// Ruta al directorio donde están las imágenes
$image_dir = 'images/';

// Obtén todos los archivos de imagen del directorio
$imagenes = array_diff(scandir($image_dir), array('..', '.'));

// Mensaje de bienvenida
$mensaje_bienvenida = "¡Bienvenido a nuestra tienda ecommerce!";

// Ruta de la imagen 404
$error_image = './images/404/404.png'; // Ruta de la imagen 404

// Descripción de ejemplo por cada imagen
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

// Verificar si el usuario está logueado
$usuario_logueado = isset($_SESSION['usuario_id']); // Comprobar si hay un ID de usuario en la sesión

// Manejar el formulario de "Comprar"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['producto_id'])) {
    // Verificar si el usuario está logueado
    if (!$usuario_logueado) {
        // Redirigir a la página de login si no está logueado
        header('Location: login.php');
        exit;
    }

    $producto_id = $_POST['producto_id'];

    // Si el carrito no existe, lo creamos
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Si el producto ya está en el carrito, aumentamos la cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        $_SESSION['carrito'][$producto_id] = 1;  // Si no está, lo agregamos con cantidad 1
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/index.css" >
    <title>Ecommerce</title>
</head>
<body>

    <div class="container">
        <!-- bienvenida -->
        <div class="welcome-message">
            <h1><?php echo $mensaje_bienvenida; ?></h1>
        </div>

        <!-- mensaje de advertencia si NO logueado -->
        <?php if (!$usuario_logueado): ?>
            <div class="warning-message">
                <p>¡Debes iniciar sesión para poder realizar compras! 
                    <a href="./src/Log_Reg/login.php">Iniciar sesión</a> o 
                    <a href="./src/Log_Reg/register.php">Registrarse</a>
                </p>
            </div>
        <?php endif; ?>

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

                        <!-- si logueado, mostramos botón de compra -->
                        <?php if ($usuario_logueado): ?>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="producto_id" value="<?php echo pathinfo($imagen, PATHINFO_FILENAME); ?>">
                                <button type="submit" class="buy-button">Comprar</button>
                            </form>
                        <?php else: ?>
                            <!-- no logueado, mensaje de alerta -->
                            <button type="button" class="buy-button" onclick="alert('¡Debes iniciar sesión para comprar!')">Comprar</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Botón carrito con la cantidad de productos -->
        <a href="carrito.php">
            <button class="cart-button">
                Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)
            </button>
        </a>
    </div>

    <!-- Script de ocultar botones de compra si no está logueado -->
    <script>
        const usuarioLogueado = <?php echo json_encode($usuario_logueado); ?>;
        if (!usuarioLogueado) {
            const buyButtons = document.querySelectorAll('.buy-button');
            buyButtons.forEach(button => {
                button.style.display = 'none';
            });
        }
    </script>

</body>
</html>
