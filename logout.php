<?php
// Iniciar sesión para acceder a las variables de sesión
session_start();

// Destruir la sesión para cerrar sesión
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar sesión</title>
    <style>
        /* Estilos del modal */
        .modal {
            display: block;  /* Mostrar el modal */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro */
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            margin: auto;
            text-align: center;
        }
        .modal button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }
        .modal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="modal">
        <div class="modal-content">
            <h2>¡Has cerrado sesión correctamente!</h2>
            <p>¿Te gustaría volver a iniciar sesión o crear una cuenta nueva?</p>
            <button onclick="window.location.href = 'login.php';">Iniciar sesión</button>
            <button onclick="window.location.href = 'registro.php';">Crear cuenta</button>
        </div>
    </div>

</body>
</html>
