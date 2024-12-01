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
    $nameuser = trim($_POST['nameuser']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $tipo_usuario = $_POST['tipo_usuario'];

    // Validación de campos
    if (empty($email)) {
        $errors[] = 'El email es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email no es válido.';
    }

    if (empty($nameuser)) {
        $errors[] = 'El nombre de usuario es obligatorio.';
    }

    if (empty($password)) {
        $errors[] = 'La contraseña es obligatoria.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($tipo_usuario)) {
        $errors[] = 'Selecciona un tipo de usuario.';
    }

    // Si no hay errores, procesamos el registro
    if (empty($errors)) {
        try {
            // Encriptar la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $stmt = $conn->prepare("INSERT INTO usuarios (email, nameuser, password, tipo_usuario) 
                                    VALUES (:email, :nameuser, :password, :tipo_usuario)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nameuser', $nameuser);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':tipo_usuario', $tipo_usuario);

            $stmt->execute();

            $successMessage = 'Usuario registrado con éxito. Puedes iniciar sesión ahora.';
            header("Location: ../../index.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Error al registrar el usuario: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h1>Registrar Nuevo Usuario</h1>

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

    <!-- Formulario de registro -->
    <form method="POST" action="register.php">
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="nameuser">Nombre de Usuario:</label>
        <input type="text" name="nameuser" id="nameuser" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br><br>

        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>

        <label for="tipo_usuario">Tipo de Usuario:</label>
        <select name="tipo_usuario" id="tipo_usuario">
            <option value="cliente">Cliente</option>
            <option value="admin">Administrador</option>
        </select><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
