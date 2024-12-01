<?php
// Conexión a la base de datos usando PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configurar el manejo de errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

// Variables de error y éxito
$errors = [];
$successMessage = '';

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validación de campos
    if (empty($email)) {
        $errors[] = 'El email es obligatorio.';
    }

    if (empty($password)) {
        $errors[] = 'La contraseña es obligatoria.';
    }

    // Si no hay errores, verificamos las credenciales
    if (empty($errors)) {
        try {
            // Verificar si el usuario existe
            $stmt = $conn->prepare("SELECT id, password, tipo_usuario FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Inicio de sesión exitoso
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['tipo_usuario'] = $user['tipo_usuario'];

                // Redirigir a la página de productos
                header("Location: ../../index.php");
                exit;
            } else {
                $errors[] = 'Credenciales incorrectas.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Error al verificar las credenciales: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/login.css">
    <title>Iniciar Sesión</title>
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>

        <!-- Mostrar mensajes de error o éxito -->
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($successMessage): ?>
            <p><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <!-- Formulario de inicio de sesión -->
        <form method="POST" action="login.php">
            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" id="email" required><br><br>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required><br><br>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <!-- Enlace para ir al registro -->
        <div class="register-link">
            <p>¿No tienes cuenta? <a href="register.php">¡Regístrate aquí!</a></p>
        </div>
    </div>
</body>
</html>
