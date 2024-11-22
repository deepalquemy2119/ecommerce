<?php
// registro.php
session_start();
include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_existente) {
        echo "Este correo ya está registrado.";
    } else {
        // Insertar el nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);

        // Loguear al usuario automáticamente
        $_SESSION['usuario_id'] = $pdo->lastInsertId();
        header('Location: index.php'); // Redirigir a la página principal
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
</head>
<body>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required>
        <br>
        <label for="email">Correo:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Crear cuenta</button>
    </form>
</body>
</html>
