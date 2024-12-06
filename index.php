<?php
// Iniciar sesión
session_start();

// Parámetros de la base de datos
$host = 'localhost';  // Cambia por tu host
$dbname = 'ecommerce';  // Nombre de tu base de datos
$username = 'root';  // Tu usuario de MySQL
$password = '';  // Tu contraseña de MySQL

// Conexión a la base de datos usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
    exit;
}

// Verificar si el usuario está logueado
$usuario_logueado = isset($_SESSION['usuario_id']); 

// Mensaje de bienvenida
$mensaje_bienvenida = "¡Bienvenido a nuestra tienda ecommerce!";

// Ruta de la imagen 404 en caso de error
$error_image = './public/images/404/404.png'; 

// Formulario de "Comprar"
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['producto_id'])) {
    if (!$usuario_logueado) {
        // Si el usuario no está logueado, redirigir a la página de login
        header('Location: ./src/Log_Reg/login.php');
        exit;
    }

    // Obtener el id del producto
    $producto_id = $_POST['producto_id'];

    // Si no existe el carrito, lo creamos
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Si el producto ya está en el carrito, incrementamos su cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        // Si el producto no está en el carrito, lo agregamos con cantidad 1
        $_SESSION['carrito'][$producto_id] = 1;  
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

  <!-- Script para ocultar los botones de compra si no está logueado -->
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

        <!-- Mensaje de advertencia si el usuario no está logueado -->
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
            <?php
            // Consulta para obtener las imágenes y productos asociados
            $sql = "SELECT p.id AS producto_id, p.nombre AS producto_nombre, p.descripcion AS producto_descripcion, i.imagen AS imagen_blob
                    FROM productos p
                    LEFT JOIN imagenes i ON p.id = i.producto_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as $producto):
                // Convertir imagen binaria a base64
                $imagen_base64 = base64_encode($producto['imagen_blob']);
                $imagen_data_uri = "data:image/jpeg;base64," . $imagen_base64;
            ?>
                <div class="card">
                    <!-- Mostrar imagen de producto desde la base de datos -->
                    <img src="<?php echo $imagen_data_uri; ?>" 
                         alt="Imagen producto" 
                         onerror="this.src='<?php echo $error_image; ?>';">

                    <div class="card-content">
                        <h3><?php echo ucfirst($producto['producto_nombre']); ?></h3>
                        <p><?php echo $producto['producto_descripcion'] ? $producto['producto_descripcion'] : 'Descripción no disponible.'; ?></p>

                        <!-- Si el usuario está logueado, mostramos el botón de compra -->
                        <?php if ($usuario_logueado): ?>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="producto_id" value="<?php echo $producto['producto_id']; ?>">
                                <button type="submit" class="buy-button">Comprar</button>
                            </form>
                        <?php else: ?>
                            <!-- Si no está logueado, mostramos un mensaje de alerta -->
                            <button type="button" class="buy-button" onclick="alert('¡Debes iniciar sesión para comprar!')">Comprar</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!--------------------------- carrito con la cantidad de productos -------------------------->
        <a href="./src/compras/carrito.php">
            <button class="cart-button">
                Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)
            </button>
        </a>
    </div>

</body>
</html>
