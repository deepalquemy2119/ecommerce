<?php
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

// var error y éxito
$errors = [];
$successMessage = '';

// formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

//validacion campos
    if (empty($email)) {
        $errors[] = 'El email es obligatorio.';
    }

    if (empty($password)) {
        $errors[] = 'La contraseña es obligatoria.';
    }

    // no hay errores, verificamos las credenciales
    if (empty($errors)) {
        try {
            //el usuario existe??
            $stmt = $conn->prepare("SELECT id, password, tipo_usuario, nameuser FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // usuario existe y la contraseña es correcta
            if ($user && password_verify($password, $user['password'])) {
                // Inicio de sesión exitoso
                session_start();
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
                $_SESSION['usuario_nombre'] = $user['nameuser']; 
                
                // dependiendo del tipo de usuario
                if ($_SESSION['tipo_usuario'] == 'admin') {
                    header("Location: crudAdmin.php"); 
                } else {
                    header("Location: admin.php"); 
                }
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
    <title>Iniciar Sesión</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ03v0fyl6nzTZ5J65ZnM8sl6xk6QPhh56gBcc5T2H9fWq22FkzRkvh87SgA" crossorigin="anonymous">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../../public/css/login.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="max-width: 500px; width: 100%; padding: 20px;">
            <div class="card-body">
                <h1 class="text-center mb-4">Iniciar Sesión</h1>

                <!-- Mostrar mensajes de error o éxito -->
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

                <!-- Formulario de inicio de sesión -->
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Iniciar Sesión</button>
                </form>

                <div class="text-center mt-3">
                    <p>¿No tienes cuenta? <a href="register.php">¡Regístrate aquí!</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (opcional para efectos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


//---------------------------------




