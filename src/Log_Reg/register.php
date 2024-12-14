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
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $nameuser = trim($_POST['nameuser']);
    $password = trim($_POST['password']);
    $tipo_usuario = $_POST['tipo_usuario'];  // 'cliente' o 'admin'

    // Validación de campos
    if (empty($email)) {
        $errors[] = 'El email es obligatorio.';
    }
    if (empty($nameuser)) {
        $errors[] = 'El nombre de usuario es obligatorio.';
    }
    if (empty($password)) {
        $errors[] = 'La contraseña es obligatoria.';
    }
    if (empty($tipo_usuario) || !in_array($tipo_usuario, ['cliente', 'admin'])) {
        $errors[] = 'tipo de usuario obligatorio: "cliente" o "admin".';
    }

    if (empty($errors)) {
        try {
            // Encriptar la contraseña
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insertar usuario en la base de datos usando procedimiento almacenado
            $stmt = $pdo->prepare("CALL insertar_usuario(:email, :nameuser, :password, :tipo_usuario)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nameuser', $nameuser);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':tipo_usuario', $tipo_usuario);
            $stmt->execute();

            // Obtener el ID del usuario insertado
            $usuario_id = $pdo->lastInsertId();

            // inserción fue exitosa
            if ($usuario_id) {
                $session_id = session_id(); 

                $stmt_sesion = $pdo->prepare("CALL insertar_sesion(:usuario_id, :session_id)");
                $stmt_sesion->bindParam(':usuario_id', $usuario_id);
                $stmt_sesion->bindParam(':session_id', $session_id);
                $stmt_sesion->execute();

                // Redirigir según el tipo de usuario
                if ($tipo_usuario == 'admin') {
                    header("Location: crudAdmin.php");
                } else {
                    header("Location: admin.php");
                }
                exit;
            }

        } catch (PDOException $e) {
            $errors[] = 'Error al registrar usuario. Verifica las credenciales. ' . $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 500px; width: 100%; padding: 20px;">
            <div class="card-body">
                <h1 class="text-center mb-4">Registrarse</h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="nameuser" class="form-label">Nombre de Usuario</label>
                        <input type="text" name="nameuser" id="nameuser" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                        <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                            <option value="cliente">Cliente</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Registrarse</button>
                </form>

                <div class="text-center mt-3">
                    <p>¿Ya tienes cuenta? <a href="login.php">¡Inicia sesión aquí!</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
