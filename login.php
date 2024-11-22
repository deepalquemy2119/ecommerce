<?php
// login.php
session_start();
include 'conexion.php';

// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['usuario_id'])) {
    header('Location: mi-cuenta.php'); // Redirigir si ya está logueado
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    <title>Iniciar sesión</title>
</head>
<body>
    <form method="POST">
        <label for="email">Correo:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Iniciar sesión</button>
    </form>
</body>
</html>
