<?php
session_start(); 


$image_dir = './public/images/';

// archivos de imagen del directorio
$imagenes = array_diff(scandir($image_dir), array('..', '.'));


$mensaje_bienvenida = "¡Bienvenido a nuestra tienda ecommerce!";

// imagen 404
$error_image = './public/images/404/404.png'; // Ruta de la imagen 404



//el usuario está logueado??
$usuario_logueado = isset($_SESSION['usuario_id']); 
// hay un ID de usuario en la sesión??

//formulario de "Comprar"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['producto_id'])) {
    if (!$usuario_logueado) {
        // a login si no está logueado
        header('Location: ./src/Log_Reg/login.php');
        exit;
    }

    $producto_id = $_POST['producto_id'];

    // carrito no existe??, lo creamos
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    //el producto ya está en el carrito, aumentamos la cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        $_SESSION['carrito'][$producto_id] = 1;  
        // no está, lo agregamos con cantidad 1
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

  <!-- ocultar botones de compra si no está logueado -->
  <script>
        const usuarioLogueado = <?php echo json_encode($usuario_logueado); ?>;
        if (!usuarioLogueado) {
            const buyButtons = document.querySelectorAll('.buy-button');
            buyButtons.forEach(button => {
                button.style.display = 'none';
            });
        }
    </script>



    <div class="container">
       
        <div class="welcome-message">
            <h1><?php echo $mensaje_bienvenida; ?></h1>
        </div>

       
        <?php if (!$usuario_logueado): ?>


            <div class="warning-message">
                <p>¡Debes iniciar sesión para poder realizar compras! 
                    <a href="./src/Log_Reg/login.php">Iniciar sesión</a> o 
                    <a href="./src/Log_Reg/register.php">Registrarse</a>
                </p>
            </div>
        <?php endif; ?>

        <!------------------------ cards imágenes --------------------------->
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

        <!--------------------------- carrito con la cantidad de productos -------------------------->
        <a href="carrito.php">
            <button class="cart-button">
                Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)
            </button>
        </a>
    </div>

  

</body>
</html>