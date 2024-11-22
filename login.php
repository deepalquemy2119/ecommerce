<?php
// login.php
session_start();
include 'conexion.php';

// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['usuario_id'])) {
    header('Location: mi-cuenta.php'); // Redirigir si ya está logueado
    exit;
}

// Determinar si el usuario está intentando iniciar sesión o registrarse
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Registro de nuevo usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'registro') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];

    // Verificar que las contraseñas coincidan
    if ($password !== $confirmar_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    // Verificar si el email ya está registrado
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_existente) {
        echo "El correo electrónico ya está registrado.";
        exit;
    }

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la base de datos
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $password_hash
    ]);

    // Loguear automáticamente al usuario
    $_SESSION['usuario_id'] = $pdo->lastInsertId();
    $_SESSION['usuario_nombre'] = $nombre;

    header('Location: index.php'); // Redirigir a la página principal
    exit;
}

// Inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si las credenciales son correctas
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        // Iniciar sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];

        // Registrar la sesión en la base de datos
        $session_id = session_id(); // Obtener el session_id
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes')); // Definir fecha de expiración (ej. 30 minutos)

        $stmt = $pdo->prepare("INSERT INTO cuentas_sesiones (usuario_id, session_id, fecha_expiracion) VALUES (:usuario_id, :session_id, :fecha_expiracion)");
        $stmt->execute([
            'usuario_id' => $usuario['id'],
            'session_id' => $session_id,
            'fecha_expiracion' => $fecha_expiracion
        ]);

        header('Location: mi-cuenta.php'); // Redirigir al panel de la cuenta
        exit;
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action == 'registro' ? 'Crear cuenta' : 'Iniciar sesión'; ?></title>
    <link rel="stylesheet" href="public/css/login.css">  <!-- Vincula el archivo de estilo -->
</head>
<body>

    <div class="form-container">
        <?php if ($action == 'login'): ?>
            <h2>Iniciar sesión</h2>
            <form method="POST" class="form">
                <label for="email">Correo:</label>
                <input type="email" name="email" required>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" required>
                <br>
                <button type="submit">Iniciar sesión</button>
            </form>

            <p>¿No tienes cuenta? <a href="login.php?action=registro">Regístrate aquí</a>.</p>

        <?php elseif ($action == 'registro'): ?>
            <h2>Crear cuenta</h2>
            <form method="POST" class="form">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" required>
                <br>
                <label for="email">Correo:</label>
                <input type="email" name="email" required>
                <br>
                <label for="password">Contraseña:</label>
                <input type="password" name="password" required>
                <br>
                <label for="confirmar_password">Confirmar contraseña:</label>
                <input type="password" name="confirmar_password" required>
                <br>
                <button type="submit">Crear cuenta</button>
            </form>

            <p>¿Ya tienes cuenta? <a href="login.php?action=login">Inicia sesión aquí</a>.</p>

        <?php endif; ?>
    </div>

</body>
</html>
