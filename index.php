<?php
session_start();
include_once 'conexion.php';

// Verificar si el usuario ya está logueado
if (isset($_SESSION['usuario_id'])) {
    header('Location: mi-cuenta.php');
    exit;
}

// Variables de error y éxito
$error = "";
$exito = "";

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['iniciar_sesion'])) {
        // Lógica de inicio de sesión
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Consultar usuario en base de datos
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Iniciar sesión si las credenciales son correctas
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            header('Location: mi-cuenta.php');
            exit;
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    } elseif (isset($_POST['registrarse'])) {
        // Lógica de registro
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validación de campos
        if ($password !== $confirm_password) {
            $error = "Las contraseñas no coinciden.";
        } else {
            // Comprobar si el email ya está registrado
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario_existente) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                // Insertar nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
                $stmt->execute([
                    'nombre' => $nombre,
                    'email' => $email,
                    'password' => $hashed_password
                ]);
                $exito = "Cuenta creada con éxito. Ahora puedes iniciar sesión.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión o Registrarse</title>
    <link rel="stylesheet" href="./public/css/index.css">
</head>
<body>

<header>
    <h3>Bienvenido a nuestra Tienda Online</h3>
</header>

<!-- Mostrar error o éxito -->
<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($exito): ?>
    <div class="exito"><?php echo $exito; ?></div>
<?php endif; ?>

<!-- Formulario de inicio de sesión y registro -->
<div class="form-container">
    <form method="POST" action="login.php">
        <h2>Iniciar sesión</h2>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" name="iniciar_sesion">Iniciar sesión</button>
        <p>¿No tienes cuenta? <a href="login.php#registro">Crea una cuenta</a></p>
    </form>

    <!-- Sección de registro (por defecto oculta) -->
    <form method="POST" action="login.php" id="registro" style="display:none;">
        <h2>Crear una cuenta</h2>
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
        <button type="submit" name="registrarse">Crear cuenta</button>
        <p>¿Ya tienes cuenta? <a href="login.php#login">Inicia sesión</a></p>
    </form>
</div>

<!-- Script para alternar entre los formularios de login y registro -->
<script>
    const registroLink = document.querySelector('a[href="#registro"]');
    const loginLink = document.querySelector('a[href="#login"]');
    const registroForm = document.getElementById('registro');
    const loginForm = document.querySelector('form[name="iniciar_sesion"]');

    // Mostrar el formulario de registro
    registroLink.addEventListener('click', function(e) {
        e.preventDefault();
        loginForm.style.display = 'none';
        registroForm.style.display = 'block';
    });

    // Mostrar el formulario de login
    loginLink.addEventListener('click', function(e) {
        e.preventDefault();
        registroForm.style.display = 'none';
        loginForm.style.display = 'block';
    });
</script>

</body>
</html>
