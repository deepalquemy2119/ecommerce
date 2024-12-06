<?php
session_start();

 // conn a la base
 include('../../conexion/conexion.php');

// usuario está logueado?? y no es administrador
if (!isset($_SESSION['usuario_id']) && $_SESSION['tipo_usuario'] == 'admin') {
    
    header("Location: crudAdmin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['producto_id'])) {
    if (!$usuario_logueado) {
        header('Location: login.php');
        exit;
    }


    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id']; // uso id de logueado

//--------------
    // el producto ya está en el carrito de este usuario??
    $stmt = $conn->prepare("SELECT * FROM carrito WHERE usuario_id = :usuario_id AND producto_id = :producto_id");
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->bindParam(':producto_id', $producto_id);
    $stmt->execute();

     // si el producto ya está en el carrito, aumento cantidad
     if ($stmt->rowCount() > 0) {

        $stmt = $conn->prepare("UPDATE carrito SET cantidad = cantidad + 1 WHERE usuario_id = :usuario_id AND producto_id = :producto_id");
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
    } else {
    // si el producto no está , insertarlo
        $stmt = $conn->prepare("INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (:usuario_id, :producto_id, 1)");
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
    }

      // Redirigir a la página del carrito
      header('Location: ../../compras/carrito.php');
      exit;
  }

//----------
$image_dir = './public/images/';

//archivos de imagen del directorio
$imagenes = array_diff(scandir($image_dir), array('..', '.'));


$mensaje_bienvenida = "Panel";

// 404
$error_image = './public/images/404/404.png';

// $descripciones = [
//     'asus_32_i9_4060' => 'Pantalla ASUS de 32" con procesador i9 y tarjeta gráfica RTX 4060.',
//     'conector_super_video' => 'Conector de video de alta definición para dispositivos multimedia.',
//     'ram_8_ddr4' => 'Memoria RAM DDR4 de 8GB para un rendimiento superior.',
//     'monitor_32_ref_160' => 'Monitor de 32" con resolución 4K para un rendimiento increíble.',
//     'mouse_genius_ergon' => 'Mouse ergonómico Genius, ideal para largas sesiones de trabajo.',
//     'on_404' => 'Imagen no disponible.',
//     'pendrive_32' => 'Pendrive de 32GB, rápido y confiable para tus datos.',
//     'sombrero_descanso' => 'Sombrero cómodo para descansar en el sol.',
//     'teclado_blanco_mec' => 'Teclado mecánico blanco con retroiluminación RGB.'
// ];


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/index.css">
    <title>Seccion para Compras</title>
</head>
<body>

<header>
    <div class="container">
        <div class="welcome-message">
            <h1><?php echo $mensaje_bienvenida; ?></h1>
            <p>Bienvenido<?php echo $_SESSION['usuario_nombre']; ?>. Estás listo para comprar..??.</p>
        </div>
</header>

<main>


    
        <div class="logout">
            <a href="logout.php"><button class="logout-button">Cerrar sesión</button></a>
        </div>
    </div>
        <!----------------- cards imágenes ---------------->
        <div class="gallery">
            <?php foreach ($imagenes as $imagen): ?>
                <div class="card">
                    <!------------------ ruta imagen con lógica de 404 si no carga ----------------- -->
                    <img src="<?php echo $image_dir . $imagen; ?>" 
                         alt="Imagen producto" 
                         onerror="this.src='<?php echo $error_image; ?>';">

                    <div class="card-content">
                        <!--------------- imagen ---------------- -->
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
