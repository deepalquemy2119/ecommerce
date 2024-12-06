<?php

session_start();

// usuario logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

include './logout.php';

// evito cache a la pagina
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Pragma: no-cache");
// header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");



// conn base de datos usando PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    //manejo de errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}

// var de error y éxito
$errors = [];
$successMessage = '';

// proceso formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $nameuser = trim($_POST['nameuser']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $tipo_usuario = $_POST['tipo_usuario'];

// validación
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

//no hay errores, procesamos el registro
    if (empty($errors)) {
        try {
// ocultar la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// nuevo usuario en la base de datos
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

    <!----------------------------- Bootstrap CSS --------------------------->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ03v0fyl6nzTZ5J65ZnM8sl6xk6QPhh56gBcc5T2H9fWq22FkzRkvh87SgA" crossorigin="anonymous">
    
    <!-------------------------- Local CSS ------------------------ -->
    <link rel="stylesheet" href="../../public/css/register.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 500px; width: 100%; padding: 20px;">
            <div class="card-body">
                <h1 class="text-center mb-4">Registrar Nuevo Usuario</h1>

                <!------------- mensajes de error o éxito --------------->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php elseif ($successMessage): ?>
                    <div class="alert alert-success">
                        <p><?php echo htmlspecialchars($successMessage); ?></p>
                    </div>
                <?php endif; ?>

                <!------------------- Formulario de registro ------------------>
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
                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                        <select name="tipo_usuario" id="tipo_usuario" class="form-select">
                            <option value="cliente">Cliente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Registrar</button>
                </form>

                <div class="text-center mt-3">
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ------------------ Bootstrap JS -------------------->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


