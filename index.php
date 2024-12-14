<?php
session_start();

$host = 'localhost';
$dbname = 'ecommerce';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
    exit;
}

// Verificar si el usuario está logueado y es cliente
$usuario_logueado = isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'cliente'; 

$mensaje_bienvenida = "Bienvenido al ecommerce!";
$error_image = './public/images/404/404.png'; 

// Si el usuario no está logueado, redirigirlo a login.php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['producto_id'])) {
    if (!$usuario_logueado) {
        header('Location: ./src/Log_Reg/login.php');
        exit;
    }

    // ID del producto
    $producto_id = $_POST['producto_id'];

    // No existe el carrito, lo creamos
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // El producto ya está en el carrito, incrementamos su cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        // El producto no está en el carrito, lo agregamos con cantidad 1
        $_SESSION['carrito'][$producto_id] = 1;  
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./public/css/index.css">
    <title>Ecommerce</title>
</head>
<body>

    <!-- Script para ocultar botones si no está logueado -->
    <script>
        const usuarioLogueado = <?php echo json_encode($usuario_logueado); ?>;
        if (!usuarioLogueado) {
            const buyButtons = document.querySelectorAll('.buy-button');
            buyButtons.forEach(button => {
                button.style.display = 'none';
            });

            // Deshabilitar el carrito
            const cartButton = document.querySelector('.cart-button');
            if (cartButton) {
                cartButton.addEventListener('click', function() {
                    alert('¡Debes iniciar sesión para ver el carrito!');
                });
            }
        }
    </script>

    <div class="container">
       
        <div class="welcome-message">
            <h1><?php echo $mensaje_bienvenida; ?></h1>
        </div>

        <!-- Advertencia si el usuario no está logueado -->
        <?php if (!$usuario_logueado): ?>
            <div class="warning-message">
                <p>Iniciar sesión para realizar compras. 
                    <a href="./src/Log_Reg/login.php">Iniciar sesión</a> o 
                    <a href="./src/Log_Reg/register.php">Registrarse</a>
                </p>
            </div>
        <?php endif; ?>

        <!-- Galería de productos -->
        <div class="gallery">
            <?php
            $sql = "SELECT p.id AS producto_id, p.nombre AS producto_nombre, p.descripcion AS producto_descripcion, i.imagen AS imagen_blob
                    FROM productos p
                    LEFT JOIN imagenes i ON p.id = i.producto_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as $producto):
                $imagen_base64 = base64_encode($producto['imagen_blob']);
                $imagen_data_uri = "data:image/jpeg;base64," . $imagen_base64;
            ?>
                <div class="card">
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
                            <button type="button" class="buy-button" onclick="alert('¡Debes iniciar sesión para comprar!')">Comprar</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Carrito con la cantidad de productos -->
        <?php if ($usuario_logueado): ?>
            <a href="./src/compras/carrito.php">
                <button class="cart-button">
                    Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)
                </button>
            </a>
        <?php else: ?>
            <button class="cart-button" onclick="alert('¡Debes iniciar sesión para ver el carrito!')">
                Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum($_SESSION['carrito']) : 0; ?>)
            </button>
        <?php endif; ?>

    </div>

</body>
</html>
