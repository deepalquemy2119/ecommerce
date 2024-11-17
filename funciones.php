<?php
function generar_hash($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verificar_password($password, $hash) {
    return password_verify($password, $hash);
}

function iniciar_sesion($usuario_id) {
    session_start();
    session_regenerate_id();
    $_SESSION['usuario_id'] = $usuario_id;
}

function cerrar_sesion() {
    session_start();
    session_unset();
    session_destroy();
}

function obtener_usuario_por_email($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
